<?php
// Загальні функції сайту

function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}

function getRoute() {
    return isset($_GET['route']) ? sanitize($_GET['route']) : '';
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function generateToken() {
    return bin2hex(random_bytes(32));
}

function verifyToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function uploadFile($file, $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'], $maxSize = MAX_FILE_SIZE) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Upload error occurred.');
    }
    
    if ($file['size'] > $maxSize) {
        throw new Exception('File too large.');
    }
    
    $fileInfo = pathinfo($file['name']);
    $extension = strtolower($fileInfo['extension']);
    
    if (!in_array($extension, $allowedTypes)) {
        throw new Exception('File type not allowed.');
    }
    
    $fileName = uniqid() . '.' . $extension;
    $uploadPath = UPLOAD_PATH . $fileName;
    
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        throw new Exception('Failed to move uploaded file.');
    }
    
    return $fileName;
}

function deleteFile($fileName) {
    $filePath = UPLOAD_PATH . $fileName;
    if (file_exists($filePath)) {
        unlink($filePath);
    }
}

function formatDate($date, $format = 'd.m.Y H:i') {
    return date($format, strtotime($date));
}

function truncateText($text, $length = 100) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

function generateSlug($text) {
    $text = preg_replace('/[^a-zA-Zа-яА-Я0-9\s]/', '', $text);
    $text = preg_replace('/\s+/', '-', trim($text));
    return strtolower($text);
}

function getSiteSettings() {
    $db = Database::getInstance();
    $result = $db->query("SELECT * FROM site_settings WHERE id = 1");
    return $result->fetch_assoc();
}

function updateSiteSetting($key, $value) {
    $db = Database::getInstance();
    return $db->update(
        "UPDATE site_settings SET `$key` = ? WHERE id = 1",
        [$value]
    );
}

function getThemeSettings() {
    $db = Database::getInstance();
    $result = $db->query("SELECT * FROM theme_settings WHERE id = 1");
    return $result->fetch_assoc();
}

function updateThemeSetting($key, $value) {
    $db = Database::getInstance();
    return $db->update(
        "UPDATE theme_settings SET `$key` = ? WHERE id = 1",
        [$value]
    );
}

function jsonResponse($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

function generateGradients() {
    return [
        'gradient-1' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        'gradient-2' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
        'gradient-3' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
        'gradient-4' => 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
        'gradient-5' => 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
        'gradient-6' => 'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)',
        'gradient-7' => 'linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%)',
        'gradient-8' => 'linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%)',
        'gradient-9' => 'linear-gradient(135deg, #ff8a80 0%, #ea80fc 100%)',
        'gradient-10' => 'linear-gradient(135deg, #8fd3f4 0%, #84fab0 100%)',
        'gradient-11' => 'linear-gradient(135deg, #d299c2 0%, #fef9d7 100%)',
        'gradient-12' => 'linear-gradient(135deg, #89f7fe 0%, #66a6ff 100%)',
        'gradient-13' => 'linear-gradient(135deg, #fdbb2d 0%, #22c1c3 100%)',
        'gradient-14' => 'linear-gradient(135deg, #e0c3fc 0%, #9bb5ff 100%)',
        'gradient-15' => 'linear-gradient(135deg, #ffeef8 0%, #f8e1ff 100%)',
        'gradient-16' => 'linear-gradient(135deg, #ffd89b 0%, #19547b 100%)',
        'gradient-17' => 'linear-gradient(135deg, #a7c0cd 0%, #f7f0ac 100%)',
        'gradient-18' => 'linear-gradient(135deg, #96fbc4 0%, #f9f047 100%)',
        'gradient-19' => 'linear-gradient(135deg, #ffeaa7 0%, #fab1a0 100%)',
        'gradient-20' => 'linear-gradient(135deg, #74b9ff 0%, #0984e3 100%)',
        'gradient-21' => 'linear-gradient(135deg, #fd79a8 0%, #fdcb6e 100%)',
        'gradient-22' => 'linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%)',
        'gradient-23' => 'linear-gradient(135deg, #55efc4 0%, #81ecec 100%)',
        'gradient-24' => 'linear-gradient(135deg, #ff7675 0%, #fd79a8 100%)',
        'gradient-25' => 'linear-gradient(135deg, #fdcb6e 0%, #e17055 100%)',
        'gradient-26' => 'linear-gradient(135deg, #00b894 0%, #00cec9 100%)',
        'gradient-27' => 'linear-gradient(135deg, #6c5ce7 0%, #74b9ff 100%)',
        'gradient-28' => 'linear-gradient(135deg, #fd79a8 0%, #fdcb6e 100%)',
        'gradient-29' => 'linear-gradient(135deg, #e17055 0%, #fab1a0 100%)',
        'gradient-30' => 'linear-gradient(135deg, #00cec9 0%, #55efc4 100%)'
    ];
}

function getMetaTags() {
    $settings = getSiteSettings();
    return [
        'title' => $settings['site_title'] ?? SITE_NAME,
        'description' => $settings['site_description'] ?? SITE_DESCRIPTION,
        'keywords' => $settings['site_keywords'] ?? SITE_KEYWORDS,
        'author' => $settings['site_author'] ?? 'AdBoard Pro',
        'favicon' => $settings['favicon'] ?? 'images/favicon.ico',
        'logo' => $settings['logo'] ?? 'images/logo.png'
    ];
}
?>
