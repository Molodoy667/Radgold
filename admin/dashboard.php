<?php
// Устанавливаем заголовок страницы
$page_title = 'Головна панель';

// Подключаем header
require_once 'includes/header.php';

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
    
    // Статистика за категоріями
    $category_stats = $db->query("
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

// Функция для приветствия в зависимости от времени
function getGreeting() {
    $hour = date('H');
    
    if ($hour >= 5 && $hour < 12) {
        return ['Доброго ранку', 'fas fa-sun', 'morning'];
    } elseif ($hour >= 12 && $hour < 17) {
        return ['Доброго дня', 'fas fa-sun', 'day']; 
    } elseif ($hour >= 17 && $hour < 22) {
        return ['Доброго вечора', 'fas fa-moon', 'evening'];
    } else {
        return ['Доброї ночі', 'fas fa-star', 'night'];
    }
}

$greeting_data = getGreeting();
?>

<!-- Page-specific styles -->
<style>
    /* Chart.js для графиков */
    .chart-container {
        position: relative;
        height: 300px;
        margin: 1rem 0;
    }
    
    /* Dashboard-specific styles */
    .dashboard-container {
        padding: 1rem;
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
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        margin-bottom: 1rem;
    }
    
    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--text-color);
        margin: 0;
    }
    
    .stat-label {
        color: var(--text-muted);
        font-size: 0.9rem;
        margin: 0;
    }
    
    .greeting-block {
        display: flex;
        align-items: center;
    }
    
    .greeting-card {
        display: flex;
        align-items: center;
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 15px;
        padding: 1rem 1.5rem;
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        width: 100%;
    }
    
    .greeting-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .greeting-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: var(--theme-gradient);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        margin-right: 1rem;
        animation: pulse 2s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
    
    .greeting-text {
        flex: 1;
    }
    
    .greeting-message {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-color);
        margin-bottom: 0.2rem;
    }
    
    .greeting-time {
        font-size: 0.9rem;
        color: var(--text-muted);
    }
    
    .text-gradient {
        background: var(--theme-gradient);
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 600;
    }
    
    .btn-gradient {
        background: var(--theme-gradient);
        border: none;
        color: white;
        transition: all 0.3s ease;
    }
    
    .btn-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(var(--theme-primary-rgb), 0.4);
        color: white;
    }
    
    .card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        color: var(--text-color);
    }
    
    .card-header {
        background: var(--surface-color);
        border-bottom: 1px solid var(--border-color);
    }
</style>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Page Content -->
<div class="dashboard-container">
    <!-- Приветствие -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="greeting-card">
                <div class="greeting-icon">
                    <i class="<?php echo $greeting_data[1]; ?>"></i>
                </div>
                <div class="greeting-text">
                    <div class="greeting-message">
                        <?php echo $greeting_data[0]; ?>, <?php echo htmlspecialchars($admin['username']); ?>!
                    </div>
                    <div class="greeting-time">
                        Зараз <?php echo date('d.m.Y, H:i'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Статистичні картки -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="stat-number"><?php echo number_format($users_count ?? 0); ?></h3>
                <p class="stat-label">Всього користувачів</p>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <h3 class="stat-number"><?php echo number_format($ads_count ?? 0); ?></h3>
                <p class="stat-label">Всього оголошень</p>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3 class="stat-number"><?php echo number_format($active_ads ?? 0); ?></h3>
                <p class="stat-label">Активних оголошень</p>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                    <i class="fas fa-list"></i>
                </div>
                <h3 class="stat-number"><?php echo number_format($categories_count ?? 0); ?></h3>
                <p class="stat-label">Категорій</p>
            </div>
        </div>
    </div>

    <!-- Додаткова статистика -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h3 class="stat-number"><?php echo number_format($new_users ?? 0); ?></h3>
                <p class="stat-label">Нових користувачів за тиждень</p>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
                    <i class="fas fa-plus-circle"></i>
                </div>
                <h3 class="stat-number"><?php echo number_format($today_ads ?? 0); ?></h3>
                <p class="stat-label">Нових оголошень сьогодні</p>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);">
                    <i class="fas fa-circle text-success"></i>
                </div>
                <h3 class="stat-number"><?php echo number_format($online_users ?? 0); ?></h3>
                <p class="stat-label">Онлайн зараз</p>
            </div>
        </div>
    </div>

    <!-- Графіки та додаткова інформація -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="text-gradient">
                        <i class="fas fa-chart-line me-2"></i>Активність користувачів
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="activityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="text-gradient">
                        <i class="fas fa-chart-pie me-2"></i>За категоріями
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Останні оголошення -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="text-gradient">
                        <i class="fas fa-clock me-2"></i>Останні оголошення
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($recent_ads)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Назва</th>
                                        <th>Автор</th>
                                        <th>Категорія</th>
                                        <th>Дата</th>
                                        <th>Статус</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_ads as $ad): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($ad['title']); ?></td>
                                            <td><?php echo htmlspecialchars($ad['username'] ?? 'Невідомий'); ?></td>
                                            <td><?php echo htmlspecialchars($ad['category_name'] ?? 'Без категорії'); ?></td>
                                            <td><?php echo date('d.m.Y H:i', strtotime($ad['created_at'])); ?></td>
                                            <td>
                                                <span class="badge <?php echo $ad['status'] === 'active' ? 'bg-success' : 'bg-warning'; ?>">
                                                    <?php echo $ad['status'] === 'active' ? 'Активне' : 'В очікуванні'; ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center py-4">Поки що немає оголошень</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js инициализация -->
<script>
// Активность пользователей
const activityCtx = document.getElementById('activityChart').getContext('2d');
new Chart(activityCtx, {
    type: 'line',
    data: {
        labels: ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Нд'],
        datasets: [{
            label: 'Нові користувачі',
            data: [12, 19, 3, 5, 2, 3, 9],
            borderColor: 'rgb(102, 126, 234)',
            backgroundColor: 'rgba(102, 126, 234, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Статистика по категориям
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
        labels: [
            <?php 
            if (!empty($category_stats)) {
                foreach ($category_stats as $stat) {
                    echo "'" . htmlspecialchars($stat['name']) . "',";
                }
            }
            ?>
        ],
        datasets: [{
            data: [
                <?php 
                if (!empty($category_stats)) {
                    foreach ($category_stats as $stat) {
                        echo $stat['ads_count'] . ",";
                    }
                }
                ?>
            ],
            backgroundColor: [
                '#667eea',
                '#f093fb', 
                '#4facfe',
                '#43e97b',
                '#fa709a'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>