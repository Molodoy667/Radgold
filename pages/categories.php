<?php
require_once '../config/config.php';
require_once '../config/database.php';

$page_title = 'Категорії';

// Підключення до бази даних
$database = new Database();
$db = $database->getConnection();

// Отримання категорій з кількістю оголошень
$query = "SELECT c.*, 
          (SELECT COUNT(*) FROM ads WHERE category_id = c.id AND status = 'active') as ads_count
          FROM categories c 
          ORDER BY c.name";
$stmt = $db->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" data-aos="fade-down">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php">Головна</a></li>
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
            <form action="search.php" method="GET" class="position-relative">
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
                <a href="category.php?id=<?php echo $category['id']; ?>" 
                   class="category-card d-block p-4 text-center text-decoration-none position-relative">
                   
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
    
    <?php if(empty($categories)): ?>
        <div class="text-center py-5" data-aos="fade-up">
            <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">Категорії не знайдено</h4>
            <p class="text-muted">Зверніться до адміністратора для додавання категорій.</p>
        </div>
    <?php endif; ?>
    
    <!-- Statistics -->
    <div class="row mt-5 pt-5 border-top" data-aos="fade-up">
        <div class="col-md-4 text-center mb-3">
            <div class="text-primary">
                <i class="fas fa-list fa-2x mb-2"></i>
                <h4><?php echo count($categories); ?></h4>
                <p class="text-muted mb-0">Категорій</p>
            </div>
        </div>
        <div class="col-md-4 text-center mb-3">
            <div class="text-success">
                <i class="fas fa-bullhorn fa-2x mb-2"></i>
                <h4><?php echo array_sum(array_column($categories, 'ads_count')); ?></h4>
                <p class="text-muted mb-0">Активних оголошень</p>
            </div>
        </div>
        <div class="col-md-4 text-center mb-3">
            <div class="text-warning">
                <i class="fas fa-crown fa-2x mb-2"></i>
                <?php 
                $most_popular = array_reduce($categories, function($max, $cat) {
                    return $cat['ads_count'] > $max['ads_count'] ? $cat : $max;
                }, ['ads_count' => 0, 'name' => 'Немає']);
                ?>
                <h6 class="mb-1"><?php echo htmlspecialchars($most_popular['name']); ?></h6>
                <p class="text-muted mb-0">Найпопулярніша</p>
            </div>
        </div>
    </div>
</div>

<script>
// Фільтрація категорій
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        // Оновлюємо активну кнопку
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        const filter = this.dataset.filter;
        applyFilter(filter);
    });
});

// Пошук в категоріях
document.getElementById('categorySearch').addEventListener('input', function() {
    const query = this.value.toLowerCase();
    const cards = document.querySelectorAll('.filterable-item');
    
    cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        if (text.includes(query)) {
            card.style.display = 'block';
            card.style.animation = 'fadeIn 0.5s ease';
        } else {
            card.style.display = 'none';
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>