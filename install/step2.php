<?php
// Перевірка системних вимог
$requirements = [];
$all_ok = true;

// Версія PHP
$php_version = phpversion();
$php_ok = version_compare($php_version, '7.3.0', '>=');
$requirements[] = [
    'name' => 'Версія PHP',
    'required' => '7.3.0 або вище',
    'current' => $php_version,
    'status' => $php_ok ? 'success' : 'error'
];
if (!$php_ok) $all_ok = false;

// Розширення PHP
$extensions = [
    'pdo' => 'PDO',
    'pdo_mysql' => 'PDO MySQL',
    'gd' => 'GD Library',
    'fileinfo' => 'File Info',
    'mbstring' => 'Multibyte String',
    'json' => 'JSON',
    'session' => 'Session'
];

foreach ($extensions as $ext => $name) {
    $loaded = extension_loaded($ext);
    $requirements[] = [
        'name' => "Розширення $name",
        'required' => 'Увімкнено',
        'current' => $loaded ? 'Увімкнено' : 'Вимкнено',
        'status' => $loaded ? 'success' : 'error'
    ];
    if (!$loaded) $all_ok = false;
}

// Перевірка папок та прав
$directories = [
    '../config/' => 'Конфігурація',
    '../assets/uploads/' => 'Завантаження файлів',
    '../assets/css/' => 'CSS файли',
    '../assets/js/' => 'JavaScript файли'
];

foreach ($directories as $dir => $desc) {
    $exists = is_dir($dir);
    $writable = $exists ? is_writable($dir) : false;
    
    if ($dir === '../config/' || $dir === '../assets/uploads/') {
        $status = $exists && $writable ? 'success' : 'error';
        $current = $exists ? ($writable ? 'Доступна для запису' : 'Тільки для читання') : 'Не існує';
        if (!$exists || !$writable) $all_ok = false;
    } else {
        $status = $exists ? 'success' : 'warning';
        $current = $exists ? 'Існує' : 'Не існує';
    }
    
    $requirements[] = [
        'name' => "Папка $desc",
        'required' => $dir === '../config/' || $dir === '../assets/uploads/' ? 'Запис дозволено' : 'Існує',
        'current' => $current,
        'status' => $status
    ];
}

// Функції PHP
$functions = [
    'file_get_contents' => 'Читання файлів',
    'file_put_contents' => 'Запис файлів',
    'curl_init' => 'cURL (рекомендовано)',
    'imagecreatetruecolor' => 'Обробка зображень'
];

foreach ($functions as $func => $desc) {
    $exists = function_exists($func);
    $requirements[] = [
        'name' => "Функція $desc",
        'required' => $func === 'curl_init' || $func === 'imagecreatetruecolor' ? 'Рекомендовано' : 'Обов\'язково',
        'current' => $exists ? 'Доступна' : 'Недоступна',
        'status' => $exists ? 'success' : ($func === 'curl_init' || $func === 'imagecreatetruecolor' ? 'warning' : 'error')
    ];
    if (!$exists && $func !== 'curl_init' && $func !== 'imagecreatetruecolor') {
        $all_ok = false;
    }
}

// Налаштування PHP
$php_settings = [
    'file_uploads' => ['Завантаження файлів', true],
    'allow_url_fopen' => ['Відкриття URL', true]
];

foreach ($php_settings as $setting => $info) {
    $current_value = ini_get($setting);
    $is_ok = $current_value == $info[1];
    $requirements[] = [
        'name' => "Налаштування {$info[0]}",
        'required' => $info[1] ? 'Увімкнено' : 'Вимкнено',
        'current' => $current_value ? 'Увімкнено' : 'Вимкнено',
        'status' => $is_ok ? 'success' : 'warning'
    ];
}
?>

<div class="text-center mb-4">
    <i class="fas fa-cogs fa-4x <?php echo $all_ok ? 'text-success' : 'text-warning'; ?> mb-3"></i>
    <p class="lead">Перевірка системних вимог та налаштувань сервера.</p>
</div>

<?php if ($all_ok): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle me-2"></i>
        <strong>Відмінно!</strong> Всі системні вимоги виконано. Система готова до встановлення.
    </div>
<?php else: ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Увага!</strong> Деякі системні вимоги не виконано. Будь ласка, усуньте помилки перед продовженням.
    </div>
<?php endif; ?>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Вимога</th>
                <th>Необхідно</th>
                <th>Поточний стан</th>
                <th>Статус</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($requirements as $req): ?>
                <tr class="requirement <?php echo $req['status']; ?>">
                    <td><strong><?php echo $req['name']; ?></strong></td>
                    <td><?php echo $req['required']; ?></td>
                    <td><?php echo $req['current']; ?></td>
                    <td>
                        <?php if ($req['status'] === 'success'): ?>
                            <i class="fas fa-check-circle text-success"></i> OK
                        <?php elseif ($req['status'] === 'warning'): ?>
                            <i class="fas fa-exclamation-triangle text-warning"></i> Попередження
                        <?php else: ?>
                            <i class="fas fa-times-circle text-danger"></i> Помилка
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if (!$all_ok): ?>
    <div class="alert alert-info mt-4">
        <h6><i class="fas fa-info-circle me-2"></i>Рекомендації для усунення помилок:</h6>
        <ul class="mb-0">
            <li><strong>PHP версія:</strong> Оновіть PHP до версії 7.3 або вище</li>
            <li><strong>Розширення PHP:</strong> Увімкніть необхідні розширення у php.ini</li>
            <li><strong>Права доступу:</strong> Встановіть права 755 або 777 для папок config/ та assets/uploads/</li>
            <li><strong>Функції PHP:</strong> Переконайтесь, що функції не заблоковані в disable_functions</li>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" class="mt-4">
    <div class="d-flex justify-content-between">
        <a href="?step=1" class="btn btn-secondary btn-lg">
            <i class="fas fa-arrow-left me-2"></i>Назад
        </a>
        <button type="submit" name="requirements_ok" class="btn btn-primary btn-lg" <?php echo !$all_ok ? 'disabled' : ''; ?>>
            <i class="fas fa-arrow-right me-2"></i>Далі
        </button>
    </div>
</form>

<?php if (!$all_ok): ?>
    <div class="text-center mt-3">
        <button type="button" class="btn btn-outline-primary" onclick="window.location.reload()">
            <i class="fas fa-sync-alt me-2"></i>Перевірити знову
        </button>
    </div>
<?php endif; ?>