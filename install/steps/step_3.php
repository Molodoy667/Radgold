<div class="text-center mb-4">
    <i class="fas fa-database fa-3x text-primary mb-3"></i>
    <h3>Налаштування бази даних</h3>
    <p class="text-muted">Введіть параметри підключення до MySQL бази даних</p>
</div>

<form method="POST" class="needs-validation" novalidate>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="db_host" class="form-label">
                <i class="fas fa-server me-2"></i>Хост бази даних <span class="text-danger">*</span>
            </label>
            <input type="text" 
                   class="form-control" 
                   id="db_host" 
                   name="db_host" 
                   value="<?php echo htmlspecialchars($_SESSION['install_data']['db_config']['host'] ?? 'localhost'); ?>" 
                   placeholder="localhost або IP адреса"
                   required>
            <div class="invalid-feedback"></div>
            <small class="form-text text-muted">Зазвичай localhost для локального сервера</small>
        </div>
        
        <div class="col-md-6 mb-3">
            <label for="db_name" class="form-label">
                <i class="fas fa-table me-2"></i>Назва бази даних <span class="text-danger">*</span>
            </label>
            <input type="text" 
                   class="form-control" 
                   id="db_name" 
                   name="db_name" 
                   value="<?php echo htmlspecialchars($_SESSION['install_data']['db_config']['name'] ?? 'adboard_site'); ?>" 
                   placeholder="adboard_site"
                   pattern="[a-zA-Z0-9_]+"
                   required>
            <div class="invalid-feedback"></div>
            <small class="form-text text-muted">Тільки літери, цифри та підкреслення</small>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="db_user" class="form-label">
                <i class="fas fa-user me-2"></i>Користувач бази даних <span class="text-danger">*</span>
            </label>
            <input type="text" 
                   class="form-control" 
                   id="db_user" 
                   name="db_user" 
                   value="<?php echo htmlspecialchars($_SESSION['install_data']['db_config']['user'] ?? 'root'); ?>" 
                   placeholder="root"
                   required>
            <div class="invalid-feedback"></div>
            <small class="form-text text-muted">Користувач з правами створення БД</small>
        </div>
        
        <div class="col-md-6 mb-3">
            <label for="db_pass" class="form-label">
                <i class="fas fa-lock me-2"></i>Пароль
            </label>
            <div class="position-relative">
                <input type="password" 
                       class="form-control pe-5" 
                       id="db_pass" 
                       name="db_pass" 
                       value="<?php echo htmlspecialchars($_SESSION['install_data']['db_config']['pass'] ?? ''); ?>" 
                       placeholder="Введіть пароль">
                <button type="button" class="btn btn-outline-secondary password-toggle" 
                        style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); border: none; background: none; padding: 5px 10px;">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <div class="invalid-feedback"></div>
            <small class="form-text text-muted">Залиште порожнім якщо пароль не встановлений</small>
        </div>
    </div>
    
    <!-- Тест підключення -->
    <div class="test-connection">
        <div class="d-flex align-items-center mb-3">
            <button type="button" id="testConnection" class="btn btn-outline-primary">
                <i class="fas fa-plug me-2"></i>Тестувати підключення
            </button>
            <div class="ms-3">
                <small class="text-muted">Рекомендується перевірити підключення перед продовженням</small>
            </div>
        </div>
        
        <div id="connectionResult" class="connection-result"></div>
    </div>
    
    <!-- Додаткова інформація -->
    <div class="alert alert-info">
        <h6><i class="fas fa-info-circle me-2"></i>Важливо знати:</h6>
        <ul class="mb-0">
            <li>База даних буде створена автоматично якщо не існує</li>
            <li>Користувач повинен мати права на створення бази даних</li>
            <li>Усі існуючі дані в базі з такою назвою будуть видалені</li>
            <li>Використовується кодування UTF8MB4 для підтримки емодзі</li>
        </ul>
    </div>
    
    <div class="navigation-buttons">
        <a href="?step=2" class="btn btn-back">
            <i class="fas fa-arrow-left me-2"></i>Назад
        </a>
        <button type="submit" id="nextBtn" class="btn btn-next" disabled>
            Продовжити <i class="fas fa-arrow-right ms-2"></i>
        </button>
    </div>
</form>

<script>
$(document).ready(function() {
    // Тест підключення до БД
    $('#testConnection').click(function() {
        const $btn = $(this);
        const $result = $('#connectionResult');
        const originalText = $btn.html();
        
        // Валідація полів перед тестом
        const host = $('#db_host').val().trim();
        const user = $('#db_user').val().trim();
        
        if (!host || !user) {
            $result.removeClass('alert-success').addClass('alert-danger alert')
                   .html('<i class="fas fa-times me-2"></i>Заповніть хост та користувача для тестування')
                   .show();
            return;
        }
        
        // Показуємо стан завантаження
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Тестування...');
        $result.hide();
        
        // Отримуємо дані форми
        const formData = {
            test_connection: 1,
            db_host: host,
            db_user: user,
            db_pass: $('#db_pass').val(),
            db_name: $('#db_name').val()
        };
        
        // AJAX запит
        $.ajax({
            url: window.location.href,
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    let detailsHtml = '';
                    if (response.details) {
                        detailsHtml = `
                            <div class="mt-2 small">
                                <strong>Деталі:</strong>
                                <ul class="mb-0">
                                    <li>Версія MySQL: ${response.details.mysql_version}</li>
                                    <li>Кодування: ${response.details.charset}</li>
                                    <li>БД існує: ${response.details.database_exists ? 'Так' : 'Ні'}</li>
                                </ul>
                            </div>
                        `;
                    }
                    
                    $result.removeClass('alert-danger').addClass('alert-success alert')
                           .html('<i class="fas fa-check me-2"></i>' + response.message + detailsHtml)
                           .show();
                    // Активуємо кнопку "Далі"
                    $('#nextBtn').prop('disabled', false);
                } else {
                    let suggestionHtml = '';
                    if (response.suggestion) {
                        suggestionHtml = `<div class="mt-2"><strong>Рекомендація:</strong> ${response.suggestion}</div>`;
                    }
                    
                    $result.removeClass('alert-success').addClass('alert-danger alert')
                           .html('<i class="fas fa-times me-2"></i>' + response.message + suggestionHtml)
                           .show();
                    
                    // Додаємо кнопку для копіювання помилки
                    setTimeout(() => {
                        $result.append(`
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyError('${response.message}')">
                                    <i class="fas fa-copy me-1"></i>Копіювати помилку
                                </button>
                            </div>
                        `);
                    }, 100);
                }
            },
            error: function(xhr, status, error) {
                let errorMessage = 'Помилка тестування підключення';
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMessage = response.message || errorMessage;
                } catch (e) {
                    errorMessage = 'Помилка сервера: ' + error;
                }
                
                $result.removeClass('alert-success').addClass('alert-danger alert')
                       .html('<i class="fas fa-times me-2"></i>' + errorMessage)
                       .show();
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Валідація назви бази даних
    $('#db_name').on('input', function() {
        const value = $(this).val();
        const isValid = /^[a-zA-Z0-9_]*$/.test(value);
        
        if (!isValid && value) {
            $(this).addClass('is-invalid');
            $(this).siblings('.invalid-feedback').text('Тільки літери, цифри та підкреслення');
        } else {
            $(this).removeClass('is-invalid');
        }
    });
    
    // Автозаповнення назви БД
    $('#db_name').on('focus', function() {
        if (!$(this).val()) {
            $(this).val('adboard_site');
        }
    });
    
    // Валідація форми перед відправкою
    $('form').on('submit', function(e) {
        const $submitBtn = $(this).find('button[type="submit"]');
        const originalText = $submitBtn.html();
        let isValid = true;
        
        // Показуємо стан завантаження
        $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Обробка...');
        
        // Перевіряємо всі обов'язкові поля
        $(this).find('[required]').each(function() {
            if (!$(this).val().trim()) {
                $(this).addClass('is-invalid');
                $(this).siblings('.invalid-feedback').text('Це поле обов\'язкове');
                isValid = false;
            }
        });
        
        // Перевіряємо назву БД
        const dbName = $('#db_name').val();
        if (dbName && !/^[a-zA-Z0-9_]+$/.test(dbName)) {
            $('#db_name').addClass('is-invalid');
            $('#db_name').siblings('.invalid-feedback').text('Неприпустимі символи в назві БД');
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            
            // Скидаємо стан кнопки при помилці
            $submitBtn.prop('disabled', false).html(originalText);
            
            // Показуємо помилку
            const $alert = $('<div class="alert alert-danger mt-3"><i class="fas fa-exclamation-triangle me-2"></i>Виправте помилки у формі</div>');
            $('.navigation-buttons').before($alert);
            
            setTimeout(() => {
                $alert.remove();
            }, 5000);
        }
    });
    
    // Очищуємо валідацію при введенні
    $('.form-control').on('input', function() {
        $(this).removeClass('is-invalid');
    });
});

// Функція копіювання помилки
function copyError(errorText) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(errorText).then(() => {
            alert('Помилка скопійована в буфер обміну');
        });
    } else {
        // Fallback для старих браузерів
        const textArea = document.createElement('textarea');
        textArea.value = errorText;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('Помилка скопійована в буфер обміну');
    }
}
</script>
