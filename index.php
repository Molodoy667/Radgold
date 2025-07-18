<?php
require_once 'config/config.php';
require_once 'config/database.php';

$page_title = 'Головна сторінка';

// Підключення до бази даних
$database = new Database();
$db = $database->getConnection();

// Отримання останніх оголошень
$query = "SELECT a.*, c.name as category_name, u.username, 
          (SELECT image_path FROM ad_images WHERE ad_id = a.id AND is_main = 1 LIMIT 1) as main_image
          FROM ads a 
          JOIN categories c ON a.category_id = c.id 
          JOIN users u ON a.user_id = u.id 
          WHERE a.status = 'active' 
          ORDER BY a.created_at DESC 
          LIMIT 12";
$stmt = $db->prepare($query);
$stmt->execute();
$latest_ads = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Отримання категорій
$query = "SELECT * FROM categories ORDER BY name";
$stmt = $db->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Статистика
$stats_query = "SELECT 
    (SELECT COUNT(*) FROM ads WHERE status = 'active') as total_ads,
    (SELECT COUNT(*) FROM users WHERE is_active = 1) as total_users,
    (SELECT COUNT(*) FROM categories) as total_categories";
$stats_stmt = $db->prepare($stats_query);
$stats_stmt->execute();
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="search-container">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <h1 class="display-4 fw-bold mb-4">
                    Знайди все, що потрібно!
                </h1>
                <p class="lead mb-4">
                    Найбільша дошка оголошень в Україні. Купуй та продавай товари та послуги легко та безпечно.
                </p>
                <div class="d-flex gap-3">
                    <a href="pages/categories.php" class="btn btn-warning btn-lg">
                        <i class="fas fa-search me-2"></i>Переглянути категорії
                    </a>
                    <a href="pages/add_ad.php" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-plus me-2"></i>Подати оголошення
                    </a>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="search-box">
                    <h3 class="text-dark mb-4">Швидкий пошук</h3>
                    <form action="pages/search.php" method="GET" class="position-relative">
                        <div class="mb-3">
                            <input type="text" class="form-control form-control-lg" 
                                   id="searchInput" name="q" placeholder="Що ви шукаєте?">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <select class="form-select mb-3" name="category">
                                    <option value="">Всі категорії</option>
                                    <?php foreach($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>">
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control mb-3" 
                                       name="location" placeholder="Місто">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-search me-2"></i>Знайти
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Статистика -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="stats-card">
                    <span class="stats-number"><?php echo number_format($stats['total_ads']); ?></span>
                    <h5>Активних оголошень</h5>
                </div>
            </div>
            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="stats-card">
                    <span class="stats-number"><?php echo number_format($stats['total_users']); ?></span>
                    <h5>Зареєстрованих користувачів</h5>
                </div>
            </div>
            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="stats-card">
                    <span class="stats-number"><?php echo $stats['total_categories']; ?></span>
                    <h5>Категорій товарів</h5>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Категорії -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5" data-aos="fade-up">
            <span class="text-gradient">Популярні категорії</span>
        </h2>
        <div class="row">
            <?php foreach(array_slice($categories, 0, 8) as $index => $category): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4" data-aos="zoom-in" data-aos-delay="<?php echo $index * 100; ?>">
                    <a href="pages/category.php?id=<?php echo $category['id']; ?>" 
                       class="category-card d-block p-4 text-center text-decoration-none">
                        <div class="category-icon">
                            <i class="<?php echo $category['icon']; ?>"></i>
                        </div>
                        <h5 class="mb-2"><?php echo htmlspecialchars($category['name']); ?></h5>
                        <p class="mb-0 opacity-75"><?php echo htmlspecialchars($category['description']); ?></p>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4" data-aos="fade-up">
            <a href="pages/categories.php" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-th me-2"></i>Всі категорії
            </a>
        </div>
    </div>
</section>

<!-- Останні оголошення -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5" data-aos="fade-up">
            <span class="text-gradient">Останні оголошення</span>
        </h2>
        <div class="row">
            <?php foreach($latest_ads as $index => $ad): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                    <div class="card ad-card h-100">
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <button class="favorite-btn" data-ad-id="<?php echo $ad['id']; ?>">
                                <i class="far fa-heart"></i>
                            </button>
                        <?php endif; ?>
                        
                        <div class="position-relative">
                            <?php if($ad['main_image']): ?>
                                <img src="<?php echo $ad['main_image']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($ad['title']); ?>">
                            <?php else: ?>
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title">
                                <a href="pages/ad_detail.php?id=<?php echo $ad['id']; ?>" 
                                   class="text-decoration-none text-dark">
                                    <?php echo htmlspecialchars(substr($ad['title'], 0, 50)); ?>
                                    <?php echo strlen($ad['title']) > 50 ? '...' : ''; ?>
                                </a>
                            </h6>
                            
                            <p class="card-text text-muted small flex-grow-1">
                                <?php echo htmlspecialchars(substr($ad['description'], 0, 80)); ?>
                                <?php echo strlen($ad['description']) > 80 ? '...' : ''; ?>
                            </p>
                            
                            <div class="mt-auto">
                                <?php if($ad['price']): ?>
                                    <div class="ad-price mb-2">
                                        <?php echo number_format($ad['price'], 0, ',', ' '); ?> ₴
                                    </div>
                                <?php endif; ?>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="ad-location">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        <?php echo htmlspecialchars($ad['location'] ?: 'Не вказано'); ?>
                                    </small>
                                    <small class="ad-date">
                                        <?php echo date('d.m.Y', strtotime($ad['created_at'])); ?>
                                    </small>
                                </div>
                                
                                <div class="mt-2">
                                    <span class="badge bg-primary">
                                        <?php echo htmlspecialchars($ad['category_name']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if(empty($latest_ads)): ?>
            <div class="text-center py-5" data-aos="fade-up">
                <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">Поки що немає оголошень</h4>
                <p class="text-muted">Станьте першим, хто розмістить оголошення!</p>
                <a href="pages/add_ad.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus me-2"></i>Додати оголошення
                </a>
            </div>
        <?php else: ?>
            <div class="text-center mt-4" data-aos="fade-up">
                <a href="pages/all_ads.php" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-eye me-2"></i>Переглянути всі оголошення
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Переваги платформи -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5" data-aos="fade-up">
            <span class="text-gradient">Чому обирають нас?</span>
        </h2>
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="text-center">
                    <div class="feature-icon bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-shield-alt fa-2x"></i>
                    </div>
                    <h4>Безпека</h4>
                    <p class="text-muted">Всі оголошення проходять модерацію для вашої безпеки</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="text-center">
                    <div class="feature-icon bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-bolt fa-2x"></i>
                    </div>
                    <h4>Швидкість</h4>
                    <p class="text-muted">Миттєва публікація та швидкий пошук потрібних товарів</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="text-center">
                    <div class="feature-icon bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-heart fa-2x"></i>
                    </div>
                    <h4>Зручність</h4>
                    <p class="text-muted">Простий та зрозумілий інтерфейс для всіх користувачів</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>