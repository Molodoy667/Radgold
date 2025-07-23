<?php
/**
 * Cron Jobs для AdBoard Pro
 * Запускати кожні 6 годин через crontab:
 * 0 */6 * * * /usr/bin/php /path/to/your/site/core/cron.php
 */

require_once 'config.php';
require_once 'functions.php';

// Логування запуску cron
file_put_contents('../logs/cron.log', date('Y-m-d H:i:s') . " - Cron job started\n", FILE_APPEND | LOCK_EX);

try {
    // 1. Архівація прострочених оголошень
    $expiredAds = archiveExpiredAds();
    
    // 2. Видалення застарілих архівних оголошень (старіше 1 року)
    $deletedAds = deleteOldArchivedAds();
    
    // 3. Очищення застарілих сесій та токенів
    cleanupExpiredSessions();
    
    // 4. Очищення застарілих пошукових запитів (старіше 6 місяців)
    cleanupOldSearchQueries();
    
    // 5. Оновлення статистики переглядів
    updateViewsStatistics();
    
    // 6. Відправка нагадувань про прострочені оголошення
    sendExpirationReminders();
    
    // Логування результатів
    $logMessage = sprintf(
        "Cron completed: %d ads archived, %d old ads deleted, sessions cleaned, views updated\n",
        $expiredAds,
        $deletedAds
    );
    
    file_put_contents('../logs/cron.log', date('Y-m-d H:i:s') . " - " . $logMessage, FILE_APPEND | LOCK_EX);
    
} catch (Exception $e) {
    $errorMessage = "Cron error: " . $e->getMessage() . "\n";
    file_put_contents('../logs/cron.log', date('Y-m-d H:i:s') . " - " . $errorMessage, FILE_APPEND | LOCK_EX);
    error_log("AdBoard Cron Error: " . $e->getMessage());
}

/**
 * Архівація прострочених оголошень
 */
function archiveExpiredAds() {
    try {
        $db = new Database();
        
        // Отримуємо максимальний термін дії оголошення з налаштувань
        $stmt = $db->prepare("SELECT value FROM site_settings WHERE setting_key = 'max_ad_duration_days'");
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $maxDays = (int)($result['value'] ?? 30);
        
        // Знаходимо прострочені оголошення
        $stmt = $db->prepare("
            SELECT id, title, user_id, created_at 
            FROM ads 
            WHERE status = 'active' 
            AND created_at < DATE_SUB(NOW(), INTERVAL ? DAY)
        ");
        $stmt->bind_param("i", $maxDays);
        $stmt->execute();
        $expiredAds = $stmt->get_result();
        
        $archivedCount = 0;
        
        while ($ad = $expiredAds->fetch_assoc()) {
            // Архівуємо оголошення
            $updateStmt = $db->prepare("
                UPDATE ads 
                SET status = 'expired', 
                    updated_at = NOW() 
                WHERE id = ?
            ");
            $updateStmt->bind_param("i", $ad['id']);
            
            if ($updateStmt->execute()) {
                $archivedCount++;
                
                // Логуємо активність
                logActivity($ad['user_id'], 'ad_expired', "Оголошення '{$ad['title']}' автоматично архівовано", [
                    'ad_id' => $ad['id'],
                    'expired_at' => date('Y-m-d H:i:s')
                ]);
                
                // Надсилаємо сповіщення користувачу
                sendAdExpirationNotification($ad['user_id'], $ad);
            }
        }
        
        return $archivedCount;
        
    } catch (Exception $e) {
        error_log("Error archiving expired ads: " . $e->getMessage());
        return 0;
    }
}

/**
 * Видалення старих архівних оголошень
 */
function deleteOldArchivedAds() {
    try {
        $db = new Database();
        
        // Знаходимо оголошення старіше 1 року в архіві
        $stmt = $db->prepare("
            SELECT id, title, user_id 
            FROM ads 
            WHERE status IN ('expired', 'rejected') 
            AND updated_at < DATE_SUB(NOW(), INTERVAL 1 YEAR)
        ");
        $stmt->execute();
        $oldAds = $stmt->get_result();
        
        $deletedCount = 0;
        
        while ($ad = $oldAds->fetch_assoc()) {
            // Видаляємо зображення оголошення
            deleteAdImages($ad['id']);
            
            // Видаляємо оголошення з бази
            $deleteStmt = $db->prepare("DELETE FROM ads WHERE id = ?");
            $deleteStmt->bind_param("i", $ad['id']);
            
            if ($deleteStmt->execute()) {
                $deletedCount++;
                
                // Логуємо активність
                logActivity($ad['user_id'], 'ad_deleted', "Старе оголошення '{$ad['title']}' видалено автоматично", [
                    'ad_id' => $ad['id'],
                    'deleted_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
        
        return $deletedCount;
        
    } catch (Exception $e) {
        error_log("Error deleting old archived ads: " . $e->getMessage());
        return 0;
    }
}

/**
 * Очищення прострочених сесій та токенів
 */
function cleanupExpiredSessions() {
    try {
        $db = new Database();
        
        // Видаляємо прострочені токени "запам'ятати мене"
        $stmt = $db->prepare("DELETE FROM remember_tokens WHERE expires_at < NOW()");
        $stmt->execute();
        
        // Видаляємо прострочені токени скидання пароля
        $stmt = $db->prepare("DELETE FROM password_resets WHERE expires_at < NOW()");
        $stmt->execute();
        
        // Очищаємо старі логи активності (старіше 6 місяців)
        $stmt = $db->prepare("DELETE FROM activity_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 6 MONTH)");
        $stmt->execute();
        
    } catch (Exception $e) {
        error_log("Error cleaning up expired sessions: " . $e->getMessage());
    }
}

/**
 * Очищення старих пошукових запитів
 */
function cleanupOldSearchQueries() {
    try {
        $db = new Database();
        
        // Видаляємо пошукові запити старіше 6 місяців
        $stmt = $db->prepare("DELETE FROM search_queries WHERE created_at < DATE_SUB(NOW(), INTERVAL 6 MONTH)");
        $stmt->execute();
        
    } catch (Exception $e) {
        error_log("Error cleaning up old search queries: " . $e->getMessage());
    }
}

/**
 * Оновлення статистики переглядів
 */
function updateViewsStatistics() {
    try {
        $db = new Database();
        
        // Агрегуємо денну статистику переглядів
        $stmt = $db->prepare("
            INSERT INTO daily_stats (date, total_views, unique_visitors, ads_created, ads_activated)
            SELECT 
                DATE(created_at) as date,
                COUNT(*) as total_views,
                COUNT(DISTINCT ip_address) as unique_visitors,
                0 as ads_created,
                0 as ads_activated
            FROM ad_views 
            WHERE DATE(created_at) = CURDATE() - INTERVAL 1 DAY
            ON DUPLICATE KEY UPDATE
                total_views = VALUES(total_views),
                unique_visitors = VALUES(unique_visitors)
        ");
        $stmt->execute();
        
        // Очищаємо старі записи переглядів (старіше 1 місяця)
        $stmt = $db->prepare("DELETE FROM ad_views WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 MONTH)");
        $stmt->execute();
        
    } catch (Exception $e) {
        error_log("Error updating views statistics: " . $e->getMessage());
    }
}

/**
 * Відправка нагадувань про закінчення терміну дії
 */
function sendExpirationReminders() {
    try {
        $db = new Database();
        
        // Знаходимо оголошення, які закінчуються через 3 дні
        $stmt = $db->prepare("
            SELECT a.id, a.title, a.user_id, u.email, u.full_name, a.created_at
            FROM ads a
            JOIN users u ON a.user_id = u.id
            WHERE a.status = 'active'
            AND DATE(a.created_at) = DATE_SUB(CURDATE(), INTERVAL 27 DAY)
            AND u.email_notifications = 1
        ");
        $stmt->execute();
        $expiringAds = $stmt->get_result();
        
        while ($ad = $expiringAds->fetch_assoc()) {
            sendExpirationReminderEmail($ad);
        }
        
    } catch (Exception $e) {
        error_log("Error sending expiration reminders: " . $e->getMessage());
    }
}

/**
 * Видалення зображень оголошення
 */
function deleteAdImages($adId) {
    try {
        $db = new Database();
        
        $stmt = $db->prepare("SELECT image_url FROM ad_images WHERE ad_id = ?");
        $stmt->bind_param("i", $adId);
        $stmt->execute();
        $images = $stmt->get_result();
        
        while ($image = $images->fetch_assoc()) {
            $imagePath = '../' . $image['image_url'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        
        // Видаляємо записи з бази
        $stmt = $db->prepare("DELETE FROM ad_images WHERE ad_id = ?");
        $stmt->bind_param("i", $adId);
        $stmt->execute();
        
    } catch (Exception $e) {
        error_log("Error deleting ad images: " . $e->getMessage());
    }
}

/**
 * Відправка сповіщення про закінчення терміну дії
 */
function sendAdExpirationNotification($userId, $ad) {
    try {
        $db = new Database();
        
        // Отримуємо дані користувача
        $stmt = $db->prepare("SELECT email, full_name, email_notifications FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        if ($user && $user['email_notifications']) {
            // TODO: Реалізувати відправку email
            // sendEmail($user['email'], 'Оголошення архівовано', generateExpirationEmailContent($ad, $user));
        }
        
    } catch (Exception $e) {
        error_log("Error sending expiration notification: " . $e->getMessage());
    }
}

/**
 * Відправка нагадування про закінчення терміну
 */
function sendExpirationReminderEmail($ad) {
    try {
        // TODO: Реалізувати відправку email нагадування
        // sendEmail($ad['email'], 'Оголошення закінчується через 3 дні', generateReminderEmailContent($ad));
        
    } catch (Exception $e) {
        error_log("Error sending expiration reminder: " . $e->getMessage());
    }
}

/**
 * Логування активності
 */
function logActivity($userId, $action, $description, $data = []) {
    try {
        $db = new Database();
        $stmt = $db->prepare("
            INSERT INTO activity_logs (user_id, action, description, data, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ");
        
        $dataJson = json_encode($data);
        $stmt->bind_param("isss", $userId, $action, $description, $dataJson);
        $stmt->execute();
        
    } catch (Exception $e) {
        // Ігноруємо помилки логування
    }
}

echo "Cron job completed successfully\n";
?>