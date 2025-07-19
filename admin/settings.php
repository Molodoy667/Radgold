<?php
// Устанавливаем заголовок страницы
$page_title = 'Налаштування';

// Подключаем header
require_once 'includes/header.php';

// Отримуємо всі налаштування
$all_settings = [];
$settings_query = "SELECT setting_key, setting_value FROM settings";
$settings_stmt = $db->prepare($settings_query);
$settings_stmt->execute();
while ($row = $settings_stmt->fetch(PDO::FETCH_ASSOC)) {
    $all_settings[$row['setting_key']] = $row['setting_value'];
}
?>

<!-- Color Picker CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css">

<!-- Page-specific styles -->
<style>
    .settings-container {
        padding: 1rem;
    }
    
    .settings-block {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 15px;
        margin-bottom: 1.5rem;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .settings-block:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow);
    }
    
    .block-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .block-header:hover {
        background: var(--surface-color);
    }
    
    .block-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        margin-right: 1rem;
    }
    
    .general-icon { background: var(--theme-gradient); }
    .security-icon { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .email-icon { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
    .theme-icon { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    .seo-icon { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
    .social-icon { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
    
    .block-info {
        flex: 1;
    }
    
    .block-info h5 {
        margin: 0 0 0.25rem 0;
        color: var(--text-color);
    }
    
    .block-info p {
        margin: 0;
        color: var(--text-muted);
        font-size: 0.9rem;
    }
    
    .block-chevron {
        color: var(--text-muted);
        transition: transform 0.3s ease;
    }
    
    .block-content {
        padding: 0 1.5rem;
        max-height: 0;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .settings-block.active .block-content {
        max-height: 2000px;
        padding: 1.5rem;
    }
    
    .settings-block.active .block-chevron {
        transform: rotate(180deg);
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .btn-back {
        background: var(--surface-color);
        border: 1px solid var(--border-color);
        color: var(--text-color);
        padding: 0.5rem 1rem;
        border-radius: 10px;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
    }
    
    .btn-back:hover {
        background: var(--theme-primary);
        border-color: var(--theme-primary);
        color: white;
        text-decoration: none;
    }
    
    .save-btn {
        background: var(--theme-gradient);
        border: none;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        font-weight: 500;
    }
    
    .save-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(var(--theme-primary-rgb), 0.4);
        color: white;
    }
    
    .upload-zone {
        border: 2px dashed var(--border-color);
        border-radius: 10px;
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
        background: var(--surface-color);
    }
    
    .upload-zone:hover {
        border-color: var(--theme-primary);
        background: var(--card-bg);
    }
    
    .upload-zone.dragover {
        border-color: var(--theme-primary);
        background: rgba(var(--theme-primary-rgb), 0.1);
    }
    
    .switch-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem;
        background: var(--surface-color);
        border-radius: 10px;
        margin-bottom: 1rem;
    }
    
    .switch-container:hover {
        background: var(--card-bg);
    }
    
    .custom-switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 30px;
    }
    
    .custom-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: var(--border-color);
        transition: .4s;
        border-radius: 30px;
    }
    
    .slider:before {
        position: absolute;
        content: "";
        height: 22px;
        width: 22px;
        left: 4px;
        bottom: 4px;
        background: white;
        transition: .4s;
        border-radius: 50%;
    }
    
    input:checked + .slider {
        background: var(--theme-gradient);
    }
    
    input:checked + .slider:before {
        transform: translateX(30px);
    }
</style>

<!-- Page Content -->
<div class="settings-container">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="mb-1">
                            <i class="fas fa-cogs me-2 text-gradient"></i>
                            Генеральні налаштування
                        </h2>
                        <p class="text-muted mb-0">Налаштування загальних параметрів сайту</p>
                    </div>
                    <a href="dashboard.php" class="btn-back">
                        <i class="fas fa-arrow-left me-2"></i>До дашборду
                    </a>
                </div>

                <!-- Основні налаштування -->
                <div class="settings-block" onclick="toggleBlock(this)">
                    <div class="block-header">
                        <div class="block-icon general-icon">
                            <i class="fas fa-globe"></i>
                        </div>
                        <div class="block-info">
                            <h5>Основні налаштування</h5>
                            <p>Назва сайту, опис, мова та інші базові параметри</p>
                        </div>
                        <i class="fas fa-chevron-down block-chevron"></i>
                    </div>
                    <div class="block-content">
                        <form id="generalForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Назва сайту</label>
                                        <input type="text" class="form-control" name="site_name" 
                                               value="<?php echo htmlspecialchars($all_settings['site_name'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Мова сайту</label>
                                        <select class="form-select" name="site_language">
                                            <option value="uk" <?php echo ($all_settings['site_language'] ?? '') === 'uk' ? 'selected' : ''; ?>>Українська</option>
                                            <option value="ru" <?php echo ($all_settings['site_language'] ?? '') === 'ru' ? 'selected' : ''; ?>>Русский</option>
                                            <option value="en" <?php echo ($all_settings['site_language'] ?? '') === 'en' ? 'selected' : ''; ?>>English</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Опис сайту</label>
                                <textarea class="form-control" name="site_description" rows="3"><?php echo htmlspecialchars($all_settings['site_description'] ?? ''); ?></textarea>
                            </div>
                            <div class="text-end">
                                <button type="button" class="save-btn" onclick="saveSettingsCustom('generalForm', 'general')">
                                    <i class="fas fa-save me-1"></i>Зберегти
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Налаштування тем -->
                <div class="settings-block" onclick="toggleBlock(this)">
                    <div class="block-header">
                        <div class="block-icon theme-icon">
                            <i class="fas fa-paint-brush"></i>
                        </div>
                        <div class="block-info">
                            <h5>Налаштування тем</h5>
                            <p>Оформлення сайту, кольорова схема та візуальні налаштування</p>
                        </div>
                        <i class="fas fa-chevron-down block-chevron"></i>
                    </div>
                    <div class="block-content">
                        <form id="themeForm">
                            <div class="switch-container">
                                <div>
                                    <h6 class="mb-1">Темний режим за замовчуванням</h6>
                                    <small class="text-muted">Включити темний режим для нових відвідувачів</small>
                                </div>
                                <label class="custom-switch">
                                    <input type="checkbox" name="default_dark_mode" 
                                           <?php echo ($all_settings['default_dark_mode'] ?? false) ? 'checked' : ''; ?>>
                                    <span class="slider"></span>
                                </label>
                            </div>
                            
                            <div class="switch-container">
                                <div>
                                    <h6 class="mb-1">Дозволити зміну тем</h6>
                                    <small class="text-muted">Дозволити користувачам змінювати теми оформлення</small>
                                </div>
                                <label class="custom-switch">
                                    <input type="checkbox" name="enable_theme_switcher" 
                                           <?php echo ($all_settings['enable_theme_switcher'] ?? true) ? 'checked' : ''; ?>>
                                    <span class="slider"></span>
                                </label>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Градієнт за замовчуванням</label>
                                <select class="form-select" name="default_theme_gradient">
                                    <?php 
                                    $gradients = [
                                        'gradient-1' => 'Фіолетово-синій',
                                        'gradient-2' => 'Рожево-червоний',
                                        'gradient-3' => 'Блакитний',
                                        'gradient-4' => 'Зелено-м\'ятний',
                                        'gradient-5' => 'Рожево-жовтий'
                                    ];
                                    $selected_gradient = $all_settings['default_theme_gradient'] ?? 'gradient-2';
                                    foreach ($gradients as $key => $name):
                                    ?>
                                        <option value="<?php echo $key; ?>" <?php echo $selected_gradient === $key ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="text-end">
                                <button type="button" class="save-btn" onclick="saveSettingsCustom('themeForm', 'theme')">
                                    <i class="fas fa-save me-1"></i>Зберегти
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Налаштування безпеки -->
                <div class="settings-block" onclick="toggleBlock(this)">
                    <div class="block-header">
                        <div class="block-icon security-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="block-info">
                            <h5>Налаштування безпеки</h5>
                            <p>Реєстрація, модерація та інші параметри безпеки</p>
                        </div>
                        <i class="fas fa-chevron-down block-chevron"></i>
                    </div>
                    <div class="block-content">
                        <form id="securityForm">
                            <div class="switch-container">
                                <div>
                                    <h6 class="mb-1">Дозволити реєстрацію</h6>
                                    <small class="text-muted">Нові користувачі можуть реєструватися на сайті</small>
                                </div>
                                <label class="custom-switch">
                                    <input type="checkbox" name="registration_enabled" 
                                           <?php echo ($all_settings['registration_enabled'] ?? true) ? 'checked' : ''; ?>>
                                    <span class="slider"></span>
                                </label>
                            </div>
                            
                            <div class="switch-container">
                                <div>
                                    <h6 class="mb-1">Модерація оголошень</h6>
                                    <small class="text-muted">Нові оголошення потребують схвалення адміністратора</small>
                                </div>
                                <label class="custom-switch">
                                    <input type="checkbox" name="ads_moderation" 
                                           <?php echo ($all_settings['ads_moderation'] ?? false) ? 'checked' : ''; ?>>
                                    <span class="slider"></span>
                                </label>
                            </div>
                            
                            <div class="text-end">
                                <button type="button" class="save-btn" onclick="saveSettingsCustom('securityForm', 'security')">
                                    <i class="fas fa-save me-1"></i>Зберегти
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Color Picker JS -->
<script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/pickr.min.js"></script>

<!-- Page Scripts -->
<script>
    // Функции для работы с блоками настроек
    function toggleBlock(block) {
        const isActive = block.classList.contains('active');
        
        // Закрываем все блоки
        document.querySelectorAll('.settings-block').forEach(b => {
            b.classList.remove('active');
        });
        
        // Открываем текущий блок если он не был активен
        if (!isActive) {
            block.classList.add('active');
        }
    }
    
    // Функция для сохранения настроек
    function saveSettingsCustom(formId, section) {
        const form = document.getElementById(formId);
        const formData = new FormData(form);
        formData.append('section', section);
        
        fetch('ajax/save_settings.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Налаштування збережено!', 'success');
                
                // Если изменились настройки темы, обновляем интерфейс
                if (section === 'theme') {
                    setTimeout(() => location.reload(), 1000);
                }
            } else {
                showToast(data.message || 'Помилка збереження налаштувань', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Помилка з\'єднання з сервером', 'error');
        });
    }
    
    // Инициализация при загрузке страницы
    document.addEventListener('DOMContentLoaded', function() {
        // Открываем первый блок по умолчанию
        const firstBlock = document.querySelector('.settings-block');
        if (firstBlock) {
            firstBlock.classList.add('active');
        }
    });
</script>

<?php require_once 'includes/footer.php'; ?>