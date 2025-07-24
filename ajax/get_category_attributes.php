<?php
require_once '../core/config.php';
require_once '../core/functions.php';

header('Content-Type: application/json');

try {
    $categoryId = (int)($_GET['category_id'] ?? 0);
    
    if (!$categoryId) {
        throw new Exception('Невірний ID категорії');
    }
    
    $db = Database::getInstance();
    $stmt = $db->prepare("
        SELECT id, name, type, options, is_required, sort_order
        FROM category_attributes 
        WHERE category_id = ? 
        ORDER BY sort_order ASC
    ");
    $stmt->bind_param("i", $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $attributes = $result->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode([
        'success' => true,
        'attributes' => $attributes
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'attributes' => []
    ]);
}
?>