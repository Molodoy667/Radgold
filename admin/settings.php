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
$message = '';

// Обработка формы
if ($_POST) {
    try {
        $settings = [
            'commission_percent' => $_POST['commission_percent'],
            'min_deal_amount' => $_POST['min_deal_amount'],
            'max_deal_amount' => $_POST['max_deal_amount'],
            'deal_timeout_hours' => $_POST['deal_timeout_hours'],
            'maintenance_mode' => isset($_POST['maintenance_mode']) ? 1 : 0,
            'welcome_message' => $_POST['welcome_message']
        ];

        foreach ($settings as $key => $value) {
            $db->query(
                'UPDATE bot_settings SET setting_value = :value WHERE setting_key = :key',
                ['value' => $value, 'key' => $key]
            );
        }

        $message = '<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Настройки успешно сохранены!</div>';
    } catch (Exception $e) {
        $message = '<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>Ошибка при сохранении настроек</div>';
    }
}

// Получаем текущие настройки
$settingsData = $db->fetchAll('SELECT setting_key, setting_value FROM bot_settings');
$settings = [];
foreach ($settingsData as $setting) {
    $settings[$setting['setting_key']] = $setting['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Настройки - Escrow Bot</title>
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
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-3">
                <div class="d-flex align-items-center mb-4">
                    <i class="bi bi-shield-check fs-2 text-white me-2"></i>
                    <h4 class="text-white mb-0">Escrow Bot</h4>
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
                    <a class="nav-link active" href="settings.php">
                        <i class="bi bi-gear me-2"></i> Настройки
                    </a>
                    <a class="nav-link" href="payments.php">
                        <i class="bi bi-credit-card me-2"></i> Платежи
                    </a>
                    <a class="nav-link" href="payment_settings.php">
                        <i class="bi bi-wallet2 me-2"></i> Реквизиты
                    </a>
                    <a class="nav-link" href="logs.php">
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
                    <h1><i class="bi bi-gear me-2"></i>Настройки бота</h1>
                </div>

                <?= $message ?>

                <form method="POST">
                    <div class="row">
                        <!-- Основные настройки -->
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="bi bi-sliders me-2"></i>Основные настройки</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="commission_percent" class="form-label">Комиссия (%)</label>
                                        <input type="number" class="form-control" id="commission_percent" name="commission_percent" 
                                               value="<?= $settings['commission_percent'] ?>" min="0" max="100" step="0.1" required>
                                        <div class="form-text">Комиссия сервиса в процентах от суммы сделки</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="min_deal_amount" class="form-label">Минимальная сумма сделки (₽)</label>
                                        <input type="number" class="form-control" id="min_deal_amount" name="min_deal_amount" 
                                               value="<?= $settings['min_deal_amount'] ?>" min="1" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="max_deal_amount" class="form-label">Максимальная сумма сделки (₽)</label>
                                        <input type="number" class="form-control" id="max_deal_amount" name="max_deal_amount" 
                                               value="<?= $settings['max_deal_amount'] ?>" min="1" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="deal_timeout_hours" class="form-label">Время на выполнение сделки (часы)</label>
                                        <input type="number" class="form-control" id="deal_timeout_hours" name="deal_timeout_hours" 
                                               value="<?= $settings['deal_timeout_hours'] ?>" min="1" max="720" required>
                                        <div class="form-text">Время, в течение которого должна быть завершена сделка</div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" 
                                                   <?= $settings['maintenance_mode'] ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="maintenance_mode">
                                                Режим технического обслуживания
                                            </label>
                                        </div>
                                        <div class="form-text">При включении бот будет недоступен для пользователей</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Сообщения -->
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="bi bi-chat-text me-2"></i>Сообщения</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="welcome_message" class="form-label">Приветственное сообщение</label>
                                        <textarea class="form-control" id="welcome_message" name="welcome_message" 
                                                  rows="4" required><?= htmlspecialchars($settings['welcome_message']) ?></textarea>
                                        <div class="form-text">Сообщение, которое увидят пользователи при запуске бота</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Статистика настроек -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Текущие значения</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="text-center p-3 bg-light rounded">
                                                <h4 class="text-primary"><?= $settings['commission_percent'] ?>%</h4>
                                                <small class="text-muted">Комиссия</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center p-3 bg-light rounded">
                                                <h4 class="text-success"><?= number_format($settings['min_deal_amount']) ?> ₽</h4>
                                                <small class="text-muted">Мин. сумма</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center p-3 bg-light rounded">
                                                <h4 class="text-warning"><?= number_format($settings['max_deal_amount']) ?> ₽</h4>
                                                <small class="text-muted">Макс. сумма</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center p-3 bg-light rounded">
                                                <h4 class="text-info"><?= $settings['deal_timeout_hours'] ?>ч</h4>
                                                <small class="text-muted">Таймаут</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="index.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Назад
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>Сохранить настройки
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>