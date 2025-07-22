<?php
if (!isAdmin()) {
    redirect(SITE_URL . '/admin/login.php');
}

// Отримуємо статистику
$db = Database::getInstance();

// Загальна статистика
$totalUsers = $db->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$totalAds = $db->query("SELECT COUNT(*) as count FROM ads")->fetch_assoc()['count'];
$activeAds = $db->query("SELECT COUNT(*) as count FROM ads WHERE status = 'active'")->fetch_assoc()['count'];
$totalViews = $db->query("SELECT SUM(views_count) as total FROM ads")->fetch_assoc()['total'] ?? 0;

// Статистика за останній місяць
$lastMonthUsers = $db->query("SELECT COUNT(*) as count FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)")->fetch_assoc()['count'];
$lastMonthAds = $db->query("SELECT COUNT(*) as count FROM ads WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)")->fetch_assoc()['count'];

// Популярні категорії
$popularCategories = $db->query("
    SELECT c.name, COUNT(a.id) as ads_count 
    FROM categories c 
    LEFT JOIN ads a ON c.id = a.category_id 
    WHERE c.parent_id IS NULL 
    GROUP BY c.id, c.name 
    ORDER BY ads_count DESC 
    LIMIT 5
");

// Останні оголошення
$recentAds = $db->query("
    SELECT a.id, a.title, a.status, a.created_at, u.username, c.name as category
    FROM ads a
    JOIN users u ON a.user_id = u.id
    JOIN categories c ON a.category_id = c.id
    ORDER BY a.created_at DESC
    LIMIT 10
");

// Останні користувачі
$recentUsers = $db->query("
    SELECT id, username, email, role, status, created_at
    FROM users
    ORDER BY created_at DESC
    LIMIT 10
");
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Dashboard</h1>
            <p class="text-muted">Ласкаво просимо в адмін-панель <?php echo SITE_NAME; ?></p>
        </div>
        <div>
            <span class="text-muted">Останній вхід: <?php echo formatDate($_SESSION['last_login'] ?? date('Y-m-d H:i:s')); ?></span>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="gradient-bg rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 60px; height: 60px;">
                            <i class="fas fa-users fa-lg text-white"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0"><?php echo number_format($totalUsers); ?></h5>
                            <p class="text-muted mb-0">Всього користувачів</p>
                            <small class="text-success">
                                <i class="fas fa-arrow-up"></i> +<?php echo $lastMonthUsers; ?> за місяць
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="gradient-bg rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 60px; height: 60px;">
                            <i class="fas fa-bullhorn fa-lg text-white"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0"><?php echo number_format($totalAds); ?></h5>
                            <p class="text-muted mb-0">Всього оголошень</p>
                            <small class="text-success">
                                <i class="fas fa-arrow-up"></i> +<?php echo $lastMonthAds; ?> за місяць
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="gradient-bg rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 60px; height: 60px;">
                            <i class="fas fa-check-circle fa-lg text-white"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0"><?php echo number_format($activeAds); ?></h5>
                            <p class="text-muted mb-0">Активні оголошення</p>
                            <small class="text-info">
                                <?php echo round($activeAds / $totalAds * 100, 1); ?>% від загальної кількості
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="gradient-bg rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 60px; height: 60px;">
                            <i class="fas fa-eye fa-lg text-white"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0"><?php echo number_format($totalViews); ?></h5>
                            <p class="text-muted mb-0">Всього переглядів</p>
                            <small class="text-primary">
                                Середньо <?php echo $totalAds > 0 ? round($totalViews / $totalAds, 1) : 0; ?> на оголошення
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Popular Categories -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header gradient-bg text-white">
                    <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Популярні категорії</h6>
                </div>
                <div class="card-body">
                    <?php while ($category = $popularCategories->fetch_assoc()): ?>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span><?php echo sanitize($category['name']); ?></span>
                            <div class="d-flex align-items-center">
                                <span class="badge gradient-bg text-white me-2">
                                    <?php echo number_format($category['ads_count']); ?>
                                </span>
                                <div class="progress" style="width: 100px; height: 8px;">
                                    <div class="progress-bar gradient-bg" 
                                         style="width: <?php echo $totalAds > 0 ? ($category['ads_count'] / $totalAds * 100) : 0; ?>%"></div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header gradient-bg text-white">
                    <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Швидкі дії</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <a href="<?php echo SITE_URL; ?>/admin/settings" class="btn btn-outline-primary w-100">
                                <i class="fas fa-cog d-block mb-2"></i>
                                Налаштування
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="<?php echo SITE_URL; ?>/admin/theme" class="btn btn-outline-success w-100">
                                <i class="fas fa-palette d-block mb-2"></i>
                                Тема
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="<?php echo SITE_URL; ?>/admin/users" class="btn btn-outline-warning w-100">
                                <i class="fas fa-users d-block mb-2"></i>
                                Користувачі
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="<?php echo SITE_URL; ?>/admin/ads" class="btn btn-outline-info w-100">
                                <i class="fas fa-bullhorn d-block mb-2"></i>
                                Оголошення
                            </a>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-grid">
                        <a href="<?php echo SITE_URL; ?>" class="btn gradient-bg text-white" target="_blank">
                            <i class="fas fa-external-link-alt me-2"></i>Переглянути сайт
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Ads -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header gradient-bg text-white">
                    <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Останні оголошення</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Назва</th>
                                    <th>Автор</th>
                                    <th>Категорія</th>
                                    <th>Статус</th>
                                    <th>Дата</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($ad = $recentAds->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo $ad['id']; ?></td>
                                        <td><?php echo truncateText(sanitize($ad['title']), 50); ?></td>
                                        <td><?php echo sanitize($ad['username']); ?></td>
                                        <td><?php echo sanitize($ad['category']); ?></td>
                                        <td>
                                            <span class="badge <?php echo $ad['status'] === 'active' ? 'bg-success' : 'bg-secondary'; ?>">
                                                <?php echo ucfirst($ad['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo formatDate($ad['created_at'], 'd.m.Y'); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header gradient-bg text-white">
                    <h6 class="mb-0"><i class="fas fa-user-plus me-2"></i>Нові користувачі</h6>
                </div>
                <div class="card-body">
                    <?php while ($user = $recentUsers->fetch_assoc()): ?>
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 40px; height: 40px;">
                                <i class="fas fa-user text-muted"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0"><?php echo sanitize($user['username']); ?></h6>
                                <small class="text-muted"><?php echo formatDate($user['created_at'], 'd.m.Y'); ?></small>
                                <span class="badge <?php echo $user['role'] === 'admin' ? 'bg-danger' : 'bg-primary'; ?> ms-2">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.progress {
    background-color: rgba(0, 0, 0, 0.1);
}
</style>
