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

// Обработка действий
$message = '';
if ($_POST) {
    $action = $_POST['action'] ?? '';
    $userId = $_POST['user_id'] ?? 0;
    
    switch ($action) {
        case 'ban_user':
            $user->banUser($userId, true);
            $message = '<div class="alert alert-warning">Пользователь заблокирован</div>';
            break;
            
        case 'unban_user':
            $user->banUser($userId, false);
            $message = '<div class="alert alert-success">Пользователь разблокирован</div>';
            break;
            
        case 'verify_user':
            $user->setVerified($userId, true);
            $message = '<div class="alert alert-success">Пользователь верифицирован</div>';
            break;
            
        case 'unverify_user':
            $user->setVerified($userId, false);
            $message = '<div class="alert alert-info">Верификация отозвана</div>';
            break;
    }
}

// Получаем всех пользователей с статистикой
$allUsers = $db->fetchAll('
    SELECT u.*,
           COUNT(d.id) as deals_count,
           SUM(CASE WHEN d.status = "completed" THEN d.amount ELSE 0 END) as total_volume,
           AVG(r.rating) as avg_rating
    FROM users u
    LEFT JOIN deals d ON (u.telegram_id = d.seller_id OR u.telegram_id = d.buyer_id)
    LEFT JOIN reviews r ON u.telegram_id = r.reviewed_id
    GROUP BY u.id
    ORDER BY u.created_at DESC
');

// Фильтрация
$statusFilter = $_GET['status'] ?? 'all';
if ($statusFilter !== 'all') {
    $allUsers = array_filter($allUsers, function($userData) use ($statusFilter) {
        switch ($statusFilter) {
            case 'banned':
                return $userData['is_banned'];
            case 'verified':
                return $userData['is_verified'];
            case 'active':
                return !$userData['is_banned'] && $userData['deals_count'] > 0;
            default:
                return true;
        }
    });
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Пользователи - Game Garant by Неадекват</title>
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
        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
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
                    <a class="nav-link active" href="users.php">
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
                    <h1><i class="bi bi-people me-2"></i>Управление пользователями</h1>
                </div>

                <?= $message ?>

                <!-- Статистика пользователей -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="bi bi-people fs-1 text-primary mb-2"></i>
                                <h3><?= count($allUsers) ?></h3>
                                <p class="mb-0 text-muted">Всего пользователей</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="bi bi-check-circle fs-1 text-success mb-2"></i>
                                <h3><?= count(array_filter($allUsers, fn($u) => $u['is_verified'])) ?></h3>
                                <p class="mb-0 text-muted">Верифицированы</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="bi bi-activity fs-1 text-info mb-2"></i>
                                <h3><?= count(array_filter($allUsers, fn($u) => $u['deals_count'] > 0)) ?></h3>
                                <p class="mb-0 text-muted">Активные</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="bi bi-ban fs-1 text-danger mb-2"></i>
                                <h3><?= count(array_filter($allUsers, fn($u) => $u['is_banned'])) ?></h3>
                                <p class="mb-0 text-muted">Заблокированы</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Фильтры -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5 class="mb-0">Фильтр пользователей:</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="btn-group" role="group">
                                    <a href="?status=all" class="btn <?= $statusFilter === 'all' ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">
                                        Все
                                    </a>
                                    <a href="?status=active" class="btn <?= $statusFilter === 'active' ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">
                                        Активные
                                    </a>
                                    <a href="?status=verified" class="btn <?= $statusFilter === 'verified' ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">
                                        Верифицированы
                                    </a>
                                    <a href="?status=banned" class="btn <?= $statusFilter === 'banned' ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">
                                        Заблокированы
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Список пользователей -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-list me-2"></i>
                            Пользователи (<?= count($allUsers) ?>)
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($allUsers)): ?>
                            <div class="text-center p-4">
                                <i class="bi bi-person-x fs-1 text-muted"></i>
                                <p class="text-muted mt-2">Пользователи не найдены</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Пользователь</th>
                                            <th>Контакты</th>
                                            <th>Статистика</th>
                                            <th>Баланс</th>
                                            <th>Рейтинг</th>
                                            <th>Статус</th>
                                            <th>Регистрация</th>
                                            <th>Действия</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($allUsers as $userData): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="user-avatar me-3">
                                                            <?= strtoupper(substr($userData['first_name'] ?? 'U', 0, 1)) ?>
                                                        </div>
                                                        <div>
                                                            <div class="fw-bold">
                                                                <?= htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']) ?>
                                                            </div>
                                                            <small class="text-muted">ID: <?= $userData['telegram_id'] ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>@<?= htmlspecialchars($userData['username']) ?></div>
                                                    <?php if ($userData['phone']): ?>
                                                        <small class="text-muted"><?= htmlspecialchars($userData['phone']) ?></small>
                                                    <?php endif; ?>
                                                    <?php if ($userData['email']): ?>
                                                        <br><small class="text-muted"><?= htmlspecialchars($userData['email']) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div><strong><?= $userData['deals_count'] ?></strong> сделок</div>
                                                    <small class="text-muted">
                                                        Оборот: <?= number_format($userData['total_volume'] ?: 0, 0) ?> ₽
                                                    </small>
                                                </td>
                                                <td>
                                                    <span class="fw-bold text-success">
                                                        <?= number_format($userData['balance'], 2) ?> ₽
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($userData['avg_rating']): ?>
                                                        <div class="d-flex align-items-center">
                                                            <span class="me-1"><?= number_format($userData['avg_rating'], 1) ?></span>
                                                            <div>
                                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                    <i class="bi bi-star<?= $i <= round($userData['avg_rating']) ? '-fill text-warning' : ' text-muted' ?> small"></i>
                                                                <?php endfor; ?>
                                                            </div>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="text-muted">Нет оценок</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div>
                                                        <?php if ($userData['is_verified']): ?>
                                                            <span class="badge bg-success mb-1">
                                                                <i class="bi bi-check-circle me-1"></i>Верифицирован
                                                            </span>
                                                        <?php endif; ?>
                                                        
                                                        <?php if ($userData['is_banned']): ?>
                                                            <span class="badge bg-danger mb-1">
                                                                <i class="bi bi-ban me-1"></i>Заблокирован
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="badge bg-success mb-1">
                                                                <i class="bi bi-check-circle me-1"></i>Активен
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div><?= date('d.m.Y', strtotime($userData['created_at'])) ?></div>
                                                    <small class="text-muted"><?= date('H:i', strtotime($userData['created_at'])) ?></small>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-info" onclick="viewUser(<?= $userData['telegram_id'] ?>)">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                        
                                                        <?php if ($userData['is_banned']): ?>
                                                            <button class="btn btn-outline-success" onclick="unbanUser(<?= $userData['telegram_id'] ?>, '<?= htmlspecialchars($userData['first_name']) ?>')">
                                                                <i class="bi bi-unlock"></i>
                                                            </button>
                                                        <?php else: ?>
                                                            <button class="btn btn-outline-danger" onclick="banUser(<?= $userData['telegram_id'] ?>, '<?= htmlspecialchars($userData['first_name']) ?>')">
                                                                <i class="bi bi-ban"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                        
                                                        <?php if ($userData['is_verified']): ?>
                                                            <button class="btn btn-outline-warning" onclick="unverifyUser(<?= $userData['telegram_id'] ?>, '<?= htmlspecialchars($userData['first_name']) ?>')">
                                                                <i class="bi bi-x-circle"></i>
                                                            </button>
                                                        <?php else: ?>
                                                            <button class="btn btn-outline-success" onclick="verifyUser(<?= $userData['telegram_id'] ?>, '<?= htmlspecialchars($userData['first_name']) ?>')">
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
    <div class="modal fade" id="actionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="actionModalTitle">Действие</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" id="modal_action">
                        <input type="hidden" name="user_id" id="modal_user_id">
                        <p id="modal_message">Подтвердите действие</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn" id="modal_submit_btn">Подтвердить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showModal(action, userId, userName, title, message, btnClass) {
            document.getElementById('modal_action').value = action;
            document.getElementById('modal_user_id').value = userId;
            document.getElementById('actionModalTitle').textContent = title;
            document.getElementById('modal_message').textContent = message.replace('{name}', userName);
            
            const submitBtn = document.getElementById('modal_submit_btn');
            submitBtn.className = 'btn ' + btnClass;
            
            new bootstrap.Modal(document.getElementById('actionModal')).show();
        }

        function banUser(userId, userName) {
            showModal('ban_user', userId, userName, 
                'Блокировка пользователя', 
                'Вы уверены, что хотите заблокировать пользователя {name}?',
                'btn-danger'
            );
        }

        function unbanUser(userId, userName) {
            showModal('unban_user', userId, userName, 
                'Разблокировка пользователя', 
                'Вы уверены, что хотите разблокировать пользователя {name}?',
                'btn-success'
            );
        }

        function verifyUser(userId, userName) {
            showModal('verify_user', userId, userName, 
                'Верификация пользователя', 
                'Вы уверены, что хотите верифицировать пользователя {name}?',
                'btn-success'
            );
        }

        function unverifyUser(userId, userName) {
            showModal('unverify_user', userId, userName, 
                'Отзыв верификации', 
                'Вы уверены, что хотите отозвать верификацию пользователя {name}?',
                'btn-warning'
            );
        }

        function viewUser(userId) {
            // Здесь можно добавить просмотр деталей пользователя
            alert('Просмотр пользователя #' + userId + ' будет добавлен в следующей версии');
        }
    </script>
</body>
</html>