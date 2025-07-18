<?php
require_once '../config/config.php';
require_once '../config/database.php';

header('Content-Type: application/json');

// Перевіряємо метод запиту
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// Отримуємо дані
$input = json_decode(file_get_contents('php://input'), true);
$page = isset($input['page']) ? $input['page'] : 'home';

// Валідація назви сторінки
$allowedPages = ['home', 'categories', 'search', 'login', 'register', 'add_ad', 'profile', 'favorites'];
if (!in_array($page, $allowedPages)) {
    echo json_encode(['error' => 'Invalid page']);
    exit();
}

try {
    // Підключення до бази даних
    $database = new Database();
    $db = $database->getConnection();
    
    // Буферизація виводу
    ob_start();
    
    $page_title = '';
    $content = '';
    
    // Завантаження контенту залежно від сторінки
    switch ($page) {
        case 'home':
            $page_title = 'Головна сторінка';
            $content = renderHomePage($db);
            break;
            
        case 'categories':
            $page_title = 'Категорії';
            $content = renderCategoriesPage($db);
            break;
            
        case 'search':
            $page_title = 'Пошук';
            $content = renderSearchPage($db);
            break;
            
        case 'login':
            $page_title = 'Вхід';
            $content = renderLoginPage();
            break;
            
        case 'register':
            $page_title = 'Реєстрація';
            $content = renderRegisterPage();
            break;
            
        case 'add_ad':
            $page_title = 'Додати оголошення';
            $content = renderAddAdPage($db);
            break;
            
        case 'profile':
            $page_title = 'Профіль';
            $content = renderProfilePage($db);
            break;
            
        case 'favorites':
            $page_title = 'Вподобання';
            $content = renderFavoritesPage($db);
            break;
            
        default:
            $page_title = 'Сторінка не знайдена';
            $content = render404Page();
    }
    
    // Очищаємо буфер
    ob_clean();
    
    // Повертаємо результат
    echo json_encode([
        'success' => true,
        'title' => $page_title . ' - ' . SITE_NAME,
        'html' => $content,
        'page' => $page
    ]);
    
} catch (Exception $e) {
    ob_clean();
    echo json_encode([
        'error' => 'Server error',
        'message' => $e->getMessage()
    ]);
}

// Функції рендерингу сторінок

function renderHomePage($db) {
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
    $query = "SELECT * FROM categories ORDER BY name LIMIT 8";
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

    ob_start();
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
                        <a href="pages/categories.php" class="btn btn-warning btn-lg" data-spa>
                            <i class="fas fa-search me-2"></i>Переглянути категорії
                        </a>
                        <a href="pages/add_ad.php" class="btn btn-outline-light btn-lg" data-spa>
                            <i class="fas fa-plus me-2"></i>Подати оголошення
                        </a>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="search-box">
                        <h3 class="text-dark mb-4">Швидкий пошук</h3>
                        <form id="quickSearchForm" data-ajax action="ajax/search.php" class="position-relative">
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
                <?php foreach($categories as $index => $category): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4" data-aos="zoom-in" data-aos-delay="<?php echo $index * 100; ?>">
                        <a href="pages/category.php?id=<?php echo $category['id']; ?>" 
                           class="category-card d-block p-4 text-center text-decoration-none" data-spa>
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
                <a href="pages/categories.php" class="btn btn-outline-primary btn-lg" data-spa>
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
                                       class="text-decoration-none text-dark" data-spa>
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
        </div>
    </section>
    <?php
    return ob_get_clean();
}

function renderCategoriesPage($db) {
    // Отримання категорій з кількістю оголошень
    $query = "SELECT c.*, 
              (SELECT COUNT(*) FROM ads WHERE category_id = c.id AND status = 'active') as ads_count
              FROM categories c 
              ORDER BY c.name";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    ob_start();
    ?>
    <div class="container py-5">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" data-aos="fade-down">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php" data-spa>Головна</a></li>
                <li class="breadcrumb-item active">Категорії</li>
            </ol>
        </nav>
        
        <!-- Header -->
        <div class="text-center mb-5" data-aos="fade-up">
            <h1 class="display-4 text-gradient">Категорії товарів</h1>
            <p class="lead text-muted">Оберіть категорію для пошуку потрібних товарів та послуг</p>
        </div>
        
        <!-- Search Box -->
        <div class="row mb-5" data-aos="fade-up" data-aos-delay="100">
            <div class="col-lg-8 mx-auto">
                <form action="pages/search.php" method="GET" class="position-relative" data-ajax>
                    <div class="input-group input-group-lg">
                        <input type="text" class="form-control" name="q" 
                               placeholder="Пошук в категоріях..." id="categorySearch">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Filter Buttons -->
        <div class="text-center mb-4" data-aos="fade-up" data-aos-delay="200">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-primary active filter-btn" data-filter="all">
                    Всі категорії
                </button>
                <button type="button" class="btn btn-outline-primary filter-btn" data-filter="popular">
                    Популярні
                </button>
                <button type="button" class="btn btn-outline-primary filter-btn" data-filter="new">
                    Нові
                </button>
            </div>
        </div>
        
        <!-- Categories Grid -->
        <div class="row" id="categoriesGrid">
            <?php foreach($categories as $index => $category): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4 filterable-item" 
                     data-category="<?php echo $category['ads_count'] > 10 ? 'popular' : 'new'; ?>"
                     data-aos="zoom-in" 
                     data-aos-delay="<?php echo ($index % 8) * 100; ?>">
                    <a href="pages/category.php?id=<?php echo $category['id']; ?>" 
                       class="category-card d-block p-4 text-center text-decoration-none position-relative" data-spa>
                       
                        <!-- Badge with count -->
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark">
                            <?php echo $category['ads_count']; ?>
                        </span>
                        
                        <div class="category-icon">
                            <i class="<?php echo $category['icon']; ?>"></i>
                        </div>
                        
                        <h5 class="mb-2"><?php echo htmlspecialchars($category['name']); ?></h5>
                        
                        <p class="mb-3 opacity-75 small">
                            <?php echo htmlspecialchars($category['description']); ?>
                        </p>
                        
                        <div class="mt-auto">
                            <span class="badge bg-light text-dark">
                                <?php echo $category['ads_count']; ?> 
                                <?php echo $category['ads_count'] === 1 ? 'оголошення' : 'оголошень'; ?>
                            </span>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

function renderLoginPage() {
    ob_start();
    ?>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-custom" data-aos="fade-up">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="text-gradient">Вхід</h2>
                            <p class="text-muted">Увійдіть у свій акаунт</p>
                        </div>
                        
                        <form id="loginForm" data-ajax action="ajax/auth.php" method="POST" class="needs-validation" novalidate>
                            <input type="hidden" name="action" value="login">
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-1"></i>Email
                                </label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                <div class="invalid-feedback">
                                    Будь ласка, введіть правильний email.
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-1"></i>Пароль
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <button class="btn btn-outline-secondary" type="button" 
                                            onclick="togglePassword('password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback">
                                    Будь ласка, введіть пароль.
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">
                                        Запам'ятати мене
                                    </label>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="fas fa-sign-in-alt me-2"></i>Увійти
                            </button>
                        </form>
                        
                        <div class="text-center">
                            <p class="mb-2">
                                <a href="pages/forgot_password.php" class="text-decoration-none" data-spa>
                                    Забули пароль?
                                </a>
                            </p>
                            <p class="mb-0">Немає акаунта? 
                                <a href="pages/register.php" class="text-decoration-none fw-bold" data-spa>Зареєструватися</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

function renderRegisterPage() {
    ob_start();
    ?>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-custom" data-aos="fade-up">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="text-gradient">Реєстрація</h2>
                            <p class="text-muted">Створіть свій обліковий запис</p>
                        </div>
                        
                        <form id="registerForm" data-ajax action="ajax/auth.php" method="POST" class="needs-validation" novalidate>
                            <input type="hidden" name="action" value="register">
                            
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-user me-1"></i>Ім'я користувача *
                                </label>
                                <input type="text" class="form-control" id="username" name="username" required>
                                <div class="invalid-feedback">
                                    Будь ласка, введіть ім'я користувача.
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="register_email" class="form-label">
                                    <i class="fas fa-envelope me-1"></i>Email *
                                </label>
                                <input type="email" class="form-control" id="register_email" name="email" required>
                                <div class="invalid-feedback">
                                    Будь ласка, введіть правильний email.
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone me-1"></i>Телефон
                                </label>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="+380501234567">
                            </div>
                            
                            <div class="mb-3">
                                <label for="register_password" class="form-label">
                                    <i class="fas fa-lock me-1"></i>Пароль *
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="register_password" 
                                           name="password" minlength="6" required>
                                    <button class="btn btn-outline-secondary" type="button" 
                                            onclick="togglePassword('register_password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback">
                                    Пароль повинен містити мінімум 6 символів.
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="confirm_password" class="form-label">
                                    <i class="fas fa-lock me-1"></i>Підтвердження пароля *
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirm_password" 
                                           name="confirm_password" minlength="6" required>
                                    <button class="btn btn-outline-secondary" type="button" 
                                            onclick="togglePassword('confirm_password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback">
                                    Будь ласка, підтвердіть пароль.
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="terms" required>
                                    <label class="form-check-label" for="terms">
                                        Я погоджуюся з умовами користування та політикою конфіденційності *
                                    </label>
                                    <div class="invalid-feedback">
                                        Необхідно погодитися з умовами.
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="fas fa-user-plus me-2"></i>Зареєструватися
                            </button>
                        </form>
                        
                        <div class="text-center">
                            <p class="mb-0">Вже маєте акаунт? 
                                <a href="pages/login.php" class="text-decoration-none fw-bold" data-spa>Увійти</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

function renderSearchPage($db) {
    ob_start();
    ?>
    <div class="container py-5">
        <h2 class="mb-4">Пошук оголошень</h2>
        <p>Сторінка пошуку (в розробці)</p>
    </div>
    <?php
    return ob_get_clean();
}

function renderAddAdPage($db) {
    if (!isset($_SESSION['user_id'])) {
        return '<div class="container py-5"><div class="alert alert-warning">Увійдіть для додавання оголошень</div></div>';
    }
    
    ob_start();
    ?>
    <div class="container py-5">
        <h2 class="mb-4">Додати оголошення</h2>
        <p>Форма додавання оголошення (в розробці)</p>
    </div>
    <?php
    return ob_get_clean();
}

function renderProfilePage($db) {
    if (!isset($_SESSION['user_id'])) {
        return '<div class="container py-5"><div class="alert alert-warning">Увійдіть для перегляду профілю</div></div>';
    }
    
    ob_start();
    ?>
    <div class="container py-5">
        <h2 class="mb-4">Профіль користувача</h2>
        <p>Сторінка профілю (в розробці)</p>
    </div>
    <?php
    return ob_get_clean();
}

function renderFavoritesPage($db) {
    if (!isset($_SESSION['user_id'])) {
        return '<div class="container py-5"><div class="alert alert-warning">Увійдіть для перегляду вподобань</div></div>';
    }
    
    ob_start();
    ?>
    <div class="container py-5">
        <h2 class="mb-4">Вподобання</h2>
        <p>Сторінка вподобань (в розробці)</p>
    </div>
    <?php
    return ob_get_clean();
}

function render404Page() {
    ob_start();
    ?>
    <div class="container py-5 text-center">
        <i class="fas fa-exclamation-triangle fa-4x text-warning mb-4"></i>
        <h1 class="display-4">404</h1>
        <h2>Сторінка не знайдена</h2>
        <p class="lead">Вибачте, запитувана сторінка не існує.</p>
        <a href="index.php" class="btn btn-primary" data-spa>
            <i class="fas fa-home me-2"></i>На головну
        </a>
    </div>
    <?php
    return ob_get_clean();
}
?>