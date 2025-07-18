<?php
session_start();

require_once '../config/config.php';
require_once '../config/database.php';

// Логування виходу
if (isset($_SESSION['admin_id'])) {
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        $log_query = "INSERT INTO admin_logs (admin_id, action, description, ip_address, user_agent) 
                     VALUES (?, 'logout', 'Вихід з адмін панелі', ?, ?)";
        $log_stmt = $db->prepare($log_query);
        $log_stmt->execute([
            $_SESSION['admin_id'],
            $_SERVER['REMOTE_ADDR'] ?? '',
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
    } catch (Exception $e) {
        error_log("Logout logging error: " . $e->getMessage());
    }
}

// Видаляємо remember me cookie
if (isset($_COOKIE['admin_remember'])) {
    setcookie('admin_remember', '', time() - 3600, '/admin/');
}

// Очищуємо сесію
session_unset();
session_destroy();

// Перенаправляємо на сторінку входу
header('Location: index.php');
exit();
?>