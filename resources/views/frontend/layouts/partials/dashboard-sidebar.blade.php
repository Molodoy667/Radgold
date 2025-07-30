<!-- Touch-enabled Glass Sidebar with Swipe Support -->
<div x-data="{ 
    sidebarOpen: false,
    startX: 0,
    currentX: 0,
    isDragging: false,
    
    openSidebar() {
        this.sidebarOpen = true;
        document.body.style.overflow = 'hidden';
    },
    
    closeSidebar() {
        this.sidebarOpen = false;
        document.body.style.overflow = '';
    },
    
    handleTouchStart(e) {
        this.startX = e.touches[0].clientX;
        this.isDragging = true;
    },
    
    handleTouchMove(e) {
        if (!this.isDragging) return;
        this.currentX = e.touches[0].clientX;
        const deltaX = this.currentX - this.startX;
        
        // Открытие свайпом вправо с края экрана
        if (!this.sidebarOpen && this.startX < 50 && deltaX > 30) {
            this.openSidebar();
        }
        
        // Закрытие свайпом влево
        if (this.sidebarOpen && deltaX < -50) {
            this.closeSidebar();
        }
    },
    
    handleTouchEnd() {
        this.isDragging = false;
    }
}" 
@touchstart="handleTouchStart($event)"
@touchmove="handleTouchMove($event)" 
@touchend="handleTouchEnd()"
class="fixed inset-0 z-[9999]">

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

    <!-- Mobile Touch Sidebar (показывается только на мобильных) -->
    <div class="lg:hidden">
        <!-- Floating Menu Button -->
        <button @click="openSidebar()" 
                class="floating-menu-btn fixed top-1/2 left-2 z-[9998] transform -translate-y-1/2 
                       w-12 h-12 rounded-full
                       bg-gradient-to-br from-blue-500/90 to-purple-600/90
                       backdrop-blur-xl border border-white/20
                       shadow-2xl hover:shadow-blue-500/25
                       flex items-center justify-center
                       transition-all duration-300 ease-out
                       hover:scale-110 active:scale-95">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                 src="{{ $user->image_url }}" 
                                 alt="{{ $user->name }}">
                            @if (auth('user')->user()->document_verified && auth('user')->user()->document_verified->status == 'approved')
                                <div class="absolute -top-1 -right-1">
                                    <x-svg.account-verification.verified-badge />
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white truncate">
                                {{ $user->name }}
                            </h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400 truncate">
                                {{ $user->email }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Menu -->
            <div class="flex-1 overflow-y-auto py-4 px-2">
                <nav class="space-y-1">
                    <!-- Overview -->
                    <a href="{{ !request()->routeIs('frontend.dashboard') ? route('frontend.dashboard') : 'javascript:void(0)' }}"
                       @click="closeSidebar()"
                       class="touch-menu-item {{ request()->routeIs('frontend.dashboard') ? 'touch-menu-active' : '' }}">
                        <div class="touch-menu-icon bg-gradient-to-br from-blue-500 to-blue-600">
                            <x-svg.overview-icon />
                        </div>
                        <span class="touch-menu-text">{{ __('overview') }}</span>
                        <svg class="touch-menu-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>

                    <!-- Public Profile -->
                    <a href="{{ route('frontend.seller.profile', authUser()->username) }}"
                       @click="closeSidebar()"
                       class="touch-menu-item {{ request()->routeIs('frontend.seller-dashboard') ? 'touch-menu-active' : '' }}">
                        <div class="touch-menu-icon bg-gradient-to-br from-green-500 to-green-600">
                            <x-svg.user-icon width="20" height="20" stroke="currentColor" />
                        </div>
                        <span class="touch-menu-text">{{ __('public_profile') }}</span>
                        <svg class="touch-menu-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>

                    <!-- Post Listing -->
                    <a href="{{ !request()->routeIs('frontend.post') ? route('frontend.post') : 'javascript:void(0)' }}"
                       @click="closeSidebar()"
                       class="touch-menu-item {{ request()->routeIs('frontend.post') ? 'touch-menu-active' : '' }}">
                        <div class="touch-menu-icon bg-gradient-to-br from-purple-500 to-purple-600">
                            <x-svg.image-select-icon />
                        </div>
                        <span class="touch-menu-text">{{ __('post_listing') }}</span>
                        <svg class="touch-menu-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>

                    <!-- My Ads -->
                    <a href="{{ !request()->routeIs('frontend.my.listing') ? route('frontend.my.listing') : 'javascript:void(0)' }}"
                       @click="closeSidebar()"
                       class="touch-menu-item {{ request()->routeIs('frontend.my.listing') ? 'touch-menu-active' : '' }}">
                        <div class="touch-menu-icon bg-gradient-to-br from-orange-500 to-orange-600">
                            <x-svg.list-icon width="20" height="20" stroke="currentColor" />
                        </div>
                        <span class="touch-menu-text">{{ __('my_ads') }}</span>
                        <svg class="touch-menu-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>

                    <!-- Resubmission -->
                    <a href="{{ !request()->routeIs('frontend.resubmission.list') ? route('frontend.resubmission.list') : 'javascript:void(0)' }}"
                       @click="closeSidebar()"
                       class="touch-menu-item {{ request()->routeIs('frontend.resubmission.list') ? 'touch-menu-active' : '' }}">
                        <div class="touch-menu-icon bg-gradient-to-br from-yellow-500 to-yellow-600">
                            <x-svg.list-icon width="20" height="20" stroke="currentColor" />
                        </div>
                        <span class="touch-menu-text">{{ __('resubmission_request') }}</span>
                        <svg class="touch-menu-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>

                    <!-- Favorites -->
                    <a href="{{ !request()->routeIs('frontend.favorite.list') ? route('frontend.favorite.list') : 'javascript:void(0)' }}"
                       @click="closeSidebar()"
                       class="touch-menu-item {{ request()->routeIs('favorite-listing') ? 'touch-menu-active' : '' }}">
                        <div class="touch-menu-icon bg-gradient-to-br from-red-500 to-pink-600">
                            <x-svg.heart-icon fill="none" stroke="currentColor" />
                        </div>
                        <span class="touch-menu-text">{{ __('favorite_ads') }}</span>
                        <svg class="touch-menu-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>

                    <!-- Messages -->
                    <a href="{{ !request()->routeIs('frontend.message') ? route('frontend.message') : 'javascript:void(0)' }}"
                       @click="closeSidebar()"
                       class="touch-menu-item {{ request()->routeIs('message') ? 'touch-menu-active' : '' }}">
                        <div class="touch-menu-icon bg-gradient-to-br from-indigo-500 to-indigo-600">
                            <x-svg.message-icon width="20" height="20" stroke="currentColor" />
                        </div>
                        <span class="touch-menu-text">{{ __('message') }}</span>
                        <svg class="touch-menu-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>

                    <!-- Plans & Billing -->
                    <a href="{{ route('frontend.plans-billing') }}"
                       @click="closeSidebar()"
                       class="touch-menu-item {{ request()->routeIs('frontend.plans-billing') ? 'touch-menu-active' : '' }}">
                        <div class="touch-menu-icon bg-gradient-to-br from-emerald-500 to-emerald-600">
                            <x-svg.invoice-icon width="20" height="20" stroke="currentColor" />
                        </div>
                        <span class="touch-menu-text">{{ __('plans_billing') }}</span>
                        <svg class="touch-menu-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>

                    <!-- Blocked Users -->
                    <a href="{{ route('get-blocked.users') }}"
                       @click="closeSidebar()"
                       class="touch-menu-item {{ request()->routeIs('get-blocked.users') ? 'touch-menu-active' : '' }}">
                        <div class="touch-menu-icon bg-gradient-to-br from-gray-500 to-gray-600">
                            <x-frontend.icons.block stroke="currentColor" />
                        </div>
                        <span class="touch-menu-text">{{ __('Blocked List') }}</span>
                        <svg class="touch-menu-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>

                    <!-- Affiliate System -->
                    <a href="{{route('frontend.wallet')}}"
                       @click="closeSidebar()"
                       class="touch-menu-item {{ request()->routeIs('frontend.wallet') ? 'touch-menu-active' : '' }}">
                        <div class="touch-menu-icon bg-gradient-to-br from-cyan-500 to-cyan-600">
                            <x-svg.heroicons.share />
                        </div>
                        <span class="touch-menu-text">
                            @if (authUser()?->affiliate?->affiliate_code != null)
                                {{__('affiliate_system')}}
                            @else
                                {{__('become_an_affiliator')}}
                            @endif
                        </span>
                        <svg class="touch-menu-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>

                    <!-- Verify Account -->
                    <a href="/dashboard/verify-account"
                       @click="closeSidebar()"
                       class="touch-menu-item {{ request()->routeIs('frontend.verify.account') ? 'touch-menu-active' : '' }}">
                        <div class="touch-menu-icon bg-gradient-to-br from-teal-500 to-teal-600">
                            <x-svg.user-check-icon width="20" height="20" stroke="currentColor" />
                        </div>
                        <span class="touch-menu-text">{{ __('verify_account') }}</span>
                        <svg class="touch-menu-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>

                    <!-- Account Settings -->
                    <a href="{{ !request()->routeIs('frontend.account-setting') ? route('frontend.account-setting') : 'javascript:void(0)' }}"
                       @click="closeSidebar()"
                       class="touch-menu-item {{ request()->routeIs('frontend.account-setting') ? 'touch-menu-active' : '' }}">
                        <div class="touch-menu-icon bg-gradient-to-br from-slate-500 to-slate-600">
                            <x-svg.setting-icon />
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
                            <x-svg.logout-icon />
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
    </div>

    <!-- Logout Form -->
    <form id="touch-sidebar-logout-form" action="{{ route('frontend.logout') }}" method="POST" class="hidden">
        @csrf
    </form>
</div>

<style>
/* Touch Menu Styles */
.touch-menu-item {
    @apply flex items-center p-4 mx-2 rounded-xl 
           bg-white/40 dark:bg-gray-800/40 
           backdrop-blur-sm border border-white/20 dark:border-gray-700/30
           transition-all duration-300 ease-out
           hover:bg-white/60 dark:hover:bg-gray-800/60
           hover:shadow-lg hover:shadow-black/5
           active:scale-[0.98] active:bg-white/80 dark:active:bg-gray-800/80;
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

/* Custom scrollbar for touch sidebar */
.lg\:hidden nav {
    scrollbar-width: thin;
    scrollbar-color: rgba(156, 163, 175, 0.3) transparent;
}

.lg\:hidden nav::-webkit-scrollbar {
    width: 4px;
}

.lg\:hidden nav::-webkit-scrollbar-track {
    background: transparent;
}

.lg\:hidden nav::-webkit-scrollbar-thumb {
    background-color: rgba(156, 163, 175, 0.3);
    border-radius: 2px;
}

.lg\:hidden nav::-webkit-scrollbar-thumb:hover {
    background-color: rgba(156, 163, 175, 0.5);
}
</style>
