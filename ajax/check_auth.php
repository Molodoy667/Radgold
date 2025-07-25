<?php
require_once '../core/config.php';
require_once '../core/functions.php';

header('Content-Type: application/json');

try {
    $response = [
        'logged_in' => false,
        'user' => null
    ];
    
    if (isLoggedIn()) {
        $response['logged_in'] = true;
        $response['user'] = [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'] ?? '',
            'role' => $_SESSION['user_role'] ?? 'user',
            'user_type' => $_SESSION['user_type'] ?? 'user'
        ];
    }
    
    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Server error',
        'logged_in' => false
    ]);
}
?>