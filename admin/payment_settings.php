<?php
session_start();
require_once '../config.php';
require_once '../classes/Database.php';
require_once '../classes/PaymentSystem.php';

$config = require '../config.php';

// Проверка авторизации
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$db = new Database($config['database']);
$paymentSystem = new PaymentSystem($db, $config);

$message = '';

// Обработка формы
if ($_POST) {
    try {
        if (isset($_POST['sberbank_data'])) {
            $sberbankData = [
                'card_number' => $_POST['sberbank_card_number'],
                'phone' => $_POST['sberbank_phone'],
                'holder_name' => $_POST['sberbank_holder_name']
            ];
            $paymentSystem->updatePaymentSetting('sberbank_data', $sberbankData);
        }
        
        if (isset($_POST['mir_card_data'])) {
            $mirData = [
                'card_number' => $_POST['mir_card_number'],
                'holder_name' => $_POST['mir_holder_name'],
                'bank_name' => $_POST['mir_bank_name']
            ];
            $paymentSystem->updatePaymentSetting('mir_card_data', $mirData);
        }
        
        if (isset($_POST['manual_card_data'])) {
            $cardData = [
                'card_number' => $_POST['manual_card_number'],
                'holder_name' => $_POST['manual_holder_name'],
                'bank_name' => $_POST['manual_bank_name']
            ];
            $paymentSystem->updatePaymentSetting('manual_card_data', $cardData);
        }
        
        $message = '<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Настройки платежей успешно сохранены!</div>';
        
    } catch (Exception $e) {
        $message = '<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>Ошибка при сохранении настроек</div>';
    }
}

// Получаем текущие настройки
$sberbankData = $db->fetch('SELECT setting_value FROM bot_settings WHERE setting_key = "sberbank_data"');
$sberbankData = $sberbankData ? json_decode($sberbankData['setting_value'], true) : [];

$mirData = $db->fetch('SELECT setting_value FROM bot_settings WHERE setting_key = "mir_card_data"');
$mirData = $mirData ? json_decode($mirData['setting_value'], true) : [];

$cardData = $db->fetch('SELECT setting_value FROM bot_settings WHERE setting_key = "manual_card_data"');
$cardData = $cardData ? json_decode($cardData['setting_value'], true) : [];

// Получаем ожидающие платежи
$pendingPayments = $paymentSystem->getPendingPayments();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Настройки платежей - Escrow Bot</title>
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
        .payment-card {
            border-left: 4px solid #007bff;
        }
        .payment-card.sberbank {
            border-left-color: #00AA13;
        }
        .payment-card.mir {
            border-left-color: #0066CC;
        }
        .payment-card.manual {
            border-left-color: #FF6B00;
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
                    <a class="nav-link active" href="payment_settings.php">
                        <i class="bi bi-wallet2 me-2"></i> Реквизиты
                    </a>
                    <a class="nav-link" href="withdrawals.php">
                        <i class="bi bi-cash-stack me-2"></i> Вывод средств
                    </a>
                    <a class="nav-link" href="content.php">
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
                    <h1><i class="bi bi-wallet2 me-2"></i>Настройки платежных реквизитов</h1>
                </div>

                <?= $message ?>
                
                <?php if (isset($_SESSION['message'])): ?>
                    <?= $_SESSION['message'] ?>
                    <?php unset($_SESSION['message']); ?>
                <?php endif; ?>

                <!-- Ожидающие платежи -->
                <?php if (!empty($pendingPayments)): ?>
                    <div class="alert alert-warning">
                        <h5><i class="bi bi-exclamation-triangle me-2"></i>Ожидающие подтверждения платежи (<?= count($pendingPayments) ?>)</h5>
                        <div class="mt-3">
                            <?php foreach ($pendingPayments as $payment): ?>
                                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                    <div>
                                        <strong>#<?= $payment['id'] ?></strong> - 
                                        <?= htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name']) ?>
                                        (<?= number_format($payment['amount'], 2) ?> ₽)
                                    </div>
                                    <div>
                                        <button class="btn btn-success btn-sm" onclick="confirmPayment(<?= $payment['id'] ?>)">
                                            <i class="bi bi-check"></i> Подтвердить
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="rejectPayment(<?= $payment['id'] ?>)">
                                            <i class="bi bi-x"></i> Отклонить
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <!-- Сбербанк -->
                    <div class="col-md-4 mb-4">
                        <div class="card payment-card sberbank">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-credit-card me-2"></i>
                                    🟢 Сбербанк
                                </h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="sberbank_data" value="1">
                                    
                                    <div class="mb-3">
                                        <label for="sberbank_card_number" class="form-label">Номер карты</label>
                                        <input type="text" class="form-control" id="sberbank_card_number" name="sberbank_card_number"
                                               value="<?= htmlspecialchars($sberbankData['card_number'] ?? '') ?>"
                                               placeholder="2202 2020 1234 5678" maxlength="19">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="sberbank_phone" class="form-label">Номер телефона</label>
                                        <input type="text" class="form-control" id="sberbank_phone" name="sberbank_phone"
                                               value="<?= htmlspecialchars($sberbankData['phone'] ?? '') ?>"
                                               placeholder="+7 900 123 45 67">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="sberbank_holder_name" class="form-label">Имя держателя</label>
                                        <input type="text" class="form-control" id="sberbank_holder_name" name="sberbank_holder_name"
                                               value="<?= htmlspecialchars($sberbankData['holder_name'] ?? '') ?>"
                                               placeholder="IVAN PETROV">
                                    </div>
                                    
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bi bi-check-lg me-2"></i>Сохранить
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Карта МИР -->
                    <div class="col-md-4 mb-4">
                        <div class="card payment-card mir">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-credit-card me-2"></i>
                                    💳 Карта МИР
                                </h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="mir_card_data" value="1">
                                    
                                    <div class="mb-3">
                                        <label for="mir_card_number" class="form-label">Номер карты МИР</label>
                                        <input type="text" class="form-control" id="mir_card_number" name="mir_card_number"
                                               value="<?= htmlspecialchars($mirData['card_number'] ?? '') ?>"
                                               placeholder="2200 1234 5678 9012" maxlength="19">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="mir_holder_name" class="form-label">Имя держателя</label>
                                        <input type="text" class="form-control" id="mir_holder_name" name="mir_holder_name"
                                               value="<?= htmlspecialchars($mirData['holder_name'] ?? '') ?>"
                                               placeholder="IVAN PETROV">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="mir_bank_name" class="form-label">Название банка</label>
                                        <input type="text" class="form-control" id="mir_bank_name" name="mir_bank_name"
                                               value="<?= htmlspecialchars($mirData['bank_name'] ?? '') ?>"
                                               placeholder="ВТБ">
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-check-lg me-2"></i>Сохранить
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Дополнительная карта -->
                    <div class="col-md-4 mb-4">
                        <div class="card payment-card manual">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-credit-card me-2"></i>
                                    💰 Банковская карта
                                </h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="manual_card_data" value="1">
                                    
                                    <div class="mb-3">
                                        <label for="manual_card_number" class="form-label">Номер карты</label>
                                        <input type="text" class="form-control" id="manual_card_number" name="manual_card_number"
                                               value="<?= htmlspecialchars($cardData['card_number'] ?? '') ?>"
                                               placeholder="4276 1234 5678 9012" maxlength="19">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="manual_holder_name" class="form-label">Имя держателя</label>
                                        <input type="text" class="form-control" id="manual_holder_name" name="manual_holder_name"
                                               value="<?= htmlspecialchars($cardData['holder_name'] ?? '') ?>"
                                               placeholder="IVAN PETROV">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="manual_bank_name" class="form-label">Название банка</label>
                                        <input type="text" class="form-control" id="manual_bank_name" name="manual_bank_name"
                                               value="<?= htmlspecialchars($cardData['bank_name'] ?? '') ?>"
                                               placeholder="Тинькофф Банк">
                                    </div>
                                    
                                    <button type="submit" class="btn btn-warning w-100">
                                        <i class="bi bi-check-lg me-2"></i>Сохранить
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Инструкция -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Инструкция по настройке</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>🟢 Сбербанк:</h6>
                                <ul>
                                    <li>Укажите номер карты Сбербанка</li>
                                    <li>Добавьте номер телефона для переводов</li>
                                    <li>Пользователи смогут переводить через Сбербанк Онлайн</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>💳 Карта МИР:</h6>
                                <ul>
                                    <li>Укажите номер карты МИР</li>
                                    <li>Добавьте имя держателя и банк</li>
                                    <li>Пользователи смогут переводить на карту МИР</li>
                                </ul>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <h6>💰 Дополнительная карта:</h6>
                                <ul>
                                    <li>Любая банковская карта</li>
                                    <li>Для переводов через другие банки</li>
                                    <li>Резервный способ пополнения</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>⚠️ Важно:</h6>
                                <ul>
                                    <li>Все платежи требуют ручного подтверждения</li>
                                    <li>Проверяйте реквизиты перед сохранением</li>
                                    <li>Регулярно проверяйте ожидающие платежи</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальные окна -->
    <div class="modal fade" id="confirmPaymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="confirmPaymentForm" method="POST" action="confirm_payment.php">
                    <div class="modal-header">
                        <h5 class="modal-title">Подтверждение платежа</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="confirm">
                        <input type="hidden" name="payment_id" id="confirm_payment_id">
                        <p>Вы уверены, что хотите подтвердить этот платеж?</p>
                        <p class="text-success">Средства будут зачислены на баланс пользователя.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-success">Подтвердить платеж</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="rejectPaymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="rejectPaymentForm" method="POST" action="confirm_payment.php">
                    <div class="modal-header">
                        <h5 class="modal-title">Отклонение платежа</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="reject">
                        <input type="hidden" name="payment_id" id="reject_payment_id">
                        <p>Укажите причину отклонения платежа:</p>
                        <textarea class="form-control" name="reason" rows="3" required 
                                  placeholder="Например: Неверная сумма, не указан комментарий..."></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-danger">Отклонить платеж</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmPayment(paymentId) {
            document.getElementById('confirm_payment_id').value = paymentId;
            new bootstrap.Modal(document.getElementById('confirmPaymentModal')).show();
        }

        function rejectPayment(paymentId) {
            document.getElementById('reject_payment_id').value = paymentId;
            new bootstrap.Modal(document.getElementById('rejectPaymentModal')).show();
        }

        // Форматирование номеров карт
        document.querySelectorAll('input[name*="card_number"]').forEach(function(input) {
            input.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                let formattedValue = value.replace(/(\d{4})(?=\d)/g, '$1 ');
                if (formattedValue.length <= 19) {
                    e.target.value = formattedValue;
                }
            });
        });
    </script>
</body>
</html>