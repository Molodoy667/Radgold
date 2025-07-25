<?php
require_once '../../core/config.php';
require_once '../../core/functions.php';

// Перевірка авторизації
if (!isLoggedIn()) {
    header('Location: login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

$userId = $_SESSION['user_id'];
$userInfo = getUserInfo($userId);

// Отримуємо статистику користувача
$userStats = getUserStats($userId);
$recentAds = getUserAds($userId, 5);
$favoriteAds = getUserFavorites($userId, 5);
$unreadMessages = getUnreadMessagesCount($userId);

include '../../themes/header.php';
?>

<div class="user-dashboard">
    <!-- Breadcrumb -->
    <section class="breadcrumb-section py-3 bg-light">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?php echo getSiteUrl(); ?>">Головна</a></li>
                    <li class="breadcrumb-item active">Особистий кабінет</li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Dashboard Header -->
    <section class="dashboard-header py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center">
                        <div class="user-avatar me-3">
                            <img src="<?php echo $userInfo['avatar'] ? '/images/avatars/' . $userInfo['avatar'] : '/images/default-avatar.svg'; ?>" 
                                 alt="Avatar" class="rounded-circle" width="60" height="60">
                        </div>
                        <div>
                            <h1 class="h3 mb-1">Вітаємо, <?php echo htmlspecialchars($userInfo['first_name'] . ' ' . $userInfo['last_name']); ?>!</h1>
                            <p class="text-muted mb-0">
                                <i class="fas fa-user me-1"></i><?php echo ucfirst($userInfo['role']); ?>
                                <span class="ms-3"><i class="fas fa-calendar me-1"></i>З нами з <?php echo date('d.m.Y', strtotime($userInfo['created_at'])); ?></span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="create-ad.php" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Додати оголошення
                    </a>
                    <a href="profile.php" class="btn btn-outline-secondary">
                        <i class="fas fa-cog me-1"></i>Налаштування
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Dashboard Stats -->
    <section class="dashboard-stats py-4">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card card border-0 h-100">
                        <div class="card-body text-center">
                            <div class="stat-icon mb-3">
                                <i class="fas fa-bullhorn text-primary fa-2x"></i>
                            </div>
                            <h3 class="h2 mb-1 text-primary"><?php echo $userStats['total_ads']; ?></h3>
                            <p class="text-muted mb-0">Всього оголошень</p>
                            <small class="text-success">
                                <i class="fas fa-check-circle me-1"></i><?php echo $userStats['active_ads']; ?> активних
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card card border-0 h-100">
                        <div class="card-body text-center">
                            <div class="stat-icon mb-3">
                                <i class="fas fa-eye text-info fa-2x"></i>
                            </div>
                            <h3 class="h2 mb-1 text-info"><?php echo number_format($userStats['total_views']); ?></h3>
                            <p class="text-muted mb-0">Переглядів</p>
                            <small class="text-info">
                                <i class="fas fa-calendar me-1"></i><?php echo $userStats['views_today']; ?> сьогодні
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card card border-0 h-100">
                        <div class="card-body text-center">
                            <div class="stat-icon mb-3">
                                <i class="fas fa-heart text-danger fa-2x"></i>
                            </div>
                            <h3 class="h2 mb-1 text-danger"><?php echo $userStats['total_favorites']; ?></h3>
                            <p class="text-muted mb-0">В улюблених</p>
                            <small class="text-danger">
                                <i class="fas fa-star me-1"></i><?php echo $userStats['favorites_week']; ?> за тиждень
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card card border-0 h-100">
                        <div class="card-body text-center">
                            <div class="stat-icon mb-3">
                                <i class="fas fa-envelope text-warning fa-2x"></i>
                            </div>
                            <h3 class="h2 mb-1 text-warning"><?php echo $unreadMessages; ?></h3>
                            <p class="text-muted mb-0">Нових повідомлень</p>
                            <small class="text-warning">
                                <i class="fas fa-bell me-1"></i>Перевірити
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Dashboard Content -->
    <section class="dashboard-content py-4">
        <div class="container">
            <div class="row">
                <!-- Sidebar -->
                <div class="col-lg-3 col-md-4 mb-4">
                    <div class="dashboard-sidebar">
                        <div class="sidebar-menu card border-0">
                            <div class="card-header bg-transparent">
                                <h6 class="mb-0">
                                    <i class="fas fa-tachometer-alt me-2"></i>Меню кабінету
                                </h6>
                            </div>
                            <div class="list-group list-group-flush">
                                <a href="#" class="list-group-item list-group-item-action active" data-section="overview">
                                    <i class="fas fa-home me-2"></i>Огляд
                                </a>
                                <a href="#" class="list-group-item list-group-item-action" data-section="my-ads">
                                    <i class="fas fa-bullhorn me-2"></i>Мої оголошення
                                    <span class="badge bg-primary ms-auto"><?php echo $userStats['total_ads']; ?></span>
                                </a>
                                <a href="#" class="list-group-item list-group-item-action" data-section="favorites">
                                    <i class="fas fa-heart me-2"></i>Улюблені
                                    <span class="badge bg-danger ms-auto"><?php echo count($favoriteAds); ?></span>
                                </a>
                                <a href="#" class="list-group-item list-group-item-action" data-section="messages">
                                    <i class="fas fa-envelope me-2"></i>Повідомлення
                                    <?php if ($unreadMessages > 0): ?>
                                        <span class="badge bg-warning ms-auto"><?php echo $unreadMessages; ?></span>
                                    <?php endif; ?>
                                </a>
                                <a href="#" class="list-group-item list-group-item-action" data-section="balance">
                                    <i class="fas fa-wallet me-2"></i>Баланс та оплати
                                </a>
                                <a href="#" class="list-group-item list-group-item-action" data-section="settings">
                                    <i class="fas fa-cog me-2"></i>Налаштування
                                </a>
                                <a href="logout.php" class="list-group-item list-group-item-action text-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i>Вийти
                                </a>
                            </div>
                        </div>

                        <!-- Balance Widget -->
                        <div class="balance-widget card border-0 mt-4">
                            <div class="card-body text-center">
                                <h6 class="card-title">Мій баланс</h6>
                                <h4 class="text-success mb-3"><?php echo number_format($userStats['balance'], 2); ?> грн</h4>
                                <button class="btn btn-success btn-sm w-100" onclick="showTopUpModal()">
                                    <i class="fas fa-plus me-1"></i>Поповнити
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="col-lg-9 col-md-8">
                    <!-- Overview Section -->
                    <div id="overview-section" class="dashboard-section">
                        <div class="row g-4">
                            <!-- Recent Ads -->
                            <div class="col-12">
                                <div class="card border-0">
                                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <i class="fas fa-clock me-2"></i>Останні оголошення
                                        </h6>
                                        <a href="#" class="btn btn-sm btn-outline-primary" data-section="my-ads">
                                            Всі оголошення
                                        </a>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($recentAds)): ?>
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Оголошення</th>
                                                            <th>Статус</th>
                                                            <th>Перегляди</th>
                                                            <th>Дата</th>
                                                            <th>Дії</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($recentAds as $ad): ?>
                                                            <tr>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <img src="<?php echo $ad['main_image'] ? '/images/uploads/' . $ad['main_image'] : '/images/no-image.svg'; ?>" 
                                                                             alt="<?php echo htmlspecialchars($ad['title']); ?>" 
                                                                             class="rounded me-2" width="40" height="40">
                                                                        <div>
                                                                            <div class="fw-bold"><?php echo htmlspecialchars($ad['title']); ?></div>
                                                                            <small class="text-muted"><?php echo $ad['category_name']; ?></small>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <span class="badge bg-<?php echo getStatusColor($ad['status']); ?>">
                                                                        <?php echo getStatusText($ad['status']); ?>
                                                                    </span>
                                                                </td>
                                                                <td><?php echo number_format($ad['views_count']); ?></td>
                                                                <td><?php echo date('d.m.Y', strtotime($ad['created_at'])); ?></td>
                                                                <td>
                                                                    <div class="btn-group btn-group-sm">
                                                                        <a href="/ad/<?php echo $ad['id']; ?>" class="btn btn-outline-primary" title="Переглянути">
                                                                            <i class="fas fa-eye"></i>
                                                                        </a>
                                                                        <a href="edit-ad.php?id=<?php echo $ad['id']; ?>" class="btn btn-outline-secondary" title="Редагувати">
                                                                            <i class="fas fa-edit"></i>
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-center py-4">
                                                <i class="fas fa-bullhorn fa-3x text-muted mb-3"></i>
                                                <h6>У вас поки немає оголошень</h6>
                                                <p class="text-muted">Створіть своє перше оголошення прямо зараз!</p>
                                                <a href="create-ad.php" class="btn btn-primary">
                                                    <i class="fas fa-plus me-1"></i>Створити оголошення
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Activity Chart -->
                            <div class="col-lg-8">
                                <div class="card border-0">
                                    <div class="card-header bg-transparent">
                                        <h6 class="mb-0">
                                            <i class="fas fa-chart-line me-2"></i>Статистика переглядів (30 днів)
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="viewsChart" height="300"></canvas>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div class="col-lg-4">
                                <div class="card border-0">
                                    <div class="card-header bg-transparent">
                                        <h6 class="mb-0">
                                            <i class="fas fa-rocket me-2"></i>Швидкі дії
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <a href="create-ad.php" class="btn btn-primary">
                                                <i class="fas fa-plus me-2"></i>Нове оголошення
                                            </a>
                                            <button class="btn btn-success" onclick="showPromoteModal()">
                                                <i class="fas fa-star me-2"></i>Просувати оголошення
                                            </button>
                                            <a href="#" class="btn btn-info" data-section="messages">
                                                <i class="fas fa-envelope me-2"></i>Повідомлення
                                            </a>
                                            <a href="profile.php" class="btn btn-outline-secondary">
                                                <i class="fas fa-user me-2"></i>Профіль
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- My Ads Section -->
                    <div id="my-ads-section" class="dashboard-section" style="display: none;">
                        <div class="card border-0">
                            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-bullhorn me-2"></i>Мої оголошення
                                </h5>
                                <div>
                                    <select class="form-select form-select-sm me-2" id="adsStatusFilter">
                                        <option value="">Всі статуси</option>
                                        <option value="active">Активні</option>
                                        <option value="pending">На модерації</option>
                                        <option value="rejected">Відхилені</option>
                                        <option value="expired">Прострочені</option>
                                    </select>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="my-ads-content">
                                    <!-- Content will be loaded via AJAX -->
                                    <div class="text-center py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Завантаження...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Other sections will be added dynamically -->
                    <div id="favorites-section" class="dashboard-section" style="display: none;"></div>
                    <div id="messages-section" class="dashboard-section" style="display: none;"></div>
                    <div id="balance-section" class="dashboard-section" style="display: none;"></div>
                    <div id="settings-section" class="dashboard-section" style="display: none;"></div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modals -->
<!-- Top Up Modal -->
<div class="modal fade" id="topUpModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Поповнення балансу</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="topUpForm">
                    <div class="mb-3">
                        <label class="form-label">Сума поповнення</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="topUpAmount" min="10" max="10000" step="10" required>
                            <span class="input-group-text">грн</span>
                        </div>
                        <div class="form-text">Мінімальна сума: 10 грн, максимальна: 10,000 грн</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Спосіб оплати</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="paymentMethod" id="liqpay" value="liqpay" checked>
                                <label class="btn btn-outline-primary w-100" for="liqpay">
                                    <i class="fab fa-cc-visa me-1"></i>LiqPay
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="paymentMethod" id="fondy" value="fondy">
                                <label class="btn btn-outline-primary w-100" for="fondy">
                                    <i class="fab fa-cc-mastercard me-1"></i>Fondy
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
                <button type="button" class="btn btn-success" onclick="processTopUp()">
                    <i class="fas fa-credit-card me-1"></i>Оплатити
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize dashboard
    initializeDashboard();
    loadViewsChart();
    
    // Sidebar navigation
    document.querySelectorAll('[data-section]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const section = this.dataset.section;
            switchSection(section);
        });
    });
});

function initializeDashboard() {
    // Load initial section content
    loadMyAds();
}

function switchSection(section) {
    // Hide all sections
    document.querySelectorAll('.dashboard-section').forEach(el => {
        el.style.display = 'none';
    });
    
    // Remove active class from all menu items
    document.querySelectorAll('.list-group-item').forEach(el => {
        el.classList.remove('active');
    });
    
    // Show selected section
    const sectionEl = document.getElementById(section + '-section');
    if (sectionEl) {
        sectionEl.style.display = 'block';
    }
    
    // Add active class to selected menu item
    const menuItem = document.querySelector(`[data-section="${section}"]`);
    if (menuItem) {
        menuItem.classList.add('active');
    }
    
    // Load section content
    switch (section) {
        case 'my-ads':
            loadMyAds();
            break;
        case 'favorites':
            loadFavorites();
            break;
        case 'messages':
            loadMessages();
            break;
        case 'balance':
            loadBalance();
            break;
        case 'settings':
            loadSettings();
            break;
    }
}

function loadMyAds() {
    const status = document.getElementById('adsStatusFilter')?.value || '';
    
    fetch(`../../ajax/user_ads.php?action=get_my_ads&status=${status}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayMyAds(data.data);
            } else {
                console.error('Error loading ads:', data.error);
            }
        })
        .catch(error => console.error('Error:', error));
}

function displayMyAds(ads) {
    // Implementation for displaying user ads
    console.log('Displaying ads:', ads);
}

function loadViewsChart() {
    const ctx = document.getElementById('viewsChart').getContext('2d');
    
    fetch('../../ajax/user_stats.php?action=views_chart&period=30days')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                new Chart(ctx, {
                    type: 'line',
                    data: data.data,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        })
        .catch(error => console.error('Error loading chart:', error));
}

function showTopUpModal() {
    new bootstrap.Modal(document.getElementById('topUpModal')).show();
}

function processTopUp() {
    const amount = document.getElementById('topUpAmount').value;
    const method = document.querySelector('input[name="paymentMethod"]:checked').value;
    
    if (!amount || amount < 10) {
        alert('Введіть коректну суму для поповнення');
        return;
    }
    
    // Process payment
    fetch('../../ajax/payment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'top_up',
            amount: amount,
            method: method
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.redirect_url) {
                window.location.href = data.redirect_url;
            } else {
                alert('Баланс успішно поповнено!');
                location.reload();
            }
        } else {
            alert('Помилка: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Виникла помилка при обробці платежу');
    });
}
</script>

<?php
// Helper functions
function getStatusColor($status) {
    $colors = [
        'active' => 'success',
        'pending' => 'warning',
        'rejected' => 'danger',
        'expired' => 'secondary',
        'draft' => 'info'
    ];
    return $colors[$status] ?? 'secondary';
}

function getStatusText($status) {
    $texts = [
        'active' => 'Активне',
        'pending' => 'На модерації',
        'rejected' => 'Відхилено',
        'expired' => 'Прострочене',
        'draft' => 'Чернетка'
    ];
    return $texts[$status] ?? 'Невідомо';
}

function getUserStats($userId) {
    try {
        $db = new Database();
        
        // Основна статистика
        $stmt = $db->prepare("
            SELECT 
                (SELECT COUNT(*) FROM ads WHERE user_id = ?) as total_ads,
                (SELECT COUNT(*) FROM ads WHERE user_id = ? AND status = 'active') as active_ads,
                (SELECT COUNT(*) FROM ads WHERE user_id = ? AND status = 'pending') as pending_ads,
                (SELECT COALESCE(SUM(views_count), 0) FROM ads WHERE user_id = ?) as total_views,
                (SELECT COUNT(*) FROM ad_views WHERE user_id = ? AND DATE(created_at) = CURDATE()) as views_today,
                (SELECT COALESCE(SUM(favorites_count), 0) FROM ads WHERE user_id = ?) as total_favorites,
                (SELECT COUNT(*) FROM favorites f JOIN ads a ON f.ad_id = a.id WHERE a.user_id = ? AND f.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)) as favorites_week,
                (SELECT COALESCE(balance, 0) FROM users WHERE id = ?) as balance
        ");
        
        $stmt->bind_param("iiiiiiii", $userId, $userId, $userId, $userId, $userId, $userId, $userId, $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        return $result;
    } catch (Exception $e) {
        return [
            'total_ads' => 0,
            'active_ads' => 0,
            'pending_ads' => 0,
            'total_views' => 0,
            'views_today' => 0,
            'total_favorites' => 0,
            'favorites_week' => 0,
            'balance' => 0
        ];
    }
}

function getUserAds($userId, $limit = 5) {
    try {
        $db = new Database();
        
        $stmt = $db->prepare("
            SELECT a.*, c.name as category_name,
                   (SELECT filename FROM ad_images WHERE ad_id = a.id AND is_main = 1 LIMIT 1) as main_image
            FROM ads a
            JOIN categories c ON a.category_id = c.id
            WHERE a.user_id = ?
            ORDER BY a.created_at DESC
            LIMIT ?
        ");
        
        $stmt->bind_param("ii", $userId, $limit);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function getUserFavorites($userId, $limit = 5) {
    try {
        $db = new Database();
        
        $stmt = $db->prepare("
            SELECT a.*, c.name as category_name,
                   (SELECT filename FROM ad_images WHERE ad_id = a.id AND is_main = 1 LIMIT 1) as main_image
            FROM ads a
            JOIN categories c ON a.category_id = c.id
            JOIN favorites f ON a.id = f.ad_id
            WHERE f.user_id = ? AND a.status = 'active'
            ORDER BY f.created_at DESC
            LIMIT ?
        ");
        
        $stmt->bind_param("ii", $userId, $limit);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function getUnreadMessagesCount($userId) {
    try {
        $db = new Database();
        
        $stmt = $db->prepare("
            SELECT COUNT(*) as count 
            FROM chat_messages 
            WHERE receiver_id = ? AND is_read = FALSE
        ");
        
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_assoc()['count'] ?? 0;
    } catch (Exception $e) {
        return 0;
    }
}

include '../../themes/footer.php';
?>