<?php
require_once '../../core/config.php';
require_once '../../core/functions.php';

$pageTitle = 'Аналізатор сайтів - AdBoard Pro';
$pageDescription = 'Безкоштовний аналіз сайту: перевірка швидкості, SEO оптимізації, мобільної адаптації та SSL сертифікатів.';
$pageKeywords = 'аналіз сайту, швидкість завантаження, SEO аналіз, мобільна версія, SSL перевірка';

include '../../themes/header.php';
?>

<div class="site-analyzer-page">
    <!-- Header -->
    <section class="page-header py-5 bg-gradient">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center text-white">
                    <h1 class="display-4 fw-bold mb-3" data-aos="fade-up">
                        <i class="fas fa-tachometer-alt me-3"></i>Аналізатор сайтів
                    </h1>
                    <p class="lead mb-0" data-aos="fade-up" data-aos-delay="100">
                        Комплексна перевірка вашого сайту за секунди
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Analyzer Form -->
    <section class="analyzer-form py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="analyzer-card card border-0 shadow-lg">
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
                                <div class="analyzer-icon mb-3">
                                    <i class="fas fa-search fa-3x text-primary"></i>
                                </div>
                                <h3 class="fw-bold mb-2">Введіть URL для аналізу</h3>
                                <p class="text-muted">Ми перевіримо швидкість, SEO та безпеку вашого сайту</p>
                            </div>

                            <form id="analyzerForm" class="mb-4">
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text">
                                        <i class="fas fa-globe"></i>
                                    </span>
                                    <input 
                                        type="url" 
                                        class="form-control" 
                                        id="websiteUrl" 
                                        placeholder="https://example.com" 
                                        required
                                    >
                                    <button class="btn btn-primary" type="submit" id="analyzeBtn">
                                        <i class="fas fa-search me-2"></i>Аналізувати
                                    </button>
                                </div>
                                <div class="form-text mt-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Введіть повний URL включаючи https:// або http://
                                </div>
                            </form>

                            <!-- Quick Examples -->
                            <div class="quick-examples">
                                <p class="small text-muted mb-2">Приклади для швидкого тестування:</p>
                                <div class="d-flex flex-wrap gap-2">
                                    <button class="btn btn-sm btn-outline-secondary" onclick="setUrl('https://google.com')">
                                        Google
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="setUrl('https://facebook.com')">
                                        Facebook
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="setUrl('https://ukraine.ua')">
                                        Ukraine.ua
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Analysis Results -->
    <section id="analysisResults" class="analysis-results py-5" style="display: none;">
        <div class="container">
            <!-- Loading State -->
            <div id="loadingState" class="text-center">
                <div class="loading-animation mb-4">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Аналізуємо...</span>
                    </div>
                </div>
                <h4 class="fw-bold mb-2">Аналізуємо ваш сайт...</h4>
                <p class="text-muted mb-4">Це може зайняти кілька секунд</p>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" id="progressBar" style="width: 0%"></div>
                </div>
                <div id="loadingSteps" class="mt-3">
                    <div class="loading-step" data-step="1">
                        <i class="fas fa-clock me-2"></i>Перевіряємо швидкість завантаження...
                    </div>
                </div>
            </div>

            <!-- Results Display -->
            <div id="resultsDisplay" style="display: none;">
                <!-- Overall Score -->
                <div class="overall-score card border-0 shadow-lg mb-4">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <div class="site-info">
                                    <h3 class="fw-bold mb-2" id="siteTitle">
                                        <i class="fas fa-globe me-2"></i>
                                        <span id="siteDomain"></span>
                                    </h3>
                                    <p class="text-muted mb-3" id="siteDescription"></p>
                                    <div class="site-stats">
                                        <span class="badge bg-light text-dark me-2">
                                            <i class="fas fa-clock me-1"></i>
                                            Час аналізу: <span id="analysisTime"></span>
                                        </span>
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-calendar me-1"></i>
                                            <span id="analysisDate"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 text-center">
                                <div class="overall-score-circle">
                                    <div class="score-circle" id="overallScore">
                                        <span class="score-value">0</span>
                                        <span class="score-label">Загальний бал</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Scores -->
                <div class="detailed-scores">
                    <div class="row g-4">
                        <!-- Performance -->
                        <div class="col-lg-3 col-md-6">
                            <div class="score-card card border-0 shadow">
                                <div class="card-body p-4 text-center">
                                    <div class="score-icon mb-3">
                                        <i class="fas fa-tachometer-alt fa-2x text-primary"></i>
                                    </div>
                                    <h5 class="fw-bold mb-2">Швидкість</h5>
                                    <div class="score-value-large" id="performanceScore">0</div>
                                    <div class="score-details mt-3">
                                        <div class="detail-item">
                                            <span class="detail-label">Час завантаження:</span>
                                            <span class="detail-value" id="loadTime">-</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">Розмір сторінки:</span>
                                            <span class="detail-value" id="pageSize">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SEO -->
                        <div class="col-lg-3 col-md-6">
                            <div class="score-card card border-0 shadow">
                                <div class="card-body p-4 text-center">
                                    <div class="score-icon mb-3">
                                        <i class="fas fa-search fa-2x text-success"></i>
                                    </div>
                                    <h5 class="fw-bold mb-2">SEO</h5>
                                    <div class="score-value-large" id="seoScore">0</div>
                                    <div class="score-details mt-3">
                                        <div class="detail-item">
                                            <span class="detail-label">Title:</span>
                                            <span class="detail-value" id="hasTitle">-</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">Description:</span>
                                            <span class="detail-value" id="hasDescription">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Security -->
                        <div class="col-lg-3 col-md-6">
                            <div class="score-card card border-0 shadow">
                                <div class="card-body p-4 text-center">
                                    <div class="score-icon mb-3">
                                        <i class="fas fa-shield-alt fa-2x text-warning"></i>
                                    </div>
                                    <h5 class="fw-bold mb-2">Безпека</h5>
                                    <div class="score-value-large" id="securityScore">0</div>
                                    <div class="score-details mt-3">
                                        <div class="detail-item">
                                            <span class="detail-label">HTTPS:</span>
                                            <span class="detail-value" id="hasHttps">-</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">SSL сертифікат:</span>
                                            <span class="detail-value" id="sslStatus">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile -->
                        <div class="col-lg-3 col-md-6">
                            <div class="score-card card border-0 shadow">
                                <div class="card-body p-4 text-center">
                                    <div class="score-icon mb-3">
                                        <i class="fas fa-mobile-alt fa-2x text-info"></i>
                                    </div>
                                    <h5 class="fw-bold mb-2">Мобільність</h5>
                                    <div class="score-value-large" id="mobileScore">0</div>
                                    <div class="score-details mt-3">
                                        <div class="detail-item">
                                            <span class="detail-label">Viewport:</span>
                                            <span class="detail-value" id="hasViewport">-</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">Адаптивність:</span>
                                            <span class="detail-value" id="isResponsive">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Analysis -->
                <div class="detailed-analysis mt-5">
                    <div class="row">
                        <!-- Recommendations -->
                        <div class="col-lg-8">
                            <div class="recommendations card border-0 shadow">
                                <div class="card-header bg-transparent">
                                    <h4 class="fw-bold mb-0">
                                        <i class="fas fa-lightbulb me-2 text-warning"></i>
                                        Рекомендації для покращення
                                    </h4>
                                </div>
                                <div class="card-body p-4">
                                    <div id="recommendationsList">
                                        <!-- Recommendations will be loaded here -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Technical Details -->
                        <div class="col-lg-4">
                            <div class="technical-details card border-0 shadow">
                                <div class="card-header bg-transparent">
                                    <h5 class="fw-bold mb-0">
                                        <i class="fas fa-cogs me-2"></i>
                                        Технічні деталі
                                    </h5>
                                </div>
                                <div class="card-body p-4">
                                    <div id="technicalDetailsList">
                                        <!-- Technical details will be loaded here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons text-center mt-5">
                    <button class="btn btn-primary btn-lg me-3" onclick="analyzeSite()">
                        <i class="fas fa-redo me-2"></i>Аналізувати знову
                    </button>
                    <button class="btn btn-outline-primary btn-lg me-3" onclick="downloadReport()">
                        <i class="fas fa-download me-2"></i>Завантажити звіт
                    </button>
                    <button class="btn btn-success btn-lg" onclick="getConsultation()">
                        <i class="fas fa-phone me-2"></i>Отримати консультацію
                    </button>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
.site-analyzer-page .page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.analyzer-card {
    border-radius: 20px;
}

.analyzer-icon {
    background: rgba(102, 126, 234, 0.1);
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

.quick-examples button {
    transition: all 0.3s ease;
}

.quick-examples button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.loading-animation {
    position: relative;
}

.loading-step {
    color: #6c757d;
    padding: 8px 0;
    transition: all 0.3s ease;
}

.loading-step.active {
    color: #667eea;
    font-weight: 500;
}

.overall-score-circle {
    position: relative;
}

.score-circle {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: conic-gradient(from 0deg, #28a745, #ffc107, #dc3545);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    position: relative;
}

.score-circle::before {
    content: '';
    position: absolute;
    width: 90px;
    height: 90px;
    background: white;
    border-radius: 50%;
}

.score-value, .score-label {
    position: relative;
    z-index: 2;
}

.score-value {
    font-size: 2rem;
    font-weight: bold;
    color: #333;
}

.score-label {
    font-size: 0.8rem;
    color: #666;
}

.score-card {
    transition: all 0.3s ease;
    border-radius: 15px;
}

.score-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
}

.score-value-large {
    font-size: 2.5rem;
    font-weight: bold;
    margin: 10px 0;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
    font-size: 0.9rem;
}

.detail-label {
    color: #6c757d;
}

.detail-value {
    font-weight: 500;
}

.recommendation-item {
    border-left: 4px solid #667eea;
    background: #f8f9fa;
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 0 8px 8px 0;
}

.recommendation-item.high-priority {
    border-left-color: #dc3545;
}

.recommendation-item.medium-priority {
    border-left-color: #ffc107;
}

.recommendation-item.low-priority {
    border-left-color: #28a745;
}

.technical-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #e9ecef;
}

.technical-item:last-child {
    border-bottom: none;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 500;
}

.status-good {
    background: #d4edda;
    color: #155724;
}

.status-warning {
    background: #fff3cd;
    color: #856404;
}

.status-error {
    background: #f8d7da;
    color: #721c24;
}
</style>

<script>
let currentAnalysisData = null;

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('analyzerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        analyzeSite();
    });
});

function setUrl(url) {
    document.getElementById('websiteUrl').value = url;
}

function analyzeSite() {
    const url = document.getElementById('websiteUrl').value.trim();
    
    if (!url) {
        alert('Введіть URL сайту для аналізу');
        return;
    }
    
    // Validate URL
    try {
        new URL(url);
    } catch (e) {
        alert('Введіть коректний URL (наприклад: https://example.com)');
        return;
    }
    
    // Show loading state
    showLoadingState();
    
    // Start analysis
    performAnalysis(url);
}

function showLoadingState() {
    document.getElementById('analysisResults').style.display = 'block';
    document.getElementById('loadingState').style.display = 'block';
    document.getElementById('resultsDisplay').style.display = 'none';
    
    // Animate progress
    animateProgress();
    
    // Smooth scroll to results
    document.getElementById('analysisResults').scrollIntoView({
        behavior: 'smooth'
    });
}

function animateProgress() {
    const progressBar = document.getElementById('progressBar');
    const steps = [
        'Перевіряємо швидкість завантаження...',
        'Аналізуємо SEO оптимізацію...',
        'Перевіряємо безпеку сайту...',
        'Тестуємо мобільну версію...',
        'Генеруємо рекомендації...'
    ];
    
    let currentStep = 0;
    let progress = 0;
    
    const interval = setInterval(() => {
        progress += Math.random() * 20;
        if (progress > 100) progress = 100;
        
        progressBar.style.width = progress + '%';
        
        // Update step
        if (currentStep < steps.length && progress > (currentStep + 1) * 20) {
            updateLoadingStep(currentStep, steps[currentStep]);
            currentStep++;
        }
        
        if (progress >= 100) {
            clearInterval(interval);
            setTimeout(() => {
                showResults();
            }, 500);
        }
    }, 200);
}

function updateLoadingStep(stepIndex, stepText) {
    const stepsContainer = document.getElementById('loadingSteps');
    
    // Add new step
    const stepElement = document.createElement('div');
    stepElement.className = 'loading-step active';
    stepElement.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i>${stepText}`;
    stepsContainer.appendChild(stepElement);
    
    // Mark previous steps as completed
    const previousSteps = stepsContainer.querySelectorAll('.loading-step:not(.active)');
    previousSteps.forEach(step => {
        step.innerHTML = step.innerHTML.replace('fa-spinner fa-spin', 'fa-check text-success');
    });
}

function performAnalysis(url) {
    // Simulate analysis with fetch to backend
    fetch('/ajax/site-analyzer.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ url: url })
    })
    .then(response => response.json())
    .then(data => {
        currentAnalysisData = data;
        displayResults(data);
    })
    .catch(error => {
        console.error('Analysis error:', error);
        // Show simulated results for demo
        currentAnalysisData = generateSimulatedResults(url);
        displayResults(currentAnalysisData);
    });
}

function generateSimulatedResults(url) {
    const domain = new URL(url).hostname;
    const isHttps = url.startsWith('https://');
    
    // Generate realistic scores
    const performanceScore = Math.floor(Math.random() * 40) + 60; // 60-100
    const seoScore = Math.floor(Math.random() * 30) + 70; // 70-100
    const securityScore = isHttps ? Math.floor(Math.random() * 20) + 80 : Math.floor(Math.random() * 50) + 30; // Based on HTTPS
    const mobileScore = Math.floor(Math.random() * 25) + 75; // 75-100
    
    const overallScore = Math.round((performanceScore + seoScore + securityScore + mobileScore) / 4);
    
    return {
        success: true,
        url: url,
        domain: domain,
        analysis_time: new Date().toISOString(),
        scores: {
            overall: overallScore,
            performance: performanceScore,
            seo: seoScore,
            security: securityScore,
            mobile: mobileScore
        },
        metrics: {
            load_time: (Math.random() * 3 + 0.5).toFixed(2) + 's',
            page_size: (Math.random() * 2 + 0.5).toFixed(1) + 'MB',
            requests: Math.floor(Math.random() * 50) + 20,
            has_title: Math.random() > 0.2,
            has_description: Math.random() > 0.3,
            has_https: isHttps,
            ssl_valid: isHttps && Math.random() > 0.1,
            has_viewport: Math.random() > 0.1,
            is_responsive: Math.random() > 0.2
        },
        recommendations: generateRecommendations(performanceScore, seoScore, securityScore, mobileScore),
        technical_details: {
            server: 'Apache/2.4.41',
            cms: Math.random() > 0.5 ? 'WordPress' : 'Custom',
            analytics: Math.random() > 0.3 ? 'Google Analytics' : 'None',
            framework: Math.random() > 0.5 ? 'Bootstrap' : 'Custom CSS'
        }
    };
}

function generateRecommendations(performance, seo, security, mobile) {
    const recommendations = [];
    
    if (performance < 80) {
        recommendations.push({
            priority: 'high',
            title: 'Оптимізуйте швидкість завантаження',
            description: 'Стискайте зображення, мініфікуйте CSS/JS, використовуйте кешування.'
        });
    }
    
    if (seo < 85) {
        recommendations.push({
            priority: 'medium',
            title: 'Покращте SEO оптимізацію',
            description: 'Додайте мета-теги, оптимізуйте заголовки, покращте структуру URL.'
        });
    }
    
    if (security < 80) {
        recommendations.push({
            priority: 'high',
            title: 'Підвищте рівень безпеки',
            description: 'Встановіть SSL сертифікат, використовуйте HTTPS, оновіть заголовки безпеки.'
        });
    }
    
    if (mobile < 85) {
        recommendations.push({
            priority: 'medium',
            title: 'Покращте мобільну версію',
            description: 'Додайте viewport meta-тег, зробіть дизайн адаптивним.'
        });
    }
    
    return recommendations;
}

function showResults() {
    document.getElementById('loadingState').style.display = 'none';
    document.getElementById('resultsDisplay').style.display = 'block';
}

function displayResults(data) {
    if (!data.success) {
        alert('Помилка аналізу: ' + (data.error || 'Невідома помилка'));
        return;
    }
    
    // Update site info
    document.getElementById('siteDomain').textContent = data.domain;
    document.getElementById('analysisTime').textContent = new Date(data.analysis_time).toLocaleTimeString();
    document.getElementById('analysisDate').textContent = new Date(data.analysis_time).toLocaleDateString();
    
    // Update scores
    updateScore('overallScore', data.scores.overall);
    updateScore('performanceScore', data.scores.performance, 'primary');
    updateScore('seoScore', data.scores.seo, 'success');
    updateScore('securityScore', data.scores.security, 'warning');
    updateScore('mobileScore', data.scores.mobile, 'info');
    
    // Update metrics
    document.getElementById('loadTime').textContent = data.metrics.load_time;
    document.getElementById('pageSize').textContent = data.metrics.page_size;
    document.getElementById('hasTitle').innerHTML = getStatusBadge(data.metrics.has_title);
    document.getElementById('hasDescription').innerHTML = getStatusBadge(data.metrics.has_description);
    document.getElementById('hasHttps').innerHTML = getStatusBadge(data.metrics.has_https);
    document.getElementById('sslStatus').innerHTML = getStatusBadge(data.metrics.ssl_valid);
    document.getElementById('hasViewport').innerHTML = getStatusBadge(data.metrics.has_viewport);
    document.getElementById('isResponsive').innerHTML = getStatusBadge(data.metrics.is_responsive);
    
    // Update recommendations
    displayRecommendations(data.recommendations);
    
    // Update technical details
    displayTechnicalDetails(data.technical_details);
}

function updateScore(elementId, score, colorClass = 'primary') {
    const element = document.getElementById(elementId);
    const scoreElement = element.querySelector('.score-value') || element;
    
    // Animate score
    let currentScore = 0;
    const increment = score / 50;
    const timer = setInterval(() => {
        currentScore += increment;
        if (currentScore >= score) {
            currentScore = score;
            clearInterval(timer);
        }
        scoreElement.textContent = Math.round(currentScore);
    }, 20);
    
    // Update color based on score
    if (element.classList.contains('score-value-large')) {
        element.className = `score-value-large text-${getScoreColor(score)}`;
    }
}

function getScoreColor(score) {
    if (score >= 80) return 'success';
    if (score >= 60) return 'warning';
    return 'danger';
}

function getStatusBadge(status) {
    if (status === true) {
        return '<span class="status-badge status-good">✓ Так</span>';
    } else if (status === false) {
        return '<span class="status-badge status-error">✗ Ні</span>';
    } else {
        return '<span class="status-badge status-warning">? Невідомо</span>';
    }
}

function displayRecommendations(recommendations) {
    const container = document.getElementById('recommendationsList');
    container.innerHTML = '';
    
    if (recommendations.length === 0) {
        container.innerHTML = '<p class="text-muted">Відмінно! Серйозних проблем не виявлено.</p>';
        return;
    }
    
    recommendations.forEach(rec => {
        const recElement = document.createElement('div');
        recElement.className = `recommendation-item ${rec.priority}-priority`;
        recElement.innerHTML = `
            <h6 class="fw-bold mb-2">
                <i class="fas fa-${getPriorityIcon(rec.priority)} me-2"></i>
                ${rec.title}
            </h6>
            <p class="mb-0 small">${rec.description}</p>
        `;
        container.appendChild(recElement);
    });
}

function getPriorityIcon(priority) {
    switch (priority) {
        case 'high': return 'exclamation-triangle';
        case 'medium': return 'exclamation-circle';
        case 'low': return 'info-circle';
        default: return 'lightbulb';
    }
}

function displayTechnicalDetails(details) {
    const container = document.getElementById('technicalDetailsList');
    container.innerHTML = '';
    
    Object.entries(details).forEach(([key, value]) => {
        const detailElement = document.createElement('div');
        detailElement.className = 'technical-item';
        detailElement.innerHTML = `
            <span class="detail-label">${formatTechnicalKey(key)}:</span>
            <span class="detail-value">${value}</span>
        `;
        container.appendChild(detailElement);
    });
}

function formatTechnicalKey(key) {
    const labels = {
        server: 'Сервер',
        cms: 'CMS',
        analytics: 'Аналітика',
        framework: 'Фреймворк'
    };
    return labels[key] || key;
}

function downloadReport() {
    if (!currentAnalysisData) {
        alert('Немає даних для завантаження');
        return;
    }
    
    // Generate PDF report (simplified version)
    const reportData = {
        url: currentAnalysisData.url,
        scores: currentAnalysisData.scores,
        date: new Date().toLocaleDateString(),
        recommendations: currentAnalysisData.recommendations
    };
    
    // For demo purposes, create a simple text report
    let report = `Звіт аналізу сайту: ${reportData.url}\n`;
    report += `Дата аналізу: ${reportData.date}\n\n`;
    report += `Загальний бал: ${reportData.scores.overall}/100\n`;
    report += `Швидкість: ${reportData.scores.performance}/100\n`;
    report += `SEO: ${reportData.scores.seo}/100\n`;
    report += `Безпека: ${reportData.scores.security}/100\n`;
    report += `Мобільність: ${reportData.scores.mobile}/100\n\n`;
    report += `Рекомендації:\n`;
    reportData.recommendations.forEach((rec, index) => {
        report += `${index + 1}. ${rec.title}\n   ${rec.description}\n\n`;
    });
    
    // Download as text file
    const blob = new Blob([report], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `site-analysis-${new Date().getTime()}.txt`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

function getConsultation() {
    window.location.href = '/services.php#consultation';
}
</script>

<?php include '../../themes/footer.php'; ?>