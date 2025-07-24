<?php
require_once '../core/config.php';
require_once '../core/functions.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Метод не дозволений');
    }
    
    // Валідація даних
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $service = $_POST['service'] ?? '';
    $message = trim($_POST['message'] ?? '');
    
    // Перевірка обов'язкових полів
    if (empty($name) || empty($phone) || empty($email) || empty($service)) {
        throw new Exception('Заповніть всі обов\'язкові поля');
    }
    
    // Валідація email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Невірний формат email');
    }
    
    // Валідація телефону (простий варіант)
    $phone = preg_replace('/[^\d+\-\(\)\s]/', '', $phone);
    if (strlen($phone) < 10) {
        throw new Exception('Невірний формат телефону');
    }
    
    // Валідація послуги
    $allowedServices = ['smm', 'seo', 'web', 'design', 'complex'];
    if (!in_array($service, $allowedServices)) {
        throw new Exception('Невідома послуга');
    }
    
    // Обмеження по довжині
    if (strlen($name) > 100) {
        throw new Exception('Ім\'я занадто довге');
    }
    
    if (strlen($message) > 1000) {
        throw new Exception('Повідомлення занадто довге');
    }
    
    // Перевірка на спам (простий варіант)
    $spamKeywords = ['casino', 'loan', 'viagra', 'buy now', 'click here'];
    $textToCheck = strtolower($name . ' ' . $message);
    
    foreach ($spamKeywords as $keyword) {
        if (strpos($textToCheck, $keyword) !== false) {
            throw new Exception('Повідомлення містить заборонений контент');
        }
    }
    
    // Збереження в базу даних
    $db = Database::getInstance();
    
    $stmt = $db->prepare("
        INSERT INTO consultation_requests (
            name, phone, email, service_type, message, 
            ip_address, user_agent, status, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, 'new', NOW())
    ");
    
    $ipAddress = getRealIpAddress();
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    $stmt->bind_param("sssssss", $name, $phone, $email, $service, $message, $ipAddress, $userAgent);
    
    if (!$stmt->execute()) {
        throw new Exception('Помилка збереження заявки');
    }
    
    $consultationId = $db->insert_id;
    
    // Відправка email адміністратору (якщо налаштовано)
    try {
        sendConsultationNotification($consultationId, [
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'service' => getServiceName($service),
            'message' => $message
        ]);
    } catch (Exception $e) {
        // Логуємо помилку email, але не переривуємо процес
        error_log("Email notification error: " . $e->getMessage());
    }
    
    // Логування активності
    if (isLoggedIn()) {
        logActivity('consultation_request', "Відправлено заявку на консультацію: $service", [
            'consultation_id' => $consultationId,
            'service' => $service
        ]);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Заявку успішно відправлено! Ми зв\'яжемося з вами найближчим часом.',
        'consultation_id' => $consultationId
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

function getRealIpAddress() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

function getServiceName($serviceCode) {
    $services = [
        'smm' => 'SMM просування',
        'seo' => 'SEO оптимізація',
        'web' => 'Створення сайту',
        'design' => 'Дизайн та брендинг',
        'complex' => 'Комплексне просування'
    ];
    
    return $services[$serviceCode] ?? $serviceCode;
}

function sendConsultationNotification($consultationId, $data) {
    $db = Database::getInstance();
    
    // Отримуємо email адміністратора
    $stmt = $db->prepare("SELECT value FROM site_settings WHERE setting_key = 'contact_email' LIMIT 1");
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $adminEmail = $result['value'] ?? 'admin@adboardpro.com';
    
    $subject = 'Нова заявка на консультацію - AdBoard Pro';
    
    $message = "
    <h2>Нова заявка на консультацію</h2>
    <p><strong>ID заявки:</strong> #$consultationId</p>
    
    <h3>Контактні дані:</h3>
    <ul>
        <li><strong>Ім'я:</strong> {$data['name']}</li>
        <li><strong>Телефон:</strong> {$data['phone']}</li>
        <li><strong>Email:</strong> {$data['email']}</li>
    </ul>
    
    <h3>Деталі:</h3>
    <ul>
        <li><strong>Послуга:</strong> {$data['service']}</li>
        <li><strong>Повідомлення:</strong> {$data['message']}</li>
    </ul>
    
    <p><strong>Дата:</strong> " . date('d.m.Y H:i') . "</p>
    
    <hr>
    <p>Для управління заявками перейдіть в <a href='" . getSiteUrl('admin') . "'>адмін панель</a></p>
    ";
    
    // TODO: Реалізувати відправку email
    // sendEmail($adminEmail, $subject, $message);
    
    return true;
}

function logActivity($action, $description, $data = []) {
    if (!isLoggedIn()) {
        return;
    }
    
    try {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            INSERT INTO activity_logs (user_id, action, description, data, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ");
        
        $userId = $_SESSION['user_id'];
        $dataJson = json_encode($data);
        $stmt->bind_param("isss", $userId, $action, $description, $dataJson);
        $stmt->execute();
    } catch (Exception $e) {
        // Ігноруємо помилки логування
    }
}
?>