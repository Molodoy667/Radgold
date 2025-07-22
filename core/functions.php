<?php
// Загальні функції сайту

function site_url($path = '') {
    return '/' . ltrim($path, '/');
}

function get_setting($name) {
    static $cache = [];
    if (isset($cache[$name])) return $cache[$name];
    try {
        $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
        $stmt = $pdo->prepare('SELECT value FROM settings WHERE name=?');
        $stmt->execute([$name]);
        $val = $stmt->fetchColumn();
        $cache[$name] = $val;
        return $val;
    } catch(Exception $e) {
        return null;
    }
}

function get_site_name() {
    return get_setting('site_name') ?: (defined('SITE_NAME') ? SITE_NAME : 'Мій сайт');
}
function get_theme() {
    return get_setting('theme') ?: 'light';
}
function get_gradient() {
    return get_setting('gradient') ?: 'gradient-1';
}