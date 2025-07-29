<?php

namespace App\Http\Traits;

use App\Enum\Job\JobStatus;
use App\Models\Transaction;
use App\Models\UserPlan;
use App\Notifications\MembershipUpgradeNotification;
use Illuminate\Support\Str;
use Modules\Category\Entities\Category;
use Modules\CustomField\Entities\CustomField;

trait PaymentTrait
{
    use AdCreateTrait , HasPlanPromotion;

    public function orderPlacing($redirect = true)
    {
        // fetch session data
        $plan = session('plan');
        $order_amount = session('order_payment');
        $transaction_id = session('transaction_id') ?? uniqid('tr_');

        // Cancel subscription plan
        $this->cancelSubscriptionPlan();

        // Plan benefit attach to user
        $this->userPlanInfoUpdate($plan);

        // Transaction create
        Transaction::create([
            'order_id' => rand(1000, 999999999),
            'transaction_id' => $transaction_id,
            'plan_id' => $plan->id,
            'user_id' => auth('user')->id(),
            'payment_provider' => $order_amount['payment_provider'],
            'amount' => $order_amount['amount'],
            'currency_symbol' => $order_amount['currency_symbol'],
            'usd_amount' => $order_amount['usd_amount'],
            'payment_status' => 'paid',
        ]);

        // Store plan benefit in session and forget session
        storePlanInformation();
        $this->forgetSessions();

        // create notification and send mail to customer
        if (checkMailConfig()) {
            $user = auth('user')->user();
            if (checkSetup('mail')) {
                $user->notify(new MembershipUpgradeNotification($user, $plan->label));
            }
        }

        // redirect to customer billing
        if ($redirect) {
            session()->flash('success', __('plan_successfully_purchased'));

            return redirect()->route('frontend.plans-billing')->send();
        }
    }

    private function forgetSessions()
    {
        session()->forget('plan');
        session()->forget('order_payment');
        session()->forget('transaction_id');
        session()->forget('stripe_amount');
        session()->forget('razor_amount');
    }

    /**
     * Update userplan information.
     *
     * @param  Plan  $plan
     * @return bool
     */
    public function userPlanInfoUpdate($plan, $user_id = null)
    {
        $userplan = UserPlan::customerData($user_id)->first();

        if (! $userplan) {
            $userplan = UserPlan::create([
                'user_id' => $user_id ?? auth('user')->id(),
                'ad_limit' => 0,
                'featured_limit' => 0,
                'urgent_limit' => 0,
                'highlight_limit' => 0,
                'top_limit' => 0,
                'bump_up_limit' => 0,
            ]);
        }

        $userplan->ad_limit = $userplan->ad_limit + $plan->ad_limit;
        $userplan->featured_limit = $userplan->featured_limit + $plan->featured_limit;
        $userplan->urgent_limit = $userplan->urgent_limit + $plan->urgent_limit;
        $userplan->highlight_limit = $userplan->highlight_limit + $plan->highlight_limit;
        $userplan->top_limit = $userplan->top_limit + $plan->top_limit;
        $userplan->bump_up_limit = $userplan->bump_up_limit + $plan->bump_up_limit;

        if (! $userplan->premium_member) {
            $userplan->premium_member = $plan->premium_member ? true : false;
        }
        // Newly added Plan data End

        if (! $userplan->badge) {
            $userplan->badge = $plan->badge ? true : false;
        }

        if ($plan->plan_payment_type == 'recurring') {
            $userplan->subscription_type = 'recurring';

            if ($plan->interval == 'monthly') {
                $userplan->expired_date = now()->addMonth();
            } elseif ($plan->interval == 'yearly') {
                $userplan->expired_date = now()->addYear();
            } else {
                $userplan->expired_date = now()->addDays($plan->custom_interval_days);
            }

            $userplan->is_restored_plan_benefits = 0;
        } else {
            $userplan->subscription_type = 'one_time';
        }

        $userplan->current_plan_id = $plan->id;
        $userplan->save();

        return true;
    }

    /**
     * Create a new transaction instance.
     *
     *
     * @return bool
     */
    public function createTransaction(string $order_id, string $payment_provider, int $payment_amount, int $plan_id)
    {
        Transaction::create([
            'order_id' => $order_id,
            'user_id' => auth('user')->id(),
            'plan_id' => $plan_id,
            'payment_provider' => $payment_provider,
            'amount' => $payment_amount,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->storeAdsData();
    }

    public function cancelSubscriptionPlan()
    {
        $user = auth('user')->user();

        if ($user->subscribed('default')) {
            $subscription = $user->subscription('default');

            if ($subscription) {
                $subscription->cancel();
            }
        }

        $userplan = UserPlan::where('user_id', $user->id)->first();
        if ($userplan) {
            $userplan->subscription_type = 'one_time';
            $userplan->current_plan_id = null;
            $userplan->expired_date = null;
            $userplan->is_restored_plan_benefits = 1;
            $userplan->ad_limit = 0;
            $userplan->featured_limit = 0;
            $userplan->badge = 0;
            $userplan->save();
        }
    }

    private function storeAdsData()
    {
        $request = (object) session('ads_request');
        
        $ad = session('ad');
        $ad['video_url'] = $ad['video_url'];
        $ad['user_id'] = auth('user')->id();
        $ad['whatsapp'] = $validatedData['whatsapp'] ?? '';
        $ad['phone'] = $validatedData['phone'] ?? '';
        $ad['email'] = $validatedData['email'] ?? '';
        $ad['status'] = setting('ads_admin_approval') ? JobStatus::PENDING->value : JobStatus::ACTIVE->value;

        $user_plan_data = UserPlan::where('user_id', auth('user')->id())->first();
        $plan = $user_plan_data->currentPlan;

        // Assign promotions to user
        $ad = $this->promotePlan($request, $ad, auth('user')->id());
        $ad->save();

        // Image Storing
        $ad_images = session('ad_images');
        $ad_thumbnail = session('ad_thumbnail');

        $ad->update(['thumbnail' => $ad_thumbnail]);

        if ($ad_images && count($ad_images)) {
            foreach ($ad_images as $image_url) {
                $ad->galleries()->create(['image' => $image_url]);
            }
        }

        // Feature Storing
        $features = session('features');
        if ($features && count($features)) {
            foreach ($features as $feature) {
                if ($feature) {
                    $ad->adFeatures()->create(['name' => $feature]);
                }
            }
        }

        // ===================== For Custom Field   ================
        $customField = session()->get('custom-field'); // without checkbox
        $checkboxFields = session()->get('custom-field-checkbox'); // with checkbox

        if ($checkboxFields) {
            foreach ($checkboxFields as $key => $values) {
                $cField = CustomField::findOrFail($key)->load('customFieldGroup');

                if (gettype($values) == 'array') {
                    $imploded_value = implode(', ', $values);

                    if ($imploded_value) {
                        $ad->productCustomFields()->create([
                            'custom_field_id' => $key,
                            'value' => $imploded_value,
                            'custom_field_group_id' => $cField->custom_field_group_id,
                        ]);
                    }
                } else {
                    if ($values) {
                        $ad->productCustomFields()->create([
                            'custom_field_id' => $key,
                            'value' => $values ?? '0',
                            'custom_field_group_id' => $cField->custom_field_group_id,
                        ]);
                    }
                }
            }
        }

        $category = Category::with('customFields.values')->findOrFail($ad->category_id);

        if ($category) {
            foreach ($category->customFields as $field) {
                $keys = array_keys($customField);

                for ($i = 0; $i < count($customField); $i++) {
                    foreach ($customField[$keys[$i]] as $key => $value) {
                        if ($field->slug == $key) {
                            $CustomField = CustomField::findOrFail($field->id)->load('customFieldGroup');

                            if ($value) {
                                $ad->productCustomFields()->create([
                                    'custom_field_id' => $field->id,
                                    'value' => $value,
                                    'custom_field_group_id' => $CustomField->custom_field_group_id,
                                ]);
                            }
                        }
                    }
                }
            }
        }

        // location
        $location = session()->get('location');
        $region = array_key_exists('region', $location) ? $location['region'] : '';
        $country = array_key_exists('country', $location) ? $location['country'] : '';
        $address = Str::slug($region . '-' . $country);

        $ad->update([
            'address' => $address,
            'neighborhood' => array_key_exists('neighborhood', $location) ? $location['neighborhood'] : '',
            'locality' => array_key_exists('locality', $location) ? $location['locality'] : '',
            'place' => array_key_exists('place', $location) ? $location['place'] : '',
            'district' => array_key_exists('district', $location) ? $location['district'] : '',
            'postcode' => array_key_exists('postcode', $location) ? $location['postcode'] : '',
            'region' => array_key_exists('region', $location) ? $location['region'] : '',
            'country' => array_key_exists('country', $location) ? $location['country'] : '',
            'long' => array_key_exists('lng', $location) ? $location['lng'] : '',
            'lat' => array_key_exists('lat', $location) ? $location['lat'] : '',
        ]);

        return view('frontend.postad.postsuccess', [
            'ad_slug' => $ad->slug,
            'mode' => 'create',
        ]);
    }
}
