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
        if (isset($_POST['help_text'])) {
            $helpText = trim($_POST['help_text']);
            $existing = $db->fetch('SELECT id FROM bot_settings WHERE setting_key = "help_text"');
            
            if ($existing) {
                $db->update('bot_settings', [
                    'setting_value' => $helpText,
                    'updated_at' => date('Y-m-d H:i:s')
                ], 'setting_key = "help_text"');
            } else {
                $db->insert('bot_settings', [
                    'setting_key' => 'help_text',
                    'setting_value' => $helpText,
                    'description' => 'Текст помощи для пользователей',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
            
            $message = '<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Текст помощи сохранен!</div>';
        }
        
        if (isset($_POST['support_text'])) {
            $supportText = trim($_POST['support_text']);
            $existing = $db->fetch('SELECT id FROM bot_settings WHERE setting_key = "support_text"');
            
            if ($existing) {
                $db->update('bot_settings', [
                    'setting_value' => $supportText,
                    'updated_at' => date('Y-m-d H:i:s')
                ], 'setting_key = "support_text"');
            } else {
                $db->insert('bot_settings', [
                    'setting_key' => 'support_text',
                    'setting_value' => $supportText,
                    'description' => 'Текст поддержки для пользователей',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
            
            $message = '<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Текст поддержки сохранен!</div>';
        }
        
    } catch (Exception $e) {
        $message = '<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>Ошибка при сохранении</div>';
    }
}

// Получаем текущие тексты
$helpText = $db->fetch('SELECT setting_value FROM bot_settings WHERE setting_key = "help_text"');
$helpText = $helpText ? $helpText['setting_value'] : '';

$supportText = $db->fetch('SELECT setting_value FROM bot_settings WHERE setting_key = "support_text"');
$supportText = $supportText ? $supportText['setting_value'] : '';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление контентом - Escrow Bot</title>
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
        .preview {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
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
                    <a class="nav-link active" href="content.php">
                        <i class="bi bi-file-text me-2"></i> Контент
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
                    <h1><i class="bi bi-file-text me-2"></i>Управление контентом</h1>
                </div>

                <?= $message ?>

                <div class="row">
                    <!-- Текст помощи -->
                    <div class="col-lg-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-question-circle me-2"></i>
                                    Текст помощи (/help)
                                </h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="help_text" class="form-label">Содержимое команды /help</label>
                                        <textarea class="form-control" id="help_text" name="help_text" rows="12" 
                                                  placeholder="Введите текст, который будет отображаться при команде /help"><?= htmlspecialchars($helpText) ?></textarea>
                                        <div class="form-text">
                                            Поддерживается HTML разметка: &lt;b&gt;жирный&lt;/b&gt;, &lt;i&gt;курсив&lt;/i&gt;, &lt;code&gt;код&lt;/code&gt;
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-2"></i>Сохранить текст помощи
                                    </button>
                                </form>
                                
                                <?php if ($helpText): ?>
                                    <div class="preview">
                                        <h6><i class="bi bi-eye me-2"></i>Предварительный просмотр:</h6>
                                        <div style="white-space: pre-line;"><?= nl2br(htmlspecialchars($helpText)) ?></div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Текст поддержки -->
                    <div class="col-lg-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-headset me-2"></i>
                                    Текст поддержки (/support)
                                </h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="support_text" class="form-label">Содержимое команды /support</label>
                                        <textarea class="form-control" id="support_text" name="support_text" rows="12" 
                                                  placeholder="Введите контактную информацию и инструкции для пользователей"><?= htmlspecialchars($supportText) ?></textarea>
                                        <div class="form-text">
                                            Укажите контакты поддержки, время работы, способы связи
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-save me-2"></i>Сохранить текст поддержки
                                    </button>
                                </form>
                                
                                <?php if ($supportText): ?>
                                    <div class="preview">
                                        <h6><i class="bi bi-eye me-2"></i>Предварительный просмотр:</h6>
                                        <div style="white-space: pre-line;"><?= nl2br(htmlspecialchars($supportText)) ?></div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Шаблоны и примеры -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-lightbulb me-2"></i>Примеры и шаблоны</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>📋 Пример текста помощи:</h6>
                                <div class="bg-light p-3 rounded">
                                    <small>
                                        <strong>📋 Доступные команды:</strong><br><br>
                                        /start - Начать работу<br>
                                        /create_deal - Создать сделку<br>
                                        /my_deals - Мои сделки<br>
                                        /balance - Баланс<br>
                                        /help - Помощь<br>
                                        /support - Поддержка<br><br>
                                        <strong>💡 Как работает гарант:</strong><br>
                                        1. Создайте сделку и найдите второго участника<br>
                                        2. Покупатель пополняет баланс на сумму сделки<br>
                                        3. После выполнения условий продавец получает средства<br>
                                        4. Сервис берет комиссию 5% за безопасность сделки
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6>📞 Пример текста поддержки:</h6>
                                <div class="bg-light p-3 rounded">
                                    <small>
                                        <strong>📞 Техническая поддержка</strong><br><br>
                                        <strong>🔧 Если у вас возникли проблемы:</strong><br>
                                        • Ошибки в работе бота<br>
                                        • Вопросы по сделкам<br>
                                        • Проблемы с платежами<br>
                                        • Споры с участниками<br><br>
                                        <strong>📱 Свяжитесь с нами:</strong><br>
                                        💬 Telegram: @your_support<br>
                                        📧 Email: support@yourbot.com<br><br>
                                        <strong>⏰ Время работы:</strong> 24/7<br>
                                        <strong>💬 Среднее время ответа:</strong> 2-4 часа
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="row">
                            <div class="col-12">
                                <h6>📝 Полезные советы:</h6>
                                <ul class="list-unstyled">
                                    <li>• Используйте эмодзи для лучшего восприятия</li>
                                    <li>• Структурируйте информацию по блокам</li>
                                    <li>• Указывайте актуальные контакты поддержки</li>
                                    <li>• Обновляйте тексты при изменении функционала</li>
                                    <li>• Тестируйте отображение в Telegram после изменений</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>