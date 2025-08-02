<?php
session_start();
require_once '../config.php';
require_once '../classes/Database.php';
require_once '../classes/Deal.php';
require_once '../classes/User.php';

$config = require '../config.php';

// Проверка авторизации
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$db = new Database($config['database']);
$deal = new Deal($db);
$user = new User($db);

// Обработка действий
$message = '';
if ($_POST) {
    $action = $_POST['action'] ?? '';
    $dealId = $_POST['deal_id'] ?? 0;
    
    switch ($action) {
        case 'cancel_deal':
            $reason = $_POST['reason'] ?? 'Отменено администратором';
            if ($deal->cancelDeal($dealId, $reason)) {
                $message = '<div class="alert alert-success">Сделка отменена</div>';
            } else {
                $message = '<div class="alert alert-danger">Ошибка отмены сделки</div>';
            }
            break;
            
        case 'complete_deal':
            if ($deal->completeDeal($dealId)) {
                $message = '<div class="alert alert-success">Сделка завершена</div>';
            } else {
                $message = '<div class="alert alert-danger">Ошибка завершения сделки</div>';
            }
            break;
    }
}

// Получаем все сделки
$allDeals = $db->fetchAll('
    SELECT d.*, 
           u1.first_name as seller_name, u1.last_name as seller_lastname, u1.username as seller_username,
           u2.first_name as buyer_name, u2.last_name as buyer_lastname, u2.username as buyer_username
    FROM deals d
    LEFT JOIN users u1 ON d.seller_id = u1.telegram_id
    LEFT JOIN users u2 ON d.buyer_id = u2.telegram_id
    ORDER BY d.created_at DESC
');

// Фильтрация по статусу
$statusFilter = $_GET['status'] ?? 'all';
if ($statusFilter !== 'all') {
    $allDeals = array_filter($allDeals, function($deal) use ($statusFilter) {
        return $deal['status'] === $statusFilter;
    });
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сделки - Escrow Bot</title>
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
        .status-badge {
            font-size: 0.8em;
            padding: 0.4em 0.8em;
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
                    <a class="nav-link active" href="deals.php">
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
                    <h1><i class="bi bi-briefcase me-2"></i>Управление сделками</h1>
                </div>

                <?= $message ?>

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
                                    <a href="?status=created" class="btn <?= $statusFilter === 'created' ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">
                                        Созданы
                                    </a>
                                    <a href="?status=paid" class="btn <?= $statusFilter === 'paid' ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">
                                        Оплачены
                                    </a>
                                    <a href="?status=disputed" class="btn <?= $statusFilter === 'disputed' ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">
                                        Споры
                                    </a>
                                    <a href="?status=completed" class="btn <?= $statusFilter === 'completed' ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">
                                        Завершены
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Список сделок -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-list me-2"></i>
                            Сделки (<?= count($allDeals) ?>)
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($allDeals)): ?>
                            <div class="text-center p-4">
                                <i class="bi bi-inbox fs-1 text-muted"></i>
                                <p class="text-muted mt-2">Сделок не найдено</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>№ Сделки</th>
                                            <th>Название</th>
                                            <th>Сумма</th>
                                            <th>Продавец</th>
                                            <th>Покупатель</th>
                                            <th>Статус</th>
                                            <th>Дата</th>
                                            <th>Действия</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($allDeals as $dealData): ?>
                                            <tr>
                                                <td><strong><?= $dealData['deal_number'] ?></strong></td>
                                                <td>
                                                    <div class="fw-bold"><?= htmlspecialchars($dealData['title']) ?></div>
                                                    <?php if ($dealData['description']): ?>
                                                        <small class="text-muted"><?= htmlspecialchars(substr($dealData['description'], 0, 50)) ?>...</small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="fw-bold"><?= number_format($dealData['amount'], 2) ?> ₽</div>
                                                    <small class="text-muted">Комиссия: <?= number_format($dealData['commission'], 2) ?> ₽</small>
                                                </td>
                                                <td>
                                                    <div><?= htmlspecialchars($dealData['seller_name'] . ' ' . $dealData['seller_lastname']) ?></div>
                                                    <small class="text-muted">@<?= $dealData['seller_username'] ?></small>
                                                </td>
                                                <td>
                                                    <div><?= htmlspecialchars($dealData['buyer_name'] . ' ' . $dealData['buyer_lastname']) ?></div>
                                                    <small class="text-muted">@<?= $dealData['buyer_username'] ?></small>
                                                </td>
                                                <td>
                                                    <?php
                                                    $statusClasses = [
                                                        'created' => 'bg-secondary',
                                                        'paid' => 'bg-warning text-dark',
                                                        'confirmed' => 'bg-info',
                                                        'completed' => 'bg-success',
                                                        'disputed' => 'bg-danger',
                                                        'cancelled' => 'bg-dark'
                                                    ];
                                                    $statusTexts = [
                                                        'created' => 'Создана',
                                                        'paid' => 'Оплачена',
                                                        'confirmed' => 'Подтверждена',
                                                        'completed' => 'Завершена',
                                                        'disputed' => 'Спор',
                                                        'cancelled' => 'Отменена'
                                                    ];
                                                    ?>
                                                    <span class="badge status-badge <?= $statusClasses[$dealData['status']] ?>">
                                                        <?= $statusTexts[$dealData['status']] ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div><?= date('d.m.Y', strtotime($dealData['created_at'])) ?></div>
                                                    <small class="text-muted"><?= date('H:i', strtotime($dealData['created_at'])) ?></small>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-primary" onclick="viewDeal(<?= $dealData['id'] ?>)">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                        <?php if (in_array($dealData['status'], ['created', 'paid', 'confirmed'])): ?>
                                                            <button class="btn btn-outline-danger" onclick="cancelDeal(<?= $dealData['id'] ?>, '<?= $dealData['deal_number'] ?>')">
                                                                <i class="bi bi-x-circle"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                        <?php if ($dealData['status'] === 'confirmed'): ?>
                                                            <button class="btn btn-outline-success" onclick="completeDeal(<?= $dealData['id'] ?>, '<?= $dealData['deal_number'] ?>')">
                                                                <i class="bi bi-check-circle"></i>
                                                            </button>
                                                        <?php endif; ?>
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

    <!-- Модальные окна -->
    <div class="modal fade" id="cancelModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Отмена сделки</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="cancel_deal">
                        <input type="hidden" name="deal_id" id="cancel_deal_id">
                        <p>Вы уверены, что хотите отменить сделку <strong id="cancel_deal_number"></strong>?</p>
                        <div class="mb-3">
                            <label for="cancel_reason" class="form-label">Причина отмены:</label>
                            <textarea class="form-control" name="reason" id="cancel_reason" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-danger">Отменить сделку</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="completeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Завершение сделки</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="complete_deal">
                        <input type="hidden" name="deal_id" id="complete_deal_id">
                        <p>Вы уверены, что хотите завершить сделку <strong id="complete_deal_number"></strong>?</p>
                        <p class="text-muted">Средства будут переведены продавцу.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-success">Завершить сделку</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function cancelDeal(dealId, dealNumber) {
            document.getElementById('cancel_deal_id').value = dealId;
            document.getElementById('cancel_deal_number').textContent = dealNumber;
            new bootstrap.Modal(document.getElementById('cancelModal')).show();
        }

        function completeDeal(dealId, dealNumber) {
            document.getElementById('complete_deal_id').value = dealId;
            document.getElementById('complete_deal_number').textContent = dealNumber;
            new bootstrap.Modal(document.getElementById('completeModal')).show();
        }

        function viewDeal(dealId) {
            // Здесь можно добавить просмотр деталей сделки
            alert('Просмотр сделки #' + dealId + ' будет добавлен в следующей версии');
        }
    </script>
</body>
</html>