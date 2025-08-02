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

// Получаем все платежи с информацией о пользователях и сделках
$allPayments = $db->fetchAll('
    SELECT p.*, 
           u.first_name, u.last_name, u.username,
           d.deal_number, d.title as deal_title
    FROM payments p
    LEFT JOIN users u ON p.user_id = u.telegram_id
    LEFT JOIN deals d ON p.deal_id = d.id
    ORDER BY p.created_at DESC
');

// Фильтрация по статусу
$statusFilter = $_GET['status'] ?? 'all';
if ($statusFilter !== 'all') {
    $allPayments = array_filter($allPayments, function($payment) use ($statusFilter) {
        return $payment['status'] === $statusFilter;
    });
}

// Статистика платежей
$stats = $db->fetch('
    SELECT 
        COUNT(*) as total_payments,
        COUNT(CASE WHEN status = "completed" THEN 1 END) as completed_payments,
        COUNT(CASE WHEN status = "pending" THEN 1 END) as pending_payments,
        COUNT(CASE WHEN status = "failed" THEN 1 END) as failed_payments,
        SUM(CASE WHEN status = "completed" THEN amount ELSE 0 END) as total_volume,
        SUM(amount) as total_amount
    FROM payments
');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Платежи - Escrow Bot</title>
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
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .stat-card-success {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        .stat-card-warning {
            background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            color: #333;
        }
        .stat-card-danger {
            background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
            color: #333;
        }
        .payment-method-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8em;
            font-weight: bold;
        }
        .yoomoney { background: #8B00FF; color: white; }
        .qiwi { background: #FF8C00; color: white; }
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
                    <a class="nav-link active" href="payments.php">
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
                    <h1><i class="bi bi-credit-card me-2"></i>Управление платежами</h1>
                </div>

                <!-- Статистика платежей -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card">
                            <div class="card-body text-center">
                                <i class="bi bi-credit-card fs-1 mb-2"></i>
                                <h3><?= $stats['total_payments'] ?></h3>
                                <p class="mb-0">Всего платежей</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card-success">
                            <div class="card-body text-center">
                                <i class="bi bi-check-circle fs-1 mb-2"></i>
                                <h3><?= $stats['completed_payments'] ?></h3>
                                <p class="mb-0">Завершено</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card-warning">
                            <div class="card-body text-center">
                                <i class="bi bi-clock fs-1 mb-2"></i>
                                <h3><?= $stats['pending_payments'] ?></h3>
                                <p class="mb-0">В ожидании</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card-danger">
                            <div class="card-body text-center">
                                <i class="bi bi-x-circle fs-1 mb-2"></i>
                                <h3><?= $stats['failed_payments'] ?></h3>
                                <p class="mb-0">Неудачные</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Финансовая статистика -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Финансовая статистика</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="text-center">
                                            <h4 class="text-success"><?= number_format($stats['total_volume'], 2) ?> ₽</h4>
                                            <small class="text-muted">Успешные платежи</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center">
                                            <h4 class="text-primary"><?= number_format($stats['total_amount'], 2) ?> ₽</h4>
                                            <small class="text-muted">Общий оборот</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-percent me-2"></i>Показатели</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="text-center">
                                            <h4 class="text-info">
                                                <?= $stats['total_payments'] > 0 ? round(($stats['completed_payments'] / $stats['total_payments']) * 100, 1) : 0 ?>%
                                            </h4>
                                            <small class="text-muted">Успешность</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center">
                                            <h4 class="text-warning">
                                                <?= $stats['total_payments'] > 0 ? round(($stats['pending_payments'] / $stats['total_payments']) * 100, 1) : 0 ?>%
                                            </h4>
                                            <small class="text-muted">В обработке</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Фильтры -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5 class="mb-0">Фильтр по статусу:</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="btn-group" role="group">
                                    <a href="?status=all" class="btn <?= $statusFilter === 'all' ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">
                                        Все
                                    </a>
                                    <a href="?status=pending" class="btn <?= $statusFilter === 'pending' ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">
                                        Ожидание
                                    </a>
                                    <a href="?status=completed" class="btn <?= $statusFilter === 'completed' ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">
                                        Завершены
                                    </a>
                                    <a href="?status=failed" class="btn <?= $statusFilter === 'failed' ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">
                                        Неудачные
                                    </a>
                                    <a href="?status=refunded" class="btn <?= $statusFilter === 'refunded' ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">
                                        Возвраты
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Список платежей -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-list me-2"></i>
                            Платежи (<?= count($allPayments) ?>)
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($allPayments)): ?>
                            <div class="text-center p-4">
                                <i class="bi bi-credit-card fs-1 text-muted"></i>
                                <p class="text-muted mt-2">Платежей не найдено</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Пользователь</th>
                                            <th>Сделка</th>
                                            <th>Сумма</th>
                                            <th>Способ оплаты</th>
                                            <th>Статус</th>
                                            <th>Дата создания</th>
                                            <th>Обновлено</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($allPayments as $payment): ?>
                                            <tr>
                                                <td><strong>#<?= $payment['id'] ?></strong></td>
                                                <td>
                                                    <div class="fw-bold">
                                                        <?= htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name']) ?>
                                                    </div>
                                                    <small class="text-muted">@<?= htmlspecialchars($payment['username']) ?></small>
                                                </td>
                                                <td>
                                                    <?php if ($payment['deal_number']): ?>
                                                        <div class="fw-bold"><?= $payment['deal_number'] ?></div>
                                                        <small class="text-muted"><?= htmlspecialchars(substr($payment['deal_title'], 0, 30)) ?>...</small>
                                                    <?php else: ?>
                                                        <span class="text-muted">Пополнение баланса</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="fw-bold text-success">
                                                        <?= number_format($payment['amount'], 2) ?> ₽
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="payment-method-icon <?= $payment['payment_method'] ?> me-2">
                                                            <?php
                                                            $icons = [
                                                                'yoomoney' => 'YM',
                                                                'qiwi' => 'Q',
                                                                'card' => 'C'
                                                            ];
                                                            echo $icons[$payment['payment_method']] ?? 'P';
                                                            ?>
                                                        </div>
                                                        <div>
                                                            <div class="fw-bold">
                                                                <?php
                                                                $methods = [
                                                                    'yoomoney' => 'ЮMoney',
                                                                    'qiwi' => 'QIWI',
                                                                    'card' => 'Банковская карта'
                                                                ];
                                                                echo $methods[$payment['payment_method']] ?? $payment['payment_method'];
                                                                ?>
                                                            </div>
                                                            <?php if ($payment['payment_id']): ?>
                                                                <small class="text-muted"><?= htmlspecialchars($payment['payment_id']) ?></small>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php
                                                    $statusClasses = [
                                                        'pending' => 'bg-warning text-dark',
                                                        'completed' => 'bg-success',
                                                        'failed' => 'bg-danger',
                                                        'refunded' => 'bg-info'
                                                    ];
                                                    $statusTexts = [
                                                        'pending' => 'Ожидание',
                                                        'completed' => 'Завершен',
                                                        'failed' => 'Неудача',
                                                        'refunded' => 'Возврат'
                                                    ];
                                                    ?>
                                                    <span class="badge <?= $statusClasses[$payment['status']] ?>">
                                                        <?= $statusTexts[$payment['status']] ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div><?= date('d.m.Y', strtotime($payment['created_at'])) ?></div>
                                                    <small class="text-muted"><?= date('H:i', strtotime($payment['created_at'])) ?></small>
                                                </td>
                                                <td>
                                                    <div><?= date('d.m.Y', strtotime($payment['updated_at'])) ?></div>
                                                    <small class="text-muted"><?= date('H:i', strtotime($payment['updated_at'])) ?></small>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>