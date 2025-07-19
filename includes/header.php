<?php
// Завантажуємо конфігурацію якщо ще не завантажена
if (!class_exists('Settings')) {
    require_once __DIR__ . '/../config/config.php';
}

// Отримуємо налаштування для META тегів
$page_title = isset($page_title) ? $page_title : null;
$page_description = isset($page_description) ? $page_description : null;
$page_keywords = isset($page_keywords) ? $page_keywords : null;

$meta_data = Settings::getMetaTags($page_title, $page_description, $page_keywords);
$site_name = Settings::get('site_name', 'Дошка Оголошень');
$logo_url = Settings::getLogoUrl();
$theme_color = Settings::get('theme_color', '#007bff');
?>
<!DOCTYPE html>
<html lang="<?php echo Settings::get('site_language', 'uk'); ?>">
<head>
    <?php echo $meta_data['meta_tags']; ?>
    
    <title><?php echo htmlspecialchars($meta_data['title']); ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AOS Animation -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/assets/css/style.css">
    
    <!-- Динамічні CSS теми -->
    <style>
        <?php echo Theme::generateCSS(); ?>
        
        .navbar-brand img {
            max-height: 40px;
            width: auto;
        }
        
        /* Стилі для панелі тем */
        .theme-panel {
            position: fixed;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            z-index: 1050;
        }
        
        .theme-toggle-btn {
            width: 60px;
            height: 120px;
            border-radius: 0 15px 15px 0;
            background: var(--theme-gradient);
            border: none;
            color: white;
            font-size: 22px;
            cursor: pointer;
            box-shadow: 2px 0 10px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            writing-mode: vertical-rl;
            text-orientation: mixed;
        }
        
        .theme-toggle-btn:hover {
            width: 70px;
            box-shadow: 3px 0 15px rgba(0,0,0,0.3);
        }
        
        .theme-toggle-btn:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(var(--theme-primary-rgb), 0.3);
        }
        
        .gradient-option {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            border: 3px solid transparent;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .gradient-option:hover {
            transform: scale(1.1);
        }
        
        .gradient-option.active {
            border-color: #fff;
            box-shadow: 0 0 0 2px var(--theme-primary);
        }
        
        .gradient-option.active::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-weight: bold;
            font-size: 18px;
            text-shadow: 0 0 3px rgba(0,0,0,0.5);
        }
        
        .theme-modal .modal-content {
            background: var(--card-bg);
            color: var(--text-color);
            border: 1px solid var(--border-color);
        }
        
        .theme-modal .modal-header {
            border-bottom: 1px solid var(--border-color);
        }
        
        .theme-modal .btn-close {
            filter: var(--theme-mode) == 'dark' ? invert(1) : none;
        }
        
        .default-logo-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--theme-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 12px;
            color: white;
            text-shadow: 0 1px 2px rgba(0,0,0,0.2);
        }
        
        .gradient-site-name {
            background: var(--theme-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
            text-shadow: none;
        }
        
        /* Fallback для браузерів без підтримки background-clip */
        @supports not (-webkit-background-clip: text) {
            .gradient-site-name {
                color: var(--theme-primary);
            }
        }
    </style>
    
    <?php echo $meta_data['analytics']; ?>
</head>
<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="<?php echo getBaseUrl(); ?>/index.php" data-spa data-page="home">
            <?php 
            $logo_path = Settings::get('site_logo', '');
            if (!empty($logo_path) && file_exists($logo_path)): 
            ?>
                <img src="<?php echo $logo_url; ?>" alt="<?php echo htmlspecialchars($site_name); ?>" class="me-2">
            <?php else: ?>
                <div class="default-logo-circle me-2">CMS</div>
            <?php endif; ?>
            <span class="gradient-site-name"><?php echo htmlspecialchars($site_name); ?></span>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo getBaseUrl(); ?>/index.php" data-spa data-page="home">
                        <i class="fas fa-home me-1"></i>Головна
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo getBaseUrl(); ?>/pages/categories.php" data-spa data-page="categories">
                        <i class="fas fa-list me-1"></i>Категорії
                    </a>
                </li>
                <?php if (Settings::get('enable_search', true)): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo getBaseUrl(); ?>/pages/search.php" data-spa data-page="search">
                        <i class="fas fa-search me-1"></i>Пошук
                    </a>
                </li>
                <?php endif; ?>
            </ul>
            
            <ul class="navbar-nav">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo getBaseUrl(); ?>/pages/profile.php" data-spa data-page="profile">
                                <i class="fas fa-user-circle me-1"></i>Мій профіль
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo getBaseUrl(); ?>/pages/my_ads.php" data-spa data-page="my_ads">
                                <i class="fas fa-list-ul me-1"></i>Мої оголошення
                            </a></li>
                            <?php if (Settings::get('enable_favorites', true)): ?>
                            <li><a class="dropdown-item" href="<?php echo getBaseUrl(); ?>/pages/favorites.php" data-spa data-page="favorites">
                                <i class="fas fa-heart me-1"></i>Вподобання
                            </a></li>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']): ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo getBaseUrl(); ?>/admin/dashboard.php">
                                <i class="fas fa-cogs me-1"></i>Адмін панель
                            </a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" onclick="handleLogout(); return false;">
                                <i class="fas fa-sign-out-alt me-1"></i>Вихід
                            </a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <?php if (Settings::get('registration_enabled', true)): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo getBaseUrl(); ?>/pages/login.php" data-spa data-page="login">
                            <i class="fas fa-sign-in-alt me-1"></i>Вхід
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo getBaseUrl(); ?>/pages/register.php" data-spa data-page="register">
                            <i class="fas fa-user-plus me-1"></i>Реєстрація
                        </a>
                    </li>
                    <?php endif; ?>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="btn btn-warning ms-2 text-dark fw-bold" href="<?php echo getBaseUrl(); ?>/pages/add_ad.php" data-spa data-page="add_ad">
                        <i class="fas fa-plus me-1"></i>Подати оголошення
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Alert Container for notifications -->
<div id="alert-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1055;"></div>

<!-- Theme Panel -->
<?php if (Theme::isThemeSwitcherEnabled()): ?>
<div class="theme-panel">
    <button class="theme-toggle-btn" data-bs-toggle="modal" data-bs-target="#themeModal" title="Налаштування теми">
        <i class="fas fa-palette"></i>
    </button>
</div>

<!-- Theme Modal -->
<div class="modal fade theme-modal" id="themeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-palette me-2"></i>Оформлення сторінки
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Dark Mode Toggle -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">Темний режим</h6>
                            <small class="text-muted">Зменшує навантаження на очі</small>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="darkModeSwitch" 
                                   <?php echo Theme::getCurrentTheme()['dark_mode'] ? 'checked' : ''; ?>>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <!-- Gradient Selection -->
                <div class="mb-3">
                    <h6 class="mb-3">Колірна тема</h6>
                    <div class="row g-2">
                        <?php 
                        $gradients = Theme::getGradients();
                        $current_gradient = Theme::getCurrentTheme()['gradient'];
                        foreach ($gradients as $key => $gradient): 
                        ?>
                            <div class="col-3">
                                <div class="gradient-option <?php echo $current_gradient === $key ? 'active' : ''; ?>" 
                                     data-gradient="<?php echo $key; ?>"
                                     style="background: linear-gradient(135deg, <?php echo $gradient[0]; ?> 0%, <?php echo $gradient[1]; ?> 100%);"
                                     title="<?php echo htmlspecialchars($gradient[2]); ?>">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрити</button>
                <button type="button" class="btn btn-primary" id="resetTheme">
                    <i class="fas fa-undo me-1"></i>Скинути
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Loading Spinner -->
<div id="page-loader" class="d-none">
    <div class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center" 
         style="background: rgba(0,0,0,0.5); z-index: 9999;">
        <div class="spinner-border text-light" role="status">
            <span class="visually-hidden">Завантаження...</span>
        </div>
    </div>
</div>

<!-- Theme Manager для всіх сторінок -->
<script>
// Простий ThemeManager для роботи на всіх сторінках
function initHeaderThemeManager() {
    console.log('Header ThemeManager: Initializing');
    
    // Градієнти
    const gradientOptions = document.querySelectorAll('.gradient-option');
    console.log('Header ThemeManager: Found gradient options:', gradientOptions.length);
    
    gradientOptions.forEach(option => {
        option.addEventListener('click', (e) => {
            console.log('Header ThemeManager: Gradient clicked:', e.target.dataset.gradient);
            const gradient = e.target.dataset.gradient;
            const darkMode = document.getElementById('darkModeSwitch')?.checked || false;
            changeHeaderTheme(gradient, darkMode);
        });
    });
    
    // Темний режим
    const darkModeSwitch = document.getElementById('darkModeSwitch');
    if (darkModeSwitch) {
        darkModeSwitch.addEventListener('change', (e) => {
            const currentGradient = document.querySelector('.gradient-option.active')?.dataset.gradient || 'gradient-2';
            changeHeaderTheme(currentGradient, e.target.checked);
        });
    }
    
    // Скидання теми
    const resetBtn = document.getElementById('resetTheme');
    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            resetHeaderTheme();
        });
    }
}

function changeHeaderTheme(gradient, darkMode) {
    console.log('Header ThemeManager: Changing theme to', gradient, 'dark mode:', darkMode);
    
    fetch('<?php echo getBaseUrl(); ?>/ajax/theme.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=change_theme&gradient=${gradient}&dark_mode=${darkMode ? 1 : 0}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            applyHeaderTheme(data.css, data.theme);
            updateHeaderUI(data.theme);
            showHeaderNotification('Тема змінена успішно!', 'success');
        } else {
            showHeaderNotification(data.message || 'Помилка зміни теми', 'error');
        }
    })
    .catch(error => {
        console.error('Theme change error:', error);
        showHeaderNotification('Помилка зміни теми', 'error');
    });
}

function resetHeaderTheme() {
    fetch('<?php echo getBaseUrl(); ?>/ajax/theme.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=reset_theme'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            applyHeaderTheme(data.css, data.theme);
            updateHeaderUI(data.theme);
            showHeaderNotification('Тема скинута до значень за замовчуванням', 'success');
            
            // Закриваємо модальне вікно
            const modal = bootstrap.Modal.getInstance(document.getElementById('themeModal'));
            if (modal) modal.hide();
        } else {
            showHeaderNotification(data.message || 'Помилка скидання теми', 'error');
        }
    })
    .catch(error => {
        console.error('Theme reset error:', error);
        showHeaderNotification('Помилка скидання теми', 'error');
    });
}

function applyHeaderTheme(css, theme) {
    // Видаляємо попередні динамічні стилі
    const existingStyle = document.getElementById('dynamic-theme-styles');
    if (existingStyle) {
        existingStyle.remove();
    }
    
    // Додаємо нові стилі
    const style = document.createElement('style');
    style.id = 'dynamic-theme-styles';
    style.textContent = css;
    document.head.appendChild(style);
}

function updateHeaderUI(theme) {
    // Оновлюємо активний градієнт
    document.querySelectorAll('.gradient-option').forEach(option => {
        option.classList.remove('active');
        if (option.dataset.gradient === theme.gradient) {
            option.classList.add('active');
        }
    });
    
    // Оновлюємо перемикач темного режиму
    const darkModeSwitch = document.getElementById('darkModeSwitch');
    if (darkModeSwitch) {
        darkModeSwitch.checked = theme.dark_mode;
    }
}

function showHeaderNotification(message, type) {
    console.log(`Header Theme notification [${type}]: ${message}`);
    
    // Можна додати Toast якщо потрібно
    if (typeof showToast === 'function') {
        showToast(message, type);
    }
}

// Ініціалізуємо після завантаження Bootstrap
document.addEventListener('DOMContentLoaded', function() {
    // Невелика затримка щоб Bootstrap встиг завантажитись
    setTimeout(initHeaderThemeManager, 100);
});
</script>