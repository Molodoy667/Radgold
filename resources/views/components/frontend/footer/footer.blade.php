<footer
    class="bg-gray-50 dark:bg-black border-t border-gray-100 dark:border-gray-400">
    

    
    <div class="container py-16" 
         x-data="{ 
             visible: false,
             quickLinksOpen: false,
             supportsOpen: false,
             init() {
                 const observer = new IntersectionObserver((entries) => {
                     entries.forEach(entry => {
                         if (entry.isIntersecting) {
                             this.visible = true;
                         }
                     });
                 }, { threshold: 0.1 });
                 observer.observe(this.$el);
             }
         }">
        <div class="flex flex-wrap gap-6 items-start">
            <div class="widget max-w-[424px] flex-grow"
                 x-show="visible"
                 x-transition:enter="transition-all ease-out duration-800"
                 x-transition:enter-start="opacity-0 transform translate-y-8"
                 x-transition:enter-end="opacity-100 transform translate-y-0">
                <a href="/" class="mb-4 inline-flex">
                    <img id="logo" src="{{ asset($setting->logo_image) }}" alt="">
                </a>
                <p class="mb-4 body-md-400 text-gray-700 dark:text-white max-w-[372px]">
                    {{ __('adlisting_is_a_trusted_directory_listing_companyrelied_upon_by_millions_of_people') }}</p>
                <a href="{{ route('frontend.priceplan') }}" class="btn-primary">
                    <span>{{ __('get_membership') }}</span>
                </a>
            </div>
            <div class="widget flex-grow"
                 x-show="visible"
                 x-transition:enter="transition-all ease-out duration-800 delay-100"
                 x-transition:enter-start="opacity-0 transform translate-y-8"
                 x-transition:enter-end="opacity-100 transform translate-y-0">
                <!-- Main Quick Links Touch Button -->
                <div class="footer-menu-section">
                    <button @click="quickLinksOpen = !quickLinksOpen" class="footer-main-touch-link group w-full">
                        <div class="footer-icon-container bg-gradient-to-br from-blue-500 to-purple-600">
                            <i class="fas fa-layer-group text-white footer-icon-pulse"></i>
                        </div>
                        <span class="footer-text">{{ __('quick_links') }}</span>
                        <i class="fas fa-chevron-down footer-chevron transition-transform duration-300" 
                           :class="quickLinksOpen ? 'rotate-180' : ''"></i>
                    </button>
                    
                    <!-- Submenu -->
                    <div x-show="quickLinksOpen" 
                         x-transition:enter="transition-all ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         x-transition:leave="transition-all ease-in duration-200"
                         x-transition:leave-start="opacity-100 transform translate-y-0"
                         x-transition:leave-end="opacity-0 transform -translate-y-2"
                         class="footer-submenu mt-3 space-y-2">
                        
                        <a href="{{ route('frontend.ads') }}" class="footer-sub-touch-link group">
                            <div class="footer-sub-icon-container bg-gradient-to-br from-blue-500 to-blue-600">
                                <i class="fas fa-th-grid text-white footer-icon-pulse"></i>
                            </div>
                            <span class="footer-sub-text">{{ __('listing') }}</span>
                            <i class="fas fa-arrow-right footer-sub-arrow"></i>
                        </a>
                        
                        <a href="{{ route('frontend.promotions') }}" class="footer-sub-touch-link group">
                            <div class="footer-sub-icon-container bg-gradient-to-br from-yellow-500 to-orange-500">
                                <i class="fas fa-fire text-white footer-icon-pulse"></i>
                            </div>
                            <span class="footer-sub-text">{{ __('promotions') }}</span>
                            <i class="fas fa-arrow-right footer-sub-arrow"></i>
                        </a>
                        
                        <a href="{{ route('frontend.about') }}" class="footer-sub-touch-link group">
                            <div class="footer-sub-icon-container bg-gradient-to-br from-green-500 to-emerald-600">
                                <i class="fas fa-users text-white footer-icon-pulse"></i>
                            </div>
                            <span class="footer-sub-text">{{ __('about_us') }}</span>
                            <i class="fas fa-arrow-right footer-sub-arrow"></i>
                        </a>
                        
                        <a href="{{ route('frontend.blog') }}" class="footer-sub-touch-link group">
                            <div class="footer-sub-icon-container bg-gradient-to-br from-purple-500 to-violet-600">
                                <i class="fas fa-newspaper text-white footer-icon-pulse"></i>
                            </div>
                            <span class="footer-sub-text">{{ __('blog') }}</span>
                            <i class="fas fa-arrow-right footer-sub-arrow"></i>
                        </a>
                        
                        <a href="{{ route('frontend.priceplan') }}" class="footer-sub-touch-link group">
                            <div class="footer-sub-icon-container bg-gradient-to-br from-red-500 to-pink-600">
                                <i class="fas fa-crown text-white footer-icon-pulse"></i>
                            </div>
                            <span class="footer-sub-text">{{ __('pricing_plan') }}</span>
                            <i class="fas fa-arrow-right footer-sub-arrow"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="widget flex-grow"
                 x-show="visible"
                 x-transition:enter="transition-all ease-out duration-800 delay-200"
                 x-transition:enter-start="opacity-0 transform translate-y-8"
                 x-transition:enter-end="opacity-100 transform translate-y-0">
                <!-- Main Supports Touch Button -->
                <div class="footer-menu-section">
                    <button @click="supportsOpen = !supportsOpen" class="footer-main-touch-link group w-full">
                        <div class="footer-icon-container bg-gradient-to-br from-orange-500 to-red-500">
                            <i class="fas fa-life-ring text-white footer-icon-pulse"></i>
                        </div>
                        <span class="footer-text">{{ __('supports') }}</span>
                        <i class="fas fa-chevron-down footer-chevron transition-transform duration-300" 
                           :class="supportsOpen ? 'rotate-180' : ''"></i>
                    </button>
                    
                    <!-- Submenu -->
                    <div x-show="supportsOpen" 
                         x-transition:enter="transition-all ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         x-transition:leave="transition-all ease-in duration-200"
                         x-transition:leave-start="opacity-100 transform translate-y-0"
                         x-transition:leave-end="opacity-0 transform -translate-y-2"
                         class="footer-submenu mt-3 space-y-2">
                        
                        <a href="{{ route('frontend.contact') }}" class="footer-sub-touch-link group">
                            <div class="footer-sub-icon-container bg-gradient-to-br from-orange-500 to-red-500">
                                <i class="fas fa-headset text-white footer-icon-pulse"></i>
                            </div>
                            <span class="footer-sub-text">{{ __('contact') }}</span>
                            <i class="fas fa-arrow-right footer-sub-arrow"></i>
                        </a>
                        
                        <a href="{{ route('frontend.faq') }}" class="footer-sub-touch-link group">
                            <div class="footer-sub-icon-container bg-gradient-to-br from-indigo-500 to-purple-600">
                                <i class="fas fa-lightbulb text-white footer-icon-pulse"></i>
                            </div>
                            <span class="footer-sub-text">{{ __('faqs') }}</span>
                            <i class="fas fa-arrow-right footer-sub-arrow"></i>
                        </a>
                        
                        <a href="{{ route('frontend.terms') }}" class="footer-sub-touch-link group">
                            <div class="footer-sub-icon-container bg-gradient-to-br from-teal-500 to-cyan-600">
                                <i class="fas fa-gavel text-white footer-icon-pulse"></i>
                            </div>
                            <span class="footer-sub-text">{{ __('terms_condition') }}</span>
                            <i class="fas fa-arrow-right footer-sub-arrow"></i>
                        </a>
                        
                        <a href="{{ route('frontend.privacy') }}" class="footer-sub-touch-link group">
                            <div class="footer-sub-icon-container bg-gradient-to-br from-pink-500 to-rose-600">
                                <i class="fas fa-user-shield text-white footer-icon-pulse"></i>
                            </div>
                            <span class="footer-sub-text">{{ __('privacy_policy') }}</span>
                            <i class="fas fa-arrow-right footer-sub-arrow"></i>
                        </a>
                        
                        <a href="{{ route('frontend.refund') }}" class="footer-sub-touch-link group">
                            <div class="footer-sub-icon-container bg-gradient-to-br from-cyan-500 to-blue-600">
                                <i class="fas fa-money-bill-wave text-white footer-icon-pulse"></i>
                            </div>
                            <span class="footer-sub-text">{{ __('refund_policy') }}</span>
                            <i class="fas fa-arrow-right footer-sub-arrow"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="widget flex-grow">
                @if ($mobile_setting->ios_download_url || $mobile_setting->android_download_url)
                    <h3 class="widget-title mb-3 caption-03 text-gray-900 dark:text-white">{{ __('download_our_app') }}
                    </h3>
                    <div class="flex flex-wrap gap-3 items-center mb-6">

                        @if ($mobile_setting->ios_download_url)
                            <a target="_blank" href="{{ asset($mobile_setting->ios_download_url) }}"
                                class="app-store-btn dark:text-white inline-flex gap-3 items-center py-3 px-4 rounded-lg shadow-[0px_4px_6px_-2px_rgba(28,33,38,0.03)] bg-white dark:bg-gray-700">
                                <x-svg.apple-icon />
                                <div>
                                    <p class="body-xs-500 text-gray-500 dark:text-gray-300">{{ __('get_it_now') }}</p>
                                    <p class="body-md-500 text-gray-900 dark:text-white">{{ __('app_store') }}</p>
                                </div>
                            </a>
                        @endif

                        @if ($mobile_setting->android_download_url)
                            <a target="_blank" href="{{ asset($mobile_setting->android_download_url) }}"
                                class="app-store-btn dark:text-white inline-flex gap-3 items-center py-3 px-4 rounded-lg shadow-[0px_4px_6px_-2px_rgba(28,33,38,0.03)] bg-white dark:bg-gray-700">
                                <x-svg.google-play-icon />
                                <div>
                                    <p class="body-xs-500 text-gray-500 dark:text-gray-300">{{ __('get_it_now') }}</p>
                                    <p class="body-md-500 text-gray-900 dark:text-white">{{ __('google_play') }}</p>
                                </div>
                            </a>
                        @endif

                    </div>
                @endif

                <ul class="footer-social flex gap-2.5 items-center">
                    @if ($setting->facebook)
                        <li>
                            <a target="_blank" href="{{ $setting->facebook }}"
                                class="w-10 h-10 rounded-full inline-flex justify-center items-center bg-white">
                                <x-svg.facebook-icon fill="#555B61" />
                            </a>
                        </li>
                    @endif
                    @if ($setting->twitter)
                        <li>
                            <a target="_blank" href="{{ $setting->twitter }}"
                                class="w-10 h-10 rounded-full inline-flex justify-center items-center bg-white">
                                <x-svg.twitter-icon fill="#555B61" />
                            </a>
                        </li>
                    @endif
                    @if ($setting->instagram)
                        <li>
                            <a target="_blank" href="{{ $setting->instagram }}"
                                class="w-10 h-10 rounded-full inline-flex justify-center items-center bg-white">
                                <x-svg.instagram-icon fill="#555B61" />
                            </a>
                        </li>
                    @endif
                    @if ($setting->youtube)
                        <li>
                            <a target="_blank" href="{{ $setting->youtube }}"
                                class="w-10 h-10 rounded-full inline-flex justify-center items-center bg-white">
                                <x-svg.youtube-icon fill="#555B61" />
                            </a>
                        </li>
                    @endif
                    @if ($setting->linkdin)
                        <li>
                            <a target="_blank" href="{{ $setting->linkdin }}"
                                class="w-10 h-10 rounded-full inline-flex justify-center items-center bg-white">
                                <x-svg.linkedin-icon fill="#555B61" />
                            </a>
                        </li>
                    @endif
                    @if ($setting->whatsapp)
                        <li>
                            <a target="_blank" href="{{ $setting->whatsapp }}"
                                class="w-10 h-10 rounded-full inline-flex justify-center items-center bg-white">
                                <x-svg.whatsapp-icon fill="#555B61" />
                            </a>
                        </li>
                    @endif
                </ul>
            </div>

        </div>
        <div class="mt-12">
            <p class="text-center body-md-400 text-gray-700 dark:text-gray-100">
                @php
                    $string = preg_replace('/<\/?p>/i', '', cms('footer_text'));
                @endphp
                {!! $string !!}
            </p>
        </div>
    </div>
</footer>
