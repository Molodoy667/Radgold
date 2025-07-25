<?php
// Тестовий файл для перевірки сайту без встановлення
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simulated constants for testing
define('SITE_URL', 'http://localhost');
define('SITE_NAME', 'AdBoard Pro');
define('SITE_DESCRIPTION', 'Рекламна компанія та дошка оголошень');

// Mock functions for testing
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function isLoggedIn() {
    return false; // For testing
}

function isAdmin() {
    return false; // For testing
}

function getRoute() {
    return '';
}

function getMetaTags() {
    return [
        'title' => 'AdBoard Pro',
        'description' => 'Рекламна компанія та дошка оголошень',
        'keywords' => 'реклама, оголошення',
        'author' => 'AdBoard Pro',
        'favicon' => 'images/favicon.svg',
        'logo' => 'images/default_logo.svg'
    ];
}

function getThemeSettings() {
    return [
        'current_theme' => 'light',
        'current_gradient' => 'gradient-1'
    ];
}

function generateGradients() {
    return [
        'gradient-1' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        'gradient-2' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
        'gradient-3' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
        // ... more gradients
    ];
}

echo "<h1>Тестування сайту AdBoard Pro</h1>";
echo "<h2>Тестування header.php</h2>";

// Test header
try {
    ob_start();
    include 'themes/header.php';
    $headerOutput = ob_get_clean();
    echo "<p style='color: green;'>✓ Header.php завантажено успішно</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Помилка в header.php: " . $e->getMessage() . "</p>";
}

echo "<h2>Тестування home.php</h2>";

// Test home page
try {
    ob_start();
    include 'pages/home.php';
    $homeOutput = ob_get_clean();
    echo "<p style='color: green;'>✓ Home.php завантажено успішно</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Помилка в home.php: " . $e->getMessage() . "</p>";
}

echo "<h2>Тестування footer.php</h2>";

// Test footer
try {
    ob_start();
    include 'themes/footer.php';
    $footerOutput = ob_get_clean();
    echo "<p style='color: green;'>✓ Footer.php завантажено успішно</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Помилка в footer.php: " . $e->getMessage() . "</p>";
}

echo "<h2>Тестування login.php</h2>";

// Test login page
try {
    ob_start();
    include 'pages/login.php';
    $loginOutput = ob_get_clean();
    echo "<p style='color: green;'>✓ Login.php завантажено успішно</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Помилка в login.php: " . $e->getMessage() . "</p>";
}

echo "<h2>Тестування install.php</h2>";

// Test if install.php loads without errors
if (file_exists('install.php')) {
    echo "<p style='color: green;'>✓ Install.php існує</p>";
} else {
    echo "<p style='color: red;'>✗ Install.php не знайдено</p>";
}

echo "<h2>Перевірка файлової структури</h2>";

$requiredFiles = [
    'index.php',
    '.htaccess',
    'database.sql',
    'core/config.php',
    'core/database.php',
    'core/functions.php',
    'themes/header.php',
    'themes/footer.php',
    'themes/style.css',
    'themes/script.js',
    'pages/home.php',
    'pages/login.php',
    'pages/404.php',
    'ajax/change_theme.php',
    'ajax/search.php',
    'admin/dashboard.php',
    'admin/login.php',
    'admin/header.php',
    'admin/footer.php'
];

$missingFiles = [];

foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✓ $file</p>";
    } else {
        echo "<p style='color: red;'>✗ $file</p>";
        $missingFiles[] = $file;
    }
}

if (empty($missingFiles)) {
    echo "<h3 style='color: green;'>🎉 Всі основні файли присутні!</h3>";
} else {
    echo "<h3 style='color: red;'>❌ Відсутні файли: " . implode(', ', $missingFiles) . "</h3>";
}

echo "<h2>Перевірка директорій</h2>";

$requiredDirs = [
    'admin',
    'ajax',
    'core',
    'images',
    'images/uploads',
    'images/thumbs',
    'images/avatars',
    'pages',
    'themes'
];

foreach ($requiredDirs as $dir) {
    if (is_dir($dir)) {
        echo "<p style='color: green;'>✓ $dir/</p>";
    } else {
        echo "<p style='color: red;'>✗ $dir/</p>";
    }
}

echo "<hr>";
echo "<h2>Готовність до встановлення</h2>";

if (empty($missingFiles)) {
    echo "<p style='color: green; font-size: 18px;'><strong>✅ Сайт готовий до встановлення!</strong></p>";
    echo "<p>Запустіть <a href='install.php' style='color: blue;'>install.php</a> для початку встановлення.</p>";
} else {
    echo "<p style='color: red; font-size: 18px;'><strong>❌ Сайт потребує доопрацювання</strong></p>";
}
?>
