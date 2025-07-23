<?php
// Крок 7: Процес встановлення

// Обробка POST запиту для фактичної установки
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'install') {
    header('Content-Type: application/json');
    
    try {
        // Отримуємо дані з сесії
        $dbConfig = $_SESSION['install_data']['db_config'] ?? [];
        $adminConfig = $_SESSION['install_data']['admin'] ?? [];
        $siteConfig = $_SESSION['install_data']['site'] ?? [];
        $additionalConfig = $_SESSION['install_data']['additional'] ?? [];
        $themeConfig = $_SESSION['install_data']['theme'] ?? [];
        
        if (empty($dbConfig) || empty($adminConfig)) {
            throw new Exception('Відсутні дані конфігурації');
        }
        
        // 1. Створення директорій
        $directories = [
            '../uploads',
            '../uploads/ads',
            '../uploads/users',
            '../uploads/temp',
            '../cache',
            '../logs'
        ];
        
        foreach ($directories as $dir) {
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
        }
        
        // 2. Створення конфігураційного файлу
        $configContent = "<?php
// AdBoard Pro Configuration
// Generated on " . date('Y-m-d H:i:s') . "

// Database Configuration
define('DB_HOST', '{$dbConfig['host']}');
define('DB_USER', '{$dbConfig['user']}');
define('DB_PASS', '{$dbConfig['pass']}');
define('DB_NAME', '{$dbConfig['name']}');

// Site Configuration
define('SITE_URL', '{$siteConfig['site_url']}');
define('SITE_NAME', '{$siteConfig['site_name']}');
define('SITE_EMAIL', '{$siteConfig['site_email']}');

// Security
define('SECRET_KEY', '" . bin2hex(random_bytes(32)) . "');
define('JWT_SECRET', '" . bin2hex(random_bytes(32)) . "');

// Environment
define('DEBUG_MODE', false);
define('MAINTENANCE_MODE', false);

// Upload settings
define('MAX_FILE_SIZE', 10485760); // 10MB
define('ALLOWED_EXTENSIONS', 'jpg,jpeg,png,gif,webp');

// Email settings (can be configured later)
define('SMTP_HOST', '');
define('SMTP_PORT', 587);
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('SMTP_SECURE', 'tls');

// Session settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 for HTTPS
?>";
        
        if (!file_put_contents('../core/config.php', $configContent)) {
            throw new Exception('Не вдалося створити файл конфігурації');
        }
        
        // 3. Підключення до БД та створення структури
        $mysqli = new mysqli($dbConfig['host'], $dbConfig['user'], $dbConfig['pass']);
        
        if ($mysqli->connect_error) {
            throw new Exception('Помилка підключення до БД: ' . $mysqli->connect_error);
        }
        
        // Створюємо базу даних якщо не існує
        if (!$mysqli->query("CREATE DATABASE IF NOT EXISTS `{$dbConfig['name']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
            throw new Exception('Не вдалося створити базу даних: ' . $mysqli->error);
        }
        
        $mysqli->select_db($dbConfig['name']);
        
        // 4. Імпорт структури БД
        $sqlFiles = [
            '../install/database.sql',
            '../install/ads_database.sql',
            '../install/admin_tables.sql'
        ];
        
        foreach ($sqlFiles as $sqlFile) {
            if (file_exists($sqlFile)) {
                $sql = file_get_contents($sqlFile);
                if ($sql) {
                    $mysqli->multi_query($sql);
                    while ($mysqli->next_result()) {
                        // Очищаємо результати
                    }
                }
            }
        }
        
        // 5. Створення адміністратора
        $adminPassword = password_hash($adminConfig['admin_password'], PASSWORD_DEFAULT);
        $stmt = $mysqli->prepare("
            INSERT INTO users (username, first_name, last_name, email, password, role, status, created_at, email_verified_at) 
            VALUES (?, ?, ?, ?, ?, 'admin', 'active', NOW(), NOW())
            ON DUPLICATE KEY UPDATE 
            username = VALUES(username), 
            first_name = VALUES(first_name),
            last_name = VALUES(last_name),
            email = VALUES(email), 
            password = VALUES(password)
        ");
        
        $stmt->bind_param("sssss", 
            $adminConfig['admin_login'], 
            $adminConfig['admin_first_name'] ?? 'Admin', 
            $adminConfig['admin_last_name'] ?? 'User',
            $adminConfig['admin_email'], 
            $adminPassword
        );
        if (!$stmt->execute()) {
            throw new Exception('Помилка створення адміністратора: ' . $stmt->error);
        }
        
        // 6. Додаємо початкові налаштування сайту
        $additionalConfig = $_SESSION['install_data']['additional'] ?? [];
        $themeConfig = $_SESSION['install_data']['theme'] ?? [];
        
        $settings = [
            ['site_name', $siteConfig['site_name']],
            ['site_url', $siteConfig['site_url']],
            ['site_email', $siteConfig['site_email']],
            ['site_description', $siteConfig['site_description'] ?? 'Сучасна дошка оголошень'],
            ['timezone', $additionalConfig['timezone'] ?? 'Europe/Kiev'],
            ['language', $additionalConfig['default_language'] ?? 'uk'],
            ['available_languages', '["uk","ru","en"]'],
            ['current_theme', $themeConfig['default_theme'] ?? 'light'],
            ['current_gradient', $themeConfig['default_gradient'] ?? 'gradient-1'],
            ['enable_animations', $additionalConfig['enable_animations'] ?? '1'],
            ['enable_particles', $additionalConfig['enable_particles'] ?? '0'],
            ['smooth_scroll', $additionalConfig['smooth_scroll'] ?? '1'],
            ['enable_tooltips', $additionalConfig['enable_tooltips'] ?? '1'],
            ['max_ad_duration_days', '30'],
            ['ads_per_page', '12'],
            ['auto_approve_ads', '0'],
            ['maintenance_mode', '0']
        ];
        
        $settingsStmt = $mysqli->prepare("
            INSERT INTO site_settings (setting_key, value) 
            VALUES (?, ?) 
            ON DUPLICATE KEY UPDATE value = VALUES(value)
        ");
        
        foreach ($settings as $setting) {
            $settingsStmt->bind_param("ss", $setting[0], $setting[1]);
            $settingsStmt->execute();
        }
        
        // 7. Створення файлу .installed
        if (!file_put_contents('../.installed', date('Y-m-d H:i:s'))) {
            throw new Exception('Не вдалося створити файл .installed');
        }
        
        $mysqli->close();
        
        // Очищаємо дані сесії установки
        unset($_SESSION['install_data']);
        
                    echo json_encode(['success' => true, 'message' => 'Установка завершена успішно']);
            exit();
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit();
        }
    }
}
?>

<div class="step-content animate__animated animate__fadeIn">
    <div class="step-header text-center">
        <h3><i class="fas fa-rocket me-3"></i>Встановлення AdBoard Pro</h3>
        <p class="text-muted">Будь ласка, зачекайте поки система налаштовується...</p>
    </div>

    <div class="installation-container">
        <!-- Основний прогрес -->
        <div class="main-progress mb-5">
            <div class="progress-circle">
                <div class="progress-inner">
                    <div class="progress-percentage" id="mainProgress">0%</div>
                </div>
                <svg class="progress-svg" width="200" height="200">
                    <circle class="progress-circle-bg" cx="100" cy="100" r="90"></circle>
                    <circle class="progress-circle-fill" cx="100" cy="100" r="90" id="progressCircle"></circle>
                </svg>
            </div>
            <h4 class="mt-3 mb-1 current-action" id="currentAction">Ініціалізація установки...</h4>
            <p class="text-muted current-description" id="currentDescription">Підготовка до встановлення системи</p>
        </div>

        <!-- Детальний прогрес -->
        <div class="installation-steps">
            <div class="step-item" id="step-1">
                <div class="step-icon">
                    <i class="fas fa-folder-plus"></i>
                </div>
                <div class="step-content">
                    <h6>Створення директорій</h6>
                    <p>Створення необхідних папок для файлів</p>
                    <div class="step-progress">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
                <div class="step-status">
                    <i class="fas fa-clock text-muted"></i>
                </div>
            </div>

            <div class="step-item" id="step-2">
                <div class="step-icon">
                    <i class="fas fa-database"></i>
                </div>
                <div class="step-content">
                    <h6>Створення бази даних</h6>
                    <p>Налаштування структури бази даних</p>
                    <div class="step-progress">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
                <div class="step-status">
                    <i class="fas fa-clock text-muted"></i>
                </div>
            </div>

            <div class="step-item" id="step-3">
                <div class="step-icon">
                    <i class="fas fa-table"></i>
                </div>
                <div class="step-content">
                    <h6>Імпорт даних</h6>
                    <p>Завантаження початкових даних</p>
                    <div class="step-progress">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
                <div class="step-status">
                    <i class="fas fa-clock text-muted"></i>
                </div>
            </div>

            <div class="step-item" id="step-4">
                <div class="step-icon">
                    <i class="fas fa-cogs"></i>
                </div>
                <div class="step-content">
                    <h6>Налаштування сайту</h6>
                    <p>Застосування параметрів сайту</p>
                    <div class="step-progress">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
                <div class="step-status">
                    <i class="fas fa-clock text-muted"></i>
                </div>
            </div>

            <div class="step-item" id="step-5">
                <div class="step-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="step-content">
                    <h6>Створення адміністратора</h6>
                    <p>Налаштування облікового запису адміна</p>
                    <div class="step-progress">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
                <div class="step-status">
                    <i class="fas fa-clock text-muted"></i>
                </div>
            </div>

            <div class="step-item" id="step-6">
                <div class="step-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="step-content">
                    <h6>Завершення установки</h6>
                    <p>Фіналізуємо та перевіряємо установку</p>
                    <div class="step-progress">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
                <div class="step-status">
                    <i class="fas fa-clock text-muted"></i>
                </div>
            </div>
        </div>

        <!-- Повідомлення про помилку -->
        <div class="alert alert-danger d-none animate__animated animate__shakeX" id="errorAlert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <span id="errorMessage"></span>
            <hr>
            <div class="mt-2">
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="location.reload()">
                    <i class="fas fa-redo me-1"></i>Спробувати знову
                </button>
            </div>
        </div>

        <!-- Лог установки -->
        <div class="installation-log mt-4">
            <h6><i class="fas fa-list-alt me-2"></i>Лог установки</h6>
            <div class="log-container" id="installLog">
                <div class="log-entry info">
                    <span class="log-time"><?php echo date('H:i:s'); ?></span>
                    <span class="log-text">Готовність до встановлення</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.installation-container {
    max-width: 800px;
    margin: 0 auto;
}

.main-progress {
    text-align: center;
}

.progress-circle {
    position: relative;
    width: 200px;
    height: 200px;
    margin: 0 auto;
}

.progress-inner {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 160px;
    height: 160px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.progress-percentage {
    color: white;
    font-size: 24px;
    font-weight: bold;
}

.progress-svg {
    position: absolute;
    top: 0;
    left: 0;
    transform: rotate(-90deg);
}

.progress-circle-bg {
    fill: none;
    stroke: #e9ecef;
    stroke-width: 8;
}

.progress-circle-fill {
    fill: none;
    stroke: #667eea;
    stroke-width: 8;
    stroke-linecap: round;
    stroke-dasharray: 565.48;
    stroke-dashoffset: 565.48;
    transition: stroke-dashoffset 0.5s ease;
}

.current-action {
    color: #495057;
    margin-bottom: 5px;
    transition: all 0.3s ease;
}

.current-description {
    margin-bottom: 0;
}

.installation-steps {
    background: #f8f9fa;
    border-radius: 15px;
    padding: 20px;
}

.step-item {
    display: flex;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.step-item:last-child {
    border-bottom: none;
}

.step-item.active {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    border-radius: 10px;
    margin: 0 -10px;
    padding: 15px 10px;
}

.step-item.completed {
    opacity: 0.7;
}

.step-icon {
    width: 50px;
    height: 50px;
    background: #e9ecef;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    font-size: 18px;
    color: #6c757d;
    transition: all 0.3s ease;
}

.step-item.active .step-icon {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    animation: pulse 2s infinite;
}

.step-item.completed .step-icon {
    background: #28a745;
    color: white;
}

.step-content {
    flex: 1;
}

.step-content h6 {
    margin-bottom: 5px;
    color: #495057;
}

.step-content p {
    margin-bottom: 8px;
    color: #6c757d;
    font-size: 0.9em;
}

.step-progress {
    width: 100%;
}

.step-progress .progress {
    height: 6px;
    background: #dee2e6;
    border-radius: 3px;
}

.step-progress .progress-bar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    transition: width 0.5s ease;
}

.step-status {
    width: 30px;
    text-align: center;
    font-size: 18px;
}

.step-item.active .step-status i {
    color: #667eea;
    animation: spin 2s linear infinite;
}

.step-item.completed .step-status i {
    color: #28a745;
}

.installation-log {
    background: #ffffff;
    border: 1px solid #dee2e6;
    border-radius: 10px;
    overflow: hidden;
}

.log-header {
    background: #f8f9fa;
    padding: 15px 20px;
    border-bottom: 1px solid #dee2e6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.log-content {
    max-height: 300px;
    overflow-y: auto;
}

.log-messages {
    padding: 15px;
    font-family: 'Courier New', monospace;
    font-size: 12px;
    line-height: 1.4;
}

.log-entry {
    margin-bottom: 5px;
    display: flex;
    align-items: center;
}

.log-time {
    color: #6c757d;
    margin-right: 10px;
    min-width: 60px;
}

.log-level {
    margin-right: 10px;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: bold;
    min-width: 40px;
    text-align: center;
}

.log-level.info {
    background: #d1ecf1;
    color: #0c5460;
}

.log-level.success {
    background: #d4edda;
    color: #155724;
}

.log-level.warning {
    background: #fff3cd;
    color: #856404;
}

.log-level.error {
    background: #f8d7da;
    color: #721c24;
}

.log-message {
    color: #495057;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-in {
    animation: slideInLeft 0.5s ease;
}

@keyframes slideInLeft {
    from {
        transform: translateX(-100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const errorAlert = document.getElementById('errorAlert');
    let isInstalling = false;
    let currentStep = 0;
    
    // Функції управління прогресом
    function updateProgress(step, percentage) {
        const stepElement = document.getElementById(`step-${step}`);
        if (stepElement) {
            const progressBar = stepElement.querySelector('.progress-bar');
            const statusIcon = stepElement.querySelector('.step-status i');
            
            progressBar.style.width = percentage + '%';
            
            if (percentage === 100) {
                statusIcon.className = 'fas fa-check-circle text-success';
                stepElement.classList.add('completed');
            } else if (percentage > 0) {
                statusIcon.className = 'fas fa-spinner fa-spin text-primary';
                stepElement.classList.add('active');
            }
        }
    }
    
    function updateMainProgress(percentage, action, description) {
        document.getElementById('mainProgress').textContent = percentage + '%';
        document.getElementById('currentAction').textContent = action;
        document.getElementById('currentDescription').textContent = description;
        
        const circumference = 2 * Math.PI * 90;
        const offset = circumference - (percentage / 100) * circumference;
        document.getElementById('progressCircle').style.strokeDasharray = circumference;
        document.getElementById('progressCircle').style.strokeDashoffset = offset;
    }
    
    function addLogEntry(message, type = 'info') {
        const logContainer = document.getElementById('installLog');
        const entry = document.createElement('div');
        entry.className = `log-entry ${type}`;
        entry.innerHTML = `
            <span class="log-time">${new Date().toLocaleTimeString()}</span>
            <span class="log-text">${message}</span>
        `;
        logContainer.appendChild(entry);
        logContainer.scrollTop = logContainer.scrollHeight;
    }
    
    function activateStep(step) {
        const stepElement = document.getElementById(`step-${step}`);
        if (stepElement) {
            stepElement.classList.add('active');
        }
    }
    
    // Основна функція установки
    async function startInstallation() {
        if (isInstalling) return;
        
        isInstalling = true;
        addLogEntry('Початок установки AdBoard Pro', 'info');
        
        try {
            // Відправляємо POST запит для фактичної установки
            const response = await fetch(window.location.href, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=install'
            });
            
            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.error || 'Невідома помилка установки');
            }
            
            // Симулюємо прогрес установки
            const steps = [
                {
                    action: 'Створення директорій',
                    description: 'Створюємо необхідні папки для файлів',
                    duration: 1000
                },
                {
                    action: 'Створення бази даних',
                    description: 'Налаштовуємо структуру бази даних',
                    duration: 2000
                },
                {
                    action: 'Імпорт даних',
                    description: 'Завантажуємо початкові дані',
                    duration: 2500
                },
                {
                    action: 'Налаштування сайту',
                    description: 'Застосовуємо параметри сайту',
                    duration: 1500
                },
                {
                    action: 'Створення адміністратора',
                    description: 'Налаштовуємо обліковий запис адміна',
                    duration: 1000
                },
                {
                    action: 'Завершення установки',
                    description: 'Фіналізуємо та перевіряємо установку',
                    duration: 1500
                }
            ];
            
            for (let i = 0; i < steps.length; i++) {
                currentStep = i + 1;
                const step = steps[i];
                
                activateStep(currentStep);
                addLogEntry(`Початок: ${step.action}`, 'info');
                
                // Оновлюємо загальний прогрес
                const overallProgress = Math.round(((i + 1) / steps.length) * 100);
                updateMainProgress(overallProgress, step.action, step.description);
                
                // Симулюємо прогрес кроку
                await simulateStepProgress(currentStep, step.duration);
                
                addLogEntry(`Завершено: ${step.action}`, 'success');
                updateProgress(currentStep, 100);
                
                // Пауза між кроками
                await new Promise(resolve => setTimeout(resolve, 500));
            }
            
            // Установка завершена
            addLogEntry('Установка успішно завершена!', 'success');
            updateMainProgress(100, 'Установка завершена!', 'AdBoard Pro готовий до використання');
            
            // Перенаправлення на наступний крок
            setTimeout(() => {
                window.location.href = '?step=8';
            }, 2000);
            
        } catch (error) {
            addLogEntry(`Помилка: ${error.message}`, 'error');
            showError(error.message);
        }
    }
    
    // Симуляція прогресу кроку
    async function simulateStepProgress(step, duration) {
        const intervals = 20;
        const delay = duration / intervals;
        
        for (let i = 0; i <= intervals; i++) {
            const percentage = Math.round((i / intervals) * 100);
            updateProgress(step, percentage);
            
            if (i < intervals) {
                await new Promise(resolve => setTimeout(resolve, delay));
            }
        }
    }
    
    // Показати помилку
    function showError(message) {
        document.getElementById('errorMessage').textContent = message;
        errorAlert.classList.remove('d-none');
        isInstalling = false;
    }
    
    // Автоматичний запуск установки
    setTimeout(startInstallation, 1000);
});
</script>