<?php
// Крок 7: Процес встановлення
?>

<div class="step-content animate__animated animate__fadeIn">
    <div class="step-header text-center">
        <h3><i class="fas fa-rocket me-3"></i>Встановлення AdBoard Pro</h3>
        <p class="text-muted">Будь ласка, зачекайте поки система налаштовується...</p>
    </div>

    <div class="installation-container">
        <!-- Основний прогрес -->
        <div class="main-progress mb-5">
            <div class="progress-circle">
                <div class="progress-inner">
                    <div class="progress-percentage" id="mainProgress">0%</div>
                </div>
                <svg class="progress-svg" width="200" height="200">
                    <circle class="progress-circle-bg" cx="100" cy="100" r="90"></circle>
                    <circle class="progress-circle-fill" cx="100" cy="100" r="90" id="progressCircle"></circle>
                </svg>
            </div>
            <h4 class="mt-3 mb-1 current-action" id="currentAction">Ініціалізація установки...</h4>
            <p class="text-muted current-description" id="currentDescription">Підготовка до встановлення системи</p>
        </div>

        <!-- Детальний прогрес -->
        <div class="installation-steps">
            <div class="step-item" id="step-1">
                <div class="step-icon">
                    <i class="fas fa-folder-plus"></i>
                </div>
                <div class="step-content">
                    <h6>Створення директорій</h6>
                    <p>Створення необхідних папок для файлів</p>
                    <div class="step-progress">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
                <div class="step-status">
                    <i class="fas fa-clock text-muted"></i>
                </div>
            </div>

            <div class="step-item" id="step-2">
                <div class="step-icon">
                    <i class="fas fa-database"></i>
                </div>
                <div class="step-content">
                    <h6>Створення бази даних</h6>
                    <p>Налаштування структури бази даних</p>
                    <div class="step-progress">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
                <div class="step-status">
                    <i class="fas fa-clock text-muted"></i>
                </div>
            </div>

            <div class="step-item" id="step-3">
                <div class="step-icon">
                    <i class="fas fa-table"></i>
                </div>
                <div class="step-content">
                    <h6>Імпорт даних</h6>
                    <p>Завантаження початкових даних</p>
                    <div class="step-progress">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
                <div class="step-status">
                    <i class="fas fa-clock text-muted"></i>
                </div>
            </div>

            <div class="step-item" id="step-4">
                <div class="step-icon">
                    <i class="fas fa-cogs"></i>
                </div>
                <div class="step-content">
                    <h6>Налаштування сайту</h6>
                    <p>Застосування параметрів сайту</p>
                    <div class="step-progress">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
                <div class="step-status">
                    <i class="fas fa-clock text-muted"></i>
                </div>
            </div>

            <div class="step-item" id="step-5">
                <div class="step-icon">
                    <i class="fas fa-palette"></i>
                </div>
                <div class="step-content">
                    <h6>Налаштування теми</h6>
                    <p>Застосування обраної теми та градієнтів</p>
                    <div class="step-progress">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
                <div class="step-status">
                    <i class="fas fa-clock text-muted"></i>
                </div>
            </div>

            <div class="step-item" id="step-6">
                <div class="step-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="step-content">
                    <h6>Створення адміністратора</h6>
                    <p>Реєстрація облікового запису адміна</p>
                    <div class="step-progress">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
                <div class="step-status">
                    <i class="fas fa-clock text-muted"></i>
                </div>
            </div>

            <div class="step-item" id="step-7">
                <div class="step-icon">
                    <i class="fas fa-file-code"></i>
                </div>
                <div class="step-content">
                    <h6>Генерація конфігурації</h6>
                    <p>Створення файлів конфігурації</p>
                    <div class="step-progress">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
                <div class="step-status">
                    <i class="fas fa-clock text-muted"></i>
                </div>
            </div>

            <div class="step-item" id="step-8">
                <div class="step-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="step-content">
                    <h6>Завершення установки</h6>
                    <p>Фіналізація та перевірка</p>
                    <div class="step-progress">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
                <div class="step-status">
                    <i class="fas fa-clock text-muted"></i>
                </div>
            </div>
        </div>

        <!-- Лог установки -->
        <div class="installation-log mt-4">
            <div class="log-header">
                <h6><i class="fas fa-terminal me-2"></i>Лог установки</h6>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="toggleLog">
                    <i class="fas fa-eye"></i> Показати деталі
                </button>
            </div>
            <div class="log-content" id="logContent" style="display: none;">
                <div class="log-messages" id="logMessages">
                    <div class="log-entry">
                        <span class="log-time"><?php echo date('H:i:s'); ?></span>
                        <span class="log-level info">INFO</span>
                        <span class="log-message">Ініціалізація процесу установки...</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Повідомлення про помилки -->
        <div class="alert alert-danger d-none" id="errorAlert">
            <h6><i class="fas fa-exclamation-triangle me-2"></i>Помилка установки</h6>
            <p class="mb-0" id="errorMessage"></p>
            <div class="mt-3">
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="location.reload()">
                    <i class="fas fa-redo me-2"></i>Спробувати знову
                </button>
                <a href="?step=6" class="btn btn-outline-secondary btn-sm ms-2">
                    <i class="fas fa-chevron-left me-2"></i>Назад до налаштувань
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.installation-container {
    max-width: 800px;
    margin: 0 auto;
}

.main-progress {
    text-align: center;
}

.progress-circle {
    position: relative;
    width: 200px;
    height: 200px;
    margin: 0 auto;
}

.progress-inner {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 160px;
    height: 160px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.progress-percentage {
    color: white;
    font-size: 24px;
    font-weight: bold;
}

.progress-svg {
    position: absolute;
    top: 0;
    left: 0;
    transform: rotate(-90deg);
}

.progress-circle-bg {
    fill: none;
    stroke: #e9ecef;
    stroke-width: 8;
}

.progress-circle-fill {
    fill: none;
    stroke: #667eea;
    stroke-width: 8;
    stroke-linecap: round;
    stroke-dasharray: 565.48;
    stroke-dashoffset: 565.48;
    transition: stroke-dashoffset 0.5s ease;
}

.current-action {
    color: #495057;
    margin-bottom: 5px;
    transition: all 0.3s ease;
}

.current-description {
    margin-bottom: 0;
}

.installation-steps {
    background: #f8f9fa;
    border-radius: 15px;
    padding: 20px;
}

.step-item {
    display: flex;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.step-item:last-child {
    border-bottom: none;
}

.step-item.active {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    border-radius: 10px;
    margin: 0 -10px;
    padding: 15px 10px;
}

.step-item.completed {
    opacity: 0.7;
}

.step-icon {
    width: 50px;
    height: 50px;
    background: #e9ecef;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    font-size: 18px;
    color: #6c757d;
    transition: all 0.3s ease;
}

.step-item.active .step-icon {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    animation: pulse 2s infinite;
}

.step-item.completed .step-icon {
    background: #28a745;
    color: white;
}

.step-content {
    flex: 1;
}

.step-content h6 {
    margin-bottom: 5px;
    color: #495057;
}

.step-content p {
    margin-bottom: 8px;
    color: #6c757d;
    font-size: 0.9em;
}

.step-progress {
    width: 100%;
}

.step-progress .progress {
    height: 6px;
    background: #dee2e6;
    border-radius: 3px;
}

.step-progress .progress-bar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    transition: width 0.5s ease;
}

.step-status {
    width: 30px;
    text-align: center;
    font-size: 18px;
}

.step-item.active .step-status i {
    color: #667eea;
    animation: spin 2s linear infinite;
}

.step-item.completed .step-status i {
    color: #28a745;
}

.installation-log {
    background: #ffffff;
    border: 1px solid #dee2e6;
    border-radius: 10px;
    overflow: hidden;
}

.log-header {
    background: #f8f9fa;
    padding: 15px 20px;
    border-bottom: 1px solid #dee2e6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.log-content {
    max-height: 300px;
    overflow-y: auto;
}

.log-messages {
    padding: 15px;
    font-family: 'Courier New', monospace;
    font-size: 12px;
    line-height: 1.4;
}

.log-entry {
    margin-bottom: 5px;
    display: flex;
    align-items: center;
}

.log-time {
    color: #6c757d;
    margin-right: 10px;
    min-width: 60px;
}

.log-level {
    margin-right: 10px;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: bold;
    min-width: 40px;
    text-align: center;
}

.log-level.info {
    background: #d1ecf1;
    color: #0c5460;
}

.log-level.success {
    background: #d4edda;
    color: #155724;
}

.log-level.warning {
    background: #fff3cd;
    color: #856404;
}

.log-level.error {
    background: #f8d7da;
    color: #721c24;
}

.log-message {
    color: #495057;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-in {
    animation: slideInLeft 0.5s ease;
}

@keyframes slideInLeft {
    from {
        transform: translateX(-100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleLogBtn = document.getElementById('toggleLog');
    const logContent = document.getElementById('logContent');
    const logMessages = document.getElementById('logMessages');
    const errorAlert = document.getElementById('errorAlert');
    const mainProgress = document.getElementById('mainProgress');
    const currentAction = document.getElementById('currentAction');
    const currentDescription = document.getElementById('currentDescription');
    const progressCircle = document.getElementById('progressCircle');
    
    let currentStep = 0;
    let totalSteps = 8;
    let isInstalling = false;
    
    // Переключення лога
    toggleLogBtn.addEventListener('click', function() {
        const isHidden = logContent.style.display === 'none';
        logContent.style.display = isHidden ? 'block' : 'none';
        const icon = this.querySelector('i');
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
        this.innerHTML = isHidden 
            ? '<i class="fas fa-eye-slash"></i> Сховати деталі'
            : '<i class="fas fa-eye"></i> Показати деталі';
    });
    
    // Функція додавання логу
    function addLogEntry(message, level = 'info') {
        const time = new Date().toLocaleTimeString();
        const entry = document.createElement('div');
        entry.className = 'log-entry animate-in';
        entry.innerHTML = `
            <span class="log-time">${time}</span>
            <span class="log-level ${level}">${level.toUpperCase()}</span>
            <span class="log-message">${message}</span>
        `;
        logMessages.appendChild(entry);
        logMessages.scrollTop = logMessages.scrollHeight;
    }
    
    // Функція оновлення прогресу
    function updateProgress(step, percentage) {
        const stepElement = document.getElementById(`step-${step}`);
        const progressBar = stepElement.querySelector('.progress-bar');
        progressBar.style.width = percentage + '%';
        
        if (percentage === 100) {
            stepElement.classList.add('completed');
            stepElement.classList.remove('active');
            stepElement.querySelector('.step-status i').className = 'fas fa-check-circle text-success';
        }
    }
    
    // Функція оновлення загального прогресу
    function updateMainProgress(percentage, action, description) {
        mainProgress.textContent = percentage + '%';
        currentAction.textContent = action;
        currentDescription.textContent = description;
        
        const circumference = 2 * Math.PI * 90;
        const offset = circumference - (percentage / 100) * circumference;
        progressCircle.style.strokeDashoffset = offset;
    }
    
    // Функція активації кроку
    function activateStep(step) {
        // Деактивуємо всі кроки
        for (let i = 1; i <= totalSteps; i++) {
            document.getElementById(`step-${i}`).classList.remove('active');
        }
        
        // Активуємо поточний крок
        const stepElement = document.getElementById(`step-${step}`);
        stepElement.classList.add('active');
        stepElement.querySelector('.step-status i').className = 'fas fa-spinner';
    }
    
    // Процес установки
    async function startInstallation() {
        if (isInstalling) return;
        isInstalling = true;
        
        const steps = [
            {
                action: 'Створення директорій',
                description: 'Створюємо необхідні папки для файлів системи',
                duration: 2000
            },
            {
                action: 'Створення бази даних',
                description: 'Налаштовуємо структуру бази даних',
                duration: 3000
            },
            {
                action: 'Імпорт даних',
                description: 'Завантажуємо початкові дані в базу',
                duration: 4000
            },
            {
                action: 'Налаштування сайту',
                description: 'Застосовуємо параметри сайту',
                duration: 2500
            },
            {
                action: 'Налаштування теми',
                description: 'Застосовуємо обрану тему та градієнти',
                duration: 2000
            },
            {
                action: 'Створення адміністратора',
                description: 'Реєструємо обліковий запис адміна',
                duration: 1500
            },
            {
                action: 'Генерація конфігурації',
                description: 'Створюємо файли конфігурації',
                duration: 2000
            },
            {
                action: 'Завершення установки',
                description: 'Фіналізуємо та перевіряємо установку',
                duration: 1500
            }
        ];
        
        try {
            for (let i = 0; i < steps.length; i++) {
                currentStep = i + 1;
                const step = steps[i];
                
                activateStep(currentStep);
                addLogEntry(`Початок: ${step.action}`, 'info');
                
                // Оновлюємо загальний прогрес
                const overallProgress = Math.round(((i + 1) / steps.length) * 100);
                updateMainProgress(overallProgress, step.action, step.description);
                
                // Симулюємо прогрес кроку
                await simulateStepProgress(currentStep, step.duration);
                
                addLogEntry(`Завершено: ${step.action}`, 'success');
                updateProgress(currentStep, 100);
                
                // Пауза між кроками
                await new Promise(resolve => setTimeout(resolve, 500));
            }
            
            // Установка завершена
            addLogEntry('Установка успішно завершена!', 'success');
            updateMainProgress(100, 'Установка завершена!', 'AdBoard Pro готовий до використання');
            
            // Перенаправлення на наступний крок
            setTimeout(() => {
                window.location.href = '?step=8';
            }, 2000);
            
        } catch (error) {
            addLogEntry(`Помилка: ${error.message}`, 'error');
            showError(error.message);
        }
    }
    
    // Симуляція прогресу кроку
    async function simulateStepProgress(step, duration) {
        const intervals = 20;
        const delay = duration / intervals;
        
        for (let i = 0; i <= intervals; i++) {
            const percentage = Math.round((i / intervals) * 100);
            updateProgress(step, percentage);
            
            if (i < intervals) {
                await new Promise(resolve => setTimeout(resolve, delay));
            }
        }
    }
    
    // Показати помилку
    function showError(message) {
        document.getElementById('errorMessage').textContent = message;
        errorAlert.classList.remove('d-none');
        isInstalling = false;
    }
    
    // Автоматичний запуск установки
    setTimeout(startInstallation, 1000);
});
</script>

<!-- Форма для POST запиту (приховано, але потрібно для переходу) -->
<form method="POST" id="installForm" style="display: none;">
    <input type="hidden" name="step" value="7">
    <input type="hidden" name="action" value="install">
</form>