<?php
// Основні налаштування сайту
define('SITE_NAME', 'Дошка Оголошень');
define('SITE_URL', 'http://localhost');
define('UPLOAD_DIR', 'assets/uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_EXTENSIONS', array('jpg', 'jpeg', 'png', 'gif'));

// Категорії оголошень
$categories = array(
    1 => 'Транспорт',
    2 => 'Нерухомість', 
    3 => 'Робота',
    4 => 'Послуги',
    5 => 'Для дому та саду',
    6 => 'Електроніка',
    7 => 'Мода і стиль',
    8 => 'Хобі, відпочинок і спорт',
    9 => 'Віддам безкоштовно',
    10 => 'Бізнес та послуги'
);

// Функція для захисту від XSS
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Стартування сесії
session_start();
?>