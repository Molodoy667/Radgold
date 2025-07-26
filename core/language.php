<?php
/**
 * Система багатомовності для AdBoard Pro
 * Підтримка мов: Українська (uk), Русский (ru), English (en)
 */

class Language {
    private static $instance = null;
    private $currentLanguage = 'uk';
    private $availableLanguages = ['uk', 'ru', 'en'];
    private $translations = [];
    private $fallbackLanguage = 'uk';
    
    /**
     * Singleton pattern
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Конструктор
     */
    private function __construct() {
        $this->initializeLanguage();
        $this->loadTranslations();
    }
    
    /**
     * Ініціалізація мови
     */
    private function initializeLanguage() {
        // Пріоритет: GET параметр > Сесія > Cookies > Браузер > Fallback
        
        // 1. Перевіряємо GET параметр
        if (isset($_GET['lang']) && in_array($_GET['lang'], $this->availableLanguages)) {
            $this->currentLanguage = $_GET['lang'];
            $_SESSION['language'] = $this->currentLanguage;
            setcookie('language', $this->currentLanguage, time() + (365 * 24 * 60 * 60), '/');
            return;
        }
        
        // 2. Перевіряємо сесію
        if (isset($_SESSION['language']) && in_array($_SESSION['language'], $this->availableLanguages)) {
            $this->currentLanguage = $_SESSION['language'];
            return;
        }
        
        // 3. Перевіряємо cookies
        if (isset($_COOKIE['language']) && in_array($_COOKIE['language'], $this->availableLanguages)) {
            $this->currentLanguage = $_COOKIE['language'];
            $_SESSION['language'] = $this->currentLanguage;
            return;
        }
        
        // 4. Визначаємо з браузера
        $browserLang = $this->detectBrowserLanguage();
        if ($browserLang && in_array($browserLang, $this->availableLanguages)) {
            $this->currentLanguage = $browserLang;
            $_SESSION['language'] = $this->currentLanguage;
            setcookie('language', $this->currentLanguage, time() + (365 * 24 * 60 * 60), '/');
            return;
        }
        
        // 5. Fallback
        $this->currentLanguage = $this->fallbackLanguage;
    }
    
    /**
     * Визначення мови браузера
     */
    private function detectBrowserLanguage() {
        if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            return null;
        }
        
        $langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        
        foreach ($langs as $lang) {
            $lang = trim(explode(';', $lang)[0]);
            $lang = substr($lang, 0, 2); // Беремо тільки код мови
            
            switch ($lang) {
                case 'uk':
                    return 'uk';
                case 'ru':
                    return 'ru';
                case 'en':
                    return 'en';
            }
        }
        
        return null;
    }
    
    /**
     * Завантаження перекладів
     */
    private function loadTranslations() {
        $langFile = __DIR__ . "/../languages/{$this->currentLanguage}.php";
        
        if (file_exists($langFile)) {
            $this->translations = include $langFile;
        } else {
            // Fallback на українську
            $fallbackFile = __DIR__ . "/../languages/{$this->fallbackLanguage}.php";
            if (file_exists($fallbackFile)) {
                $this->translations = include $fallbackFile;
            }
        }
    }
    
    /**
     * Отримання перекладу
     */
    public function get($key, $params = []) {
        $value = $this->getNestedValue($this->translations, $key);
        
        if ($value === null) {
            // Спробуємо завантажити з fallback мови
            $fallbackFile = __DIR__ . "/../languages/{$this->fallbackLanguage}.php";
            if (file_exists($fallbackFile) && $this->currentLanguage !== $this->fallbackLanguage) {
                $fallbackTranslations = include $fallbackFile;
                $value = $this->getNestedValue($fallbackTranslations, $key);
            }
            
            // Якщо все ще не знайдено, повертаємо ключ
            if ($value === null) {
                return $key;
            }
        }
        
        // Підстановка параметрів
        if (!empty($params)) {
            foreach ($params as $param => $paramValue) {
                $value = str_replace(':' . $param, $paramValue, $value);
            }
        }
        
        return $value;
    }
    
    /**
     * Отримання вкладеного значення з масиву
     */
    private function getNestedValue($array, $key) {
        $keys = explode('.', $key);
        $value = $array;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return null;
            }
            $value = $value[$k];
        }
        
        return $value;
    }
    
    /**
     * Отримання поточної мови
     */
    public function getCurrentLanguage() {
        return $this->currentLanguage;
    }
    
    /**
     * Отримання доступних мов
     */
    public function getAvailableLanguages() {
        return $this->availableLanguages;
    }
    
    /**
     * Зміна мови
     */
    public function setLanguage($language) {
        if (in_array($language, $this->availableLanguages)) {
            $this->currentLanguage = $language;
            $_SESSION['language'] = $language;
            setcookie('language', $language, time() + (365 * 24 * 60 * 60), '/');
            $this->loadTranslations();
            return true;
        }
        return false;
    }
    
    /**
     * Отримання назви мови
     */
    public function getLanguageName($code = null) {
        $code = $code ?: $this->currentLanguage;
        
        $names = [
            'uk' => 'Українська',
            'ru' => 'Русский',
            'en' => 'English'
        ];
        
        return $names[$code] ?? $code;
    }
    
    /**
     * Отримання флагу мови
     */
    public function getLanguageFlag($code = null) {
        $code = $code ?: $this->currentLanguage;
        
        $flags = [
            'uk' => '🇺🇦',
            'ru' => '🇷🇺',
            'en' => '🇺🇸'
        ];
        
        return $flags[$code] ?? '🌐';
    }
    
    /**
     * Генерація URL з мовою
     */
    public function url($path = '', $language = null) {
        $language = $language ?: $this->currentLanguage;
        $baseUrl = rtrim(getSiteUrl(), '/');
        
        // Видаляємо мову з поточного шляху якщо вона є
        $path = ltrim($path, '/');
        $path = preg_replace('/^(uk|ru|en)\//', '', $path);
        
        // Додаємо мову тільки якщо це не українська (за замовчуванням)
        if ($language !== 'uk') {
            return $baseUrl . '/' . $language . '/' . $path;
        }
        
        return $baseUrl . '/' . $path;
    }
    
    /**
     * Форматування дати згідно мови
     */
    public function formatDate($timestamp, $format = 'full') {
        $date = is_numeric($timestamp) ? $timestamp : strtotime($timestamp);
        
        switch ($this->currentLanguage) {
            case 'uk':
                setlocale(LC_TIME, 'uk_UA.UTF-8', 'ukrainian');
                break;
            case 'ru':
                setlocale(LC_TIME, 'ru_RU.UTF-8', 'russian');
                break;
            case 'en':
                setlocale(LC_TIME, 'en_US.UTF-8', 'english');
                break;
        }
        
        switch ($format) {
            case 'short':
                return date('d.m.Y', $date);
            case 'medium':
                return date('d.m.Y H:i', $date);
            case 'full':
                return strftime('%d %B %Y', $date);
            case 'time':
                return date('H:i', $date);
            default:
                return date($format, $date);
        }
    }
    
    /**
     * Форматування чисел
     */
    public function formatNumber($number, $decimals = 0) {
        switch ($this->currentLanguage) {
            case 'uk':
            case 'ru':
                return number_format($number, $decimals, ',', ' ');
            case 'en':
                return number_format($number, $decimals, '.', ',');
            default:
                return number_format($number, $decimals);
        }
    }
    
    /**
     * Форматування валюти
     */
    public function formatCurrency($amount, $currency = 'UAH') {
        $formatted = $this->formatNumber($amount, 2);
        
        switch ($currency) {
            case 'UAH':
                return $formatted . ' ' . $this->get('currency.uah');
            case 'USD':
                return '$' . $formatted;
            case 'EUR':
                return '€' . $formatted;
            default:
                return $formatted . ' ' . $currency;
        }
    }
    
    /**
     * Плюралізація
     */
    public function plural($count, $forms) {
        $count = abs($count);
        
        switch ($this->currentLanguage) {
            case 'uk':
            case 'ru':
                // Правила для української та російської
                if ($count % 10 == 1 && $count % 100 != 11) {
                    return $forms[0]; // 1, 21, 31...
                } elseif ($count % 10 >= 2 && $count % 10 <= 4 && ($count % 100 < 10 || $count % 100 >= 20)) {
                    return $forms[1]; // 2-4, 22-24...
                } else {
                    return $forms[2]; // 0, 5-20, 25-30...
                }
                
            case 'en':
            default:
                return $count == 1 ? $forms[0] : ($forms[1] ?? $forms[0]);
        }
    }
}

/**
 * Глобальна функція для перекладу
 */
function __($key, $params = []) {
    return Language::getInstance()->get($key, $params);
}

/**
 * Глобальна функція для плюралізації
 */
function _n($count, $forms) {
    return Language::getInstance()->plural($count, $forms);
}

/**
 * Глобальна функція для форматування дати
 */
function _d($timestamp, $format = 'full') {
    return Language::getInstance()->formatDate($timestamp, $format);
}

/**
 * Глобальна функція для форматування валюти
 */
function _c($amount, $currency = 'UAH') {
    return Language::getInstance()->formatCurrency($amount, $currency);
}

/**
 * Глобальна функція для отримання поточної мови
 */
function getCurrentLanguage() {
    return Language::getInstance()->getCurrentLanguage();
}

/**
 * Глобальна функція для зміни мови
 */
function setLanguage($language) {
    return Language::getInstance()->setLanguage($language);
}

/**
 * Отримання мовного URL
 */
function langUrl($path = '', $language = null) {
    return Language::getInstance()->url($path, $language);
}
?>