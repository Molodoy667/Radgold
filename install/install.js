/**
 * JavaScript функціонал для інсталятора дошки оголошень
 */

document.addEventListener('DOMContentLoaded', function() {
    // Анімації для кроків
    animateSteps();
    
    // Валідація форм
    setupFormValidation();
    
    // Tooltips
    initTooltips();
    
    // Автофокус на поля
    setupAutoFocus();
});

function animateSteps() {
    const steps = document.querySelectorAll('.step');
    steps.forEach((step, index) => {
        step.style.animationDelay = (index * 0.1) + 's';
        step.classList.add('animate__animated', 'animate__fadeInUp');
    });
}

function setupFormValidation() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
    
    // Валідація в реальному часі
    const inputs = document.querySelectorAll('input[required]');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (!this.checkValidity()) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
    });
}

function initTooltips() {
    // Ініціалізація Bootstrap tooltips якщо доступні
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
}

function setupAutoFocus() {
    // Автофокус на перше поле форми
    const firstInput = document.querySelector('form input:not([type="hidden"]):first-of-type');
    if (firstInput && !firstInput.value) {
        firstInput.focus();
    }
}

// Функція для показу повідомлень
function showMessage(message, type = 'info') {
    const alertClass = {
        'success': 'alert-success',
        'error': 'alert-danger',
        'warning': 'alert-warning',
        'info': 'alert-info'
    };
    
    const alert = document.createElement('div');
    alert.className = `alert ${alertClass[type]} alert-dismissible fade show position-fixed`;
    alert.style.cssText = `
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    `;
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alert);
    
    // Автоматичне приховування через 5 секунд
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}

// Анімація кнопок
function addButtonAnimation() {
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            if (!this.disabled) {
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 150);
            }
        });
    });
}

// Індикатор прогресу для довгих операцій
function showProgress(container, message) {
    container.innerHTML = `
        <div class="d-flex align-items-center">
            <div class="spinner-border spinner-border-sm me-2" role="status">
                <span class="visually-hidden">Завантаження...</span>
            </div>
            <span>${message}</span>
        </div>
    `;
}

// Перевірка підтримки браузера
function checkBrowserSupport() {
    const requiredFeatures = [
        'fetch',
        'Promise',
        'FormData'
    ];
    
    const unsupported = requiredFeatures.filter(feature => !window[feature]);
    
    if (unsupported.length > 0) {
        showMessage(
            'Ваш браузер не підтримує деякі необхідні функції. Рекомендуємо оновити браузер.',
            'warning'
        );
    }
}

// Копіювання тексту в буфер обміну
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            showMessage('Скопійовано в буфер обміну!', 'success');
        });
    } else {
        // Fallback для старих браузерів
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showMessage('Скопійовано в буфер обміну!', 'success');
    }
}

// Анімація появи елементів
function animateOnScroll() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate__animated', 'animate__fadeInUp');
            }
        });
    });
    
    const elements = document.querySelectorAll('.card, .alert');
    elements.forEach(el => observer.observe(el));
}

// Збереження прогресу інсталяції в localStorage
function saveInstallProgress(step, data = {}) {
    const progress = {
        step: step,
        timestamp: Date.now(),
        data: data
    };
    localStorage.setItem('install_progress', JSON.stringify(progress));
}

function getInstallProgress() {
    const saved = localStorage.getItem('install_progress');
    return saved ? JSON.parse(saved) : null;
}

function clearInstallProgress() {
    localStorage.removeItem('install_progress');
}

// Перевірка з'єднання з інтернетом
function checkConnection() {
    return fetch('https://httpbin.org/get', {
        method: 'HEAD',
        mode: 'no-cors'
    }).then(() => true).catch(() => false);
}

// Генерація безпечного пароля
function generatePassword(length = 12) {
    const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
    let password = "";
    for (let i = 0; i < length; i++) {
        password += charset.charAt(Math.floor(Math.random() * charset.length));
    }
    return password;
}

// Функція для показу/приховування пароля з анімацією
function togglePasswordWithAnimation(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    
    // Анімація зміни іконки
    icon.style.transform = 'scale(0.8)';
    
    setTimeout(() => {
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fas fa-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'fas fa-eye';
        }
        icon.style.transform = 'scale(1)';
    }, 150);
}

// Ініціалізація після завантаження DOM
document.addEventListener('DOMContentLoaded', function() {
    checkBrowserSupport();
    addButtonAnimation();
    animateOnScroll();
    
    // Показуємо збережений прогрес якщо є
    const progress = getInstallProgress();
    if (progress && progress.step) {
        console.log('Знайдено збережений прогрес:', progress);
    }
});