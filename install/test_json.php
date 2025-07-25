<?php
/**
 * Тестовый файл для проверки JSON обработки в установке
 * Симулирует 8 этап установки для отладки
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'test') {
    try {
        // Симулюємо роботу установки
        
        // Тест 1: Базова обробка
        $response = [
            'success' => true,
            'message' => 'Тест JSON обробки пройшов успішно',
            'timestamp' => date('Y-m-d H:i:s'),
            'server_info' => [
                'php_version' => PHP_VERSION,
                'json_extension' => extension_loaded('json'),
                'mysqli_extension' => extension_loaded('mysqli')
            ]
        ];
        
        // Перевіряємо чи можна закодувати в JSON
        $jsonString = json_encode($response, JSON_UNESCAPED_UNICODE);
        
        if ($jsonString === false) {
            throw new Exception('Помилка кодування JSON: ' . json_last_error_msg());
        }
        
        echo $jsonString;
        exit();
        
    } catch (Exception $e) {
        $errorResponse = [
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
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
    <title>Тест JSON для установки</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0"><i class="fas fa-vial me-2"></i>Тест JSON обробки</h3>
                    </div>
                    <div class="card-body">
                        <p class="mb-4">Цей тест перевіряє чи правильно працює JSON обробка як в 8 етапі установки.</p>
                        
                        <button id="testBtn" class="btn btn-primary btn-lg">
                            <i class="fas fa-play me-2"></i>Запустити тест
                        </button>
                        
                        <div id="results" class="mt-4" style="display: none;">
                            <h5>Результати тесту:</h5>
                            <div id="responseContainer"></div>
                        </div>
                        
                        <div id="error" class="alert alert-danger mt-4" style="display: none;">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <span id="errorMessage"></span>
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
                body: 'action=test'
            });
            
            // Перевіряємо статус відповіді
            if (!response.ok) {
                throw new Error(`HTTP Error: ${response.status} ${response.statusText}`);
            }
            
            // Отримуємо текст відповіді
            const responseText = await response.text();
            
            // Перевіряємо чи це валідний JSON
            let result;
            try {
                result = JSON.parse(responseText);
            } catch (jsonError) {
                console.error('Invalid JSON response:', responseText);
                throw new Error('Сервер повернув невалідну JSON відповідь: ' + responseText.substring(0, 200));
            }
            
            // Показуємо результати
            if (result.success) {
                responseContainer.innerHTML = `
                    <div class="alert alert-success">
                        <i class="fas fa-check me-2"></i><strong>Успіх!</strong> ${result.message}
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <h6>Інформація сервера:</h6>
                            <pre class="bg-light p-3 rounded">${JSON.stringify(result, null, 2)}</pre>
                        </div>
                    </div>
                `;
            } else {
                responseContainer.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-times me-2"></i><strong>Помилка:</strong> ${result.error}
                    </div>
                `;
            }
            
            resultsDiv.style.display = 'block';
            
        } catch (error) {
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