<!-- Modern Navigation Menu -->
<section class="modern-nav-menu py-4 mb-4">
    <div class="container-fluid px-3 px-md-4">
        <div class="nav-menu-container">
            <div class="nav-menu-grid">
                <!-- Оголошення -->
                <a href="<?php echo function_exists('getSiteUrl') ? getSiteUrl('ads') : '/ads'; ?>" class="nav-menu-item" data-category="ads">
                    <div class="menu-item-icon">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <div class="menu-item-content">
                        <h5>Оголошення</h5>
                        <p>Тисячі актуальних пропозицій</p>
                    </div>
                    <div class="menu-item-badge">
                        <span>Нові</span>
                    </div>
                    <div class="menu-item-decoration"></div>
                </a>
                
                <!-- Послуги -->
                <a href="<?php echo function_exists('getSiteUrl') ? getSiteUrl('services') : '/services'; ?>" class="nav-menu-item" data-category="services">
                    <div class="menu-item-icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <div class="menu-item-content">
                        <h5>Послуги</h5>
                        <p>Професійні рішення</p>
                    </div>
                    <div class="menu-item-badge hot">
                        <span>ТОП</span>
                    </div>
                    <div class="menu-item-decoration"></div>
                </a>
                
                <!-- Категорії -->
                <a href="<?php echo function_exists('getSiteUrl') ? getSiteUrl('categories') : '/categories'; ?>" class="nav-menu-item" data-category="categories">
                    <div class="menu-item-icon">
                        <i class="fas fa-th-large"></i>
                    </div>
                    <div class="menu-item-content">
                        <h5>Категорії</h5>
                        <p>Всі розділи в одному місці</p>
                    </div>
                    <div class="menu-item-decoration"></div>
                </a>
                
                <!-- Створити оголошення -->
                <a href="<?php echo function_exists('getSiteUrl') ? getSiteUrl('create-ad') : '/create-ad'; ?>" class="nav-menu-item featured" data-category="create">
                    <div class="menu-item-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div class="menu-item-content">
                        <h5>Створити</h5>
                        <p>Додайте оголошення</p>
                    </div>
                    <div class="menu-item-badge premium">
                        <span>+</span>
                    </div>
                    <div class="menu-item-decoration"></div>
                </a>
                
                <!-- Про нас -->
                <a href="<?php echo function_exists('getSiteUrl') ? getSiteUrl('about') : '/about'; ?>" class="nav-menu-item" data-category="about">
                    <div class="menu-item-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="menu-item-content">
                        <h5>Про нас</h5>
                        <p>Історія та місія</p>
                    </div>
                    <div class="menu-item-decoration"></div>
                </a>
                
                <!-- Контакти -->
                <a href="<?php echo function_exists('getSiteUrl') ? getSiteUrl('contact') : '/contact'; ?>" class="nav-menu-item" data-category="contact">
                    <div class="menu-item-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="menu-item-content">
                        <h5>Контакти</h5>
                        <p>Зв'яжіться з нами</p>
                    </div>
                    <div class="menu-item-decoration"></div>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Hero Section -->
<section class="hero-section py-5 mb-5 position-relative overflow-hidden">
    <div class="gradient-bg position-absolute w-100 h-100" style="opacity: 0.1;"></div>
    <div class="container-fluid px-3 px-md-4 position-relative">
        <div class="row align-items-center min-vh-75 g-4">
            <div class="col-lg-6 col-md-12" data-aos="fade-right">
                <h1 class="display-4 display-md-3 fw-bold mb-4 text-center text-lg-start">
                    Ваша реклама - наш успіх!
                    <span class="text-gradient d-block">AdBoard Pro</span>
                </h1>
                <p class="lead mb-4 text-center text-lg-start fs-5 fs-md-4">
                    Професійна рекламна компанія та сучасна дошка оголошень. 
                    Ми допоможемо вашому бізнесу досягти нових висот та знайти цільову аудиторію.
                </p>
                <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center justify-content-lg-start">
                    <a href="<?php echo function_exists('getSiteUrl') ? getSiteUrl('ads') : '/ads'; ?>" class="btn gradient-bg text-white btn-lg hover-scale">
                        <i class="fas fa-search me-2"></i>Переглянути оголошення
                    </a>
                    <a href="<?php echo function_exists('getSiteUrl') ? getSiteUrl('services') : '/services'; ?>" class="btn btn-outline-primary btn-lg hover-scale">
                        <i class="fas fa-cogs me-2"></i>Наші послуги
                    </a>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 mt-4 mt-lg-0" data-aos="fade-left">
                <div class="position-relative text-center">
                    <img src="https://via.placeholder.com/600x400/667eea/ffffff?text=AdBoard+Pro" 
                         alt="AdBoard Pro" 
                         class="img-fluid rounded shadow-lg animate-float w-100" 
                         style="max-width: 500px;">
                    <div class="position-absolute top-0 start-0 w-100 h-100 gradient-bg rounded" style="opacity: 0.1;"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section py-5">
    <div class="container-fluid px-3 px-md-4">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="display-5 display-md-4 fw-bold mb-3">Чому обирають нас?</h2>
            <p class="lead text-muted fs-5">Переваги роботи з AdBoard Pro</p>
        </div>
        
        <div class="row g-3 g-md-4">
            <div class="col-lg-4 col-md-6 col-sm-12" data-aos="fade-up" data-aos-delay="100">
                <div class="card h-100 border-0 shadow-sm hover-scale">
                    <div class="card-body text-center p-3 p-md-4">
                        <div class="gradient-bg rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                             style="width: 60px; height: 60px;">
                            <i class="fas fa-rocket fa-lg text-white"></i>
                        </div>
                        <h5 class="card-title fw-bold fs-6 fs-md-5">Швидкі результати</h5>
                        <p class="card-text text-muted small">
                            Ваші оголошення будуть опубліковані миттєво та отримають максимальну видимість
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 col-sm-12" data-aos="fade-up" data-aos-delay="200">
                <div class="card h-100 border-0 shadow-sm hover-scale">
                    <div class="card-body text-center p-3 p-md-4">
                        <div class="gradient-bg rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                             style="width: 60px; height: 60px;">
                            <i class="fas fa-users fa-lg text-white"></i>
                        </div>
                        <h5 class="card-title fw-bold fs-6 fs-md-5">Широка аудиторія</h5>
                        <p class="card-text text-muted small">
                            Тисячі активних користувачів щодня шукають саме те, що ви пропонуєте
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 col-sm-12" data-aos="fade-up" data-aos-delay="300">
                <div class="card h-100 border-0 shadow-sm hover-scale">
                    <div class="card-body text-center p-3 p-md-4">
                        <div class="gradient-bg rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                             style="width: 60px; height: 60px;">
                            <i class="fas fa-shield-alt fa-lg text-white"></i>
                        </div>
                        <h5 class="card-title fw-bold fs-6 fs-md-5">Безпека та надійність</h5>
                        <p class="card-text text-muted small">
                            Всі оголошення проходять модерацію, гарантуючи високу якість контенту
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 col-sm-12" data-aos="fade-up" data-aos-delay="400">
                <div class="card h-100 border-0 shadow-sm hover-scale">
                    <div class="card-body text-center p-3 p-md-4">
                        <div class="gradient-bg rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                             style="width: 60px; height: 60px;">
                            <i class="fas fa-chart-line fa-lg text-white"></i>
                        </div>
                        <h5 class="card-title fw-bold fs-6 fs-md-5">Аналітика та статистика</h5>
                        <p class="card-text text-muted small">
                            Детальна статистика переглядів та взаємодій з вашими оголошеннями
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 col-sm-12" data-aos="fade-up" data-aos-delay="500">
                <div class="card h-100 border-0 shadow-sm hover-scale">
                    <div class="card-body text-center p-3 p-md-4">
                        <div class="gradient-bg rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                             style="width: 60px; height: 60px;">
                            <i class="fas fa-mobile-alt fa-lg text-white"></i>
                        </div>
                        <h5 class="card-title fw-bold fs-6 fs-md-5">Мобільна оптимізація</h5>
                        <p class="card-text text-muted small">
                            Сайт відмінно працює на всіх пристроях - від смартфонів до комп'ютерів
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 col-sm-12" data-aos="fade-up" data-aos-delay="600">
                <div class="card h-100 border-0 shadow-sm hover-scale">
                    <div class="card-body text-center p-3 p-md-4">
                        <div class="gradient-bg rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                             style="width: 60px; height: 60px;">
                            <i class="fas fa-headset fa-lg text-white"></i>
                        </div>
                        <h5 class="card-title fw-bold fs-6 fs-md-5">Підтримка 24/7</h5>
                        <p class="card-text text-muted small">
                            Наша команда підтримки завжди готова допомогти вам у будь-який час
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services-section py-5 bg-light">
    <div class="container-fluid px-3 px-md-4">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="display-5 display-md-4 fw-bold mb-3">Наші послуги</h2>
            <p class="lead text-muted fs-5">Комплексні рішення для вашого бізнесу</p>
        </div>
        
        <div class="row g-3 g-md-4">
            <div class="col-lg-6 col-md-12" data-aos="fade-right">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex align-items-center mb-3 flex-column flex-sm-row text-center text-sm-start">
                            <div class="gradient-bg rounded-circle d-flex align-items-center justify-content-center me-0 me-sm-3 mb-2 mb-sm-0" 
                                 style="width: 50px; height: 50px;">
                                <i class="fas fa-bullhorn text-white"></i>
                            </div>
                            <h4 class="mb-0 fw-bold fs-5 fs-md-4">Дошка оголошень</h4>
                        </div>
                        <p class="text-muted mb-3 small">
                            Безкоштовне розміщення оголошень у різних категоріях. 
                            Простий інтерфейс, швидка публікація, широка аудиторія.
                        </p>
                        <ul class="list-unstyled small">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Безлімітна кількість оголошень</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Завантаження фото та відео</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Геолокація та карти</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Соціальні мережі інтеграція</li>
                        </ul>
                        <div class="text-center text-sm-start">
                            <a href="<?php echo function_exists('getSiteUrl') ? getSiteUrl('ads') : '/ads'; ?>" class="btn gradient-bg text-white btn-sm">
                                Переглянути оголошення <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 col-md-12" data-aos="fade-left">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex align-items-center mb-3 flex-column flex-sm-row text-center text-sm-start">
                            <div class="gradient-bg rounded-circle d-flex align-items-center justify-content-center me-0 me-sm-3 mb-2 mb-sm-0" 
                                 style="width: 50px; height: 50px;">
                                <i class="fas fa-ad text-white"></i>
                            </div>
                            <h4 class="mb-0 fw-bold fs-5 fs-md-4">Рекламні послуги</h4>
                        </div>
                        <p class="text-muted mb-3 small">
                            Професійне просування вашого бізнесу. Створення та запуск 
                            ефективних рекламних кампаній у різних каналах.
                        </p>
                        <ul class="list-unstyled small">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Контекстна реклама</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>SMM просування</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>SEO оптимізація</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Медійна реклама</li>
                        </ul>
                        <div class="text-center text-sm-start">
                            <a href="<?php echo function_exists('getSiteUrl') ? getSiteUrl('services') : '/services'; ?>" class="btn gradient-bg text-white btn-sm">
                                Дізнатися більше <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="stats-section py-5">
    <div class="container-fluid px-3 px-md-4">
        <div class="row text-center g-3 g-md-4">
            <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-item">
                    <div class="gradient-bg rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 60px; height: 60px;">
                        <i class="fas fa-users fa-lg text-white"></i>
                    </div>
                    <h3 class="fw-bold text-gradient counter fs-4" data-target="25000">0</h3>
                    <p class="text-muted small">Активних користувачів</p>
                </div>
            </div>
            
            <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-item">
                    <div class="gradient-bg rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 60px; height: 60px;">
                        <i class="fas fa-bullhorn fa-lg text-white"></i>
                    </div>
                    <h3 class="fw-bold text-gradient counter fs-4" data-target="150000">0</h3>
                    <p class="text-muted small">Оголошень опубліковано</p>
                </div>
            </div>
            
            <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-item">
                    <div class="gradient-bg rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 60px; height: 60px;">
                        <i class="fas fa-handshake fa-lg text-white"></i>
                    </div>
                    <h3 class="fw-bold text-gradient counter fs-4" data-target="89000">0</h3>
                    <p class="text-muted small">Успішних угод</p>
                </div>
            </div>
            
            <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="400">
                <div class="stat-item">
                    <div class="gradient-bg rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 60px; height: 60px;">
                        <i class="fas fa-star fa-lg text-white"></i>
                    </div>
                    <h3 class="fw-bold text-gradient fs-4">4.9</h3>
                    <p class="text-muted small">Рейтинг сервісу</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section py-5">
    <div class="gradient-bg">
        <div class="container-fluid px-3 px-md-4">
            <div class="row align-items-center g-4">
                <div class="col-lg-8 col-md-12 text-center text-lg-start" data-aos="fade-right">
                    <h2 class="display-6 display-md-5 fw-bold text-white mb-3">
                        Готові розпочати?
                    </h2>
                    <p class="lead text-white opacity-75 mb-0 fs-6">
                        Приєднуйтесь до тисяч задоволених клієнтів та розвивайте свій бізнес разом з нами!
                    </p>
                </div>
                <div class="col-lg-4 col-md-12 text-center text-lg-end" data-aos="fade-left">
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center justify-content-lg-end">
                        <a href="<?php echo function_exists('getSiteUrl') ? getSiteUrl('pages/register.php') : '/pages/register.php'; ?>" class="btn btn-light btn-lg hover-scale">
                            <i class="fas fa-user-plus me-2"></i>Реєстрація
                        </a>
                        <a href="<?php echo function_exists('getSiteUrl') ? getSiteUrl('contact') : '/contact'; ?>" class="btn btn-outline-light btn-lg hover-scale">
                            <i class="fas fa-envelope me-2"></i>Контакти
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Додаткові стилі для мобільних -->
<style>
/* Adaptive hero section */
@media (max-width: 576px) {
    .display-4 {
        font-size: 2rem !important;
    }
    
    .display-5 {
        font-size: 1.8rem !important;
    }
    
    .lead {
        font-size: 1.1rem !important;
    }
    
    .btn-lg {
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
    }
    
    .hero-section .row {
        text-align: center;
    }
}

/* Responsive cards */
@media (max-width: 768px) {
    .card-body {
        padding: 1rem !important;
    }
    
    .gradient-bg.rounded-circle {
        width: 50px !important;
        height: 50px !important;
    }
    
    .gradient-bg.rounded-circle i {
        font-size: 1rem !important;
    }
}

/* Touch friendly buttons */
@media (max-width: 576px) {
    .btn {
        width: 100%;
        margin-bottom: 10px;
    }
    
    .d-flex.gap-3 {
        flex-direction: column !important;
    }
}

/* Responsive statistics */
@media (max-width: 576px) {
    .stats-section .col-6 {
        margin-bottom: 2rem;
    }
    
    .counter {
        font-size: 1.5rem !important;
    }
}
</style>

<script>
// Анімація лічильників з перевіркою jQuery
document.addEventListener('DOMContentLoaded', function() {
    // Fallback анімація без jQuery
    function animateCounter(element) {
        const target = parseInt(element.dataset.target);
        const increment = target / 100;
        let current = 0;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            element.textContent = Math.ceil(current).toLocaleString();
        }, 20);
    }
    
    // Запуск анімації при появі елементів у viewport
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                observer.unobserve(entry.target);
            }
        });
    });
    
    document.querySelectorAll('.counter').forEach(counter => {
        observer.observe(counter);
    });
});

// Покращення hover ефектів на мобільних
if ('ontouchstart' in window) {
    document.querySelectorAll('.hover-scale').forEach(element => {
        element.addEventListener('touchstart', function() {
            this.style.transform = 'scale(1.05)';
        });
        
        element.addEventListener('touchend', function() {
            this.style.transform = 'scale(1)';
        });
    });
    
    // Menu item interactions
    const menuItems = document.querySelectorAll('.nav-menu-item');
    menuItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.animation = 'float 2s ease-in-out infinite';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.animation = '';
        });
        
        // Ripple effect on click
        item.addEventListener('click', function(e) {
            const ripple = document.createElement('div');
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.background = 'rgba(255, 255, 255, 0.3)';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'ripple 0.6s linear';
            ripple.style.left = (e.clientX - this.offsetLeft - 25) + 'px';
            ripple.style.top = (e.clientY - this.offsetTop - 25) + 'px';
            ripple.style.width = '50px';
            ripple.style.height = '50px';
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
}
</script>

<style>
/* Modern Navigation Menu */
.modern-nav-menu {
    background: var(--theme-bg);
    position: relative;
    z-index: 3;
}

.nav-menu-container {
    position: relative;
}

.nav-menu-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.nav-menu-item {
    background: var(--theme-bg-secondary);
    border: 2px solid var(--theme-border);
    border-radius: 15px;
    padding: 20px;
    text-decoration: none;
    color: var(--theme-text);
    position: relative;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    backdrop-filter: blur(10px);
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    cursor: pointer;
}

.nav-menu-item:hover {
    color: var(--theme-text);
    text-decoration: none;
    transform: translateY(-8px) scale(1.02);
    border-color: var(--theme-accent);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.nav-menu-item.featured {
    background: linear-gradient(135deg, rgba(76, 175, 80, 0.1), rgba(139, 195, 74, 0.1));
    border-color: rgba(76, 175, 80, 0.3);
}

.nav-menu-item.featured:hover {
    background: linear-gradient(135deg, rgba(76, 175, 80, 0.2), rgba(139, 195, 74, 0.2));
    border-color: #4caf50;
}

.menu-item-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: var(--current-gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 15px;
    font-size: 1.4rem;
    color: white;
    transition: all 0.3s ease;
    position: relative;
    z-index: 2;
}

.nav-menu-item:hover .menu-item-icon {
    transform: scale(1.1) rotate(5deg);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
}

.menu-item-content {
    position: relative;
    z-index: 2;
}

.menu-item-content h5 {
    margin: 0 0 5px 0;
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--theme-text);
    transition: all 0.3s ease;
}

.menu-item-content p {
    margin: 0;
    font-size: 0.85rem;
    color: var(--theme-muted);
    transition: all 0.3s ease;
}

.nav-menu-item:hover .menu-item-content h5 {
    color: var(--theme-accent);
}

.menu-item-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #4caf50;
    color: white;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
    z-index: 3;
    transform: scale(0.9);
    transition: all 0.3s ease;
}

.menu-item-badge.hot {
    background: linear-gradient(135deg, #ff6b6b, #ee5a24);
    animation: pulse 2s infinite;
}

.menu-item-badge.premium {
    background: linear-gradient(135deg, #f39c12, #e67e22);
    border-radius: 50%;
    width: 25px;
    height: 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
}

.nav-menu-item:hover .menu-item-badge {
    transform: scale(1.1);
}

.menu-item-decoration {
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    transition: all 0.6s ease;
    z-index: 1;
}

.nav-menu-item:hover .menu-item-decoration {
    left: 100%;
}

/* Animations */
@keyframes pulse {
    0%, 100% {
        transform: scale(0.9);
        opacity: 0.8;
    }
    50% {
        transform: scale(1.1);
        opacity: 1;
    }
}

@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-5px);
    }
}

@keyframes ripple {
    to {
        transform: scale(4);
        opacity: 0;
    }
}

.nav-menu-item {
    animation: slideInUp 0.6s ease-out forwards;
}

.nav-menu-item:nth-child(1) { animation-delay: 0.1s; }
.nav-menu-item:nth-child(2) { animation-delay: 0.2s; }
.nav-menu-item:nth-child(3) { animation-delay: 0.3s; }
.nav-menu-item:nth-child(4) { animation-delay: 0.4s; }
.nav-menu-item:nth-child(5) { animation-delay: 0.5s; }
.nav-menu-item:nth-child(6) { animation-delay: 0.6s; }

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Hover effects for different categories */
.nav-menu-item[data-category="ads"]:hover {
    background: linear-gradient(135deg, rgba(33, 150, 243, 0.1), rgba(3, 169, 244, 0.1));
}

.nav-menu-item[data-category="services"]:hover {
    background: linear-gradient(135deg, rgba(156, 39, 176, 0.1), rgba(233, 30, 99, 0.1));
}

.nav-menu-item[data-category="categories"]:hover {
    background: linear-gradient(135deg, rgba(255, 152, 0, 0.1), rgba(255, 193, 7, 0.1));
}

.nav-menu-item[data-category="about"]:hover {
    background: linear-gradient(135deg, rgba(96, 125, 139, 0.1), rgba(120, 144, 156, 0.1));
}

.nav-menu-item[data-category="contact"]:hover {
    background: linear-gradient(135deg, rgba(76, 175, 80, 0.1), rgba(139, 195, 74, 0.1));
}

/* Tablet Responsive */
@media (max-width: 768px) {
    .nav-menu-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
    }
    
    .nav-menu-item {
        padding: 15px;
    }
    
    .menu-item-icon {
        width: 50px;
        height: 50px;
        font-size: 1.2rem;
    }
    
    .menu-item-content h5 {
        font-size: 1rem;
    }
    
    .menu-item-content p {
        font-size: 0.8rem;
    }
}

/* Mobile Responsive */
@media (max-width: 576px) {
    .nav-menu-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
    
    .nav-menu-item {
        padding: 15px;
    }
    
    .menu-item-content h5 {
        font-size: 0.9rem;
    }
    
    .menu-item-content p {
        font-size: 0.7rem;
    }
    
    .menu-item-icon {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
}
</style>
