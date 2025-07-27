<?php
/**
 * Главная страница Marketplace
 */

// Подключение необходимых файлов
require_once 'config/config.php';
require_once 'functions/database.php';
require_once 'functions/helpers.php';

// Начало сессии
session_start();

// SEO данные для страницы
$page_title = 'Главная страница';
$page_description = 'Marketplace - современная торговая площадка с широким ассортиментом качественных товаров по выгодным ценам. Быстрая доставка по всей России.';
$page_keywords = 'marketplace, интернет-магазин, торговая площадка, товары, покупки онлайн, доставка';
$breadcrumbs = []; // Главная страница не нуждается в breadcrumbs

// Подключение шапки
include 'theme/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center min-vh-75">
            <div class="col-lg-6" data-animation="animate-slide-left">
                <div class="hero-content">
                    <h1 class="display-3 fw-bold mb-4">
                        Добро пожаловать в
                        <span class="text-gradient"><?php echo h(SITE_NAME); ?></span>
                    </h1>
                    <p class="lead mb-4 text-muted">
                        Откройте для себя мир качественных товаров с доставкой по всей России. 
                        Тысячи товаров от проверенных продавцов ждут вас!
                    </p>
                    
                    <!-- Статистика -->
                    <div class="row g-4 mb-4">
                        <div class="col-4">
                            <div class="text-center">
                                <div class="h3 fw-bold text-primary mb-0">50K+</div>
                                <small class="text-muted">Товаров</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center">
                                <div class="h3 fw-bold text-primary mb-0">1K+</div>
                                <small class="text-muted">Продавцов</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center">
                                <div class="h3 fw-bold text-primary mb-0">10K+</div>
                                <small class="text-muted">Покупателей</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- CTA кнопки -->
                    <div class="hero-actions">
                        <a href="/catalog" class="btn btn-3d me-3">
                            <i class="fas fa-shopping-bag me-2"></i>
                            Начать покупки
                        </a>
                        <a href="/seller/register" class="btn btn-3d outline">
                            <i class="fas fa-store me-2"></i>
                            Стать продавцом
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6" data-animation="animate-slide-right">
                <div class="hero-image">
                    <img src="<?php echo themeUrl('images/hero-image.jpg'); ?>" 
                         alt="Marketplace - интернет-покупки" 
                         class="img-fluid rounded-lg shadow-3d lazy-img"
                         data-src="<?php echo themeUrl('images/hero-image.jpg'); ?>">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Декоративные элементы -->
    <div class="hero-decoration">
        <div class="floating-elements">
            <div class="floating-icon" style="top: 15%; left: 10%; animation-delay: 0s;">
                <i class="fas fa-shopping-cart icon-3d"></i>
            </div>
            <div class="floating-icon" style="top: 25%; right: 15%; animation-delay: 1s;">
                <i class="fas fa-gift icon-3d"></i>
            </div>
            <div class="floating-icon" style="bottom: 30%; left: 20%; animation-delay: 2s;">
                <i class="fas fa-star icon-3d"></i>
            </div>
        </div>
    </div>
</section>

<!-- Преимущества -->
<section class="features-section py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center mb-5">
                <h2 class="display-5 fw-bold" data-animation="animate-fade-in">
                    Почему выбирают нас?
                </h2>
                <p class="lead text-muted" data-animation="animate-fade-in">
                    Мы создали платформу, которая объединяет лучшее для покупателей и продавцов
                </p>
            </div>
        </div>
        
        <div class="row g-4">
            <!-- Быстрая доставка -->
            <div class="col-lg-3 col-md-6" data-animation="animate-fade-in">
                <div class="card-3d h-100 text-center p-4">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-shipping-fast icon-3d"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Быстрая доставка</h5>
                    <p class="text-muted">
                        Доставка по всей России от 1 дня. 
                        Бесплатная доставка от 2000 руб.
                    </p>
                </div>
            </div>
            
            <!-- Безопасные платежи -->
            <div class="col-lg-3 col-md-6" data-animation="animate-fade-in">
                <div class="card-3d h-100 text-center p-4">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-shield-alt icon-3d"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Безопасные платежи</h5>
                    <p class="text-muted">
                        Все платежи защищены SSL. 
                        Поддержка всех популярных способов оплаты.
                    </p>
                </div>
            </div>
            
            <!-- Качественные товары -->
            <div class="col-lg-3 col-md-6" data-animation="animate-fade-in">
                <div class="card-3d h-100 text-center p-4">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-award icon-3d"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Качественные товары</h5>
                    <p class="text-muted">
                        Все продавцы проверены. 
                        Гарантия качества на все товары.
                    </p>
                </div>
            </div>
            
            <!-- Поддержка 24/7 -->
            <div class="col-lg-3 col-md-6" data-animation="animate-fade-in">
                <div class="card-3d h-100 text-center p-4">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-headset icon-3d"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Поддержка 24/7</h5>
                    <p class="text-muted">
                        Наша команда поддержки готова помочь 
                        в любое время дня и ночи.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Популярные категории -->
<section class="categories-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center mb-5">
                <h2 class="display-5 fw-bold" data-animation="animate-fade-in">
                    Популярные категории
                </h2>
                <p class="lead text-muted" data-animation="animate-fade-in">
                    Найдите то, что ищете в наших популярных категориях
                </p>
            </div>
        </div>
        
        <div class="row g-4">
            <!-- Электроника -->
            <div class="col-lg-4 col-md-6" data-animation="animate-slide-left">
                <a href="/catalog/electronics" class="text-decoration-none">
                    <div class="card-3d category-card">
                        <div class="category-image">
                            <img src="<?php echo themeUrl('images/categories/electronics.jpg'); ?>" 
                                 alt="Электроника" 
                                 class="img-fluid lazy-img"
                                 data-src="<?php echo themeUrl('images/categories/electronics.jpg'); ?>">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-laptop text-primary me-2"></i>
                                Электроника
                            </h5>
                            <p class="card-text text-muted">
                                Смартфоны, ноутбуки, планшеты и аксессуары
                            </p>
                            <span class="badge bg-primary">500+ товаров</span>
                        </div>
                    </div>
                </a>
            </div>
            
            <!-- Одежда и обувь -->
            <div class="col-lg-4 col-md-6" data-animation="animate-fade-in">
                <a href="/catalog/clothing" class="text-decoration-none">
                    <div class="card-3d category-card">
                        <div class="category-image">
                            <img src="<?php echo themeUrl('images/categories/clothing.jpg'); ?>" 
                                 alt="Одежда и обувь" 
                                 class="img-fluid lazy-img"
                                 data-src="<?php echo themeUrl('images/categories/clothing.jpg'); ?>">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-tshirt text-primary me-2"></i>
                                Одежда и обувь
                            </h5>
                            <p class="card-text text-muted">
                                Мужская, женская и детская одежда, обувь
                            </p>
                            <span class="badge bg-primary">1200+ товаров</span>
                        </div>
                    </div>
                </a>
            </div>
            
            <!-- Дом и сад -->
            <div class="col-lg-4 col-md-6" data-animation="animate-slide-right">
                <a href="/catalog/home" class="text-decoration-none">
                    <div class="card-3d category-card">
                        <div class="category-image">
                            <img src="<?php echo themeUrl('images/categories/home.jpg'); ?>" 
                                 alt="Дом и сад" 
                                 class="img-fluid lazy-img"
                                 data-src="<?php echo themeUrl('images/categories/home.jpg'); ?>">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-home text-primary me-2"></i>
                                Дом и сад
                            </h5>
                            <p class="card-text text-muted">
                                Мебель, декор, садовый инструмент
                            </p>
                            <span class="badge bg-primary">800+ товаров</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        
        <div class="text-center mt-5">
            <a href="/catalog" class="btn btn-3d">
                <i class="fas fa-th-large me-2"></i>
                Посмотреть все категории
            </a>
        </div>
    </div>
</section>

<!-- Популярные товары -->
<section class="products-section py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center mb-5">
                <h2 class="display-5 fw-bold" data-animation="animate-fade-in">
                    Популярные товары
                </h2>
                <p class="lead text-muted" data-animation="animate-fade-in">
                    Самые покупаемые товары на нашей площадке
                </p>
            </div>
        </div>
        
        <div class="row g-4" id="popular-products">
            <!-- Товары будут загружены через AJAX -->
            <div class="col-12 text-center">
                <div class="loader-3d"></div>
                <p class="mt-3 text-muted">Загрузка популярных товаров...</p>
            </div>
        </div>
    </div>
</section>

<!-- Отзывы клиентов -->
<section class="testimonials-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center mb-5">
                <h2 class="display-5 fw-bold" data-animation="animate-fade-in">
                    Что говорят наши клиенты
                </h2>
                <p class="lead text-muted" data-animation="animate-fade-in">
                    Более 10,000 довольных покупателей уже выбрали нас
                </p>
            </div>
        </div>
        
        <div class="row g-4">
            <!-- Отзыв 1 -->
            <div class="col-lg-4" data-animation="animate-slide-left">
                <div class="card-3d testimonial-card p-4">
                    <div class="stars mb-3">
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                    </div>
                    <blockquote class="mb-3">
                        "Отличная площадка! Быстрая доставка, качественные товары. 
                        Уже несколько раз покупал здесь и всегда остаюсь доволен."
                    </blockquote>
                    <div class="testimonial-author">
                        <img src="<?php echo themeUrl('images/testimonials/user1.jpg'); ?>" 
                             alt="Алексей К." 
                             class="rounded-circle me-3 lazy-img"
                             data-src="<?php echo themeUrl('images/testimonials/user1.jpg'); ?>"
                             width="50" height="50">
                        <div>
                            <div class="fw-bold">Алексей К.</div>
                            <small class="text-muted">Покупатель</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Отзыв 2 -->
            <div class="col-lg-4" data-animation="animate-fade-in">
                <div class="card-3d testimonial-card p-4">
                    <div class="stars mb-3">
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                    </div>
                    <blockquote class="mb-3">
                        "Как продавец, очень доволен платформой. Удобный интерфейс, 
                        честные условия, оперативная поддержка."
                    </blockquote>
                    <div class="testimonial-author">
                        <img src="<?php echo themeUrl('images/testimonials/user2.jpg'); ?>" 
                             alt="Мария С." 
                             class="rounded-circle me-3 lazy-img"
                             data-src="<?php echo themeUrl('images/testimonials/user2.jpg'); ?>"
                             width="50" height="50">
                        <div>
                            <div class="fw-bold">Мария С.</div>
                            <small class="text-muted">Продавец</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Отзыв 3 -->
            <div class="col-lg-4" data-animation="animate-slide-right">
                <div class="card-3d testimonial-card p-4">
                    <div class="stars mb-3">
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                    </div>
                    <blockquote class="mb-3">
                        "Современный дизайн, удобная навигация. Нашёл всё что нужно 
                        быстро и легко. Рекомендую!"
                    </blockquote>
                    <div class="testimonial-author">
                        <img src="<?php echo themeUrl('images/testimonials/user3.jpg'); ?>" 
                             alt="Дмитрий В." 
                             class="rounded-circle me-3 lazy-img"
                             data-src="<?php echo themeUrl('images/testimonials/user3.jpg'); ?>"
                             width="50" height="50">
                        <div>
                            <div class="fw-bold">Дмитрий В.</div>
                            <small class="text-muted">Покупатель</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section py-5 bg-gradient">
    <div class="container">
        <div class="row justify-content-center text-center text-white">
            <div class="col-lg-8" data-animation="animate-fade-in">
                <h2 class="display-5 fw-bold mb-4">
                    Готовы начать?
                </h2>
                <p class="lead mb-4">
                    Присоединяйтесь к тысячам довольных пользователей уже сегодня!
                </p>
                <div class="cta-buttons">
                    <a href="/register" class="btn btn-3d btn-lg me-3 text-dark bg-white">
                        <i class="fas fa-user-plus me-2"></i>
                        Зарегистрироваться
                    </a>
                    <a href="/catalog" class="btn btn-3d outline btn-lg">
                        <i class="fas fa-eye me-2"></i>
                        Посмотреть товары
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Дополнительная логика для главной страницы
$(document).ready(function() {
    // Загрузка популярных товаров
    loadPopularProducts();
    
    // Анимация счетчиков
    animateCounters();
    
    // Анимация floating элементов
    animateFloatingElements();
});

function loadPopularProducts() {
    $.ajax({
        url: '/api/products/popular',
        method: 'GET',
        success: function(response) {
            if (response.success && response.products) {
                displayProducts(response.products);
            } else {
                $('#popular-products').html('<div class="col-12 text-center"><p class="text-muted">Товары временно недоступны</p></div>');
            }
        },
        error: function() {
            $('#popular-products').html('<div class="col-12 text-center"><p class="text-muted">Ошибка загрузки товаров</p></div>');
        }
    });
}

function displayProducts(products) {
    let html = '';
    products.forEach(product => {
        html += `
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card-3d product-card h-100">
                    <div class="product-image">
                        <img src="${product.image}" alt="${product.name}" class="img-fluid lazy-img" data-src="${product.image}">
                        <div class="product-overlay">
                            <button class="btn btn-3d btn-sm add-to-cart" data-product-id="${product.id}">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                            <button class="btn btn-3d btn-sm" onclick="quickView(${product.id})">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">${product.name}</h6>
                        <p class="text-muted small">${product.description}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h6 text-primary mb-0">${product.price} ₽</span>
                            <div class="stars">
                                ${'★'.repeat(product.rating)}${'☆'.repeat(5-product.rating)}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    $('#popular-products').html(html).addClass('animate-fade-in');
    
    // Инициализация lazy loading для новых изображений
    initLazyLoading();
}

function animateCounters() {
    // Анимация счетчиков при появлении в области видимости
    const counters = document.querySelectorAll('.hero-content .h3');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = parseInt(entry.target.textContent.replace(/\D/g, ''));
                animateCounter(entry.target, target);
                observer.unobserve(entry.target);
            }
        });
    });
    
    counters.forEach(counter => observer.observe(counter));
}

function animateCounter(element, target) {
    let current = 0;
    const increment = target / 100;
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }
        element.textContent = Math.floor(current) + (target >= 1000 ? 'K+' : '+');
    }, 20);
}

function animateFloatingElements() {
    // CSS анимация уже прописана, добавляем класс для активации
    $('.floating-icon').addClass('animate-bounce');
}

function quickView(productId) {
    // Функция быстрого просмотра товара
    console.log('Quick view for product:', productId);
    // Здесь будет модальное окно с товаром
}
</script>

<style>
/* Дополнительные стили для главной страницы */
.hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    position: relative;
    overflow: hidden;
}

.min-vh-75 {
    min-height: 75vh;
}

.floating-elements {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
}

.floating-icon {
    position: absolute;
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

.category-card:hover {
    transform: translateY(-10px);
}

.category-image {
    height: 200px;
    overflow: hidden;
}

.category-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.category-card:hover .category-image img {
    transform: scale(1.1);
}

.product-card {
    position: relative;
    overflow: hidden;
}

.product-image {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.product-card:hover .product-overlay {
    opacity: 1;
}

.testimonial-card {
    height: 100%;
}

.testimonial-author {
    display: flex;
    align-items: center;
}

.stars {
    color: #ffc107;
}

.cta-section {
    background: var(--gradient-primary);
}

@media (max-width: 768px) {
    .hero-section {
        text-align: center;
    }
    
    .hero-actions {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    .hero-actions .btn {
        width: 100%;
    }
}
</style>

<?php
// Подключение подвала
include 'theme/footer.php';
?>
