// Основний JavaScript файл

// Utility функції
const Utils = {
    // Показати повідомлення
    showMessage: function(title, text, type = 'success') {
        Swal.fire({
            title: title,
            text: text,
            icon: type,
            confirmButtonText: 'OK',
            confirmButtonColor: '#007bff'
        });
    },
    
    // Показати підтвердження
    confirmAction: function(title, text, callback) {
        Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Так',
            cancelButtonText: 'Скасувати',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed && typeof callback === 'function') {
                callback();
            }
        });
    },
    
    // Показати loading
    showLoading: function() {
        $('#loadingOverlay').fadeIn(200);
    },
    
    // Сховати loading
    hideLoading: function() {
        $('#loadingOverlay').fadeOut(200);
    },
    
    // Валідація email
    validateEmail: function(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    },
    
    // Валідація пароля
    validatePassword: function(password) {
        return password.length >= 6;
    },
    
    // Форматування дати
    formatDate: function(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('uk-UA', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    },
    
    // Скорочення тексту
    truncateText: function(text, length = 100) {
        if (text.length <= length) return text;
        return text.substring(0, length) + '...';
    }
};

// Клас для роботи з API
class API {
    static async request(url, options = {}) {
        const defaultOptions = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        };
        
        const config = { ...defaultOptions, ...options };
        
        try {
            const response = await fetch(url, config);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return await response.json();
            } else {
                return await response.text();
            }
        } catch (error) {
            console.error('API Request Error:', error);
            throw error;
        }
    }
    
    static async get(url) {
        return this.request(url);
    }
    
    static async post(url, data) {
        return this.request(url, {
            method: 'POST',
            body: JSON.stringify(data)
        });
    }
    
    static async postForm(url, formData) {
        return this.request(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });
    }
}

// Клас для роботи з темами
class ThemeManager {
    constructor() {
        this.currentTheme = $('html').attr('data-theme') || 'light';
        this.currentGradient = $('html').attr('data-gradient') || 'gradient-1';
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.updateTheme();
    }
    
    bindEvents() {
        // Обробник зміни теми
        $(document).on('change', 'input[name="theme"]', (e) => {
            this.changeTheme(e.target.value);
        });
        
        // Обробник зміни градієнта
        $(document).on('click', '.gradient-option', (e) => {
            const gradient = $(e.currentTarget).data('gradient');
            this.changeGradient(gradient);
        });
    }
    
    async changeTheme(theme) {
        try {
            Utils.showLoading();
            
            const response = await API.post('ajax/change_theme.php', {
                action: 'change_theme',
                theme: theme
            });
            
            if (response.success) {
                this.currentTheme = theme;
                this.updateTheme();
                Utils.showMessage('Успіх', 'Тему змінено');
            } else {
                throw new Error(response.message || 'Помилка зміни теми');
            }
        } catch (error) {
            console.error('Theme change error:', error);
            Utils.showMessage('Помилка', 'Не вдалося змінити тему', 'error');
        } finally {
            Utils.hideLoading();
        }
    }
    
    async changeGradient(gradient) {
        try {
            Utils.showLoading();
            
            const response = await API.post('ajax/change_theme.php', {
                action: 'change_gradient',
                gradient: gradient
            });
            
            if (response.success) {
                this.currentGradient = gradient;
                this.updateGradient();
                Utils.showMessage('Успіх', 'Градієнт змінено');
            } else {
                throw new Error(response.message || 'Помилка зміни градієнта');
            }
        } catch (error) {
            console.error('Gradient change error:', error);
            Utils.showMessage('Помилка', 'Не вдалося змінити градієнт', 'error');
        } finally {
            Utils.hideLoading();
        }
    }
    
    updateTheme() {
        $('html').attr('data-theme', this.currentTheme);
        $('body').removeClass('light-theme dark-theme').addClass(this.currentTheme + '-theme');
        
        const root = document.documentElement;
        if (this.currentTheme === 'dark') {
            root.style.setProperty('--theme-bg', '#1a1a1a');
            root.style.setProperty('--theme-text', '#ffffff');
            root.style.setProperty('--theme-bg-secondary', '#2d2d2d');
            root.style.setProperty('--theme-border', '#404040');
        } else {
            root.style.setProperty('--theme-bg', '#ffffff');
            root.style.setProperty('--theme-text', '#333333');
            root.style.setProperty('--theme-bg-secondary', '#f8f9fa');
            root.style.setProperty('--theme-border', '#dee2e6');
        }
    }
    
    updateGradient() {
        $('html').attr('data-gradient', this.currentGradient);
        $('.gradient-option').removeClass('active').find('i').remove();
        $(`.gradient-option[data-gradient="${this.currentGradient}"]`)
            .addClass('active')
            .append('<i class="fas fa-check"></i>');
    }
}

// Клас для роботи з формами
class FormManager {
    constructor() {
        this.init();
    }
    
    init() {
        this.bindEvents();
    }
    
    bindEvents() {
        // Обробник відправки форм через AJAX
        $(document).on('submit', '.ajax-form', (e) => {
            e.preventDefault();
            this.submitForm(e.target);
        });
        
        // Валідація в реальному часі
        $(document).on('input', '.form-control', (e) => {
            this.validateField(e.target);
        });
        
        // Обробник завантаження файлів
        $(document).on('change', '.file-upload', (e) => {
            this.handleFileUpload(e.target);
        });
    }
    
    async submitForm(form) {
        const $form = $(form);
        const formData = new FormData(form);
        
        try {
            Utils.showLoading();
            
            // Додаємо CSRF токен
            const token = $('meta[name="csrf-token"]').attr('content');
            if (token) {
                formData.append('csrf_token', token);
            }
            
            const response = await API.postForm($form.attr('action'), formData);
            
            if (response.success) {
                Utils.showMessage('Успіх', response.message || 'Дані збережено');
                
                // Перенаправлення якщо потрібно
                if (response.redirect) {
                    setTimeout(() => {
                        window.location.href = response.redirect;
                    }, 1500);
                }
                
                // Очищення форми
                if (response.reset_form) {
                    form.reset();
                }
            } else {
                throw new Error(response.message || 'Помилка обробки форми');
            }
        } catch (error) {
            console.error('Form submission error:', error);
            Utils.showMessage('Помилка', error.message, 'error');
        } finally {
            Utils.hideLoading();
        }
    }
    
    validateField(field) {
        const $field = $(field);
        const value = $field.val();
        const type = $field.attr('type');
        const required = $field.prop('required');
        
        let isValid = true;
        let message = '';
        
        // Перевірка обов'язкових полів
        if (required && !value.trim()) {
            isValid = false;
            message = 'Це поле обов\'язкове';
        }
        
        // Перевірка email
        if (type === 'email' && value && !Utils.validateEmail(value)) {
            isValid = false;
            message = 'Невірний формат email';
        }
        
        // Перевірка пароля
        if (type === 'password' && value && !Utils.validatePassword(value)) {
            isValid = false;
            message = 'Пароль повинен містити мінімум 6 символів';
        }
        
        // Візуальний зворотний зв'язок
        if (isValid) {
            $field.removeClass('is-invalid').addClass('is-valid');
            $field.siblings('.invalid-feedback').hide();
        } else {
            $field.removeClass('is-valid').addClass('is-invalid');
            $field.siblings('.invalid-feedback').text(message).show();
        }
        
        return isValid;
    }
    
    handleFileUpload(input) {
        const file = input.files[0];
        if (!file) return;
        
        const $input = $(input);
        const maxSize = 5 * 1024 * 1024; // 5MB
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        
        // Перевірка розміру
        if (file.size > maxSize) {
            Utils.showMessage('Помилка', 'Розмір файлу не повинен перевищувати 5MB', 'error');
            input.value = '';
            return;
        }
        
        // Перевірка типу
        if (!allowedTypes.includes(file.type)) {
            Utils.showMessage('Помилка', 'Дозволені тільки зображення (JPG, PNG, GIF)', 'error');
            input.value = '';
            return;
        }
        
        // Попередній перегляд
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = $input.siblings('.file-preview');
                if (preview.length) {
                    preview.html(`<img src="${e.target.result}" alt="Preview" class="img-thumbnail" style="max-width: 200px;">`);
                }
            };
            reader.readAsDataURL(file);
        }
    }
}

// Клас для анімацій
class AnimationManager {
    constructor() {
        this.init();
    }
    
    init() {
        this.observeElements();
        this.initScrollAnimations();
    }
    
    observeElements() {
        // Intersection Observer для анімацій при скролі
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const element = entry.target;
                    const animation = element.dataset.animation;
                    
                    if (animation) {
                        element.classList.add('animate__animated', `animate__${animation}`);
                    }
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });
        
        // Спостерігаємо за елементами з анімаціями
        document.querySelectorAll('[data-animation]').forEach(el => {
            observer.observe(el);
        });
    }
    
    initScrollAnimations() {
        // Паралакс ефект для header
        $(window).scroll(() => {
            const scrolled = $(window).scrollTop();
            const parallax = scrolled * 0.5;
            
            $('.parallax-bg').css('transform', `translateY(${parallax}px)`);
        });
        
        // Анімація елементів при скролі
        $(window).scroll(() => {
            $('.animate-on-scroll').each(function() {
                const elementTop = $(this).offset().top;
                const elementBottom = elementTop + $(this).outerHeight();
                const viewportTop = $(window).scrollTop();
                const viewportBottom = viewportTop + $(window).height();
                
                if (elementBottom > viewportTop && elementTop < viewportBottom) {
                    $(this).addClass('animate__animated animate__fadeInUp');
                }
            });
        });
    }
}

// Клас для роботи з медіа
class MediaManager {
    constructor() {
        this.init();
    }
    
    init() {
        this.initLightbox();
        this.initLazyLoading();
    }
    
    initLightbox() {
        // Lightbox для зображень
        $(document).on('click', '.lightbox-trigger', function(e) {
            e.preventDefault();
            
            const src = $(this).attr('href') || $(this).data('src');
            const title = $(this).attr('title') || $(this).data('title');
            
            Swal.fire({
                imageUrl: src,
                imageAlt: title,
                showConfirmButton: false,
                showCloseButton: true,
                background: 'transparent',
                backdrop: 'rgba(0,0,0,0.8)'
            });
        });
    }
    
    initLazyLoading() {
        // Lazy loading для зображень
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    observer.unobserve(img);
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
}

// Ініціалізація при завантаженні сторінки
$(document).ready(function() {
    // Ініціалізуємо всі модулі
    const themeManager = new ThemeManager();
    const formManager = new FormManager();
    const animationManager = new AnimationManager();
    const mediaManager = new MediaManager();
    
    // Додаткові ініціалізації
    initTooltips();
    initPopovers();
    initSmoothScroll();
    initSearch();
    
    console.log('AdBoard Pro initialized successfully!');
});

// Допоміжні функції
function initTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

function initPopovers() {
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
}

function initSmoothScroll() {
    $('a[href^="#"]').on('click', function(e) {
        const target = $(this.getAttribute('href'));
        if (target.length) {
            e.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 80
            }, 1000);
        }
    });
}

function initSearch() {
    let searchTimeout;
    
    $(document).on('input', '.search-input', function() {
        const query = $(this).val();
        const $results = $(this).siblings('.search-results');
        
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            $results.hide();
            return;
        }
        
        searchTimeout = setTimeout(() => {
            performSearch(query, $results);
        }, 300);
    });
}

async function performSearch(query, $results) {
    try {
        const response = await API.post('ajax/search.php', {
            query: query,
            limit: 10
        });
        
        if (response.success && response.data.length > 0) {
            let html = '<div class="list-group">';
            response.data.forEach(item => {
                html += `
                    <a href="${item.url}" class="list-group-item list-group-item-action">
                        <div class="d-flex align-items-center">
                            ${item.image ? `<img src="${item.image}" class="me-3" width="50" height="50" style="object-fit: cover; border-radius: 8px;">` : ''}
                            <div>
                                <h6 class="mb-1">${item.title}</h6>
                                <small class="text-muted">${Utils.truncateText(item.description, 100)}</small>
                            </div>
                        </div>
                    </a>
                `;
            });
            html += '</div>';
            
            $results.html(html).show();
        } else {
            $results.html('<div class="p-3 text-center text-muted">Нічого не знайдено</div>').show();
        }
    } catch (error) {
        console.error('Search error:', error);
        $results.hide();
    }
}

// Глобальні функції для доступу з HTML
window.Utils = Utils;
window.API = API;
