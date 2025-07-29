<?php

use Modules\Plan\Entities\Plan;

beforeEach(function () {
    $this->admin = createAdmin();
    $this->actingAs($this->admin, 'admin');
});

test('admin can visit plan index page', function () {
    $this->get(route('module.plan.index'))
        ->assertStatus(200)
        ->assertSeeText('Plan');
});

test('admin can visit plan create page', function () {
    $this->get(route('module.plan.create'))
        ->assertStatus(200)
        ->assertSeeText('Create');
});

test('admin can record new plan', function () {
    $data = [
        'label' => 'Free',
        'price' => 0.00,
        'ad_limit' => 1,

        'featured_limit' => 0,
        'featured_duration' => 0,

        'urgent_limit' => 0,
        'urgent_duration' => 0,

        'highlight_limit' => 0,
        'highlight_duration' => 0,

        'top_limit' => 0,
        'top_duration' => 0,

        'bump_up_limit' => 0,
        'bump_up_duration' => 0,

        'badge' => false,
        'premium_member' => false,

        'recommended' => false,
        'interval' => 'monthly',
        'plan_payment_type' => 'recurring',
        'custom_interval_days' => 15,
        'stripe_id' => 'price_1OUoNnDHsbz9CBNMyNPBdF7W',
    ];
    $this->post(route('module.plan.store'), $data)
        ->assertStatus(302);
});

test('admin can\'t record new plan for validation', function () {
    $this->post(route('module.plan.store'), [])
        ->assertSessionHasErrors([
            'label', 'price', 'ad_limit', 'featured_limit',
            'featured_duration', 'badge', 'premium_member', 'plan_payment_type',
        ])
        ->assertStatus(302);
    expect(Plan::count())->toBe(1);
});

test('admin can visit plan edit page', function () {

    $plan = Plan::first(); // Fetch the first Plan in the database

    $this->get(route('module.plan.edit', $plan->id)) // Use the actual ID of the plan
        ->assertStatus(200)
        ->assertSeeText('Edit');
});

test('admin can update any plan', function () {

    $data = [
        'label' => 'Free',
        'price' => 0.00,
        'ad_limit' => 1,

        'featured_limit' => 0,
        'featured_duration' => 0,

        'urgent_limit' => 0,
        'urgent_duration' => 0,

        'highlight_limit' => 0,
        'highlight_duration' => 0,

        'top_limit' => 0,
        'top_duration' => 0,

        'bump_up_limit' => 0,
        'bump_up_duration' => 0,

        'badge' => false,
        'premium_member' => false,

        'recommended' => false,
        'interval' => 'monthly',
        'plan_payment_type' => 'recurring',
        'custom_interval_days' => 15,
        'stripe_id' => 'price_1OUoNnDHsbz9CBNMyNPBdF7W',
    ];

    $plan = Plan::first(); // Fetch the first Plan in the database

    $this->put(route('module.plan.update', $plan->id), $data)
        ->assertStatus(302);
});

test('admin can\'t update a plan for validation', function () {

    $plan = Plan::first(); // Fetch the first Plan in the database
    $this->put(route('module.plan.update', $plan->id), [])
        ->assertSessionHasErrors([
            'label', 'price', 'ad_limit', 'featured_limit',
            'featured_duration', 'badge', 'premium_member', 'plan_payment_type',
        ])
        ->assertStatus(302);
});

test('admin can delete a plan', function () {
    $plan = Plan::first(); // Fetch the first Plan in the database
    $this->delete(route('module.plan.delete', $plan->id))
        ->assertStatus(302);
    expect(Plan::count())->toBe(0);
});
