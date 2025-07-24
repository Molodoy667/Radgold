<?php
require_once '../../core/config.php';
require_once '../../core/functions.php';

// Перевірка авторизації партнера
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'partner') {
    header('Location: login.php');
    exit();
}

$user = getUserById($_SESSION['user_id']);
if (!$user) {
    header('Location: login.php');
    exit();
}

// Отримання статистики партнера
$stats = getPartnerStats($_SESSION['user_id']);
$recentActivity = getPartnerActivity($_SESSION['user_id'], 10);
$campaigns = getPartnerCampaigns($_SESSION['user_id']);

include '../../themes/header.php';
?>

<div class="partner-dashboard-container">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-xl-3 col-lg-4 partner-sidebar">
                <div class="sidebar-content">
                    <!-- Partner Profile -->
                    <div class="partner-profile">
                        <div class="profile-avatar">
                            <?php if ($user['avatar']): ?>
                                <img src="<?php echo $user['avatar']; ?>" alt="Avatar">
                            <?php else: ?>
                                <div class="avatar-placeholder">
                                    <i class="fas fa-user"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="profile-info">
                            <h4><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h4>
                            <p class="partner-status">
                                <i class="fas fa-crown"></i>
                                <?php echo $user['status'] === 'active' ? 'Активний партнер' : 'На модерації'; ?>
                            </p>
                        </div>
                    </div>
                    
                    <!-- Navigation Menu -->
                    <nav class="partner-nav">
                        <a href="#dashboard" class="nav-item active" data-section="dashboard">
                            <i class="fas fa-chart-pie"></i>
                            <span>Панель керування</span>
                        </a>
                        <a href="#campaigns" class="nav-item" data-section="campaigns">
                            <i class="fas fa-bullhorn"></i>
                            <span>Рекламні кампанії</span>
                        </a>
                        <a href="#analytics" class="nav-item" data-section="analytics">
                            <i class="fas fa-chart-line"></i>
                            <span>Аналітика</span>
                        </a>
                        <a href="#billing" class="nav-item" data-section="billing">
                            <i class="fas fa-credit-card"></i>
                            <span>Біллінг</span>
                        </a>
                        <a href="#profile" class="nav-item" data-section="profile">
                            <i class="fas fa-user-cog"></i>
                            <span>Профіль</span>
                        </a>
                        <a href="#support" class="nav-item" data-section="support">
                            <i class="fas fa-headset"></i>
                            <span>Підтримка</span>
                        </a>
                        <a href="../../ajax/logout.php" class="nav-item logout">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Вихід</span>
                        </a>
                    </nav>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-xl-9 col-lg-8 partner-main">
                <!-- Dashboard Section -->
                <div class="content-section active" id="dashboard-section">
                    <div class="section-header">
                        <h2>Панель керування</h2>
                        <p>Огляд вашої партнерської діяльності</p>
                    </div>
                    
                    <!-- Stats Cards -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?php echo number_format($stats['views'] ?? 0); ?></h3>
                                <p>Перегляди</p>
                            </div>
                            <div class="stat-trend up">
                                <i class="fas fa-arrow-up"></i>
                                <span>+12%</span>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-mouse-pointer"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?php echo number_format($stats['clicks'] ?? 0); ?></h3>
                                <p>Кліки</p>
                            </div>
                            <div class="stat-trend up">
                                <i class="fas fa-arrow-up"></i>
                                <span>+8%</span>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div class="stat-content">
                                <h3>$<?php echo number_format($stats['spent'] ?? 0, 2); ?></h3>
                                <p>Витрачено</p>
                            </div>
                            <div class="stat-trend down">
                                <i class="fas fa-arrow-down"></i>
                                <span>-3%</span>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-percentage"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?php echo number_format($stats['ctr'] ?? 0, 2); ?>%</h3>
                                <p>CTR</p>
                            </div>
                            <div class="stat-trend up">
                                <i class="fas fa-arrow-up"></i>
                                <span>+5%</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Charts Row -->
                    <div class="charts-row">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h4>Статистика переглядів</h4>
                                <div class="chart-controls">
                                    <select class="form-select">
                                        <option>Останні 7 днів</option>
                                        <option>Останні 30 днів</option>
                                        <option>Останні 3 місяці</option>
                                    </select>
                                </div>
                            </div>
                            <div class="chart-content">
                                <canvas id="viewsChart"></canvas>
                            </div>
                        </div>
                        
                        <div class="chart-card">
                            <div class="chart-header">
                                <h4>Активні кампанії</h4>
                            </div>
                            <div class="campaigns-preview">
                                <?php if (!empty($campaigns)): ?>
                                    <?php foreach (array_slice($campaigns, 0, 3) as $campaign): ?>
                                        <div class="campaign-item">
                                            <div class="campaign-status <?php echo $campaign['status']; ?>"></div>
                                            <div class="campaign-info">
                                                <h5><?php echo htmlspecialchars($campaign['name']); ?></h5>
                                                <p>Budget: $<?php echo number_format($campaign['budget'], 2); ?></p>
                                            </div>
                                            <div class="campaign-metrics">
                                                <span class="metric"><?php echo $campaign['clicks']; ?> clicks</span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="empty-state">
                                        <i class="fas fa-bullhorn"></i>
                                        <h5>Немає активних кампаній</h5>
                                        <p>Створіть свою першу рекламну кампанію</p>
                                        <button class="btn btn-primary" onclick="showSection('campaigns')">
                                            Створити кампанію
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Activity -->
                    <div class="activity-card">
                        <div class="card-header">
                            <h4>Остання активність</h4>
                            <a href="#" class="view-all">Переглянути все</a>
                        </div>
                        <div class="activity-list">
                            <?php if (!empty($recentActivity)): ?>
                                <?php foreach ($recentActivity as $activity): ?>
                                    <div class="activity-item">
                                        <div class="activity-icon">
                                            <i class="fas fa-<?php echo $activity['icon']; ?>"></i>
                                        </div>
                                        <div class="activity-content">
                                            <p><?php echo htmlspecialchars($activity['description']); ?></p>
                                            <small><?php echo timeAgo($activity['created_at']); ?></small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="empty-activity">
                                    <p>Немає активності</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Campaigns Section -->
                <div class="content-section" id="campaigns-section">
                    <div class="section-header">
                        <h2>Рекламні кампанії</h2>
                        <button class="btn btn-primary" onclick="createCampaign()">
                            <i class="fas fa-plus"></i>
                            Створити кампанію
                        </button>
                    </div>
                    
                    <div class="campaigns-grid">
                        <!-- Campaign cards will be loaded here -->
                        <div class="coming-soon">
                            <i class="fas fa-rocket"></i>
                            <h4>Незабаром</h4>
                            <p>Функціонал створення рекламних кампаній буде доступний незабаром</p>
                        </div>
                    </div>
                </div>
                
                <!-- Analytics Section -->
                <div class="content-section" id="analytics-section">
                    <div class="section-header">
                        <h2>Аналітика</h2>
                    </div>
                    
                    <div class="coming-soon">
                        <i class="fas fa-chart-bar"></i>
                        <h4>Детальна аналітика</h4>
                        <p>Розширені звіти та метрики будуть доступні незабаром</p>
                    </div>
                </div>
                
                <!-- Other sections... -->
                <div class="content-section" id="billing-section">
                    <div class="section-header">
                        <h2>Біллінг</h2>
                    </div>
                    <div class="coming-soon">
                        <i class="fas fa-credit-card"></i>
                        <h4>Біллінг та платежі</h4>
                        <p>Функціонал біллінгу буде доступний незабаром</p>
                    </div>
                </div>
                
                <div class="content-section" id="profile-section">
                    <div class="section-header">
                        <h2>Профіль</h2>
                    </div>
                    <div class="coming-soon">
                        <i class="fas fa-user-edit"></i>
                        <h4>Редагування профілю</h4>
                        <p>Налаштування профілю будуть доступні незабаром</p>
                    </div>
                </div>
                
                <div class="content-section" id="support-section">
                    <div class="section-header">
                        <h2>Підтримка</h2>
                    </div>
                    <div class="coming-soon">
                        <i class="fas fa-headset"></i>
                        <h4>Центр підтримки</h4>
                        <p>Система тікетів та підтримки буде доступна незабаром</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Navigation functionality
function showSection(sectionName) {
    // Hide all sections
    document.querySelectorAll('.content-section').forEach(section => {
        section.classList.remove('active');
    });
    
    // Remove active class from nav items
    document.querySelectorAll('.nav-item').forEach(item => {
        item.classList.remove('active');
    });
    
    // Show selected section
    document.getElementById(sectionName + '-section').classList.add('active');
    
    // Add active class to nav item
    document.querySelector(`[data-section="${sectionName}"]`).classList.add('active');
}

// Nav item click handlers
document.querySelectorAll('.nav-item[data-section]').forEach(item => {
    item.addEventListener('click', function(e) {
        e.preventDefault();
        const section = this.getAttribute('data-section');
        showSection(section);
    });
});

// Chart initialization (placeholder)
document.addEventListener('DOMContentLoaded', function() {
    // Initialize charts here when Chart.js is available
    console.log('Partner dashboard loaded');
});

function createCampaign() {
    alert('Функціонал створення кампаній буде доступний незабаром!');
}
</script>

<style>
/* Partner Dashboard Styles */
.partner-dashboard-container {
    min-height: 100vh;
    background: var(--theme-bg);
    padding-top: 20px;
}

.partner-sidebar {
    background: var(--theme-bg-secondary);
    border-right: 1px solid var(--theme-border);
    min-height: calc(100vh - 20px);
}

.sidebar-content {
    padding: 20px;
    position: sticky;
    top: 20px;
}

.partner-profile {
    text-align: center;
    margin-bottom: 30px;
    padding: 20px;
    background: var(--theme-bg);
    border-radius: 15px;
    border: 1px solid var(--theme-border);
}

.profile-avatar {
    width: 80px;
    height: 80px;
    margin: 0 auto 15px;
    border-radius: 50%;
    overflow: hidden;
    background: linear-gradient(135deg, #ff9800, #f57c00);
    display: flex;
    align-items: center;
    justify-content: center;
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-placeholder {
    color: white;
    font-size: 2rem;
}

.profile-info h4 {
    color: var(--theme-text);
    margin-bottom: 5px;
    font-weight: 600;
}

.partner-status {
    color: var(--theme-muted);
    margin: 0;
    font-size: 0.9rem;
}

.partner-status i {
    color: #ff9800;
    margin-right: 5px;
}

.partner-nav {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.nav-item {
    display: flex;
    align-items: center;
    padding: 12px 15px;
    color: var(--theme-text);
    text-decoration: none;
    border-radius: 10px;
    transition: all 0.3s ease;
    position: relative;
}

.nav-item:hover {
    background: var(--theme-bg);
    color: var(--theme-text);
    text-decoration: none;
    transform: translateX(5px);
}

.nav-item.active {
    background: linear-gradient(135deg, #ff9800, #f57c00);
    color: white;
}

.nav-item.logout {
    margin-top: 20px;
    color: #dc3545;
}

.nav-item.logout:hover {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
}

.nav-item i {
    width: 20px;
    margin-right: 12px;
    font-size: 1.1rem;
}

.partner-main {
    padding: 20px;
}

.content-section {
    display: none;
}

.content-section.active {
    display: block;
}

.section-header {
    margin-bottom: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.section-header h2 {
    color: var(--theme-text);
    margin: 0;
    font-weight: 700;
}

.section-header p {
    color: var(--theme-muted);
    margin: 5px 0 0 0;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: var(--theme-bg-secondary);
    border: 1px solid var(--theme-border);
    border-radius: 15px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    background: linear-gradient(135deg, #ff9800, #f57c00);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
}

.stat-content h3 {
    color: var(--theme-text);
    margin: 0 0 5px 0;
    font-size: 1.8rem;
    font-weight: 700;
}

.stat-content p {
    color: var(--theme-muted);
    margin: 0;
    font-size: 0.9rem;
}

.stat-trend {
    margin-left: auto;
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 0.9rem;
    font-weight: 600;
}

.stat-trend.up {
    color: #28a745;
}

.stat-trend.down {
    color: #dc3545;
}

.charts-row {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
    margin-bottom: 30px;
}

.chart-card {
    background: var(--theme-bg-secondary);
    border: 1px solid var(--theme-border);
    border-radius: 15px;
    padding: 20px;
}

.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.chart-header h4 {
    color: var(--theme-text);
    margin: 0;
    font-weight: 600;
}

.chart-content {
    height: 300px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--theme-bg);
    border-radius: 10px;
    color: var(--theme-muted);
}

.campaigns-preview {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.campaign-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 15px;
    background: var(--theme-bg);
    border-radius: 10px;
    border: 1px solid var(--theme-border);
}

.campaign-status {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #28a745;
}

.campaign-info h5 {
    margin: 0 0 5px 0;
    color: var(--theme-text);
    font-size: 0.9rem;
}

.campaign-info p {
    margin: 0;
    color: var(--theme-muted);
    font-size: 0.8rem;
}

.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: var(--theme-muted);
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 15px;
    opacity: 0.5;
}

.empty-state h5 {
    color: var(--theme-text);
    margin-bottom: 10px;
}

.activity-card {
    background: var(--theme-bg-secondary);
    border: 1px solid var(--theme-border);
    border-radius: 15px;
    padding: 20px;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.card-header h4 {
    color: var(--theme-text);
    margin: 0;
    font-weight: 600;
}

.view-all {
    color: #ff9800;
    text-decoration: none;
    font-size: 0.9rem;
}

.view-all:hover {
    text-decoration: underline;
}

.activity-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 12px;
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ff9800, #f57c00);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
}

.activity-content p {
    margin: 0 0 5px 0;
    color: var(--theme-text);
    font-size: 0.9rem;
}

.activity-content small {
    color: var(--theme-muted);
    font-size: 0.8rem;
}

.coming-soon {
    text-align: center;
    padding: 60px 20px;
    color: var(--theme-muted);
}

.coming-soon i {
    font-size: 4rem;
    margin-bottom: 20px;
    opacity: 0.5;
    color: #ff9800;
}

.coming-soon h4 {
    color: var(--theme-text);
    margin-bottom: 15px;
}

.btn {
    padding: 10px 20px;
    border-radius: 8px;
    border: none;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.btn-primary {
    background: linear-gradient(135deg, #ff9800, #f57c00);
    color: white;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #f57c00, #e65100);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 152, 0, 0.3);
}

.form-select {
    padding: 8px 12px;
    border: 1px solid var(--theme-border);
    border-radius: 6px;
    background: var(--theme-bg);
    color: var(--theme-text);
}

/* Responsive */
@media (max-width: 992px) {
    .partner-sidebar {
        position: relative;
        min-height: auto;
    }
    
    .charts-row {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }
}

@media (max-width: 768px) {
    .section-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include '../../themes/footer.php'; ?>

<?php
// Функції для отримання даних партнера (заглушки)
function getPartnerStats($userId) {
    return [
        'views' => rand(1000, 10000),
        'clicks' => rand(100, 1000),
        'spent' => rand(100, 5000),
        'ctr' => rand(1, 10)
    ];
}

function getPartnerActivity($userId, $limit) {
    return [
        [
            'icon' => 'eye',
            'description' => 'Ваша кампанія "Літній розпродаж" отримала 150+ переглядів',
            'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours'))
        ],
        [
            'icon' => 'click',
            'description' => 'Новий клік на рекламу "Зимові знижки"',
            'created_at' => date('Y-m-d H:i:s', strtotime('-5 hours'))
        ]
    ];
}

function getPartnerCampaigns($userId) {
    return [];
}
?>