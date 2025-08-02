<?php
session_start();
require_once '../config.php';
require_once '../classes/Database.php';

$config = require '../config.php';

// Проверка авторизации
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$db = new Database($config['database']);

// Получаем логи админ-панели
$adminLogs = $db->fetchAll('
    SELECT al.*, u.first_name, u.last_name, u.username
    FROM admin_logs al
    LEFT JOIN users u ON al.admin_id = u.telegram_id
    ORDER BY al.created_at DESC
    LIMIT 100
');

// Добавляем лог о просмотре логов
$db->insert('admin_logs', [
    'admin_id' => $_SESSION['admin_id'] ?? 0,
    'action' => 'view_logs',
    'details' => 'Просмотр логов системы',
    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
    'created_at' => date('Y-m-d H:i:s')
]);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Логи - Game Garant by Неадекват</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .nav-link {
            color: rgba(255,255,255,0.8) !important;
            border-radius: 10px;
            margin: 2px 0;
        }
        .nav-link:hover, .nav-link.active {
            color: white !important;
            background: rgba(255,255,255,0.1);
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .log-entry {
            border-left: 4px solid #007bff;
            padding-left: 1rem;
            margin-bottom: 1rem;
        }
        .log-entry.warning {
            border-left-color: #ffc107;
        }
        .log-entry.danger {
            border-left-color: #dc3545;
        }
        .log-entry.success {
            border-left-color: #28a745;
        }
        .log-time {
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
            color: #6c757d;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-3">
                <div class="d-flex align-items-center mb-4">
                    <i class="bi bi-shield-check fs-2 text-white me-2"></i>
                    <h4 class="text-white mb-0">Game Garant by Неадекват</h4>
                </div>
                
                <nav class="nav flex-column">
                    <a class="nav-link" href="index.php">
                        <i class="bi bi-house-door me-2"></i> Главная
                    </a>
                    <a class="nav-link" href="deals.php">
                        <i class="bi bi-briefcase me-2"></i> Сделки
                    </a>
                    <a class="nav-link" href="users.php">
                        <i class="bi bi-people me-2"></i> Пользователи
                    </a>
                    <a class="nav-link" href="settings.php">
                        <i class="bi bi-gear me-2"></i> Настройки
                    </a>
                    <a class="nav-link" href="payments.php">
                        <i class="bi bi-credit-card me-2"></i> Платежи
                    </a>
                    <a class="nav-link" href="payment_settings.php">
                        <i class="bi bi-wallet2 me-2"></i> Реквизиты
                    </a>
                    <a class="nav-link" href="withdrawals.php">
                        <i class="bi bi-cash-stack me-2"></i> Вывод средств
                    </a>
                    <a class="nav-link" href="content.php">
                        <i class="bi bi-file-text me-2"></i> Контент
                    </a>
                    <a class="nav-link active" href="logs.php">
                        <i class="bi bi-journal-text me-2"></i> Логи
                    </a>
                    <hr class="text-white-50">
                    <a class="nav-link" href="logout.php">
                        <i class="bi bi-box-arrow-right me-2"></i> Выход
                    </a>
                </nav>
            </div>

            <!-- Main content -->
            <div class="col-md-9 col-lg-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="bi bi-journal-text me-2"></i>Системные логи</h1>
                    <div>
                        <button class="btn btn-outline-primary btn-sm" onclick="location.reload()">
                            <i class="bi bi-arrow-clockwise me-1"></i>Обновить
                        </button>
                    </div>
                </div>

                <!-- Информация о логах -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="bi bi-journal-text fs-1 text-primary mb-2"></i>
                                <h3><?= count($adminLogs) ?></h3>
                                <p class="mb-0 text-muted">Записей логов</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="bi bi-clock fs-1 text-info mb-2"></i>
                                <h3><?= date('H:i') ?></h3>
                                <p class="mb-0 text-muted">Текущее время</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="bi bi-calendar fs-1 text-success mb-2"></i>
                                <h3><?= date('d.m') ?></h3>
                                <p class="mb-0 text-muted">Сегодня</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="bi bi-server fs-1 text-warning mb-2"></i>
                                <h3>OK</h3>
                                <p class="mb-0 text-muted">Статус системы</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Системная информация -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Системная информация</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>PHP версия:</strong><br>
                                <span class="text-muted"><?= phpversion() ?></span>
                            </div>
                            <div class="col-md-3">
                                <strong>Время работы:</strong><br>
                                <span class="text-muted"><?= date('Y-m-d H:i:s') ?></span>
                            </div>
                            <div class="col-md-3">
                                <strong>Использование памяти:</strong><br>
                                <span class="text-muted"><?= round(memory_get_usage() / 1024 / 1024, 2) ?> MB</span>
                            </div>
                            <div class="col-md-3">
                                <strong>IP адрес:</strong><br>
                                <span class="text-muted"><?= $_SERVER['REMOTE_ADDR'] ?? 'unknown' ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PHP Error Log -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Последние ошибки PHP</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $errorLog = ini_get('error_log');
                        if ($errorLog && file_exists($errorLog)) {
                            $errors = array_slice(file($errorLog), -10);
                            if (!empty($errors)) {
                                echo '<div class="log-container" style="max-height: 300px; overflow-y: auto;">';
                                foreach (array_reverse($errors) as $error) {
                                    echo '<div class="log-entry danger">';
                                    echo '<small class="log-time">' . date('Y-m-d H:i:s') . '</small><br>';
                                    echo '<code>' . htmlspecialchars(trim($error)) . '</code>';
                                    echo '</div>';
                                }
                                echo '</div>';
                            } else {
                                echo '<p class="text-muted">Ошибок не найдено</p>';
                            }
                        } else {
                            echo '<p class="text-muted">Лог файл ошибок не найден</p>';
                        }
                        ?>
                    </div>
                </div>

                <!-- Логи админ-панели -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-person-gear me-2"></i>
                            Логи админ-панели (<?= count($adminLogs) ?>)
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($adminLogs)): ?>
                            <div class="text-center p-4">
                                <i class="bi bi-journal fs-1 text-muted"></i>
                                <p class="text-muted mt-2">Логов не найдено</p>
                            </div>
                        <?php else: ?>
                            <div style="max-height: 600px; overflow-y: auto;">
                                <?php foreach ($adminLogs as $log): ?>
                                    <div class="log-entry <?= getLogClass($log['action']) ?>">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <div class="fw-bold">
                                                    <?= getActionIcon($log['action']) ?>
                                                    <?= htmlspecialchars($log['action']) ?>
                                                </div>
                                                <?php if ($log['details']): ?>
                                                    <div class="text-muted mt-1">
                                                        <?= htmlspecialchars($log['details']) ?>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if ($log['first_name']): ?>
                                                    <small class="text-info">
                                                        Администратор: <?= htmlspecialchars($log['first_name'] . ' ' . $log['last_name']) ?>
                                                        (@<?= htmlspecialchars($log['username']) ?>)
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-end">
                                                <div class="log-time">
                                                    <?= date('d.m.Y H:i:s', strtotime($log['created_at'])) ?>
                                                </div>
                                                <?php if ($log['ip_address']): ?>
                                                    <small class="text-muted">IP: <?= htmlspecialchars($log['ip_address']) ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Примеры команд для мониторинга -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-terminal me-2"></i>Полезные команды для мониторинга</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Логи веб-сервера:</h6>
                                <code>tail -f /var/log/apache2/access.log</code><br>
                                <code>tail -f /var/log/nginx/access.log</code>
                            </div>
                            <div class="col-md-6">
                                <h6>Логи ошибок:</h6>
                                <code>tail -f /var/log/apache2/error.log</code><br>
                                <code>tail -f /var/log/nginx/error.log</code>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Мониторинг системы:</h6>
                                <code>htop</code> - процессы<br>
                                <code>df -h</code> - место на диске
                            </div>
                            <div class="col-md-6">
                                <h6>MySQL логи:</h6>
                                <code>tail -f /var/log/mysql/error.log</code><br>
                                <code>SHOW PROCESSLIST;</code> - активные запросы
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Автообновление каждые 30 секунд
        setInterval(function() {
            location.reload();
        }, 30000);
    </script>
</body>
</html>

<?php
// Вспомогательные функции для отображения логов
function getLogClass($action) {
    $dangerActions = ['ban_user', 'cancel_deal', 'delete', 'error'];
    $warningActions = ['unverify_user', 'dispute', 'warning'];
    $successActions = ['verify_user', 'complete_deal', 'login', 'success'];
    
    foreach ($dangerActions as $dangerAction) {
        if (strpos($action, $dangerAction) !== false) return 'danger';
    }
    
    foreach ($warningActions as $warningAction) {
        if (strpos($action, $warningAction) !== false) return 'warning';
    }
    
    foreach ($successActions as $successAction) {
        if (strpos($action, $successAction) !== false) return 'success';
    }
    
    return '';
}

function getActionIcon($action) {
    $icons = [
        'login' => '<i class="bi bi-box-arrow-in-right me-1"></i>',
        'logout' => '<i class="bi bi-box-arrow-right me-1"></i>',
        'ban_user' => '<i class="bi bi-ban me-1"></i>',
        'unban_user' => '<i class="bi bi-unlock me-1"></i>',
        'verify_user' => '<i class="bi bi-check-circle me-1"></i>',
        'cancel_deal' => '<i class="bi bi-x-circle me-1"></i>',
        'complete_deal' => '<i class="bi bi-check-circle me-1"></i>',
        'view_logs' => '<i class="bi bi-eye me-1"></i>',
        'update_settings' => '<i class="bi bi-gear me-1"></i>',
    ];
    
    foreach ($icons as $key => $icon) {
        if (strpos($action, $key) !== false) {
            return $icon;
        }
    }
    
    return '<i class="bi bi-info-circle me-1"></i>';
}
?>