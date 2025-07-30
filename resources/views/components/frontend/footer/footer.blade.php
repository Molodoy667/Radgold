<footer
    class="footer-glass-bg border-t border-white/20 dark:border-gray-700/50">
    

    
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
                <p class="mb-4 body-md-400 text-gray-800 dark:text-white max-w-[372px]">
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

<style>
/* Footer Theme Background - Same as Header Top */
.footer-glass-bg {
    background-color: var(--primary-800) !important; /* Same as header-top for light theme */
    backdrop-filter: blur(20px) !important;
    color: rgba(255, 255, 255, 0.8) !important; /* Same text color as header-top */
}

.dark .footer-glass-bg {
    background: rgba(17, 24, 39, 0.9) !important; /* Same as header-top dark theme */
    backdrop-filter: blur(20px) !important;
    border-top: 1px solid rgba(255, 255, 255, 0.2) !important;
    color: rgba(255, 255, 255, 0.8) !important;
}

/* Footer Glass Touch Links - Complete Styling */
.footer-main-touch-link,
.footer-sub-touch-link {
    display: flex !important;
    align-items: center !important;
    padding: 16px 20px !important;
    border-radius: 16px !important;
    background: rgba(255, 255, 255, 0.1) !important; /* White glass for both themes */
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
    backdrop-filter: blur(15px) !important;
    -webkit-backdrop-filter: blur(15px) !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    text-decoration: none !important;
    -webkit-tap-highlight-color: transparent !important;
    position: relative !important;
    overflow: hidden !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1) !important;
    color: rgba(255, 255, 255, 0.9) !important; /* White text for both themes */
}

.dark .footer-main-touch-link,
.dark .footer-sub-touch-link {
    background: rgba(255, 255, 255, 0.12) !important;
    border: 1px solid rgba(255, 255, 255, 0.25) !important;
    backdrop-filter: blur(15px) !important;
    color: white !important; /* White text for dark theme */
}

/* Footer Touch Link Shimmer Effect */
.footer-main-touch-link::before,
.footer-sub-touch-link::before {
    content: '' !important;
    position: absolute !important;
    top: 0 !important;
    left: -100% !important;
    width: 100% !important;
    height: 100% !important;
    background: linear-gradient(90deg, transparent, rgba(0, 0, 0, 0.2), transparent) !important; /* Dark shimmer for light theme */
    transition: left 0.5s !important;
}

.dark .footer-main-touch-link::before,
.dark .footer-sub-touch-link::before {
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent) !important; /* Light shimmer for dark theme */
}

.footer-main-touch-link:hover::before,
.footer-sub-touch-link:hover::before {
    left: 100% !important;
}

/* Footer Touch Link Hover Effects */
.footer-main-touch-link:hover,
.footer-sub-touch-link:hover {
    background: rgba(0, 0, 0, 0.15) !important; /* Darker for light theme */
    border-color: rgba(0, 0, 0, 0.3) !important;
    transform: translateY(-2px) scale(1.02) !important;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15) !important;
}

.dark .footer-main-touch-link:hover,
.dark .footer-sub-touch-link:hover {
    background: rgba(255, 255, 255, 0.18) !important;
    border-color: rgba(255, 255, 255, 0.35) !important;
}

/* Footer Touch Link Active States */
.footer-main-touch-link:active,
.footer-sub-touch-link:active {
    transform: translateY(0) scale(0.98) !important;
}

/* Footer Text Styling */
.footer-text,
.footer-sub-text {
    flex: 1 !important;
    font-weight: 500 !important;
    font-size: 16px !important;
    color: rgba(255, 255, 255, 0.9) !important; /* White text for both themes */
    transition: all 0.3s ease !important;
}

.dark .footer-text,
.dark .footer-sub-text {
    color: rgba(255, 255, 255, 0.9) !important; /* White text for dark theme */
}

.footer-main-touch-link:hover .footer-text,
.footer-sub-touch-link:hover .footer-sub-text {
    transform: translateX(4px) !important;
}

/* Footer Chevrons and Arrows */
.footer-chevron,
.footer-sub-arrow {
    font-size: 14px !important;
    color: #6b7280 !important; /* Gray for light theme */
    transition: all 0.3s ease !important;
}

.dark .footer-chevron,
.dark .footer-sub-arrow {
    color: rgba(255, 255, 255, 0.8) !important; /* Light gray for dark theme */
}

.footer-main-touch-link:hover .footer-chevron,
.footer-sub-touch-link:hover .footer-sub-arrow {
    opacity: 1 !important;
    transform: scale(1.1) !important;
    color: #374151 !important;
}

.dark .footer-main-touch-link:hover .footer-chevron,
.dark .footer-sub-touch-link:hover .footer-sub-arrow {
    color: white !important;
}

/* Footer Icon Containers */
.footer-icon-container,
.footer-sub-icon-container {
    width: 48px !important;
    height: 48px !important;
    border-radius: 12px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin-right: 16px !important;
    flex-shrink: 0 !important;
    transition: all 0.3s ease !important;
    border: 1px solid rgba(255, 255, 255, 0.3) !important;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1) !important;
}

.footer-main-touch-link:hover .footer-icon-container,
.footer-sub-touch-link:hover .footer-sub-icon-container {
    transform: scale(1.1) rotate(5deg) !important;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2) !important;
}

/* Footer Submenu Styling */
.footer-submenu {
    background: rgba(0, 0, 0, 0.05) !important; /* Dark for light theme */
    border: 1px solid rgba(0, 0, 0, 0.1) !important;
    backdrop-filter: blur(15px) !important;
    -webkit-backdrop-filter: blur(15px) !important;
    border-radius: 12px !important;
    padding: 8px !important;
    margin-left: 12px !important;
    margin-top: 8px !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1) !important;
}

.dark .footer-submenu {
    background: rgba(255, 255, 255, 0.03) !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
}

/* Footer Sub Touch Links */
.footer-sub-touch-link {
    padding: 12px 16px !important;
    border-radius: 10px !important;
    background: rgba(0, 0, 0, 0.08) !important; /* Dark for light theme */
    border: 1px solid rgba(0, 0, 0, 0.15) !important;
    margin-bottom: 4px !important;
}

.dark .footer-sub-touch-link {
    background: rgba(255, 255, 255, 0.06) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
}

.footer-sub-touch-link:hover {
    background: rgba(0, 0, 0, 0.12) !important; /* Darker for light theme */
    border-color: rgba(0, 0, 0, 0.25) !important;
    transform: translateX(4px) scale(1.02) !important;
}

.dark .footer-sub-touch-link:hover {
    background: rgba(255, 255, 255, 0.1) !important;
    border-color: rgba(255, 255, 255, 0.2) !important;
}

/* Footer Sub Icon Containers */
.footer-sub-icon-container {
    width: 32px !important;
    height: 32px !important;
    border-radius: 8px !important;
    margin-right: 12px !important;
}

/* Footer Icon Pulse Animation */
.footer-icon-pulse {
    animation: footerIconPulse 2s ease-in-out infinite !important;
}

@keyframes footerIconPulse {
    0%, 100% {
        transform: scale(1);
        opacity: 1;
    }
    50% {
        transform: scale(1.05);
        opacity: 0.9;
    }
}

/* Footer Links - Regular */
.footer-link {
    color: #4b5563 !important; /* Medium gray for light theme */
    transition: color 0.3s ease !important;
}

.footer-link:hover {
    color: #1f2937 !important; /* Darker gray on hover for light theme */
}

.dark .footer-link {
    color: #d1d5db !important; /* Light gray for dark theme */
}

.dark .footer-link:hover {
    color: white !important; /* White on hover for dark theme */
}

/* Footer Widget Titles */
.widget h3 {
    color: #111827 !important; /* Very dark for light theme */
}

.dark .widget h3 {
    color: white !important; /* White for dark theme */
}

/* Footer Description Text */
.widget p {
    color: #374151 !important; /* Dark gray for light theme */
}

.dark .widget p {
    color: #d1d5db !important; /* Light gray for dark theme */
}

/* Footer Copyright */
.copyright-area p {
    color: #6b7280 !important; /* Medium gray for light theme */
}

.dark .copyright-area p {
    color: #9ca3af !important; /* Medium light gray for dark theme */
}

/* Footer Menu Section */
.footer-menu-section {
    position: relative !important;
}

/* Smooth Transitions */
.footer-menu-section * {
    transition-timing-function: cubic-bezier(0.25, 0.8, 0.25, 1) !important;
}
</style>
