<?php
/**
 * Вспомогательные функции
 */

/**
 * Безопасный вывод данных
 */
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Генерация CSRF токена
 */
function generateCSRFToken() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Проверка CSRF токена
 */
function verifyCSRFToken($token) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

/**
 * Редирект
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Получение URL темы
 */
function themeUrl($path = '') {
    return THEME_URL . ($path ? '/' . $path : '');
}

/**
 * Получение URL изображений
 */
function imageUrl($path = '') {
    return IMAGES_URL . ($path ? '/' . $path : '');
}

/**
 * Проверка AJAX запроса
 */
function isAjax() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

/**
 * JSON ответ
 */
function jsonResponse($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Форматирование цены
 */
function formatPrice($price, $currency = 'руб.') {
    return number_format($price, 2, '.', ' ') . ' ' . $currency;
}

/**
 * Обрезка текста
 */
function truncateText($text, $length = 100, $suffix = '...') {
    if (mb_strlen($text) > $length) {
        return mb_substr($text, 0, $length) . $suffix;
    }
    return $text;
}