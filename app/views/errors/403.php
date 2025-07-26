<?php
ob_start();
?>

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary/20 to-secondary/20 py-12 px-4">
    <div class="max-w-md w-full text-center">
        <div class="mb-8">
            <div class="text-8xl mb-4">🚫</div>
            <h1 class="text-4xl font-bold text-foreground mb-2">403</h1>
            <h2 class="text-xl text-muted-foreground mb-4">Доступ запрещен</h2>
            <p class="text-muted-foreground">
                У вас нет прав для доступа к этой странице.
            </p>
        </div>
        
        <div class="space-y-4">
            <a href="/" class="btn-primary inline-block">
                <i class="icon-home mr-2"></i>
                На главную
            </a>
            <div class="text-sm text-muted-foreground">
                или <a href="/login" class="text-primary hover:text-primary/80">войдите в аккаунт</a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
?>