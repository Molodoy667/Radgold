<?php
/**
 * Файл перевірки налаштування проекту
 * Перевіряє всі необхідні компоненти для роботи дошки оголошень
 */

// Початок виводу HTML
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Перевірка налаштування - Дошка Оголошень</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">
                            <i class="fas fa-cogs me-2"></i>
                            Перевірка налаштування проекту
                        </h3>
                    </div>
                    <div class="card-body">
                        
                        <?php
                        $checks = [];
                        $overall_status = true;
                        
                        // Перевірка версії PHP
                        $php_version = phpversion();
                        $php_ok = version_compare($php_version, '7.3.0', '>=');
                        $checks[] = [
                            'name' => 'Версія PHP',
                            'status' => $php_ok,
                            'message' => "PHP $php_version" . ($php_ok ? ' ✓' : ' (потрібно 7.3+)'),
                            'critical' => true
                        ];
                        if (!$php_ok) $overall_status = false;
                        
                        // Перевірка розширень PHP
                        $extensions = ['pdo', 'pdo_mysql', 'gd', 'fileinfo', 'mbstring'];
                        foreach ($extensions as $ext) {
                            $loaded = extension_loaded($ext);
                            $checks[] = [
                                'name' => "Розширення $ext",
                                'status' => $loaded,
                                'message' => $loaded ? 'Встановлено ✓' : 'Не встановлено ✗',
                                'critical' => true
                            ];
                            if (!$loaded) $overall_status = false;
                        }
                        
                        // Перевірка файлів конфігурації
                        $config_files = [
                            'config/config.php' => 'Основна конфігурація',
                            'config/database.php' => 'Конфігурація бази даних',
                            'database.sql' => 'SQL схема'
                        ];
                        
                        foreach ($config_files as $file => $desc) {
                            $exists = file_exists($file);
                            $checks[] = [
                                'name' => $desc,
                                'status' => $exists,
                                'message' => $exists ? 'Знайдено ✓' : 'Відсутній ✗',
                                'critical' => true
                            ];
                            if (!$exists) $overall_status = false;
                        }
                        
                        // Перевірка папок
                        $directories = [
                            'assets/uploads' => 'Папка завантажень',
                            'assets/css' => 'Папка CSS',
                            'assets/js' => 'Папка JavaScript',
                            'pages' => 'Папка сторінок',
                            'includes' => 'Папка включень'
                        ];
                        
                        foreach ($directories as $dir => $desc) {
                            $exists = is_dir($dir);
                            $writable = $exists ? is_writable($dir) : false;
                            
                            if ($dir === 'assets/uploads') {
                                $status = $exists && $writable;
                                $message = $exists ? 
                                    ($writable ? 'Існує та доступна для запису ✓' : 'Існує, але недоступна для запису ⚠') :
                                    'Не існує ✗';
                            } else {
                                $status = $exists;
                                $message = $exists ? 'Існує ✓' : 'Не існує ✗';
                            }
                            
                            $checks[] = [
                                'name' => $desc,
                                'status' => $status,
                                'message' => $message,
                                'critical' => $dir === 'assets/uploads'
                            ];
                            
                            if ($dir === 'assets/uploads' && !$status) $overall_status = false;
                        }
                        
                        // Перевірка підключення до бази даних
                        try {
                            require_once 'config/database.php';
                            $database = new Database();
                            $db = $database->getConnection();
                            
                            if ($db) {
                                $checks[] = [
                                    'name' => 'Підключення до БД',
                                    'status' => true,
                                    'message' => 'Успішне підключення ✓',
                                    'critical' => true
                                ];
                                
                                // Перевірка таблиць
                                $tables = ['users', 'categories', 'ads', 'ad_images', 'favorites'];
                                foreach ($tables as $table) {
                                    try {
                                        $stmt = $db->query("SELECT 1 FROM $table LIMIT 1");
                                        $checks[] = [
                                            'name' => "Таблиця $table",
                                            'status' => true,
                                            'message' => 'Існує ✓',
                                            'critical' => false
                                        ];
                                    } catch (PDOException $e) {
                                        $checks[] = [
                                            'name' => "Таблиця $table",
                                            'status' => false,
                                            'message' => 'Не існує ✗',
                                            'critical' => false
                                        ];
                                    }
                                }
                            }
                        } catch (Exception $e) {
                            $checks[] = [
                                'name' => 'Підключення до БД',
                                'status' => false,
                                'message' => 'Помилка підключення: ' . $e->getMessage(),
                                'critical' => true
                            ];
                            $overall_status = false;
                        }
                        
                        // Відображення результатів
                        ?>
                        
                        <div class="alert <?php echo $overall_status ? 'alert-success' : 'alert-danger'; ?> mb-4">
                            <h5 class="mb-2">
                                <?php if ($overall_status): ?>
                                    <i class="fas fa-check-circle me-2"></i>Все готово!
                                <?php else: ?>
                                    <i class="fas fa-exclamation-triangle me-2"></i>Потрібні додаткові налаштування
                                <?php endif; ?>
                            </h5>
                            <p class="mb-0">
                                <?php if ($overall_status): ?>
                                    Ваш проект налаштовано правильно і готовий до використання.
                                <?php else: ?>
                                    Деякі критичні компоненти відсутні або неправильно налаштовані.
                                <?php endif; ?>
                            </p>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Компонент</th>
                                        <th>Статус</th>
                                        <th>Опис</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($checks as $check): ?>
                                        <tr class="<?php echo $check['status'] ? 'table-success' : ($check['critical'] ? 'table-danger' : 'table-warning'); ?>">
                                            <td>
                                                <?php echo $check['name']; ?>
                                                <?php if ($check['critical'] && !$check['status']): ?>
                                                    <span class="badge bg-danger ms-1">Критично</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($check['status']): ?>
                                                    <span class="text-success"><i class="fas fa-check"></i></span>
                                                <?php else: ?>
                                                    <span class="text-danger"><i class="fas fa-times"></i></span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo $check['message']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <?php if (!$overall_status): ?>
                            <div class="alert alert-info mt-4">
                                <h6><i class="fas fa-info-circle me-2"></i>Інструкції по виправленню:</h6>
                                <ul class="mb-0">
                                    <li>Переконайтесь, що встановлено PHP 7.3 або вище</li>
                                    <li>Встановіть необхідні розширення PHP</li>
                                    <li>Створіть базу даних та імпортуйте файл database.sql</li>
                                    <li>Налаштуйте параметри підключення в config/database.php</li>
                                    <li>Встановіть права 755 для папки assets/uploads</li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-success mt-4">
                                <h6><i class="fas fa-rocket me-2"></i>Готово до запуску!</h6>
                                <p class="mb-3">Ваш проект готовий. Ви можете:</p>
                                <div class="d-grid gap-2 d-md-flex">
                                    <a href="index.php" class="btn btn-success">
                                        <i class="fas fa-home me-2"></i>Перейти на сайт
                                    </a>
                                    <a href="pages/register.php" class="btn btn-primary">
                                        <i class="fas fa-user-plus me-2"></i>Зареєструватися
                                    </a>
                                    <a href="pages/login.php" class="btn btn-outline-primary">
                                        <i class="fas fa-sign-in-alt me-2"></i>Увійти
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                    </div>
                </div>
                
                <!-- Інформація про проект -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-info me-2"></i>Інформація про проект
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Технології:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fab fa-php me-2"></i>PHP 7.3+</li>
                                    <li><i class="fas fa-database me-2"></i>MySQL 5.7+</li>
                                    <li><i class="fab fa-bootstrap me-2"></i>Bootstrap 5.3</li>
                                    <li><i class="fab fa-js me-2"></i>JavaScript ES6</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Функції:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-users me-2"></i>Система користувачів</li>
                                    <li><i class="fas fa-bullhorn me-2"></i>Розміщення оголошень</li>
                                    <li><i class="fas fa-search me-2"></i>Пошук та фільтри</li>
                                    <li><i class="fas fa-heart me-2"></i>Система вподобань</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>