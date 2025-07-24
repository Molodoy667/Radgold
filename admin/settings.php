<?php
require_once '../core/config.php';
require_once '../core/functions.php';

// Перевірка прав доступу
if (!isLoggedIn() || !isAdmin()) {
    header('Location: login.php');
    exit();
}

$error = '';
$success = '';

// Обробка форми
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $settingsToUpdate = [];
        
        // Отримуємо всі POST дані та фільтруємо
        foreach ($_POST as $key => $value) {
            if ($key !== 'csrf_token' && $key !== 'action') {
                $settingsToUpdate[$key] = $value;
            }
        }
        
        // Перевірка CSRF токену
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception('Невірний CSRF токен');
        }
        
        $db = Database::getInstance();
        $updated = 0;
        
        foreach ($settingsToUpdate as $key => $value) {
            $stmt = $db->prepare("UPDATE site_settings SET setting_value = ?, updated_at = NOW() WHERE setting_key = ?");
            if ($stmt->execute([$value, $key])) {
                $updated++;
            }
        }
        
        $success = "Налаштування оновлено успішно! Змінено $updated параметрів.";
        
        // Логування
        logActivity($_SESSION['user_id'], 'settings_updated', 'Оновлено налаштування системи', [
            'updated_count' => $updated,
            'settings' => array_keys($settingsToUpdate)
        ]);
        
    } catch (Exception $e) {
        $error = $e->getMessage();
        error_log("Settings update error: " . $e->getMessage());
    }
}

// Отримуємо поточні налаштування
try {
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT setting_key, setting_value, setting_type, setting_group, description FROM site_settings ORDER BY setting_group, setting_key");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $settings = [];
    while ($row = $result->fetch_assoc()) {
        $settings[$row['setting_group']][] = $row;
    }
    
} catch (Exception $e) {
    $error = "Помилка завантаження налаштувань: " . $e->getMessage();
    $settings = [];
}

include 'header.php';
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-cogs me-2"></i>Налаштування системи
            </h1>
            <p class="text-muted mb-0">Конфігурація параметрів сайту та системи</p>
        </div>
        <div>
            <button type="button" class="btn btn-success" onclick="testEmailSettings()">
                <i class="fas fa-envelope me-1"></i>Тест Email
            </button>
            <button type="button" class="btn btn-info" onclick="clearCache()">
                <i class="fas fa-broom me-1"></i>Очистити кеш
            </button>
        </div>
    </div>

    <!-- Alerts -->
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form method="POST" id="settingsForm">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        
        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
            <?php 
            $tabIndex = 0;
            $groupLabels = [
                'general' => 'Основні',
                'email' => 'Email',
                'payments' => 'Платежі',
                'security' => 'Безпека',
                'theme' => 'Тема',
                'ads' => 'Оголошення',
                'social' => 'Соцмережі',
                'analytics' => 'Аналітика',
                'system' => 'Система'
            ];
            ?>
            <?php foreach ($groupLabels as $groupKey => $groupLabel): ?>
                <?php if (isset($settings[$groupKey])): ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?php echo $tabIndex === 0 ? 'active' : ''; ?>" 
                                id="<?php echo $groupKey; ?>-tab" 
                                data-bs-toggle="tab" 
                                data-bs-target="#<?php echo $groupKey; ?>" 
                                type="button" role="tab">
                            <?php echo getGroupIcon($groupKey); ?>
                            <?php echo $groupLabel; ?>
                        </button>
                    </li>
                    <?php $tabIndex++; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="settingsTabContent">
            <?php 
            $tabIndex = 0;
            foreach ($groupLabels as $groupKey => $groupLabel): 
                if (!isset($settings[$groupKey])) continue;
            ?>
                <div class="tab-pane fade <?php echo $tabIndex === 0 ? 'show active' : ''; ?>" 
                     id="<?php echo $groupKey; ?>" 
                     role="tabpanel">
                    
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <?php echo getGroupIcon($groupKey); ?>
                                <?php echo $groupLabel; ?> налаштування
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php foreach ($settings[$groupKey] as $setting): ?>
                                    <div class="col-md-6 mb-3">
                                        <?php renderSettingField($setting); ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php $tabIndex++; ?>
            <?php endforeach; ?>
        </div>

        <!-- Submit Button -->
        <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-secondary" onclick="resetForm()">
                <i class="fas fa-undo me-1"></i>Скинути
            </button>
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-save me-2"></i>Зберегти налаштування
            </button>
        </div>
    </form>
</div>

<!-- Test Email Modal -->
<div class="modal fade" id="emailTestModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-envelope me-2"></i>Тест Email налаштувань
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="emailTestForm">
                    <div class="mb-3">
                        <label class="form-label">Email отримувача</label>
                        <input type="email" class="form-control" id="testEmail" required
                               value="<?php echo $_SESSION['user_email'] ?? ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Тема листа</label>
                        <input type="text" class="form-control" id="testSubject" 
                               value="Тест Email - AdBoard Pro">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Повідомлення</label>
                        <textarea class="form-control" id="testMessage" rows="3">Це тестове повідомлення для перевірки налаштувань SMTP.</textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
                <button type="button" class="btn btn-primary" onclick="sendTestEmail()">
                    <i class="fas fa-paper-plane me-1"></i>Відправити
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Форма налаштувань
document.getElementById('settingsForm').addEventListener('submit', function(e) {
    // Показуємо індикатор завантаження
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Збереження...';
    submitBtn.disabled = true;
    
    // Дозволяємо відправку форми
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 2000);
});

// Тест Email
function testEmailSettings() {
    const modal = new bootstrap.Modal(document.getElementById('emailTestModal'));
    modal.show();
}

function sendTestEmail() {
    const email = document.getElementById('testEmail').value;
    const subject = document.getElementById('testSubject').value;
    const message = document.getElementById('testMessage').value;
    
    if (!email || !subject || !message) {
        alert('Заповніть всі поля');
        return;
    }
    
    fetch('ajax/test_email.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            email: email,
            subject: subject,
            message: message
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Email відправлено успішно!');
            bootstrap.Modal.getInstance(document.getElementById('emailTestModal')).hide();
        } else {
            alert('Помилка відправки: ' + data.message);
        }
    })
    .catch(error => {
        alert('Помилка: ' + error.message);
    });
}

// Очистка кешу
function clearCache() {
    if (confirm('Ви впевнені що хочете очистити кеш?')) {
        fetch('ajax/clear_cache.php', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Кеш очищено успішно!');
            } else {
                alert('Помилка очистки кешу: ' + data.message);
            }
        });
    }
}

// Скидання форми
function resetForm() {
    if (confirm('Скинути всі зміни?')) {
        location.reload();
    }
}
</script>

<?php
include 'footer.php';

// Функції для рендерингу
function getGroupIcon($group) {
    $icons = [
        'general' => '<i class="fas fa-cog me-2"></i>',
        'email' => '<i class="fas fa-envelope me-2"></i>',
        'payments' => '<i class="fas fa-credit-card me-2"></i>',
        'security' => '<i class="fas fa-shield-alt me-2"></i>',
        'theme' => '<i class="fas fa-palette me-2"></i>',
        'ads' => '<i class="fas fa-bullhorn me-2"></i>',
        'social' => '<i class="fas fa-share-alt me-2"></i>',
        'analytics' => '<i class="fas fa-chart-bar me-2"></i>',
        'system' => '<i class="fas fa-server me-2"></i>'
    ];
    return $icons[$group] ?? '<i class="fas fa-cog me-2"></i>';
}

function renderSettingField($setting) {
    $key = $setting['setting_key'];
    $value = $setting['setting_value'];
    $type = $setting['setting_type'];
    $description = $setting['description'];
    
    echo '<div class="setting-field">';
    echo '<label for="' . $key . '" class="form-label fw-bold">' . ucfirst(str_replace('_', ' ', $key)) . '</label>';
    
    switch ($type) {
        case 'bool':
            echo '<div class="form-check form-switch">';
            echo '<input class="form-check-input" type="checkbox" id="' . $key . '" name="' . $key . '" value="1"' . ($value ? ' checked' : '') . '>';
            echo '<label class="form-check-label" for="' . $key . '">' . ($value ? 'Увімкнено' : 'Вимкнено') . '</label>';
            echo '</div>';
            break;
            
        case 'textarea':
        case 'text':
            echo '<textarea class="form-control" id="' . $key . '" name="' . $key . '" rows="3">' . htmlspecialchars($value) . '</textarea>';
            break;
            
        case 'email':
            echo '<input type="email" class="form-control" id="' . $key . '" name="' . $key . '" value="' . htmlspecialchars($value) . '">';
            break;
            
        case 'url':
            echo '<input type="url" class="form-control" id="' . $key . '" name="' . $key . '" value="' . htmlspecialchars($value) . '">';
            break;
            
        case 'int':
            echo '<input type="number" class="form-control" id="' . $key . '" name="' . $key . '" value="' . htmlspecialchars($value) . '">';
            break;
            
        default:
            $inputType = (strpos($key, 'password') !== false) ? 'password' : 'text';
            echo '<input type="' . $inputType . '" class="form-control" id="' . $key . '" name="' . $key . '" value="' . htmlspecialchars($value) . '">';
            break;
    }
    
    if ($description) {
        echo '<div class="form-text">' . htmlspecialchars($description) . '</div>';
    }
    echo '</div>';
}
?>