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
                <!-- Touch Panel System -->
                <div x-data="{ 
                    panelOpen: false,
                    startX: 0,
                    startY: 0,
                    currentX: 0,
                    currentY: 0,
                    isDragging: false,
                    
                    openPanel() { 
                        this.panelOpen = true; 
                        document.body.style.overflow = 'hidden';
                        this.addPulseEffect();
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
                        
                        // Prevent default scrolling for horizontal swipes
                        if (Math.abs(deltaX) > deltaY && Math.abs(deltaX) > 15) {
                            e.preventDefault();
                        }
                        
                        // Open panel with swipe right from left edge (within 40px)
                        if (!this.panelOpen && this.startX <= 40 && deltaX > 80) {
                            this.openPanel();
                            this.isDragging = false;
                        }
                        
                        // Close panel with swipe left
                        if (this.panelOpen && deltaX < -80) {
                            this.closePanel();
                            this.isDragging = false;
                        }
                    },
                    
                    handleTouchEnd() {
                        this.isDragging = false;
                    },
                    
                    addPulseEffect() {
                        const button = this.$el.querySelector('.touch-menu-btn');
                        if (button) {
                            button.classList.add('pulse-effect');
                            setTimeout(() => button.classList.remove('pulse-effect'), 600);
                        }
                    }
                }" 
                @touchstart="handleTouchStart($event)"
                @touchmove="handleTouchMove($event)" 
                @touchend="handleTouchEnd()"
                class="touch-panel-wrapper">
                     
                    <!-- Touch Menu Button -->
                    <button @click="openPanel()" 
                            class="touch-menu-btn w-11 h-11 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 
                                   border border-white/20 shadow-lg hover:shadow-xl
                                   flex items-center justify-center relative overflow-hidden
                                   transition-all duration-300 ease-out transform
                                   hover:scale-110 active:scale-95 hover:rotate-180">
                        <!-- Icon with rotation animation -->
                        <i class="fas fa-bars text-white text-lg transition-transform duration-300" 
                           :class="panelOpen ? 'rotate-90' : ''"></i>
                        
                        <!-- Animated background -->
                        <div class="absolute inset-0 bg-gradient-to-br from-purple-500 to-pink-500 opacity-0 hover:opacity-100 transition-opacity duration-300"></div>
                        
                        <!-- Pulse effect -->
                        <div class="absolute inset-0 rounded-xl bg-white/30 scale-0 pulse-ring"></div>
                    </button>

                    <!-- Swipe Indicator (shows at screen edge) -->
                    <div x-show="!panelOpen" 
                         class="fixed left-0 top-1/2 transform -translate-y-1/2 z-[99998] swipe-indicator">
                        <div class="w-1 h-16 bg-gradient-to-b from-blue-500 to-purple-600 rounded-r-full opacity-30 hover:opacity-60 transition-opacity duration-300"></div>
                        <div class="absolute left-2 top-1/2 transform -translate-y-1/2 text-xs text-gray-400 writing-vertical">Swipe →</div>
                    </div>

                    <!-- Touch Panel Overlay -->
                    <div x-show="panelOpen" 
                         x-transition:enter="transition-all ease-out duration-400"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition-all ease-in duration-300"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         @click="closePanel()"
                         class="fixed inset-0 z-[999999] bg-black/80"
                         style="display: none; position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; width: 100vw !important; height: 100vh !important; z-index: 999999 !important;">
                         
                        <!-- Panel Container -->
                        <div @click.stop
                             x-show="panelOpen"
                             x-transition:enter="transform transition-all ease-out duration-400"
                             x-transition:enter-start="-translate-x-full opacity-0 scale-95"
                             x-transition:enter-end="translate-x-0 opacity-100 scale-100"
                             x-transition:leave="transform transition-all ease-in duration-300"
                             x-transition:leave-start="translate-x-0 opacity-100 scale-100"
                             x-transition:leave-end="-translate-x-full opacity-0 scale-95"
                             class="h-full w-80 bg-gradient-to-br from-blue-500/95 to-purple-600/95 backdrop-blur-xl shadow-2xl border-r border-white/20 panel-content"
                             style="position: relative; z-index: 1000000;">
                             
                                                         @auth('user')
                                 <!-- Authenticated User Panel -->
                                 <div class="h-full flex flex-col">
                                     
                                     <!-- Header with User Info -->
                                     <div class="p-6 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                                         <!-- Close button -->
                                         <button @click="closePanel()" 
                                                 class="absolute top-4 right-4 w-8 h-8 rounded-lg 
                                                        bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600
                                                        flex items-center justify-center
                                                        transition-colors duration-200">
                                             <i class="fas fa-times text-gray-600 dark:text-gray-400 text-sm"></i>
                                         </button>

                                                                                 <!-- User Profile -->
                                         <div class="flex items-center space-x-3">
                                             <div class="relative">
                                                 <img class="w-16 h-16 rounded-xl object-cover border-2 border-gray-200 dark:border-gray-600" 
                                                      src="{{ authUser()->image_url }}" 
                                                      alt="{{ authUser()->name }}">
                                                 @if (auth('user')->user()->document_verified && auth('user')->user()->document_verified->status == 'approved')
                                                     <div class="absolute -top-1 -right-1 w-5 h-5 bg-green-500 rounded-full flex items-center justify-center border-2 border-white dark:border-gray-800">
                                                         <i class="fas fa-check text-white text-xs"></i>
                                                     </div>
                                                 @endif
                                             </div>
                                             <div class="flex-1 min-w-0">
                                                 <h2 class="text-lg font-semibold text-gray-900 dark:text-white truncate">
                                                     {{ authUser()->name }}
                                                 </h2>
                                                 <p class="text-sm text-gray-600 dark:text-gray-400 truncate">
                                                     {{ authUser()->email }}
                                                 </p>
                                             </div>
                                         </div>
                                     </div>

                                                                         <!-- Navigation Menu -->
                                     <div class="flex-1 overflow-y-auto py-4 px-4">
                                         <nav class="space-y-2">
                                                                                           <!-- Dashboard -->
                                              <a href="{{ route('frontend.dashboard') }}"
                                                 @click="closePanel()"
                                                 class="touch-nav-item nav-item-bounce {{ request()->routeIs('frontend.dashboard') ? 'bg-blue-50/20 border-blue-200/30' : '' }}">
                                                  <div class="touch-nav-icon bg-gradient-to-br from-blue-500 to-blue-600 icon-pulse">
                                                      <i class="fas fa-tachometer-alt text-white"></i>
                                                  </div>
                                                  <span class="touch-nav-text text-white">{{ __('overview') }}</span>
                                                  <i class="fas fa-chevron-right text-white/70 nav-arrow"></i>
                                              </a>

                                                                                         <!-- My Ads -->
                                             <a href="{{ route('frontend.my.listing') }}"
                                                @click="closePanel()"
                                                class="touch-nav-item nav-item-bounce {{ request()->routeIs('frontend.my.listing') ? 'bg-orange-50/20 border-orange-200/30' : '' }}">
                                                 <div class="touch-nav-icon bg-gradient-to-br from-orange-500 to-orange-600 icon-pulse">
                                                     <i class="fas fa-list-alt text-white"></i>
                                                 </div>
                                                 <span class="touch-nav-text text-white">{{ __('my_ads') }}</span>
                                                 <i class="fas fa-chevron-right text-white/70 nav-arrow"></i>
                                             </a>

                                             <!-- Post Listing -->
                                             <a href="{{ route('frontend.post') }}"
                                                @click="closePanel()"
                                                class="touch-nav-item nav-item-bounce {{ request()->routeIs('frontend.post') ? 'bg-purple-50/20 border-purple-200/30' : '' }}">
                                                 <div class="touch-nav-icon bg-gradient-to-br from-purple-500 to-purple-600 icon-pulse">
                                                     <i class="fas fa-plus-circle text-white"></i>
                                                 </div>
                                                 <span class="touch-nav-text text-white">{{ __('post_listing') }}</span>
                                                 <i class="fas fa-chevron-right text-white/70 nav-arrow"></i>
                                             </a>

                                             <!-- Messages -->
                                             <a href="{{ route('frontend.message') }}"
                                                @click="closePanel()"
                                                class="touch-nav-item nav-item-bounce {{ request()->routeIs('message') ? 'bg-indigo-50/20 border-indigo-200/30' : '' }}">
                                                 <div class="touch-nav-icon bg-gradient-to-br from-indigo-500 to-indigo-600 icon-pulse">
                                                     <i class="fas fa-comments text-white"></i>
                                                 </div>
                                                 <span class="touch-nav-text text-white">{{ __('message') }}</span>
                                                 <i class="fas fa-chevron-right text-white/70 nav-arrow"></i>
                                             </a>

                                             <!-- Favorites -->
                                             <a href="{{ route('frontend.favorite.list') }}"
                                                @click="closePanel()"
                                                class="touch-nav-item nav-item-bounce {{ request()->routeIs('favorite-listing') ? 'bg-red-50/20 border-red-200/30' : '' }}">
                                                 <div class="touch-nav-icon bg-gradient-to-br from-red-500 to-pink-600 icon-pulse">
                                                     <i class="fas fa-heart text-white"></i>
                                                 </div>
                                                 <span class="touch-nav-text text-white">{{ __('favorite_ads') }}</span>
                                                 <i class="fas fa-chevron-right text-white/70 nav-arrow"></i>
                                             </a>

                                             <!-- Account Settings -->
                                             <a href="{{ route('frontend.account-setting') }}"
                                                @click="closePanel()"
                                                class="touch-nav-item nav-item-bounce {{ request()->routeIs('frontend.account-setting') ? 'bg-gray-50/20 border-gray-200/30' : '' }}">
                                                 <div class="touch-nav-icon bg-gradient-to-br from-slate-500 to-slate-600 icon-pulse">
                                                     <i class="fas fa-cog text-white"></i>
                                                 </div>
                                                 <span class="touch-nav-text text-white">{{ __('account_setting') }}</span>
                                                 <i class="fas fa-chevron-right text-white/70 nav-arrow"></i>
                                             </a>

                                             <!-- Divider -->
                                             <div class="border-t border-gray-200 dark:border-gray-600 my-4"></div>

                                             <!-- Logout -->
                                             <a href="javascript:void(0)"
                                                @click="closePanel(); document.getElementById('auth-logout-form').submit();"
                                                class="touch-nav-item hover:bg-red-50 dark:hover:bg-red-900/20">
                                                 <div class="touch-nav-icon bg-red-500">
                                                     <i class="fas fa-sign-out-alt text-white"></i>
                                                 </div>
                                                 <span class="touch-nav-text text-red-600 dark:text-red-400">{{ __('logout') }}</span>
                                                 <i class="fas fa-chevron-right text-red-400"></i>
                                             </a>
                                        </nav>
                                    </div>
                                </div>
                                                         @else
                                 <!-- Guest User Panel -->
                                 <div class="h-full flex flex-col">
                                     
                                     <!-- Header for Guests -->
                                     <div class="p-6 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                                         <!-- Close button -->
                                         <button @click="closePanel()" 
                                                 class="absolute top-4 right-4 w-8 h-8 rounded-lg 
                                                        bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600
                                                        flex items-center justify-center
                                                        transition-colors duration-200">
                                             <i class="fas fa-times text-gray-600 dark:text-gray-400 text-sm"></i>
                                         </button>

                                         <!-- Welcome Message -->
                                         <div class="text-center">
                                             <div class="w-16 h-16 mx-auto mb-3 bg-blue-500 dark:bg-blue-600 rounded-xl flex items-center justify-center">
                                                 <i class="fas fa-user-circle text-white text-2xl"></i>
                                             </div>
                                             <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                 {{ __('welcome') }}!
                                             </h2>
                                             <p class="text-sm text-gray-600 dark:text-gray-400">
                                                 Добро пожаловать на платформу
                                             </p>
                                         </div>
                                     </div>

                                     <!-- Auth Buttons -->
                                     <div class="p-4 space-y-3 border-b border-gray-200 dark:border-gray-700">
                                         <a href="{{ route('users.login') }}" 
                                            @click="closePanel()"
                                            class="flex items-center justify-center space-x-3 w-full px-4 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200">
                                             <i class="fas fa-sign-in-alt"></i>
                                             <span class="font-medium">{{ __('sign_in') }}</span>
                                         </a>
                                         
                                         <a href="{{ route('frontend.signup') }}" 
                                            @click="closePanel()"
                                            class="flex items-center justify-center space-x-3 w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 rounded-lg transition-colors duration-200">
                                             <i class="fas fa-user-plus"></i>
                                             <span class="font-medium">{{ __('sign_up') }}</span>
                                         </a>
                                     </div>

                                                                         <!-- Navigation Menu for Guests -->
                                     <div class="flex-1 overflow-y-auto py-4 px-4">
                                         <nav class="space-y-2">
                                             <!-- Home -->
                                             <a href="{{ route('frontend.index') }}"
                                                @click="closePanel()"
                                                class="touch-nav-item nav-item-bounce">
                                                 <div class="touch-nav-icon bg-gradient-to-br from-blue-500 to-blue-600 icon-pulse">
                                                     <i class="fas fa-home text-white"></i>
                                                 </div>
                                                 <span class="touch-nav-text text-white">{{ __('home') }}</span>
                                                 <i class="fas fa-chevron-right text-white/70 nav-arrow"></i>
                                             </a>

                                             <!-- Browse Ads -->
                                             <a href="{{ route('frontend.adlist') }}"
                                                @click="closePanel()"
                                                class="touch-nav-item nav-item-bounce">
                                                 <div class="touch-nav-icon bg-gradient-to-br from-green-500 to-green-600 icon-pulse">
                                                     <i class="fas fa-search text-white"></i>
                                                 </div>
                                                 <span class="touch-nav-text text-white">{{ __('browse_ads') }}</span>
                                                 <i class="fas fa-chevron-right text-white/70 nav-arrow"></i>
                                             </a>

                                             <!-- Categories -->
                                             <a href="{{ route('frontend.ads') }}"
                                                @click="closePanel()"
                                                class="touch-nav-item nav-item-bounce">
                                                 <div class="touch-nav-icon bg-gradient-to-br from-purple-500 to-purple-600 icon-pulse">
                                                     <i class="fas fa-th-large text-white"></i>
                                                 </div>
                                                 <span class="touch-nav-text text-white">{{ __('categories') }}</span>
                                                 <i class="fas fa-chevron-right text-white/70 nav-arrow"></i>
                                             </a>

                                             <!-- Pricing -->
                                             <a href="{{ route('frontend.priceplan') }}"
                                                @click="closePanel()"
                                                class="touch-nav-item nav-item-bounce">
                                                 <div class="touch-nav-icon bg-gradient-to-br from-orange-500 to-orange-600 icon-pulse">
                                                     <i class="fas fa-tags text-white"></i>
                                                 </div>
                                                 <span class="touch-nav-text text-white">{{ __('pricing_plan') }}</span>
                                                 <i class="fas fa-chevron-right text-white/70 nav-arrow"></i>
                                             </a>

                                             <!-- Contact -->
                                             <a href="{{ route('frontend.contact') }}"
                                                @click="closePanel()"
                                                class="touch-nav-item nav-item-bounce">
                                                 <div class="touch-nav-icon bg-gradient-to-br from-teal-500 to-teal-600 icon-pulse">
                                                     <i class="fas fa-envelope text-white"></i>
                                                 </div>
                                                 <span class="touch-nav-text text-white">{{ __('contact') }}</span>
                                                 <i class="fas fa-chevron-right text-white/70 nav-arrow"></i>
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

        // Simple panel functionality  
        // All needed functionality is already in Alpine.js x-data
    </script>
@endpush

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
/* Touch Menu Button Animations */
.touch-menu-btn {
    animation: breathe 3s ease-in-out infinite;
}

@keyframes breathe {
    0%, 100% { 
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
        transform: scale(1);
    }
    50% { 
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.6);
        transform: scale(1.05);
    }
}

.pulse-effect .pulse-ring {
    animation: pulse-ring 0.6s ease-out;
}

@keyframes pulse-ring {
    0% {
        transform: scale(0);
        opacity: 1;
    }
    100% {
        transform: scale(2);
        opacity: 0;
    }
}

/* Swipe Indicator */
.swipe-indicator {
    animation: swipe-hint 4s ease-in-out infinite;
}

@keyframes swipe-hint {
    0%, 80%, 100% {
        opacity: 0.3;
        transform: translateY(-50%) translateX(0);
    }
    40% {
        opacity: 0.8;
        transform: translateY(-50%) translateX(5px);
    }
}

.writing-vertical {
    writing-mode: vertical-lr;
    text-orientation: mixed;
}

/* Panel Content Animation */
.panel-content {
    animation: panel-glow 0.4s ease-out;
}

@keyframes panel-glow {
    from {
        box-shadow: 0 0 0 rgba(59, 130, 246, 0);
    }
    to {
        box-shadow: 0 20px 40px rgba(59, 130, 246, 0.1);
    }
}

/* Touch Navigation Items */
.touch-nav-item {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    margin: 0 0 8px 0;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    -webkit-tap-highlight-color: transparent;
    position: relative;
    overflow: hidden;
    backdrop-filter: blur(10px);
}

.touch-nav-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    flex-shrink: 0;
    transition: all 0.3s ease;
    position: relative;
}

.touch-nav-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    transition: left 0.5s;
}

.touch-nav-item:hover::before {
    left: 100%;
}

.touch-nav-item:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.4);
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 8px 25px rgba(255, 255, 255, 0.1);
}

.touch-nav-item:hover .touch-nav-icon {
    transform: scale(1.1) rotate(5deg);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.touch-nav-item:active {
    transform: translateY(0) scale(0.98);
    background: rgba(255, 255, 255, 0.15);
}

/* Navigation Item Bounce Animation */
.nav-item-bounce {
    animation: nav-bounce 0.6s ease-out;
}

@keyframes nav-bounce {
    0% {
        opacity: 0;
        transform: translateX(-20px);
    }
    60% {
        opacity: 1;
        transform: translateX(5px);
    }
    100% {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Dark mode */
@media (prefers-color-scheme: dark) {
    .touch-nav-item {
        background: rgb(31, 41, 55);
        border-color: rgb(75, 85, 99);
    }
    
    .touch-nav-item:hover {
        background: rgb(55, 65, 81);
        border-color: rgb(107, 114, 128);
    }
    
    .touch-nav-item:active {
        background: rgb(75, 85, 99);
    }
}

/* Icon styles simplified */
.touch-nav-item i.icon-pulse {
    transition: all 0.3s ease;
    min-width: 20px;
}

.touch-nav-item:hover i.icon-pulse {
    transform: scale(1.2);
    color: rgb(59, 130, 246) !important;
}

@media (prefers-color-scheme: dark) {
    .touch-nav-item:hover i.icon-pulse {
        color: rgb(147, 197, 253) !important;
    }
}

.icon-pulse {
    animation: icon-pulse 2s ease-in-out infinite;
}

@keyframes icon-pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

.touch-nav-text {
    flex: 1;
    font-weight: 500;
    font-size: 15px;
    color: white;
    transition: all 0.3s ease;
}

.touch-nav-item:hover .touch-nav-text {
    transform: translateX(3px);
    color: rgba(255, 255, 255, 0.9);
}

/* Footer Touch Links */
.footer-touch-link {
    display: flex;
    align-items: center;
    padding: 8px 12px;
    border-radius: 8px;
    color: inherit;
    text-decoration: none;
    transition: all 0.3s ease;
    -webkit-tap-highlight-color: transparent;
}

.footer-touch-link:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateX(4px);
    color: inherit;
}

.footer-touch-link i {
    transition: all 0.3s ease;
}

.footer-touch-link:hover i {
    transform: scale(1.2);
}

.nav-arrow {
    transition: all 0.3s ease;
}

.touch-nav-item:hover .nav-arrow {
    transform: translateX(5px) scale(1.2);
    color: rgb(59, 130, 246) !important;
}

@media (prefers-color-scheme: dark) {
    .touch-nav-text {
        color: rgb(243, 244, 246);
    }
    
    .touch-nav-item:hover .touch-nav-text {
        color: rgb(147, 197, 253);
    }
    
    .touch-nav-item:hover .nav-arrow {
        color: rgb(147, 197, 253) !important;
    }
}

/* Active states for specific items */
.touch-nav-item.bg-blue-50 {
    background: rgb(239, 246, 255) !important;
    border-color: rgb(147, 197, 253);
}

.touch-nav-item.bg-orange-50 {
    background: rgb(255, 247, 237) !important;
    border-color: rgb(251, 146, 60);
}

.touch-nav-item.bg-purple-50 {
    background: rgb(250, 245, 255) !important;
    border-color: rgb(196, 181, 253);
}

.touch-nav-item.bg-indigo-50 {
    background: rgb(238, 242, 255) !important;
    border-color: rgb(165, 180, 252);
}

.touch-nav-item.bg-red-50 {
    background: rgb(254, 242, 242) !important;
    border-color: rgb(252, 165, 165);
}

.touch-nav-item.bg-gray-50 {
    background: rgb(249, 250, 251) !important;
    border-color: rgb(209, 213, 219);
}

/* Dark mode active states */
@media (prefers-color-scheme: dark) {
    .touch-nav-item.dark\:bg-blue-900\/20 {
        background: rgba(30, 58, 138, 0.2) !important;
        border-color: rgb(59, 130, 246);
    }
    
    .touch-nav-item.dark\:bg-orange-900\/20 {
        background: rgba(124, 45, 18, 0.2) !important;
        border-color: rgb(249, 115, 22);
    }
    
    .touch-nav-item.dark\:bg-purple-900\/20 {
        background: rgba(88, 28, 135, 0.2) !important;
        border-color: rgb(147, 51, 234);
    }
    
    .touch-nav-item.dark\:bg-indigo-900\/20 {
        background: rgba(49, 46, 129, 0.2) !important;
        border-color: rgb(99, 102, 241);
    }
    
    .touch-nav-item.dark\:bg-red-900\/20 {
        background: rgba(127, 29, 29, 0.2) !important;
        border-color: rgb(239, 68, 68);
    }
    
    .touch-nav-item.dark\:bg-gray-700\/20 {
        background: rgba(55, 65, 81, 0.2) !important;
        border-color: rgb(107, 114, 128);
    }
}

/* Hover effects for logout */
.touch-nav-item.hover\:bg-red-50:hover {
    background: rgb(254, 242, 242) !important;
}

@media (prefers-color-scheme: dark) {
    .touch-nav-item.dark\:hover\:bg-red-900\/20:hover {
        background: rgba(127, 29, 29, 0.2) !important;
    }
}

/* Touch Footer Styles */
.touch-footer {
    z-index: 999999 !important;
}

.touch-footer-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 12px 8px;
    border-radius: 12px;
    background: white;
    border: 1px solid rgb(229, 231, 235);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    -webkit-tap-highlight-color: transparent;
    position: relative;
    overflow: hidden;
    min-height: 70px;
}

.touch-footer-item:hover {
    background: rgb(249, 250, 251);
    border-color: rgb(209, 213, 219);
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 8px 15px -3px rgb(0 0 0 / 0.1);
}

.touch-footer-item:active {
    transform: translateY(0) scale(0.98);
    background: rgb(243, 244, 246);
}

@media (prefers-color-scheme: dark) {
    .touch-footer-item {
        background: rgb(31, 41, 55);
        border-color: rgb(75, 85, 99);
    }
    
    .touch-footer-item:hover {
        background: rgb(55, 65, 81);
        border-color: rgb(107, 114, 128);
    }
    
    .touch-footer-item:active {
        background: rgb(75, 85, 99);
    }
}

.footer-icon-pulse {
    animation: footer-icon-pulse 2.5s ease-in-out infinite;
}

@keyframes footer-icon-pulse {
    0%, 100% {
        transform: scale(1);
        opacity: 1;
    }
    50% {
        transform: scale(1.1);
        opacity: 0.8;
    }
}

.touch-footer-item:hover .footer-icon-pulse {
    animation-duration: 1s;
    transform: scale(1.2);
}

/* Footer shimmer effect */
.touch-footer-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    transition: left 0.5s;
}

.touch-footer-item:hover::before {
    left: 100%;
}
</style>
