<?php
/**
 * Клас для управління темами оформлення
 */
class Theme {
    private static $db = null;
    private static $current_theme = null;
    
    /**
     * Градієнти тем
     */
    private static $gradients = [
        'gradient-1' => ['#667eea', '#764ba2', 'Фіолетово-синій'],
        'gradient-2' => ['#f093fb', '#f5576c', 'Рожево-червоний'],
        'gradient-3' => ['#4facfe', '#00f2fe', 'Блакитний'],
        'gradient-4' => ['#43e97b', '#38f9d7', 'Зелено-м\'ятний'],
        'gradient-5' => ['#fa709a', '#fee140', 'Рожево-жовтий'],
        'gradient-6' => ['#a8edea', '#fed6e3', 'М\'ятно-рожевий'],
        'gradient-7' => ['#ffecd2', '#fcb69f', 'Персиковий'],
        'gradient-8' => ['#ff8a80', '#ea6100', 'Оранжево-червоний'],
        'gradient-9' => ['#8360c3', '#2ebf91', 'Фіолетово-зелений'],
        'gradient-10' => ['#667db6', '#0082c8', 'Синьо-блакитний'],
        'gradient-11' => ['#f8cdda', '#1d2b64', 'Рожево-темний'],
        'gradient-12' => ['#89f7fe', '#66a6ff', 'Небесний'],
        'gradient-13' => ['#fdbb2d', '#22c1c3', 'Жовто-блакитний'],
        'gradient-14' => ['#e8198b', '#c7eafd', 'Малиново-голубий'],
        'gradient-15' => ['#396afc', '#2948ff', 'Електричний синій'],
        'gradient-16' => ['#c471f5', '#fa71cd', 'Фіолетово-рожевий'],
        'gradient-17' => ['#ff9a9e', '#fecfef', 'Рожева хмара'],
        'gradient-18' => ['#a18cd1', '#fbc2eb', 'Лавандовий сон'],
        'gradient-19' => ['#fad0c4', '#ffd1ff', 'Рожева вата'],
        'gradient-20' => ['#ffecd2', '#fcb69f', 'Золотий захід'],
        'gradient-21' => ['#f6d365', '#fda085', 'Теплий ранок'],
        'gradient-22' => ['#96fbc4', '#f9f047', 'Лаймовий фреш'],
        'gradient-23' => ['#667eea', '#764ba2', 'Космічний'],
        'gradient-24' => ['#ff6b6b', '#feca57', 'Літній день'],
        'gradient-25' => ['#48cae4', '#023e8a', 'Океанський'],
        'gradient-26' => ['#a8e6cf', '#dcedc1', 'Весняна зелень'],
        'gradient-27' => ['#ff8a80', '#ffad80', 'Коралловий'],
        'gradient-28' => ['#d299c2', '#fef9d7', 'Ніжний ранок'],
        'gradient-29' => ['#89f7fe', '#66a6ff', 'Кристальний'],
        'gradient-30' => ['#fc466b', '#3f5efb', 'Неоновий контраст']
    ];
    
    /**
     * Ініціалізація
     */
    public static function init($database_connection = null) {
        if ($database_connection) {
            self::$db = $database_connection;
        }
    }
    
    /**
     * Отримання поточної теми користувача
     */
    public static function getCurrentTheme() {
        if (self::$current_theme !== null) {
            return self::$current_theme;
        }
        
        $user_id = $_SESSION['user_id'] ?? null;
        $session_id = session_id();
        
        // Спробуємо завантажити з бази даних
        if (self::$db) {
            try {
                $query = "SELECT theme_gradient, dark_mode FROM user_themes 
                         WHERE " . ($user_id ? "user_id = ?" : "session_id = ?") . " 
                         ORDER BY updated_at DESC LIMIT 1";
                $stmt = self::$db->prepare($query);
                $stmt->execute([$user_id ?: $session_id]);
                $theme = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($theme) {
                    self::$current_theme = [
                        'gradient' => $theme['theme_gradient'],
                        'dark_mode' => (bool)$theme['dark_mode']
                    ];
                    return self::$current_theme;
                }
            } catch (PDOException $e) {
                error_log("Theme error: " . $e->getMessage());
            }
        }
        
        // Стандартна тема
        self::$current_theme = [
            'gradient' => Settings::get('default_theme_gradient', 'gradient-2'), // рожевий за замовчуванням
            'dark_mode' => Settings::get('default_dark_mode', false)
        ];
        
        return self::$current_theme;
    }
    
    /**
     * Збереження теми користувача
     */
    public static function saveTheme($gradient, $dark_mode = false) {
        if (!self::$db) {
            return false;
        }
        
        $user_id = $_SESSION['user_id'] ?? null;
        $session_id = session_id();
        
        try {
            // Перевіряємо чи існує запис
            $check_query = "SELECT id FROM user_themes 
                           WHERE " . ($user_id ? "user_id = ?" : "session_id = ?");
            $check_stmt = self::$db->prepare($check_query);
            $check_stmt->execute([$user_id ?: $session_id]);
            $exists = $check_stmt->fetch();
            
            if ($exists) {
                // Оновлюємо існуючий запис
                $update_query = "UPDATE user_themes 
                               SET theme_gradient = ?, dark_mode = ?, updated_at = CURRENT_TIMESTAMP
                               WHERE " . ($user_id ? "user_id = ?" : "session_id = ?");
                $update_stmt = self::$db->prepare($update_query);
                $result = $update_stmt->execute([$gradient, $dark_mode ? 1 : 0, $user_id ?: $session_id]);
            } else {
                // Створюємо новий запис
                $insert_query = "INSERT INTO user_themes (user_id, session_id, theme_gradient, dark_mode) 
                               VALUES (?, ?, ?, ?)";
                $insert_stmt = self::$db->prepare($insert_query);
                $result = $insert_stmt->execute([$user_id, $session_id, $gradient, $dark_mode ? 1 : 0]);
            }
            
            if ($result) {
                // Оновлюємо кеш
                self::$current_theme = [
                    'gradient' => $gradient,
                    'dark_mode' => (bool)$dark_mode
                ];
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Theme save error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Отримання CSS змінних для поточної теми
     */
    public static function getCSSVariables() {
        $theme = self::getCurrentTheme();
        $gradient = self::$gradients[$theme['gradient']] ?? self::$gradients['gradient-2'];
        $dark_mode = $theme['dark_mode'];
        
        $primary_color = $gradient[0];
        $secondary_color = $gradient[1];
        
        // Кольори для темного/світлого режиму
        if ($dark_mode) {
            $bg_color = '#121212';
            $surface_color = '#1e1e1e';
            $text_color = '#ffffff';
            $text_muted = '#b0b0b0';
            $border_color = '#333333';
            $card_bg = '#2d2d2d';
            $navbar_bg = '#1a1a1a';
        } else {
            $bg_color = '#ffffff';
            $surface_color = '#f8f9fa';
            $text_color = '#212529';
            $text_muted = '#6c757d';
            $border_color = '#dee2e6';
            $card_bg = '#ffffff';
            $navbar_bg = '#ffffff';
        }
        
        // Конвертируем hex в RGB для CSS переменных
        $primary_rgb = self::hexToRgb($primary_color);
        $secondary_rgb = self::hexToRgb($secondary_color);
        
        return [
            '--theme-primary' => $primary_color,
            '--theme-secondary' => $secondary_color,
            '--theme-primary-rgb' => $primary_rgb,
            '--theme-secondary-rgb' => $secondary_rgb,
            '--theme-gradient' => "linear-gradient(135deg, {$primary_color} 0%, {$secondary_color} 100%)",
            '--bg-color' => $bg_color,
            '--surface-color' => $surface_color,
            '--text-color' => $text_color,
            '--text-muted' => $text_muted,
            '--border-color' => $border_color,
            '--card-bg' => $card_bg,
            '--navbar-bg' => $navbar_bg,
            '--shadow' => $dark_mode ? '0 4px 6px rgba(0, 0, 0, 0.3)' : '0 4px 6px rgba(0, 0, 0, 0.1)',
            '--theme-mode' => $dark_mode ? 'dark' : 'light'
        ];
    }
    
    /**
     * Конвертація hex в RGB
     */
    private static function hexToRgb($hex) {
        $hex = ltrim($hex, '#');
        
        if (strlen($hex) == 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        return "$r, $g, $b";
    }
    
    /**
     * Генерація CSS стилів для теми
     */
    public static function generateCSS() {
        $vars = self::getCSSVariables();
        $css = ":root {\n";
        
        foreach ($vars as $key => $value) {
            $css .= "    {$key}: {$value};\n";
        }
        
        $css .= "}\n\n";
        
        // Додаткові стилі для темного режиму
        if ($vars['--theme-mode'] === 'dark') {
            $css .= "
body {
    background-color: var(--bg-color) !important;
    color: var(--text-color) !important;
}

.bg-light {
    background-color: var(--surface-color) !important;
}

.bg-white {
    background-color: var(--card-bg) !important;
}

.card {
    background-color: var(--card-bg) !important;
    border-color: var(--border-color) !important;
    color: var(--text-color) !important;
}

.navbar-light {
    background-color: var(--navbar-bg) !important;
    border-bottom: 1px solid var(--border-color);
}

.navbar-light .navbar-nav .nav-link {
    color: var(--text-color) !important;
}

.navbar-light .navbar-brand {
    color: var(--text-color) !important;
}

.text-muted {
    color: var(--text-muted) !important;
}

.border {
    border-color: var(--border-color) !important;
}

.form-control {
    background-color: var(--surface-color) !important;
    border-color: var(--border-color) !important;
    color: var(--text-color) !important;
}

.form-control:focus {
    background-color: var(--surface-color) !important;
    border-color: var(--theme-primary) !important;
    color: var(--text-color) !important;
    box-shadow: 0 0 0 0.2rem rgba(var(--theme-primary), 0.25) !important;
}

.dropdown-menu {
    background-color: var(--card-bg) !important;
    border-color: var(--border-color) !important;
}

.dropdown-item {
    color: var(--text-color) !important;
}

.dropdown-item:hover {
    background-color: var(--surface-color) !important;
}
";
        }
        
        // Стилі для градієнтів
        $css .= "
.btn-primary {
    background: var(--theme-gradient) !important;
    border: none !important;
    color: white !important;
    transition: all 0.3s ease !important;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2) !important;
}

.btn-primary:hover, .btn-primary:focus, .btn-primary:active {
    background: var(--theme-gradient) !important;
    color: white !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 20px rgba(0,0,0,0.3) !important;
}

.text-gradient {
    background: var(--theme-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.bg-gradient {
    background: var(--theme-gradient) !important;
}

.search-container {
    background: var(--theme-gradient);
}
";
        
        return $css;
    }
    
    /**
     * Отримання всіх доступних градієнтів
     */
    public static function getGradients() {
        return self::$gradients;
    }
    
    /**
     * Перевірка чи дозволена зміна теми
     */
    public static function isThemeSwitcherEnabled() {
        return Settings::get('enable_theme_switcher', true);
    }
}
?>