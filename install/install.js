$(document).ready(function() {
    // Ініціалізація
    initializeInstaller();
    
    // Обробники подій
    bindEvents();
    
    // Показуємо лог якщо є записи
    if ($('.log-entry').length > 0) {
        $('.log-container').show();
    }
});

function initializeInstaller() {
    // Анімація кроків
    animateSteps();
    
    // Валідація форм
    setupFormValidation();
    
    // Налаштування прогрес-бару
    setupProgressBar();
    
    // Автофокус на перше поле
    $('.form-control:first').focus();
}

function bindEvents() {
    // Тест підключення до БД
    $('#testConnection').click(function() {
        testDatabaseConnection();
    });
    
    // Вибір градієнта
    $('.gradient-option').click(function() {
        selectGradient($(this));
    });
    
    // Валідація в реальному часі
    $('.form-control').on('input', function() {
        validateField($(this));
    });
    
    // Показ/сховати пароль
    $('.password-toggle').click(function() {
        togglePasswordVisibility($(this));
    });
    
    // Перевірка чекбоксу ліцензії
    $('#acceptLicense').change(function() {
        $('#nextBtn').prop('disabled', !$(this).is(':checked'));
    });
    
    // Обробка форм
    $('form').submit(function(e) {
        showLoadingState($(this));
    });
    
    // Прокрутка логу вниз
    scrollLogToBottom();
}

function animateSteps() {
    $('.step-circle').each(function(index) {
        $(this).css('animation-delay', (index * 0.1) + 's');
    });
}

function setupFormValidation() {
    // Додаємо валідацію Bootstrap
    $('form').addClass('needs-validation');
    
    // Кастомна валідація
    $('.form-control').each(function() {
        $(this).after('<div class="invalid-feedback"></div>');
    });
}

function setupProgressBar() {
    const currentStep = parseInt(new URLSearchParams(window.location.search).get('step') || '1');
    const maxSteps = 8;
    const progress = ((currentStep - 1) / (maxSteps - 1)) * 100;
    
    animateProgress(0, progress, 1000);
}

function testDatabaseConnection() {
    const $btn = $('#testConnection');
    const $result = $('#connectionResult');
    const originalText = $btn.html();
    
    // Отримуємо дані форми
    const formData = {
        test_connection: 1,
        db_host: $('#db_host').val(),
        db_user: $('#db_user').val(),
        db_pass: $('#db_pass').val()
    };
    
    // Показуємо завантаження
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Тестування...');
    $result.hide();
    
    // AJAX запит
    $.post(window.location.href, formData)
        .done(function(response) {
            // Перезавантажуємо сторінку для показу результату
            location.reload();
        })
        .fail(function() {
            $result.removeClass('alert-success').addClass('alert-danger')
                   .html('<i class="fas fa-times me-2"></i>Помилка тестування підключення')
                   .show();
        })
        .always(function() {
            $btn.prop('disabled', false).html(originalText);
        });
}

function selectGradient($element) {
    // Знімаємо виділення з інших
    $('.gradient-option').removeClass('selected').find('i').remove();
    
    // Виділяємо обраний
    $element.addClass('selected').append('<i class="fas fa-check"></i>');
    
    // Оновлюємо hidden поле
    $('#selectedGradient').val($element.data('gradient'));
    
    // Анімація
    $element.addClass('animate__animated animate__pulse');
    setTimeout(() => {
        $element.removeClass('animate__animated animate__pulse');
    }, 1000);
}

function validateField($field) {
    const value = $field.val().trim();
    const fieldName = $field.attr('name');
    let isValid = true;
    let message = '';
    
    // Перевірка обов'язкових полів
    if ($field.prop('required') && !value) {
        isValid = false;
        message = 'Це поле обов\'язкове';
    }
    
    // Спеціальні валідації
    switch (fieldName) {
        case 'db_name':
            if (value && !/^[a-zA-Z0-9_]+$/.test(value)) {
                isValid = false;
                message = 'Тільки літери, цифри та підкреслення';
            }
            break;
            
        case 'admin_email':
            if (value && !isValidEmail(value)) {
                isValid = false;
                message = 'Невірний формат email';
            }
            break;
            
        case 'admin_password':
            if (value && value.length < 6) {
                isValid = false;
                message = 'Мінімум 6 символів';
            }
            break;
            
        case 'admin_password_confirm':
            const password = $('input[name="admin_password"]').val();
            if (value && value !== password) {
                isValid = false;
                message = 'Паролі не співпадають';
            }
            break;
            
        case 'site_url':
            if (value && !isValidUrl(value)) {
                isValid = false;
                message = 'Невірний формат URL';
            }
            break;
    }
    
    // Показуємо результат валідації
    if (isValid) {
        $field.removeClass('is-invalid').addClass('is-valid');
        $field.siblings('.invalid-feedback').hide();
    } else {
        $field.removeClass('is-valid').addClass('is-invalid');
        $field.siblings('.invalid-feedback').text(message).show();
    }
    
    return isValid;
}

function togglePasswordVisibility($btn) {
    const $input = $btn.siblings('input');
    const $icon = $btn.find('i');
    
    if ($input.attr('type') === 'password') {
        $input.attr('type', 'text');
        $icon.removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
        $input.attr('type', 'password');
        $icon.removeClass('fa-eye-slash').addClass('fa-eye');
    }
}

function showLoadingState($form) {
    const $btn = $form.find('button[type="submit"]');
    const originalText = $btn.html();
    
    $btn.prop('disabled', true)
        .html('<i class="fas fa-spinner fa-spin me-2"></i>Обробка...');
    
    // Показуємо прогрес якщо це крок установки
    const currentStep = parseInt(new URLSearchParams(window.location.search).get('step') || '1');
    if (currentStep === 7) {
        showInstallationProgress();
    }
}

function showInstallationProgress() {
    const $container = $('.install-animation');
    if ($container.length) {
        $container.show();
        
        // Симулюємо прогрес установки
        let progress = 0;
        const interval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress >= 100) {
                progress = 100;
                clearInterval(interval);
            }
            
            $('.progress-fill').css('width', progress + '%');
            $('#progressText').text(`Установка: ${Math.round(progress)}%`);
        }, 500);
    }
}

function animateProgress(from, to, duration) {
    const $progressFill = $('.progress-fill');
    let current = from;
    const increment = (to - from) / (duration / 50);
    
    const interval = setInterval(() => {
        current += increment;
        if (current >= to) {
            current = to;
            clearInterval(interval);
        }
        $progressFill.css('width', current + '%');
    }, 50);
}

function scrollLogToBottom() {
    const $logContainer = $('.log-container');
    if ($logContainer.length) {
        $logContainer.scrollTop($logContainer[0].scrollHeight);
    }
}

function isValidEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

function isValidUrl(url) {
    try {
        new URL(url);
        return true;
    } catch {
        return false;
    }
}

// Додаємо кнопки показу/сховання пароля
$(document).ready(function() {
    $('input[type="password"]').each(function() {
        const $input = $(this);
        const $wrapper = $input.parent();
        
        if (!$wrapper.hasClass('position-relative')) {
            $input.wrap('<div class="position-relative"></div>');
        }
        
        $input.after(`
            <button type="button" class="btn btn-outline-secondary password-toggle" 
                    style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); border: none; background: none; padding: 5px 10px;">
                <i class="fas fa-eye"></i>
            </button>
        `);
    });
});

// Анімація успішного завершення
function showSuccessAnimation() {
    $('.success-page').addClass('animate__animated animate__fadeIn');
    $('.success-icon').addClass('animate__animated animate__bounceIn');
    
    // Конфетті ефект (якщо потрібно)
    if (typeof confetti !== 'undefined') {
        confetti({
            particleCount: 100,
            spread: 70,
            origin: { y: 0.6 }
        });
    }
}

// Перевірка системних вимог
function checkSystemRequirements() {
    $('.requirement-item').each(function(index) {
        $(this).css('animation-delay', (index * 0.1) + 's')
               .addClass('animate__animated animate__slideInLeft');
    });
}

// Автоматичне оновлення прогресу для кроку 7
if (window.location.search.includes('step=7')) {
    setInterval(() => {
        $.get(window.location.href + '&ajax=1')
            .done(function(response) {
                if (response.logs) {
                    updateInstallLog(response.logs);
                }
                if (response.completed) {
                    window.location.href = '?step=8';
                }
            });
    }, 1000);
}

function updateInstallLog(logs) {
    const $logContainer = $('.log-container .log-entries');
    logs.forEach(log => {
        const $entry = $(`
            <div class="log-entry log-${log.status}">
                <span class="text-muted">${log.time}</span> 
                [${log.step.toUpperCase()}] 
                ${log.message}
            </div>
        `);
        $logContainer.append($entry);
    });
    
    scrollLogToBottom();
}

// Global error handler
window.addEventListener('error', function(e) {
    console.error('Install Error:', e.error);
});
