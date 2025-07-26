<?php

/**
 * Enhanced PSR-4 Autoloader for GameMarket Pro
 */
spl_autoload_register(function ($className) {
    // Преобразуем пространство имен в путь к файлу
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';
    
    // Проверяем, что класс использует наше пространство имен
    $len = strlen($prefix);
    if (strncmp($prefix, $className, $len) !== 0) {
        return;
    }
    
    // Получаем относительное имя класса
    $relativeClass = substr($className, $len);
    
    // Особые правила для конкретных пространств имен
    $parts = explode('\\', $relativeClass);
    $namespace = strtolower($parts[0]); // Core, Controllers, Models -> core, controllers, models
    $className = $parts[1] ?? '';
    
    // Возможные пути к файлу
    $possiblePaths = [
        // Стандартный PSR-4 путь
        $baseDir . str_replace('\\', '/', $relativeClass) . '.php',
        // Путь с маленькими буквами для папок
        $baseDir . $namespace . '/' . $className . '.php',
        // Дополнительные варианты
        $baseDir . strtolower($relativeClass) . '.php'
    ];
    
    // Пробуем найти файл по одному из путей
    foreach ($possiblePaths as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
    
    // Если не нашли, выводим отладочную информацию в dev режиме
    if (defined('DEBUG_AUTOLOAD') && DEBUG_AUTOLOAD) {
        error_log("Autoloader: Class '$className' not found. Tried paths: " . implode(', ', $possiblePaths));
    }
});

// Дополнительный автозагрузчик для helper функций
if (file_exists(__DIR__ . '/../app/helpers.php')) {
    require_once __DIR__ . '/../app/helpers.php';
}