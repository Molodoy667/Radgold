<?php
require_once '../core/config.php';
require_once '../core/functions.php';

// Перевірка авторизації
if (!isLoggedIn()) {
    header('Location: user/login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

$error = '';
$success = '';

// Обробка форми
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $adData = [
            'user_id' => $_SESSION['user_id'],
            'category_id' => (int)($_POST['category_id'] ?? 0),
            'location_id' => (int)($_POST['location_id'] ?? 0),
            'title' => sanitize($_POST['title'] ?? ''),
            'description' => sanitize($_POST['description'] ?? ''),
            'price' => !empty($_POST['price']) ? (float)$_POST['price'] : null,
            'currency' => sanitize($_POST['currency'] ?? 'UAH'),
            'contact_name' => sanitize($_POST['contact_name'] ?? ''),
            'contact_phone' => sanitize($_POST['contact_phone'] ?? ''),
            'contact_email' => sanitize($_POST['contact_email'] ?? ''),
            'address' => sanitize($_POST['address'] ?? ''),
            'condition_type' => sanitize($_POST['condition_type'] ?? 'used')
        ];
        
        // Валідація
        $validation = validateAdData($adData);
        if (!$validation['valid']) {
            throw new Exception($validation['message']);
        }
        
        // Створення оголошення
        $adId = createAd($adData);
        
        if ($adId) {
            // Збереження атрибутів
            if (!empty($_POST['attributes'])) {
                saveAdAttributes($adId, $_POST['attributes']);
            }
            
            // Завантаження зображень
            if (!empty($_FILES['images']['name'][0])) {
                $uploadResult = uploadAdImages($adId, $_FILES['images']);
                if (!$uploadResult['success']) {
                    throw new Exception('Помилка завантаження зображень: ' . $uploadResult['message']);
                }
            }
            
            $success = 'Оголошення успішно створено! Воно буде опубліковано після модерації.';
            
            // Перенаправлення через 3 секунди
            header("refresh:3;url=user/dashboard.php");
        } else {
            throw new Exception('Помилка створення оголошення');
        }
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Отримання даних для форми
$categories = getCategories();
$locations = getLocations();
$selectedCategory = (int)($_GET['category'] ?? 0);
$categoryAttributes = $selectedCategory ? getCategoryAttributes($selectedCategory) : [];

include '../themes/header.php';
?>

<div class="create-ad-page">
    <!-- Page Header -->
    <section class="page-header py-5">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1 class="page-title">
                        <i class="fas fa-plus-circle me-3"></i>Створити оголошення
                    </h1>
                    <p class="page-subtitle">Заповніть форму щоб розмістити ваше оголошення</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Create Form -->
    <section class="create-form py-5">
        <div class="container">
            <?php if ($error): ?>
                <div class="alert alert-danger animate__animated animate__shakeX">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success animate__animated animate__bounceIn">
                    <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                    <div class="mt-2">
                        <a href="user/dashboard.php" class="btn btn-sm btn-outline-success">
                            <i class="fas fa-tachometer-alt me-1"></i>Перейти в кабінет
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" id="createAdForm" class="ad-form">
                <div class="row">
                    <!-- Left Column -->
                    <div class="col-lg-8">
                        <!-- Basic Information -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-info-circle me-2"></i>Основна інформація
                            </h3>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="category_id" class="form-label">Категорія *</label>
                                        <select class="form-select" id="category_id" name="category_id" required onchange="loadCategoryAttributes()">
                                            <option value="">Оберіть категорію</option>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo $category['id']; ?>" 
                                                        <?php echo $selectedCategory == $category['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($category['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">Оберіть категорію</div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="location_id" class="form-label">Місто *</label>
                                        <select class="form-select" id="location_id" name="location_id" required>
                                            <option value="">Оберіть місто</option>
                                            <?php foreach ($locations as $location): ?>
                                                <option value="<?php echo $location['id']; ?>">
                                                    <?php echo htmlspecialchars($location['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">Оберіть місто</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="title" class="form-label">Назва оголошення *</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="title" 
                                       name="title" 
                                       placeholder="Наприклад: iPhone 12 Pro 256GB"
                                       maxlength="255"
                                       required>
                                <div class="char-counter">
                                    <span id="titleCounter">0</span>/255 символів
                                </div>
                                <div class="invalid-feedback">Введіть назву оголошення</div>
                            </div>
                            
                            <div class="form-group mb-4">
                                <label for="description" class="form-label">Опис *</label>
                                <textarea class="form-control" 
                                          id="description" 
                                          name="description" 
                                          rows="6"
                                          placeholder="Детальний опис товару або послуги..."
                                          required></textarea>
                                <div class="char-counter">
                                    <span id="descCounter">0</span>/2000 символів
                                </div>
                                <div class="invalid-feedback">Введіть опис</div>
                            </div>
                        </div>

                        <!-- Price and Condition -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-dollar-sign me-2"></i>Ціна та стан
                            </h3>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="price" class="form-label">Ціна</label>
                                        <div class="input-group">
                                            <input type="number" 
                                                   class="form-control" 
                                                   id="price" 
                                                   name="price" 
                                                   placeholder="0.00"
                                                   min="0"
                                                   step="0.01">
                                            <select class="form-select" name="currency" style="max-width: 100px;">
                                                <option value="UAH">UAH</option>
                                                <option value="USD">USD</option>
                                                <option value="EUR">EUR</option>
                                            </select>
                                        </div>
                                        <small class="form-text text-muted">Залиште порожнім якщо ціна договірна</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="condition_type" class="form-label">Стан</label>
                                        <select class="form-select" id="condition_type" name="condition_type">
                                            <option value="new">Новий</option>
                                            <option value="used" selected>Вживаний</option>
                                            <option value="refurbished">Відновлений</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Category Attributes -->
                        <div class="form-section" id="attributesSection" style="display: none;">
                            <h3 class="section-title">
                                <i class="fas fa-list me-2"></i>Характеристики
                            </h3>
                            <div id="attributesContainer"></div>
                        </div>

                        <!-- Images -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-images me-2"></i>Зображення
                            </h3>
                            
                            <div class="image-upload-area" id="imageUploadArea">
                                <input type="file" 
                                       id="images" 
                                       name="images[]" 
                                       accept="image/*" 
                                       multiple 
                                       style="display: none;"
                                       onchange="handleImageUpload(event)">
                                
                                <div class="upload-placeholder" onclick="document.getElementById('images').click()">
                                    <i class="fas fa-cloud-upload-alt fa-3x mb-3"></i>
                                    <p>Натисніть або перетягніть зображення сюди</p>
                                    <small class="text-muted">Максимум 10 зображень, до 5МБ кожне</small>
                                </div>
                                
                                <div class="image-preview-container" id="imagePreviewContainer" style="display: none;">
                                    <div class="row" id="imagePreviewGrid"></div>
                                    <button type="button" class="btn btn-outline-primary mt-3" onclick="document.getElementById('images').click()">
                                        <i class="fas fa-plus me-2"></i>Додати ще зображення
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-lg-4">
                        <!-- Contact Information -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-address-book me-2"></i>Контактна інформація
                            </h3>
                            
                            <div class="form-group mb-3">
                                <label for="contact_name" class="form-label">Ім'я *</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="contact_name" 
                                       name="contact_name" 
                                       value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>"
                                       required>
                                <div class="invalid-feedback">Введіть ваше ім'я</div>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="contact_phone" class="form-label">Телефон *</label>
                                <input type="tel" 
                                       class="form-control" 
                                       id="contact_phone" 
                                       name="contact_phone" 
                                       placeholder="+380 XX XXX XX XX"
                                       required>
                                <div class="invalid-feedback">Введіть номер телефону</div>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="contact_email" class="form-label">Email</label>
                                <input type="email" 
                                       class="form-control" 
                                       id="contact_email" 
                                       name="contact_email" 
                                       value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>"
                                       placeholder="email@example.com">
                            </div>
                            
                            <div class="form-group mb-4">
                                <label for="address" class="form-label">Адреса</label>
                                <textarea class="form-control" 
                                          id="address" 
                                          name="address" 
                                          rows="3"
                                          placeholder="Вкажіть точну адресу для зустрічі"></textarea>
                            </div>
                        </div>

                        <!-- Publishing Options -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-rocket me-2"></i>Опції публікації
                            </h3>
                            
                            <div class="publishing-options">
                                <div class="option-card">
                                    <div class="option-header">
                                        <h5>Звичайне розміщення</h5>
                                        <span class="price">Безкоштовно</span>
                                    </div>
                                    <ul class="option-features">
                                        <li>✓ Стандартне розміщення</li>
                                        <li>✓ Активне 30 днів</li>
                                        <li>✓ Базова статистика</li>
                                    </ul>
                                </div>
                                
                                <div class="premium-options mt-3">
                                    <h6>Додаткові послуги:</h6>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="make_urgent" name="make_urgent">
                                        <label class="form-check-label" for="make_urgent">
                                            <i class="fas fa-exclamation text-danger me-1"></i>
                                            Термінове (30 грн за 3 дні)
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="make_featured" name="make_featured">
                                        <label class="form-check-label" for="make_featured">
                                            <i class="fas fa-star text-warning me-1"></i>
                                            Рекомендоване (150 грн за 7 днів)
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="highlight_ad" name="highlight_ad">
                                        <label class="form-check-label" for="highlight_ad">
                                            <i class="fas fa-highlighter text-info me-1"></i>
                                            Виділити кольором (50 грн за 7 днів)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="form-section">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                    <i class="fas fa-paper-plane me-2"></i>Опублікувати оголошення
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="saveDraft()">
                                    <i class="fas fa-save me-2"></i>Зберегти як чернетку
                                </button>
                            </div>
                            
                            <div class="form-note mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Оголошення буде розміщено після модерації, зазвичай протягом 2-24 годин
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

<style>
.create-ad-page {
    background: var(--theme-bg);
    min-height: 100vh;
}

.page-header {
    background: linear-gradient(135deg, var(--current-gradient));
    color: white;
}

.page-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.page-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
}

.form-section {
    background: var(--theme-bg-secondary);
    border-radius: 10px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.section-title {
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    color: var(--primary-color);
    border-bottom: 2px solid var(--primary-color);
    padding-bottom: 0.5rem;
}

.char-counter {
    text-align: right;
    font-size: 0.8rem;
    color: #666;
    margin-top: 0.25rem;
}

.image-upload-area {
    border: 2px dashed #ddd;
    border-radius: 10px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
}

.image-upload-area:hover {
    border-color: var(--primary-color);
    background: rgba(var(--primary-color), 0.05);
}

.upload-placeholder {
    cursor: pointer;
    color: #666;
}

.image-preview-item {
    position: relative;
    margin-bottom: 1rem;
}

.image-preview-item img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 8px;
}

.image-remove-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    background: rgba(220, 53, 69, 0.8);
    color: white;
    border: none;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.image-remove-btn:hover {
    background: #dc3545;
}

.main-image-badge {
    position: absolute;
    bottom: 5px;
    left: 5px;
    background: rgba(40, 167, 69, 0.9);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.7rem;
}

.option-card {
    border: 2px solid var(--theme-border);
    border-radius: 8px;
    padding: 1.5rem;
    background: var(--theme-bg);
}

.option-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.option-header h5 {
    margin: 0;
    font-weight: 600;
}

.price {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--primary-color);
}

.option-features {
    list-style: none;
    padding: 0;
    margin: 0;
}

.option-features li {
    padding: 0.25rem 0;
    color: #28a745;
}

.premium-options .form-check {
    margin-bottom: 0.75rem;
    padding: 0.5rem;
    background: rgba(var(--primary-color), 0.05);
    border-radius: 5px;
}

.form-note {
    text-align: center;
    padding: 1rem;
    background: rgba(var(--primary-color), 0.1);
    border-radius: 8px;
}

@media (max-width: 768px) {
    .page-title {
        font-size: 2rem;
    }
    
    .form-section {
        padding: 1.5rem;
    }
}
</style>

<script>
let uploadedImages = [];
let maxImages = 10;

document.addEventListener('DOMContentLoaded', function() {
    setupFormValidation();
    setupCharCounters();
    setupImageDragDrop();
    
    // Load category attributes if category is preselected
    if (document.getElementById('category_id').value) {
        loadCategoryAttributes();
    }
});

function setupFormValidation() {
    const form = document.getElementById('createAdForm');
    form.addEventListener('submit', function(e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add('was-validated');
    });
}

function setupCharCounters() {
    const titleInput = document.getElementById('title');
    const descInput = document.getElementById('description');
    
    titleInput.addEventListener('input', function() {
        updateCharCounter('titleCounter', this.value.length, 255);
    });
    
    descInput.addEventListener('input', function() {
        updateCharCounter('descCounter', this.value.length, 2000);
    });
}

function updateCharCounter(counterId, current, max) {
    const counter = document.getElementById(counterId);
    counter.textContent = current;
    
    if (current > max * 0.9) {
        counter.style.color = '#dc3545';
    } else if (current > max * 0.75) {
        counter.style.color = '#ffc107';
    } else {
        counter.style.color = '#666';
    }
}

function setupImageDragDrop() {
    const uploadArea = document.getElementById('imageUploadArea');
    
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.style.backgroundColor = 'rgba(var(--primary-color), 0.1)';
    });
    
    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.style.backgroundColor = '';
    });
    
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.style.backgroundColor = '';
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleImageFiles(files);
        }
    });
}

function handleImageUpload(event) {
    handleImageFiles(event.target.files);
}

function handleImageFiles(files) {
    const remainingSlots = maxImages - uploadedImages.length;
    const filesToProcess = Math.min(files.length, remainingSlots);
    
    for (let i = 0; i < filesToProcess; i++) {
        const file = files[i];
        
        if (file.type.startsWith('image/')) {
            if (file.size <= 5 * 1024 * 1024) { // 5MB limit
                addImagePreview(file);
            } else {
                showNotification('Файл ' + file.name + ' занадто великий (максимум 5МБ)', 'warning');
            }
        }
    }
    
    if (files.length > remainingSlots) {
        showNotification(`Можна завантажити максимум ${maxImages} зображень`, 'warning');
    }
}

function addImagePreview(file) {
    const reader = new FileReader();
    reader.onload = function(e) {
        const imageId = 'img_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        uploadedImages.push({
            id: imageId,
            file: file,
            url: e.target.result
        });
        
        updateImagePreview();
    };
    reader.readAsDataURL(file);
}

function updateImagePreview() {
    const container = document.getElementById('imagePreviewContainer');
    const grid = document.getElementById('imagePreviewGrid');
    const placeholder = document.querySelector('.upload-placeholder');
    
    if (uploadedImages.length > 0) {
        container.style.display = 'block';
        placeholder.style.display = 'none';
        
        grid.innerHTML = uploadedImages.map((img, index) => `
            <div class="col-6 col-md-4 col-lg-3">
                <div class="image-preview-item">
                    <img src="${img.url}" alt="Preview ${index + 1}">
                    <button type="button" class="image-remove-btn" onclick="removeImage('${img.id}')">
                        <i class="fas fa-times"></i>
                    </button>
                    ${index === 0 ? '<div class="main-image-badge">Головне фото</div>' : ''}
                </div>
            </div>
        `).join('');
    } else {
        container.style.display = 'none';
        placeholder.style.display = 'block';
    }
}

function removeImage(imageId) {
    uploadedImages = uploadedImages.filter(img => img.id !== imageId);
    updateImagePreview();
    updateFileInput();
}

function updateFileInput() {
    // Update the file input with current images
    const dt = new DataTransfer();
    uploadedImages.forEach(img => {
        dt.items.add(img.file);
    });
    document.getElementById('images').files = dt.files;
}

function loadCategoryAttributes() {
    const categoryId = document.getElementById('category_id').value;
    
    if (!categoryId) {
        document.getElementById('attributesSection').style.display = 'none';
        return;
    }
    
    fetch(`/ajax/get_category_attributes.php?category_id=${categoryId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.attributes.length > 0) {
                renderCategoryAttributes(data.attributes);
                document.getElementById('attributesSection').style.display = 'block';
            } else {
                document.getElementById('attributesSection').style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error loading attributes:', error);
        });
}

function renderCategoryAttributes(attributes) {
    const container = document.getElementById('attributesContainer');
    
    container.innerHTML = attributes.map(attr => {
        let inputHtml = '';
        
        switch (attr.type) {
            case 'text':
                inputHtml = `<input type="text" class="form-control" name="attributes[${attr.id}]" placeholder="${attr.name}">`;
                break;
            case 'number':
                inputHtml = `<input type="number" class="form-control" name="attributes[${attr.id}]" placeholder="${attr.name}">`;
                break;
            case 'select':
                const options = JSON.parse(attr.options || '[]');
                inputHtml = `
                    <select class="form-select" name="attributes[${attr.id}]">
                        <option value="">Оберіть ${attr.name.toLowerCase()}</option>
                        ${options.map(opt => `<option value="${opt}">${opt}</option>`).join('')}
                    </select>
                `;
                break;
            case 'textarea':
                inputHtml = `<textarea class="form-control" name="attributes[${attr.id}]" rows="3" placeholder="${attr.name}"></textarea>`;
                break;
        }
        
        return `
            <div class="col-md-6 mb-3">
                <label class="form-label">${attr.name} ${attr.is_required ? '*' : ''}</label>
                ${inputHtml}
            </div>
        `;
    }).join('');
    
    container.innerHTML = `<div class="row">${container.innerHTML}</div>`;
}

function saveDraft() {
    const formData = new FormData(document.getElementById('createAdForm'));
    formData.append('save_as_draft', '1');
    
    fetch('/ajax/save_ad_draft.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Чернетку збережено!', 'success');
        } else {
            showNotification(data.message, 'error');
        }
    });
}
</script>

<?php 
include '../themes/footer.php';

// Helper functions
function validateAdData($data) {
    if (empty($data['title'])) {
        return ['valid' => false, 'message' => 'Назва оголошення обов\'язкова'];
    }
    
    if (empty($data['description'])) {
        return ['valid' => false, 'message' => 'Опис обов\'язковий'];
    }
    
    if (empty($data['category_id'])) {
        return ['valid' => false, 'message' => 'Оберіть категорію'];
    }
    
    if (empty($data['location_id'])) {
        return ['valid' => false, 'message' => 'Оберіть місто'];
    }
    
    if (empty($data['contact_name'])) {
        return ['valid' => false, 'message' => 'Ім\'я контактної особи обов\'язкове'];
    }
    
    if (empty($data['contact_phone'])) {
        return ['valid' => false, 'message' => 'Номер телефону обов\'язковий'];
    }
    
    return ['valid' => true];
}

function createAd($data) {
    try {
        $db = new Database();
        
        // Generate slug
        $slug = generateSlug($data['title']);
        
        $stmt = $db->prepare("
            INSERT INTO ads (
                user_id, category_id, location_id, title, slug, description, 
                price, currency, contact_name, contact_phone, contact_email, 
                address, condition_type, status, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
        ");
        
        $stmt->bind_param("iiisssdssssss", 
            $data['user_id'], $data['category_id'], $data['location_id'],
            $data['title'], $slug, $data['description'], $data['price'],
            $data['currency'], $data['contact_name'], $data['contact_phone'],
            $data['contact_email'], $data['address'], $data['condition_type']
        );
        
        if ($stmt->execute()) {
            return $db->insert_id;
        }
        
        return false;
    } catch (Exception $e) {
        return false;
    }
}

function generateSlug($title) {
    $slug = strtolower(trim($title));
    $slug = preg_replace('/[^a-z0-9\s\-]/', '', $slug);
    $slug = preg_replace('/[\s\-]+/', '-', $slug);
    return trim($slug, '-');
}

function getCategoryAttributes($categoryId) {
    try {
        $db = new Database();
        $stmt = $db->prepare("
            SELECT * FROM category_attributes 
            WHERE category_id = ? 
            ORDER BY sort_order ASC
        ");
        $stmt->bind_param("i", $categoryId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function saveAdAttributes($adId, $attributes) {
    try {
        $db = new Database();
        
        foreach ($attributes as $attributeId => $value) {
            if (!empty($value)) {
                $stmt = $db->prepare("
                    INSERT INTO ad_attributes (ad_id, attribute_id, value) 
                    VALUES (?, ?, ?)
                ");
                $stmt->bind_param("iis", $adId, $attributeId, $value);
                $stmt->execute();
            }
        }
        
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function uploadAdImages($adId, $files) {
    try {
        $uploadDir = '../images/uploads/';
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        $db = new Database();
        $uploadedCount = 0;
        
        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $fileType = $files['type'][$i];
                $fileSize = $files['size'][$i];
                
                if (!in_array($fileType, $allowedTypes)) {
                    continue;
                }
                
                if ($fileSize > $maxSize) {
                    continue;
                }
                
                $extension = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
                $filename = uniqid('ad_' . $adId . '_') . '.' . $extension;
                $filepath = $uploadDir . $filename;
                
                if (move_uploaded_file($files['tmp_name'][$i], $filepath)) {
                    $isMain = ($uploadedCount === 0) ? 1 : 0;
                    
                    $stmt = $db->prepare("
                        INSERT INTO ad_images (ad_id, filename, original_name, file_size, mime_type, is_main, sort_order) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->bind_param("ississi", $adId, $filename, $files['name'][$i], $fileSize, $fileType, $isMain, $uploadedCount);
                    
                    if ($stmt->execute()) {
                        $uploadedCount++;
                    }
                }
            }
        }
        
        return ['success' => true, 'uploaded' => $uploadedCount];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}
?>