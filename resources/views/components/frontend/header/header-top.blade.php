<div class="header-top bg-primary-800 dark:bg-gray-800 border-b border-primary-600 dark:border-primary-800 text-white/80">
    <div class="container">
        <div class="py-1.5 lg:hidden border-b">
            <button type="button" data-modal-target="locationModal" data-modal-toggle="locationModal"
                class="inline-flex gap-1.5 items-center heading-07 transition-all duration-300 text-white/80 hover:text-white">
                <x-svg.search-location-icon width="20" height="20" />
                @if (selected_country())
                    <span>{{ selected_country()->name }}</span>
                @else
                    <span>{{ __('all_country') }}</span>
                @endif
            </button>
        </div>
        <div class="flex gap-3 justify-between items-center py-1.5">
            <ul class="lg:!inline-flex hidden flex-wrap gap-5 items-center">
                <li>
                    <a href="{{ route('frontend.priceplan') }}"
                        class="heading-07 transition-all duration-300 text-white/80 hover:text-white">{{ __('pricing_plan') }}</a>
                </li>
                <!-- City List Modal Trigger Button Start -->
                <li>
                    <button type="button" data-modal-target="locationModal" data-modal-toggle="locationModal"
                        class="inline-flex gap-1.5 items-center heading-07 transition-all duration-300 text-white/80 hover:text-white">
                        <x-svg.search-location-icon width="20" height="20" />
                        @if (selected_country())
                            <span>{{ selected_country()->name }}</span>
                        @else
                            <span>{{ __('all_country') }}</span>
                        @endif
                    </button>
                </li>

                <!-- City List Modal Trigger Button End -->

            </ul>



            <!-- City List Modal Start -->
            <div id="locationModal" tabindex="-1" aria-hidden="true"
                class="hidden overflow-y-auto overflow-x-hidden bg-black/50 fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                <div class="relative w-full max-w-2xl max-h-full mx-4">
                    <!-- Modal content -->
                    <div class="relative bg-white dark:bg-gray-700 rounded-lg shadow pb-6">

                        <h2 class="text-gray-900 heading-07 px-6 pt-6 pb-3">{{ __('select_your_country') }}</h2>
                        <form class="px-6 pb-3">
                            <div>
                                <div class="relative mt-2 rounded-md shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <x-svg.search-icon stroke="var(--primary-500)" />
                                    </div>
                                    <input id="search-input" type="text" placeholder="Search Country" name="location"
                                        value="" autocomplete="off"
                                        class="block w-full rounded-md border-0 py-1.5 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>
                        </form>
                        <div class="city-list h-80 px-6 pb-6 overflow-y-auto no-scrollbar overflow-x-auto">
                            <ul>
                                <li>
                                    <a class="flex gap-2 px-3 py-2 hover:bg-primary-50 transition-all duration-300 text-gray-600 dark:text-gray-200 body-base-400"
                                        href="{{ route('frontend.set.country', ['country' => 'all_country']) }}">
                                        <i class="fa-solid fa-globe"></i>
                                        {{ __('all_country') }}
                                    </a>
                                </li>
                                @foreach ($headerCountries as $country)
                                    <li id="lang-dropdown-item">
                                        <a class="flex gap-2 px-3 py-2 hover:bg-primary-50 transition-all duration-300 text-gray-600 dark:text-gray-200 body-base-400"
                                            href="{{ route('frontend.set.country', ['country' => $country->id]) }}">
                                            <i class="flag-icon {{ $country->icon }}"></i>
                                            {{ $country->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                    </div>
                </div>
            </div>
            <!-- City List Modal End -->
            <div class="inline-flex lg:hidden flex-wrap gap-3 items-center">
                <!-- Universal Touch Panel System -->
                <div x-data="touchPanel()" 
                     @touchstart="handleTouchStart($event)"
                     @touchmove="handleTouchMove($event)" 
                     @touchend="handleTouchEnd()"
                     class="touch-panel-wrapper">
                     
                    <!-- Touch Menu Button -->
                    <button @click="openPanel()" 
                            class="touch-btn relative w-12 h-12 rounded-2xl
                                   bg-gradient-to-br from-blue-500/90 to-purple-600/90
                                   backdrop-blur-xl border border-white/20
                                   shadow-lg hover:shadow-blue-500/30
                                   flex items-center justify-center
                                   transition-all duration-300 ease-out
                                   hover:scale-105 active:scale-95
                                   overflow-hidden">
                        <!-- Animated background -->
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-400 to-purple-500 opacity-0 transition-opacity duration-300 hover:opacity-20"></div>
                        
                        <!-- Menu icon with animation -->
                        <div class="relative z-10 transform transition-transform duration-300" :class="panelOpen ? 'rotate-90' : ''">
                            <i class="fas fa-bars text-white text-lg"></i>
                        </div>
                        
                        <!-- Ripple effect -->
                        <div class="absolute inset-0 rounded-2xl opacity-0 pointer-events-none touch-ripple"></div>
                    </button>

                    <!-- Full Screen Touch Panel -->
                    <div x-show="panelOpen" 
                         x-transition:enter="transition-all ease-out duration-400"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition-all ease-in duration-300"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 z-[99999] bg-black/50 backdrop-blur-md"
                         style="display: none;">
                         
                        <!-- Panel Container -->
                        <div x-show="panelOpen"
                             x-transition:enter="transform transition-all ease-out duration-400"
                             x-transition:enter-start="-translate-x-full opacity-0"
                             x-transition:enter-end="translate-x-0 opacity-100"
                             x-transition:leave="transform transition-all ease-in duration-300"
                             x-transition:leave-start="translate-x-0 opacity-100"
                             x-transition:leave-end="-translate-x-full opacity-0"
                             @click.stop
                             class="touch-panel-container h-full w-full max-w-sm relative">
                             
                            @auth('user')
                                <!-- Authenticated User Panel -->
                                <div class="h-full bg-gradient-to-br from-white/95 to-gray-50/95 dark:from-gray-900/95 dark:to-gray-800/95 backdrop-blur-2xl border-r border-white/20 dark:border-gray-700/30 shadow-2xl">
                                    
                                    <!-- Header with User Info -->
                                    <div class="relative p-6 bg-gradient-to-br from-blue-500/10 to-purple-600/10 border-b border-white/10">
                                        <!-- Close button -->
                                        <button @click="closePanel()" 
                                                class="absolute top-4 right-4 w-10 h-10 rounded-xl 
                                                       bg-white/20 hover:bg-white/30 dark:bg-gray-800/50 dark:hover:bg-gray-700/50
                                                       flex items-center justify-center
                                                       transition-all duration-200 group">
                                            <i class="fas fa-times text-gray-700 dark:text-gray-300 group-hover:scale-110 transition-transform"></i>
                                        </button>

                                        <!-- User Profile -->
                                        <div class="flex items-center space-x-4">
                                            <div class="relative">
                                                <img class="w-20 h-20 rounded-2xl object-cover ring-3 ring-white/30 shadow-xl" 
                                                     src="{{ authUser()->image_url }}" 
                                                     alt="{{ authUser()->name }}">
                                                @if (auth('user')->user()->document_verified && auth('user')->user()->document_verified->status == 'approved')
                                                    <div class="absolute -top-1 -right-1 w-7 h-7 bg-green-500 rounded-full flex items-center justify-center ring-2 ring-white">
                                                        <i class="fas fa-check text-white text-xs"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h2 class="text-xl font-bold text-gray-900 dark:text-white truncate">
                                                    {{ authUser()->name }}
                                                </h2>
                                                <p class="text-sm text-gray-600 dark:text-gray-400 truncate">
                                                    {{ authUser()->email }}
                                                </p>
                                                <div class="flex items-center mt-1">
                                                    <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                                                    <span class="text-xs text-green-600 dark:text-green-400 font-medium">{{ __('online') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Navigation Menu -->
                                    <div class="flex-1 overflow-y-auto py-6 px-4">
                                        <nav class="space-y-2">
                                            <!-- Dashboard -->
                                            <a href="{{ route('frontend.dashboard') }}"
                                               @click="closePanel()"
                                               class="touch-nav-item {{ request()->routeIs('frontend.dashboard') ? 'active' : '' }}">
                                                <div class="touch-nav-icon bg-gradient-to-br from-blue-500 to-blue-600">
                                                    <i class="fas fa-tachometer-alt"></i>
                                                </div>
                                                <span class="touch-nav-text">{{ __('overview') }}</span>
                                                <i class="fas fa-chevron-right touch-nav-arrow"></i>
                                            </a>

                                            <!-- My Ads -->
                                            <a href="{{ route('frontend.my.listing') }}"
                                               @click="closePanel()"
                                               class="touch-nav-item {{ request()->routeIs('frontend.my.listing') ? 'active' : '' }}">
                                                <div class="touch-nav-icon bg-gradient-to-br from-orange-500 to-orange-600">
                                                    <i class="fas fa-list-alt"></i>
                                                </div>
                                                <span class="touch-nav-text">{{ __('my_ads') }}</span>
                                                <i class="fas fa-chevron-right touch-nav-arrow"></i>
                                            </a>

                                            <!-- Post Listing -->
                                            <a href="{{ route('frontend.post') }}"
                                               @click="closePanel()"
                                               class="touch-nav-item {{ request()->routeIs('frontend.post') ? 'active' : '' }}">
                                                <div class="touch-nav-icon bg-gradient-to-br from-purple-500 to-purple-600">
                                                    <i class="fas fa-plus-circle"></i>
                                                </div>
                                                <span class="touch-nav-text">{{ __('post_listing') }}</span>
                                                <i class="fas fa-chevron-right touch-nav-arrow"></i>
                                            </a>

                                            <!-- Messages -->
                                            <a href="{{ route('frontend.message') }}"
                                               @click="closePanel()"
                                               class="touch-nav-item {{ request()->routeIs('message') ? 'active' : '' }}">
                                                <div class="touch-nav-icon bg-gradient-to-br from-indigo-500 to-indigo-600">
                                                    <i class="fas fa-comments"></i>
                                                </div>
                                                <span class="touch-nav-text">{{ __('message') }}</span>
                                                <i class="fas fa-chevron-right touch-nav-arrow"></i>
                                            </a>

                                            <!-- Favorites -->
                                            <a href="{{ route('frontend.favorite.list') }}"
                                               @click="closePanel()"
                                               class="touch-nav-item {{ request()->routeIs('favorite-listing') ? 'active' : '' }}">
                                                <div class="touch-nav-icon bg-gradient-to-br from-red-500 to-pink-600">
                                                    <i class="fas fa-heart"></i>
                                                </div>
                                                <span class="touch-nav-text">{{ __('favorite_ads') }}</span>
                                                <i class="fas fa-chevron-right touch-nav-arrow"></i>
                                            </a>

                                            <!-- Plans & Billing -->
                                            <a href="{{ route('frontend.plans-billing') }}"
                                               @click="closePanel()"
                                               class="touch-nav-item {{ request()->routeIs('frontend.plans-billing') ? 'active' : '' }}">
                                                <div class="touch-nav-icon bg-gradient-to-br from-emerald-500 to-emerald-600">
                                                    <i class="fas fa-credit-card"></i>
                                                </div>
                                                <span class="touch-nav-text">{{ __('plans_billing') }}</span>
                                                <i class="fas fa-chevron-right touch-nav-arrow"></i>
                                            </a>

                                            <!-- Account Settings -->
                                            <a href="{{ route('frontend.account-setting') }}"
                                               @click="closePanel()"
                                               class="touch-nav-item {{ request()->routeIs('frontend.account-setting') ? 'active' : '' }}">
                                                <div class="touch-nav-icon bg-gradient-to-br from-slate-500 to-slate-600">
                                                    <i class="fas fa-cog"></i>
                                                </div>
                                                <span class="touch-nav-text">{{ __('account_setting') }}</span>
                                                <i class="fas fa-chevron-right touch-nav-arrow"></i>
                                            </a>

                                            <!-- Divider -->
                                            <div class="border-t border-gray-200/50 dark:border-gray-700/50 my-4"></div>

                                            <!-- Logout -->
                                            <a href="javascript:void(0)"
                                               @click="closePanel(); document.getElementById('auth-logout-form').submit();"
                                               class="touch-nav-item logout-item">
                                                <div class="touch-nav-icon bg-gradient-to-br from-red-500 to-red-600">
                                                    <i class="fas fa-sign-out-alt"></i>
                                                </div>
                                                <span class="touch-nav-text text-red-600 dark:text-red-400">{{ __('logout') }}</span>
                                                <i class="fas fa-chevron-right touch-nav-arrow text-red-500"></i>
                                            </a>
                                        </nav>
                                    </div>
                                </div>
                            @else
                                <!-- Guest User Panel -->
                                <div class="h-full bg-gradient-to-br from-white/95 to-gray-50/95 dark:from-gray-900/95 dark:to-gray-800/95 backdrop-blur-2xl border-r border-white/20 dark:border-gray-700/30 shadow-2xl">
                                    
                                    <!-- Header for Guests -->
                                    <div class="relative p-6 bg-gradient-to-br from-green-500/10 to-blue-600/10 border-b border-white/10">
                                        <!-- Close button -->
                                        <button @click="closePanel()" 
                                                class="absolute top-4 right-4 w-10 h-10 rounded-xl 
                                                       bg-white/20 hover:bg-white/30 dark:bg-gray-800/50 dark:hover:bg-gray-700/50
                                                       flex items-center justify-center
                                                       transition-all duration-200 group">
                                            <i class="fas fa-times text-gray-700 dark:text-gray-300 group-hover:scale-110 transition-transform"></i>
                                        </button>

                                        <!-- Welcome Message -->
                                        <div class="text-center">
                                            <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-xl">
                                                <i class="fas fa-user-circle text-white text-3xl"></i>
                                            </div>
                                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                                                {{ __('welcome') }}!
                                            </h2>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                {{ __('explore_our_platform') }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Auth Buttons -->
                                    <div class="p-6 space-y-4">
                                        <a href="{{ route('frontend.login') }}" 
                                           @click="closePanel()"
                                           class="auth-btn-primary group w-full">
                                            <div class="flex items-center justify-center space-x-3">
                                                <i class="fas fa-sign-in-alt group-hover:scale-110 transition-transform"></i>
                                                <span>{{ __('sign_in') }}</span>
                                            </div>
                                        </a>
                                        
                                        <a href="{{ route('frontend.register') }}" 
                                           @click="closePanel()"
                                           class="auth-btn-secondary group w-full">
                                            <div class="flex items-center justify-center space-x-3">
                                                <i class="fas fa-user-plus group-hover:scale-110 transition-transform"></i>
                                                <span>{{ __('sign_up') }}</span>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- Navigation Menu for Guests -->
                                    <div class="flex-1 overflow-y-auto py-4 px-4">
                                        <nav class="space-y-2">
                                            <!-- Home -->
                                            <a href="{{ route('frontend.index') }}"
                                               @click="closePanel()"
                                               class="touch-nav-item">
                                                <div class="touch-nav-icon bg-gradient-to-br from-blue-500 to-blue-600">
                                                    <i class="fas fa-home"></i>
                                                </div>
                                                <span class="touch-nav-text">{{ __('home') }}</span>
                                                <i class="fas fa-chevron-right touch-nav-arrow"></i>
                                            </a>

                                            <!-- Browse Ads -->
                                            <a href="{{ route('frontend.adlist') }}"
                                               @click="closePanel()"
                                               class="touch-nav-item">
                                                <div class="touch-nav-icon bg-gradient-to-br from-green-500 to-green-600">
                                                    <i class="fas fa-search"></i>
                                                </div>
                                                <span class="touch-nav-text">{{ __('browse_ads') }}</span>
                                                <i class="fas fa-chevron-right touch-nav-arrow"></i>
                                            </a>

                                            <!-- Categories -->
                                            <a href="{{ route('frontend.categories') }}"
                                               @click="closePanel()"
                                               class="touch-nav-item">
                                                <div class="touch-nav-icon bg-gradient-to-br from-purple-500 to-purple-600">
                                                    <i class="fas fa-th-large"></i>
                                                </div>
                                                <span class="touch-nav-text">{{ __('categories') }}</span>
                                                <i class="fas fa-chevron-right touch-nav-arrow"></i>
                                            </a>

                                            <!-- Pricing -->
                                            <a href="{{ route('frontend.priceplan') }}"
                                               @click="closePanel()"
                                               class="touch-nav-item">
                                                <div class="touch-nav-icon bg-gradient-to-br from-orange-500 to-orange-600">
                                                    <i class="fas fa-tags"></i>
                                                </div>
                                                <span class="touch-nav-text">{{ __('pricing_plan') }}</span>
                                                <i class="fas fa-chevron-right touch-nav-arrow"></i>
                                            </a>

                                            <!-- Blog -->
                                            @if(Route::has('frontend.blog'))
                                            <a href="{{ route('frontend.blog') }}"
                                               @click="closePanel()"
                                               class="touch-nav-item">
                                                <div class="touch-nav-icon bg-gradient-to-br from-indigo-500 to-indigo-600">
                                                    <i class="fas fa-blog"></i>
                                                </div>
                                                <span class="touch-nav-text">{{ __('blog') }}</span>
                                                <i class="fas fa-chevron-right touch-nav-arrow"></i>
                                            </a>
                                            @endif

                                            <!-- Contact -->
                                            <a href="{{ route('frontend.contact') }}"
                                               @click="closePanel()"
                                               class="touch-nav-item">
                                                <div class="touch-nav-icon bg-gradient-to-br from-teal-500 to-teal-600">
                                                    <i class="fas fa-envelope"></i>
                                                </div>
                                                <span class="touch-nav-text">{{ __('contact') }}</span>
                                                <i class="fas fa-chevron-right touch-nav-arrow"></i>
                                            </a>
                                        </nav>
                                    </div>
                                </div>
                            @endauth
                        </div>
                        
                        <!-- Click outside to close -->
                        <div @click="closePanel()" class="absolute inset-0 -z-10"></div>
                    </div>

                    <!-- Logout Forms -->
                    <form id="auth-logout-form" action="{{ route('frontend.logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                </div>
                <button @click="searchbar = !searchbar">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M21 21L15.0001 15M17 10C17 13.866 13.866 17 10 17C6.13401 17 3 13.866 3 10C3 6.13401 6.13401 3 10 3C13.866 3 17 6.13401 17 10Z"
                            stroke="var(--gray-100)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
            <div class="inline-flex gap-4 items-center">
                <button id="darkModeToggle" class="bg-white text-primary-500 border border-gray-100 p-1 rounded-full">
                    <span id="icon">
                        <!-- Custom SVG for Light Mode -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                        </svg>
                    </span>
                </button>
                @if ($setting->currency_changing && count($headerCurrencies))
                    @php
                        $currency_count = count($headerCurrencies) && count($headerCurrencies) > 1;
                        $current_currency_code = currentCurrencyCode();
                        $current_currency_symbol = currentCurrencySymbol();
                    @endphp
                    <span class="relative" x-data="{ currencyDropdown: false }" @click.outside="currencyDropdown = false">
                        <button @click="currencyDropdown = !currencyDropdown"
                            class="inline-flex heading-07 tarnsition-all duration-300 hover:text-white py-1.5 gap-1.5 items-center"
                            :class="currencyDropdown ? 'text-white' : ''" type="button">
                            <span>
                                {{ $current_currency_code }}
                            </span>
                            <x-svg.arrow-down-icon />
                        </button>

                        @if ($currency_count)
                            <div class="currDropdown" x-show="currencyDropdown" x-transition x-cloak
                                @click.outside="currencyDropdown=false">
                                <ul
                                    class="bg-white flex flex-col py-2 rounded-md border border-gray-100 drop-shadow-[drop-shadow(0px_8px_8px_rgba(28,33,38,0.03))_drop-shadow(0px_20px_24px_rgba(28,33,38,0.08))] min-w-[12rem] relative after:absolute after:border after:border-r-transparent after:border-b-transparent after:border-gray-200 after:rounded ltr:after:right-10 rtl:after:left-10 after:bg-white after:top-[-7.8px] after:h-4 after:w-4 after:transform after:rotate-[45deg] after:content-['']">
                                    @foreach ($headerCurrencies as $currency)
                                        <li>
                                            <a href="{{ route('changeCurrency', $currency->code) }}"
                                                class="hover:bg-primary-50 py-1 px-4 transition-all duration-300 flex text-gray-700 body-md-400 {{ $current_currency_code === $currency->code ? 'bg-primary-50' : '' }}">
                                                {{ $currency->code }} ({{ $currency->symbol }})
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </span>
                @endif

                @if ($language_enable && $setting->language_changing)
                    <span class="relative" x-data="{ langDropdown: false }" @click.outside="langDropdown = false">
                        <button @click="langDropdown = !langDropdown"
                            class="inline-flex heading-07 tarnsition-all duration-300 hover:text-white py-1.5 gap-1.5 items-center"
                            :class="langDropdown ? 'text-white' : ''" type="button">
                            <span>
                                {{ currentLanguage() ? currentLanguage()->name : 'Default' }}
                            </span>
                            <x-svg.arrow-down-icon />
                        </button>
                        <!-- Dropdown menu -->
                        <div class="langDropdown " x-show="langDropdown" x-transition x-cloak
                            @click.outside="langDropdown=false">
                            <ul
                                class="bg-white flex flex-col py-2 rounded-md border border-gray-100 drop-shadow-[drop-shadow(0px_8px_8px_rgba(28,33,38,0.03))_drop-shadow(0px_20px_24px_rgba(28,33,38,0.08))] min-w-[12rem] relative after:absolute after:border after:border-r-transparent after:border-b-transparent after:border-gray-200 after:rounded ltr:after:right-10 rtl:after:left-10 after:bg-white after:top-[-7.8px] after:h-4 after:w-4 after:transform after:rotate-[45deg] after:content-['']">
                                @foreach ($languages as $lang)
                                    <li>
                                        <a href="{{ route('changeLanguage', $lang->code) }}"
                                            class="hover:bg-primary-50 py-1 px-4 transition-all duration-300 flex text-gray-700 body-md-400 {{ currentLanguage()->name == $lang->name ? 'bg-primary-50' : '' }}">
                                            {{ $lang->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Select the input field and the list items
            const searchInput = document.getElementById("search-input");
            const countryListItems = document.querySelectorAll(".city-list ul li");

            // Add an event listener to the input field
            searchInput.addEventListener("input", function() {
                const inputValue = searchInput.value.toLowerCase().trim(); // Trim whitespace

                // Sort the list items based on input matching the first word
                countryListItems.forEach(function(item) {
                    const countryName = item.textContent.toLowerCase();
                    const firstWord = countryName.split(" ")[0];

                    if (firstWord === inputValue) {
                        item.style.display = "block";
                    } else if (countryName.includes(inputValue)) {
                        item.style.display = "block";
                    } else {
                        item.style.display = "none";
                    }
                });
            });
        });

        // Touch Panel Alpine.js Component
        function touchPanel() {
            return {
                panelOpen: false,
                startX: 0,
                startY: 0,
                currentX: 0,
                currentY: 0,
                isDragging: false,
                
                openPanel() {
                    this.panelOpen = true;
                    document.body.style.overflow = 'hidden';
                    this.addRippleEffect();
                },
                
                closePanel() {
                    this.panelOpen = false;
                    document.body.style.overflow = '';
                },
                
                handleTouchStart(e) {
                    this.startX = e.touches[0].clientX;
                    this.startY = e.touches[0].clientY;
                    this.isDragging = true;
                },
                
                handleTouchMove(e) {
                    if (!this.isDragging) return;
                    
                    this.currentX = e.touches[0].clientX;
                    this.currentY = e.touches[0].clientY;
                    
                    const deltaX = this.currentX - this.startX;
                    const deltaY = Math.abs(this.currentY - this.startY);
                    
                    // Prevent scrolling if horizontal swipe is detected
                    if (Math.abs(deltaX) > deltaY && Math.abs(deltaX) > 10) {
                        e.preventDefault();
                    }
                    
                    // Open panel with swipe right from edge (within 30px from left edge)
                    if (!this.panelOpen && this.startX <= 30 && deltaX > 50) {
                        this.openPanel();
                        this.isDragging = false;
                    }
                    
                    // Close panel with swipe left
                    if (this.panelOpen && deltaX < -50) {
                        this.closePanel();
                        this.isDragging = false;
                    }
                },
                
                handleTouchEnd() {
                    this.isDragging = false;
                },
                
                addRippleEffect() {
                    const button = this.$el.querySelector('.touch-btn');
                    const ripple = button.querySelector('.touch-ripple');
                    
                    ripple.style.opacity = '0.3';
                    ripple.style.transform = 'scale(0)';
                    ripple.style.transition = 'transform 0.6s, opacity 0.6s';
                    
                    setTimeout(() => {
                        ripple.style.transform = 'scale(2)';
                        ripple.style.opacity = '0';
                    }, 50);
                }
            }
        }
    </script>
@endpush

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
/* Touch Panel System Styles */
.touch-btn {
    animation: gentlePulse 3s ease-in-out infinite;
    -webkit-tap-highlight-color: transparent;
    user-select: none;
}

@keyframes gentlePulse {
    0%, 100% {
        box-shadow: 0 4px 20px 0 rgba(59, 130, 246, 0.4),
                    0 0 0 0 rgba(59, 130, 246, 0.7);
    }
    50% {
        box-shadow: 0 8px 30px 0 rgba(59, 130, 246, 0.6),
                    0 0 0 10px rgba(59, 130, 246, 0);
    }
}

.touch-ripple {
    background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 70%);
    border-radius: inherit;
}

/* Touch Panel Container */
.touch-panel-container {
    max-width: 380px;
    box-shadow: 10px 0 30px rgba(0, 0, 0, 0.3);
}

/* Navigation Items */
.touch-nav-item {
    @apply flex items-center p-4 mx-3 rounded-2xl 
           bg-white/60 dark:bg-gray-800/60 
           backdrop-blur-lg border border-white/30 dark:border-gray-700/30
           transition-all duration-300 ease-out
           hover:bg-white/80 dark:hover:bg-gray-800/80
           hover:shadow-xl hover:shadow-black/10
           active:scale-[0.98] active:bg-white/90 dark:active:bg-gray-800/90
           hover:-translate-y-1;
    -webkit-tap-highlight-color: transparent;
    user-select: none;
    margin-bottom: 8px;
}

.touch-nav-item.active {
    @apply bg-gradient-to-r from-blue-500/30 to-purple-600/30
           border-blue-500/50 dark:border-blue-400/50
           shadow-xl shadow-blue-500/20;
    transform: translateY(-2px);
}

.touch-nav-item.logout-item:hover {
    @apply bg-red-50/80 dark:bg-red-900/20
           border-red-300/50 dark:border-red-600/50;
}

.touch-nav-icon {
    @apply w-12 h-12 rounded-xl 
           flex items-center justify-center
           text-white shadow-lg text-lg
           transition-transform duration-300;
    min-width: 48px;
}

.touch-nav-item:hover .touch-nav-icon {
    transform: scale(1.1) rotate(5deg);
}

.touch-nav-text {
    @apply flex-1 ml-4 text-gray-900 dark:text-gray-100 
           font-semibold text-base leading-tight;
}

.touch-nav-arrow {
    @apply text-gray-400 dark:text-gray-500
           transition-all duration-300;
}

.touch-nav-item:hover .touch-nav-arrow {
    @apply transform translate-x-2 scale-110 text-gray-600 dark:text-gray-300;
}

/* Auth Buttons */
.auth-btn-primary {
    @apply block px-6 py-4 text-center font-semibold text-white 
           bg-gradient-to-r from-blue-600 to-purple-600
           rounded-2xl shadow-lg hover:shadow-xl
           transition-all duration-300 ease-out
           hover:scale-105 active:scale-95
           border border-blue-500/20;
    -webkit-tap-highlight-color: transparent;
}

.auth-btn-primary:hover {
    background: linear-gradient(45deg, #3b82f6, #8b5cf6);
    box-shadow: 0 20px 40px rgba(59, 130, 246, 0.3);
}

.auth-btn-secondary {
    @apply block px-6 py-4 text-center font-semibold text-gray-700 dark:text-gray-200
           bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg
           rounded-2xl border-2 border-gray-200 dark:border-gray-600
           shadow-lg hover:shadow-xl
           transition-all duration-300 ease-out
           hover:scale-105 active:scale-95
           hover:bg-white dark:hover:bg-gray-700/80;
    -webkit-tap-highlight-color: transparent;
}

/* Custom Scrollbar */
.touch-panel-container nav {
    scrollbar-width: thin;
    scrollbar-color: rgba(156, 163, 175, 0.3) transparent;
}

.touch-panel-container nav::-webkit-scrollbar {
    width: 6px;
}

.touch-panel-container nav::-webkit-scrollbar-track {
    background: transparent;
    border-radius: 3px;
}

.touch-panel-container nav::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, rgba(156, 163, 175, 0.3), rgba(156, 163, 175, 0.5));
    border-radius: 3px;
}

.touch-panel-container nav::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(180deg, rgba(156, 163, 175, 0.5), rgba(156, 163, 175, 0.7));
}

/* Responsive Improvements */
@media (max-width: 480px) {
    .touch-panel-container {
        max-width: 100vw;
    }
    
    .touch-nav-item {
        margin-left: 12px;
        margin-right: 12px;
        padding: 16px;
    }
    
    .touch-nav-icon {
        width: 48px;
        height: 48px;
        min-width: 48px;
    }
    
    .touch-nav-text {
        font-size: 16px;
    }
}

/* Accessibility */
@media (prefers-reduced-motion: reduce) {
    .touch-nav-item,
    .touch-nav-arrow,
    .touch-nav-icon,
    .touch-btn {
        animation: none;
        transition: none;
    }
}

@media (prefers-contrast: high) {
    .touch-nav-item {
        border-width: 2px;
        background: rgba(255, 255, 255, 0.95);
    }
    
    .touch-nav-item.active {
        background: rgba(59, 130, 246, 0.3);
        border-color: rgb(59, 130, 246);
    }
}

/* Panel Animation Enhancements */
.touch-panel-container > div {
    animation: slideInContent 0.4s ease-out;
}

@keyframes slideInContent {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Loading state (optional) */
.touch-nav-item.loading {
    pointer-events: none;
    opacity: 0.7;
}

.touch-nav-item.loading .touch-nav-icon {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
