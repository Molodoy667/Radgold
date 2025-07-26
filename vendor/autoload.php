<?php

/**
 * Simple PSR-4 Autoloader for GameMarket Pro
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
    
    // Заменяем пространство имен разделителями на разделители директорий
    // и добавляем .php в конце
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    
    // Если файл существует, подключаем его
    if (file_exists($file)) {
        require $file;
    }
});