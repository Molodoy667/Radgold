<?php
/**
 * Клас для роботи з налаштуваннями сайту
 */
class Settings {
    private static $settings = null;
    private static $db = null;
    
    /**
     * Ініціалізація налаштувань
     */
    public static function init($database_connection = null) {
        if ($database_connection) {
            self::$db = $database_connection;
            self::loadSettings();
        }
    }
    
    /**
     * Завантаження налаштувань з бази даних
     */
    private static function loadSettings() {
        if (self::$db === null) {
            return;
        }
        
        try {
            $query = "SELECT setting_key, setting_value, setting_type FROM settings";
            $stmt = self::$db->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            self::$settings = [];
            foreach ($results as $row) {
                $value = $row['setting_value'];
                
                // Конвертуємо значення відповідно до типу
                switch ($row['setting_type']) {
                    case 'boolean':
                        $value = (bool) $value;
                        break;
                    case 'integer':
                        $value = (int) $value;
                        break;
                    case 'json':
                        $value = json_decode($value, true);
                        break;
                    // string залишається як є
                }
                
                self::$settings[$row['setting_key']] = $value;
            }
        } catch (PDOException $e) {
            // В разі помилки використовуємо стандартні значення
            self::$settings = self::getDefaultSettings();
        }
    }
    
    /**
     * Отримання значення налаштування
     */
    public static function get($key, $default = null) {
        if (self::$settings === null) {
            self::$settings = self::getDefaultSettings();
        }
        
        return isset(self::$settings[$key]) ? self::$settings[$key] : $default;
    }
    
    /**
     * Встановлення значення налаштування
     */
    public static function set($key, $value) {
        if (self::$db === null) {
            return false;
        }
        
        try {
            // Визначаємо тип значення
            $type = 'string';
            if (is_bool($value)) {
                $type = 'boolean';
                $value = $value ? '1' : '0';
            } elseif (is_int($value)) {
                $type = 'integer';
                $value = (string) $value;
            } elseif (is_array($value)) {
                $type = 'json';
                $value = json_encode($value);
            }
            
            $query = "INSERT INTO settings (setting_key, setting_value, setting_type) 
                     VALUES (?, ?, ?) 
                     ON DUPLICATE KEY UPDATE 
                     setting_value = VALUES(setting_value), 
                     setting_type = VALUES(setting_type),
                     updated_at = CURRENT_TIMESTAMP";
            
            $stmt = self::$db->prepare($query);
            $result = $stmt->execute([$key, $value, $type]);
            
            if ($result) {
                // Оновлюємо кеш
                if (self::$settings !== null) {
                    self::$settings[$key] = $value;
                }
            }
            
            return $result;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Отримання всіх налаштувань
     */
    public static function getAll() {
        if (self::$settings === null) {
            self::$settings = self::getDefaultSettings();
        }
        
        return self::$settings;
    }
    
    /**
     * Стандартні значення налаштувань (якщо БД недоступна)
     */
    private static function getDefaultSettings() {
        return [
            // Основні налаштування
            'site_name' => 'Дошка Оголошень',
            'site_title' => 'Дошка Оголошень - Купуй та продавай легко',
            'site_description' => 'Найбільша дошка оголошень в Україні. Купуй та продавай товари та послуги легко та безпечно.',
            'site_keywords' => 'оголошення, купівля, продаж, товари, послуги',
            'site_logo' => 'assets/images/logo.png',
            'site_favicon' => 'assets/images/favicon.ico',
            'site_url' => 'http://localhost',
            'site_language' => 'uk',
            'site_timezone' => 'Europe/Kiev',
            
            // Технічні
            'admin_email' => 'admin@example.com',
            'items_per_page' => 12,
            'max_images_per_ad' => 5,
            'max_image_size' => 5242880,
            'allowed_image_types' => 'jpg,jpeg,png,gif,webp',
            'registration_enabled' => true,
            'moderation_enabled' => false,
            'ads_auto_expire_days' => 30,
            
            // Контакти
            'contact_phone' => '+380 (44) 123-45-67',
            'contact_email' => 'info@example.com',
            'contact_address' => 'м. Київ, вул. Хрещатик, 1',
            
            // Соціальні мережі
            'social_facebook' => '',
            'social_instagram' => '',
            'social_telegram' => '',
            'social_twitter' => '',
            'social_youtube' => '',
            
            // SEO
            'analytics_google' => '',
            'analytics_yandex' => '',
            'google_site_verification' => '',
            'yandex_verification' => '',
            
            // Дизайн
            'theme_color' => '#007bff',
            'theme_secondary_color' => '#6c757d',
            'header_background' => '#ffffff',
            'footer_background' => '#343a40',
            
            // Функціонал
            'enable_comments' => true,
            'enable_ratings' => true,
            'enable_favorites' => true,
            'enable_sharing' => true,
            'enable_search' => true,
            
            // Системні
            'maintenance_mode' => false,
            'maintenance_message' => 'Сайт тимчасово недоступний через технічні роботи.',
            'max_login_attempts' => 5,
            'session_lifetime' => 3600,
            'backup_enabled' => true,
            'debug_mode' => false
        ];
    }
    
    /**
     * Очищення кешу налаштувань
     */
    public static function clearCache() {
        self::$settings = null;
    }
    
    /**
     * Перевірка чи сайт в режимі обслуговування
     */
    public static function isMaintenanceMode() {
        return self::get('maintenance_mode', false);
    }
    
    /**
     * Отримання повної URL адреси сайту
     */
    public static function getSiteUrl($path = '') {
        $base_url = rtrim(self::get('site_url', 'http://localhost'), '/');
        if (empty($path)) {
            return $base_url;
        }
        return $base_url . '/' . ltrim($path, '/');
    }
    
    /**
     * Отримання URL логотипу
     */
    public static function getLogoUrl() {
        $logo_path = self::get('site_logo', 'assets/images/logo.png');
        return self::getSiteUrl($logo_path);
    }
    
    /**
     * Отримання URL фавікону
     */
    public static function getFaviconUrl() {
        $favicon_path = self::get('site_favicon', 'assets/images/favicon.ico');
        return self::getSiteUrl($favicon_path);
    }
    
    /**
     * Генерація META тегів для головної частини сторінки
     */
    public static function getMetaTags($custom_title = null, $custom_description = null, $custom_keywords = null) {
        $title = $custom_title ?: self::get('site_title', 'Дошка Оголошень');
        $description = $custom_description ?: self::get('site_description', '');
        $keywords = $custom_keywords ?: self::get('site_keywords', '');
        $favicon = self::getFaviconUrl();
        $site_url = self::get('site_url', '');
        $language = self::get('site_language', 'uk');
        $theme_color = self::get('theme_color', '#007bff');
        
        // Google Analytics
        $ga_code = self::get('analytics_google', '');
        $ga_script = '';
        if (!empty($ga_code)) {
            $ga_script = "
            <!-- Google Analytics -->
            <script async src=\"https://www.googletagmanager.com/gtag/js?id={$ga_code}\"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', '{$ga_code}');
            </script>";
        }
        
        // Yandex Metrica
        $ym_code = self::get('analytics_yandex', '');
        $ym_script = '';
        if (!empty($ym_code)) {
            $ym_script = "
            <!-- Yandex.Metrica -->
            <script type=\"text/javascript\">
                (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
                m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
                (window, document, \"script\", \"https://mc.yandex.ru/metrika/watch.js\", \"ym\");
                ym({$ym_code}, \"init\", {clickmap:true,trackLinks:true,accurateTrackBounce:true});
            </script>";
        }
        
        // Верифікація
        $google_verification = self::get('google_site_verification', '');
        $yandex_verification = self::get('yandex_verification', '');
        
        $verification_tags = '';
        if (!empty($google_verification)) {
            $verification_tags .= "\n    <meta name=\"google-site-verification\" content=\"{$google_verification}\">";
        }
        if (!empty($yandex_verification)) {
            $verification_tags .= "\n    <meta name=\"yandex-verification\" content=\"{$yandex_verification}\">";
        }
        
        return [
            'title' => $title,
            'meta_tags' => "
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <meta name=\"description\" content=\"" . htmlspecialchars($description) . "\">
    <meta name=\"keywords\" content=\"" . htmlspecialchars($keywords) . "\">
    <meta name=\"author\" content=\"" . htmlspecialchars(self::get('site_name', '')) . "\">
    <meta name=\"robots\" content=\"index, follow\">
    <meta name=\"language\" content=\"{$language}\">
    <meta name=\"theme-color\" content=\"{$theme_color}\">{$verification_tags}
    
    <!-- Open Graph / Facebook -->
    <meta property=\"og:type\" content=\"website\">
    <meta property=\"og:url\" content=\"{$site_url}\">
    <meta property=\"og:title\" content=\"" . htmlspecialchars($title) . "\">
    <meta property=\"og:description\" content=\"" . htmlspecialchars($description) . "\">
    <meta property=\"og:site_name\" content=\"" . htmlspecialchars(self::get('site_name', '')) . "\">
    
    <!-- Twitter -->
    <meta property=\"twitter:card\" content=\"summary_large_image\">
    <meta property=\"twitter:url\" content=\"{$site_url}\">
    <meta property=\"twitter:title\" content=\"" . htmlspecialchars($title) . "\">
    <meta property=\"twitter:description\" content=\"" . htmlspecialchars($description) . "\">
    
    <!-- Favicon -->
    <link rel=\"icon\" type=\"image/x-icon\" href=\"{$favicon}\">
    <link rel=\"shortcut icon\" href=\"{$favicon}\">",
            'analytics' => $ga_script . $ym_script
        ];
    }
}
?>