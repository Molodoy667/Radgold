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
    $clearedItems = [];
    $errors = [];
    
    // Очищення файлового кешу
    $cacheDir = '../cache/';
    if (is_dir($cacheDir)) {
        $files = glob($cacheDir . '*');
        foreach ($files as $file) {
            if (is_file($file) && pathinfo($file, PATHINFO_EXTENSION) === 'cache') {
                if (unlink($file)) {
                    $clearedItems[] = 'Файл кешу: ' . basename($file);
                } else {
                    $errors[] = 'Не вдалося видалити: ' . basename($file);
                }
            }
        }
    }
    
    // Очищення тимчасових файлів
    $tempDir = '../temp/';
    if (is_dir($tempDir)) {
        $files = glob($tempDir . '*');
        foreach ($files as $file) {
            if (is_file($file) && (time() - filemtime($file)) > 3600) { // старше 1 години
                if (unlink($file)) {
                    $clearedItems[] = 'Тимчасовий файл: ' . basename($file);
                } else {
                    $errors[] = 'Не вдалося видалити тимчасовий файл: ' . basename($file);
                }
            }
        }
    }
    
    // Очищення логів старших 30 днів
    $logsDir = '../logs/';
    if (is_dir($logsDir)) {
        $files = glob($logsDir . '*.log');
        foreach ($files as $file) {
            if (is_file($file) && (time() - filemtime($file)) > (30 * 24 * 3600)) { // старше 30 днів
                if (unlink($file)) {
                    $clearedItems[] = 'Старий лог: ' . basename($file);
                } else {
                    $errors[] = 'Не вдалося видалити лог: ' . basename($file);
                }
            }
        }
    }
    
    // Очищення сесійних файлів PHP
    if (function_exists('session_gc')) {
        session_gc();
        $clearedItems[] = 'PHP сесії очищені';
    }
    
    // Очищення кешу OPcache якщо доступно
    if (function_exists('opcache_reset')) {
        opcache_reset();
        $clearedItems[] = 'OPcache очищено';
    }
    
    // Очищення статистики користувачів (застарілі записи)
    try {
        $db = Database::getInstance();
        
        // Видаляємо старі записи переглядів (старші 90 днів)
        $stmt = $db->prepare("DELETE FROM ad_views WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY)");
        $stmt->execute();
        $deletedViews = $stmt->affected_rows;
        if ($deletedViews > 0) {
            $clearedItems[] = "Видалено $deletedViews старих переглядів";
        }
        
        // Видаляємо старі логи активності (старші 60 днів)
        $stmt = $db->prepare("DELETE FROM activity_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 60 DAY)");
        $stmt->execute();
        $deletedLogs = $stmt->affected_rows;
        if ($deletedLogs > 0) {
            $clearedItems[] = "Видалено $deletedLogs старих логів";
        }
        
        // Видаляємо експірувані токени
        $stmt = $db->prepare("DELETE FROM remember_tokens WHERE expires_at < NOW()");
        $stmt->execute();
        $deletedTokens = $stmt->affected_rows;
        if ($deletedTokens > 0) {
            $clearedItems[] = "Видалено $deletedTokens експірованих токенів";
        }
        
        // Видаляємо старі скидання паролів
        $stmt = $db->prepare("DELETE FROM password_resets WHERE expires_at < NOW() OR created_at < DATE_SUB(NOW(), INTERVAL 7 DAY)");
        $stmt->execute();
        $deletedResets = $stmt->affected_rows;
        if ($deletedResets > 0) {
            $clearedItems[] = "Видалено $deletedResets старих скидань паролів";
        }
        
    } catch (Exception $e) {
        $errors[] = 'Помилка очистки БД: ' . $e->getMessage();
    }
    
    // Логування
    logActivity($_SESSION['user_id'], 'cache_cleared', 'Очищено кеш системи', [
        'cleared_items' => count($clearedItems),
        'errors' => count($errors),
        'items' => $clearedItems
    ]);
    
    if (empty($clearedItems) && empty($errors)) {
        echo json_encode([
            'success' => true, 
            'message' => 'Кеш був вже очищений або порожній'
        ]);
    } else {
        $message = '';
        if (!empty($clearedItems)) {
            $message .= 'Очищено: ' . count($clearedItems) . ' елементів';
        }
        if (!empty($errors)) {
            $message .= ($message ? '. ' : '') . 'Помилки: ' . count($errors);
        }
        
        echo json_encode([
            'success' => empty($errors) || !empty($clearedItems),
            'message' => $message,
            'details' => [
                'cleared' => $clearedItems,
                'errors' => $errors
            ]
        ]);
    }
    
} catch (Exception $e) {
    error_log("Cache clear error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Помилка очистки кешу: ' . $e->getMessage()
    ]);
}
?>