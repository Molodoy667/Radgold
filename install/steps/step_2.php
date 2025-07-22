<?php
$requirements = checkSystemRequirements();
$missingFiles = checkFiles();
$allRequirementsMet = !in_array(false, $requirements, true);
$allFilesPresent = empty($missingFiles);
$canProceed = $allRequirementsMet && $allFilesPresent;
?>

<div class="text-center mb-4">
    <i class="fas fa-clipboard-check fa-3x text-primary mb-3"></i>
    <h3>Перевірка системи</h3>
    <p class="text-muted">Перевіряємо системні вимоги, наявність файлів та права доступу</p>
</div>

<!-- Системні вимоги -->
<div class="mb-4">
    <h5><i class="fas fa-server me-2"></i>Системні вимоги</h5>
    <?php foreach ($requirements as $requirement => $status): ?>
        <div class="requirement-item <?php echo $status ? 'requirement-ok' : 'requirement-error'; ?> animate__animated animate__slideInLeft"
             style="animation-delay: <?php echo array_search($requirement, array_keys($requirements)) * 0.1; ?>s">
            <i class="fas <?php echo $status ? 'fa-check-circle' : 'fa-times-circle'; ?> me-2"></i>
            <strong><?php echo $requirement; ?></strong>
            <?php if (!$status): ?>
                <div class="mt-1">
                    <small class="text-muted">
                        <?php
                        if (strpos($requirement, 'extension') !== false) {
                            echo 'Встановіть це розширення PHP на вашому сервері';
                        } elseif (strpos($requirement, 'версія') !== false) {
                            echo 'Оновіть PHP до версії 7.4 або новішої';
                        } elseif (strpos($requirement, 'запису') !== false) {
                            echo 'Надайте права запису (chmod 755) для цієї директорії';
                        } else {
                            echo 'Увімкніть цю функцію у налаштуваннях PHP';
                        }
                        ?>
                    </small>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<!-- Перевірка файлів -->
<div class="mb-4">
    <h5><i class="fas fa-folder-open me-2"></i>Перевірка файлів</h5>
    <?php if ($allFilesPresent): ?>
        <div class="requirement-item requirement-ok">
            <i class="fas fa-check-circle me-2"></i>
            <strong>Всі необхідні файли присутні</strong>
            <div class="mt-1">
                <small class="text-muted">Знайдено всі файли, необхідні для роботи системи</small>
            </div>
        </div>
    <?php else: ?>
        <div class="requirement-item requirement-error">
            <i class="fas fa-times-circle me-2"></i>
            <strong>Відсутні файли системи</strong>
            <div class="mt-2">
                <small class="text-muted">Відсутні наступні файли:</small>
                <ul class="mt-1 mb-0">
                    <?php foreach ($missingFiles as $file): ?>
                        <li><code><?php echo $file; ?></code></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Автоматичне створення директорій -->
<div class="mb-4">
    <h5><i class="fas fa-folder-plus me-2"></i>Створення директорій</h5>
    <?php
    $directories = [
        '../images/uploads' => 'Директорія для завантажених зображень',
        '../images/thumbs' => 'Директорія для мініатюр зображень',
        '../images/avatars' => 'Директорія для аватарів користувачів',
        '../logs' => 'Директорія для логів системи'
    ];
    
    $directoriesCreated = 0;
    foreach ($directories as $dir => $description):
        $exists = is_dir($dir);
        $created = false;
        
        if (!$exists) {
            $created = @mkdir($dir, 0755, true);
            if ($created) {
                $directoriesCreated++;
            }
        } else {
            $directoriesCreated++;
        }
        
        $status = $exists || $created;
    ?>
        <div class="requirement-item <?php echo $status ? 'requirement-ok' : 'requirement-error'; ?>">
            <i class="fas <?php echo $status ? 'fa-check-circle' : 'fa-times-circle'; ?> me-2"></i>
            <strong><?php echo basename($dir); ?>/</strong>
            <div class="mt-1">
                <small class="text-muted"><?php echo $description; ?></small>
                <?php if ($created): ?>
                    <span class="badge bg-success ms-2">Створено</span>
                <?php elseif ($exists): ?>
                    <span class="badge bg-info ms-2">Існує</span>
                <?php else: ?>
                    <span class="badge bg-danger ms-2">Помилка створення</span>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Загальний статус -->
<div class="alert <?php echo $canProceed ? 'alert-success' : 'alert-warning'; ?>">
    <?php if ($canProceed): ?>
        <i class="fas fa-check-circle me-2"></i>
        <strong>Система готова до установки!</strong>
        <p class="mb-0 mt-2">Всі системні вимоги виконані, файли присутні та директорії створені. Можна продовжувати установку.</p>
    <?php else: ?>
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Система потребує налаштування</strong>
        <p class="mb-0 mt-2">Виправте вказані вище помилки перед продовженням установки. Оновіть сторінку після внесення змін.</p>
    <?php endif; ?>
</div>

<form method="POST">
    <div class="navigation-buttons">
        <a href="?step=1" class="btn btn-back">
            <i class="fas fa-arrow-left me-2"></i>Назад
        </a>
        
        <?php if ($canProceed): ?>
            <button type="submit" class="btn btn-next">
                Продовжити <i class="fas fa-arrow-right ms-2"></i>
            </button>
        <?php else: ?>
            <button type="button" class="btn btn-secondary" onclick="location.reload()">
                <i class="fas fa-sync-alt me-2"></i>Перевірити знову
            </button>
        <?php endif; ?>
    </div>
</form>

<script>
$(document).ready(function() {
    // Анімація елементів
    $('.requirement-item').each(function(index) {
        $(this).css('animation-delay', (index * 0.1) + 's');
    });
    
    // Автоматичне оновлення кожні 5 секунд якщо є помилки
    <?php if (!$canProceed): ?>
    let autoRefreshCount = 0;
    const maxAutoRefresh = 6; // 30 секунд максимум
    
    const refreshInterval = setInterval(function() {
        autoRefreshCount++;
        if (autoRefreshCount >= maxAutoRefresh) {
            clearInterval(refreshInterval);
            return;
        }
        
        // Показуємо індикатор оновлення
        const $indicator = $('<div class="text-center mt-3"><small class="text-muted"><i class="fas fa-sync fa-spin me-1"></i>Перевірка змін...</small></div>');
        $('.alert').after($indicator);
        
        setTimeout(() => {
            $indicator.remove();
            location.reload();
        }, 1000);
    }, 5000);
    <?php endif; ?>
});
</script>
