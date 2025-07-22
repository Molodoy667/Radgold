<?php
// Обробка AJAX-запитів
header('Content-Type: application/json');
$response = ['status' => 'ok', 'message' => 'AJAX працює'];
echo json_encode($response);