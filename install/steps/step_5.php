<?php
// Крок 5: Налаштування теми оформлення
$themeData = $_SESSION['install_data']['theme'] ?? [];
?>

<div class="step-content animate__animated animate__fadeIn">
    <div class="step-header">
        <h3><i class="fas fa-palette me-3"></i>Налаштування теми оформлення</h3>
        <p class="text-muted">Оберіть тему за замовчуванням та градієнт для оформлення</p>
    </div>

    <form method="POST" id="themeForm">
        <input type="hidden" name="step" value="5">
        
        <!-- Вибір теми -->
        <div class="mb-5">
            <h5 class="mb-4">
                <i class="fas fa-moon me-2"></i>Тема за замовчуванням
            </h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="theme-option" data-theme="light">
                        <input type="radio" 
                               id="theme_light" 
                               name="default_theme" 
                               value="light" 
                               <?php echo ($themeData['default_theme'] ?? 'light') === 'light' ? 'checked' : ''; ?>>
                        <label for="theme_light" class="theme-card">
                            <div class="theme-preview light-preview">
                                <div class="preview-header"></div>
                                <div class="preview-content">
                                    <div class="preview-sidebar"></div>
                                    <div class="preview-main">
                                        <div class="preview-block"></div>
                                        <div class="preview-block"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="theme-info">
                                <h6><i class="fas fa-sun me-2"></i>Світла тема</h6>
                                <p class="mb-0">Класичний світлий дизайн</p>
                            </div>
                        </label>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="theme-option" data-theme="dark">
                        <input type="radio" 
                               id="theme_dark" 
                               name="default_theme" 
                               value="dark" 
                               <?php echo ($themeData['default_theme'] ?? 'light') === 'dark' ? 'checked' : ''; ?>>
                        <label for="theme_dark" class="theme-card">
                            <div class="theme-preview dark-preview">
                                <div class="preview-header"></div>
                                <div class="preview-content">
                                    <div class="preview-sidebar"></div>
                                    <div class="preview-main">
                                        <div class="preview-block"></div>
                                        <div class="preview-block"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="theme-info">
                                <h6><i class="fas fa-moon me-2"></i>Темна тема</h6>
                                <p class="mb-0">Сучасний темний дизайн</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Вибір градієнту -->
        <div class="mb-5">
            <h5 class="mb-4">
                <i class="fas fa-brush me-2"></i>Градієнт за замовчуванням
            </h5>
            <div class="gradient-picker">
                <input type="hidden" 
                       id="selected_gradient" 
                       name="default_gradient" 
                       value="<?php echo htmlspecialchars($themeData['default_gradient'] ?? 'gradient-1'); ?>">
                
                <div class="gradient-grid">
                    <?php
                    $gradients = [
                        'gradient-1' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                        'gradient-2' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                        'gradient-3' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                        'gradient-4' => 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
                        'gradient-5' => 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
                        'gradient-6' => 'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)',
                        'gradient-7' => 'linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%)',
                        'gradient-8' => 'linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%)',
                        'gradient-9' => 'linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%)',
                        'gradient-10' => 'linear-gradient(135deg, #fad0c4 0%, #ffd1ff 100%)',
                        'gradient-11' => 'linear-gradient(135deg, #ff8a80 0%, #ff80ab 100%)',
                        'gradient-12' => 'linear-gradient(135deg, #81c784 0%, #aed581 100%)',
                        'gradient-13' => 'linear-gradient(135deg, #64b5f6 0%, #42a5f5 100%)',
                        'gradient-14' => 'linear-gradient(135deg, #ffb74d 0%, #ffa726 100%)',
                        'gradient-15' => 'linear-gradient(135deg, #9575cd 0%, #7986cb 100%)',
                        'gradient-16' => 'linear-gradient(135deg, #4db6ac 0%, #26a69a 100%)',
                        'gradient-17' => 'linear-gradient(135deg, #f06292 0%, #ec407a 100%)',
                        'gradient-18' => 'linear-gradient(135deg, #ab47bc 0%, #8e24aa 100%)',
                        'gradient-19' => 'linear-gradient(135deg, #5c6bc0 0%, #3f51b5 100%)',
                        'gradient-20' => 'linear-gradient(135deg, #26c6da 0%, #00acc1 100%)',
                        'gradient-21' => 'linear-gradient(135deg, #66bb6a 0%, #4caf50 100%)',
                        'gradient-22' => 'linear-gradient(135deg, #ffca28 0%, #ffc107 100%)',
                        'gradient-23' => 'linear-gradient(135deg, #ff7043 0%, #ff5722 100%)',
                        'gradient-24' => 'linear-gradient(135deg, #8d6e63 0%, #795548 100%)',
                        'gradient-25' => 'linear-gradient(135deg, #78909c 0%, #607d8b 100%)',
                        'gradient-26' => 'linear-gradient(135deg, #e91e63 0%, #ad1457 100%)',
                        'gradient-27' => 'linear-gradient(135deg, #673ab7 0%, #512da8 100%)',
                        'gradient-28' => 'linear-gradient(135deg, #3f51b5 0%, #303f9f 100%)',
                        'gradient-29' => 'linear-gradient(135deg, #009688 0%, #00695c 100%)',
                        'gradient-30' => 'linear-gradient(135deg, #ff9800 0%, #f57c00 100%)'
                    ];
                    
                    $selectedGradient = $themeData['default_gradient'] ?? 'gradient-1';
                    
                    foreach ($gradients as $key => $gradient):
                    ?>
                        <div class="gradient-item <?php echo $key === $selectedGradient ? 'selected' : ''; ?>" 
                             data-gradient="<?php echo $key; ?>" 
                             style="background: <?php echo $gradient; ?>"
                             title="<?php echo ucfirst(str_replace('-', ' ', $key)); ?>">
                            <div class="gradient-check">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="gradient-preview mt-4">
                <h6>Попередній перегляд:</h6>
                <div class="preview-elements">
                    <button type="button" class="btn btn-gradient preview-btn">Кнопка</button>
                    <div class="preview-header-gradient">Заголовок</div>
                    <div class="preview-card-gradient">
                        <h6>Карточка</h6>
                        <p class="mb-0">Приклад контенту з градієнтом</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Додаткові налаштування -->
        <div class="mb-5">
            <h5 class="mb-4">
                <i class="fas fa-cogs me-2"></i>Додаткові налаштування
            </h5>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="enable_animations" 
                               name="enable_animations" 
                               <?php echo ($themeData['enable_animations'] ?? true) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="enable_animations">
                            <i class="fas fa-magic me-2"></i>Увімкнути анімації
                        </label>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="enable_particles" 
                               name="enable_particles" 
                               <?php echo ($themeData['enable_particles'] ?? false) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="enable_particles">
                            <i class="fas fa-sparkles me-2"></i>Частинки на фоні
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="smooth_scroll" 
                               name="smooth_scroll" 
                               <?php echo ($themeData['smooth_scroll'] ?? true) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="smooth_scroll">
                            <i class="fas fa-arrows-alt-v me-2"></i>Плавна прокрутка
                        </label>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="enable_tooltips" 
                               name="enable_tooltips" 
                               <?php echo ($themeData['enable_tooltips'] ?? true) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="enable_tooltips">
                            <i class="fas fa-question-circle me-2"></i>Підказки
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="info-box">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Примітка:</strong> Всі налаштування теми можна буде змінити в розділі "Тема та дизайн" адміністративної панелі.
        </div>

        <div class="navigation-buttons">
            <a href="?step=4" class="btn btn-outline-secondary">
                <i class="fas fa-chevron-left me-2"></i>Назад
            </a>
            <button type="submit" class="btn btn-primary">
                Далі<i class="fas fa-chevron-right ms-2"></i>
            </button>
        </div>
    </form>
</div>

<style>
.theme-option {
    margin-bottom: 20px;
}

.theme-card {
    display: block;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
    text-decoration: none;
    color: inherit;
}

.theme-card:hover {
    border-color: var(--primary-color, #667eea);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.theme-option input[type="radio"]:checked + .theme-card {
    border-color: var(--primary-color, #667eea);
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
}

.theme-preview {
    width: 100%;
    height: 120px;
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 15px;
    border: 1px solid #dee2e6;
}

.light-preview {
    background: #f8f9fa;
}

.dark-preview {
    background: #343a40;
}

.preview-header {
    height: 25px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.preview-content {
    display: flex;
    height: 95px;
}

.preview-sidebar {
    width: 30%;
    background: #e9ecef;
}

.dark-preview .preview-sidebar {
    background: #495057;
}

.preview-main {
    flex: 1;
    padding: 8px;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.preview-block {
    height: 20px;
    background: #dee2e6;
    border-radius: 4px;
}

.dark-preview .preview-block {
    background: #6c757d;
}

.theme-info h6 {
    margin-bottom: 5px;
    color: #495057;
}

.theme-info p {
    color: #6c757d;
    font-size: 0.9em;
}

.gradient-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(50px, 1fr));
    gap: 10px;
    max-width: 600px;
}

.gradient-item {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    cursor: pointer;
    position: relative;
    transition: all 0.3s ease;
    border: 3px solid transparent;
}

.gradient-item:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.gradient-item.selected {
    border-color: #fff;
    box-shadow: 0 0 0 2px var(--primary-color, #667eea);
    transform: scale(1.05);
}

.gradient-check {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 18px;
    opacity: 0;
    transition: all 0.3s ease;
    text-shadow: 0 1px 3px rgba(0,0,0,0.5);
}

.gradient-item.selected .gradient-check {
    opacity: 1;
}

.preview-elements {
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
}

.preview-btn {
    background: var(--current-gradient, linear-gradient(135deg, #667eea 0%, #764ba2 100%));
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
}

.preview-header-gradient {
    padding: 8px 16px;
    background: var(--current-gradient, linear-gradient(135deg, #667eea 0%, #764ba2 100%));
    color: white;
    border-radius: 6px;
    font-weight: 600;
}

.preview-card-gradient {
    padding: 15px;
    border-radius: 8px;
    border: 2px solid transparent;
    background: linear-gradient(white, white) padding-box,
                var(--current-gradient, linear-gradient(135deg, #667eea 0%, #764ba2 100%)) border-box;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const gradientItems = document.querySelectorAll('.gradient-item');
    const selectedGradientInput = document.getElementById('selected_gradient');
    
    // Градієнти
    const gradients = {
        'gradient-1': 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        'gradient-2': 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
        'gradient-3': 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
        'gradient-4': 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
        'gradient-5': 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
        'gradient-6': 'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)',
        'gradient-7': 'linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%)',
        'gradient-8': 'linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%)',
        'gradient-9': 'linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%)',
        'gradient-10': 'linear-gradient(135deg, #fad0c4 0%, #ffd1ff 100%)',
        'gradient-11': 'linear-gradient(135deg, #ff8a80 0%, #ff80ab 100%)',
        'gradient-12': 'linear-gradient(135deg, #81c784 0%, #aed581 100%)',
        'gradient-13': 'linear-gradient(135deg, #64b5f6 0%, #42a5f5 100%)',
        'gradient-14': 'linear-gradient(135deg, #ffb74d 0%, #ffa726 100%)',
        'gradient-15': 'linear-gradient(135deg, #9575cd 0%, #7986cb 100%)',
        'gradient-16': 'linear-gradient(135deg, #4db6ac 0%, #26a69a 100%)',
        'gradient-17': 'linear-gradient(135deg, #f06292 0%, #ec407a 100%)',
        'gradient-18': 'linear-gradient(135deg, #ab47bc 0%, #8e24aa 100%)',
        'gradient-19': 'linear-gradient(135deg, #5c6bc0 0%, #3f51b5 100%)',
        'gradient-20': 'linear-gradient(135deg, #26c6da 0%, #00acc1 100%)',
        'gradient-21': 'linear-gradient(135deg, #66bb6a 0%, #4caf50 100%)',
        'gradient-22': 'linear-gradient(135deg, #ffca28 0%, #ffc107 100%)',
        'gradient-23': 'linear-gradient(135deg, #ff7043 0%, #ff5722 100%)',
        'gradient-24': 'linear-gradient(135deg, #8d6e63 0%, #795548 100%)',
        'gradient-25': 'linear-gradient(135deg, #78909c 0%, #607d8b 100%)',
        'gradient-26': 'linear-gradient(135deg, #e91e63 0%, #ad1457 100%)',
        'gradient-27': 'linear-gradient(135deg, #673ab7 0%, #512da8 100%)',
        'gradient-28': 'linear-gradient(135deg, #3f51b5 0%, #303f9f 100%)',
        'gradient-29': 'linear-gradient(135deg, #009688 0%, #00695c 100%)',
        'gradient-30': 'linear-gradient(135deg, #ff9800 0%, #f57c00 100%)'
    };
    
    // Обробка вибору градієнту
    gradientItems.forEach(item => {
        item.addEventListener('click', function() {
            // Видаляємо вибір з усіх елементів
            gradientItems.forEach(g => g.classList.remove('selected'));
            
            // Додаємо вибір до поточного
            this.classList.add('selected');
            
            // Оновлюємо приховане поле
            const gradientKey = this.dataset.gradient;
            selectedGradientInput.value = gradientKey;
            
            // Оновлюємо попередній перегляд
            updateGradientPreview(gradients[gradientKey]);
        });
    });
    
    // Функція оновлення попереднього перегляду
    function updateGradientPreview(gradient) {
        document.documentElement.style.setProperty('--current-gradient', gradient);
    }
    
    // Ініціалізація попереднього перегляду
    const currentGradient = selectedGradientInput.value;
    if (gradients[currentGradient]) {
        updateGradientPreview(gradients[currentGradient]);
    }
    
    // Скриття радіо кнопок
    const radioInputs = document.querySelectorAll('input[type="radio"]');
    radioInputs.forEach(input => {
        input.style.display = 'none';
    });
});
</script>