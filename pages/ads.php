<?php
require_once '../core/config.php';
require_once '../core/functions.php';

// Обробка пошукових параметрів
$search = sanitize($_GET['search'] ?? '');
$categoryId = (int)($_GET['category'] ?? 0);
$locationId = (int)($_GET['location'] ?? 0);
$priceMin = (int)($_GET['price_min'] ?? 0);
$priceMax = (int)($_GET['price_max'] ?? 0);
$sortBy = sanitize($_GET['sort'] ?? 'newest');
$page = (int)($_GET['page'] ?? 1);
$perPage = 12;

// Отримання даних
$categories = getCategories();
$locations = getLocations();
$ads = searchAds($search, $categoryId, $locationId, $priceMin, $priceMax, $sortBy, $page, $perPage);
$totalAds = countAds($search, $categoryId, $locationId, $priceMin, $priceMax);
$totalPages = ceil($totalAds / $perPage);

// Збереження пошукового запиту для аналітики
if (!empty($search) || $categoryId || $locationId) {
    saveSearchQuery($search, $categoryId, $locationId, $totalAds);
}

include '../themes/header.php';
?>

<div class="ads-page">
    <!-- Hero секція з пошуком -->
    <section class="hero-search">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <h1 class="hero-title animate__animated animate__fadeInLeft">
                            <i class="fas fa-bullhorn text-primary me-3"></i>
                            Знайди те, що шукаєш
                        </h1>
                        <p class="hero-subtitle animate__animated animate__fadeInLeft animate__delay-1s">
                            Тисячі актуальних оголошень в Україні. Купуй, продавай, обмінюй легко та безпечно!
                        </p>
                        <div class="hero-stats animate__animated animate__fadeInUp animate__delay-2s">
                            <div class="stat-item">
                                <span class="stat-number"><?php echo number_format(getTotalAdsCount()); ?></span>
                                <span class="stat-label">Оголошень</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number"><?php echo number_format(getActiveUsersCount()); ?></span>
                                <span class="stat-label">Користувачів</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number"><?php echo count($categories); ?></span>
                                <span class="stat-label">Категорій</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="search-widget animate__animated animate__fadeInRight">
                        <form method="GET" id="searchForm" class="search-form">
                            <div class="search-header">
                                <h3><i class="fas fa-search me-2"></i>Розширений пошук</h3>
                            </div>
                            
                            <div class="search-fields">
                                <div class="form-group mb-3">
                                    <label class="form-label">Що шукаєте?</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text" 
                                               class="form-control" 
                                               name="search" 
                                               placeholder="Введіть назву товару, послуги..."
                                               value="<?php echo htmlspecialchars($search); ?>">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Категорія</label>
                                            <select class="form-select" name="category" id="categorySelect">
                                                <option value="">Всі категорії</option>
                                                <?php foreach ($categories as $category): ?>
                                                    <option value="<?php echo $category['id']; ?>" 
                                                            <?php echo $categoryId == $category['id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($category['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Місто</label>
                                            <select class="form-select" name="location">
                                                <option value="">Вся Україна</option>
                                                <?php foreach ($locations as $location): ?>
                                                    <option value="<?php echo $location['id']; ?>"
                                                            <?php echo $locationId == $location['id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($location['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Ціна від (грн)</label>
                                            <input type="number" 
                                                   class="form-control" 
                                                   name="price_min" 
                                                   placeholder="0"
                                                   value="<?php echo $priceMin ?: ''; ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Ціна до (грн)</label>
                                            <input type="number" 
                                                   class="form-control" 
                                                   name="price_max" 
                                                   placeholder="100000"
                                                   value="<?php echo $priceMax ?: ''; ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="search-actions">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-search me-2"></i>Знайти
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="clearSearch()">
                                        <i class="fas fa-times me-2"></i>Очистити
                                    </button>
                                    <a href="#" class="btn btn-outline-primary" onclick="saveCurrentSearch()">
                                        <i class="fas fa-bookmark me-2"></i>Зберегти пошук
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Популярні категорії -->
    <section class="popular-categories py-5">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="section-title text-center mb-5">
                        <i class="fas fa-fire text-danger me-2"></i>Популярні категорії
                    </h2>
                </div>
            </div>
            
            <div class="row">
                <?php foreach (array_slice($categories, 0, 8) as $category): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <a href="?category=<?php echo $category['id']; ?>" 
                           class="category-card" 
                           data-aos="fade-up" 
                           data-aos-delay="<?php echo ($category['sort_order'] * 100); ?>">
                            <div class="category-icon">
                                <i class="<?php echo $category['icon']; ?>"></i>
                            </div>
                            <div class="category-info">
                                <h4><?php echo htmlspecialchars($category['name']); ?></h4>
                                <span class="ads-count"><?php echo getAdsByCategory($category['id']); ?> оголошень</span>
                            </div>
                            <div class="category-arrow">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    
    <!-- Результати пошуку -->
    <section class="search-results py-5">
        <div class="container">
            <!-- Заголовок та фільтри -->
            <div class="results-header">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h2 class="results-title">
                            <?php if ($search): ?>
                                Результати пошуку: "<?php echo htmlspecialchars($search); ?>"
                            <?php elseif ($categoryId): ?>
                                <?php 
                                $currentCategory = array_filter($categories, function($cat) use ($categoryId) {
                                    return $cat['id'] == $categoryId;
                                });
                                $currentCategory = reset($currentCategory);
                                ?>
                                <?php echo htmlspecialchars($currentCategory['name']); ?>
                            <?php else: ?>
                                Всі оголошення
                            <?php endif; ?>
                        </h2>
                        <p class="results-count">Знайдено <strong><?php echo number_format($totalAds); ?></strong> оголошень</p>
                    </div>
                    
                    <div class="col-lg-6">
                        <div class="results-controls">
                            <div class="view-mode">
                                <button class="btn btn-outline-secondary active" data-view="grid" title="Сітка">
                                    <i class="fas fa-th"></i>
                                </button>
                                <button class="btn btn-outline-secondary" data-view="list" title="Список">
                                    <i class="fas fa-list"></i>
                                </button>
                            </div>
                            
                            <div class="sort-options">
                                <select class="form-select" name="sort" onchange="updateSort(this.value)">
                                    <option value="newest" <?php echo $sortBy === 'newest' ? 'selected' : ''; ?>>Найновіші</option>
                                    <option value="oldest" <?php echo $sortBy === 'oldest' ? 'selected' : ''; ?>>Найстаріші</option>
                                    <option value="price_asc" <?php echo $sortBy === 'price_asc' ? 'selected' : ''; ?>>Ціна: дешевші</option>
                                    <option value="price_desc" <?php echo $sortBy === 'price_desc' ? 'selected' : ''; ?>>Ціна: дорожчі</option>
                                    <option value="popular" <?php echo $sortBy === 'popular' ? 'selected' : ''; ?>>Популярні</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Активні фільтри -->
                <?php if ($search || $categoryId || $locationId || $priceMin || $priceMax): ?>
                    <div class="active-filters">
                        <h6>Активні фільтри:</h6>
                        <div class="filter-tags">
                            <?php if ($search): ?>
                                <span class="filter-tag">
                                    Пошук: "<?php echo htmlspecialchars($search); ?>"
                                    <a href="<?php echo removeParam('search'); ?>" class="remove-filter">×</a>
                                </span>
                            <?php endif; ?>
                            
                            <?php if ($categoryId): ?>
                                <span class="filter-tag">
                                    Категорія: <?php echo htmlspecialchars($currentCategory['name']); ?>
                                    <a href="<?php echo removeParam('category'); ?>" class="remove-filter">×</a>
                                </span>
                            <?php endif; ?>
                            
                            <?php if ($locationId): ?>
                                <?php 
                                $currentLocation = array_filter($locations, function($loc) use ($locationId) {
                                    return $loc['id'] == $locationId;
                                });
                                $currentLocation = reset($currentLocation);
                                ?>
                                <span class="filter-tag">
                                    Місто: <?php echo htmlspecialchars($currentLocation['name']); ?>
                                    <a href="<?php echo removeParam('location'); ?>" class="remove-filter">×</a>
                                </span>
                            <?php endif; ?>
                            
                            <?php if ($priceMin || $priceMax): ?>
                                <span class="filter-tag">
                                    Ціна: <?php echo $priceMin ?: '0'; ?> - <?php echo $priceMax ?: '∞'; ?> грн
                                    <a href="<?php echo removeParam(['price_min', 'price_max']); ?>" class="remove-filter">×</a>
                                </span>
                            <?php endif; ?>
                            
                            <a href="<?php echo strtok($_SERVER['REQUEST_URI'], '?'); ?>" class="clear-all-filters">
                                <i class="fas fa-times me-1"></i>Очистити всі
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Оголошення -->
            <div class="ads-grid" id="adsContainer">
                <?php if (empty($ads)): ?>
                    <div class="no-results">
                        <div class="no-results-icon">
                            <i class="fas fa-search fa-3x text-muted"></i>
                        </div>
                        <h3>Оголошень не знайдено</h3>
                        <p class="text-muted">Спробуйте змінити параметри пошуку або переглянути всі категорії</p>
                        <a href="?" class="btn btn-primary">Переглянути всі оголошення</a>
                    </div>
                <?php else: ?>
                    <div class="row" id="adsGrid">
                        <?php foreach ($ads as $ad): ?>
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="ad-card" data-aos="fade-up">
                                    <?php if ($ad['is_featured']): ?>
                                        <div class="ad-featured-badge">
                                            <i class="fas fa-star"></i> Рекомендоване
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($ad['is_urgent']): ?>
                                        <div class="ad-urgent-badge">
                                            <i class="fas fa-exclamation"></i> Термінове
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="ad-image">
                                        <?php $mainImage = getAdMainImage($ad['id']); ?>
                                        <img src="<?php echo $mainImage ?: '../images/no-image.svg'; ?>" 
                                             alt="<?php echo htmlspecialchars($ad['title']); ?>"
                                             loading="lazy">
                                        <div class="ad-overlay">
                                            <a href="ad-detail.php?id=<?php echo $ad['id']; ?>" class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye me-1"></i>Переглянути
                                            </a>
                                            <button class="btn btn-outline-light btn-sm" onclick="toggleFavorite(<?php echo $ad['id']; ?>)">
                                                <i class="fas fa-heart"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="ad-content">
                                        <div class="ad-header">
                                            <h5 class="ad-title">
                                                <a href="ad-detail.php?id=<?php echo $ad['id']; ?>">
                                                    <?php echo htmlspecialchars($ad['title']); ?>
                                                </a>
                                            </h5>
                                            <?php if ($ad['price']): ?>
                                                <div class="ad-price">
                                                    <?php echo number_format($ad['price']); ?> <?php echo $ad['currency']; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="ad-description">
                                            <?php echo truncateText($ad['description'], 100); ?>
                                        </div>
                                        
                                        <div class="ad-meta">
                                            <div class="ad-location">
                                                <i class="fas fa-map-marker-alt text-muted me-1"></i>
                                                <?php echo htmlspecialchars($ad['location_name']); ?>
                                            </div>
                                            <div class="ad-date">
                                                <i class="fas fa-clock text-muted me-1"></i>
                                                <?php echo timeAgo($ad['created_at']); ?>
                                            </div>
                                        </div>
                                        
                                        <div class="ad-stats">
                                            <span class="stat-item">
                                                <i class="fas fa-eye"></i> <?php echo $ad['views_count']; ?>
                                            </span>
                                            <span class="stat-item">
                                                <i class="fas fa-heart"></i> <?php echo $ad['favorites_count']; ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Пагінація -->
                    <?php if ($totalPages > 1): ?>
                        <nav aria-label="Пагінація оголошень" class="mt-5">
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?php echo buildPaginationUrl($page - 1); ?>">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="<?php echo buildPaginationUrl($i); ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($page < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?php echo buildPaginationUrl($page + 1); ?>">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<style>
.hero-search {
    background: linear-gradient(135deg, var(--current-gradient)), url('images/hero-bg.jpg');
    background-size: cover;
    background-position: center;
    padding: 100px 0;
    position: relative;
    overflow: hidden;
}

.hero-search::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.4);
    z-index: 1;
}

.hero-content, .search-widget {
    position: relative;
    z-index: 2;
}

.hero-title {
    font-size: 3rem;
    font-weight: 700;
    color: white;
    margin-bottom: 1rem;
}

.hero-subtitle {
    font-size: 1.2rem;
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 2rem;
}

.hero-stats {
    display: flex;
    gap: 2rem;
}

.stat-item {
    text-align: center;
    color: white;
}

.stat-number {
    display: block;
    font-size: 2rem;
    font-weight: 700;
    color: #ffc107;
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.8;
}

.search-widget {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.search-header h3 {
    color: var(--primary-color);
    margin-bottom: 1.5rem;
}

.search-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.popular-categories {
    background: #f8f9fa;
}

.category-card {
    display: flex;
    align-items: center;
    padding: 1.5rem;
    background: white;
    border-radius: 10px;
    text-decoration: none;
    color: inherit;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    border-color: var(--primary-color);
    text-decoration: none;
    color: inherit;
}

.category-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--current-gradient));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    flex-shrink: 0;
}

.category-icon i {
    font-size: 1.5rem;
    color: white;
}

.category-info {
    flex: 1;
}

.category-info h4 {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: #333;
}

.ads-count {
    color: #666;
    font-size: 0.9rem;
}

.category-arrow {
    color: var(--primary-color);
    font-size: 1.2rem;
    opacity: 0;
    transition: all 0.3s ease;
}

.category-card:hover .category-arrow {
    opacity: 1;
    transform: translateX(5px);
}

.results-header {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #eee;
}

.results-controls {
    display: flex;
    align-items: center;
    gap: 1rem;
    justify-content: flex-end;
}

.view-mode {
    display: flex;
    border: 1px solid #ddd;
    border-radius: 5px;
    overflow: hidden;
}

.view-mode .btn {
    border: none;
    border-radius: 0;
}

.view-mode .btn.active {
    background: var(--primary-color);
    color: white;
}

.active-filters {
    margin-top: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.filter-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    align-items: center;
}

.filter-tag {
    background: var(--primary-color);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.remove-filter, .clear-all-filters {
    color: white;
    text-decoration: none;
    font-weight: bold;
}

.remove-filter:hover, .clear-all-filters:hover {
    color: #ffc107;
}

.ad-card {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    position: relative;
}

.ad-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}

.ad-featured-badge, .ad-urgent-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    z-index: 3;
}

.ad-featured-badge {
    background: linear-gradient(135deg, #ffc107, #ff8f00);
    color: white;
}

.ad-urgent-badge {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
    top: 40px;
}

.ad-image {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.ad-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.ad-card:hover .ad-image img {
    transform: scale(1.05);
}

.ad-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.ad-card:hover .ad-overlay {
    opacity: 1;
}

.ad-content {
    padding: 1.25rem;
}

.ad-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.ad-title {
    font-size: 1rem;
    font-weight: 600;
    margin: 0;
    flex: 1;
    margin-right: 1rem;
}

.ad-title a {
    color: #333;
    text-decoration: none;
}

.ad-title a:hover {
    color: var(--primary-color);
}

.ad-price {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--primary-color);
    white-space: nowrap;
}

.ad-description {
    color: #666;
    font-size: 0.9rem;
    line-height: 1.4;
    margin-bottom: 1rem;
}

.ad-meta {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1rem;
    font-size: 0.85rem;
    color: #666;
}

.ad-stats {
    display: flex;
    gap: 1rem;
    font-size: 0.8rem;
    color: #999;
}

.no-results {
    text-align: center;
    padding: 4rem 2rem;
    color: #666;
}

.no-results-icon {
    margin-bottom: 2rem;
}

@media (max-width: 768px) {
    .hero-title {
        font-size: 2rem;
    }
    
    .hero-stats {
        flex-direction: column;
        gap: 1rem;
    }
    
    .search-widget {
        padding: 1.5rem;
        margin-top: 2rem;
    }
    
    .search-actions {
        flex-direction: column;
    }
    
    .results-controls {
        flex-direction: column;
        align-items: stretch;
        gap: 0.5rem;
    }
    
    .filter-tags {
        justify-content: center;
    }
    
    .category-card {
        padding: 1rem;
    }
    
    .category-icon {
        width: 50px;
        height: 50px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ініціалізація AOS анімацій
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 600,
            easing: 'ease-out-sine',
            once: true
        });
    }
    
    // Збереження стану пошуку в localStorage
    saveSearchState();
    
    // Автодоповнення пошуку
    setupSearchAutocomplete();
});

function clearSearch() {
    document.getElementById('searchForm').reset();
    window.location.href = window.location.pathname;
}

function updateSort(sortValue) {
    const url = new URL(window.location);
    url.searchParams.set('sort', sortValue);
    url.searchParams.set('page', '1'); // Скидаємо на першу сторінку
    window.location.href = url.toString();
}

function toggleFavorite(adId) {
    if (!isLoggedIn()) {
        showLoginModal();
        return;
    }
    
    fetch('ajax/toggle_favorite.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ ad_id: adId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateFavoriteButton(adId, data.is_favorite);
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        showNotification('Помилка при додаванні в улюблені', 'error');
    });
}

function saveCurrentSearch() {
    if (!isLoggedIn()) {
        showLoginModal();
        return;
    }
    
    const searchData = {
        query: document.querySelector('[name="search"]').value,
        category: document.querySelector('[name="category"]').value,
        location: document.querySelector('[name="location"]').value,
        price_min: document.querySelector('[name="price_min"]').value,
        price_max: document.querySelector('[name="price_max"]').value
    };
    
    const name = prompt('Назва збереженого пошуку:');
    if (!name) return;
    
    fetch('ajax/save_search.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ ...searchData, name })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Пошук збережено!', 'success');
        } else {
            showNotification(data.message, 'error');
        }
    });
}

function saveSearchState() {
    const searchForm = document.getElementById('searchForm');
    const formData = new FormData(searchForm);
    const searchState = {};
    
    for (let [key, value] of formData.entries()) {
        if (value) searchState[key] = value;
    }
    
    localStorage.setItem('lastSearch', JSON.stringify(searchState));
}

function setupSearchAutocomplete() {
    const searchInput = document.querySelector('[name="search"]');
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            hideAutocomplete();
            return;
        }
        
        searchTimeout = setTimeout(() => {
            fetch(`ajax/search_suggestions.php?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    showAutocomplete(data.suggestions);
                });
        }, 300);
    });
}

function showAutocomplete(suggestions) {
    // Реалізація автодоповнення
}

function hideAutocomplete() {
    // Приховування автодоповнення
}

// Переключення режиму перегляду
document.querySelectorAll('[data-view]').forEach(btn => {
    btn.addEventListener('click', function() {
        const view = this.dataset.view;
        const container = document.getElementById('adsGrid');
        
        // Оновлення активної кнопки
        document.querySelectorAll('[data-view]').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        // Зміна класів контейнера
        if (view === 'list') {
            container.classList.add('list-view');
        } else {
            container.classList.remove('list-view');
        }
        
        // Збереження в localStorage
        localStorage.setItem('adsView', view);
    });
});

// Відновлення режиму перегляду
const savedView = localStorage.getItem('adsView');
if (savedView) {
    document.querySelector(`[data-view="${savedView}"]`)?.click();
}
</script>

<?php 
include '../themes/footer.php';

// Функції для роботи з оголошеннями
function getCategories() {
    try {
        $db = new Database();
        $result = $db->query("
            SELECT * FROM categories 
            WHERE is_active = 1 AND parent_id IS NULL
            ORDER BY sort_order ASC
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function getLocations() {
    try {
        $db = new Database();
        $result = $db->query("
            SELECT * FROM locations 
            WHERE is_active = 1 
            ORDER BY sort_order ASC
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function searchAds($search, $categoryId, $locationId, $priceMin, $priceMax, $sortBy, $page, $perPage) {
    try {
        $db = new Database();
        $offset = ($page - 1) * $perPage;
        
        $where = ["a.status = 'active'"];
        $params = [];
        $types = '';
        
        if ($search) {
            $where[] = "MATCH(a.title, a.description) AGAINST(? IN NATURAL LANGUAGE MODE)";
            $params[] = $search;
            $types .= 's';
        }
        
        if ($categoryId) {
            $where[] = "a.category_id = ?";
            $params[] = $categoryId;
            $types .= 'i';
        }
        
        if ($locationId) {
            $where[] = "a.location_id = ?";
            $params[] = $locationId;
            $types .= 'i';
        }
        
        if ($priceMin) {
            $where[] = "a.price >= ?";
            $params[] = $priceMin;
            $types .= 'd';
        }
        
        if ($priceMax) {
            $where[] = "a.price <= ?";
            $params[] = $priceMax;
            $types .= 'd';
        }
        
        $orderBy = match($sortBy) {
            'oldest' => 'a.created_at ASC',
            'price_asc' => 'a.price ASC',
            'price_desc' => 'a.price DESC',
            'popular' => 'a.views_count DESC',
            default => 'a.is_featured DESC, a.is_urgent DESC, a.created_at DESC'
        };
        
        $sql = "
            SELECT a.*, c.name as category_name, l.name as location_name
            FROM ads a
            JOIN categories c ON a.category_id = c.id
            JOIN locations l ON a.location_id = l.id
            WHERE " . implode(' AND ', $where) . "
            ORDER BY {$orderBy}
            LIMIT ? OFFSET ?
        ";
        
        $params[] = $perPage;
        $params[] = $offset;
        $types .= 'ii';
        
        $stmt = $db->prepare($sql);
        if ($types) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function countAds($search, $categoryId, $locationId, $priceMin, $priceMax) {
    try {
        $db = new Database();
        
        $where = ["status = 'active'"];
        $params = [];
        $types = '';
        
        if ($search) {
            $where[] = "MATCH(title, description) AGAINST(? IN NATURAL LANGUAGE MODE)";
            $params[] = $search;
            $types .= 's';
        }
        
        if ($categoryId) {
            $where[] = "category_id = ?";
            $params[] = $categoryId;
            $types .= 'i';
        }
        
        if ($locationId) {
            $where[] = "location_id = ?";
            $params[] = $locationId;
            $types .= 'i';
        }
        
        if ($priceMin) {
            $where[] = "price >= ?";
            $params[] = $priceMin;
            $types .= 'd';
        }
        
        if ($priceMax) {
            $where[] = "price <= ?";
            $params[] = $priceMax;
            $types .= 'd';
        }
        
        $sql = "SELECT COUNT(*) as total FROM ads WHERE " . implode(' AND ', $where);
        
        $stmt = $db->prepare($sql);
        if ($types) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return (int)$row['total'];
    } catch (Exception $e) {
        return 0;
    }
}

function getTotalAdsCount() {
    try {
        $db = new Database();
        $result = $db->query("SELECT COUNT(*) as total FROM ads WHERE status = 'active'");
        $row = $result->fetch_assoc();
        return (int)$row['total'];
    } catch (Exception $e) {
        return 0;
    }
}

function getActiveUsersCount() {
    try {
        $db = new Database();
        $result = $db->query("SELECT COUNT(*) as total FROM users WHERE status = 'active'");
        $row = $result->fetch_assoc();
        return (int)$row['total'];
    } catch (Exception $e) {
        return 0;
    }
}

function getAdsByCategory($categoryId) {
    try {
        $db = new Database();
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM ads WHERE category_id = ? AND status = 'active'");
        $stmt->bind_param("i", $categoryId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return (int)$row['total'];
    } catch (Exception $e) {
        return 0;
    }
}

function getAdMainImage($adId) {
    try {
        $db = new Database();
        $stmt = $db->prepare("SELECT filename FROM ad_images WHERE ad_id = ? AND is_main = 1 LIMIT 1");
        $stmt->bind_param("i", $adId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row ? 'images/uploads/' . $row['filename'] : null;
    } catch (Exception $e) {
        return null;
    }
}

function saveSearchQuery($query, $categoryId, $locationId, $resultsCount) {
    try {
        $db = new Database();
        $userId = isLoggedIn() ? $_SESSION['user_id'] : null;
        $ip = $_SERVER['REMOTE_ADDR'];
        
        $stmt = $db->prepare("
            INSERT INTO search_queries (user_id, query, category_id, location_id, results_count, ip_address) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("isiiis", $userId, $query, $categoryId, $locationId, $resultsCount, $ip);
        $stmt->execute();
    } catch (Exception $e) {
        // Ігноруємо помилки логування
    }
}

function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'щойно';
    if ($time < 3600) return floor($time/60) . ' хв тому';
    if ($time < 86400) return floor($time/3600) . ' год тому';
    if ($time < 2592000) return floor($time/86400) . ' дн тому';
    
    return date('d.m.Y', strtotime($datetime));
}

function removeParam($param) {
    $params = $_GET;
    if (is_array($param)) {
        foreach ($param as $p) {
            unset($params[$p]);
        }
    } else {
        unset($params[$param]);
    }
    
    return '?' . http_build_query($params);
}

function buildPaginationUrl($page) {
    $params = $_GET;
    $params['page'] = $page;
    return '?' . http_build_query($params);
}
?>