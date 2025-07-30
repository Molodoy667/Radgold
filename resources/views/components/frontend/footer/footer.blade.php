<footer
    class="bg-gray-50 dark:bg-black border-t border-gray-100 dark:border-gray-400">
    
    <!-- Mobile Touch Footer -->
    <div x-data="{ 
        footerOpen: false,
        scrollY: 0,
        lastScrollY: 0,
        scrollThreshold: 200,
        
        init() {
            this.scrollY = window.scrollY;
            this.lastScrollY = window.scrollY;
            window.addEventListener('scroll', () => {
                this.scrollY = window.scrollY;
                const scrollingDown = this.scrollY > this.lastScrollY;
                const scrolledEnough = this.scrollY > this.scrollThreshold;
                
                if (scrollingDown && scrolledEnough && !this.footerOpen) {
                    this.footerOpen = true;
                }
                
                this.lastScrollY = this.scrollY;
            });
        }
    }" 
    class="lg:hidden">
        <!-- Touch Footer Panel -->
        <div x-show="footerOpen"
             x-transition:enter="transform transition-all ease-out duration-500"
             x-transition:enter-start="translate-y-full opacity-0"
             x-transition:enter-end="translate-y-0 opacity-100"
             x-transition:leave="transform transition-all ease-in duration-300"
             x-transition:leave-start="translate-y-0 opacity-100"
             x-transition:leave-end="translate-y-full opacity-0"
             class="fixed bottom-0 left-0 right-0 z-[99999] touch-footer"
             style="display: none;">
             
            <!-- Footer Content -->
            <div class="bg-white/95 dark:bg-gray-900/95 backdrop-blur-xl border-t border-gray-200 dark:border-gray-700 shadow-2xl">
                
                <!-- Close Button -->
                <div class="flex justify-center py-2">
                    <button @click="footerOpen = false" 
                            class="w-12 h-1.5 bg-gray-300 dark:bg-gray-600 rounded-full opacity-60 hover:opacity-100 transition-opacity duration-200"></button>
                </div>
                
                <!-- Quick Links Grid -->
                <div class="px-4 pb-6">
                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <!-- Quick Links -->
                        <a href="{{ route('frontend.ads') }}" class="touch-footer-item">
                            <i class="fas fa-list text-blue-500 text-lg mb-2 footer-icon-pulse"></i>
                            <span class="text-xs text-gray-700 dark:text-gray-300">{{ __('listing') }}</span>
                        </a>
                        
                        <a href="{{ route('frontend.about') }}" class="touch-footer-item">
                            <i class="fas fa-info-circle text-green-500 text-lg mb-2 footer-icon-pulse"></i>
                            <span class="text-xs text-gray-700 dark:text-gray-300">{{ __('about_us') }}</span>
                        </a>
                        
                        <a href="{{ route('frontend.contact') }}" class="touch-footer-item">
                            <i class="fas fa-envelope text-purple-500 text-lg mb-2 footer-icon-pulse"></i>
                            <span class="text-xs text-gray-700 dark:text-gray-300">{{ __('contact') }}</span>
                        </a>
                        
                        <a href="{{ route('frontend.blog') }}" class="touch-footer-item">
                            <i class="fas fa-blog text-orange-500 text-lg mb-2 footer-icon-pulse"></i>
                            <span class="text-xs text-gray-700 dark:text-gray-300">{{ __('blog') }}</span>
                        </a>
                        
                        <a href="{{ route('frontend.priceplan') }}" class="touch-footer-item">
                            <i class="fas fa-tags text-red-500 text-lg mb-2 footer-icon-pulse"></i>
                            <span class="text-xs text-gray-700 dark:text-gray-300">{{ __('pricing_plan') }}</span>
                        </a>
                        
                        <a href="{{ route('frontend.faq') }}" class="touch-footer-item">
                            <i class="fas fa-question-circle text-indigo-500 text-lg mb-2 footer-icon-pulse"></i>
                            <span class="text-xs text-gray-700 dark:text-gray-300">{{ __('faqs') }}</span>
                        </a>
                    </div>
                    
                    <!-- Footer Text -->
                    <div class="text-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            © {{ date('Y') }} {{ $setting->app_name }}. {{ __('all_rights_reserved') }}.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container py-16">
        <div class="flex flex-wrap gap-6 items-start">
            <div class="widget max-w-[424px] flex-grow">
                <a href="/" class="mb-4 inline-flex">
                    <img id="logo" src="{{ asset($setting->logo_image) }}" alt="">
                </a>
                <p class="mb-4 body-md-400 text-gray-700 dark:text-white max-w-[372px]">
                    {{ __('adlisting_is_a_trusted_directory_listing_companyrelied_upon_by_millions_of_people') }}</p>
                <a href="{{ route('frontend.priceplan') }}" class="btn-primary">
                    <span>{{ __('get_membership') }}</span>
                </a>
            </div>
            <div class="widget flex-grow">
                <h3 class="widget-title heading-06 text-gray-900 dark:text-white mb-3.5">{{ __('quick_links') }}</h3>
                <ul class="flex flex-col gap-3.5">
                    <li><a href="{{ route('frontend.ads') }}" class="footer-link dark:text-gray-100 heading-07 capitalize">{{ __('listing') }}</a></li>
                    <li><a href="{{ route('frontend.promotions') }}" class="footer-link dark:text-gray-100 heading-07 capitalize">{{ __('promotions') }}</a></li>
                    <li><a href="{{ route('frontend.about') }}" class="footer-link dark:text-gray-100 heading-07">{{ __('about_us') }}</a>
                    </li>
                    <li><a href="{{ route('frontend.blog') }}" class="footer-link dark:text-gray-100 heading-07">
                            {{ __('blog') }}</a>
                    </li>
                    <li><a href="{{ route('frontend.priceplan') }}"
                            class="footer-link dark:text-gray-100 heading-07">{{ __('pricing_plan') }}</a></li>
                </ul>
            </div>
            <div class="widget flex-grow">
                <h3 class="widget-title heading-06 text-gray-900 dark:text-white mb-3.5">{{ __('supports') }}</h3>
                <ul class="flex flex-col gap-3.5">
                    <li><a href="{{ route('frontend.contact') }}"
                            class="footer-link dark:text-gray-100 heading-07">{{ __('contact') }}</a></li>
                    <li><a href="{{ route('frontend.faq') }}"
                            class="footer-link dark:text-gray-100 heading-07">{{ __('faqs') }}</a>
                    </li>
                    <li><a href="{{ route('frontend.terms') }}"
                            class="footer-link dark:text-gray-100 heading-07">{{ __('terms_condition') }}</a>
                    </li>
                    <li><a href="{{ route('frontend.privacy') }}"
                            class="footer-link dark:text-gray-100 heading-07">{{ __('privacy_policy') }}</a></li>
                    <li><a href="{{ route('frontend.refund') }}"
                            class="footer-link dark:text-gray-100 heading-07">{{ __('refund_policy') }}</a></li>
                </ul>
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
