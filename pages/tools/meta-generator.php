<?php
require_once '../../core/config.php';
require_once '../../core/functions.php';

$pageTitle = 'Генератор мета-тегів - AdBoard Pro';
$pageDescription = 'Безкоштовний генератор мета-тегів для SEO оптимізації: title, description, keywords, Open Graph, Twitter Cards.';
$pageKeywords = 'генератор мета-тегів, SEO теги, title, description, Open Graph, Twitter Cards';

include '../../themes/header.php';
?>

<div class="meta-generator-page">
    <!-- Header -->
    <section class="page-header py-5 bg-gradient">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center text-white">
                    <h1 class="display-4 fw-bold mb-3" data-aos="fade-up">
                        <i class="fas fa-tags me-3"></i>Генератор мета-тегів
                    </h1>
                    <p class="lead mb-0" data-aos="fade-up" data-aos-delay="100">
                        Створіть SEO-оптимізовані мета-теги для вашого сайту
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Generator Form -->
    <section class="generator-section py-5">
        <div class="container">
            <div class="row">
                <!-- Form -->
                <div class="col-lg-6 mb-4">
                    <div class="generator-form card border-0 shadow-lg">
                        <div class="card-header bg-transparent">
                            <h3 class="h4 fw-bold mb-0">
                                <i class="fas fa-edit me-2 text-primary"></i>Введіть дані вашої сторінки
                            </h3>
                        </div>
                        <div class="card-body p-4">
                            <form id="metaForm">
                                <!-- Basic SEO -->
                                <div class="form-section mb-4">
                                    <h5 class="fw-bold mb-3 text-primary">
                                        <i class="fas fa-search me-2"></i>Основні SEO теги
                                    </h5>
                                    
                                    <div class="mb-3">
                                        <label for="pageTitle" class="form-label">
                                            Title (заголовок сторінки)
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="pageTitle" maxlength="60" required>
                                        <div class="form-text">
                                            <span class="char-counter" data-target="pageTitle">0</span>/60 символів
                                            <span class="ms-2 text-info">
                                                <i class="fas fa-info-circle"></i>
                                                Оптимально: 50-60 символів
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="pageDescription" class="form-label">
                                            Description (опис сторінки)
                                            <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" id="pageDescription" rows="3" maxlength="160" required></textarea>
                                        <div class="form-text">
                                            <span class="char-counter" data-target="pageDescription">0</span>/160 символів
                                            <span class="ms-2 text-info">
                                                <i class="fas fa-info-circle"></i>
                                                Оптимально: 150-160 символів
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="pageKeywords" class="form-label">Keywords (ключові слова)</label>
                                        <input type="text" class="form-control" id="pageKeywords" placeholder="слово1, слово2, слово3">
                                        <div class="form-text">
                                            <i class="fas fa-lightbulb me-1"></i>
                                            Розділяйте ключові слова комами. Рекомендовано: 5-10 слів
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="pageUrl" class="form-label">URL сторінки</label>
                                        <input type="url" class="form-control" id="pageUrl" placeholder="https://yoursite.com/page">
                                        <div class="form-text">
                                            Повний URL сторінки для Open Graph та Twitter Cards
                                        </div>
                                    </div>
                                </div>

                                <!-- Open Graph -->
                                <div class="form-section mb-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <h5 class="fw-bold mb-0 text-primary me-3">
                                            <i class="fab fa-facebook me-2"></i>Open Graph (Facebook, LinkedIn)
                                        </h5>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="enableOG" checked>
                                            <label class="form-check-label" for="enableOG">Увімкнути</label>
                                        </div>
                                    </div>

                                    <div id="ogFields">
                                        <div class="mb-3">
                                            <label for="ogTitle" class="form-label">OG Title</label>
                                            <input type="text" class="form-control" id="ogTitle" maxlength="95">
                                            <div class="form-text">
                                                <span class="char-counter" data-target="ogTitle">0</span>/95 символів
                                                <span class="ms-2 text-muted">Автозаповнення з Title</span>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="ogDescription" class="form-label">OG Description</label>
                                            <textarea class="form-control" id="ogDescription" rows="2" maxlength="300"></textarea>
                                            <div class="form-text">
                                                <span class="char-counter" data-target="ogDescription">0</span>/300 символів
                                                <span class="ms-2 text-muted">Автозаповнення з Description</span>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="ogImage" class="form-label">OG Image (зображення)</label>
                                            <input type="url" class="form-control" id="ogImage" placeholder="https://yoursite.com/image.jpg">
                                            <div class="form-text">
                                                <i class="fas fa-image me-1"></i>
                                                Рекомендований розмір: 1200x630 px
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="ogType" class="form-label">OG Type</label>
                                                <select class="form-select" id="ogType">
                                                    <option value="website">Website</option>
                                                    <option value="article">Article</option>
                                                    <option value="product">Product</option>
                                                    <option value="profile">Profile</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="ogSiteName" class="form-label">Site Name</label>
                                                <input type="text" class="form-control" id="ogSiteName" placeholder="Назва вашого сайту">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Twitter Cards -->
                                <div class="form-section mb-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <h5 class="fw-bold mb-0 text-primary me-3">
                                            <i class="fab fa-twitter me-2"></i>Twitter Cards
                                        </h5>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="enableTwitter" checked>
                                            <label class="form-check-label" for="enableTwitter">Увімкнути</label>
                                        </div>
                                    </div>

                                    <div id="twitterFields">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="twitterCard" class="form-label">Card Type</label>
                                                <select class="form-select" id="twitterCard">
                                                    <option value="summary">Summary</option>
                                                    <option value="summary_large_image">Summary Large Image</option>
                                                    <option value="app">App</option>
                                                    <option value="player">Player</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="twitterSite" class="form-label">Twitter Username</label>
                                                <input type="text" class="form-control" id="twitterSite" placeholder="@yoursite">
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="twitterTitle" class="form-label">Twitter Title</label>
                                            <input type="text" class="form-control" id="twitterTitle" maxlength="70">
                                            <div class="form-text">
                                                <span class="char-counter" data-target="twitterTitle">0</span>/70 символів
                                                <span class="ms-2 text-muted">Автозаповнення з Title</span>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="twitterDescription" class="form-label">Twitter Description</label>
                                            <textarea class="form-control" id="twitterDescription" rows="2" maxlength="200"></textarea>
                                            <div class="form-text">
                                                <span class="char-counter" data-target="twitterDescription">0</span>/200 символів
                                                <span class="ms-2 text-muted">Автозаповнення з Description</span>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="twitterImage" class="form-label">Twitter Image</label>
                                            <input type="url" class="form-control" id="twitterImage" placeholder="https://yoursite.com/twitter-image.jpg">
                                            <div class="form-text">
                                                <i class="fas fa-image me-1"></i>
                                                Рекомендований розмір: 1024x512 px
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Tags -->
                                <div class="form-section mb-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <h5 class="fw-bold mb-0 text-primary me-3">
                                            <i class="fas fa-cogs me-2"></i>Додаткові теги
                                        </h5>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="enableAdditional">
                                            <label class="form-check-label" for="enableAdditional">Увімкнути</label>
                                        </div>
                                    </div>

                                    <div id="additionalFields" style="display: none;">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="robots" class="form-label">Robots</label>
                                                <select class="form-select" id="robots">
                                                    <option value="index, follow">Index, Follow</option>
                                                    <option value="noindex, follow">No Index, Follow</option>
                                                    <option value="index, nofollow">Index, No Follow</option>
                                                    <option value="noindex, nofollow">No Index, No Follow</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="canonical" class="form-label">Canonical URL</label>
                                                <input type="url" class="form-control" id="canonical" placeholder="https://yoursite.com/canonical-page">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="author" class="form-label">Author</label>
                                                <input type="text" class="form-control" id="author" placeholder="Ім'я автора">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="language" class="form-label">Language</label>
                                                <select class="form-select" id="language">
                                                    <option value="uk">Українська (uk)</option>
                                                    <option value="ru">Русский (ru)</option>
                                                    <option value="en">English (en)</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="viewport" class="form-label">Viewport</label>
                                            <input type="text" class="form-control" id="viewport" value="width=device-width, initial-scale=1.0">
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="text-center">
                                    <button type="button" class="btn btn-primary btn-lg me-3" onclick="generateMetaTags()">
                                        <i class="fas fa-magic me-2"></i>Генерувати теги
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-lg" onclick="clearForm()">
                                        <i class="fas fa-trash me-2"></i>Очистити
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Generated Code -->
                <div class="col-lg-6">
                    <div class="generated-code card border-0 shadow-lg sticky-top">
                        <div class="card-header bg-primary text-white">
                            <h4 class="h5 fw-bold mb-0">
                                <i class="fas fa-code me-2"></i>Згенеровані мета-теги
                            </h4>
                        </div>
                        <div class="card-body p-0">
                            <div id="generatedTags" class="code-container">
                                <div class="text-center text-muted py-5">
                                    <i class="fas fa-code fa-3x mb-3"></i>
                                    <p>Заповніть форму зліва для генерації мета-тегів</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="d-flex gap-2">
                                <button class="btn btn-success w-100" onclick="copyToClipboard()">
                                    <i class="fas fa-copy me-2"></i>Копіювати код
                                </button>
                                <button class="btn btn-outline-primary w-100" onclick="downloadCode()">
                                    <i class="fas fa-download me-2"></i>Завантажити
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Preview Cards -->
                    <div class="preview-section mt-4">
                        <h5 class="fw-bold mb-3">
                            <i class="fas fa-eye me-2"></i>Попередній перегляд
                        </h5>
                        
                        <!-- Google Preview -->
                        <div class="preview-card google-preview card border-0 shadow-sm mb-3">
                            <div class="card-header">
                                <h6 class="fw-bold mb-0">
                                    <i class="fab fa-google me-2"></i>Google Search Result
                                </h6>
                            </div>
                            <div class="card-body">
                                <div id="googlePreview">
                                    <div class="preview-title">Заголовок сторінки</div>
                                    <div class="preview-url">https://yoursite.com</div>
                                    <div class="preview-description">Опис сторінки буде відображатися тут...</div>
                                </div>
                            </div>
                        </div>

                        <!-- Facebook Preview -->
                        <div class="preview-card facebook-preview card border-0 shadow-sm mb-3">
                            <div class="card-header">
                                <h6 class="fw-bold mb-0">
                                    <i class="fab fa-facebook me-2"></i>Facebook Share
                                </h6>
                            </div>
                            <div class="card-body">
                                <div id="facebookPreview">
                                    <div class="preview-image-placeholder">
                                        <i class="fas fa-image"></i>
                                        <span>Зображення</span>
                                    </div>
                                    <div class="preview-content">
                                        <div class="preview-title">Заголовок</div>
                                        <div class="preview-description">Опис...</div>
                                        <div class="preview-domain">YOURSITE.COM</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Twitter Preview -->
                        <div class="preview-card twitter-preview card border-0 shadow-sm">
                            <div class="card-header">
                                <h6 class="fw-bold mb-0">
                                    <i class="fab fa-twitter me-2"></i>Twitter Card
                                </h6>
                            </div>
                            <div class="card-body">
                                <div id="twitterPreview">
                                    <div class="preview-image-placeholder">
                                        <i class="fas fa-image"></i>
                                        <span>Зображення</span>
                                    </div>
                                    <div class="preview-content">
                                        <div class="preview-title">Заголовок</div>
                                        <div class="preview-description">Опис...</div>
                                        <div class="preview-domain">yoursite.com</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
.meta-generator-page .page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.form-section {
    border-bottom: 1px solid #e9ecef;
    padding-bottom: 1.5rem;
}

.form-section:last-child {
    border-bottom: none;
}

.char-counter {
    font-weight: 500;
}

.char-counter.warning {
    color: #ffc107;
}

.char-counter.danger {
    color: #dc3545;
}

.sticky-top {
    top: 100px;
}

.code-container {
    max-height: 600px;
    overflow-y: auto;
    background: #282c34;
    color: #abb2bf;
    font-family: 'Courier New', monospace;
    font-size: 13px;
    line-height: 1.5;
    padding: 20px;
}

.code-container pre {
    margin: 0;
    white-space: pre-wrap;
    word-wrap: break-word;
}

.code-tag {
    color: #e06c75;
}

.code-attr {
    color: #d19a66;
}

.code-value {
    color: #98c379;
}

.code-comment {
    color: #5c6370;
    font-style: italic;
}

.preview-card {
    border-radius: 10px;
}

.google-preview .preview-title {
    color: #1a0dab;
    font-size: 18px;
    font-weight: 400;
    text-decoration: underline;
    margin-bottom: 2px;
}

.google-preview .preview-url {
    color: #006621;
    font-size: 14px;
    margin-bottom: 8px;
}

.google-preview .preview-description {
    color: #545454;
    font-size: 13px;
    line-height: 1.4;
}

.facebook-preview #facebookPreview {
    border: 1px solid #dddfe2;
    border-radius: 8px;
    overflow: hidden;
    background: white;
}

.facebook-preview .preview-image-placeholder {
    height: 150px;
    background: #f0f2f5;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #65676b;
}

.facebook-preview .preview-content {
    padding: 12px;
}

.facebook-preview .preview-title {
    font-weight: 600;
    color: #1c1e21;
    font-size: 16px;
    margin-bottom: 4px;
}

.facebook-preview .preview-description {
    color: #65676b;
    font-size: 14px;
    margin-bottom: 8px;
}

.facebook-preview .preview-domain {
    color: #65676b;
    font-size: 12px;
    text-transform: uppercase;
    font-weight: 600;
}

.twitter-preview #twitterPreview {
    border: 1px solid #cfd9de;
    border-radius: 16px;
    overflow: hidden;
    background: white;
}

.twitter-preview .preview-image-placeholder {
    height: 120px;
    background: #f7f9fa;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #536471;
}

.twitter-preview .preview-content {
    padding: 12px;
}

.twitter-preview .preview-title {
    font-weight: 700;
    color: #0f1419;
    font-size: 15px;
    margin-bottom: 2px;
}

.twitter-preview .preview-description {
    color: #536471;
    font-size: 14px;
    margin-bottom: 8px;
}

.twitter-preview .preview-domain {
    color: #536471;
    font-size: 14px;
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
    initializeMetaGenerator();
});

function initializeMetaGenerator() {
    // Character counters
    setupCharacterCounters();
    
    // Auto-fill functionality
    setupAutoFill();
    
    // Toggle sections
    setupToggleSections();
    
    // Real-time preview updates
    setupPreviewUpdates();
}

function setupCharacterCounters() {
    const inputs = ['pageTitle', 'pageDescription', 'ogTitle', 'ogDescription', 'twitterTitle', 'twitterDescription'];
    
    inputs.forEach(inputId => {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('input', function() {
                updateCharacterCounter(inputId);
            });
        }
    });
}

function updateCharacterCounter(inputId) {
    const input = document.getElementById(inputId);
    const counter = document.querySelector(`.char-counter[data-target="${inputId}"]`);
    
    if (!input || !counter) return;
    
    const length = input.value.length;
    const maxLength = parseInt(input.getAttribute('maxlength')) || 1000;
    
    counter.textContent = length;
    
    // Color coding
    counter.className = 'char-counter';
    if (length > maxLength * 0.9) {
        counter.classList.add('danger');
    } else if (length > maxLength * 0.8) {
        counter.classList.add('warning');
    }
}

function setupAutoFill() {
    // Auto-fill OG tags from basic tags
    document.getElementById('pageTitle').addEventListener('input', function() {
        if (!document.getElementById('ogTitle').value) {
            document.getElementById('ogTitle').value = this.value;
            updateCharacterCounter('ogTitle');
        }
        if (!document.getElementById('twitterTitle').value) {
            document.getElementById('twitterTitle').value = this.value;
            updateCharacterCounter('twitterTitle');
        }
        updatePreview();
    });
    
    document.getElementById('pageDescription').addEventListener('input', function() {
        if (!document.getElementById('ogDescription').value) {
            document.getElementById('ogDescription').value = this.value;
            updateCharacterCounter('ogDescription');
        }
        if (!document.getElementById('twitterDescription').value) {
            document.getElementById('twitterDescription').value = this.value;
            updateCharacterCounter('twitterDescription');
        }
        updatePreview();
    });
    
    document.getElementById('pageUrl').addEventListener('input', function() {
        updatePreview();
    });
}

function setupToggleSections() {
    document.getElementById('enableOG').addEventListener('change', function() {
        document.getElementById('ogFields').style.display = this.checked ? 'block' : 'none';
    });
    
    document.getElementById('enableTwitter').addEventListener('change', function() {
        document.getElementById('twitterFields').style.display = this.checked ? 'block' : 'none';
    });
    
    document.getElementById('enableAdditional').addEventListener('change', function() {
        document.getElementById('additionalFields').style.display = this.checked ? 'block' : 'none';
    });
}

function setupPreviewUpdates() {
    const inputs = ['pageTitle', 'pageDescription', 'pageUrl', 'ogTitle', 'ogDescription', 'ogImage', 'twitterTitle', 'twitterDescription', 'twitterImage'];
    
    inputs.forEach(inputId => {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('input', updatePreview);
        }
    });
}

function updatePreview() {
    updateGooglePreview();
    updateFacebookPreview();
    updateTwitterPreview();
}

function updateGooglePreview() {
    const title = document.getElementById('pageTitle').value || 'Заголовок сторінки';
    const description = document.getElementById('pageDescription').value || 'Опис сторінки буде відображатися тут...';
    const url = document.getElementById('pageUrl').value || 'https://yoursite.com';
    
    document.querySelector('#googlePreview .preview-title').textContent = title;
    document.querySelector('#googlePreview .preview-description').textContent = description;
    document.querySelector('#googlePreview .preview-url').textContent = url;
}

function updateFacebookPreview() {
    const title = document.getElementById('ogTitle').value || document.getElementById('pageTitle').value || 'Заголовок';
    const description = document.getElementById('ogDescription').value || document.getElementById('pageDescription').value || 'Опис...';
    const url = document.getElementById('pageUrl').value || 'https://yoursite.com';
    const domain = url.replace(/https?:\/\//, '').split('/')[0].toUpperCase();
    
    document.querySelector('#facebookPreview .preview-title').textContent = title;
    document.querySelector('#facebookPreview .preview-description').textContent = description;
    document.querySelector('#facebookPreview .preview-domain').textContent = domain;
}

function updateTwitterPreview() {
    const title = document.getElementById('twitterTitle').value || document.getElementById('pageTitle').value || 'Заголовок';
    const description = document.getElementById('twitterDescription').value || document.getElementById('pageDescription').value || 'Опис...';
    const url = document.getElementById('pageUrl').value || 'https://yoursite.com';
    const domain = url.replace(/https?:\/\//, '').split('/')[0];
    
    document.querySelector('#twitterPreview .preview-title').textContent = title;
    document.querySelector('#twitterPreview .preview-description').textContent = description;
    document.querySelector('#twitterPreview .preview-domain').textContent = domain;
}

function generateMetaTags() {
    const form = document.getElementById('metaForm');
    const formData = new FormData(form);
    
    // Get form values
    const data = {};
    for (let [key, value] of formData.entries()) {
        data[key] = value;
    }
    
    // Get values from inputs directly
    data.pageTitle = document.getElementById('pageTitle').value;
    data.pageDescription = document.getElementById('pageDescription').value;
    data.pageKeywords = document.getElementById('pageKeywords').value;
    data.pageUrl = document.getElementById('pageUrl').value;
    
    // OG tags
    data.enableOG = document.getElementById('enableOG').checked;
    if (data.enableOG) {
        data.ogTitle = document.getElementById('ogTitle').value;
        data.ogDescription = document.getElementById('ogDescription').value;
        data.ogImage = document.getElementById('ogImage').value;
        data.ogType = document.getElementById('ogType').value;
        data.ogSiteName = document.getElementById('ogSiteName').value;
    }
    
    // Twitter tags
    data.enableTwitter = document.getElementById('enableTwitter').checked;
    if (data.enableTwitter) {
        data.twitterCard = document.getElementById('twitterCard').value;
        data.twitterSite = document.getElementById('twitterSite').value;
        data.twitterTitle = document.getElementById('twitterTitle').value;
        data.twitterDescription = document.getElementById('twitterDescription').value;
        data.twitterImage = document.getElementById('twitterImage').value;
    }
    
    // Additional tags
    data.enableAdditional = document.getElementById('enableAdditional').checked;
    if (data.enableAdditional) {
        data.robots = document.getElementById('robots').value;
        data.canonical = document.getElementById('canonical').value;
        data.author = document.getElementById('author').value;
        data.language = document.getElementById('language').value;
        data.viewport = document.getElementById('viewport').value;
    }
    
    // Generate HTML
    const html = generateHTML(data);
    
    // Display generated code
    displayGeneratedCode(html);
}

function generateHTML(data) {
    let html = '';
    
    // Basic meta tags
    html += '<!-- Basic SEO Meta Tags -->\n';
    if (data.pageTitle) {
        html += `<title>${escapeHtml(data.pageTitle)}</title>\n`;
    }
    if (data.pageDescription) {
        html += `<meta name="description" content="${escapeHtml(data.pageDescription)}">\n`;
    }
    if (data.pageKeywords) {
        html += `<meta name="keywords" content="${escapeHtml(data.pageKeywords)}">\n`;
    }
    
    // Additional meta tags
    if (data.enableAdditional) {
        html += '\n<!-- Additional Meta Tags -->\n';
        if (data.robots) {
            html += `<meta name="robots" content="${data.robots}">\n`;
        }
        if (data.author) {
            html += `<meta name="author" content="${escapeHtml(data.author)}">\n`;
        }
        if (data.language) {
            html += `<meta http-equiv="content-language" content="${data.language}">\n`;
        }
        if (data.viewport) {
            html += `<meta name="viewport" content="${data.viewport}">\n`;
        }
        if (data.canonical) {
            html += `<link rel="canonical" href="${data.canonical}">\n`;
        }
    }
    
    // Open Graph tags
    if (data.enableOG) {
        html += '\n<!-- Open Graph Meta Tags -->\n';
        html += `<meta property="og:type" content="${data.ogType || 'website'}">\n`;
        if (data.ogTitle || data.pageTitle) {
            html += `<meta property="og:title" content="${escapeHtml(data.ogTitle || data.pageTitle)}">\n`;
        }
        if (data.ogDescription || data.pageDescription) {
            html += `<meta property="og:description" content="${escapeHtml(data.ogDescription || data.pageDescription)}">\n`;
        }
        if (data.pageUrl) {
            html += `<meta property="og:url" content="${data.pageUrl}">\n`;
        }
        if (data.ogImage) {
            html += `<meta property="og:image" content="${data.ogImage}">\n`;
        }
        if (data.ogSiteName) {
            html += `<meta property="og:site_name" content="${escapeHtml(data.ogSiteName)}">\n`;
        }
    }
    
    // Twitter Card tags
    if (data.enableTwitter) {
        html += '\n<!-- Twitter Card Meta Tags -->\n';
        html += `<meta name="twitter:card" content="${data.twitterCard || 'summary'}">\n`;
        if (data.twitterSite) {
            html += `<meta name="twitter:site" content="${data.twitterSite}">\n`;
        }
        if (data.twitterTitle || data.pageTitle) {
            html += `<meta name="twitter:title" content="${escapeHtml(data.twitterTitle || data.pageTitle)}">\n`;
        }
        if (data.twitterDescription || data.pageDescription) {
            html += `<meta name="twitter:description" content="${escapeHtml(data.twitterDescription || data.pageDescription)}">\n`;
        }
        if (data.twitterImage) {
            html += `<meta name="twitter:image" content="${data.twitterImage}">\n`;
        }
    }
    
    return html;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function displayGeneratedCode(html) {
    const container = document.getElementById('generatedTags');
    
    // Syntax highlight
    const highlighted = syntaxHighlight(html);
    
    container.innerHTML = `<pre>${highlighted}</pre>`;
    
    // Scroll to top of code container
    container.scrollTop = 0;
}

function syntaxHighlight(html) {
    return html
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/(&lt;\/?)([\w-]+)/g, '$1<span class="code-tag">$2</span>')
        .replace(/([\w-]+)=/g, '<span class="code-attr">$1</span>=')
        .replace(/="([^"]*)"/g, '="<span class="code-value">$1</span>"')
        .replace(/(&lt;!--.*?--&gt;)/g, '<span class="code-comment">$1</span>');
}

function copyToClipboard() {
    const codeContainer = document.getElementById('generatedTags');
    const pre = codeContainer.querySelector('pre');
    
    if (!pre) {
        alert('Спочатку згенеруйте мета-теги');
        return;
    }
    
    // Get plain text content
    const text = pre.textContent || pre.innerText;
    
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            showNotification('Код скопійовано в буфер обміну!', 'success');
        }).catch(() => {
            fallbackCopyToClipboard(text);
        });
    } else {
        fallbackCopyToClipboard(text);
    }
}

function fallbackCopyToClipboard(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    document.body.appendChild(textArea);
    textArea.select();
    try {
        document.execCommand('copy');
        showNotification('Код скопійовано в буфер обміну!', 'success');
    } catch (err) {
        showNotification('Помилка копіювання. Спробуйте вручну.', 'error');
    }
    document.body.removeChild(textArea);
}

function downloadCode() {
    const codeContainer = document.getElementById('generatedTags');
    const pre = codeContainer.querySelector('pre');
    
    if (!pre) {
        alert('Спочатку згенеруйте мета-теги');
        return;
    }
    
    const text = pre.textContent || pre.innerText;
    const blob = new Blob([text], { type: 'text/html' });
    const url = window.URL.createObjectURL(blob);
    
    const a = document.createElement('a');
    a.href = url;
    a.download = 'meta-tags.html';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
    
    showNotification('Файл з мета-тегами завантажено!', 'success');
}

function clearForm() {
    if (confirm('Очистити всі поля форми?')) {
        document.getElementById('metaForm').reset();
        
        // Clear generated code
        document.getElementById('generatedTags').innerHTML = `
            <div class="text-center text-muted py-5">
                <i class="fas fa-code fa-3x mb-3"></i>
                <p>Заповніть форму зліва для генерації мета-тегів</p>
            </div>
        `;
        
        // Reset character counters
        document.querySelectorAll('.char-counter').forEach(counter => {
            counter.textContent = '0';
            counter.className = 'char-counter';
        });
        
        // Reset preview
        updatePreview();
        
        showNotification('Форму очищено', 'info');
    }
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close ms-auto" onclick="this.parentElement.remove()"></button>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}
</script>

<?php include '../../themes/footer.php'; ?>