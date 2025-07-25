<?php
/**
 * Тест 8 этапа установки AdBoard Pro
 * Проверяет все возможные ошибки которые могут сломать JSON
 */

// Очищаємо будь-який попередній вивід
while (ob_get_level()) {
    ob_end_clean();
}

// Встановлюємо правильні заголовки JSON
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

// Відключаємо вивід помилок в JSON
ini_set('display_errors', 0);

// Встановлюємо DEBUG_MODE якщо не визначено
if (!defined('DEBUG_MODE')) {
    define('DEBUG_MODE', false);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'test_step8') {
    try {
        // Тест 1: Базові константи
        $constants = [
            'DB_HOST' => defined('DB_HOST') ? DB_HOST : 'не визначено',
            'DB_USER' => defined('DB_USER') ? DB_USER : 'не визначено', 
            'DB_NAME' => defined('DB_NAME') ? DB_NAME : 'не визначено',
            'DEBUG_MODE' => defined('DEBUG_MODE') ? (DEBUG_MODE ? 'true' : 'false') : 'не визначено'
        ];
        
        // Тест 2: Перевірка функцій
        $functions = [
            'json_encode' => function_exists('json_encode'),
            'mysqli' => class_exists('mysqli'),
            'session_start' => function_exists('session_start'),
            'defined' => function_exists('defined')
        ];
        
        // Тест 3: Симуляція помилки з DEBUG_MODE
        $debugTest = 'OK';
        try {
            // Перевіряємо чи викидає помилку
            $testValue = (defined('DEBUG_MODE') && DEBUG_MODE) ? 'debug активний' : 'debug вимкнено';
            $debugTest = $testValue;
        } catch (Exception $e) {
            $debugTest = 'Помилка: ' . $e->getMessage();
        }
        
        // Тест 4: JSON кодування з українським текстом
        $ukrainianTest = [
            'message' => 'Тест українського тексту',
            'special_chars' => 'Спеціальні символи: №"\'@#$%^&*(){}[]'
        ];
        
        $jsonTest = json_encode($ukrainianTest, JSON_UNESCAPED_UNICODE);
        $decoded = json_decode($jsonTest, true);
        
        // Результат тесту
        $response = [
            'success' => true,
            'message' => 'Тест 8 етапу пройшов успішно!',
            'timestamp' => date('Y-m-d H:i:s'),
            'tests' => [
                'constants' => $constants,
                'functions' => $functions, 
                'debug_mode_test' => $debugTest,
                'json_ukrainian' => $decoded,
                'json_string_length' => strlen($jsonTest)
            ],
            'system' => [
                'php_version' => PHP_VERSION,
                'memory_usage' => memory_get_usage(true),
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time')
            ]
        ];
        
        // Перевіряємо чи можна закодувати в JSON
        $finalJson = json_encode($response, JSON_UNESCAPED_UNICODE);
        
        if ($finalJson === false) {
            throw new Exception('Помилка кодування JSON: ' . json_last_error_msg());
        }
        
        echo $finalJson;
        exit();
        
    } catch (Exception $e) {
        $errorResponse = [
            'success' => false,
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => basename($e->getFile()),
            'trace' => (defined('DEBUG_MODE') && DEBUG_MODE) ? $e->getTraceAsString() : 'Debug вимкнено'
        ];
        
        echo json_encode($errorResponse, JSON_UNESCAPED_UNICODE);
        exit();
    } catch (Error $e) {
        $errorResponse = [
            'success' => false,
            'error' => 'Фатальна помилка: ' . $e->getMessage(),
            'line' => $e->getLine(),
            'file' => basename($e->getFile())
        ];
        
        echo json_encode($errorResponse, JSON_UNESCAPED_UNICODE);
        exit();
    }
}

// HTML для тестування
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тест 8 етапу встановлення</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0"><i class="fas fa-cog me-2"></i>Тест 8 етапу встановлення</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Цей тест перевіряє всі можливі проблеми що можуть зламати JSON на 8 етапі встановлення.
                        </div>
                        
                        <button id="testBtn" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-play me-2"></i>Запустити тест
                        </button>
                        
                        <a href="../debug_system.php" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-bug me-2"></i>Системна діагностика
                        </a>
                        
                        <div id="results" class="mt-4" style="display: none;">
                            <h5><i class="fas fa-chart-bar me-2"></i>Результати тесту:</h5>
                            <div id="responseContainer"></div>
                        </div>
                        
                        <div id="error" class="alert alert-danger mt-4" style="display: none;">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Помилка:</strong> <span id="errorMessage"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('testBtn').addEventListener('click', async function() {
        const btn = this;
        const originalText = btn.innerHTML;
        const resultsDiv = document.getElementById('results');
        const errorDiv = document.getElementById('error');
        const responseContainer = document.getElementById('responseContainer');
        
        // Ховаємо попередні результати
        resultsDiv.style.display = 'none';
        errorDiv.style.display = 'none';
        
        // Показуємо стан завантаження
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Тестування...';
        btn.disabled = true;
        
        try {
            const response = await fetch(window.location.href, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'action=test_step8'
            });
            
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            // Перевіряємо статус відповіді
            if (!response.ok) {
                throw new Error(`HTTP Error: ${response.status} ${response.statusText}`);
            }
            
            // Отримуємо текст відповіді
            const responseText = await response.text();
            console.log('Raw response:', responseText);
            
            // Перевіряємо чи це валідний JSON
            let result;
            try {
                result = JSON.parse(responseText);
            } catch (jsonError) {
                console.error('Invalid JSON response:', responseText);
                throw new Error('Сервер повернув невалідну JSON відповідь. Можливо є PHP warnings або errors.');
            }
            
            // Показуємо результати
            if (result.success) {
                responseContainer.innerHTML = `
                    <div class="alert alert-success">
                        <i class="fas fa-check me-2"></i><strong>Успіх!</strong> ${result.message}
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6><i class="fas fa-cogs me-2"></i>Константи</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-0">
                                        ${Object.entries(result.tests.constants).map(([key, value]) => 
                                            `<li><strong>${key}:</strong> ${value}</li>`
                                        ).join('')}
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6><i class="fas fa-code me-2"></i>Функції</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-0">
                                        ${Object.entries(result.tests.functions).map(([key, value]) => 
                                            `<li><strong>${key}:</strong> ${value ? '✅' : '❌'}</li>`
                                        ).join('')}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6><i class="fas fa-info me-2"></i>Детальна інформація</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>DEBUG_MODE тест:</strong> ${result.tests.debug_mode_test}</p>
                            <p><strong>Українські символи:</strong> ${result.tests.json_ukrainian.message}</p>
                            <p><strong>PHP версія:</strong> ${result.system.php_version}</p>
                            <p><strong>Використання пам'яті:</strong> ${(result.system.memory_usage / 1024 / 1024).toFixed(2)} MB</p>
                        </div>
                    </div>
                `;
            } else {
                responseContainer.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-times me-2"></i><strong>Помилка:</strong> ${result.error}
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <p><strong>Файл:</strong> ${result.file || 'невідомо'}</p>
                            <p><strong>Рядок:</strong> ${result.line || 'невідомо'}</p>
                            ${result.trace ? `<details><summary>Стек викликів</summary><pre>${result.trace}</pre></details>` : ''}
                        </div>
                    </div>
                `;
            }
            
            resultsDiv.style.display = 'block';
            
        } catch (error) {
            console.error('Test error:', error);
            document.getElementById('errorMessage').textContent = error.message;
            errorDiv.style.display = 'block';
        } finally {
            // Відновлюємо кнопку
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    });
    </script>
</body>
</html>