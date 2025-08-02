<?php
session_start();
require_once '../config.php';
require_once '../classes/Database.php';
require_once '../classes/PaymentSystem.php';

$config = require '../config.php';

// Проверка авторизации
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

if ($_POST) {
    $db = new Database($config['database']);
    $paymentSystem = new PaymentSystem($db, $config);
    
    $action = $_POST['action'] ?? '';
    $paymentId = $_POST['payment_id'] ?? 0;
    $adminId = $_SESSION['admin_id'] ?? 0; // Если есть ID админа в сессии
    
    if ($action === 'confirm') {
        if ($paymentSystem->confirmPayment($paymentId, $adminId)) {
            $_SESSION['message'] = '<div class="alert alert-success">Платеж успешно подтвержден!</div>';
        } else {
            $_SESSION['message'] = '<div class="alert alert-danger">Ошибка подтверждения платежа</div>';
        }
    } elseif ($action === 'reject') {
        $reason = $_POST['reason'] ?? 'Не указана';
        if ($paymentSystem->rejectPayment($paymentId, $reason, $adminId)) {
            $_SESSION['message'] = '<div class="alert alert-warning">Платеж отклонен</div>';
        } else {
            $_SESSION['message'] = '<div class="alert alert-danger">Ошибка отклонения платежа</div>';
        }
    }
}

// Перенаправляем обратно
header('Location: payment_settings.php');
exit;
?>