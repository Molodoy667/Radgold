/**
 * Enhanced Touch Sidebar for Radgold
 * Provides advanced swipe gestures and touch interactions
 */

class TouchSidebar {
    constructor() {
        this.isOpen = false;
        this.startX = 0;
        this.startY = 0;
        this.currentX = 0;
        this.currentY = 0;
        this.isDragging = false;
        this.velocity = 0;
        this.lastX = 0;
        this.lastTime = 0;
        
        // Thresholds
        this.EDGE_THRESHOLD = 20; // Pixels from edge to trigger
        this.SWIPE_THRESHOLD = 50; // Minimum distance for swipe
        this.VELOCITY_THRESHOLD = 0.3; // Minimum velocity for quick swipe
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.setupHammerJS();
    }
    
    bindEvents() {
        // Global touch events for edge detection
        document.addEventListener('touchstart', this.handleGlobalTouchStart.bind(this), { passive: false });
        document.addEventListener('touchmove', this.handleGlobalTouchMove.bind(this), { passive: false });
        document.addEventListener('touchend', this.handleGlobalTouchEnd.bind(this), { passive: false });
        
        // Keyboard accessibility
        document.addEventListener('keydown', this.handleKeydown.bind(this));
        
        // Window resize
        window.addEventListener('resize', this.handleResize.bind(this));
        
        // Focus trap when sidebar is open
        document.addEventListener('focusin', this.handleFocusTrap.bind(this));
    }
    
    setupHammerJS() {
        // If HammerJS is available, use it for better gesture recognition
        if (typeof Hammer !== 'undefined') {
            const sidebar = document.querySelector('.glass-sidebar');
            if (sidebar) {
                const hammer = new Hammer(sidebar);
                hammer.get('swipe').set({ direction: Hammer.DIRECTION_HORIZONTAL });
                hammer.on('swipeleft', () => this.closeSidebar());
            }
        }
    }
    
    handleGlobalTouchStart(e) {
        this.startX = e.touches[0].clientX;
        this.startY = e.touches[0].clientY;
        this.lastX = this.startX;
        this.lastTime = Date.now();
        this.isDragging = false;
        
        // Check if touch started near left edge
        if (!this.isOpen && this.startX <= this.EDGE_THRESHOLD) {
            this.isDragging = true;
            document.body.style.userSelect = 'none';
        }
    }
    
    handleGlobalTouchMove(e) {
        if (!this.isDragging && !this.isOpen) return;
        
        this.currentX = e.touches[0].clientX;
        this.currentY = e.touches[0].clientY;
        
        const deltaX = this.currentX - this.startX;
        const deltaY = Math.abs(this.currentY - this.startY);
        const time = Date.now();
        const timeDelta = time - this.lastTime;
        
        // Calculate velocity
        if (timeDelta > 0) {
            this.velocity = (this.currentX - this.lastX) / timeDelta;
        }
        
        // Prevent scrolling if horizontal swipe is detected
        if (Math.abs(deltaX) > deltaY && Math.abs(deltaX) > 10) {
            e.preventDefault();
        }
        
        // Open sidebar with swipe right from edge
        if (!this.isOpen && this.startX <= this.EDGE_THRESHOLD && deltaX > 30) {
            this.openSidebar();
            this.isDragging = false;
        }
        
        // Close sidebar with swipe left
        if (this.isOpen && deltaX < -this.SWIPE_THRESHOLD) {
            this.closeSidebar();
        }
        
        this.lastX = this.currentX;
        this.lastTime = time;
    }
    
    handleGlobalTouchEnd(e) {
        if (!this.isDragging && !this.isOpen) return;
        
        const deltaX = this.currentX - this.startX;
        
        // Quick swipe detection based on velocity
        if (Math.abs(this.velocity) > this.VELOCITY_THRESHOLD) {
            if (this.velocity > 0 && !this.isOpen && this.startX <= this.EDGE_THRESHOLD) {
                this.openSidebar();
            } else if (this.velocity < 0 && this.isOpen) {
                this.closeSidebar();
            }
        }
        
        this.isDragging = false;
        document.body.style.userSelect = '';
        this.velocity = 0;
    }
    
    handleKeydown(e) {
        // Close sidebar with Escape key
        if (e.key === 'Escape' && this.isOpen) {
            this.closeSidebar();
        }
        
        // Prevent tab from leaving sidebar when open
        if (e.key === 'Tab' && this.isOpen) {
            this.trapFocus(e);
        }
    }
    
    handleResize() {
        // Close sidebar on resize to desktop
        if (window.innerWidth >= 1024 && this.isOpen) {
            this.closeSidebar();
        }
    }
    
    handleFocusTrap(e) {
        if (!this.isOpen) return;
        
        const sidebar = document.querySelector('.glass-sidebar');
        if (sidebar && !sidebar.contains(e.target)) {
            // Focus went outside sidebar, bring it back
            const firstFocusable = sidebar.querySelector('button, a, input, select, textarea, [tabindex]:not([tabindex="-1"])');
            if (firstFocusable) {
                firstFocusable.focus();
            }
        }
    }
    
    trapFocus(e) {
        const sidebar = document.querySelector('.glass-sidebar');
        if (!sidebar) return;
        
        const focusableElements = sidebar.querySelectorAll(
            'button:not([disabled]), a[href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
        );
        
        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];
        
        if (e.shiftKey && document.activeElement === firstElement) {
            e.preventDefault();
            lastElement.focus();
        } else if (!e.shiftKey && document.activeElement === lastElement) {
            e.preventDefault();
            firstElement.focus();
        }
    }
    
    openSidebar() {
        this.isOpen = true;
        document.body.style.overflow = 'hidden';
        
        // Trigger Alpine.js state update
        if (window.Alpine) {
            const sidebarComponent = document.querySelector('[x-data*="sidebarOpen"]');
            if (sidebarComponent && sidebarComponent._x_dataStack) {
                sidebarComponent._x_dataStack[0].sidebarOpen = true;
            }
        }
        
        // Focus first element for accessibility
        setTimeout(() => {
            const sidebar = document.querySelector('.glass-sidebar');
            const firstFocusable = sidebar?.querySelector('button:not([disabled]), a[href]');
            firstFocusable?.focus();
        }, 100);
        
        // Analytics
        this.trackEvent('sidebar_opened', 'swipe');
    }
    
    closeSidebar() {
        this.isOpen = false;
        document.body.style.overflow = '';
        
        // Trigger Alpine.js state update
        if (window.Alpine) {
            const sidebarComponent = document.querySelector('[x-data*="sidebarOpen"]');
            if (sidebarComponent && sidebarComponent._x_dataStack) {
                sidebarComponent._x_dataStack[0].sidebarOpen = false;
            }
        }
        
        // Return focus to menu button
        setTimeout(() => {
            const menuButton = document.querySelector('.floating-menu-btn');
            menuButton?.focus();
        }, 200);
        
        // Analytics
        this.trackEvent('sidebar_closed', 'swipe');
    }
    
    trackEvent(action, method) {
        // Google Analytics tracking
        if (typeof gtag !== 'undefined') {
            gtag('event', action, {
                'event_category': 'navigation',
                'event_label': method,
                'value': 1
            });
        }
        
        // Custom analytics
        if (window.analytics && typeof window.analytics.track === 'function') {
            window.analytics.track(action, {
                method: method,
                timestamp: Date.now()
            });
        }
    }
    
    // Public API
    toggle() {
        this.isOpen ? this.closeSidebar() : this.openSidebar();
    }
    
    destroy() {
        document.removeEventListener('touchstart', this.handleGlobalTouchStart);
        document.removeEventListener('touchmove', this.handleGlobalTouchMove);
        document.removeEventListener('touchend', this.handleGlobalTouchEnd);
        document.removeEventListener('keydown', this.handleKeydown);
        window.removeEventListener('resize', this.handleResize);
        document.removeEventListener('focusin', this.handleFocusTrap);
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Only initialize on mobile devices
    if (window.innerWidth < 1024) {
        window.touchSidebar = new TouchSidebar();
    }
});

// Re-initialize on window resize if needed
window.addEventListener('resize', () => {
    if (window.innerWidth < 1024 && !window.touchSidebar) {
        window.touchSidebar = new TouchSidebar();
    } else if (window.innerWidth >= 1024 && window.touchSidebar) {
        window.touchSidebar.destroy();
        window.touchSidebar = null;
    }
});

// Alpine.js integration
document.addEventListener('alpine:init', () => {
    Alpine.data('touchSidebar', () => ({
        sidebarOpen: false,
        
        openSidebar() {
            this.sidebarOpen = true;
            document.body.style.overflow = 'hidden';
            
            // Update touch sidebar state
            if (window.touchSidebar) {
                window.touchSidebar.isOpen = true;
            }
            
            // Focus first element for accessibility
            setTimeout(() => {
                const sidebar = document.querySelector('.glass-sidebar');
                const firstFocusable = sidebar?.querySelector('button:not([disabled]), a[href]');
                firstFocusable?.focus();
            }, 100);
        },
        
        closeSidebar() {
            this.sidebarOpen = false;
            document.body.style.overflow = '';
            
            // Update touch sidebar state
            if (window.touchSidebar) {
                window.touchSidebar.isOpen = false;
            }
            
            // Return focus to menu button
            setTimeout(() => {
                const menuButton = document.querySelector('.floating-menu-btn');
                menuButton?.focus();
            }, 200);
        },
        
        toggle() {
            this.sidebarOpen ? this.closeSidebar() : this.openSidebar();
        }
    }));
});