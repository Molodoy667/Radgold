<div class="text-center mb-4">
    <i class="fas fa-user-shield fa-4x text-primary mb-3"></i>
    <p class="lead">Створення облікового запису адміністратора та завершення встановлення.</p>
</div>

<div class="row">
    <div class="col-md-8">
        <form id="adminForm">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user-cog me-2"></i>Дані адміністратора</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="admin_username" class="form-label">
                            <i class="fas fa-user me-1"></i>Ім'я користувача *
                        </label>
                        <input type="text" class="form-control" id="admin_username" name="admin_username" 
                               value="admin" required>
                        <div class="form-text">Використовується для входу в адмін панель</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="admin_email" class="form-label">
                            <i class="fas fa-envelope me-1"></i>Email адреса *
                        </label>
                        <input type="email" class="form-control" id="admin_email" name="admin_email" 
                               placeholder="admin@example.com" required>
                        <div class="form-text">Використовується для відновлення пароля</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="admin_password" class="form-label">
                            <i class="fas fa-lock me-1"></i>Пароль *
                        </label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="admin_password" name="admin_password" 
                                   minlength="6" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="toggleAdminPassword()">
                                <i class="fas fa-eye" id="adminPasswordIcon"></i>
                            </button>
                        </div>
                        <div class="form-text">Мінімум 6 символів</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_admin_password" class="form-label">
                            <i class="fas fa-lock me-1"></i>Підтвердження пароля *
                        </label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="confirm_admin_password" 
                                   name="confirm_admin_password" minlength="6" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="toggleConfirmPassword()">
                                <i class="fas fa-eye" id="confirmPasswordIcon"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Фінальний крок</h6>
            </div>
            <div class="card-body">
                <h6>Що відбудеться:</h6>
                <ol class="small">
                    <li>Створення конфігурації БД</li>
                    <li>Імпорт структури БД</li>
                    <li>Створення адміністратора</li>
                    <li>Блокування інсталятора</li>
                </ol>
                
                <div class="alert alert-success small mt-3">
                    <i class="fas fa-check-circle me-1"></i>
                    Після встановлення ви будете перенаправлені в адмін панель
                </div>
                
                <div class="alert alert-warning small">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    <strong>Важливо:</strong> Видаліть папку install/ після встановлення!
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-database me-2"></i>База даних</h6>
            </div>
            <div class="card-body">
                <small>
                    <strong>Хост:</strong> <?php echo $_SESSION['db_config']['db_host'] ?? 'Не налаштовано'; ?><br>
                    <strong>База:</strong> <?php echo $_SESSION['db_config']['db_name'] ?? 'Не налаштовано'; ?><br>
                    <strong>Користувач:</strong> <?php echo $_SESSION['db_config']['db_username'] ?? 'Не налаштовано'; ?>
                </small>
            </div>
        </div>
    </div>
</div>

<div id="installationResult" class="mt-4" style="display: none;"></div>

<div class="mt-4">
    <div class="d-flex justify-content-between">
        <a href="?step=3" class="btn btn-secondary btn-lg">
            <i class="fas fa-arrow-left me-2"></i>Назад
        </a>
        <button type="button" class="btn btn-success btn-lg" onclick="startInstallation()">
            <i class="fas fa-rocket me-2"></i>Завершити встановлення
        </button>
    </div>
</div>

<script>
function toggleAdminPassword() {
    const passwordInput = document.getElementById('admin_password');
    const passwordIcon = document.getElementById('adminPasswordIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        passwordIcon.className = 'fas fa-eye-slash';
    } else {
        passwordInput.type = 'password';
        passwordIcon.className = 'fas fa-eye';
    }
}

function toggleConfirmPassword() {
    const passwordInput = document.getElementById('confirm_admin_password');
    const passwordIcon = document.getElementById('confirmPasswordIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        passwordIcon.className = 'fas fa-eye-slash';
    } else {
        passwordInput.type = 'password';
        passwordIcon.className = 'fas fa-eye';
    }
}

// Валідація паролів
document.getElementById('confirm_admin_password').addEventListener('input', function() {
    const password = document.getElementById('admin_password').value;
    const confirmPassword = this.value;
    
    if (password !== confirmPassword) {
        this.setCustomValidity('Паролі не співпадають');
        this.classList.add('is-invalid');
    } else {
        this.setCustomValidity('');
        this.classList.remove('is-invalid');
    }
});

function startInstallation() {
    const form = document.getElementById('adminForm');
    
    // Перевірка валідності форми
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }
    
    // Перевірка співпадіння паролів
    const password = document.getElementById('admin_password').value;
    const confirmPassword = document.getElementById('confirm_admin_password').value;
    
    if (password !== confirmPassword) {
        alert('Паролі не співпадають!');
        return;
    }
    
    if (password.length < 6) {
        alert('Пароль повинен містити мінімум 6 символів!');
        return;
    }
    
    // Показуємо прогрес
    const resultDiv = document.getElementById('installationResult');
    resultDiv.style.display = 'block';
    resultDiv.innerHTML = `
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="fas fa-cog fa-spin me-2"></i>Встановлення системи...
                </h5>
                <div class="progress mb-3">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                         role="progressbar" style="width: 0%"></div>
                </div>
                <div id="installProgress">Підготовка до встановлення...</div>
            </div>
        </div>
    `;
    
    // Симуляція прогресу
    let progress = 0;
    const progressBar = document.querySelector('.progress-bar');
    const progressText = document.getElementById('installProgress');
    
    const steps = [
        'Створення конфігурації бази даних...',
        'Підключення до бази даних...',
        'Створення структури таблиць...',
        'Імпорт початкових даних...',
        'Створення адміністратора...',
        'Блокування інсталятора...'
    ];
    
    let stepIndex = 0;
    const progressInterval = setInterval(() => {
        progress += 20;
        progressBar.style.width = progress + '%';
        
        if (stepIndex < steps.length) {
            progressText.textContent = steps[stepIndex];
            stepIndex++;
        }
        
        if (progress >= 100) {
            clearInterval(progressInterval);
            // Виконуємо фактичне встановлення
            performInstallation();
        }
    }, 800);
}

function performInstallation() {
    const form = document.getElementById('adminForm');
    const formData = new FormData(form);
    formData.append('install', '1');
    
    fetch('', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text();
    })
    .then(text => {
        try {
            return JSON.parse(text);
        } catch (e) {
            console.error('Response text:', text);
            throw new Error('Invalid JSON response');
        }
    })
    .then(data => {
        const resultDiv = document.getElementById('installationResult');
        
        if (data.success) {
            resultDiv.innerHTML = `
                <div class="card border-success">
                    <div class="card-body">
                        <h5 class="card-title text-success">
                            <i class="fas fa-check-circle me-2"></i>Встановлення завершено успішно!
                        </h5>
                        <p class="card-text">${data.message}</p>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Важливо:</strong> Не забудьте видалити папку <code>install/</code> з сервера!
                        </div>
                        
                        <div class="text-center">
                            <a href="${data.redirect}" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Перейти в адмін панель
                            </a>
                        </div>
                    </div>
                </div>
            `;
            
            // Автоматичне перенаправлення через 5 секунд
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 5000);
            
        } else {
            resultDiv.innerHTML = `
                <div class="card border-danger">
                    <div class="card-body">
                        <h5 class="card-title text-danger">
                            <i class="fas fa-times-circle me-2"></i>Помилка встановлення
                        </h5>
                        <p class="card-text text-danger">${data.message}</p>
                        <button type="button" class="btn btn-warning" onclick="startInstallation()">
                            <i class="fas fa-redo me-2"></i>Спробувати знову
                        </button>
                    </div>
                </div>
            `;
        }
    })
    .catch(error => {
        const resultDiv = document.getElementById('installationResult');
        resultDiv.innerHTML = `
            <div class="card border-danger">
                <div class="card-body">
                    <h5 class="card-title text-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>Помилка з'єднання
                    </h5>
                    <p class="card-text">Не вдалося зв'язатися з сервером. Перевірте підключення.</p>
                    <button type="button" class="btn btn-warning" onclick="startInstallation()">
                        <i class="fas fa-redo me-2"></i>Спробувати знову
                    </button>
                </div>
            </div>
        `;
    });
}
</script>