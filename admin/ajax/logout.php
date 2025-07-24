<?php
require_once '../../core/config.php';
require_once '../../core/functions.php';

// Перевіряємо чи користувач авторизований
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Не авторизований']);
    exit;
}

// Логуємо вихід
if (isset($_SESSION['user_id'])) {
    logActivity($_SESSION['user_id'], 'admin_logout', 'Вихід з адмін-панелі', ['ip' => getClientIP()]);
}

// Видаляємо remember token якщо є
if (isset($_COOKIE['remember_token'])) {
    try {
        $db = Database::getInstance();
        $db->query("DELETE FROM remember_tokens WHERE token = ?", [$_COOKIE['remember_token']]);
        setcookie('remember_token', '', time() - 3600, '/', '', false, true);
    } catch (Exception $e) {
        logError('Logout token cleanup error: ' . $e->getMessage());
    }
}

// Очищаємо сесію
session_destroy();

// Очищаємо cookie PHPSESSID
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

echo json_encode(['success' => true, 'redirect' => SITE_URL . '/admin/']);
?>