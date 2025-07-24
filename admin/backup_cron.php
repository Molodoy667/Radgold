<?php
/**
 * Cron script для автоматичного створення backup'ів
 * Використання: php backup_cron.php [database|files|full]
 */

// Запобігаємо виконанню через веб
if (isset($_SERVER['HTTP_HOST'])) {
    die('Цей скрипт може виконуватися тільки через командний рядок');
}

require_once '../core/config.php';
require_once '../core/functions.php';

// Тип backup'у з аргументу командного рядка
$backupType = isset($argv[1]) ? $argv[1] : 'database';

// Перевіряємо валідність типу
if (!in_array($backupType, ['database', 'files', 'full'])) {
    echo "Помилка: Невірний тип backup'у. Доступні: database, files, full\n";
    exit(1);
}

try {
    echo "[" . date('Y-m-d H:i:s') . "] Початок створення $backupType backup'у...\n";
    
    // Отримуємо налаштування backup'у
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT setting_value FROM site_settings WHERE setting_key = 'backup_enabled'");
    $stmt->execute();
    $result = $stmt->get_result();
    $backupEnabled = $result->fetch_row()[0] ?? '0';
    
    if (!$backupEnabled) {
        echo "[" . date('Y-m-d H:i:s') . "] Backup відключений в налаштуваннях. Завершення.\n";
        exit(0);
    }
    
    // Створюємо backup
    $filename = 'auto_backup_' . $backupType . '_' . date('Y-m-d_H-i-s') . '.zip';
    $filePath = '../backups/' . $filename;
    
    // Створюємо запис в БД
    $stmt = $db->prepare("
        INSERT INTO backups (filename, file_path, backup_type, status, created_by) 
        VALUES (?, ?, ?, 'running', NULL)
    ");
    $stmt->execute([$filename, $filePath, $backupType]);
    $backupId = $db->lastInsertId();
    
    echo "[" . date('Y-m-d H:i:s') . "] Створено запис backup'у ID: $backupId\n";
    
    // Створюємо директорію якщо не існує
    if (!is_dir('../backups/')) {
        mkdir('../backups/', 0755, true);
        echo "[" . date('Y-m-d H:i:s') . "] Створено директорію backups/\n";
    }
    
    $zip = new ZipArchive();
    if ($zip->open($filePath, ZipArchive::CREATE) !== TRUE) {
        throw new Exception('Не вдалося створити ZIP файл');
    }
    
    if ($backupType === 'database' || $backupType === 'full') {
        echo "[" . date('Y-m-d H:i:s') . "] Створення dump'у бази даних...\n";
        
        // Backup бази даних
        $dumpFile = '../backups/database_' . date('Y-m-d_H-i-s') . '.sql';
        createDatabaseDump($dumpFile);
        $zip->addFile($dumpFile, 'database.sql');
        
        echo "[" . date('Y-m-d H:i:s') . "] Dump бази даних створено\n";
    }
    
    if ($backupType === 'files' || $backupType === 'full') {
        echo "[" . date('Y-m-d H:i:s') . "] Додавання файлів до архіву...\n";
        
        // Backup файлів
        addFilesToZip($zip, '../', '', ['backups', 'temp', 'logs', '.git', 'node_modules']);
        
        echo "[" . date('Y-m-d H:i:s') . "] Файли додано до архіву\n";
    }
    
    $zip->close();
    
    // Видаляємо тимчасовий dump якщо був створений
    if (isset($dumpFile) && file_exists($dumpFile)) {
        unlink($dumpFile);
    }
    
    // Оновлюємо статус
    $fileSize = filesize($filePath);
    $stmt = $db->prepare("
        UPDATE backups 
        SET status = 'completed', file_size = ?, completed_at = NOW() 
        WHERE id = ?
    ");
    $stmt->execute([$fileSize, $backupId]);
    
    echo "[" . date('Y-m-d H:i:s') . "] Backup створено успішно!\n";
    echo "[" . date('Y-m-d H:i:s') . "] Файл: $filename\n";
    echo "[" . date('Y-m-d H:i:s') . "] Розмір: " . formatFileSize($fileSize) . "\n";
    
    // Логування в системі
    $stmt = $db->prepare("
        INSERT INTO activity_logs (action, description, data, created_at) 
        VALUES ('backup_created', 'Автоматичне створення backup', ?, NOW())
    ");
    $stmt->execute([json_encode([
        'backup_id' => $backupId,
        'type' => $backupType,
        'size' => $fileSize,
        'automated' => true
    ])]);
    
    exit(0);
    
} catch (Exception $e) {
    $errorMessage = $e->getMessage();
    echo "[" . date('Y-m-d H:i:s') . "] ПОМИЛКА: $errorMessage\n";
    
    // Оновлюємо статус помилки якщо backup був створений
    if (isset($backupId)) {
        $stmt = $db->prepare("
            UPDATE backups 
            SET status = 'failed', error_message = ?, completed_at = NOW() 
            WHERE id = ?
        ");
        $stmt->execute([$errorMessage, $backupId]);
    }
    
    // Логування помилки
    if (isset($db)) {
        $stmt = $db->prepare("
            INSERT INTO activity_logs (action, description, data, created_at) 
            VALUES ('backup_failed', 'Помилка автоматичного backup', ?, NOW())
        ");
        $stmt->execute([json_encode([
            'error' => $errorMessage,
            'type' => $backupType,
            'automated' => true
        ])]);
    }
    
    exit(1);
}

// Допоміжні функції
function createDatabaseDump($filename) {
    $db = Database::getInstance();
    
    $tables = [];
    $result = $db->query("SHOW TABLES");
    while ($row = $result->fetch_row()) {
        $tables[] = $row[0];
    }
    
    $output = "-- AdBoard Pro Automated Database Backup\n";
    $output .= "-- Created: " . date('Y-m-d H:i:s') . "\n\n";
    $output .= "SET FOREIGN_KEY_CHECKS=0;\n";
    $output .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
    $output .= "SET AUTOCOMMIT = 0;\n";
    $output .= "START TRANSACTION;\n\n";
    
    foreach ($tables as $table) {
        // Структура таблиці
        $result = $db->query("SHOW CREATE TABLE `$table`");
        $row = $result->fetch_row();
        $output .= "DROP TABLE IF EXISTS `$table`;\n";
        $output .= $row[1] . ";\n\n";
        
        // Дані таблиці
        $result = $db->query("SELECT * FROM `$table`");
        if ($result->num_rows > 0) {
            $output .= "INSERT INTO `$table` VALUES\n";
            $rows = [];
            while ($row = $result->fetch_row()) {
                $row = array_map(function($value) use ($db) {
                    return $value === null ? 'NULL' : "'" . $db->real_escape_string($value) . "'";
                }, $row);
                $rows[] = '(' . implode(',', $row) . ')';
            }
            $output .= implode(",\n", $rows) . ";\n\n";
        }
    }
    
    $output .= "SET FOREIGN_KEY_CHECKS=1;\n";
    $output .= "COMMIT;\n";
    
    file_put_contents($filename, $output);
}

function addFilesToZip($zip, $dir, $zipDir = '', $exclude = []) {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir),
        RecursiveIteratorIterator::LEAVES_ONLY
    );
    
    foreach ($files as $file) {
        if (!$file->isDir()) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($dir));
            
            // Пропускаємо виключені директорії
            $skip = false;
            foreach ($exclude as $excludeDir) {
                if (strpos($relativePath, $excludeDir) === 0) {
                    $skip = true;
                    break;
                }
            }
            
            if (!$skip) {
                $zip->addFile($filePath, $zipDir . $relativePath);
            }
        }
    }
}

function formatFileSize($bytes) {
    if ($bytes == 0) return '0 B';
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = floor(log($bytes) / log(1024));
    return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
}
?>