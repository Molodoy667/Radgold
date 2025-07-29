@extends('frontend.layouts.app')

@section('title', 'Seller Profile')

@section('content')
<x-frontend.breadcrumb.breadcrumb :links="[['text' => 'Dashboard', 'url' => '/dashboard'], ['text' => 'Seller Profile']]" />

<section class="py-16">
    <div class="container">
        <div class="flex flex-col lg:flex-row gap-6 items-start">

            @include('frontend.layouts.partials.seller-dashboard-sidebar')

            <div class="flex flex-col gap-5 w-full">

                @include('frontend.seller.hero')

                <div>
                    <div class="w-full" x-data="{ tab: 'ads' }">
                        <div class="flex gap-x-[1.5rem]  border-b border-b-gray-100 w-full">
                            <h5 :class="`heading-05 ${tab == 'ads' ? 'active-tab':'text-gray-500'} transition-all duration-150 ease-in-out  pb-[0.94rem] hover:active-tab relative `"
                                role="button" @click="tab='ads'">
                                {{ __('recent_ads') }}
                            </h5>
                            <h5 :class="`heading-05 ${tab == 'review_list' ? 'active-tab':'text-gray-500'} transition-all duration-150 ease-in-out  pb-[0.94rem] hover:active-tab relative`"
                                role="button" @click="tab='review_list'">{{ __('seller_review') }}</h5>
                        </div>

                        <div class="mt-[1.5rem]">
                            <div x-show="tab == 'ads'">
                                @include('frontend.seller.ad-list')
                            </div>

                            <div x-show="tab == 'review_list'">
                                {{-- <x-frontend.company.rating /> --}}
                                @include('frontend.seller.review-list')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('js')
@endpush
