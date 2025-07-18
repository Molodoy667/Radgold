<div class="text-center mb-4">
    <i class="fas fa-database fa-4x text-primary mb-3"></i>
    <p class="lead">Налаштування підключення до бази даних MySQL.</p>
</div>

<div class="row">
    <div class="col-md-8">
        <form id="dbForm">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-server me-2"></i>Параметри підключення</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="db_host" class="form-label">
                            <i class="fas fa-globe me-1"></i>Хост бази даних
                        </label>
                        <input type="text" class="form-control" id="db_host" name="db_host" 
                               value="<?php echo $_SESSION['db_config']['db_host'] ?? 'localhost'; ?>" required>
                        <div class="form-text">Зазвичай localhost або IP адреса сервера</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="db_name" class="form-label">
                            <i class="fas fa-database me-1"></i>Назва бази даних
                        </label>
                        <input type="text" class="form-control" id="db_name" name="db_name" 
                               value="<?php echo $_SESSION['db_config']['db_name'] ?? 'classifieds_board'; ?>" required>
                        <div class="form-text">База даних буде створена автоматично, якщо не існує</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="db_username" class="form-label">
                            <i class="fas fa-user me-1"></i>Ім'я користувача
                        </label>
                        <input type="text" class="form-control" id="db_username" name="db_username" 
                               value="<?php echo $_SESSION['db_config']['db_username'] ?? 'root'; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="db_password" class="form-label">
                            <i class="fas fa-lock me-1"></i>Пароль
                        </label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="db_password" name="db_password" 
                                   value="<?php echo $_SESSION['db_config']['db_password'] ?? ''; ?>">
                            <button class="btn btn-outline-secondary" type="button" onclick="toggleDbPassword()">
                                <i class="fas fa-eye" id="passwordIcon"></i>
                            </button>
                        </div>
                        <div class="form-text">Залиште порожнім, якщо пароль не встановлено</div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary" onclick="testConnection()">
                            <i class="fas fa-plug me-2"></i>Перевірити підключення
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Інформація</h6>
            </div>
            <div class="card-body">
                <h6>Вимоги до MySQL:</h6>
                <ul class="small">
                    <li>MySQL 5.7 або вище</li>
                    <li>MariaDB 10.2 або вище</li>
                    <li>Права на створення бази даних</li>
                    <li>Права на створення таблиць</li>
                </ul>
                
                <h6 class="mt-3">Що буде створено:</h6>
                <ul class="small">
                    <li>Таблиці системи</li>
                    <li>Початкові дані</li>
                    <li>Категорії оголошень</li>
                    <li>Налаштування системи</li>
                </ul>
                
                <div class="alert alert-warning small mt-3">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    <strong>Увага:</strong> Якщо база даних існує, всі дані будуть замінені!
                </div>
            </div>
        </div>
    </div>
</div>

<div id="connectionResult" class="mt-4" style="display: none;"></div>

<form method="POST" class="mt-4" id="saveForm" style="display: none;">
    <input type="hidden" name="db_host" id="save_db_host">
    <input type="hidden" name="db_name" id="save_db_name">
    <input type="hidden" name="db_username" id="save_db_username">
    <input type="hidden" name="db_password" id="save_db_password">
    
    <div class="d-flex justify-content-between">
        <a href="?step=2" class="btn btn-secondary btn-lg">
            <i class="fas fa-arrow-left me-2"></i>Назад
        </a>
        <button type="submit" name="save_db" class="btn btn-primary btn-lg">
            <i class="fas fa-arrow-right me-2"></i>Далі
        </button>
    </div>
</form>

<script>
function toggleDbPassword() {
    const passwordInput = document.getElementById('db_password');
    const passwordIcon = document.getElementById('passwordIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        passwordIcon.className = 'fas fa-eye-slash';
    } else {
        passwordInput.type = 'password';
        passwordIcon.className = 'fas fa-eye';
    }
}

function testConnection() {
    const form = document.getElementById('dbForm');
    const formData = new FormData(form);
    formData.append('test_db', '1');
    
    // Показуємо індикатор завантаження
    const resultDiv = document.getElementById('connectionResult');
    resultDiv.style.display = 'block';
    resultDiv.innerHTML = `
        <div class="alert alert-info">
            <i class="fas fa-spinner fa-spin me-2"></i>
            Перевірка підключення до бази даних...
        </div>
    `;
    
    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resultDiv.innerHTML = `
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    ${data.message}
                </div>
            `;
            
            // Показуємо форму для продовження
            document.getElementById('saveForm').style.display = 'block';
            
            // Копіюємо дані в приховані поля
            document.getElementById('save_db_host').value = document.getElementById('db_host').value;
            document.getElementById('save_db_name').value = document.getElementById('db_name').value;
            document.getElementById('save_db_username').value = document.getElementById('db_username').value;
            document.getElementById('save_db_password').value = document.getElementById('db_password').value;
            
        } else {
            resultDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle me-2"></i>
                    ${data.message}
                </div>
            `;
            document.getElementById('saveForm').style.display = 'none';
        }
    })
    .catch(error => {
        resultDiv.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Помилка з'єднання з сервером
            </div>
        `;
        document.getElementById('saveForm').style.display = 'none';
    });
}

// Показуємо форму, якщо підключення вже перевірено
<?php if (isset($_SESSION['db_tested']) && $_SESSION['db_tested']): ?>
document.getElementById('saveForm').style.display = 'block';
document.getElementById('connectionResult').style.display = 'block';
document.getElementById('connectionResult').innerHTML = `
    <div class="alert alert-success">
        <i class="fas fa-check-circle me-2"></i>
        Підключення до бази даних успішне!
    </div>
`;
// Копіюємо дані в приховані поля
document.getElementById('save_db_host').value = document.getElementById('db_host').value;
document.getElementById('save_db_name').value = document.getElementById('db_name').value;
document.getElementById('save_db_username').value = document.getElementById('db_username').value;
document.getElementById('save_db_password').value = document.getElementById('db_password').value;
<?php endif; ?>
</script>