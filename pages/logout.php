<?php
require_once '../config/config.php';

// Видаляємо всі дані сесії
session_unset();
session_destroy();

// Видаляємо cookie для запам'ятовування
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

// Перенаправляємо на головну сторінку
header("Location: ../index.php");
exit();
?>