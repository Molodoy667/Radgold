<?php
session_start();

// Перевірка авторизації адміна
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: index.php');
    exit();
}

require_once '../config/config.php';
require_once '../config/database.php';

// Підключення до бази даних
$database = new Database();
$db = $database->getConnection();

// Отримуємо дані адміна
$admin_query = "SELECT * FROM users WHERE id = ? AND is_admin = 1";
$admin_stmt = $db->prepare($admin_query);
$admin_stmt->execute([$_SESSION['admin_id']]);
$admin = $admin_stmt->fetch(PDO::FETCH_ASSOC);

// Статистика для дашборда
try {
    // Загальна кількість користувачів
    $users_count = $db->query("SELECT COUNT(*) FROM users WHERE is_active = 1")->fetchColumn();
    
    // Загальна кількість оголошень
    $ads_count = $db->query("SELECT COUNT(*) FROM ads")->fetchColumn();
    
    // Активні оголошення
    $active_ads = $db->query("SELECT COUNT(*) FROM ads WHERE status = 'active'")->fetchColumn();
    
    // Кількість категорій
    $categories_count = $db->query("SELECT COUNT(*) FROM categories WHERE is_active = 1")->fetchColumn();
    
    // Нові користувачі за останній тиждень
    $new_users = $db->query("SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();
    
    // Нові оголошення за сьогодні
    $today_ads = $db->query("SELECT COUNT(*) FROM ads WHERE DATE(created_at) = CURDATE()")->fetchColumn();
    
    // Останні оголошення
    $recent_ads = $db->query("
        SELECT a.*, u.username, c.name as category_name 
        FROM ads a 
        LEFT JOIN users u ON a.user_id = u.id 
        LEFT JOIN categories c ON a.category_id = c.id 
        ORDER BY a.created_at DESC 
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // Топ категорії за кількістю оголошень
    $top_categories = $db->query("
        SELECT c.name, COUNT(a.id) as ads_count 
        FROM categories c 
        LEFT JOIN ads a ON c.id = a.category_id 
        WHERE c.is_active = 1 
        GROUP BY c.id, c.name 
        ORDER BY ads_count DESC 
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // Онлайн користувачі (за останні 15 хвилин)
    $online_users = $db->query("SELECT COUNT(*) FROM users WHERE last_login >= DATE_SUB(NOW(), INTERVAL 15 MINUTE)")->fetchColumn();
    
} catch (Exception $e) {
    error_log("Dashboard stats error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель управління - <?php echo Settings::get('site_name', 'Дошка Оголошень'); ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        <?php echo Theme::generateCSS(); ?>
        
        .admin-navbar {
            background: var(--card-bg) !important;
            border-bottom: 1px solid var(--border-color);
            box-shadow: var(--shadow);
        }
        
        .admin-navbar .navbar-brand {
            font-weight: 600;
            color: var(--text-color) !important;
        }
        
        .admin-navbar .nav-link {
            color: var(--text-color) !important;
            transition: all 0.3s ease;
        }
        
        .admin-navbar .nav-link:hover {
            color: var(--theme-primary) !important;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            right: -350px;
            width: 350px;
            height: 100vh;
            background: var(--card-bg);
            border-left: 1px solid var(--border-color);
            box-shadow: -5px 0 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            z-index: 1050;
            overflow-y: auto;
        }
        
        .sidebar.active {
            right: 0;
        }
        
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            background: var(--theme-gradient);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .close-sidebar {
            background: none;
            border: none;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 5px;
            transition: background 0.3s ease;
        }
        
        .close-sidebar:hover {
            background: rgba(255,255,255,0.2);
        }
        
        .sidebar-menu {
            padding: 1rem 0;
        }
        
        .sidebar-menu .menu-item {
            display: block;
            padding: 1rem 1.5rem;
            color: var(--text-color);
            text-decoration: none;
            border-bottom: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }
        
        .sidebar-menu .menu-item:hover {
            background: var(--surface-color);
            color: var(--theme-primary);
            padding-left: 2rem;
        }
        
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1040;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        .dashboard-container {
            padding-top: 80px;
            min-height: 100vh;
            background: var(--surface-color);
        }
        
        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 15px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 1rem;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--text-color);
            margin: 0;
        }
        
        .stat-label {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin: 0;
        }
        
        .profile-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--theme-primary);
        }
        
        .profile-placeholder {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--theme-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        
        .recent-activity {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 15px;
            padding: 1.5rem;
        }
        
        .activity-item {
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border-color);
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .chart-container {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                right: -100%;
            }
        }
    </style>
</head>
<body>
    <!-- Admin Navbar -->
    <nav class="navbar navbar-expand-lg admin-navbar fixed-top">
        <div class="container-fluid">
            <!-- Profile Section -->
            <div class="navbar-brand d-flex align-items-center">
                <button class="btn btn-outline-primary me-3" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                
                <?php if ($admin['avatar'] && file_exists('../' . $admin['avatar'])): ?>
                    <img src="../<?php echo htmlspecialchars($admin['avatar']); ?>" 
                         alt="Профіль" class="profile-avatar me-2" 
                         onclick="openProfileModal()">
                <?php else: ?>
                    <div class="profile-placeholder me-2" onclick="openProfileModal()">
                        <?php echo strtoupper(substr($admin['username'], 0, 1)); ?>
                    </div>
                <?php endif; ?>
                
                <span class="d-none d-md-inline">
                    Привіт, <?php echo htmlspecialchars($admin['username']); ?>!
                </span>
            </div>
            
            <!-- Logo -->
            <div class="navbar-brand mx-auto">
                <?php 
                $logo_path = Settings::get('site_logo', '');
                if (!empty($logo_path) && file_exists('../' . $logo_path)): 
                ?>
                    <img src="../<?php echo Settings::getLogoUrl(); ?>" alt="<?php echo htmlspecialchars(Settings::get('site_name')); ?>" style="max-height: 40px;">
                <?php else: ?>
                    <i class="fas fa-cog me-2"></i><?php echo htmlspecialchars(Settings::get('site_name', 'Дошка Оголошень')); ?>
                <?php endif; ?>
            </div>
            
            <!-- Navigation Toggle -->
            <div class="navbar-nav">
                <a href="../index.php" class="nav-link" target="_blank" title="Відвідати сайт">
                    <i class="fas fa-external-link-alt"></i>
                    <span class="d-none d-md-inline ms-1">Сайт</span>
                </a>
                <a href="logout.php" class="nav-link text-danger" title="Вихід">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="d-none d-md-inline ms-1">Вихід</span>
                </a>
            </div>
        </div>
    </nav>
    
    <!-- Sidebar -->
    <div class="sidebar-overlay" onclick="closeSidebar()"></div>
    <div class="sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <div>
                <h5 class="mb-0">
                    <i class="fas fa-cogs me-2"></i>Панель управління
                </h5>
                <small>Адміністрування сайту</small>
            </div>
            <button class="close-sidebar" onclick="closeSidebar()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="sidebar-menu">
            <a href="#" class="menu-item" onclick="loadPage('dashboard')">
                <i class="fas fa-tachometer-alt me-3"></i>Головна панель
            </a>
            <a href="#" class="menu-item" onclick="loadPage('settings')">
                <i class="fas fa-cog me-3"></i>Генеральні налаштування
            </a>
            <a href="#" class="menu-item" onclick="loadPage('categories')">
                <i class="fas fa-list me-3"></i>Категорії
            </a>
            <a href="#" class="menu-item" onclick="loadPage('users')">
                <i class="fas fa-users me-3"></i>Користувачі
            </a>
            <a href="#" class="menu-item" onclick="loadPage('ads')">
                <i class="fas fa-bullhorn me-3"></i>Оголошення
            </a>
            <a href="#" class="menu-item" onclick="loadPage('logs')">
                <i class="fas fa-history me-3"></i>Логи системи
            </a>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="dashboard-container">
        <div class="container-fluid" id="main-content">
            <!-- Page Title -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-gradient">
                    <i class="fas fa-tachometer-alt me-2"></i>Панель управління
                </h2>
                <div class="text-muted">
                    <i class="fas fa-calendar me-1"></i>
                    <?php echo date('d.m.Y H:i'); ?>
                </div>
            </div>
            
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="stat-number"><?php echo number_format($users_count ?? 0); ?></h3>
                        <p class="stat-label">Користувачів</p>
                        <small class="text-success">
                            <i class="fas fa-arrow-up me-1"></i>+<?php echo $new_users ?? 0; ?> за тиждень
                        </small>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <h3 class="stat-number"><?php echo number_format($ads_count ?? 0); ?></h3>
                        <p class="stat-label">Оголошень</p>
                        <small class="text-info">
                            <i class="fas fa-plus me-1"></i>+<?php echo $today_ads ?? 0; ?> сьогодні
                        </small>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                            <i class="fas fa-eye"></i>
                        </div>
                        <h3 class="stat-number"><?php echo number_format($active_ads ?? 0); ?></h3>
                        <p class="stat-label">Активних</p>
                        <small class="text-warning">
                            <i class="fas fa-check me-1"></i>Опубліковано
                        </small>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="fas fa-wifi"></i>
                        </div>
                        <h3 class="stat-number"><?php echo number_format($online_users ?? 0); ?></h3>
                        <p class="stat-label">Онлайн</p>
                        <small class="text-success">
                            <i class="fas fa-circle me-1" style="font-size: 8px;"></i>Зараз активні
                        </small>
                    </div>
                </div>
            </div>
            
            <!-- Charts and Recent Activity -->
            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div class="chart-container">
                        <h5 class="mb-3">
                            <i class="fas fa-chart-bar me-2"></i>Топ категорії
                        </h5>
                        <canvas id="categoriesChart" height="100"></canvas>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-4">
                    <div class="recent-activity">
                        <h5 class="mb-3">
                            <i class="fas fa-clock me-2"></i>Останні оголошення
                        </h5>
                        <?php if (!empty($recent_ads)): ?>
                            <?php foreach ($recent_ads as $ad): ?>
                                <div class="activity-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1"><?php echo htmlspecialchars($ad['title']); ?></h6>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars($ad['username']); ?> • 
                                                <?php echo htmlspecialchars($ad['category_name']); ?>
                                            </small>
                                        </div>
                                        <small class="text-muted">
                                            <?php echo date('d.m H:i', strtotime($ad['created_at'])); ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">Немає оголошень</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Profile Modal -->
    <div class="modal fade" id="profileModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-edit me-2"></i>Редагування профілю
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="profileForm" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <div class="mb-3">
                                    <?php if ($admin['avatar'] && file_exists('../' . $admin['avatar'])): ?>
                                        <img src="../<?php echo htmlspecialchars($admin['avatar']); ?>" 
                                             alt="Аватар" class="img-thumbnail mb-2" 
                                             style="width: 150px; height: 150px; object-fit: cover;" id="avatarPreview">
                                    <?php else: ?>
                                        <div class="bg-gradient text-white d-flex align-items-center justify-content-center mb-2" 
                                             style="width: 150px; height: 150px; border-radius: 8px; margin: 0 auto; font-size: 3rem;" id="avatarPreview">
                                            <?php echo strtoupper(substr($admin['username'], 0, 1)); ?>
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*" onchange="previewAvatar(this)">
                                    <small class="text-muted">JPG, PNG до 2MB</small>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Ім'я користувача</label>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           value="<?php echo htmlspecialchars($admin['username']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">Новий пароль (якщо змінюєте)</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="new_password" name="new_password" minlength="6">
                                        <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('new_password')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Підтвердження пароля</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="6">
                                        <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('confirm_password')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
                    <button type="button" class="btn btn-primary" onclick="saveProfile()">
                        <i class="fas fa-save me-1"></i>Зберегти
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sidebar Management
        function toggleSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }
        
        function closeSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        }
        
        // Profile Management
        function openProfileModal() {
            const modal = new bootstrap.Modal(document.getElementById('profileModal'));
            modal.show();
        }
        
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('avatarPreview');
                    if (preview.tagName === 'IMG') {
                        preview.src = e.target.result;
                    } else {
                        preview.outerHTML = `<img src="${e.target.result}" alt="Аватар" class="img-thumbnail mb-2" style="width: 150px; height: 150px; object-fit: cover;" id="avatarPreview">`;
                    }
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const button = input.parentNode.querySelector('button i');
            
            if (input.type === 'password') {
                input.type = 'text';
                button.classList.remove('fa-eye');
                button.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                button.classList.remove('fa-eye-slash');
                button.classList.add('fa-eye');
            }
        }
        
        function saveProfile() {
            const form = document.getElementById('profileForm');
            const formData = new FormData(form);
            
            // Перевірка паролів
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (newPassword && newPassword !== confirmPassword) {
                alert('Паролі не співпадають!');
                return;
            }
            
            fetch('ajax/profile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Помилка збереження профілю');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Помилка збереження профілю');
            });
        }
        
        // Page Loading
        function loadPage(page) {
            closeSidebar();
            
            if (page === 'dashboard') {
                window.location.href = 'dashboard.php';
            } else if (page === 'settings') {
                window.location.href = 'settings.php';
            } else if (page === 'categories') {
                window.location.href = 'categories.php';
            } else if (page === 'users') {
                // TODO: window.location.href = 'users.php';
                console.log('Users page - coming soon');
            } else if (page === 'ads') {
                // TODO: window.location.href = 'ads.php';
                console.log('Ads page - coming soon');
            } else if (page === 'logs') {
                // TODO: window.location.href = 'logs.php';
                console.log('Logs page - coming soon');
            }
        }
        
        // Categories Chart
        const ctx = document.getElementById('categoriesChart').getContext('2d');
        const categoriesData = <?php echo json_encode($top_categories ?? []); ?>;
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: categoriesData.map(cat => cat.name),
                datasets: [{
                    label: 'Кількість оголошень',
                    data: categoriesData.map(cat => cat.ads_count),
                    backgroundColor: [
                        'rgba(240, 147, 251, 0.8)',
                        'rgba(245, 87, 108, 0.8)',
                        'rgba(79, 172, 254, 0.8)',
                        'rgba(67, 233, 123, 0.8)',
                        'rgba(250, 112, 154, 0.8)'
                    ],
                    borderColor: [
                        '#f093fb',
                        '#f5576c',
                        '#4facfe',
                        '#43e97b',
                        '#fa709a'
                    ],
                    borderWidth: 2,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
        
        // Auto close sidebar on window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                closeSidebar();
            }
        });
    </script>
</body>
</html>