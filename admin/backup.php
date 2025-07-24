<?php
require_once '../core/config.php';
require_once '../core/functions.php';

// Перевірка прав доступу
if (!isLoggedIn() || !isAdmin()) {
    header('Location: login.php');
    exit();
}

$error = '';
$success = '';

// Обробка дій
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Невірний CSRF токен';
    } else {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'create_backup':
                try {
                    $backupType = $_POST['backup_type'] ?? 'database';
                    $backupId = createBackup($backupType);
                    $success = "Резервна копія створена успішно! ID: $backupId";
                } catch (Exception $e) {
                    $error = $e->getMessage();
                }
                break;
                
            case 'delete_backup':
                try {
                    $backupId = (int)$_POST['backup_id'];
                    deleteBackup($backupId);
                    $success = 'Резервна копія видалена успішно!';
                } catch (Exception $e) {
                    $error = $e->getMessage();
                }
                break;
                
            case 'download_backup':
                try {
                    $backupId = (int)$_POST['backup_id'];
                    downloadBackup($backupId);
                    exit(); // Файл завантажується
                } catch (Exception $e) {
                    $error = $e->getMessage();
                }
                break;
        }
    }
}

// Отримання списку резервних копій
try {
    $db = Database::getInstance();
    $stmt = $db->prepare("
        SELECT b.*, u.first_name, u.last_name 
        FROM backups b 
        LEFT JOIN users u ON b.created_by = u.id 
        ORDER BY b.started_at DESC 
        LIMIT 50
    ");
    $stmt->execute();
    $backups = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Статистика
    $stmt = $db->prepare("
        SELECT 
            COUNT(*) as total_backups,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_backups,
            SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_backups,
            SUM(CASE WHEN status = 'running' THEN 1 ELSE 0 END) as running_backups,
            SUM(file_size) as total_size
        FROM backups 
        WHERE started_at > DATE_SUB(NOW(), INTERVAL 30 DAY)
    ");
    $stmt->execute();
    $stats = $stmt->get_result()->fetch_assoc();
    
} catch (Exception $e) {
    $error = "Помилка завантаження даних: " . $e->getMessage();
    $backups = [];
    $stats = ['total_backups' => 0, 'completed_backups' => 0, 'failed_backups' => 0, 'running_backups' => 0, 'total_size' => 0];
}

include 'header.php';
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-download me-2"></i>Резервні копії
            </h1>
            <p class="text-muted mb-0">Управління резервними копіями системи</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBackupModal">
                <i class="fas fa-plus me-1"></i>Створити backup
            </button>
            <button type="button" class="btn btn-info" onclick="checkBackupSettings()">
                <i class="fas fa-cog me-1"></i>Налаштування
            </button>
        </div>
    </div>

    <!-- Alerts -->
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-uppercase mb-1">Всього копій</div>
                            <div class="h5 mb-0"><?php echo $stats['total_backups']; ?></div>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-archive fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-uppercase mb-1">Успішних</div>
                            <div class="h5 mb-0"><?php echo $stats['completed_backups']; ?></div>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-uppercase mb-1">З помилками</div>
                            <div class="h5 mb-0"><?php echo $stats['failed_backups']; ?></div>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-uppercase mb-1">Загальний розмір</div>
                            <div class="h5 mb-0"><?php echo formatFileSize($stats['total_size']); ?></div>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-hdd fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Backup List -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Список резервних копій
            </h5>
        </div>
        <div class="card-body">
            <?php if (empty($backups)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Резервних копій поки немає</h5>
                    <p class="text-muted">Створіть першу резервну копію для збереження даних</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Тип</th>
                                <th>Файл</th>
                                <th>Розмір</th>
                                <th>Статус</th>
                                <th>Створено</th>
                                <th>Автор</th>
                                <th>Дії</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($backups as $backup): ?>
                                <tr>
                                    <td><?php echo $backup['id']; ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo getBackupTypeBadge($backup['backup_type']); ?>">
                                            <?php echo getBackupTypeLabel($backup['backup_type']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-monospace"><?php echo htmlspecialchars($backup['filename']); ?></small>
                                    </td>
                                    <td><?php echo $backup['file_size'] ? formatFileSize($backup['file_size']) : '-'; ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo getStatusBadge($backup['status']); ?>">
                                            <?php echo getStatusLabel($backup['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small>
                                            <?php echo date('d.m.Y H:i', strtotime($backup['started_at'])); ?>
                                            <?php if ($backup['completed_at']): ?>
                                                <br><span class="text-success">Завершено: <?php echo date('H:i', strtotime($backup['completed_at'])); ?></span>
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php if ($backup['first_name']): ?>
                                            <small><?php echo htmlspecialchars($backup['first_name'] . ' ' . $backup['last_name']); ?></small>
                                        <?php else: ?>
                                            <small class="text-muted">Система</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <?php if ($backup['status'] === 'completed' && file_exists($backup['file_path'])): ?>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                    <input type="hidden" name="action" value="download_backup">
                                                    <input type="hidden" name="backup_id" value="<?php echo $backup['id']; ?>">
                                                    <button type="submit" class="btn btn-outline-primary" title="Завантажити">
                                                        <i class="fas fa-download"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <button type="button" class="btn btn-outline-info" onclick="showBackupDetails(<?php echo $backup['id']; ?>)" title="Деталі">
                                                <i class="fas fa-info"></i>
                                            </button>
                                            
                                            <form method="POST" class="d-inline" onsubmit="return confirm('Видалити цю резервну копію?')">
                                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <input type="hidden" name="action" value="delete_backup">
                                                <input type="hidden" name="backup_id" value="<?php echo $backup['id']; ?>">
                                                <button type="submit" class="btn btn-outline-danger" title="Видалити">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Create Backup Modal -->
<div class="modal fade" id="createBackupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus me-2"></i>Створити резервну копію
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="action" value="create_backup">
                    
                    <div class="mb-3">
                        <label class="form-label">Тип резервної копії</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="backup_type" value="database" id="type-db" checked>
                            <label class="form-check-label" for="type-db">
                                <strong>База даних</strong>
                                <div class="small text-muted">Тільки структура та дані БД</div>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="backup_type" value="files" id="type-files">
                            <label class="form-check-label" for="type-files">
                                <strong>Файли</strong>
                                <div class="small text-muted">Тільки файли проекту</div>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="backup_type" value="full" id="type-full">
                            <label class="form-check-label" for="type-full">
                                <strong>Повна</strong>
                                <div class="small text-muted">База даних + файли (рекомендовано)</div>
                            </label>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Увага!</strong> Створення повної резервної копії може зайняти деякий час в залежності від розміру даних.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-play me-1"></i>Створити backup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showBackupDetails(backupId) {
    fetch(`ajax/backup_details.php?id=${backupId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Деталі backup:\n' + JSON.stringify(data.backup, null, 2));
            } else {
                alert('Помилка: ' + data.message);
            }
        });
}

function checkBackupSettings() {
    // Перенаправлення на сторінку налаштувань
    window.location.href = 'settings.php#system';
}

// Автооновлення статусу кожні 10 секунд якщо є запущені backup'и
<?php if ($stats['running_backups'] > 0): ?>
    setInterval(() => {
        location.reload();
    }, 10000);
<?php endif; ?>
</script>

<?php
include 'footer.php';

// Допоміжні функції
function getBackupTypeBadge($type) {
    switch ($type) {
        case 'database': return 'primary';
        case 'files': return 'warning';
        case 'full': return 'success';
        default: return 'secondary';
    }
}

function getBackupTypeLabel($type) {
    switch ($type) {
        case 'database': return 'База даних';
        case 'files': return 'Файли';
        case 'full': return 'Повна';
        default: return ucfirst($type);
    }
}

function getStatusBadge($status) {
    switch ($status) {
        case 'completed': return 'success';
        case 'running': return 'warning';
        case 'failed': return 'danger';
        default: return 'secondary';
    }
}

function getStatusLabel($status) {
    switch ($status) {
        case 'completed': return 'Завершено';
        case 'running': return 'Виконується';
        case 'failed': return 'Помилка';
        default: return ucfirst($status);
    }
}

function formatFileSize($bytes) {
    if ($bytes == 0) return '0 B';
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = floor(log($bytes) / log(1024));
    return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
}

function createBackup($type) {
    global $_SESSION;
    
    $db = Database::getInstance();
    $filename = 'backup_' . $type . '_' . date('Y-m-d_H-i-s') . '.zip';
    $filePath = '../backups/' . $filename;
    
    // Створюємо запис в БД
    $stmt = $db->prepare("
        INSERT INTO backups (filename, file_path, backup_type, status, created_by) 
        VALUES (?, ?, ?, 'running', ?)
    ");
    $stmt->execute([$filename, $filePath, $type, $_SESSION['user_id']]);
    $backupId = $db->lastInsertId();
    
    try {
        // Створюємо директорію якщо не існує
        if (!is_dir('../backups/')) {
            mkdir('../backups/', 0755, true);
        }
        
        $zip = new ZipArchive();
        if ($zip->open($filePath, ZipArchive::CREATE) !== TRUE) {
            throw new Exception('Не вдалося створити ZIP файл');
        }
        
        if ($type === 'database' || $type === 'full') {
            // Backup бази даних
            $dumpFile = '../backups/database_' . date('Y-m-d_H-i-s') . '.sql';
            createDatabaseDump($dumpFile);
            $zip->addFile($dumpFile, 'database.sql');
        }
        
        if ($type === 'files' || $type === 'full') {
            // Backup файлів
            addFilesToZip($zip, '../', '', ['backups', 'temp', 'logs', '.git', 'node_modules']);
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
        
        logActivity($_SESSION['user_id'], 'backup_created', 'Створено резервну копію', [
            'backup_id' => $backupId,
            'type' => $type,
            'size' => $fileSize
        ]);
        
        return $backupId;
        
    } catch (Exception $e) {
        // Оновлюємо статус помилки
        $stmt = $db->prepare("
            UPDATE backups 
            SET status = 'failed', error_message = ?, completed_at = NOW() 
            WHERE id = ?
        ");
        $stmt->execute([$e->getMessage(), $backupId]);
        
        throw $e;
    }
}

function createDatabaseDump($filename) {
    $db = Database::getInstance();
    
    $tables = [];
    $result = $db->query("SHOW TABLES");
    while ($row = $result->fetch_row()) {
        $tables[] = $row[0];
    }
    
    $output = "-- AdBoard Pro Database Backup\n";
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

function deleteBackup($backupId) {
    global $_SESSION;
    
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT * FROM backups WHERE id = ?");
    $stmt->execute([$backupId]);
    $backup = $stmt->get_result()->fetch_assoc();
    
    if (!$backup) {
        throw new Exception('Резервну копію не знайдено');
    }
    
    // Видаляємо файл
    if (file_exists($backup['file_path'])) {
        unlink($backup['file_path']);
    }
    
    // Видаляємо запис з БД
    $stmt = $db->prepare("DELETE FROM backups WHERE id = ?");
    $stmt->execute([$backupId]);
    
    logActivity($_SESSION['user_id'], 'backup_deleted', 'Видалено резервну копію', [
        'backup_id' => $backupId,
        'filename' => $backup['filename']
    ]);
}

function downloadBackup($backupId) {
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT * FROM backups WHERE id = ? AND status = 'completed'");
    $stmt->execute([$backupId]);
    $backup = $stmt->get_result()->fetch_assoc();
    
    if (!$backup || !file_exists($backup['file_path'])) {
        throw new Exception('Файл резервної копії не знайдено');
    }
    
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . $backup['filename'] . '"');
    header('Content-Length: ' . filesize($backup['file_path']));
    readfile($backup['file_path']);
    
    logActivity($_SESSION['user_id'], 'backup_downloaded', 'Завантажено резервну копію', [
        'backup_id' => $backupId,
        'filename' => $backup['filename']
    ]);
}
?>