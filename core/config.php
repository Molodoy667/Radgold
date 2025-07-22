<?php
// Конфігурація сайту

if (!file_exists(__DIR__ . '/installed.lock')) {
    header('Location: /install/index.php');
    exit;
}

// Налаштування бази даних
const DB_HOST = 'localhost';
const DB_NAME = 'my_database';
const DB_USER = 'root';
const DB_PASS = '';

// Інші константи
const SITE_NAME = 'Мій сайт';