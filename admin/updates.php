<?php
require_once '../core/config.php';
require_once '../core/functions.php';

// Перевірка авторизації адміністратора
if (!isAdmin()) {
    header('Location: login.php');
    exit();
}

$error = '';
$success = '';

// Обробка завантаження оновлення
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['upload_update'])) {
        if (isset($_FILES['update_file']) && $_FILES['update_file']['error'] === UPLOAD_ERR_OK) {
            try {
                $result = uploadUpdate($_FILES['update_file']);
                if ($result['success']) {
                    $success = 'Оновлення успішно завантажене та встановлене!';
                } else {
                    $error = $result['message'];
                }
            } catch (Exception $e) {
                $error = 'Помилка завантаження: ' . $e->getMessage();
            }
        } else {
            $error = 'Оберіть файл оновлення для завантаження.';
        }
    }
    
    if (isset($_POST['delete_update'])) {
        $updateId = (int)$_POST['update_id'];
        if (deleteUpdate($updateId)) {
            $success = 'Оновлення видалено.';
        } else {
            $error = 'Помилка видалення оновлення.';
        }
    }
}

// Отримання списку оновлень
$updates = getInstalledUpdates();

include 'header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Встановлення оновлень</h1>
            <p class="text-muted">Управління оновленнями системи</p>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger animate__animated animate__shakeX">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success animate__animated animate__bounceIn">
            <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Форма завантаження оновлення -->
        <div class="col-md-6">
            <div class="card update-upload-card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-upload me-2"></i>Завантажити оновлення
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" id="updateForm">
                        <div class="mb-3">
                            <label for="update_file" class="form-label">Файл оновлення (.zip)</label>
                            <input type="file" class="form-control" id="update_file" name="update_file" 
                                   accept=".zip" required>
                            <div class="form-text">
                                Максимальний розмір файлу: <?php echo ini_get('upload_max_filesize'); ?>
                            </div>
                        </div>
                        
                        <div class="upload-progress" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">Прогрес встановлення</small>
                                <span class="progress-percentage">0%</span>
                            </div>
                            <div class="progress mb-3">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                     role="progressbar" style="width: 0%"></div>
                            </div>
                            <div class="progress-status">
                                <small class="text-muted">Очікування...</small>
                            </div>
                        </div>
                        
                        <div class="installation-log" style="display: none;">
                            <h6>Лог встановлення:</h6>
                            <div class="log-container">
                                <textarea class="form-control" rows="10" readonly id="installLog"></textarea>
                            </div>
                        </div>
                        
                        <button type="submit" name="upload_update" class="btn btn-primary" id="uploadBtn">
                            <i class="fas fa-upload me-2"></i>Завантажити та встановити
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Інформація про поточну версію -->
        <div class="col-md-6">
            <div class="card system-info-card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Інформація про систему
                    </h5>
                </div>
                <div class="card-body">
                    <div class="system-info">
                        <div class="info-item">
                            <strong>Поточна версія:</strong>
                            <span class="version">v1.0.0</span>
                        </div>
                        <div class="info-item">
                            <strong>Дата останнього оновлення:</strong>
                            <span><?php echo getLastUpdateDate(); ?></span>
                        </div>
                        <div class="info-item">
                            <strong>PHP версія:</strong>
                            <span><?php echo PHP_VERSION; ?></span>
                        </div>
                        <div class="info-item">
                            <strong>MySQL версія:</strong>
                            <span><?php echo getMySQLVersion(); ?></span>
                        </div>
                        <div class="info-item">
                            <strong>Вільне місце:</strong>
                            <span><?php echo formatBytes(disk_free_space('.')); ?></span>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning mt-3">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Важливо:</h6>
                        <ul class="mb-0">
                            <li>Обов'язково створіть бекап перед оновленням</li>
                            <li>Оновлення містить файли та зміни бази даних</li>
                            <li>Процес може зайняти кілька хвилин</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Список встановлених оновлень -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card updates-history-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>Історія оновлень
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($updates)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Оновлення ще не встановлювалися</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Назва</th>
                                        <th>Версія</th>
                                        <th>Дата встановлення</th>
                                        <th>Розмір</th>
                                        <th>Статус</th>
                                        <th>Дії</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($updates as $update): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($update['name']); ?></strong>
                                                <br>
                                                <small class="text-muted"><?php echo htmlspecialchars($update['description']); ?></small>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary"><?php echo htmlspecialchars($update['version']); ?></span>
                                            </td>
                                            <td><?php echo date('d.m.Y H:i', strtotime($update['installed_at'])); ?></td>
                                            <td><?php echo formatBytes($update['file_size']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $update['status'] === 'success' ? 'success' : 'danger'; ?>">
                                                    <?php echo $update['status'] === 'success' ? 'Успішно' : 'Помилка'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-info" 
                                                            onclick="viewUpdateDetails(<?php echo $update['id']; ?>)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-primary" 
                                                            onclick="viewUpdateLog(<?php echo $update['id']; ?>)">
                                                        <i class="fas fa-file-alt"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger" 
                                                            onclick="deleteUpdateConfirm(<?php echo $update['id']; ?>, '<?php echo htmlspecialchars($update['name']); ?>')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальне вікно деталей оновлення -->
<div class="modal fade" id="updateDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Деталі оновлення</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="updateDetailsContent">
                <!-- Завантажується через AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Модальне вікно лога оновлення -->
<div class="modal fade" id="updateLogModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Лог встановлення</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <pre id="updateLogContent" class="log-content"></pre>
            </div>
        </div>
    </div>
</div>

<!-- Форма видалення (приховано) -->
<form method="POST" id="deleteForm" style="display: none;">
    <input type="hidden" name="delete_update" value="1">
    <input type="hidden" name="update_id" id="deleteUpdateId">
</form>

<style>
.update-upload-card, .system-info-card, .updates-history-card {
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.update-upload-card:hover, .system-info-card:hover, .updates-history-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.system-info .info-item {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid #eee;
}

.system-info .info-item:last-child {
    border-bottom: none;
}

.version {
    font-weight: bold;
    color: #28a745;
}

.log-container {
    max-height: 300px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.log-content {
    background: #f8f9fa;
    border: none;
    max-height: 400px;
    overflow-y: auto;
    font-family: 'Courier New', monospace;
    font-size: 0.85rem;
}

.progress-bar {
    transition: width 0.3s ease;
}

.installation-log textarea {
    background: #2d3748;
    color: #e2e8f0;
    border: none;
    font-family: 'Courier New', monospace;
    font-size: 0.8rem;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.7; }
    100% { opacity: 1; }
}

.progress-bar-animated {
    animation: pulse 1s infinite;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const updateForm = document.getElementById('updateForm');
    const uploadBtn = document.getElementById('uploadBtn');
    const progressContainer = document.querySelector('.upload-progress');
    const progressBar = document.querySelector('.progress-bar');
    const progressPercentage = document.querySelector('.progress-percentage');
    const progressStatus = document.querySelector('.progress-status');
    const logContainer = document.querySelector('.installation-log');
    const installLog = document.getElementById('installLog');
    
    updateForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const fileInput = document.getElementById('update_file');
        if (!fileInput.files[0]) {
            alert('Оберіть файл оновлення');
            return;
        }
        
        const formData = new FormData(updateForm);
        
        // Показуємо прогрес
        uploadBtn.disabled = true;
        progressContainer.style.display = 'block';
        logContainer.style.display = 'block';
        
        // Симуляція прогресу (в реальному проекті використовуйте WebSocket або Server-Sent Events)
        simulateInstallation();
        
        // AJAX завантаження
        fetch('update_handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess(data.message);
                setTimeout(() => location.reload(), 2000);
            } else {
                showError(data.message);
            }
        })
        .catch(error => {
            showError('Помилка завантаження: ' + error.message);
        })
        .finally(() => {
            uploadBtn.disabled = false;
        });
    });
    
    function simulateInstallation() {
        const steps = [
            { progress: 10, status: 'Завантаження файлу...' },
            { progress: 25, status: 'Перевірка архіву...' },
            { progress: 40, status: 'Розпакування файлів...' },
            { progress: 60, status: 'Копіювання файлів...' },
            { progress: 75, status: 'Імпорт змін бази даних...' },
            { progress: 90, status: 'Завершення встановлення...' },
            { progress: 100, status: 'Готово!' }
        ];
        
        let currentStep = 0;
        
        function nextStep() {
            if (currentStep < steps.length) {
                const step = steps[currentStep];
                updateProgress(step.progress, step.status);
                
                // Додаємо до лога
                appendToLog(`[${new Date().toLocaleTimeString()}] ${step.status}`);
                
                currentStep++;
                setTimeout(nextStep, 1000 + Math.random() * 1000);
            }
        }
        
        nextStep();
    }
    
    function updateProgress(percentage, status) {
        progressBar.style.width = percentage + '%';
        progressPercentage.textContent = percentage + '%';
        progressStatus.innerHTML = `<small class="text-muted">${status}</small>`;
    }
    
    function appendToLog(message) {
        installLog.value += message + '\n';
        installLog.scrollTop = installLog.scrollHeight;
    }
    
    function showSuccess(message) {
        const alert = document.createElement('div');
        alert.className = 'alert alert-success animate__animated animate__bounceIn';
        alert.innerHTML = `<i class="fas fa-check-circle me-2"></i>${message}`;
        document.querySelector('.container-fluid').insertBefore(alert, document.querySelector('.row'));
        setTimeout(() => alert.remove(), 5000);
    }
    
    function showError(message) {
        const alert = document.createElement('div');
        alert.className = 'alert alert-danger animate__animated animate__shakeX';
        alert.innerHTML = `<i class="fas fa-exclamation-circle me-2"></i>${message}`;
        document.querySelector('.container-fluid').insertBefore(alert, document.querySelector('.row'));
        setTimeout(() => alert.remove(), 5000);
    }
});

function viewUpdateDetails(updateId) {
    fetch(`update_details.php?id=${updateId}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('updateDetailsContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('updateDetailsModal')).show();
        });
}

function viewUpdateLog(updateId) {
    fetch(`update_log.php?id=${updateId}`)
        .then(response => response.text())
        .then(log => {
            document.getElementById('updateLogContent').textContent = log;
            new bootstrap.Modal(document.getElementById('updateLogModal')).show();
        });
}

function deleteUpdateConfirm(updateId, updateName) {
    if (confirm(`Видалити оновлення "${updateName}"?`)) {
        document.getElementById('deleteUpdateId').value = updateId;
        document.getElementById('deleteForm').submit();
    }
}
</script>

<?php
include 'footer.php';

// Функції для роботи з оновленнями
function uploadUpdate($file) {
    // Логіка завантаження та встановлення оновлення
    // Буде реалізована в окремому файлі
    return ['success' => true, 'message' => 'Оновлення завантажене'];
}

function getInstalledUpdates() {
    try {
        $db = new Database();
        $result = $db->query("
            SELECT * FROM system_updates 
            ORDER BY installed_at DESC
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function deleteUpdate($updateId) {
    try {
        $db = new Database();
        $stmt = $db->prepare("DELETE FROM system_updates WHERE id = ?");
        $stmt->bind_param("i", $updateId);
        return $stmt->execute();
    } catch (Exception $e) {
        return false;
    }
}

function getLastUpdateDate() {
    try {
        $db = new Database();
        $result = $db->query("
            SELECT installed_at FROM system_updates 
            WHERE status = 'success' 
            ORDER BY installed_at DESC 
            LIMIT 1
        ");
        $row = $result->fetch_assoc();
        return $row ? date('d.m.Y H:i', strtotime($row['installed_at'])) : 'Ніколи';
    } catch (Exception $e) {
        return 'Невідомо';
    }
}

function getMySQLVersion() {
    try {
        $db = new Database();
        $result = $db->query("SELECT VERSION() as version");
        $row = $result->fetch_assoc();
        return $row['version'];
    } catch (Exception $e) {
        return 'Невідомо';
    }
}

function formatBytes($size, $precision = 2) {
    $base = log($size, 1024);
    $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');
    return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
}
?>