<!-- Legacy Desktop Sidebar (только для desktop страниц dashboard) -->
<div class="bg-white dark:bg-gray-900 hidden lg:block sticky top-6 w-full sm:max-w-[360px] sm:min-w-[360px] min-w-[330px] max-w-[300px] border border-gray-100 dark:border-gray-600 shadow-[0px_4px_8px_0px_rgba(28,33,38,0.08)] rounded-xl">

    @php
        $user = auth('user')->user();
    @endphp

    <!-- Desktop Sidebar (скрытый на мобильных) -->
    <div class="bg-white dark:bg-gray-900 hidden lg:block sticky top-6 w-full sm:max-w-[360px] sm:min-w-[360px] min-w-[330px] max-w-[300px] border border-gray-100 dark:border-gray-600 shadow-[0px_4px_8px_0px_rgba(28,33,38,0.08)] rounded-xl">
        <!-- Desktop content remains the same for large screens -->
        <div class="p-6 flex gap-4 items-center border-b border-gray-100 dark:border-gray-600">
            <img class="w-14 h-14 rounded-full object-cover" src="{{ $user->image_url }}" alt="">
            <div>
                <div class="flex gap-1">
                    <h2 class="heading-07 mb-0.5 text-gray-900 dark:text-white">{{ $user->name }}</h2>
                    @if (auth('user')->user()->document_verified && auth('user')->user()->document_verified->status == 'approved')
                        <span><x-svg.account-verification.verified-badge /></span>
                    @endif
                </div>
                <p class="body-md-400 text-gray-600 dark:text-gray-300">{{ $user->email }}</p>
            </div>
        </div>
        <ul class="py-6">
            <li>
                <a href="{{ !request()->routeIs('frontend.dashboard') ? route('frontend.dashboard') : 'javascript:void(0)' }}"
                    class="sidebar-menu-link {{ request()->routeIs('frontend.dashboard') ? 'active' : '' }}">
                    <x-svg.overview-icon />
                    <span>{{ __('overview') }}</span>
                </a>
            </li>
            
            <li>
                <a href="{{ route('frontend.seller.profile', authUser()->username) }}"
                    class="sidebar-menu-link {{ request()->routeIs('frontend.seller-dashboard') ? 'active' : '' }}">
                    <x-svg.user-icon width="24" height="24" stroke="currentColor" />
                    <span>{{ __('public_profile') }}</span>
                </a>
            </li>

            <li>
                <a href="{{ !request()->routeIs('frontend.post') ? route('frontend.post') : 'javascript:void(0)' }}"
                    class="sidebar-menu-link {{ request()->routeIs('frontend.post') ? 'active' : '' }}">
                    <x-svg.image-select-icon />
                    <span>{{ __('post_listing') }}</span>
                </a>
            </li>

            <li>
                <a href="{{ !request()->routeIs('frontend.my.listing') ? route('frontend.my.listing') : 'javascript:void(0)' }}"
                    class="sidebar-menu-link {{ request()->routeIs('frontend.my.listing') ? 'active' : '' }}">
                    <x-svg.list-icon width="24" height="24" stroke="currentColor" />
                    <span>{{ __('my_ads') }}</span>
                </a>
            </li>
            <li>
                <a href="{{ !request()->routeIs('frontend.resubmission.list') ? route('frontend.resubmission.list') : 'javascript:void(0)' }}"
                    class="sidebar-menu-link {{ request()->routeIs('frontend.resubmission.list') ? 'active' : '' }}">
                    <x-svg.list-icon width="24" height="24" stroke="currentColor" />
                    <span>{{ __('resubmission_request') }}</span>
                </a>
            </li>
            <li>
                <a href="{{ !request()->routeIs('frontend.favorite.list') ? route('frontend.favorite.list') : 'javascript:void(0)' }}"
                    class="sidebar-menu-link {{ request()->routeIs('favorite-listing') ? 'active' : '' }}">
                    <x-svg.heart-icon fill="none" stroke="currentColor" />
                    <span>{{ __('favorite_ads') }}</span>
                </a>
            </li>
            <li>
                <a href="{{ !request()->routeIs('frontend.message') ? route('frontend.message') : 'javascript:void(0)' }}"
                    class="sidebar-menu-link {{ request()->routeIs('message') ? 'active' : '' }}">
                    <x-svg.message-icon width="24" height="24" stroke="currentColor" />
                    <span>{{ __('message') }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('frontend.plans-billing') }}"
                    class="sidebar-menu-link {{ request()->routeIs('frontend.plans-billing') ? 'active' : '' }}">
                    <x-svg.invoice-icon width="24" height="24" stroke="currentColor" />
                    <span>{{ __('plans_billing') }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('get-blocked.users') }}"
                    class="sidebar-menu-link {{ request()->routeIs('get-blocked.users') ? 'active' : '' }}">
                    <x-frontend.icons.block stroke="currentColor" />
                    <span>{{ __('Blocked List') }}</span>
                </a>
            </li>
            <li>
                <a href="{{route('frontend.wallet')}}" class="sidebar-menu-link {{ request()->routeIs('frontend.wallet') ? 'active' : '' }}">
                    <x-svg.heroicons.share />
                    @if (authUser()?->affiliate?->affiliate_code != null)
                        <span>{{__('affiliate_system')}}</span>
                    @else
                        <span>{{__('become_an_affiliator')}}</span>
                    @endif
                </a>
            </li>
            <li>
                <a href="/dashboard/verify-account"
                    class="sidebar-menu-link {{ request()->routeIs('frontend.verify.account') ? 'active' : '' }}">
                    <x-svg.user-check-icon width="24" height="24" stroke="currentColor" />
                    <span>{{ __('verify_account') }}</span>
                </a>
            </li>
            <li>
                <a href="{{ !request()->routeIs('frontend.account-setting') ? route('frontend.account-setting') : 'javascript:void(0)' }}"
                    class="sidebar-menu-link {{ request()->routeIs('frontend.account-setting') ? 'active' : '' }}">
                    <x-svg.setting-icon />
                    <span>{{ __('account_setting') }}</span>
                </a>
            </li>
            <li>
                <a href="javascript:void(0"
                    onclick="event.preventDefault();document.getElementById('sidebar-logout-form').submit();"
                    class="sidebar-menu-link">
                    <x-svg.logout-icon />
                    <span>{{ __('logout') }}</span>
                </a>
            </li>
            <form id="sidebar-logout-form" action="{{ route('frontend.logout') }}" method="POST" class="hidden invisible">
                @csrf
            </form>
        </ul>
    </div>

        <!-- Мобильная версия теперь интегрирована в header -->
</div>

<!-- Touch Sidebar для мобильных устройств -->
<div x-data="touchSidebar" class="lg:hidden">
    <!-- Floating Menu Button -->
    <button 
        @click="openSidebar()"
        class="floating-menu-btn fixed bottom-6 right-6 z-50 w-14 h-14 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-full shadow-lg flex items-center justify-center transition-all duration-300 hover:scale-110 focus:outline-none focus:ring-4 focus:ring-blue-300"
        aria-label="Open dashboard menu">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>

    <!-- Touch Sidebar -->
    <div 
        x-show="sidebarOpen"
        x-transition:enter="touch-sidebar-enter"
        x-transition:leave="touch-sidebar-leave"
        class="glass-sidebar fixed top-0 left-0 h-full w-80 max-w-[85vw] z-[9999] overflow-y-auto"
        @click.away="closeSidebar()"
        role="dialog"
        aria-modal="true"
        aria-label="Dashboard navigation">
        
        <!-- Header -->
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <img class="w-12 h-12 rounded-full object-cover" src="{{ $user->image_url }}" alt="{{ $user->name }}">
                    <div>
                        <div class="flex items-center gap-1">
                            <h2 class="font-semibold text-gray-900 dark:text-white text-sm">{{ $user->name }}</h2>
                            @if (auth('user')->user()->document_verified && auth('user')->user()->document_verified->status == 'approved')
                                <span class="text-blue-500"><x-svg.account-verification.verified-badge /></span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ $user->email }}</p>
                    </div>
                </div>
                <button 
                    @click="closeSidebar()"
                    class="touch-menu-item p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                    aria-label="Close menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="p-4 space-y-2">
            <!-- Overview -->
            <a href="{{ !request()->routeIs('frontend.dashboard') ? route('frontend.dashboard') : 'javascript:void(0)' }}"
                class="touch-menu-item flex items-center gap-3 p-3 rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 {{ request()->routeIs('frontend.dashboard') ? 'touch-menu-active bg-blue-50 dark:bg-blue-900/20' : '' }}"
                @click="closeSidebar()">
                <div class="touch-menu-icon w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                    <x-svg.overview-icon class="w-5 h-5 text-white" />
                </div>
                <span class="touch-menu-text font-medium text-gray-700 dark:text-gray-300">{{ __('overview') }}</span>
                <div class="touch-menu-arrow ml-auto">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </a>

            <!-- Public Profile -->
            <a href="{{ route('frontend.seller.profile', authUser()->username) }}"
                class="touch-menu-item flex items-center gap-3 p-3 rounded-xl hover:bg-green-50 dark:hover:bg-green-900/20 transition-all duration-200 {{ request()->routeIs('frontend.seller-dashboard') ? 'touch-menu-active bg-green-50 dark:bg-green-900/20' : '' }}"
                @click="closeSidebar()">
                <div class="touch-menu-icon w-10 h-10 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                    <x-svg.user-icon class="w-5 h-5 text-white" />
                </div>
                <span class="touch-menu-text font-medium text-gray-700 dark:text-gray-300">{{ __('public_profile') }}</span>
                <div class="touch-menu-arrow ml-auto">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </a>

            <!-- Post Listing -->
            <a href="{{ !request()->routeIs('frontend.post') ? route('frontend.post') : 'javascript:void(0)' }}"
                class="touch-menu-item flex items-center gap-3 p-3 rounded-xl hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all duration-200 {{ request()->routeIs('frontend.post') ? 'touch-menu-active bg-purple-50 dark:bg-purple-900/20' : '' }}"
                @click="closeSidebar()">
                <div class="touch-menu-icon w-10 h-10 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                    <x-svg.image-select-icon class="w-5 h-5 text-white" />
                </div>
                <span class="touch-menu-text font-medium text-gray-700 dark:text-gray-300">{{ __('post_listing') }}</span>
                <div class="touch-menu-arrow ml-auto">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </a>

            <!-- My Ads -->
            <a href="{{ !request()->routeIs('frontend.my.listing') ? route('frontend.my.listing') : 'javascript:void(0)' }}"
                class="touch-menu-item flex items-center gap-3 p-3 rounded-xl hover:bg-orange-50 dark:hover:bg-orange-900/20 transition-all duration-200 {{ request()->routeIs('frontend.my.listing') ? 'touch-menu-active bg-orange-50 dark:bg-orange-900/20' : '' }}"
                @click="closeSidebar()">
                <div class="touch-menu-icon w-10 h-10 bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
                    <x-svg.list-icon class="w-5 h-5 text-white" />
                </div>
                <span class="touch-menu-text font-medium text-gray-700 dark:text-gray-300">{{ __('my_ads') }}</span>
                <div class="touch-menu-arrow ml-auto">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </a>

            <!-- Resubmission -->
            <a href="{{ !request()->routeIs('frontend.resubmission.list') ? route('frontend.resubmission.list') : 'javascript:void(0)' }}"
                class="touch-menu-item flex items-center gap-3 p-3 rounded-xl hover:bg-yellow-50 dark:hover:bg-yellow-900/20 transition-all duration-200 {{ request()->routeIs('frontend.resubmission.list') ? 'touch-menu-active bg-yellow-50 dark:bg-yellow-900/20' : '' }}"
                @click="closeSidebar()">
                <div class="touch-menu-icon w-10 h-10 bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg flex items-center justify-center">
                    <x-svg.list-icon class="w-5 h-5 text-white" />
                </div>
                <span class="touch-menu-text font-medium text-gray-700 dark:text-gray-300">{{ __('resubmission_request') }}</span>
                <div class="touch-menu-arrow ml-auto">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </a>

            <!-- Favorites -->
            <a href="{{ !request()->routeIs('frontend.favorite.list') ? route('frontend.favorite.list') : 'javascript:void(0)' }}"
                class="touch-menu-item flex items-center gap-3 p-3 rounded-xl hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-200 {{ request()->routeIs('favorite-listing') ? 'touch-menu-active bg-red-50 dark:bg-red-900/20' : '' }}"
                @click="closeSidebar()">
                <div class="touch-menu-icon w-10 h-10 bg-gradient-to-r from-red-500 to-pink-600 rounded-lg flex items-center justify-center">
                    <x-svg.heart-icon class="w-5 h-5 text-white" />
                </div>
                <span class="touch-menu-text font-medium text-gray-700 dark:text-gray-300">{{ __('favorite_ads') }}</span>
                <div class="touch-menu-arrow ml-auto">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </a>

            <!-- Messages -->
            <a href="{{ !request()->routeIs('frontend.message') ? route('frontend.message') : 'javascript:void(0)' }}"
                class="touch-menu-item flex items-center gap-3 p-3 rounded-xl hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-all duration-200 {{ request()->routeIs('message') ? 'touch-menu-active bg-indigo-50 dark:bg-indigo-900/20' : '' }}"
                @click="closeSidebar()">
                <div class="touch-menu-icon w-10 h-10 bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center">
                    <x-svg.message-icon class="w-5 h-5 text-white" />
                </div>
                <span class="touch-menu-text font-medium text-gray-700 dark:text-gray-300">{{ __('message') }}</span>
                <div class="touch-menu-arrow ml-auto">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </a>

            <!-- Plans & Billing -->
            <a href="{{ route('frontend.plans-billing') }}"
                class="touch-menu-item flex items-center gap-3 p-3 rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-all duration-200 {{ request()->routeIs('frontend.plans-billing') ? 'touch-menu-active bg-emerald-50 dark:bg-emerald-900/20' : '' }}"
                @click="closeSidebar()">
                <div class="touch-menu-icon w-10 h-10 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-lg flex items-center justify-center">
                    <x-svg.invoice-icon class="w-5 h-5 text-white" />
                </div>
                <span class="touch-menu-text font-medium text-gray-700 dark:text-gray-300">{{ __('plans_billing') }}</span>
                <div class="touch-menu-arrow ml-auto">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </a>

            <!-- Blocked Users -->
            <a href="{{ route('get-blocked.users') }}"
                class="touch-menu-item flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-all duration-200 {{ request()->routeIs('get-blocked.users') ? 'touch-menu-active bg-gray-50 dark:bg-gray-800' : '' }}"
                @click="closeSidebar()">
                <div class="touch-menu-icon w-10 h-10 bg-gradient-to-r from-gray-500 to-gray-600 rounded-lg flex items-center justify-center">
                    <x-frontend.icons.block class="w-5 h-5 text-white" />
                </div>
                <span class="touch-menu-text font-medium text-gray-700 dark:text-gray-300">{{ __('Blocked List') }}</span>
                <div class="touch-menu-arrow ml-auto">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </a>

            <!-- Affiliate -->
            <a href="{{route('frontend.wallet')}}" 
                class="touch-menu-item flex items-center gap-3 p-3 rounded-xl hover:bg-cyan-50 dark:hover:bg-cyan-900/20 transition-all duration-200 {{ request()->routeIs('frontend.wallet') ? 'touch-menu-active bg-cyan-50 dark:bg-cyan-900/20' : '' }}"
                @click="closeSidebar()">
                <div class="touch-menu-icon w-10 h-10 bg-gradient-to-r from-cyan-500 to-cyan-600 rounded-lg flex items-center justify-center">
                    <x-svg.heroicons.share class="w-5 h-5 text-white" />
                </div>
                <span class="touch-menu-text font-medium text-gray-700 dark:text-gray-300">
                    @if (authUser()?->affiliate?->affiliate_code != null)
                        {{__('affiliate_system')}}
                    @else
                        {{__('become_an_affiliator')}}
                    @endif
                </span>
                <div class="touch-menu-arrow ml-auto">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </a>

            <!-- Verify Account -->
            <a href="/dashboard/verify-account"
                class="touch-menu-item flex items-center gap-3 p-3 rounded-xl hover:bg-teal-50 dark:hover:bg-teal-900/20 transition-all duration-200 {{ request()->routeIs('frontend.verify.account') ? 'touch-menu-active bg-teal-50 dark:bg-teal-900/20' : '' }}"
                @click="closeSidebar()">
                <div class="touch-menu-icon w-10 h-10 bg-gradient-to-r from-teal-500 to-teal-600 rounded-lg flex items-center justify-center">
                    <x-svg.user-check-icon class="w-5 h-5 text-white" />
                </div>
                <span class="touch-menu-text font-medium text-gray-700 dark:text-gray-300">{{ __('verify_account') }}</span>
                <div class="touch-menu-arrow ml-auto">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </a>

            <!-- Settings -->
            <a href="{{ !request()->routeIs('frontend.account-setting') ? route('frontend.account-setting') : 'javascript:void(0)' }}"
                class="touch-menu-item flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-all duration-200 {{ request()->routeIs('frontend.account-setting') ? 'touch-menu-active bg-slate-50 dark:bg-slate-800' : '' }}"
                @click="closeSidebar()">
                <div class="touch-menu-icon w-10 h-10 bg-gradient-to-r from-slate-500 to-slate-600 rounded-lg flex items-center justify-center">
                    <x-svg.setting-icon class="w-5 h-5 text-white" />
                </div>
                <span class="touch-menu-text font-medium text-gray-700 dark:text-gray-300">{{ __('account_setting') }}</span>
                <div class="touch-menu-arrow ml-auto">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </a>

            <!-- Logout -->
            <button 
                onclick="event.preventDefault();document.getElementById('touch-sidebar-logout-form').submit();"
                class="touch-menu-item w-full flex items-center gap-3 p-3 rounded-xl hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-200"
                @click="closeSidebar()">
                <div class="touch-menu-icon w-10 h-10 bg-gradient-to-r from-red-500 to-red-600 rounded-lg flex items-center justify-center">
                    <x-svg.logout-icon class="w-5 h-5 text-white" />
                </div>
                <span class="touch-menu-text font-medium text-gray-700 dark:text-gray-300">{{ __('logout') }}</span>
                <div class="touch-menu-arrow ml-auto">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </button>
            <form id="touch-sidebar-logout-form" action="{{ route('frontend.logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </nav>
    </div>

    <!-- Overlay -->
    <div 
        x-show="sidebarOpen"
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black bg-opacity-50 z-[9998]"
        @click="closeSidebar()"
        aria-hidden="true">
    </div>
</div>

<!-- Стили перенесены в header-top компонент -->
