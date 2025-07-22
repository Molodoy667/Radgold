<?php
require_once '../core/config.php';
require_once '../core/functions.php';

$pageTitle = 'Послуги рекламної компанії - AdBoard Pro';
$pageDescription = 'Професійні рекламні послуги: SMM просування, SEO оптимізація, створення сайтів, дизайн та брендинг. Ефективне просування вашого бізнесу.';
$pageKeywords = 'SMM, SEO, створення сайтів, дизайн, брендинг, реклама, просування, маркетинг';

include '../themes/header.php';
?>

<div class="services-page">
    <!-- Hero Section -->
    <section class="hero-section py-5 position-relative overflow-hidden">
        <div class="hero-bg"></div>
        <div class="container position-relative">
            <div class="row align-items-center min-vh-50">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h1 class="display-4 fw-bold mb-4 animate__animated animate__fadeInLeft">
                        Професійні <span class="text-gradient">рекламні послуги</span>
                    </h1>
                    <p class="lead mb-4 animate__animated animate__fadeInLeft animate__delay-1s">
                        Комплексний підхід до просування вашого бізнесу. SMM, SEO, створення сайтів та дизайн від експертів з багаторічним досвідом.
                    </p>
                    <div class="hero-actions animate__animated animate__fadeInLeft animate__delay-2s">
                        <a href="#services" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-rocket me-2"></i>Наші послуги
                        </a>
                        <a href="#consultation" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-phone me-2"></i>Консультація
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image text-center animate__animated animate__fadeInRight">
                        <div class="floating-elements">
                            <div class="floating-card" data-aos="fade-up" data-aos-delay="100">
                                <i class="fab fa-instagram text-gradient"></i>
                                <span>SMM</span>
                            </div>
                            <div class="floating-card" data-aos="fade-up" data-aos-delay="200">
                                <i class="fas fa-search text-gradient"></i>
                                <span>SEO</span>
                            </div>
                            <div class="floating-card" data-aos="fade-up" data-aos-delay="300">
                                <i class="fas fa-laptop-code text-gradient"></i>
                                <span>WEB</span>
                            </div>
                            <div class="floating-card" data-aos="fade-up" data-aos-delay="400">
                                <i class="fas fa-palette text-gradient"></i>
                                <span>DESIGN</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Grid -->
    <section id="services" class="services-grid py-5">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <h2 class="display-5 fw-bold mb-3" data-aos="fade-up">Наші послуги</h2>
                    <p class="lead text-muted" data-aos="fade-up" data-aos-delay="100">
                        Повний спектр цифрового маркетингу для вашого успіху
                    </p>
                </div>
            </div>

            <div class="row g-4">
                <!-- SMM Service -->
                <div class="col-lg-6 col-md-12" data-aos="fade-up" data-aos-delay="100">
                    <div class="service-card card border-0 h-100 shadow-hover">
                        <div class="card-body p-4">
                            <div class="service-icon mb-4">
                                <div class="icon-wrapper gradient-1">
                                    <i class="fab fa-instagram fa-2x"></i>
                                </div>
                            </div>
                            <h3 class="h4 fw-bold mb-3">SMM Просування</h3>
                            <p class="text-muted mb-4">
                                Професійне ведення соціальних мереж, створення контенту та реклама в Instagram, Facebook, TikTok, Telegram.
                            </p>
                            <div class="platforms mb-4">
                                <span class="platform-badge instagram">
                                    <i class="fab fa-instagram me-1"></i>Instagram
                                </span>
                                <span class="platform-badge facebook">
                                    <i class="fab fa-facebook me-1"></i>Facebook
                                </span>
                                <span class="platform-badge tiktok">
                                    <i class="fab fa-tiktok me-1"></i>TikTok
                                </span>
                                <span class="platform-badge telegram">
                                    <i class="fab fa-telegram me-1"></i>Telegram
                                </span>
                            </div>
                            <div class="service-features mb-4">
                                <div class="feature">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Контент-план та створення постів
                                </div>
                                <div class="feature">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Налаштування та ведення реклами
                                </div>
                                <div class="feature">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Аналітика та звітність
                                </div>
                                <div class="feature">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Дизайн сторіс та постів
                                </div>
                            </div>
                            <div class="service-pricing mb-4">
                                <div class="price-range">
                                    <span class="price-from">від 5,000 грн/міс</span>
                                </div>
                            </div>
                            <div class="service-actions">
                                <a href="services/smm.php" class="btn btn-primary w-100 mb-2">
                                    <i class="fas fa-info-circle me-2"></i>Детальніше
                                </a>
                                <button class="btn btn-outline-primary w-100" onclick="orderService('smm')">
                                    <i class="fas fa-shopping-cart me-2"></i>Замовити
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SEO Service -->
                <div class="col-lg-6 col-md-12" data-aos="fade-up" data-aos-delay="200">
                    <div class="service-card card border-0 h-100 shadow-hover">
                        <div class="card-body p-4">
                            <div class="service-icon mb-4">
                                <div class="icon-wrapper gradient-2">
                                    <i class="fas fa-search fa-2x"></i>
                                </div>
                            </div>
                            <h3 class="h4 fw-bold mb-3">SEO Просування</h3>
                            <p class="text-muted mb-4">
                                Комплексна SEO оптимізація сайту для підвищення позицій в пошукових системах Google та Bing.
                            </p>
                            <div class="seo-types mb-4">
                                <span class="seo-badge">
                                    <i class="fas fa-cogs me-1"></i>Технічний аудит
                                </span>
                                <span class="seo-badge">
                                    <i class="fas fa-file-alt me-1"></i>Контент SEO
                                </span>
                                <span class="seo-badge">
                                    <i class="fas fa-link me-1"></i>Лінкбілдинг
                                </span>
                            </div>
                            <div class="service-features mb-4">
                                <div class="feature">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Повний аудит сайту
                                </div>
                                <div class="feature">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Оптимізація контенту та мета-тегів
                                </div>
                                <div class="feature">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Технічна оптимізація
                                </div>
                                <div class="feature">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Збір якісних посилань
                                </div>
                            </div>
                            <div class="service-pricing mb-4">
                                <div class="price-range">
                                    <span class="price-from">від 8,000 грн/міс</span>
                                </div>
                            </div>
                            <div class="service-actions">
                                <a href="services/seo.php" class="btn btn-primary w-100 mb-2">
                                    <i class="fas fa-info-circle me-2"></i>Детальніше
                                </a>
                                <button class="btn btn-outline-primary w-100" onclick="orderService('seo')">
                                    <i class="fas fa-shopping-cart me-2"></i>Замовити
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Web Development Service -->
                <div class="col-lg-6 col-md-12" data-aos="fade-up" data-aos-delay="300">
                    <div class="service-card card border-0 h-100 shadow-hover">
                        <div class="card-body p-4">
                            <div class="service-icon mb-4">
                                <div class="icon-wrapper gradient-3">
                                    <i class="fas fa-laptop-code fa-2x"></i>
                                </div>
                            </div>
                            <h3 class="h4 fw-bold mb-3">Створення сайтів</h3>
                            <p class="text-muted mb-4">
                                Розробка сучасних сайтів: лендінги, корпоративні сайти, інтернет-магазини, блог-платформи.
                            </p>
                            <div class="website-types mb-4">
                                <span class="website-badge">
                                    <i class="fas fa-rocket me-1"></i>Лендінги
                                </span>
                                <span class="website-badge">
                                    <i class="fas fa-store me-1"></i>Магазини
                                </span>
                                <span class="website-badge">
                                    <i class="fas fa-building me-1"></i>Корпоративні
                                </span>
                                <span class="website-badge">
                                    <i class="fas fa-blog me-1"></i>Блоги
                                </span>
                            </div>
                            <div class="service-features mb-4">
                                <div class="feature">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Адаптивний дизайн
                                </div>
                                <div class="feature">
                                    <i class="fas fa-check text-success me-2"></i>
                                    SEO оптимізація
                                </div>
                                <div class="feature">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Швидкість завантаження
                                </div>
                                <div class="feature">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Система управління
                                </div>
                            </div>
                            <div class="service-pricing mb-4">
                                <div class="price-range">
                                    <span class="price-from">від 15,000 грн</span>
                                </div>
                            </div>
                            <div class="service-actions">
                                <a href="services/web-development.php" class="btn btn-primary w-100 mb-2">
                                    <i class="fas fa-info-circle me-2"></i>Детальніше
                                </a>
                                <button class="btn btn-outline-primary w-100" onclick="orderService('web')">
                                    <i class="fas fa-shopping-cart me-2"></i>Замовити
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Design Service -->
                <div class="col-lg-6 col-md-12" data-aos="fade-up" data-aos-delay="400">
                    <div class="service-card card border-0 h-100 shadow-hover">
                        <div class="card-body p-4">
                            <div class="service-icon mb-4">
                                <div class="icon-wrapper gradient-4">
                                    <i class="fas fa-palette fa-2x"></i>
                                </div>
                            </div>
                            <h3 class="h4 fw-bold mb-3">Дизайн та брендинг</h3>
                            <p class="text-muted mb-4">
                                Створення фірмового стилю, логотипів, банерів, презентацій та повного брендбуку для вашого бізнесу.
                            </p>
                            <div class="design-types mb-4">
                                <span class="design-badge">
                                    <i class="fas fa-crown me-1"></i>Логотипи
                                </span>
                                <span class="design-badge">
                                    <i class="fas fa-image me-1"></i>Банери
                                </span>
                                <span class="design-badge">
                                    <i class="fas fa-presentation me-1"></i>Презентації
                                </span>
                                <span class="design-badge">
                                    <i class="fas fa-id-card me-1"></i>Візитки
                                </span>
                            </div>
                            <div class="service-features mb-4">
                                <div class="feature">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Унікальний дизайн
                                </div>
                                <div class="feature">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Фірмовий стиль
                                </div>
                                <div class="feature">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Векторні формати
                                </div>
                                <div class="feature">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Брендбук
                                </div>
                            </div>
                            <div class="service-pricing mb-4">
                                <div class="price-range">
                                    <span class="price-from">від 3,000 грн</span>
                                </div>
                            </div>
                            <div class="service-actions">
                                <a href="services/design.php" class="btn btn-primary w-100 mb-2">
                                    <i class="fas fa-info-circle me-2"></i>Детальніше
                                </a>
                                <button class="btn btn-outline-primary w-100" onclick="orderService('design')">
                                    <i class="fas fa-shopping-cart me-2"></i>Замовити
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Process Section -->
    <section class="process-section py-5 bg-light">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <h2 class="display-5 fw-bold mb-3" data-aos="fade-up">Як ми працюємо</h2>
                    <p class="lead text-muted" data-aos="fade-up" data-aos-delay="100">
                        Простий та зрозумілий процес роботи
                    </p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="process-step text-center">
                        <div class="step-number">1</div>
                        <div class="step-icon mb-3">
                            <i class="fas fa-comments fa-2x text-primary"></i>
                        </div>
                        <h4 class="h5 fw-bold mb-2">Консультація</h4>
                        <p class="text-muted">
                            Обговорюємо ваші цілі, потреби та бюджет. Безкоштовна консультація з експертом.
                        </p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="process-step text-center">
                        <div class="step-number">2</div>
                        <div class="step-icon mb-3">
                            <i class="fas fa-file-contract fa-2x text-primary"></i>
                        </div>
                        <h4 class="h5 fw-bold mb-2">Бриф</h4>
                        <p class="text-muted">
                            Заповнюємо детальний бриф для розуміння специфіки вашого бізнесу.
                        </p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="process-step text-center">
                        <div class="step-number">3</div>
                        <div class="step-icon mb-3">
                            <i class="fas fa-rocket fa-2x text-primary"></i>
                        </div>
                        <h4 class="h5 fw-bold mb-2">Реалізація</h4>
                        <p class="text-muted">
                            Виконуємо роботу згідно затвердженого плану з регулярним звітуванням.
                        </p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="process-step text-center">
                        <div class="step-number">4</div>
                        <div class="step-icon mb-3">
                            <i class="fas fa-chart-line fa-2x text-primary"></i>
                        </div>
                        <h4 class="h5 fw-bold mb-2">Результат</h4>
                        <p class="text-muted">
                            Отримуєте готовий результат та детальну аналітику ефективності.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tools Section -->
    <section class="tools-section py-5">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <h2 class="display-5 fw-bold mb-3" data-aos="fade-up">Корисні інструменти</h2>
                    <p class="lead text-muted" data-aos="fade-up" data-aos-delay="100">
                        Безкоштовні інструменти для вашого бізнесу
                    </p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="tool-card card border-0 h-100 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="tool-icon mb-3">
                                <i class="fas fa-calculator fa-3x text-primary"></i>
                            </div>
                            <h4 class="h5 fw-bold mb-2">Калькулятор вартості</h4>
                            <p class="text-muted mb-3">
                                Розрахуйте вартість послуг онлайн
                            </p>
                            <a href="tools/calculator.php" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-right me-2"></i>Скористатися
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="tool-card card border-0 h-100 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="tool-icon mb-3">
                                <i class="fas fa-tachometer-alt fa-3x text-primary"></i>
                            </div>
                            <h4 class="h5 fw-bold mb-2">Аналіз сайту</h4>
                            <p class="text-muted mb-3">
                                Перевірте швидкість та SEO сайту
                            </p>
                            <a href="tools/site-analyzer.php" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-right me-2"></i>Перевірити
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="tool-card card border-0 h-100 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="tool-icon mb-3">
                                <i class="fas fa-tags fa-3x text-primary"></i>
                            </div>
                            <h4 class="h5 fw-bold mb-2">Генератор мета-тегів</h4>
                            <p class="text-muted mb-3">
                                Створіть SEO теги для сайту
                            </p>
                            <a href="tools/meta-generator.php" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-right me-2"></i>Генерувати
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Consultation Section -->
    <section id="consultation" class="consultation-section py-5 bg-gradient">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h2 class="display-5 fw-bold text-white mb-3" data-aos="fade-right">
                        Безкоштовна консультація
                    </h2>
                    <p class="lead text-white-50 mb-4" data-aos="fade-right" data-aos-delay="100">
                        Обговоримо ваш проект та запропонуємо найкращі рішення для вашого бізнесу.
                    </p>
                    <div class="consultation-benefits" data-aos="fade-right" data-aos-delay="200">
                        <div class="benefit">
                            <i class="fas fa-check-circle text-white me-2"></i>
                            <span class="text-white">Аналіз конкурентів</span>
                        </div>
                        <div class="benefit">
                            <i class="fas fa-check-circle text-white me-2"></i>
                            <span class="text-white">Стратегія просування</span>
                        </div>
                        <div class="benefit">
                            <i class="fas fa-check-circle text-white me-2"></i>
                            <span class="text-white">Розрахунок бюджету</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="consultation-form" data-aos="fade-left">
                        <div class="card border-0 shadow-lg">
                            <div class="card-body p-4">
                                <h4 class="fw-bold mb-3">Замовити консультацію</h4>
                                <form id="consultationForm">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="name" placeholder="Ваше ім'я" required>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="tel" class="form-control" name="phone" placeholder="Телефон" required>
                                        </div>
                                        <div class="col-12">
                                            <input type="email" class="form-control" name="email" placeholder="Email" required>
                                        </div>
                                        <div class="col-12">
                                            <select class="form-select" name="service" required>
                                                <option value="">Оберіть послугу</option>
                                                <option value="smm">SMM просування</option>
                                                <option value="seo">SEO оптимізація</option>
                                                <option value="web">Створення сайту</option>
                                                <option value="design">Дизайн та брендинг</option>
                                                <option value="complex">Комплексне просування</option>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <textarea class="form-control" name="message" rows="3" placeholder="Опишіть ваш проект"></textarea>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="fas fa-paper-plane me-2"></i>Відправити заявку
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
.hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.hero-bg {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><polygon fill="rgba(255,255,255,0.1)" points="0,1000 1000,0 1000,1000"/></svg>');
    background-size: cover;
}

.floating-elements {
    position: relative;
    height: 400px;
}

.floating-card {
    position: absolute;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    padding: 20px;
    text-align: center;
    color: white;
    animation: float 6s ease-in-out infinite;
}

.floating-card:nth-child(1) {
    top: 50px;
    left: 50px;
    animation-delay: 0s;
}

.floating-card:nth-child(2) {
    top: 120px;
    right: 80px;
    animation-delay: 1.5s;
}

.floating-card:nth-child(3) {
    bottom: 120px;
    left: 20px;
    animation-delay: 3s;
}

.floating-card:nth-child(4) {
    bottom: 50px;
    right: 50px;
    animation-delay: 4.5s;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-20px);
    }
}

.service-card {
    transition: all 0.3s ease;
    border-radius: 20px;
    overflow: hidden;
}

.service-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important;
}

.icon-wrapper {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.gradient-1 { background: linear-gradient(45deg, #ff6b6b, #ee5a24); }
.gradient-2 { background: linear-gradient(45deg, #4834d4, #686de0); }
.gradient-3 { background: linear-gradient(45deg, #00d2d3, #54a0ff); }
.gradient-4 { background: linear-gradient(45deg, #ff9ff3, #f368e0); }

.platform-badge, .seo-badge, .website-badge, .design-badge {
    display: inline-block;
    background: rgba(102, 126, 234, 0.1);
    color: #667eea;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.875rem;
    margin: 2px;
    border: 1px solid rgba(102, 126, 234, 0.2);
}

.feature {
    margin-bottom: 8px;
    font-size: 0.9rem;
}

.price-from {
    font-size: 1.2rem;
    font-weight: 600;
    color: #28a745;
}

.process-step {
    position: relative;
    padding: 30px 20px;
}

.step-number {
    position: absolute;
    top: -15px;
    left: 50%;
    transform: translateX(-50%);
    width: 40px;
    height: 40px;
    background: linear-gradient(45deg, #667eea, #764ba2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 1.2rem;
}

.tool-card {
    transition: all 0.3s ease;
}

.tool-card:hover {
    transform: translateY(-5px);
}

.consultation-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.benefit {
    margin-bottom: 10px;
}

.shadow-hover {
    transition: all 0.3s ease;
}

.shadow-hover:hover {
    box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize AOS
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true
    });
    
    // Consultation form handler
    document.getElementById('consultationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        submitConsultationForm();
    });
});

function orderService(serviceType) {
    if (!isLoggedIn()) {
        window.location.href = '/user/login.php?return=' + encodeURIComponent(window.location.href);
        return;
    }
    
    // Redirect to order page with service type
    window.location.href = `/services/order.php?service=${serviceType}`;
}

function submitConsultationForm() {
    const form = document.getElementById('consultationForm');
    const formData = new FormData(form);
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Відправляємо...';
    submitBtn.disabled = true;
    
    fetch('/ajax/consultation.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showNotification('Заявку відправлено! Ми зв\'яжемося з вами найближчим часом.', 'success');
            form.reset();
        } else {
            showNotification('Помилка: ' + data.error, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Виникла помилка при відправці заявки', 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close ms-auto" onclick="this.parentElement.remove()"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

function isLoggedIn() {
    // Check if user is logged in (you can implement this based on your auth system)
    return <?php echo isLoggedIn() ? 'true' : 'false'; ?>;
}
</script>

<?php include '../themes/footer.php'; ?>