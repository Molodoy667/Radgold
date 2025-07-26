<?php
require_once '../../core/config.php';
require_once '../../core/functions.php';

$pageTitle = 'Калькулятор вартості послуг - AdBoard Pro';
$pageDescription = 'Розрахуйте вартість рекламних послуг онлайн: SMM, SEO, створення сайтів, дизайн. Миттєвий розрахунок з детальним прайсом.';
$pageKeywords = 'калькулятор вартості, ціни на послуги, розрахунок SMM, SEO ціни, вартість сайту';

include '../../themes/header.php';
?>

<div class="calculator-page">
    <!-- Header -->
    <section class="page-header py-5 bg-gradient">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center text-white">
                    <h1 class="display-4 fw-bold mb-3" data-aos="fade-up">
                        <i class="fas fa-calculator me-3"></i>Калькулятор вартості
                    </h1>
                    <p class="lead mb-0" data-aos="fade-up" data-aos-delay="100">
                        Розрахуйте вартість наших послуг миттєво та безкоштовно
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Calculator -->
    <section class="calculator-section py-5">
        <div class="container">
            <div class="row">
                <!-- Calculator Form -->
                <div class="col-lg-8 mb-4">
                    <div class="calculator-form card border-0 shadow-lg">
                        <div class="card-header bg-transparent">
                            <h3 class="h4 fw-bold mb-0">
                                <i class="fas fa-cogs me-2 text-primary"></i>Конфігуратор послуг
                            </h3>
                        </div>
                        <div class="card-body p-4">
                            <form id="calculatorForm">
                                <!-- Service Type Selection -->
                                <div class="service-selection mb-4">
                                    <h5 class="fw-bold mb-3">1. Оберіть тип послуги</h5>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="service-option">
                                                <input type="radio" class="btn-check" name="serviceType" id="smm" value="smm">
                                                <label class="btn btn-outline-primary w-100 p-3" for="smm">
                                                    <div class="service-icon mb-2">
                                                        <i class="fab fa-instagram fa-2x"></i>
                                                    </div>
                                                    <div class="fw-bold">SMM Просування</div>
                                                    <small class="text-muted">Соціальні мережі</small>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="service-option">
                                                <input type="radio" class="btn-check" name="serviceType" id="seo" value="seo">
                                                <label class="btn btn-outline-primary w-100 p-3" for="seo">
                                                    <div class="service-icon mb-2">
                                                        <i class="fas fa-search fa-2x"></i>
                                                    </div>
                                                    <div class="fw-bold">SEO Оптимізація</div>
                                                    <small class="text-muted">Пошукове просування</small>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="service-option">
                                                <input type="radio" class="btn-check" name="serviceType" id="web" value="web">
                                                <label class="btn btn-outline-primary w-100 p-3" for="web">
                                                    <div class="service-icon mb-2">
                                                        <i class="fas fa-laptop-code fa-2x"></i>
                                                    </div>
                                                    <div class="fw-bold">Створення сайту</div>
                                                    <small class="text-muted">Веб-розробка</small>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="service-option">
                                                <input type="radio" class="btn-check" name="serviceType" id="design" value="design">
                                                <label class="btn btn-outline-primary w-100 p-3" for="design">
                                                    <div class="service-icon mb-2">
                                                        <i class="fas fa-palette fa-2x"></i>
                                                    </div>
                                                    <div class="fw-bold">Дизайн</div>
                                                    <small class="text-muted">Графічний дизайн</small>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Service Options -->
                                <div id="serviceOptions" class="service-options" style="display: none;">
                                    <!-- SMM Options -->
                                    <div id="smmOptions" class="options-group" style="display: none;">
                                        <h5 class="fw-bold mb-3">2. Налаштування SMM</h5>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Кількість платформ</label>
                                                <select class="form-select" name="smmPlatforms" data-price="1500">
                                                    <option value="1" data-price="1500">1 платформа (+1,500 грн)</option>
                                                    <option value="2" data-price="2500">2 платформи (+2,500 грн)</option>
                                                    <option value="3" data-price="3500">3 платформи (+3,500 грн)</option>
                                                    <option value="4" data-price="4000">4+ платформи (+4,000 грн)</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Кількість постів на місяць</label>
                                                <select class="form-select" name="smmPosts" data-price="1000">
                                                    <option value="8" data-price="1000">8-12 постів (+1,000 грн)</option>
                                                    <option value="16" data-price="1500">16-20 постів (+1,500 грн)</option>
                                                    <option value="24" data-price="2000">24-30 постів (+2,000 грн)</option>
                                                    <option value="36" data-price="2500">30+ постів (+2,500 грн)</option>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Додаткові послуги</label>
                                                <div class="row g-2">
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="smmStories" value="500" id="smmStories">
                                                            <label class="form-check-label" for="smmStories">
                                                                Stories (+500 грн/міс)
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="smmAds" value="1000" id="smmAds">
                                                            <label class="form-check-label" for="smmAds">
                                                                Налаштування реклами (+1,000 грн)
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="smmAnalytics" value="300" id="smmAnalytics">
                                                            <label class="form-check-label" for="smmAnalytics">
                                                                Детальна аналітика (+300 грн/міс)
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="smmInfluencer" value="2000" id="smmInfluencer">
                                                            <label class="form-check-label" for="smmInfluencer">
                                                                Робота з блогерами (+2,000 грн)
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- SEO Options -->
                                    <div id="seoOptions" class="options-group" style="display: none;">
                                        <h5 class="fw-bold mb-3">2. Налаштування SEO</h5>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Тип сайту</label>
                                                <select class="form-select" name="seoSiteType" data-price="5000">
                                                    <option value="small" data-price="5000">Малий сайт (до 50 сторінок) (+5,000 грн)</option>
                                                    <option value="medium" data-price="8000">Середній сайт (50-200 сторінок) (+8,000 грн)</option>
                                                    <option value="large" data-price="12000">Великий сайт (200+ сторінок) (+12,000 грн)</option>
                                                    <option value="ecommerce" data-price="15000">Інтернет-магазин (+15,000 грн)</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Конкурентність ніші</label>
                                                <select class="form-select" name="seoCompetition" data-price="0">
                                                    <option value="low" data-price="0">Низька (без доплати)</option>
                                                    <option value="medium" data-price="2000">Середня (+2,000 грн/міс)</option>
                                                    <option value="high" data-price="4000">Висока (+4,000 грн/міс)</option>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Додаткові послуги</label>
                                                <div class="row g-2">
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="seoAudit" value="2000" id="seoAudit">
                                                            <label class="form-check-label" for="seoAudit">
                                                                Технічний аудит (+2,000 грн)
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="seoContent" value="1500" id="seoContent">
                                                            <label class="form-check-label" for="seoContent">
                                                                Створення контенту (+1,500 грн/міс)
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="seoLinks" value="3000" id="seoLinks">
                                                            <label class="form-check-label" for="seoLinks">
                                                                Побудова посилань (+3,000 грн/міс)
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="seoLocal" value="1000" id="seoLocal">
                                                            <label class="form-check-label" for="seoLocal">
                                                                Локальне SEO (+1,000 грн/міс)
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Web Development Options -->
                                    <div id="webOptions" class="options-group" style="display: none;">
                                        <h5 class="fw-bold mb-3">2. Налаштування сайту</h5>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Тип сайту</label>
                                                <select class="form-select" name="webType" data-price="8000">
                                                    <option value="landing" data-price="8000">Лендінг (+8,000 грн)</option>
                                                    <option value="corporate" data-price="15000">Корпоративний сайт (+15,000 грн)</option>
                                                    <option value="ecommerce" data-price="25000">Інтернет-магазин (+25,000 грн)</option>
                                                    <option value="blog" data-price="12000">Блог/Новинний сайт (+12,000 грн)</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Кількість сторінок</label>
                                                <select class="form-select" name="webPages" data-price="0">
                                                    <option value="1-5" data-price="0">1-5 сторінок (включено)</option>
                                                    <option value="6-15" data-price="3000">6-15 сторінок (+3,000 грн)</option>
                                                    <option value="16-30" data-price="6000">16-30 сторінок (+6,000 грн)</option>
                                                    <option value="30+" data-price="10000">30+ сторінок (+10,000 грн)</option>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Додаткові функції</label>
                                                <div class="row g-2">
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="webCMS" value="3000" id="webCMS">
                                                            <label class="form-check-label" for="webCMS">
                                                                CMS панель (+3,000 грн)
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="webMultilang" value="2000" id="webMultilang">
                                                            <label class="form-check-label" for="webMultilang">
                                                                Багатомовність (+2,000 грн)
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="webIntegrations" value="1500" id="webIntegrations">
                                                            <label class="form-check-label" for="webIntegrations">
                                                                API інтеграції (+1,500 грн)
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="webAnimations" value="1000" id="webAnimations">
                                                            <label class="form-check-label" for="webAnimations">
                                                                Анімації (+1,000 грн)
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Design Options -->
                                    <div id="designOptions" class="options-group" style="display: none;">
                                        <h5 class="fw-bold mb-3">2. Налаштування дизайну</h5>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Тип дизайну</label>
                                                <select class="form-select" name="designType" data-price="2000">
                                                    <option value="logo" data-price="2000">Логотип (+2,000 грн)</option>
                                                    <option value="branding" data-price="8000">Повний брендинг (+8,000 грн)</option>
                                                    <option value="banners" data-price="1000">Банери/реклама (+1,000 грн)</option>
                                                    <option value="presentation" data-price="1500">Презентація (+1,500 грн)</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Кількість концептів</label>
                                                <select class="form-select" name="designConcepts" data-price="0">
                                                    <option value="2" data-price="0">2 концепти (включено)</option>
                                                    <option value="3" data-price="500">3 концепти (+500 грн)</option>
                                                    <option value="5" data-price="1000">5 концептів (+1,000 грн)</option>
                                                    <option value="unlimited" data-price="2000">Необмежено (+2,000 грн)</option>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Додаткові послуги</label>
                                                <div class="row g-2">
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="designBrandbook" value="3000" id="designBrandbook">
                                                            <label class="form-check-label" for="designBrandbook">
                                                                Брендбук (+3,000 грн)
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="designBusinessCards" value="500" id="designBusinessCards">
                                                            <label class="form-check-label" for="designBusinessCards">
                                                                Візитки (+500 грн)
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="designPackaging" value="2000" id="designPackaging">
                                                            <label class="form-check-label" for="designPackaging">
                                                                Дизайн упаковки (+2,000 грн)
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="designVector" value="300" id="designVector">
                                                            <label class="form-check-label" for="designVector">
                                                                Векторні формати (+300 грн)
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Duration -->
                                <div id="durationSection" class="duration-section mb-4" style="display: none;">
                                    <h5 class="fw-bold mb-3">3. Термін співпраці</h5>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="duration" value="1" id="duration1" checked>
                                                <label class="form-check-label" for="duration1">
                                                    1 місяць (без знижки)
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="duration" value="3" id="duration3" data-discount="5">
                                                <label class="form-check-label" for="duration3">
                                                    3 місяці (знижка 5%)
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="duration" value="6" id="duration6" data-discount="10">
                                                <label class="form-check-label" for="duration6">
                                                    6 місяців (знижка 10%)
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="duration" value="12" id="duration12" data-discount="15">
                                                <label class="form-check-label" for="duration12">
                                                    12 місяців (знижка 15%)
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Price Summary -->
                <div class="col-lg-4">
                    <div class="price-summary card border-0 shadow-lg sticky-top">
                        <div class="card-header bg-primary text-white">
                            <h4 class="h5 fw-bold mb-0">
                                <i class="fas fa-receipt me-2"></i>Розрахунок вартості
                            </h4>
                        </div>
                        <div class="card-body p-4">
                            <div id="priceBreakdown">
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-calculator fa-3x mb-3"></i>
                                    <p>Оберіть послугу для розрахунку вартості</p>
                                </div>
                            </div>
                            
                            <div id="priceTotal" class="price-total mt-4" style="display: none;">
                                <hr>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold">Підсумок:</span>
                                    <span class="h4 fw-bold text-success" id="totalAmount">0 грн</span>
                                </div>
                                <div id="discountInfo" class="text-muted small mb-3" style="display: none;">
                                    <div class="d-flex justify-content-between">
                                        <span>Знижка:</span>
                                        <span id="discountAmount">0 грн</span>
                                    </div>
                                </div>
                                <div id="monthlyNote" class="text-muted small" style="display: none;">
                                    * Вартість за місяць
                                </div>
                            </div>

                            <div id="actionButtons" class="action-buttons mt-4" style="display: none;">
                                <button type="button" class="btn btn-primary w-100 mb-2" onclick="orderCalculatedService()">
                                    <i class="fas fa-shopping-cart me-2"></i>Замовити послугу
                                </button>
                                <button type="button" class="btn btn-outline-primary w-100" onclick="getConsultation()">
                                    <i class="fas fa-phone me-2"></i>Отримати консультацію
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Service Benefits -->
                    <div class="benefits-card card border-0 shadow-sm mt-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3">
                                <i class="fas fa-star me-2 text-warning"></i>Чому обирають нас
                            </h5>
                            <div class="benefit-item mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Безкоштовна консультація
                            </div>
                            <div class="benefit-item mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Прозора ціноутворення
                            </div>
                            <div class="benefit-item mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Досвідчена команда
                            </div>
                            <div class="benefit-item mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Гарантія якості
                            </div>
                            <div class="benefit-item">
                                <i class="fas fa-check text-success me-2"></i>
                                Регулярна звітність
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
.calculator-page .page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.service-option {
    transition: all 0.3s ease;
}

.service-option label {
    height: 120px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    transition: all 0.3s ease;
    border: 2px solid #e9ecef;
}

.service-option input[type="radio"]:checked + label {
    background: linear-gradient(45deg, #667eea, #764ba2);
    color: white;
    border-color: #667eea;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.options-group {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
}

.price-summary {
    top: 100px;
}

.price-breakdown-item {
    display: flex;
    justify-content: between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #e9ecef;
}

.price-breakdown-item:last-child {
    border-bottom: none;
}

.benefit-item {
    font-size: 0.9rem;
}

.sticky-top {
    top: 100px !important;
}

@media (max-width: 991px) {
    .sticky-top {
        position: relative !important;
        top: auto !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize calculator
    initializeCalculator();
});

function initializeCalculator() {
    // Service type selection
    document.querySelectorAll('input[name="serviceType"]').forEach(radio => {
        radio.addEventListener('change', function() {
            showServiceOptions(this.value);
            updatePriceCalculation();
        });
    });
    
    // All form inputs
    document.getElementById('calculatorForm').addEventListener('change', function() {
        updatePriceCalculation();
    });
}

function showServiceOptions(serviceType) {
    // Hide all options
    document.querySelectorAll('.options-group').forEach(group => {
        group.style.display = 'none';
    });
    
    // Show selected options
    const optionsElement = document.getElementById(serviceType + 'Options');
    if (optionsElement) {
        optionsElement.style.display = 'block';
    }
    
    // Show service options container
    document.getElementById('serviceOptions').style.display = 'block';
    
    // Show duration section for recurring services
    const durationSection = document.getElementById('durationSection');
    if (serviceType === 'smm' || serviceType === 'seo') {
        durationSection.style.display = 'block';
    } else {
        durationSection.style.display = 'none';
    }
}

function updatePriceCalculation() {
    const form = document.getElementById('calculatorForm');
    const formData = new FormData(form);
    
    const serviceType = formData.get('serviceType');
    if (!serviceType) {
        hidePrice();
        return;
    }
    
    let basePrice = 0;
    let breakdown = [];
    let isRecurring = false;
    
    // Calculate based on service type
    switch (serviceType) {
        case 'smm':
            basePrice = 2000; // Base SMM price
            breakdown.push({name: 'SMM базовий пакет', price: basePrice});
            isRecurring = true;
            
            // Add platform cost
            const platforms = formData.get('smmPlatforms');
            if (platforms) {
                const platformPrice = getPriceFromSelect('smmPlatforms');
                basePrice += platformPrice;
                breakdown.push({name: `${platforms} платформ${platforms > 1 ? 'и' : 'а'}`, price: platformPrice});
            }
            
            // Add posts cost
            const posts = formData.get('smmPosts');
            if (posts) {
                const postsPrice = getPriceFromSelect('smmPosts');
                basePrice += postsPrice;
                breakdown.push({name: `${posts} постів/міс`, price: postsPrice});
            }
            
            // Add extras
            basePrice += addCheckboxExtras(formData, breakdown, 'smm');
            break;
            
        case 'seo':
            basePrice = 3000; // Base SEO price
            breakdown.push({name: 'SEO базовий пакет', price: basePrice});
            isRecurring = true;
            
            // Add site type cost
            const siteType = getPriceFromSelect('seoSiteType');
            basePrice += siteType;
            breakdown.push({name: 'Тип сайту', price: siteType});
            
            // Add competition cost
            const competition = getPriceFromSelect('seoCompetition');
            if (competition > 0) {
                basePrice += competition;
                breakdown.push({name: 'Конкурентність ніші', price: competition});
            }
            
            // Add extras
            basePrice += addCheckboxExtras(formData, breakdown, 'seo');
            break;
            
        case 'web':
            const webType = getPriceFromSelect('webType');
            basePrice = webType;
            breakdown.push({name: 'Тип сайту', price: webType});
            
            // Add pages cost
            const pages = getPriceFromSelect('webPages');
            if (pages > 0) {
                basePrice += pages;
                breakdown.push({name: 'Додаткові сторінки', price: pages});
            }
            
            // Add extras
            basePrice += addCheckboxExtras(formData, breakdown, 'web');
            break;
            
        case 'design':
            const designType = getPriceFromSelect('designType');
            basePrice = designType;
            breakdown.push({name: 'Тип дизайну', price: designType});
            
            // Add concepts cost
            const concepts = getPriceFromSelect('designConcepts');
            if (concepts > 0) {
                basePrice += concepts;
                breakdown.push({name: 'Додаткові концепти', price: concepts});
            }
            
            // Add extras
            basePrice += addCheckboxExtras(formData, breakdown, 'design');
            break;
    }
    
    // Apply duration discount for recurring services
    let discount = 0;
    let finalPrice = basePrice;
    
    if (isRecurring) {
        const duration = formData.get('duration');
        const discountPercent = getDiscountPercent(duration);
        
        if (discountPercent > 0) {
            discount = Math.round(basePrice * discountPercent / 100);
            finalPrice = basePrice - discount;
        }
    }
    
    // Display price
    displayPrice(breakdown, finalPrice, discount, isRecurring);
}

function getPriceFromSelect(selectName) {
    const select = document.querySelector(`select[name="${selectName}"]`);
    if (!select) return 0;
    
    const selectedOption = select.options[select.selectedIndex];
    return parseInt(selectedOption.getAttribute('data-price')) || 0;
}

function addCheckboxExtras(formData, breakdown, prefix) {
    let extraCost = 0;
    const checkboxes = document.querySelectorAll(`input[name^="${prefix}"]:checked`);
    
    checkboxes.forEach(checkbox => {
        const price = parseInt(checkbox.value);
        extraCost += price;
        
        const label = document.querySelector(`label[for="${checkbox.id}"]`);
        if (label) {
            breakdown.push({
                name: label.textContent.replace(/\(\+[\d,]+ грн.*?\)/, '').trim(),
                price: price
            });
        }
    });
    
    return extraCost;
}

function getDiscountPercent(duration) {
    const durationInput = document.querySelector(`input[name="duration"][value="${duration}"]`);
    return parseInt(durationInput?.getAttribute('data-discount')) || 0;
}

function displayPrice(breakdown, finalPrice, discount, isRecurring) {
    const priceBreakdown = document.getElementById('priceBreakdown');
    const priceTotal = document.getElementById('priceTotal');
    const actionButtons = document.getElementById('actionButtons');
    
    // Clear previous breakdown
    priceBreakdown.innerHTML = '';
    
    // Add breakdown items
    breakdown.forEach(item => {
        const itemDiv = document.createElement('div');
        itemDiv.className = 'price-breakdown-item d-flex justify-content-between';
        itemDiv.innerHTML = `
            <span>${item.name}</span>
            <span>${item.price.toLocaleString()} грн</span>
        `;
        priceBreakdown.appendChild(itemDiv);
    });
    
    // Update total
    document.getElementById('totalAmount').textContent = finalPrice.toLocaleString() + ' грн';
    
    // Show/hide discount info
    const discountInfo = document.getElementById('discountInfo');
    if (discount > 0) {
        document.getElementById('discountAmount').textContent = discount.toLocaleString() + ' грн';
        discountInfo.style.display = 'block';
    } else {
        discountInfo.style.display = 'none';
    }
    
    // Show/hide monthly note
    const monthlyNote = document.getElementById('monthlyNote');
    monthlyNote.style.display = isRecurring ? 'block' : 'none';
    
    // Show price summary
    priceTotal.style.display = 'block';
    actionButtons.style.display = 'block';
}

function hidePrice() {
    document.getElementById('priceBreakdown').innerHTML = `
        <div class="text-center text-muted py-4">
            <i class="fas fa-calculator fa-3x mb-3"></i>
            <p>Оберіть послугу для розрахунку вартості</p>
        </div>
    `;
    document.getElementById('priceTotal').style.display = 'none';
    document.getElementById('actionButtons').style.display = 'none';
}

function orderCalculatedService() {
    const serviceType = document.querySelector('input[name="serviceType"]:checked')?.value;
    
    if (!serviceType) {
        alert('Оберіть послугу для замовлення');
        return;
    }
    
    // Check if user is logged in
    if (!<?php echo isLoggedIn() ? 'true' : 'false'; ?>) {
        const currentUrl = encodeURIComponent(window.location.href);
        window.location.href = `/user/login.php?return=${currentUrl}`;
        return;
    }
    
    // Redirect to order page with calculated data
    const calculatorData = getCalculatorData();
    const queryString = new URLSearchParams({
        service: serviceType,
        calculator: JSON.stringify(calculatorData)
    }).toString();
    
    window.location.href = `/services/order.php?${queryString}`;
}

function getConsultation() {
    // Scroll to consultation form on services page
    window.location.href = '/services.php#consultation';
}

function getCalculatorData() {
    const form = document.getElementById('calculatorForm');
    const formData = new FormData(form);
    const data = {};
    
    for (let [key, value] of formData.entries()) {
        data[key] = value;
    }
    
    return data;
}
</script>

<?php include '../../themes/footer.php'; ?>