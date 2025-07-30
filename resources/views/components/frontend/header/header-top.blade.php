<!-- Header Top Section -->
@php
    $isAuthenticated = auth('user')->check();
@endphp

<div class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <div class="container mx-auto">
        <div class="flex items-center justify-between h-16 px-4">
            
            <!-- Left section: Language, Location, Contact -->
            <div class="hidden md:flex items-center space-x-6">
                <!-- Language Selector -->
                @if ($languages->count() > 1)
                    <div class="relative group">
                        <button class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400">
                            <span class="flag-icon {{ current_language_code_flag() }}"></span>
                            <span>{{ current_language_name() }}</span>
                            <svg class="w-4 h-4 transform group-hover:rotate-180 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="absolute top-full left-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            @foreach ($languages as $language)
                                <a href="{{ route('changeLanguage', $language->code) }}" 
                                   class="flex items-center space-x-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 first:rounded-t-lg last:rounded-b-lg">
                                    <span class="flag-icon {{ $language->icon }}"></span>
                                    <span>{{ $language->name }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Location -->
                @if (session('location'))
                    <div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>{{ session('location') }}</span>
                    </div>
                @endif

                <!-- Contact -->
                @if ($setting->contact_phone)
                    <a href="tel:{{ $setting->contact_phone }}" 
                       class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <span>{{ $setting->contact_phone }}</span>
                    </a>
                @endif
            </div>

            <!-- Center section: Logo (mobile) -->
            <div class="md:hidden">
                <a href="{{ route('frontend.index') }}" class="flex items-center">
                    <img class="h-8 w-auto" src="{{ $setting->logo_image_url }}" alt="{{ $setting->app_name }}">
                </a>
            </div>

            <!-- Right section: Auth buttons or User menu -->
            <div class="flex items-center space-x-4">
                @if ($isAuthenticated)
                    <!-- Authenticated User Menu -->
                    <div class="hidden md:flex items-center space-x-4">
                        <!-- Notifications -->
                        <button class="relative p-2 text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-400 ring-2 ring-white dark:ring-gray-800"></span>
                        </button>

                        <!-- Messages -->
                        <a href="{{ route('frontend.message') }}" 
                           class="relative p-2 text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </a>

                        <!-- User Profile Dropdown -->
                        <div class="relative group">
                            <button class="flex items-center space-x-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400">
                                <img class="w-8 h-8 rounded-full object-cover" src="{{ authUser()->image_url }}" alt="{{ authUser()->name }}">
                                <span class="hidden lg:block">{{ authUser()->name }}</span>
                                <svg class="w-4 h-4 transform group-hover:rotate-180 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div class="absolute top-full right-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                <div class="p-4 border-b border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center space-x-3">
                                        <img class="w-12 h-12 rounded-full object-cover" src="{{ authUser()->image_url }}" alt="{{ authUser()->name }}">
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white">{{ authUser()->name }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ authUser()->email }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="py-2">
                                    <a href="{{ route('frontend.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        {{ __('overview') }}
                                    </a>
                                    <a href="{{ route('frontend.account-setting') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        {{ __('account_setting') }}
                                    </a>
                                    <div class="border-t border-gray-200 dark:border-gray-600 my-2"></div>
                                    <a href="javascript:void(0)" 
                                       onclick="event.preventDefault();document.getElementById('header-logout-form').submit();"
                                       class="block px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        {{ __('logout') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile: Touch Sidebar Button for Authenticated Users -->
                    <div class="md:hidden">
                        <!-- Dashboard Touch Menu Integration -->
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
                                
                                <!-- Notification badge if user has unread items -->
                                <span class="absolute -top-1 -right-1 block h-3 w-3 rounded-full bg-red-400 ring-2 ring-white"></span>
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
                    </div>
                @else
                    <!-- Guest Users -->
                    <div class="flex items-center space-x-2">
                        <!-- Sign In -->
                        <a href="{{ route('frontend.login') }}" 
                           class="hidden md:block px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400">
                            {{ __('sign_in') }}
                        </a>
                        
                        <!-- Sign Up -->
                        <a href="{{ route('frontend.register') }}" 
                           class="hidden md:block px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors duration-200">
                            {{ __('sign_up') }}
                        </a>

                        <!-- Mobile: Standard Menu for Guests -->
                        <div class="md:hidden flex items-center space-x-2">
                            <a href="{{ route('frontend.login') }}" 
                               class="p-2 text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </a>
                            
                            <button @click="mobileMenu = true"
                                    class="p-2 text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Search Button (Mobile) -->
                <button @click="searchbar = !searchbar"
                        class="md:hidden p-2 text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Logout Form for Desktop -->
<form id="header-logout-form" action="{{ route('frontend.logout') }}" method="POST" class="hidden">
    @csrf
</form>

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

/* Touch Menu Styles Integration */
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
