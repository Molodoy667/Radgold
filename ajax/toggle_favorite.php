<?php
require_once '../config/config.php';
require_once '../config/database.php';

header('Content-Type: application/json');

// Перевіряємо авторизацію
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Авторизуйтесь для додавання в вподобання']);
    exit();
}

// Отримуємо дані
$input = json_decode(file_get_contents('php://input'), true);
$ad_id = isset($input['ad_id']) ? (int)$input['ad_id'] : 0;
$user_id = $_SESSION['user_id'];

if ($ad_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Невірний ID оголошення']);
    exit();
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Перевіряємо, чи існує оголошення
    $check_ad = "SELECT id FROM ads WHERE id = ? AND status = 'active'";
    $check_stmt = $db->prepare($check_ad);
    $check_stmt->execute([$ad_id]);
    
    if ($check_stmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'message' => 'Оголошення не знайдено']);
        exit();
    }
    
    // Перевіряємо, чи вже є в вподобаннях
    $check_query = "SELECT id FROM favorites WHERE user_id = ? AND ad_id = ?";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->execute([$user_id, $ad_id]);
    
    if ($check_stmt->rowCount() > 0) {
        // Видаляємо з вподобань
        $delete_query = "DELETE FROM favorites WHERE user_id = ? AND ad_id = ?";
        $delete_stmt = $db->prepare($delete_query);
        $delete_stmt->execute([$user_id, $ad_id]);
        
        echo json_encode(['success' => true, 'action' => 'removed', 'message' => 'Видалено з вподобань']);
    } else {
        // Додаємо до вподобань
        $insert_query = "INSERT INTO favorites (user_id, ad_id) VALUES (?, ?)";
        $insert_stmt = $db->prepare($insert_query);
        $insert_stmt->execute([$user_id, $ad_id]);
        
        echo json_encode(['success' => true, 'action' => 'added', 'message' => 'Додано до вподобань']);
    }
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Помилка бази даних']);
}
?>