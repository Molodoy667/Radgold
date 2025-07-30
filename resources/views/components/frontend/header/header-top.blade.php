<div class="header-top bg-primary-800 dark:bg-gray-900/90 dark:backdrop-blur-xl dark:border-b dark:border-white/20 text-white/80 header-glass-dark">
    <div class="container">

        <div class="flex gap-3 justify-between items-center py-1.5">
            <ul class="lg:!inline-flex hidden flex-wrap gap-5 items-center">
                <li>
                    <a href="{{ route('frontend.priceplan') }}"
                        class="heading-07 transition-all duration-300 text-white/80 hover:text-white">{{ __('pricing_plan') }}</a>
                </li>


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
                        
                        // Open panel with swipe right from left edge (150px from left side)
                        if (!this.panelOpen && this.startX <= 150 && deltaX > 60) {
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
                class="touch-panel-wrapper"
                x-init="
                    // Add touch area for left edge swipe detection
                    const touchArea = document.createElement('div');
                    touchArea.style.cssText = 'position: fixed; top: 0; left: 0; width: 150px; height: 100vh; z-index: 99997; pointer-events: auto;';
                    touchArea.addEventListener('touchstart', (e) => handleTouchStart(e), { passive: false });
                    touchArea.addEventListener('touchmove', (e) => handleTouchMove(e), { passive: false });
                    touchArea.addEventListener('touchend', (e) => handleTouchEnd(e), { passive: false });
                    document.body.appendChild(touchArea);
                    
                    // Cleanup
                    this.$el.addEventListener('alpine:destroying', () => {
                        document.body.removeChild(touchArea);
                    });
                ">
                     
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
                         class="fixed inset-0 z-[99999] bg-black/80 touch-panel-overlay"
                         style="display: none; position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; width: 100vw !important; height: 100vh !important; z-index: 99999 !important;">
                         
                        <!-- Panel Container -->
                        <div @click.stop class="touch-panel-container"
                             x-show="panelOpen"
                             x-transition:enter="transform transition-all ease-out duration-400"
                             x-transition:enter-start="-translate-x-full opacity-0 scale-95"
                             x-transition:enter-end="translate-x-0 opacity-100 scale-100"
                             x-transition:leave="transform transition-all ease-in duration-300"
                             x-transition:leave-start="translate-x-0 opacity-100 scale-100"
                             x-transition:leave-end="-translate-x-full opacity-0 scale-95"
                             class="h-full w-80 bg-gradient-to-br from-blue-500/95 to-purple-600/95 dark:from-gray-800/95 dark:to-gray-900/95 backdrop-blur-xl shadow-2xl border-r border-white/20 dark:border-gray-700/30 panel-content"
                             style="position: relative; z-index: 1000000;">
                             
                                                         @auth('user')
                                 <!-- Authenticated User Panel -->
                                 <div class="h-full flex flex-col">
                                     
                                     <!-- Header with User Info - Glass Style -->
                                     <div class="p-6 bg-white/12 dark:bg-gray-800/30 backdrop-blur-xl border-b border-white/25 dark:border-gray-600/40 glass-header">
                                         <!-- Close button - Glass Pulsing -->
                                         <button @click="closePanel()" 
                                                 class="absolute top-4 right-4 w-10 h-10 rounded-xl
                                                        glass-close-btn
                                                        flex items-center justify-center
                                                        transition-all duration-300">
                                             <i class="fas fa-times text-white text-sm pulse-icon"></i>
                                         </button>

                                                                                 <!-- User Profile Glass Container -->
                                         <div class="flex items-center space-x-3 p-3 rounded-xl bg-white/10 dark:bg-gray-700/30 backdrop-blur-md border border-white/20 dark:border-gray-600/30">
                                             <div class="relative">
                                                 <img class="w-16 h-16 rounded-xl object-cover border-2 border-white/30" 
                                                      src="{{ authUser()->image_url }}" 
                                                      alt="{{ authUser()->name }}">
                                                 @if (auth('user')->user()->document_verified && auth('user')->user()->document_verified->status == 'approved')
                                                     <div class="absolute -top-1 -right-1 w-5 h-5 bg-green-500 rounded-full flex items-center justify-center border-2 border-white">
                                                         <i class="fas fa-check text-white text-xs"></i>
                                                     </div>
                                                 @endif
                                             </div>
                                             <div class="flex-1 min-w-0">
                                                 <h2 class="text-lg font-semibold text-white truncate">
                                                     {{ authUser()->name }}
                                                 </h2>
                                                 <p class="text-sm text-white/80 truncate">
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
                                                     <i class="fas fa-chart-line text-white"></i>
                                                 </div>
                                                 <span class="touch-nav-text text-white">{{ __('overview') }}</span>
                                                 <i class="fas fa-chevron-right text-white/70 nav-arrow"></i>
                                             </a>

                                                                                           <!-- My Ads -->
                                             <a href="{{ route('frontend.my.listing') }}"
                                                @click="closePanel()"
                                                class="touch-nav-item nav-item-bounce {{ request()->routeIs('frontend.my.listing') ? 'bg-orange-50/20 border-orange-200/30' : '' }}">
                                                 <div class="touch-nav-icon bg-gradient-to-br from-orange-500 to-orange-600 icon-pulse">
                                                     <i class="fas fa-store text-white"></i>
                                                 </div>
                                                 <span class="touch-nav-text text-white">{{ __('my_ads') }}</span>
                                                 <i class="fas fa-chevron-right text-white/70 nav-arrow"></i>
                                             </a>

                                             <!-- Post Listing -->
                                             <a href="{{ route('frontend.post') }}"
                                                @click="closePanel()"
                                                class="touch-nav-item nav-item-bounce {{ request()->routeIs('frontend.post') ? 'bg-purple-50/20 border-purple-200/30' : '' }}">
                                                 <div class="touch-nav-icon bg-gradient-to-br from-purple-500 to-purple-600 icon-pulse">
                                                     <i class="fas fa-bullhorn text-white"></i>
                                                 </div>
                                                 <span class="touch-nav-text text-white">{{ __('post_listing') }}</span>
                                                 <i class="fas fa-chevron-right text-white/70 nav-arrow"></i>
                                             </a>

                                             <!-- Messages -->
                                             <a href="{{ route('frontend.message') }}"
                                                @click="closePanel()"
                                                class="touch-nav-item nav-item-bounce {{ request()->routeIs('message') ? 'bg-indigo-50/20 border-indigo-200/30' : '' }}">
                                                 <div class="touch-nav-icon bg-gradient-to-br from-indigo-500 to-indigo-600 icon-pulse">
                                                     <i class="fas fa-paper-plane text-white"></i>
                                                 </div>
                                                 <span class="touch-nav-text text-white">{{ __('message') }}</span>
                                                 <i class="fas fa-chevron-right text-white/70 nav-arrow"></i>
                                             </a>

                                             <!-- Favorites -->
                                             <a href="{{ route('frontend.favorite.list') }}"
                                                @click="closePanel()"
                                                class="touch-nav-item nav-item-bounce {{ request()->routeIs('favorite-listing') ? 'bg-red-50/20 border-red-200/30' : '' }}">
                                                 <div class="touch-nav-icon bg-gradient-to-br from-red-500 to-pink-600 icon-pulse">
                                                     <i class="fas fa-bookmark text-white"></i>
                                                 </div>
                                                 <span class="touch-nav-text text-white">{{ __('favorite_ads') }}</span>
                                                 <i class="fas fa-chevron-right text-white/70 nav-arrow"></i>
                                             </a>

                                                                                          <!-- Public Profile -->
                                             <a href="{{ route('frontend.seller.profile', authUser()->username) }}"
                                               @click="closePanel()"
                                               class="touch-nav-item nav-item-bounce {{ request()->routeIs('frontend.seller-dashboard') ? 'bg-green-50/20 border-green-200/30' : '' }}">
                                                <div class="touch-nav-icon bg-gradient-to-br from-green-500 to-green-600 icon-pulse">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                                <span class="touch-nav-text text-white">{{ __('public_profile') }}</span>
                                                <i class="fas fa-chevron-right text-white/70 nav-arrow"></i>
                                            </a>

                                            <!-- Resubmission Request -->
                                             <a href="{{ route('frontend.resubmission.list') }}"
                                               @click="closePanel()"
                                               class="touch-nav-item nav-item-bounce {{ request()->routeIs('frontend.resubmission.list') ? 'bg-yellow-50/20 border-yellow-200/30' : '' }}">
                                                <div class="touch-nav-icon bg-gradient-to-br from-yellow-500 to-yellow-600 icon-pulse">
                                                    <i class="fas fa-undo text-white"></i>
                                                </div>
                                                <span class="touch-nav-text text-white">{{ __('resubmission_request') }}</span>
                                                <i class="fas fa-chevron-right text-white/70 nav-arrow"></i>
                                            </a>

                                            <!-- Plans & Billing -->
                                             <a href="{{ route('frontend.plans-billing') }}"
                                               @click="closePanel()"
                                               class="touch-nav-item nav-item-bounce {{ request()->routeIs('frontend.plans-billing') ? 'bg-emerald-50/20 border-emerald-200/30' : '' }}">
                                                <div class="touch-nav-icon bg-gradient-to-br from-emerald-500 to-emerald-600 icon-pulse">
                                                    <i class="fas fa-credit-card text-white"></i>
                                                </div>
                                                <span class="touch-nav-text text-white">{{ __('plans_billing') }}</span>
                                                <i class="fas fa-chevron-right text-white/70 nav-arrow"></i>
                                            </a>

                                            <!-- Blocked Users -->
                                             <a href="{{ route('get-blocked.users') }}"
                                               @click="closePanel()"
                                               class="touch-nav-item nav-item-bounce {{ request()->routeIs('get-blocked.users') ? 'bg-red-50/20 border-red-200/30' : '' }}">
                                                <div class="touch-nav-icon bg-gradient-to-br from-red-500 to-red-600 icon-pulse">
                                                    <i class="fas fa-user-slash text-white"></i>
                                                </div>
                                                <span class="touch-nav-text text-white">{{ __('Blocked List') }}</span>
                                                <i class="fas fa-chevron-right text-white/70 nav-arrow"></i>
                                            </a>

                                            <!-- Affiliate System -->
                                             <a href="{{ route('frontend.wallet') }}"
                                               @click="closePanel()"
                                               class="touch-nav-item nav-item-bounce {{ request()->routeIs('frontend.wallet') ? 'bg-blue-50/20 border-blue-200/30' : '' }}">
                                                <div class="touch-nav-icon bg-gradient-to-br from-blue-500 to-blue-600 icon-pulse">
                                                    <i class="fas fa-share-alt text-white"></i>
                                                </div>
                                                <span class="touch-nav-text text-white">
                                                    @if (authUser()?->affiliate?->affiliate_code != null)
                                                        {{ __('affiliate_system') }}
                                                    @else
                                                        {{ __('become_an_affiliator') }}
                                                    @endif
                                                </span>
                                                <i class="fas fa-chevron-right text-white/70 nav-arrow"></i>
                                            </a>

                                            <!-- Verify Account -->
                                             <a href="/dashboard/verify-account"
                                               @click="closePanel()"
                                               class="touch-nav-item nav-item-bounce {{ request()->routeIs('frontend.verify.account') ? 'bg-teal-50/20 border-teal-200/30' : '' }}">
                                                <div class="touch-nav-icon bg-gradient-to-br from-teal-500 to-teal-600 icon-pulse">
                                                    <i class="fas fa-user-check text-white"></i>
                                                </div>
                                                <span class="touch-nav-text text-white">{{ __('verify_account') }}</span>
                                                <i class="fas fa-chevron-right text-white/70 nav-arrow"></i>
                                            </a>

                                            <!-- Account Settings -->
                                             <a href="{{ route('frontend.account-setting') }}"
                                               @click="closePanel()"
                                               class="touch-nav-item nav-item-bounce {{ request()->routeIs('frontend.account-setting') ? 'bg-gray-50/20 border-gray-200/30' : '' }}">
                                                <div class="touch-nav-icon bg-gradient-to-br from-slate-500 to-slate-600 icon-pulse">
                                                    <i class="fas fa-user-cog text-white"></i>
                                                </div>
                                                <span class="touch-nav-text text-white">{{ __('account_setting') }}</span>
                                                <i class="fas fa-chevron-right text-white/70 nav-arrow"></i>
                                            </a>

                                                                                         <!-- Language & Currency Settings Section -->
                                             <div class="border-t border-white/20 dark:border-gray-600/30 my-4 pt-4">
                                                <div class="mb-3">
                                                    <h3 class="text-white/80 text-sm font-medium mb-3 px-2">{{ __('preferences') }}</h3>
                                                </div>

                                                @if ($setting->currency_changing && count($headerCurrencies))
                                                    @php
                                                        $currency_count = count($headerCurrencies) && count($headerCurrencies) > 1;
                                                        $current_currency_code = currentCurrencyCode();
                                                        $current_currency_symbol = currentCurrencySymbol();
                                                    @endphp
                                                    <!-- Currency Selector -->
                                                    <div x-data="{ currencyOpen: false }" class="mb-3">
                                                        <button @click="currencyOpen = !currencyOpen"
                                                                class="touch-nav-item nav-item-bounce w-full">
                                                            <div class="touch-nav-icon bg-gradient-to-br from-emerald-500 to-emerald-600 icon-pulse">
                                                                <i class="fas fa-dollar-sign text-white"></i>
                                                            </div>
                                                            <span class="touch-nav-text text-white">{{ __('currency') }}: {{ $current_currency_code }}</span>
                                                            <i class="fas fa-chevron-right text-white/70 nav-arrow transition-transform duration-200" 
                                                               :class="currencyOpen ? 'rotate-90' : ''"></i>
                                                        </button>
                                                        
                                                        @if ($currency_count)
                                                            <div x-show="currencyOpen" 
                                                                 x-transition:enter="transition ease-out duration-200"
                                                                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                                                                 x-transition:enter-end="opacity-100 transform translate-y-0"
                                                                 x-transition:leave="transition ease-in duration-150"
                                                                 x-transition:leave-start="opacity-100 transform translate-y-0"
                                                                 x-transition:leave-end="opacity-0 transform -translate-y-2"
                                                                 class="ml-12 mt-2 space-y-1">
                                                                @foreach ($headerCurrencies as $currency)
                                                                    <a href="{{ route('changeCurrency', $currency->code) }}"
                                                                       @click="closePanel()"
                                                                       class="flex items-center px-3 py-2 text-sm text-white/90 hover:text-white bg-white/5 dark:bg-gray-600/20 hover:bg-white/10 dark:hover:bg-gray-500/30 rounded-lg transition-all duration-200 {{ $current_currency_code === $currency->code ? 'bg-white/15 dark:bg-gray-500/40 ring-1 ring-white/20 dark:ring-gray-400/30' : '' }}">
                                                                        <i class="fas fa-coins w-4 h-4 mr-2 text-emerald-400"></i>
                                                                        <span>{{ $currency->code }} ({{ $currency->symbol }})</span>
                                                                        @if ($current_currency_code === $currency->code)
                                                                            <i class="fas fa-check ml-auto text-emerald-400"></i>
                                                                        @endif
                                                                    </a>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif

                                                @if ($language_enable && $setting->language_changing)
                                                    <!-- Language Selector -->
                                                    <div x-data="{ languageOpen: false }" class="mb-3">
                                                        <button @click="languageOpen = !languageOpen"
                                                                class="touch-nav-item nav-item-bounce w-full">
                                                            <div class="touch-nav-icon bg-gradient-to-br from-indigo-500 to-indigo-600 icon-pulse">
                                                                <i class="fas fa-language text-white"></i>
                                                            </div>
                                                            <span class="touch-nav-text text-white">{{ __('language') }}: {{ currentLanguage() ? currentLanguage()->name : 'Default' }}</span>
                                                            <i class="fas fa-chevron-right text-white/70 nav-arrow transition-transform duration-200" 
                                                               :class="languageOpen ? 'rotate-90' : ''"></i>
                                                        </button>
                                                        
                                                        <div x-show="languageOpen" 
                                                             x-transition:enter="transition ease-out duration-200"
                                                             x-transition:enter-start="opacity-0 transform -translate-y-2"
                                                             x-transition:enter-end="opacity-100 transform translate-y-0"
                                                             x-transition:leave="transition ease-in duration-150"
                                                             x-transition:leave-start="opacity-100 transform translate-y-0"
                                                             x-transition:leave-end="opacity-0 transform -translate-y-2"
                                                             class="ml-12 mt-2 space-y-1">
                                                            @foreach ($languages as $lang)
                                                                <a href="{{ route('changeLanguage', $lang->code) }}"
                                                                   @click="closePanel()"
                                                                   class="flex items-center px-3 py-2 text-sm text-white/90 hover:text-white bg-white/5 dark:bg-gray-600/20 hover:bg-white/10 dark:hover:bg-gray-500/30 rounded-lg transition-all duration-200 {{ currentLanguage()->name == $lang->name ? 'bg-white/15 dark:bg-gray-500/40 ring-1 ring-white/20 dark:ring-gray-400/30' : '' }}">
                                                                    <i class="fas fa-globe w-4 h-4 mr-2 text-indigo-400"></i>
                                                                    <span>{{ $lang->name }}</span>
                                                                    @if (currentLanguage()->name == $lang->name)
                                                                        <i class="fas fa-check ml-auto text-indigo-400"></i>
                                                                    @endif
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                                                                         <!-- Divider -->
                                             <div class="border-t border-white/20 dark:border-gray-600/30 my-4"></div>

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
                                     
                                     <!-- Header for Guests - Glass Style -->
                                     <div class="p-6 bg-white/12 dark:bg-gray-800/30 backdrop-blur-xl border-b border-white/25 dark:border-gray-600/40 glass-header">
                                         <!-- Close button - Glass Pulsing -->
                                         <button @click="closePanel()" 
                                                 class="absolute top-4 right-4 w-10 h-10 rounded-xl
                                                        glass-close-btn
                                                        flex items-center justify-center
                                                        transition-all duration-300">
                                             <i class="fas fa-times text-white text-sm pulse-icon"></i>
                                         </button>

                                         <!-- Welcome Message Glass Container -->
                                         <div class="text-center p-4 rounded-xl bg-white/10 dark:bg-gray-700/30 backdrop-blur-md border border-white/20 dark:border-gray-600/30">
                                                                                              <div class="w-16 h-16 mx-auto mb-3 bg-white/20 dark:bg-gray-600/40 rounded-xl flex items-center justify-center border border-white/30 dark:border-gray-500/50">
                                                 <i class="fas fa-user-circle text-white text-2xl"></i>
                                             </div>
                                             <h2 class="text-lg font-semibold text-white">
                                                 {{ __('welcome') }}!
                                             </h2>
                                             <p class="text-sm text-white/80">
                                                 Добро пожаловать на платформу
                                             </p>
                                         </div>
                                     </div>

                                     <!-- Auth Buttons Glass -->
                                     <div class="p-4 space-y-3 border-b border-white/20 dark:border-gray-600/30">
                                         <a href="{{ route('users.login') }}" 
                                            @click="closePanel()"
                                            class="flex items-center justify-center space-x-3 w-full px-4 py-3 bg-white/15 dark:bg-gray-700/40 backdrop-blur-md border border-white/30 dark:border-gray-600/40 text-white rounded-lg transition-all duration-300 hover:bg-white/20 dark:hover:bg-gray-600/50 hover:scale-105">
                                             <i class="fas fa-sign-in-alt"></i>
                                             <span class="font-medium">{{ __('sign_in') }}</span>
                                         </a>
                                         
                                         <a href="{{ route('frontend.signup') }}" 
                                            @click="closePanel()"
                                            class="flex items-center justify-center space-x-3 w-full px-4 py-3 bg-white/10 dark:bg-gray-700/30 backdrop-blur-md border border-white/25 dark:border-gray-600/35 text-white rounded-lg transition-all duration-300 hover:bg-white/15 dark:hover:bg-gray-600/40 hover:scale-105">
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
                                                     <i class="fas fa-house-user text-white"></i>
                                                 </div>
                                                 <span class="touch-nav-text text-white">{{ __('home') }}</span>
                                                 <i class="fas fa-chevron-right text-white/70 nav-arrow"></i>
                                             </a>

                                             <!-- Browse Ads -->
                                             <a href="{{ route('frontend.adlist') }}"
                                                @click="closePanel()"
                                                class="touch-nav-item nav-item-bounce">
                                                 <div class="touch-nav-icon bg-gradient-to-br from-green-500 to-green-600 icon-pulse">
                                                     <i class="fas fa-binoculars text-white"></i>
                                                 </div>
                                                 <span class="touch-nav-text text-white">{{ __('browse_ads') }}</span>
                                                 <i class="fas fa-chevron-right text-white/70 nav-arrow"></i>
                                             </a>

                                             <!-- Categories -->
                                             <a href="{{ route('frontend.ads') }}"
                                                @click="closePanel()"
                                                class="touch-nav-item nav-item-bounce">
                                                 <div class="touch-nav-icon bg-gradient-to-br from-purple-500 to-purple-600 icon-pulse">
                                                     <i class="fas fa-layer-group text-white"></i>
                                                 </div>
                                                 <span class="touch-nav-text text-white">{{ __('categories') }}</span>
                                                 <i class="fas fa-chevron-right text-white/70 nav-arrow"></i>
                                             </a>

                                             <!-- Pricing -->
                                             <a href="{{ route('frontend.priceplan') }}"
                                                @click="closePanel()"
                                                class="touch-nav-item nav-item-bounce">
                                                 <div class="touch-nav-icon bg-gradient-to-br from-orange-500 to-orange-600 icon-pulse">
                                                     <i class="fas fa-gem text-white"></i>
                                                 </div>
                                                 <span class="touch-nav-text text-white">{{ __('pricing_plan') }}</span>
                                                 <i class="fas fa-chevron-right text-white/70 nav-arrow"></i>
                                             </a>

                                             <!-- Contact -->
                                             <a href="{{ route('frontend.contact') }}"
                                                @click="closePanel()"
                                                class="touch-nav-item nav-item-bounce">
                                                 <div class="touch-nav-icon bg-gradient-to-br from-teal-500 to-teal-600 icon-pulse">
                                                     <i class="fas fa-phone-alt text-white"></i>
                                                 </div>
                                                 <span class="touch-nav-text text-white">{{ __('contact') }}</span>
                                                 <i class="fas fa-chevron-right text-white/70 nav-arrow"></i>
                                             </a>

                                                                                           <!-- Language & Currency Settings Section for Guests -->
                                              <div class="border-t border-white/20 dark:border-gray-600/30 my-4 pt-4">
                                                 <div class="mb-3">
                                                     <h3 class="text-white/80 text-sm font-medium mb-3 px-2">{{ __('preferences') }}</h3>
                                                 </div>

                                                 @if ($setting->currency_changing && count($headerCurrencies))
                                                     @php
                                                         $currency_count = count($headerCurrencies) && count($headerCurrencies) > 1;
                                                         $current_currency_code = currentCurrencyCode();
                                                         $current_currency_symbol = currentCurrencySymbol();
                                                     @endphp
                                                     <!-- Currency Selector -->
                                                     <div x-data="{ currencyOpen: false }" class="mb-3">
                                                         <button @click="currencyOpen = !currencyOpen"
                                                                 class="touch-nav-item nav-item-bounce w-full">
                                                             <div class="touch-nav-icon bg-gradient-to-br from-emerald-500 to-emerald-600 icon-pulse">
                                                                 <i class="fas fa-dollar-sign text-white"></i>
                                                             </div>
                                                             <span class="touch-nav-text text-white">{{ __('currency') }}: {{ $current_currency_code }}</span>
                                                             <i class="fas fa-chevron-right text-white/70 nav-arrow transition-transform duration-200" 
                                                                :class="currencyOpen ? 'rotate-90' : ''"></i>
                                                         </button>
                                                         
                                                         @if ($currency_count)
                                                             <div x-show="currencyOpen" 
                                                                  x-transition:enter="transition ease-out duration-200"
                                                                  x-transition:enter-start="opacity-0 transform -translate-y-2"
                                                                  x-transition:enter-end="opacity-100 transform translate-y-0"
                                                                  x-transition:leave="transition ease-in duration-150"
                                                                  x-transition:leave-start="opacity-100 transform translate-y-0"
                                                                  x-transition:leave-end="opacity-0 transform -translate-y-2"
                                                                  class="ml-12 mt-2 space-y-1">
                                                                 @foreach ($headerCurrencies as $currency)
                                                                     <a href="{{ route('changeCurrency', $currency->code) }}"
                                                                        @click="closePanel()"
                                                                        class="flex items-center px-3 py-2 text-sm text-white/90 hover:text-white bg-white/5 dark:bg-gray-600/20 hover:bg-white/10 dark:hover:bg-gray-500/30 rounded-lg transition-all duration-200 {{ $current_currency_code === $currency->code ? 'bg-white/15 dark:bg-gray-500/40 ring-1 ring-white/20 dark:ring-gray-400/30' : '' }}">
                                                                         <i class="fas fa-coins w-4 h-4 mr-2 text-emerald-400"></i>
                                                                         <span>{{ $currency->code }} ({{ $currency->symbol }})</span>
                                                                         @if ($current_currency_code === $currency->code)
                                                                             <i class="fas fa-check ml-auto text-emerald-400"></i>
                                                                         @endif
                                                                     </a>
                                                                 @endforeach
                                                             </div>
                                                         @endif
                                                     </div>
                                                 @endif

                                                 @if ($language_enable && $setting->language_changing)
                                                     <!-- Language Selector -->
                                                     <div x-data="{ languageOpen: false }" class="mb-3">
                                                         <button @click="languageOpen = !languageOpen"
                                                                 class="touch-nav-item nav-item-bounce w-full">
                                                             <div class="touch-nav-icon bg-gradient-to-br from-indigo-500 to-indigo-600 icon-pulse">
                                                                 <i class="fas fa-language text-white"></i>
                                                             </div>
                                                             <span class="touch-nav-text text-white">{{ __('language') }}: {{ currentLanguage() ? currentLanguage()->name : 'Default' }}</span>
                                                             <i class="fas fa-chevron-right text-white/70 nav-arrow transition-transform duration-200" 
                                                                :class="languageOpen ? 'rotate-90' : ''"></i>
                                                         </button>
                                                         
                                                         <div x-show="languageOpen" 
                                                              x-transition:enter="transition ease-out duration-200"
                                                              x-transition:enter-start="opacity-0 transform -translate-y-2"
                                                              x-transition:enter-end="opacity-100 transform translate-y-0"
                                                              x-transition:leave="transition ease-in duration-150"
                                                              x-transition:leave-start="opacity-100 transform translate-y-0"
                                                              x-transition:leave-end="opacity-0 transform -translate-y-2"
                                                              class="ml-12 mt-2 space-y-1">
                                                             @foreach ($languages as $lang)
                                                                 <a href="{{ route('changeLanguage', $lang->code) }}"
                                                                    @click="closePanel()"
                                                                    class="flex items-center px-3 py-2 text-sm text-white/90 hover:text-white bg-white/5 dark:bg-gray-600/20 hover:bg-white/10 dark:hover:bg-gray-500/30 rounded-lg transition-all duration-200 {{ currentLanguage()->name == $lang->name ? 'bg-white/15 dark:bg-gray-500/40 ring-1 ring-white/20 dark:ring-gray-400/30' : '' }}">
                                                                     <i class="fas fa-globe w-4 h-4 mr-2 text-indigo-400"></i>
                                                                     <span>{{ $lang->name }}</span>
                                                                     @if (currentLanguage()->name == $lang->name)
                                                                         <i class="fas fa-check ml-auto text-indigo-400"></i>
                                                                     @endif
                                                                 </a>
                                                             @endforeach
                                                         </div>
                                                     </div>
                                                 @endif
                                             </div>
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

/* Touch Navigation Items - Universal Glass Style */
.touch-nav-item {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    margin: 0 0 8px 0;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.12) !important;
    border: 1px solid rgba(255, 255, 255, 0.25) !important;
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    -webkit-tap-highlight-color: transparent;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    color: white !important;
}

/* Universal glass hover for all themes */
.touch-nav-item:hover {
    background: rgba(255, 255, 255, 0.18) !important;
    border-color: rgba(255, 255, 255, 0.35) !important;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
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

/* Enhanced Touch Nav Text Color - Universal White Text */
.touch-nav-text {
    color: white !important;
}

/* Enhanced Mobile Scrolling for Touch Panel */
.touch-panel-container {
    /* Smooth scrolling for iOS */
    -webkit-overflow-scrolling: touch;
    /* Momentum scrolling */
    scroll-behavior: smooth;
}

.touch-panel-container .overflow-y-auto {
    /* Enhanced scrollbar for webkit browsers */
    scrollbar-width: thin;
    scrollbar-color: rgba(255, 255, 255, 0.3) transparent;
}

.touch-panel-container .overflow-y-auto::-webkit-scrollbar {
    width: 4px;
}

.touch-panel-container .overflow-y-auto::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 2px;
}

.touch-panel-container .overflow-y-auto::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 2px;
    transition: background 0.3s ease;
}

.touch-panel-container .overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
}

/* Touch panel width optimization for mobile */
.touch-panel-container {
    width: min(320px, 85vw);
    max-width: 320px;
}

.touch-nav-item .nav-arrow {
    color: rgba(255, 255, 255, 0.7) !important;
}

/* Glass Header Text Color - Universal White */
.glass-header h2,
.glass-header p,
.glass-header span,
.glass-header .text-lg,
.glass-header .text-sm {
    color: white !important;
}

/* Glass Header Styles */
.glass-header {
    position: relative;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.glass-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));
    border-radius: inherit;
    pointer-events: none;
}

/* Glass Close Button */
.glass-close-btn {
    background: rgba(255, 255, 255, 0.15) !important;
    border: 1px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.glass-close-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.4s;
}

.glass-close-btn:hover::before {
    left: 100%;
}

.glass-close-btn:hover {
    background: rgba(255, 255, 255, 0.25) !important;
    border-color: rgba(255, 255, 255, 0.4);
    transform: scale(1.1) rotate(90deg);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.glass-close-btn:active {
    transform: scale(0.95) rotate(90deg);
}

/* Pulse Icon Animation */
.pulse-icon {
    animation: pulseIcon 2s ease-in-out infinite;
}

@keyframes pulseIcon {
    0%, 100% {
        transform: scale(1);
        opacity: 1;
    }
    50% {
        transform: scale(1.1);
        opacity: 0.8;
    }
}

/* Universal Glass Text Override for Touch Panels */
.glass-header *,
.touch-nav-item * {
    color: white !important;
}

/* Specific overrides for readability */
.glass-header .text-gray-900,
.glass-header .text-gray-600,
.glass-header .dark\\:text-white,
.glass-header .dark\\:text-gray-400 {
    color: white !important;
}

/* Header Glass Dark Theme */
.header-glass-dark {
    position: relative;
}

.dark .header-glass-dark {
    background: rgba(17, 24, 39, 0.9) !important;
    backdrop-filter: blur(20px) !important;
    -webkit-backdrop-filter: blur(20px) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1) !important;
}

/* Touch Panel Dark Theme Enhancements */
.dark .touch-panel-container {
    background: linear-gradient(135deg, rgba(31, 41, 55, 0.95), rgba(17, 24, 39, 0.95)) !important;
    border-right: 1px solid rgba(156, 163, 175, 0.3) !important;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important;
}

.dark .glass-header {
    background: rgba(31, 41, 55, 0.4) !important;
    border-bottom: 1px solid rgba(156, 163, 175, 0.3) !important;
}

.dark .glass-close-btn {
    background: rgba(156, 163, 175, 0.2) !important;
    border: 1px solid rgba(156, 163, 175, 0.4) !important;
}

.dark .glass-close-btn:hover {
    background: rgba(156, 163, 175, 0.3) !important;
    border-color: rgba(156, 163, 175, 0.5) !important;
}

/* Touch Nav Items Dark Theme */
.dark .touch-nav-item {
    background: rgba(55, 65, 81, 0.3) !important;
    border: 1px solid rgba(75, 85, 99, 0.4) !important;
}

.dark .touch-nav-item:hover {
    background: rgba(55, 65, 81, 0.5) !important;
    border-color: rgba(107, 114, 128, 0.6) !important;
    transform: translateX(4px) !important;
}

.dark .touch-nav-item:active {
    background: rgba(75, 85, 99, 0.6) !important;
}

/* Dark Theme Text Colors for Touch Panel */
.dark .touch-nav-text,
.dark .glass-header h2,
.dark .glass-header p,
.dark .glass-header span {
    color: #f9fafb !important;
}

/* Dark Theme Icon Colors */
.dark .touch-nav-icon {
    background: linear-gradient(135deg, rgba(75, 85, 99, 0.8), rgba(55, 65, 81, 0.9)) !important;
}

.dark .touch-nav-item:hover .touch-nav-icon {
    background: linear-gradient(135deg, rgba(107, 114, 128, 0.9), rgba(75, 85, 99, 1)) !important;
}

/* Dark Theme Text Visibility Improvements */
.dark body {
    background-color: #111827 !important;
    color: #f9fafb !important;
}

.dark p, .dark span, .dark div, .dark h1, .dark h2, .dark h3, .dark h4, .dark h5, .dark h6 {
    color: #e5e7eb !important;
}

.dark .text-gray-900 {
    color: #f9fafb !important;
}

.dark .text-gray-800 {
    color: #f3f4f6 !important;
}

.dark .text-gray-700 {
    color: #e5e7eb !important;
}

.dark .text-gray-600 {
    color: #d1d5db !important;
}

.dark .text-gray-500 {
    color: #9ca3af !important;
}

.dark .border-gray-200 {
    border-color: #374151 !important;
}

.dark .border-gray-100 {
    border-color: #4b5563 !important;
}

.dark .bg-white {
    background-color: #1f2937 !important;
}

.dark .bg-gray-50 {
    background-color: #374151 !important;
}

.dark .bg-gray-100 {
    background-color: #4b5563 !important;
}

/* Z-Index Management for Touch Panel - REASONABLE VALUES */
.touch-panel-overlay {
    z-index: 99999 !important;
}

.touch-panel-wrapper {
    z-index: 99998 !important;
    position: relative !important;
}

.touch-panel-container {
    z-index: 99999 !important;
    position: relative !important;
}

/* Touch button - high but reasonable z-index */
.touch-menu-btn {
    position: relative !important;
    z-index: 100000 !important;
}

/* Touch button positioning fixes - CAREFUL APPROACH */
.touch-menu-btn {
    position: relative !important;
    vertical-align: middle !important;
}

.dark .touch-menu-btn {
    position: relative !important;
    top: 0 !important;
    vertical-align: middle !important;
}

/* Touch panel wrapper positioning */
.touch-panel-wrapper {
    display: inline-flex !important;
    align-items: center !important;
    vertical-align: middle !important;
}

.dark .touch-panel-wrapper {
    display: inline-flex !important;
    align-items: center !important;
    vertical-align: middle !important;
}



</style>
