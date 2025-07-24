<?php
require_once '../core/config.php';
require_once '../core/functions.php';

header('Content-Type: application/json');

// Перевірка авторизації
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Необхідно увійти в акаунт'
    ]);
    exit();
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $adId = (int)($input['ad_id'] ?? 0);
    $userId = $_SESSION['user_id'];
    
    if (!$adId) {
        throw new Exception('Невірний ID оголошення');
    }
    
    // Перевірка існування оголошення
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT id FROM ads WHERE id = ? AND status = 'active'");
    $stmt->bind_param("i", $adId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Оголошення не знайдено');
    }
    
    // Перевірка чи вже в улюблених
    $stmt = $db->prepare("SELECT id FROM favorites WHERE user_id = ? AND ad_id = ?");
    $stmt->bind_param("ii", $userId, $adId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $isFavorite = $result->num_rows > 0;
    
    if ($isFavorite) {
        // Видаляємо з улюблених
        $stmt = $db->prepare("DELETE FROM favorites WHERE user_id = ? AND ad_id = ?");
        $stmt->bind_param("ii", $userId, $adId);
        $stmt->execute();
        
        $message = 'Видалено з улюблених';
        $newStatus = false;
    } else {
        // Додаємо в улюблені
        $stmt = $db->prepare("INSERT INTO favorites (user_id, ad_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $userId, $adId);
        $stmt->execute();
        
        $message = 'Додано в улюблені';
        $newStatus = true;
    }
    
    echo json_encode([
        'success' => true,
        'message' => $message,
        'is_favorite' => $newStatus
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>