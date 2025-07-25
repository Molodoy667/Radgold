<?php
namespace App\Core;

class ErrorHandler {
    public static function register() {
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }
    
    public static function handleError($level, $message, $file = '', $line = 0) {
        if (error_reporting() !== 0) {
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }
    
    public static function handleException($exception) {
        // Логируем ошибку
        Logger::exception($exception);
        
        // Показываем ошибку в зависимости от режима
        if ($_ENV['APP_DEBUG'] ?? false) {
            self::renderDebugException($exception);
        } else {
            self::renderProductionException($exception);
        }
    }
    
    public static function handleShutdown() {
        $error = error_get_last();
        
        if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
            self::handleError($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }
    
    private static function renderDebugException($exception) {
        http_response_code(500);
        
        echo '<!DOCTYPE html>';
        echo '<html lang="ru">';
        echo '<head>';
        echo '<meta charset="UTF-8">';
        echo '<title>Ошибка - Game Marketplace</title>';
        echo '<style>';
        echo 'body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }';
        echo '.error-container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }';
        echo '.error-title { color: #e74c3c; font-size: 24px; margin-bottom: 10px; }';
        echo '.error-message { background: #f8f9fa; padding: 15px; border-radius: 4px; margin: 10px 0; }';
        echo '.error-trace { background: #f8f9fa; padding: 15px; border-radius: 4px; font-family: monospace; font-size: 12px; white-space: pre-wrap; }';
        echo '</style>';
        echo '</head>';
        echo '<body>';
        echo '<div class="error-container">';
        echo '<div class="error-title">🚨 Ошибка приложения</div>';
        echo '<div class="error-message"><strong>Сообщение:</strong> ' . htmlspecialchars($exception->getMessage()) . '</div>';
        echo '<div class="error-message"><strong>Файл:</strong> ' . htmlspecialchars($exception->getFile()) . ':' . $exception->getLine() . '</div>';
        echo '<div class="error-trace"><strong>Стек вызовов:</strong><br>' . htmlspecialchars($exception->getTraceAsString()) . '</div>';
        echo '</div>';
        echo '</body>';
        echo '</html>';
    }
    
    private static function renderProductionException($exception) {
        http_response_code(500);
        
        echo '<!DOCTYPE html>';
        echo '<html lang="ru">';
        echo '<head>';
        echo '<meta charset="UTF-8">';
        echo '<title>Ошибка сервера - Game Marketplace</title>';
        echo '<style>';
        echo 'body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; text-align: center; }';
        echo '.error-container { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 500px; margin: 0 auto; }';
        echo '.error-icon { font-size: 64px; margin-bottom: 20px; }';
        echo '.error-title { color: #e74c3c; font-size: 24px; margin-bottom: 15px; }';
        echo '.error-message { color: #666; margin-bottom: 20px; }';
        echo '.back-link { color: #3498db; text-decoration: none; }';
        echo '.back-link:hover { text-decoration: underline; }';
        echo '</style>';
        echo '</head>';
        echo '<body>';
        echo '<div class="error-container">';
        echo '<div class="error-icon">⚠️</div>';
        echo '<div class="error-title">Ошибка сервера</div>';
        echo '<div class="error-message">Произошла внутренняя ошибка сервера. Пожалуйста, попробуйте позже.</div>';
        echo '<a href="/" class="back-link">← Вернуться на главную</a>';
        echo '</div>';
        echo '</body>';
        echo '</html>';
    }
}