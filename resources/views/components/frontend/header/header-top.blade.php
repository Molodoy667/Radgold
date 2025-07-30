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
                @auth('user')
                    <!-- Touch Sidebar Button for Authenticated Users -->
                    <div x-data="{ 
                        sidebarOpen: false,
                        openSidebar() { 
                            this.sidebarOpen = true; 
                            document.body.style.overflow = 'hidden';
                        },
                        closeSidebar() { 
                            this.sidebarOpen = false; 
                            document.body.style.overflow = '';
                        }
                    }">
                        <!-- Modern Touch Menu Button -->
                        <button @click="openSidebar()" 
                                class="touch-sidebar-btn relative w-10 h-10 rounded-xl
                                       bg-gradient-to-br from-blue-500/90 to-purple-600/90
                                       backdrop-blur-xl border border-white/20
                                       shadow-lg hover:shadow-blue-500/25
                                       flex items-center justify-center
                                       transition-all duration-300 ease-out
                                       hover:scale-105 active:scale-95">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>

                        <!-- Glass Overlay -->
                        <div x-show="sidebarOpen" 
                             x-transition:enter="transition-opacity ease-out duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition-opacity ease-in duration-200"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             @click="closeSidebar()"
                             class="fixed inset-0 bg-black/30 backdrop-blur-sm z-[9997]"
                             style="display: none;">
                        </div>

                        <!-- Touch Sidebar Panel -->
                        <div x-show="sidebarOpen"
                             x-transition:enter="transform transition-transform ease-out duration-300"
                             x-transition:enter-start="-translate-x-full"
                             x-transition:enter-end="translate-x-0"
                             x-transition:leave="transform transition-transform ease-in duration-200"
                             x-transition:leave-start="translate-x-0"
                             x-transition:leave-end="-translate-x-full"
                             class="glass-sidebar fixed top-0 left-0 h-full w-80 z-[9998] 
                                    shadow-2xl shadow-black/10"
                             style="display: none;">
                             
                            <!-- Header with User Info -->
                            <div class="relative overflow-hidden">
                                <!-- Decorative gradient background -->
                                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/20 to-purple-600/20"></div>
                                
                                <div class="relative p-6 border-b border-white/10 backdrop-blur-sm">
                                    <!-- Close button -->
                                    <button @click="closeSidebar()" 
                                            class="absolute top-4 right-4 w-8 h-8 rounded-full 
                                                   bg-white/20 hover:bg-white/30 
                                                   flex items-center justify-center
                                                   transition-all duration-200">
                                        <svg class="w-5 h-5 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>

                                    <!-- User Avatar and Info -->
                                    <div class="flex items-center space-x-4">
                                        <div class="relative">
                                            <img class="w-16 h-16 rounded-2xl object-cover ring-2 ring-white/30 shadow-lg" 
                                                 src="{{ authUser()->image_url }}" 
                                                 alt="{{ authUser()->name }}">
                                            @if (auth('user')->user()->document_verified && auth('user')->user()->document_verified->status == 'approved')
                                                <div class="absolute -top-1 -right-1">
                                                    <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
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
                            </div>

                            <!-- Navigation Menu -->
                            <div class="flex-1 overflow-y-auto py-4 px-2">
                                <nav class="space-y-1">
                                    <!-- Overview -->
                                    <a href="{{ route('frontend.dashboard') }}"
                                       @click="closeSidebar()"
                                       class="touch-menu-item {{ request()->routeIs('frontend.dashboard') ? 'touch-menu-active' : '' }}">
                                        <div class="touch-menu-icon bg-gradient-to-br from-blue-500 to-blue-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2v0"></path>
                                            </svg>
                                        </div>
                                        <span class="touch-menu-text">{{ __('overview') }}</span>
                                        <svg class="touch-menu-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>

                                    <!-- My Ads -->
                                    <a href="{{ route('frontend.my.listing') }}"
                                       @click="closeSidebar()"
                                       class="touch-menu-item {{ request()->routeIs('frontend.my.listing') ? 'touch-menu-active' : '' }}">
                                        <div class="touch-menu-icon bg-gradient-to-br from-orange-500 to-orange-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                        </div>
                                        <span class="touch-menu-text">{{ __('my_ads') }}</span>
                                        <svg class="touch-menu-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>

                                    <!-- Post Listing -->
                                    <a href="{{ route('frontend.post') }}"
                                       @click="closeSidebar()"
                                       class="touch-menu-item {{ request()->routeIs('frontend.post') ? 'touch-menu-active' : '' }}">
                                        <div class="touch-menu-icon bg-gradient-to-br from-purple-500 to-purple-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        </div>
                                        <span class="touch-menu-text">{{ __('post_listing') }}</span>
                                        <svg class="touch-menu-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>

                                    <!-- Messages -->
                                    <a href="{{ route('frontend.message') }}"
                                       @click="closeSidebar()"
                                       class="touch-menu-item {{ request()->routeIs('message') ? 'touch-menu-active' : '' }}">
                                        <div class="touch-menu-icon bg-gradient-to-br from-indigo-500 to-indigo-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                            </svg>
                                        </div>
                                        <span class="touch-menu-text">{{ __('message') }}</span>
                                        <svg class="touch-menu-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>

                                    <!-- Favorites -->
                                    <a href="{{ route('frontend.favorite.list') }}"
                                       @click="closeSidebar()"
                                       class="touch-menu-item {{ request()->routeIs('favorite-listing') ? 'touch-menu-active' : '' }}">
                                        <div class="touch-menu-icon bg-gradient-to-br from-red-500 to-pink-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                            </svg>
                                        </div>
                                        <span class="touch-menu-text">{{ __('favorite_ads') }}</span>
                                        <svg class="touch-menu-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>

                                    <!-- Account Settings -->
                                    <a href="{{ route('frontend.account-setting') }}"
                                       @click="closeSidebar()"
                                       class="touch-menu-item {{ request()->routeIs('frontend.account-setting') ? 'touch-menu-active' : '' }}">
                                        <div class="touch-menu-icon bg-gradient-to-br from-slate-500 to-slate-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                        </div>
                                        <span class="touch-menu-text">{{ __('account_setting') }}</span>
                                        <svg class="touch-menu-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>

                                    <!-- Logout -->
                                    <a href="javascript:void(0)"
                                       @click="closeSidebar(); document.getElementById('touch-sidebar-logout-form').submit();"
                                       class="touch-menu-item border-t border-gray-200/50 dark:border-gray-700/50 mt-4 pt-4">
                                        <div class="touch-menu-icon bg-gradient-to-br from-red-500 to-red-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                            </svg>
                                        </div>
                                        <span class="touch-menu-text text-red-600 dark:text-red-400">{{ __('logout') }}</span>
                                        <svg class="touch-menu-arrow text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </nav>
                            </div>

                            <!-- Bottom gradient fade -->
                            <div class="absolute bottom-0 left-0 right-0 h-8 bg-gradient-to-t from-white/95 to-transparent dark:from-gray-900/95 pointer-events-none"></div>
                        </div>

                        <!-- Logout Form -->
                        <form id="touch-sidebar-logout-form" action="{{ route('frontend.logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    </div>
                @else
                    <!-- Standard Menu Button for Guests -->
                    <button @click="mobileMenu = true">
                        <x-svg.menu-icon />
                    </button>
                @endauth
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
    </script>
@endpush

<style>
/* Touch Sidebar Button Animation */
.touch-sidebar-btn {
    animation: gentlePulse 3s ease-in-out infinite;
}

@keyframes gentlePulse {
    0%, 100% {
        box-shadow: 0 4px 14px 0 rgba(59, 130, 246, 0.3);
    }
    50% {
        box-shadow: 0 6px 20px 0 rgba(59, 130, 246, 0.4);
    }
}

/* Touch Menu Styles */
.touch-menu-item {
    @apply flex items-center p-4 mx-2 rounded-xl 
           bg-white/40 dark:bg-gray-800/40 
           backdrop-blur-sm border border-white/20 dark:border-gray-700/30
           transition-all duration-300 ease-out
           hover:bg-white/60 dark:hover:bg-gray-800/60
           hover:shadow-lg hover:shadow-black/5
           active:scale-[0.98] active:bg-white/80 dark:active:bg-gray-800/80;
    -webkit-tap-highlight-color: transparent;
    user-select: none;
}

.touch-menu-active {
    @apply bg-gradient-to-r from-blue-500/20 to-purple-600/20
           border-blue-500/30 dark:border-blue-400/30
           shadow-lg shadow-blue-500/10;
}

.touch-menu-icon {
    @apply w-10 h-10 rounded-lg 
           flex items-center justify-center
           text-white shadow-lg;
}

.touch-menu-text {
    @apply flex-1 ml-3 text-gray-900 dark:text-gray-100 
           font-medium text-[15px] leading-tight;
}

.touch-menu-arrow {
    @apply w-5 h-5 text-gray-400 dark:text-gray-500
           transition-transform duration-200;
}

.touch-menu-item:hover .touch-menu-arrow {
    @apply transform translate-x-1 text-gray-600 dark:text-gray-300;
}

.glass-sidebar {
    background: linear-gradient(135deg, 
        rgba(255, 255, 255, 0.95) 0%, 
        rgba(255, 255, 255, 0.85) 100%);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-right: 1px solid rgba(255, 255, 255, 0.2);
}

@media (prefers-color-scheme: dark) {
    .glass-sidebar {
        background: linear-gradient(135deg, 
            rgba(17, 24, 39, 0.95) 0%, 
            rgba(31, 41, 55, 0.85) 100%);
        border-right: 1px solid rgba(75, 85, 99, 0.3);
    }
}

/* Custom scrollbar */
.glass-sidebar nav {
    scrollbar-width: thin;
    scrollbar-color: rgba(156, 163, 175, 0.3) transparent;
}

.glass-sidebar nav::-webkit-scrollbar {
    width: 4px;
}

.glass-sidebar nav::-webkit-scrollbar-track {
    background: transparent;
}

.glass-sidebar nav::-webkit-scrollbar-thumb {
    background-color: rgba(156, 163, 175, 0.3);
    border-radius: 2px;
}
</style>
