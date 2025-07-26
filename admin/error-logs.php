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

// Обробка POST запитів
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['toggle_logging'])) {
        $status = isset($_POST['enable_logging']) ? '1' : '0';
        if (updateErrorLoggingStatus($status)) {
            $success = $status === '1' ? 'Логування помилок увімкнено' : 'Логування помилок вимкнено';
        } else {
            $error = 'Помилка оновлення налаштувань';
        }
    }
    
    if (isset($_POST['clear_logs'])) {
        if (clearErrorLogs()) {
            $success = 'Лог помилок очищено';
        } else {
            $error = 'Помилка очищення логу';
        }
    }
}

// AJAX запити
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    switch ($_GET['action']) {
        case 'get_logs':
            echo json_encode(getErrorLogs());
            exit();
            
        case 'get_stats':
            echo json_encode(getErrorStats());
            exit();
            
        case 'clear_logs':
            echo json_encode(['success' => clearErrorLogs()]);
            exit();
    }
}

// Отримання налаштувань
$loggingEnabled = isErrorLoggingEnabled();
$logFile = defined('LOG_FILE') ? LOG_FILE : 'logs/error.log';
$logExists = file_exists($logFile);
$logSize = $logExists ? filesize($logFile) : 0;

include 'header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Лог помилок</h1>
            <p class="text-muted">Моніторинг та управління помилками PHP</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary" onclick="refreshLogs()">
                <i class="fas fa-sync-alt"></i> Оновити
            </button>
            <button type="button" class="btn btn-outline-danger" onclick="clearLogsConfirm()">
                <i class="fas fa-trash"></i> Очистити
            </button>
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
        <!-- Налаштування логування -->
        <div class="col-md-4">
            <div class="card settings-card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cog me-2"></i>Налаштування
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" id="settingsForm">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="enable_logging" 
                                       name="enable_logging" <?php echo $loggingEnabled ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="enable_logging">
                                    Увімкнути логування помилок
                                </label>
                            </div>
                            <div class="form-text">
                                Автоматично записує всі PHP помилки та попередження
                            </div>
                        </div>
                        
                        <button type="submit" name="toggle_logging" class="btn btn-primary w-100 mb-3">
                            <i class="fas fa-save me-2"></i>Зберегти налаштування
                        </button>
                    </form>
                    
                    <div class="log-info">
                        <h6>Інформація про лог:</h6>
                        <div class="info-item">
                            <strong>Файл:</strong>
                            <small class="text-muted"><?php echo $logFile; ?></small>
                        </div>
                        <div class="info-item">
                            <strong>Розмір:</strong>
                            <span id="logSize"><?php echo formatBytes($logSize); ?></span>
                        </div>
                        <div class="info-item">
                            <strong>Статус:</strong>
                            <span class="badge bg-<?php echo $loggingEnabled ? 'success' : 'secondary'; ?>" id="logStatus">
                                <?php echo $loggingEnabled ? 'Активний' : 'Неактивний'; ?>
                            </span>
                        </div>
                        <div class="info-item">
                            <strong>Останнє оновлення:</strong>
                            <span id="lastUpdate"><?php echo $logExists ? date('d.m.Y H:i:s', filemtime($logFile)) : 'Ніколи'; ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Статистика помилок -->
            <div class="card stats-card mt-3">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Статистика
                    </h5>
                </div>
                <div class="card-body" id="errorStats">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin"></i> Завантаження...
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Лог помилок -->
        <div class="col-md-8">
            <div class="card logs-card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-file-alt me-2"></i>Лог помилок
                            <span class="badge bg-secondary ms-2" id="logCount">0</span>
                        </h5>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="autoRefresh" checked>
                            <label class="form-check-label" for="autoRefresh">
                                Автооновлення
                            </label>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="log-container">
                        <div class="log-toolbar">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-secondary" onclick="filterLogs('all')">
                                    Всі
                                </button>
                                <button type="button" class="btn btn-outline-danger" onclick="filterLogs('error')">
                                    Помилки
                                </button>
                                <button type="button" class="btn btn-outline-warning" onclick="filterLogs('warning')">
                                    Попередження
                                </button>
                                <button type="button" class="btn btn-outline-info" onclick="filterLogs('notice')">
                                    Повідомлення
                                </button>
                            </div>
                            <div class="search-box">
                                <input type="text" class="form-control form-control-sm" 
                                       placeholder="Пошук..." id="searchLogs">
                            </div>
                        </div>
                        
                        <div class="log-content" id="logContent">
                            <div class="text-center p-4">
                                <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                                <p class="text-muted mt-2">Завантаження логів...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.settings-card, .stats-card, .logs-card {
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.settings-card:hover, .stats-card:hover, .logs-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.log-info .info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #eee;
}

.log-info .info-item:last-child {
    border-bottom: none;
}

.log-container {
    height: 70vh;
    display: flex;
    flex-direction: column;
}

.log-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid #dee2e6;
    background: #f8f9fa;
}

.search-box {
    width: 200px;
}

.log-content {
    flex: 1;
    overflow-y: auto;
    background: #1e1e1e;
    color: #e1e1e1;
    font-family: 'Courier New', monospace;
    font-size: 0.85rem;
    padding: 1rem;
}

.log-entry {
    margin-bottom: 0.5rem;
    padding: 0.5rem;
    border-radius: 4px;
    border-left: 4px solid #666;
    background: rgba(255, 255, 255, 0.05);
    transition: all 0.3s ease;
}

.log-entry:hover {
    background: rgba(255, 255, 255, 0.1);
}

.log-entry.error {
    border-left-color: #dc3545;
    background: rgba(220, 53, 69, 0.1);
}

.log-entry.warning {
    border-left-color: #ffc107;
    background: rgba(255, 193, 7, 0.1);
}

.log-entry.notice {
    border-left-color: #17a2b8;
    background: rgba(23, 162, 184, 0.1);
}

.log-entry.fatal {
    border-left-color: #721c24;
    background: rgba(114, 28, 36, 0.2);
}

.log-timestamp {
    color: #6c757d;
    font-size: 0.8rem;
    margin-right: 1rem;
}

.log-level {
    display: inline-block;
    padding: 0.2rem 0.5rem;
    border-radius: 3px;
    font-size: 0.7rem;
    font-weight: bold;
    margin-right: 1rem;
    text-transform: uppercase;
}

.log-level.error {
    background: #dc3545;
    color: white;
}

.log-level.warning {
    background: #ffc107;
    color: #212529;
}

.log-level.notice {
    background: #17a2b8;
    color: white;
}

.log-level.fatal {
    background: #721c24;
    color: white;
}

.log-message {
    word-break: break-word;
    line-height: 1.4;
}

.log-file {
    color: #6f42c1;
    font-size: 0.8rem;
    margin-top: 0.25rem;
}

.stats-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.stats-item:last-child {
    border-bottom: none;
}

.stats-number {
    font-size: 1.2rem;
    font-weight: bold;
}

.hidden {
    display: none !important;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.7; }
    100% { opacity: 1; }
}

.live-indicator {
    display: inline-block;
    width: 8px;
    height: 8px;
    background: #28a745;
    border-radius: 50%;
    animation: pulse 2s infinite;
    margin-right: 0.5rem;
}
</style>

<script>
let autoRefreshInterval;
let currentFilter = 'all';
let searchTerm = '';

document.addEventListener('DOMContentLoaded', function() {
    loadLogs();
    loadStats();
    setupAutoRefresh();
    setupSearch();
    
    // Оновлення кожні 5 секунд якщо увімкнено автооновлення
    autoRefreshInterval = setInterval(() => {
        if (document.getElementById('autoRefresh').checked) {
            loadLogs();
            loadStats();
        }
    }, 5000);
});

function loadLogs() {
    fetch('?action=get_logs')
        .then(response => response.json())
        .then(data => {
            displayLogs(data);
            updateLogCount(data.length);
        })
        .catch(error => {
            console.error('Error loading logs:', error);
            document.getElementById('logContent').innerHTML = 
                '<div class="text-center p-4"><i class="fas fa-exclamation-triangle text-warning"></i><p class="text-muted mt-2">Помилка завантаження логів</p></div>';
        });
}

function loadStats() {
    fetch('?action=get_stats')
        .then(response => response.json())
        .then(data => {
            displayStats(data);
        })
        .catch(error => {
            console.error('Error loading stats:', error);
        });
}

function displayLogs(logs) {
    const container = document.getElementById('logContent');
    
    if (logs.length === 0) {
        container.innerHTML = 
            '<div class="text-center p-4"><i class="fas fa-check-circle text-success fa-2x"></i><p class="text-muted mt-2">Немає помилок для відображення</p></div>';
        return;
    }
    
    const html = logs.map(log => {
        const level = log.level.toLowerCase();
        const timestamp = new Date(log.timestamp).toLocaleString('uk-UA');
        
        return `
            <div class="log-entry ${level}" data-level="${level}" data-message="${log.message.toLowerCase()}">
                <div>
                    <span class="log-timestamp">${timestamp}</span>
                    <span class="log-level ${level}">${log.level}</span>
                </div>
                <div class="log-message">${escapeHtml(log.message)}</div>
                ${log.file ? `<div class="log-file">${escapeHtml(log.file)}:${log.line}</div>` : ''}
            </div>
        `;
    }).join('');
    
    container.innerHTML = html;
    
    // Автоскрол вниз
    container.scrollTop = container.scrollHeight;
    
    // Застосовуємо фільтри
    filterLogs(currentFilter);
    searchLogs(searchTerm);
}

function displayStats(stats) {
    const container = document.getElementById('errorStats');
    
    const html = `
        <div class="stats-item">
            <span>Всього записів:</span>
            <span class="stats-number text-primary">${stats.total}</span>
        </div>
        <div class="stats-item">
            <span>Помилки:</span>
            <span class="stats-number text-danger">${stats.errors}</span>
        </div>
        <div class="stats-item">
            <span>Попередження:</span>
            <span class="stats-number text-warning">${stats.warnings}</span>
        </div>
        <div class="stats-item">
            <span>Повідомлення:</span>
            <span class="stats-number text-info">${stats.notices}</span>
        </div>
        <div class="stats-item">
            <span>За сьогодні:</span>
            <span class="stats-number text-success">${stats.today}</span>
        </div>
    `;
    
    container.innerHTML = html;
}

function filterLogs(level) {
    currentFilter = level;
    
    // Оновлення активної кнопки
    document.querySelectorAll('.log-toolbar .btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Фільтрація записів
    document.querySelectorAll('.log-entry').forEach(entry => {
        if (level === 'all' || entry.dataset.level === level) {
            entry.classList.remove('hidden');
        } else {
            entry.classList.add('hidden');
        }
    });
}

function setupSearch() {
    const searchInput = document.getElementById('searchLogs');
    searchInput.addEventListener('input', function() {
        searchTerm = this.value.toLowerCase();
        searchLogs(searchTerm);
    });
}

function searchLogs(term) {
    document.querySelectorAll('.log-entry').forEach(entry => {
        const messageMatch = entry.dataset.message.includes(term);
        const isFiltered = !entry.classList.contains('hidden');
        
        if (term === '' || messageMatch) {
            if (isFiltered || currentFilter === 'all' || entry.dataset.level === currentFilter) {
                entry.classList.remove('hidden');
            }
        } else {
            entry.classList.add('hidden');
        }
    });
}

function setupAutoRefresh() {
    const autoRefreshCheckbox = document.getElementById('autoRefresh');
    autoRefreshCheckbox.addEventListener('change', function() {
        if (this.checked) {
            // Додаємо індикатор live
            const title = document.querySelector('.logs-card .card-title');
            if (!title.querySelector('.live-indicator')) {
                title.insertAdjacentHTML('beforeend', '<span class="live-indicator"></span>');
            }
        } else {
            // Видаляємо індикатор live
            const indicator = document.querySelector('.live-indicator');
            if (indicator) {
                indicator.remove();
            }
        }
    });
}

function refreshLogs() {
    loadLogs();
    loadStats();
    
    // Анімація кнопки
    const btn = event.target.closest('button');
    const icon = btn.querySelector('i');
    icon.classList.add('fa-spin');
    setTimeout(() => icon.classList.remove('fa-spin'), 1000);
}

function clearLogsConfirm() {
    if (confirm('Ви впевнені, що хочете очистити всі логи помилок? Цю дію неможливо скасувати.')) {
        clearLogs();
    }
}

function clearLogs() {
    fetch('?action=clear_logs', { method: 'POST' })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadLogs();
                loadStats();
                showAlert('Лог помилок очищено', 'success');
            } else {
                showAlert('Помилка очищення логу', 'danger');
            }
        });
}

function updateLogCount(count) {
    document.getElementById('logCount').textContent = count;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showAlert(message, type) {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} animate__animated animate__bounceIn`;
    alert.innerHTML = `<i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle me-2"></i>${message}`;
    
    document.querySelector('.container-fluid').insertBefore(alert, document.querySelector('.row'));
    
    setTimeout(() => {
        alert.classList.add('animate__fadeOut');
        setTimeout(() => alert.remove(), 1000);
    }, 3000);
}
</script>

<?php
include 'footer.php';

// Функції для роботи з логами помилок
function isErrorLoggingEnabled() {
    try {
        $db = new Database();
        $result = $db->query("SELECT value FROM site_settings WHERE setting_key = 'error_logging_enabled'");
        $row = $result->fetch_assoc();
        return $row ? (bool)$row['value'] : true;
    } catch (Exception $e) {
        return ini_get('log_errors');
    }
}

function updateErrorLoggingStatus($status) {
    try {
        $db = new Database();
        
        // Оновлення в БД
        $stmt = $db->prepare("
            INSERT INTO site_settings (setting_key, value) 
            VALUES ('error_logging_enabled', ?) 
            ON DUPLICATE KEY UPDATE value = ?
        ");
        $stmt->bind_param("ss", $status, $status);
        $result = $stmt->execute();
        
        // Оновлення PHP налаштувань
        ini_set('log_errors', $status);
        if ($status === '1') {
            ini_set('error_log', 'logs/error.log');
        }
        
        return $result;
    } catch (Exception $e) {
        return false;
    }
}

function getErrorLogs() {
    $logFile = defined('LOG_FILE') ? LOG_FILE : 'logs/error.log';
    
    if (!file_exists($logFile)) {
        return [];
    }
    
    $logs = [];
    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    // Читаємо останні 500 рядків
    $lines = array_slice($lines, -500);
    
    foreach ($lines as $line) {
        if (preg_match('/\[(.*?)\]\s+(.*?):\s+(.*?)(?:\s+in\s+(.*?)\s+on\s+line\s+(\d+))?/', $line, $matches)) {
            $logs[] = [
                'timestamp' => $matches[1],
                'level' => trim($matches[2]),
                'message' => trim($matches[3]),
                'file' => isset($matches[4]) ? $matches[4] : '',
                'line' => isset($matches[5]) ? $matches[5] : ''
            ];
        } else {
            // Якщо формат не розпізнано, додаємо як є
            $logs[] = [
                'timestamp' => date('Y-m-d H:i:s'),
                'level' => 'UNKNOWN',
                'message' => $line,
                'file' => '',
                'line' => ''
            ];
        }
    }
    
    return array_reverse($logs); // Останні записи спочатку
}

function getErrorStats() {
    $logs = getErrorLogs();
    $stats = [
        'total' => count($logs),
        'errors' => 0,
        'warnings' => 0,
        'notices' => 0,
        'today' => 0
    ];
    
    $today = date('Y-m-d');
    
    foreach ($logs as $log) {
        $level = strtolower($log['level']);
        
        if (strpos($level, 'error') !== false || strpos($level, 'fatal') !== false) {
            $stats['errors']++;
        } elseif (strpos($level, 'warning') !== false) {
            $stats['warnings']++;
        } elseif (strpos($level, 'notice') !== false) {
            $stats['notices']++;
        }
        
        if (strpos($log['timestamp'], $today) === 0) {
            $stats['today']++;
        }
    }
    
    return $stats;
}

function clearErrorLogs() {
    $logFile = defined('LOG_FILE') ? LOG_FILE : 'logs/error.log';
    
    try {
        if (file_exists($logFile)) {
            return file_put_contents($logFile, '') !== false;
        }
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>