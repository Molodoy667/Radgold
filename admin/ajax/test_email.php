<?php
require_once '../../core/config.php';
require_once '../../core/functions.php';

header('Content-Type: application/json');

// Перевірка прав доступу
if (!isLoggedIn() || !isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Недостатньо прав доступу']);
    exit();
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['email']) || !isset($input['subject']) || !isset($input['message'])) {
        throw new Exception('Неповні дані');
    }
    
    $email = filter_var($input['email'], FILTER_VALIDATE_EMAIL);
    if (!$email) {
        throw new Exception('Невірний email адрес');
    }
    
    $subject = htmlspecialchars($input['subject']);
    $message = htmlspecialchars($input['message']);
    
    // Отримуємо налаштування SMTP
    $db = Database::getInstance();
    $stmt = $db->prepare("
        SELECT setting_key, setting_value 
        FROM site_settings 
        WHERE setting_key IN ('smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 'smtp_encryption', 'email_from_name', 'email_from_address')
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $settings = [];
    while ($row = $result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    
    // Перевіряємо чи налаштовано SMTP
    if (empty($settings['smtp_host']) || empty($settings['smtp_username'])) {
        throw new Exception('SMTP налаштування не сконфігуровано');
    }
    
    // Використовуємо PHPMailer для відправки
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
    
    $mail = new PHPMailer(true);
    
    // SMTP налаштування
    $mail->isSMTP();
    $mail->Host = $settings['smtp_host'];
    $mail->SMTPAuth = true;
    $mail->Username = $settings['smtp_username'];
    $mail->Password = $settings['smtp_password'];
    $mail->SMTPSecure = $settings['smtp_encryption'] === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = (int)($settings['smtp_port'] ?: 587);
    $mail->CharSet = 'UTF-8';
    
    // Відправник
    $fromEmail = $settings['email_from_address'] ?: $settings['smtp_username'];
    $fromName = $settings['email_from_name'] ?: 'AdBoard Pro';
    $mail->setFrom($fromEmail, $fromName);
    
    // Отримувач
    $mail->addAddress($email);
    
    // Контент
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = "
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>$subject</title>
        </head>
        <body>
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; border-radius: 10px 10px 0 0;'>
                    <h1 style='color: white; margin: 0; text-align: center;'>📧 Test Email</h1>
                </div>
                <div style='background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px;'>
                    <p style='font-size: 16px; line-height: 1.6; color: #333;'>
                        $message
                    </p>
                    <hr style='border: none; border-top: 1px solid #dee2e6; margin: 20px 0;'>
                    <p style='font-size: 14px; color: #6c757d;'>
                        <strong>Час відправки:</strong> " . date('d.m.Y H:i:s') . "<br>
                        <strong>SMTP сервер:</strong> " . $settings['smtp_host'] . "<br>
                        <strong>Статус:</strong> ✅ Email налаштування працюють правильно!
                    </p>
                </div>
            </div>
        </body>
        </html>
    ";
    
    $mail->AltBody = strip_tags($message) . "\n\n---\nЧас відправки: " . date('d.m.Y H:i:s') . "\nSMTP сервер: " . $settings['smtp_host'];
    
    // Відправляємо
    $mail->send();
    
    // Логування
    logActivity($_SESSION['user_id'], 'email_test', 'Тест email налаштувань', [
        'recipient' => $email,
        'smtp_host' => $settings['smtp_host']
    ]);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Email відправлено успішно на ' . $email
    ]);
    
} catch (Exception $e) {
    error_log("Email test error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}
?>