<?php
/**
 * Cron script для очистки старих backup'ів
 * Використання: php backup_cleanup.php
 */

// Запобігаємо виконанню через веб
if (isset($_SERVER['HTTP_HOST'])) {
    die('Цей скрипт може виконуватися тільки через командний рядок');
}

require_once '../core/config.php';
require_once '../core/functions.php';

try {
    echo "[" . date('Y-m-d H:i:s') . "] Початок очистки старих backup'ів...\n";
    
    $db = Database::getInstance();
    
    // Отримуємо налаштування retention
    $stmt = $db->prepare("SELECT setting_value FROM site_settings WHERE setting_key = 'backup_retention_days'");
    $stmt->execute();
    $result = $stmt->get_result();
    $retentionDays = (int)($result->fetch_row()[0] ?? 30);
    
    echo "[" . date('Y-m-d H:i:s') . "] Retention period: $retentionDays днів\n";
    
    // Знаходимо старі backup'и
    $stmt = $db->prepare("
        SELECT id, filename, file_path, file_size, started_at 
        FROM backups 
        WHERE started_at < DATE_SUB(NOW(), INTERVAL ? DAY)
        ORDER BY started_at ASC
    ");
    $stmt->execute([$retentionDays]);
    $oldBackups = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    $deletedCount = 0;
    $freedSpace = 0;
    $errors = [];
    
    foreach ($oldBackups as $backup) {
        try {
            echo "[" . date('Y-m-d H:i:s') . "] Видалення backup ID {$backup['id']}: {$backup['filename']}\n";
            
            // Видаляємо файл якщо існує
            if (file_exists($backup['file_path'])) {
                $freedSpace += $backup['file_size'];
                if (!unlink($backup['file_path'])) {
                    throw new Exception("Не вдалося видалити файл: {$backup['file_path']}");
                }
            }
            
            // Видаляємо запис з БД
            $stmt = $db->prepare("DELETE FROM backups WHERE id = ?");
            if (!$stmt->execute([$backup['id']])) {
                throw new Exception("Не вдалося видалити запис з БД");
            }
            
            $deletedCount++;
            echo "[" . date('Y-m-d H:i:s') . "] ✓ Видалено backup ID {$backup['id']}\n";
            
        } catch (Exception $e) {
            $error = "Помилка видалення backup ID {$backup['id']}: " . $e->getMessage();
            echo "[" . date('Y-m-d H:i:s') . "] ✗ $error\n";
            $errors[] = $error;
        }
    }
    
    // Очистка сирітських файлів (файли без записів в БД)
    echo "[" . date('Y-m-d H:i:s') . "] Пошук сирітських файлів...\n";
    
    $backupDir = '../backups/';
    if (is_dir($backupDir)) {
        $files = glob($backupDir . '*.zip');
        $orphanedCount = 0;
        $orphanedSpace = 0;
        
        foreach ($files as $file) {
            $filename = basename($file);
            
            // Перевіряємо чи існує запис в БД
            $stmt = $db->prepare("SELECT id FROM backups WHERE filename = ?");
            $stmt->execute([$filename]);
            
            if ($stmt->get_result()->num_rows === 0) {
                // Файл-сирота
                $fileSize = filesize($file);
                $fileAge = time() - filemtime($file);
                
                // Видаляємо тільки якщо файл старший за retention period
                if ($fileAge > ($retentionDays * 24 * 3600)) {
                    try {
                        if (unlink($file)) {
                            $orphanedCount++;
                            $orphanedSpace += $fileSize;
                            echo "[" . date('Y-m-d H:i:s') . "] ✓ Видалено сирітський файл: $filename\n";
                        }
                    } catch (Exception $e) {
                        echo "[" . date('Y-m-d H:i:s') . "] ✗ Помилка видалення $filename: " . $e->getMessage() . "\n";
                    }
                }
            }
        }
        
        if ($orphanedCount > 0) {
            echo "[" . date('Y-m-d H:i:s') . "] Видалено $orphanedCount сирітських файлів\n";
            $freedSpace += $orphanedSpace;
        }
    }
    
    // Очистка старих логів активності (старше 90 днів)
    echo "[" . date('Y-m-d H:i:s') . "] Очистка старих логів активності...\n";
    $stmt = $db->prepare("DELETE FROM activity_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY)");
    $stmt->execute();
    $deletedLogs = $stmt->affected_rows;
    
    if ($deletedLogs > 0) {
        echo "[" . date('Y-m-d H:i:s') . "] Видалено $deletedLogs старих записів логів\n";
    }
    
    // Очистка старих переглядів (старше 120 днів)
    echo "[" . date('Y-m-d H:i:s') . "] Очистка старих переглядів...\n";
    $stmt = $db->prepare("DELETE FROM ad_views WHERE created_at < DATE_SUB(NOW(), INTERVAL 120 DAY)");
    $stmt->execute();
    $deletedViews = $stmt->affected_rows;
    
    if ($deletedViews > 0) {
        echo "[" . date('Y-m-d H:i:s') . "] Видалено $deletedViews старих переглядів\n";
    }
    
    // Очистка експірованих токенів
    echo "[" . date('Y-m-d H:i:s') . "] Очистка експірованих токенів...\n";
    $stmt = $db->prepare("DELETE FROM remember_tokens WHERE expires_at < NOW()");
    $stmt->execute();
    $deletedTokens = $stmt->affected_rows;
    
    $stmt = $db->prepare("DELETE FROM password_resets WHERE expires_at < NOW() OR created_at < DATE_SUB(NOW(), INTERVAL 7 DAY)");
    $stmt->execute();
    $deletedResets = $stmt->affected_rows;
    
    if ($deletedTokens > 0 || $deletedResets > 0) {
        echo "[" . date('Y-m-d H:i:s') . "] Видалено $deletedTokens токенів та $deletedResets запитів скидання паролів\n";
    }
    
    // Підсумки
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "[" . date('Y-m-d H:i:s') . "] ПІДСУМКИ ОЧИСТКИ:\n";
    echo "✓ Видалено backup'ів: $deletedCount\n";
    echo "✓ Звільнено місця: " . formatFileSize($freedSpace) . "\n";
    echo "✓ Видалено логів: $deletedLogs\n";
    echo "✓ Видалено переглядів: $deletedViews\n";
    echo "✓ Видалено токенів: " . ($deletedTokens + $deletedResets) . "\n";
    
    if (!empty($errors)) {
        echo "\n⚠️  ПОМИЛКИ:\n";
        foreach ($errors as $error) {
            echo "- $error\n";
        }
    }
    
    // Логування результатів
    $stmt = $db->prepare("
        INSERT INTO activity_logs (action, description, data, created_at) 
        VALUES ('cleanup_performed', 'Автоматична очистка системи', ?, NOW())
    ");
    $stmt->execute([json_encode([
        'deleted_backups' => $deletedCount,
        'freed_space' => $freedSpace,
        'deleted_logs' => $deletedLogs,
        'deleted_views' => $deletedViews,
        'deleted_tokens' => $deletedTokens + $deletedResets,
        'errors_count' => count($errors),
        'automated' => true
    ])]);
    
    echo "\n[" . date('Y-m-d H:i:s') . "] Очистка завершена успішно!\n";
    exit(0);
    
} catch (Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] КРИТИЧНА ПОМИЛКА: " . $e->getMessage() . "\n";
    
    // Логування критичної помилки
    if (isset($db)) {
        try {
            $stmt = $db->prepare("
                INSERT INTO activity_logs (action, description, data, created_at) 
                VALUES ('cleanup_failed', 'Помилка автоматичної очистки', ?, NOW())
            ");
            $stmt->execute([json_encode([
                'error' => $e->getMessage(),
                'automated' => true
            ])]);
        } catch (Exception $logError) {
            echo "[" . date('Y-m-d H:i:s') . "] Не вдалося записати лог помилки: " . $logError->getMessage() . "\n";
        }
    }
    
    exit(1);
}

function formatFileSize($bytes) {
    if ($bytes == 0) return '0 B';
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = floor(log($bytes) / log(1024));
    return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
}
?>