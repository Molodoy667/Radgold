<?php
session_start();
require_once '../config.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';
require_once '../classes/Deal.php';

$config = require '../config.php';

// Проверка авторизации
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$db = new Database($config['database']);
$deal = new Deal($db);
$user = new User($db);

// Получаем статистику
$dealStats = $deal->getDealStats();
$totalUsers = $db->fetch('SELECT COUNT(*) as count FROM users')['count'];
$activeDeals = $deal->getActiveDeals();
$recentUsers = $db->fetchAll('SELECT * FROM users ORDER BY created_at DESC LIMIT 10');

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель - Escrow Bot</title>
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
                    <a class="nav-link active" href="index.php">
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
                    <h1>Панель управления</h1>
                    <div class="text-muted">
                        <i class="bi bi-calendar me-1"></i>
                        <?= date('d.m.Y H:i') ?>
                    </div>
                </div>

                <!-- Статистика -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card">
                            <div class="card-body text-center">
                                <i class="bi bi-briefcase fs-1 mb-2"></i>
                                <h3><?= $dealStats['total_deals'] ?></h3>
                                <p class="mb-0">Всего сделок</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card-success">
                            <div class="card-body text-center">
                                <i class="bi bi-check-circle fs-1 mb-2"></i>
                                <h3><?= $dealStats['completed_deals'] ?></h3>
                                <p class="mb-0">Завершено</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card-warning">
                            <div class="card-body text-center">
                                <i class="bi bi-exclamation-triangle fs-1 mb-2"></i>
                                <h3><?= $dealStats['disputed_deals'] ?></h3>
                                <p class="mb-0">Споры</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card-danger">
                            <div class="card-body text-center">
                                <i class="bi bi-people fs-1 mb-2"></i>
                                <h3><?= $totalUsers ?></h3>
                                <p class="mb-0">Пользователей</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Финансовая статистика -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Финансы</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="text-center">
                                            <h4 class="text-success"><?= number_format($dealStats['total_volume'], 2) ?> ₽</h4>
                                            <small class="text-muted">Общий оборот</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center">
                                            <h4 class="text-primary"><?= number_format($dealStats['total_commission'], 2) ?> ₽</h4>
                                            <small class="text-muted">Комиссия</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-activity me-2"></i>Активность</h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center">
                                    <h4 class="text-info"><?= count($activeDeals) ?></h4>
                                    <small class="text-muted">Активных сделок</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Последние сделки -->
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Последние сделки</h5>
                                <a href="deals.php" class="btn btn-sm btn-outline-primary">Все сделки</a>
                            </div>
                            <div class="card-body">
                                <?php if (empty($activeDeals)): ?>
                                    <p class="text-muted text-center">Активных сделок нет</p>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>№</th>
                                                    <th>Название</th>
                                                    <th>Сумма</th>
                                                    <th>Статус</th>
                                                    <th>Дата</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach (array_slice($activeDeals, 0, 5) as $deal): ?>
                                                    <tr>
                                                        <td><strong><?= $deal['deal_number'] ?></strong></td>
                                                        <td><?= htmlspecialchars($deal['title']) ?></td>
                                                        <td><?= number_format($deal['amount'], 2) ?> ₽</td>
                                                        <td>
                                                            <?php
                                                            $statusClasses = [
                                                                'created' => 'bg-secondary',
                                                                'paid' => 'bg-warning',
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
                                                            <span class="badge <?= $statusClasses[$deal['status']] ?>">
                                                                <?= $statusTexts[$deal['status']] ?>
                                                            </span>
                                                        </td>
                                                        <td><?= date('d.m.Y', strtotime($deal['created_at'])) ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Новые пользователи -->
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="bi bi-person-plus me-2"></i>Новые пользователи</h5>
                                <a href="users.php" class="btn btn-sm btn-outline-primary">Все</a>
                            </div>
                            <div class="card-body">
                                <?php foreach (array_slice($recentUsers, 0, 5) as $recentUser): ?>
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                            <i class="bi bi-person text-white"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold"><?= htmlspecialchars($recentUser['first_name'] . ' ' . $recentUser['last_name']) ?></div>
                                            <small class="text-muted"><?= date('d.m.Y', strtotime($recentUser['created_at'])) ?></small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
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