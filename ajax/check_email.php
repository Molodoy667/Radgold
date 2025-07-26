<?php
require_once '../core/config.php';
require_once '../core/functions.php';

header('Content-Type: application/json');

// Перевірка методу запиту
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// Отримання даних
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['email']) || empty($input['email'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Email is required']);
    exit();
}

$email = sanitize($input['email']);

// Валідація email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email format']);
    exit();
}

try {
    $exists = userExists($email);
    
    echo json_encode([
        'exists' => $exists,
        'message' => $exists ? 'Email вже використовується' : 'Email доступний'
    ]);
    
} catch (Exception $e) {
    error_log("Email check error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}
?>