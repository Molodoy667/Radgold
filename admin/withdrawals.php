<?php
session_start();
require_once '../config.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';

$config = require '../config.php';

// Проверка авторизации
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$db = new Database($config['database']);
$user = new User($db);

$message = '';

// Обработка действий
if ($_POST) {
    $action = $_POST['action'] ?? '';
    $requestId = $_POST['request_id'] ?? 0;
    $comment = $_POST['comment'] ?? '';
    
    if ($action === 'approve' && $requestId) {
        // Одобряем заявку на вывод
        $request = $db->fetch('SELECT * FROM withdrawal_requests WHERE id = ?', [$requestId]);
        if ($request && $request['status'] === 'pending') {
            $db->beginTransaction();
            try {
                // Списываем средства с баланса пользователя
                $db->query(
                    'UPDATE users SET balance = balance - ? WHERE telegram_id = ?',
                    [$request['amount'], $request['user_id']]
                );
                
                // Обновляем статус заявки
                $db->update('withdrawal_requests', [
                    'status' => 'approved',
                    'admin_comment' => $comment,
                    'updated_at' => date('Y-m-d H:i:s')
                ], 'id = ?', [$requestId]);
                
                // Логируем действие
                $db->insert('admin_logs', [
                    'admin_id' => 0,
                    'action' => 'approve_withdrawal',
                    'target_type' => 'withdrawal',
                    'target_id' => $requestId,
                    'details' => "Одобрена заявка на вывод {$request['amount']} руб.",
                    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                
                $db->commit();
                $message = '<div class="alert alert-success">Заявка на вывод одобрена!</div>';
            } catch (Exception $e) {
                $db->rollback();
                $message = '<div class="alert alert-danger">Ошибка при одобрении заявки</div>';
            }
        }
    } elseif ($action === 'reject' && $requestId) {
        // Отклоняем заявку
        $request = $db->fetch('SELECT * FROM withdrawal_requests WHERE id = ?', [$requestId]);
        if ($request && $request['status'] === 'pending') {
            $db->update('withdrawal_requests', [
                'status' => 'rejected',
                'admin_comment' => $comment,
                'updated_at' => date('Y-m-d H:i:s')
            ], 'id = ?', [$requestId]);
            
            // Логируем действие
            $db->insert('admin_logs', [
                'admin_id' => 0,
                'action' => 'reject_withdrawal',
                'target_type' => 'withdrawal',
                'target_id' => $requestId,
                'details' => "Отклонена заявка на вывод {$request['amount']} руб. Причина: {$comment}",
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            $message = '<div class="alert alert-warning">Заявка отклонена</div>';
        }
    } elseif ($action === 'complete' && $requestId) {
        // Помечаем как выполненную
        $db->update('withdrawal_requests', [
            'status' => 'completed',
            'admin_comment' => $comment,
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = ?', [$requestId]);
        
        $message = '<div class="alert alert-success">Заявка помечена как выполненная</div>';
    }
}

// Получаем все заявки на вывод
$withdrawals = $db->fetchAll('
    SELECT wr.*, u.first_name, u.last_name, u.username, u.balance
    FROM withdrawal_requests wr
    LEFT JOIN users u ON wr.user_id = u.telegram_id
    ORDER BY wr.created_at DESC
');

// Статистика
$stats = $db->fetch('
    SELECT 
        COUNT(*) as total_requests,
        COUNT(CASE WHEN status = "pending" THEN 1 END) as pending_requests,
        COUNT(CASE WHEN status = "approved" THEN 1 END) as approved_requests,
        COUNT(CASE WHEN status = "completed" THEN 1 END) as completed_requests,
        SUM(CASE WHEN status = "completed" THEN amount ELSE 0 END) as total_withdrawn
    FROM withdrawal_requests
');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заявки на вывод - Game Garant by Неадекват</title>
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
        .status-pending { color: #ffc107; }
        .status-approved { color: #28a745; }
        .status-rejected { color: #dc3545; }
        .status-completed { color: #6c757d; }
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
                    <a class="nav-link active" href="withdrawals.php">
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
                    <h1><i class="bi bi-cash-stack me-2"></i>Заявки на вывод средств</h1>
                </div>

                <?= $message ?>

                <!-- Статистика -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="bi bi-list-ul fs-1 text-primary"></i>
                                <h5 class="card-title mt-2">Всего заявок</h5>
                                <h3 class="text-primary"><?= $stats['total_requests'] ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="bi bi-clock fs-1 text-warning"></i>
                                <h5 class="card-title mt-2">Ожидают</h5>
                                <h3 class="text-warning"><?= $stats['pending_requests'] ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="bi bi-check-circle fs-1 text-success"></i>
                                <h5 class="card-title mt-2">Выполнено</h5>
                                <h3 class="text-success"><?= $stats['completed_requests'] ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="bi bi-currency-dollar fs-1 text-info"></i>
                                <h5 class="card-title mt-2">Выведено</h5>
                                <h3 class="text-info"><?= number_format($stats['total_withdrawn'], 2) ?> ₽</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Список заявок -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Все заявки на вывод</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($withdrawals)): ?>
                            <div class="text-center py-4">
                                <i class="bi bi-inbox fs-1 text-muted"></i>
                                <p class="text-muted mt-2">Заявок на вывод пока нет</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Пользователь</th>
                                            <th>Сумма</th>
                                            <th>Статус</th>
                                            <th>Дата создания</th>
                                            <th>Комментарий</th>
                                            <th>Действия</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($withdrawals as $withdrawal): ?>
                                            <tr>
                                                <td><strong>#<?= $withdrawal['id'] ?></strong></td>
                                                <td>
                                                    <div>
                                                        <strong><?= htmlspecialchars($withdrawal['first_name'] . ' ' . $withdrawal['last_name']) ?></strong>
                                                        <?php if ($withdrawal['username']): ?>
                                                            <br><small class="text-muted">@<?= htmlspecialchars($withdrawal['username']) ?></small>
                                                        <?php endif; ?>
                                                        <br><small class="text-info">Баланс: <?= number_format($withdrawal['balance'], 2) ?> ₽</small>
                                                    </div>
                                                </td>
                                                <td><strong><?= number_format($withdrawal['amount'], 2) ?> ₽</strong></td>
                                                <td>
                                                    <?php
                                                    $statusClass = 'status-' . $withdrawal['status'];
                                                    $statusText = [
                                                        'pending' => 'Ожидает',
                                                        'approved' => 'Одобрена',
                                                        'rejected' => 'Отклонена',
                                                        'completed' => 'Выполнена'
                                                    ][$withdrawal['status']] ?? $withdrawal['status'];
                                                    ?>
                                                    <span class="<?= $statusClass ?>">
                                                        <i class="bi bi-circle-fill me-1"></i><?= $statusText ?>
                                                    </span>
                                                </td>
                                                <td><?= date('d.m.Y H:i', strtotime($withdrawal['created_at'])) ?></td>
                                                <td>
                                                    <?php if ($withdrawal['admin_comment']): ?>
                                                        <small><?= htmlspecialchars($withdrawal['admin_comment']) ?></small>
                                                    <?php else: ?>
                                                        <small class="text-muted">—</small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($withdrawal['status'] === 'pending'): ?>
                                                        <button class="btn btn-success btn-sm" onclick="approveWithdrawal(<?= $withdrawal['id'] ?>)">
                                                            <i class="bi bi-check"></i> Одобрить
                                                        </button>
                                                        <button class="btn btn-danger btn-sm" onclick="rejectWithdrawal(<?= $withdrawal['id'] ?>)">
                                                            <i class="bi bi-x"></i> Отклонить
                                                        </button>
                                                    <?php elseif ($withdrawal['status'] === 'approved'): ?>
                                                        <button class="btn btn-info btn-sm" onclick="completeWithdrawal(<?= $withdrawal['id'] ?>)">
                                                            <i class="bi bi-check-all"></i> Выполнено
                                                        </button>
                                                    <?php else: ?>
                                                        <small class="text-muted">—</small>
                                                    <?php endif; ?>
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
    <div class="modal fade" id="approveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Одобрить заявку на вывод</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="approve">
                        <input type="hidden" name="request_id" id="approve_request_id">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Внимание!</strong> Средства будут списаны с баланса пользователя.
                        </div>
                        <div class="mb-3">
                            <label for="approve_comment" class="form-label">Комментарий (необязательно)</label>
                            <textarea class="form-control" name="comment" id="approve_comment" rows="3" 
                                      placeholder="Укажите способ вывода или другие детали..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-success">Одобрить заявку</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Отклонить заявку</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="reject">
                        <input type="hidden" name="request_id" id="reject_request_id">
                        <div class="mb-3">
                            <label for="reject_comment" class="form-label">Причина отклонения *</label>
                            <textarea class="form-control" name="comment" id="reject_comment" rows="3" required
                                      placeholder="Укажите причину отклонения заявки..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-danger">Отклонить заявку</button>
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
                        <h5 class="modal-title">Пометить как выполненную</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="complete">
                        <input type="hidden" name="request_id" id="complete_request_id">
                        <div class="mb-3">
                            <label for="complete_comment" class="form-label">Комментарий</label>
                            <textarea class="form-control" name="comment" id="complete_comment" rows="3"
                                      placeholder="Детали выполнения заявки..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-info">Пометить как выполненную</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function approveWithdrawal(requestId) {
            document.getElementById('approve_request_id').value = requestId;
            new bootstrap.Modal(document.getElementById('approveModal')).show();
        }

        function rejectWithdrawal(requestId) {
            document.getElementById('reject_request_id').value = requestId;
            new bootstrap.Modal(document.getElementById('rejectModal')).show();
        }

        function completeWithdrawal(requestId) {
            document.getElementById('complete_request_id').value = requestId;
            new bootstrap.Modal(document.getElementById('completeModal')).show();
        }
    </script>
</body>
</html>