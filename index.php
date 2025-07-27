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

$page_title = 'Главная страница';

// Подключение шапки
include 'theme/header.php';
?>

<div class="container my-5">
    <!-- Приветственный блок -->
    <div class="hero-section bg-primary text-white rounded p-5 mb-5">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-4 fw-bold">Добро пожаловать в <?php echo h(SITE_NAME); ?>!</h1>
                <p class="lead">Ваша надежная торговая площадка с широким ассортиментом товаров</p>
                <a href="/catalog" class="btn btn-light btn-lg">
                    <i class="fas fa-shopping-bag"></i> Перейти к покупкам
                </a>
            </div>
            <div class="col-md-4 text-center">
                <i class="fas fa-store fa-5x opacity-50"></i>
            </div>
        </div>
    </div>
    
    <!-- Преимущества -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="text-center mb-4">Почему выбирают нас?</h2>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-shipping-fast fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">Быстрая доставка</h5>
                    <p class="card-text">Доставим ваш заказ в кратчайшие сроки по всей России</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-shield-alt fa-3x text-success mb-3"></i>
                    <h5 class="card-title">Гарантия качества</h5>
                    <p class="card-text">Все товары проходят проверку качества перед отправкой</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-headset fa-3x text-info mb-3"></i>
                    <h5 class="card-title">Поддержка 24/7</h5>
                    <p class="card-text">Наша служба поддержки готова помочь вам в любое время</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Популярные категории -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="text-center mb-4">Популярные категории</h2>
        </div>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card category-card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-laptop fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">Электроника</h5>
                    <p class="card-text">Смартфоны, ноутбуки, планшеты</p>
                    <a href="/catalog?category=electronics" class="btn btn-outline-primary">Смотреть</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card category-card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-tshirt fa-3x text-success mb-3"></i>
                    <h5 class="card-title">Одежда</h5>
                    <p class="card-text">Мужская и женская одежда</p>
                    <a href="/catalog?category=clothing" class="btn btn-outline-success">Смотреть</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card category-card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-home fa-3x text-warning mb-3"></i>
                    <h5 class="card-title">Дом и сад</h5>
                    <p class="card-text">Товары для дома и дачи</p>
                    <a href="/catalog?category=home" class="btn btn-outline-warning">Смотреть</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card category-card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-dumbbell fa-3x text-danger mb-3"></i>
                    <h5 class="card-title">Спорт</h5>
                    <p class="card-text">Спортивные товары и инвентарь</p>
                    <a href="/catalog?category=sport" class="btn btn-outline-danger">Смотреть</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Популярные товары -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="text-center mb-4">Популярные товары</h2>
            <p class="text-center text-muted mb-4">Товары, которые покупают чаще всего</p>
        </div>
        
        <!-- Здесь будут отображаться популярные товары из базы данных -->
        <div class="col-12 text-center">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> 
                Популярные товары будут отображаться после добавления товаров в каталог
            </div>
            <a href="/admin" class="btn btn-primary">
                <i class="fas fa-plus"></i> Добавить товары
            </a>
        </div>
    </div>
    
    <!-- Newsletter подписка -->
    <div class="row">
        <div class="col-12">
            <div class="card bg-light">
                <div class="card-body text-center py-5">
                    <h3>Подписаться на новости</h3>
                    <p class="lead">Получайте уведомления о новых товарах и акциях</p>
                    <form class="row justify-content-center" id="newsletter-form">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="email" class="form-control" placeholder="Ваш email адрес" required>
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-envelope"></i> Подписаться
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hero-section {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.category-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}

.category-card .card-body {
    padding: 2rem;
}
</style>

<script>
// Обработка подписки на newsletter
document.getElementById('newsletter-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const email = this.querySelector('input[type="email"]').value;
    
    // AJAX запрос для подписки
    fetch('/api/newsletter/subscribe', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ email: email })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Спасибо за подписку!');
            this.querySelector('input[type="email"]').value = '';
        } else {
            alert('Ошибка при подписке: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Произошла ошибка при подписке');
    });
});
</script>

<?php
// Подключение подвала
include 'theme/footer.php';
?>
