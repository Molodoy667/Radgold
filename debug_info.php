<?php
/**
 * AdBoard Pro - Debug Information
 * Системна інформація та статистика проекту
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Функції для аналізу
function countFiles($dir, $extension = null) {
    if (!is_dir($dir)) return 0;
    
    $count = 0;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            if ($extension === null || pathinfo($file, PATHINFO_EXTENSION) === $extension) {
                $count++;
            }
        }
    }
    
    return $count;
}

function countDirectories($dir) {
    if (!is_dir($dir)) return 0;
    
    $count = 0;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    
    foreach ($iterator as $file) {
        if ($file->isDir() && !in_array($file->getBasename(), ['.', '..'])) {
            $count++;
        }
    }
    
    return $count;
}

function getDirectorySize($dir) {
    if (!is_dir($dir)) return 0;
    
    $size = 0;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $size += $file->getSize();
        }
    }
    
    return $size;
}

function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}

function countLinesInFile($file) {
    if (!file_exists($file)) return 0;
    return count(file($file));
}

function countCodeLines($dir, $extensions = ['php', 'js', 'css']) {
    $totalLines = 0;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            if (in_array($ext, $extensions)) {
                $totalLines += countLinesInFile($file);
            }
        }
    }
    
    return $totalLines;
}

// Збір статистики
$stats = [
    'project' => [
        'name' => 'AdBoard Pro',
        'version' => '1.0.0',
        'generated' => date('Y-m-d H:i:s'),
        'installed' => file_exists('.installed'),
        'install_date' => file_exists('.installed') ? date('Y-m-d H:i:s', filemtime('.installed')) : 'Не встановлено'
    ],
    
    'files' => [
        'total_files' => countFiles('.'),
        'php_files' => countFiles('.', 'php'),
        'js_files' => countFiles('.', 'js'),
        'css_files' => countFiles('.', 'css'),
        'sql_files' => countFiles('.', 'sql'),
        'total_directories' => countDirectories('.'),
        'project_size' => formatBytes(getDirectorySize('.'))
    ],
    
    'code' => [
        'total_lines' => countCodeLines('.'),
        'php_lines' => countCodeLines('.', ['php']),
        'js_lines' => countCodeLines('.', ['js']),
        'css_lines' => countCodeLines('.', ['css'])
    ],
    
    'structure' => [],
    
    'system' => [
        'php_version' => PHP_VERSION,
        'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'memory_limit' => ini_get('memory_limit'),
        'max_execution_time' => ini_get('max_execution_time'),
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size')
    ],
    
    'database' => [
        'config_exists' => file_exists('core/config.php'),
        'connection' => 'Не перевірено'
    ]
];

// Перевірка підключення до БД
if ($stats['database']['config_exists'] && $stats['project']['installed']) {
    try {
        require_once 'core/config.php';
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            $stats['database']['connection'] = 'Помилка: ' . $mysqli->connect_error;
        } else {
            $stats['database']['connection'] = 'Успішно';
            
            // Статистика таблиць
            $result = $mysqli->query("SHOW TABLES");
            $stats['database']['tables_count'] = $result ? $result->num_rows : 0;
            
            // Статистика користувачів
            $result = $mysqli->query("SELECT COUNT(*) as count FROM users");
            if ($result) {
                $row = $result->fetch_assoc();
                $stats['database']['users_count'] = $row['count'];
            }
            
            // Статистика оголошень
            $result = $mysqli->query("SELECT COUNT(*) as count FROM ads");
            if ($result) {
                $row = $result->fetch_assoc();
                $stats['database']['ads_count'] = $row['count'];
            }
            
            $mysqli->close();
        }
    } catch (Exception $e) {
        $stats['database']['connection'] = 'Помилка: ' . $e->getMessage();
    }
}

// Аналіз структури проекту
function analyzeDirectory($dir, $level = 0) {
    $structure = [];
    
    if (!is_dir($dir) || $level > 2) {
        return $structure;
    }
    
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        
        $path = $dir . '/' . $item;
        if (is_dir($path)) {
            $structure[$item] = [
                'type' => 'directory',
                'files' => countFiles($path),
                'size' => formatBytes(getDirectorySize($path)),
                'children' => $level < 2 ? analyzeDirectory($path, $level + 1) : []
            ];
        }
    }
    
    return $structure;
}

$stats['structure'] = analyzeDirectory('.');

?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AdBoard Pro - Debug Info</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .debug-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
        }
        
        .stat-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            margin-bottom: 1rem;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #333;
        }
        
        .structure-tree {
            font-family: 'Courier New', monospace;
        }
        
        .tree-item {
            padding: 0.25rem 0;
            border-left: 2px solid #e9ecef;
            margin-left: 1rem;
            padding-left: 1rem;
        }
        
        .tree-folder {
            color: #007bff;
            font-weight: bold;
        }
        
        .status-ok { color: #28a745; }
        .status-error { color: #dc3545; }
        .status-warning { color: #ffc107; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="debug-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="mb-0">
                        <i class="fas fa-bug me-3"></i>
                        AdBoard Pro - Debug Information
                    </h1>
                    <p class="mb-0 opacity-75">Системна інформація та статистика проекту</p>
                </div>
                <div class="col-auto">
                    <div class="text-end">
                        <div class="h4 mb-0">v<?php echo $stats['project']['version']; ?></div>
                        <small><?php echo $stats['project']['generated']; ?></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <!-- Статус встановлення -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert <?php echo $stats['project']['installed'] ? 'alert-success' : 'alert-warning'; ?>">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas <?php echo $stats['project']['installed'] ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?> fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">
                                Статус встановлення: 
                                <?php echo $stats['project']['installed'] ? 'Встановлено' : 'Не встановлено'; ?>
                            </h5>
                            <p class="mb-0">
                                <?php if ($stats['project']['installed']): ?>
                                    Сайт встановлено: <?php echo $stats['project']['install_date']; ?>
                                <?php else: ?>
                                    Для роботи сайту потрібно пройти процес встановлення в папці <code>/install/</code>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Статистика файлів -->
        <div class="row mb-4">
            <div class="col-12">
                <h3><i class="fas fa-file-code me-2"></i>Статистика файлів</h3>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <div class="stat-icon mx-auto" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                            <i class="fas fa-files-o"></i>
                        </div>
                        <div class="stat-value"><?php echo number_format($stats['files']['total_files']); ?></div>
                        <div class="text-muted">Всього файлів</div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <div class="stat-icon mx-auto" style="background: linear-gradient(135deg, #f093fb, #f5576c);">
                            <i class="fab fa-php"></i>
                        </div>
                        <div class="stat-value"><?php echo number_format($stats['files']['php_files']); ?></div>
                        <div class="text-muted">PHP файлів</div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <div class="stat-icon mx-auto" style="background: linear-gradient(135deg, #4facfe, #00f2fe);">
                            <i class="fas fa-folder"></i>
                        </div>
                        <div class="stat-value"><?php echo number_format($stats['files']['total_directories']); ?></div>
                        <div class="text-muted">Директорій</div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <div class="stat-icon mx-auto" style="background: linear-gradient(135deg, #fa709a, #fee140);">
                            <i class="fas fa-hdd"></i>
                        </div>
                        <div class="stat-value"><?php echo $stats['files']['project_size']; ?></div>
                        <div class="text-muted">Розмір проекту</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Статистика коду -->
        <div class="row mb-4">
            <div class="col-12">
                <h3><i class="fas fa-code me-2"></i>Статистика коду</h3>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h6>Рядки коду</h6>
                        <div class="row">
                            <div class="col-6">
                                <div class="d-flex justify-content-between">
                                    <span>Всього:</span>
                                    <strong><?php echo number_format($stats['code']['total_lines']); ?></strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>PHP:</span>
                                    <strong><?php echo number_format($stats['code']['php_lines']); ?></strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex justify-content-between">
                                    <span>JavaScript:</span>
                                    <strong><?php echo number_format($stats['code']['js_lines']); ?></strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>CSS:</span>
                                    <strong><?php echo number_format($stats['code']['css_lines']); ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h6>Типи файлів</h6>
                        <div class="row">
                            <div class="col-6">
                                <div class="d-flex justify-content-between">
                                    <span>PHP:</span>
                                    <strong><?php echo $stats['files']['php_files']; ?></strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>JavaScript:</span>
                                    <strong><?php echo $stats['files']['js_files']; ?></strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex justify-content-between">
                                    <span>CSS:</span>
                                    <strong><?php echo $stats['files']['css_files']; ?></strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>SQL:</span>
                                    <strong><?php echo $stats['files']['sql_files']; ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Системна інформація -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-server me-2"></i>Системна інформація</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td>PHP версія:</td>
                                <td><strong><?php echo $stats['system']['php_version']; ?></strong></td>
                            </tr>
                            <tr>
                                <td>Веб-сервер:</td>
                                <td><?php echo $stats['system']['server']; ?></td>
                            </tr>
                            <tr>
                                <td>Memory limit:</td>
                                <td><?php echo $stats['system']['memory_limit']; ?></td>
                            </tr>
                            <tr>
                                <td>Max execution time:</td>
                                <td><?php echo $stats['system']['max_execution_time']; ?>s</td>
                            </tr>
                            <tr>
                                <td>Upload max filesize:</td>
                                <td><?php echo $stats['system']['upload_max_filesize']; ?></td>
                            </tr>
                            <tr>
                                <td>Post max size:</td>
                                <td><?php echo $stats['system']['post_max_size']; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-database me-2"></i>База даних</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td>Конфігурація:</td>
                                <td>
                                    <i class="fas <?php echo $stats['database']['config_exists'] ? 'fa-check text-success' : 'fa-times text-danger'; ?>"></i>
                                    <?php echo $stats['database']['config_exists'] ? 'Існує' : 'Відсутня'; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Підключення:</td>
                                <td>
                                    <span class="<?php echo strpos($stats['database']['connection'], 'Успішно') !== false ? 'status-ok' : 'status-error'; ?>">
                                        <?php echo $stats['database']['connection']; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php if (isset($stats['database']['tables_count'])): ?>
                            <tr>
                                <td>Кількість таблиць:</td>
                                <td><strong><?php echo $stats['database']['tables_count']; ?></strong></td>
                            </tr>
                            <tr>
                                <td>Користувачі:</td>
                                <td><strong><?php echo $stats['database']['users_count'] ?? 0; ?></strong></td>
                            </tr>
                            <tr>
                                <td>Оголошення:</td>
                                <td><strong><?php echo $stats['database']['ads_count'] ?? 0; ?></strong></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Структура проекту -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-sitemap me-2"></i>Структура проекту</h5>
                    </div>
                    <div class="card-body">
                        <div class="structure-tree">
                            <?php foreach ($stats['structure'] as $name => $info): ?>
                                <div class="tree-item">
                                    <i class="fas fa-folder text-warning me-2"></i>
                                    <span class="tree-folder"><?php echo $name; ?></span>
                                    <small class="text-muted">
                                        (<?php echo $info['files']; ?> файлів, <?php echo $info['size']; ?>)
                                    </small>
                                    
                                    <?php if (!empty($info['children'])): ?>
                                        <?php foreach ($info['children'] as $childName => $childInfo): ?>
                                            <div class="tree-item ms-3">
                                                <i class="fas fa-folder text-warning me-2"></i>
                                                <span class="tree-folder"><?php echo $childName; ?></span>
                                                <small class="text-muted">
                                                    (<?php echo $childInfo['files']; ?> файлів, <?php echo $childInfo['size']; ?>)
                                                </small>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Дії -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-tools me-2"></i>Швидкі дії</h5>
                    </div>
                    <div class="card-body">
                        <div class="btn-group me-2 mb-2">
                            <?php if (!$stats['project']['installed']): ?>
                                <a href="install/" class="btn btn-primary">
                                    <i class="fas fa-download me-2"></i>Встановити сайт
                                </a>
                            <?php else: ?>
                                <a href="index.php" class="btn btn-success">
                                    <i class="fas fa-home me-2"></i>Перейти на сайт
                                </a>
                                <a href="admin/" class="btn btn-info">
                                    <i class="fas fa-cog me-2"></i>Адмін панель
                                </a>
                            <?php endif; ?>
                        </div>
                        
                        <div class="btn-group me-2 mb-2">
                            <button class="btn btn-outline-secondary" onclick="location.reload()">
                                <i class="fas fa-sync me-2"></i>Оновити дані
                            </button>
                            <button class="btn btn-outline-danger" onclick="if(confirm('Видалити .installed файл?')) { fetch('?action=reset'); location.reload(); }">
                                <i class="fas fa-trash me-2"></i>Скинути встановлення
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Обробка дій
if (isset($_GET['action']) && $_GET['action'] === 'reset') {
    if (file_exists('.installed')) {
        unlink('.installed');
    }
    header('Location: debug_info.php');
    exit();
}
?>