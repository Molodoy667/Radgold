<?php
// Крок 8: Завершення установки
?>

<div class="step-content animate__animated animate__fadeIn">
    <div class="completion-container">
        <!-- Заголовок успіху -->
        <div class="success-header text-center mb-5">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2 class="success-title">Сайт успішно встановлений!</h2>
            <p class="success-subtitle">AdBoard Pro готовий до використання</p>
        </div>

        <!-- Інформація про установку -->
        <div class="installation-info">
            <div class="row">
                <div class="col-md-6">
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-globe"></i>
                        </div>
                        <div class="info-content">
                            <h5>Сайт готовий</h5>
                            <p>Ваш сайт успішно налаштований та готовий приймати відвідувачів</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="info-content">
                            <h5>Адмін створений</h5>
                            <p>Обліковий запис адміністратора готовий для управління сайтом</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-database"></i>
                        </div>
                        <div class="info-content">
                            <h5>База даних</h5>
                            <p>Структура бази даних створена та заповнена початковими даними</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-palette"></i>
                        </div>
                        <div class="info-content">
                            <h5>Тема налаштована</h5>
                            <p>Обрана тема та градієнт застосовані до всього сайту</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Дані для входу -->
        <div class="credentials-section">
            <h4><i class="fas fa-key me-2"></i>Дані для входу в адмін-панель</h4>
            <div class="credentials-box">
                <div class="row">
                    <div class="col-md-4">
                        <div class="credential-item">
                            <label>Логін:</label>
                            <div class="credential-value">
                                <code><?php echo htmlspecialchars($_SESSION['install_data']['admin']['admin_login'] ?? 'admin'); ?></code>
                                <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="copyToClipboard(this)" data-text="<?php echo htmlspecialchars($_SESSION['install_data']['admin']['admin_login'] ?? 'admin'); ?>">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="credential-item">
                            <label>Email:</label>
                            <div class="credential-value">
                                <code><?php echo htmlspecialchars($_SESSION['install_data']['admin']['admin_email'] ?? 'admin@example.com'); ?></code>
                                <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="copyToClipboard(this)" data-text="<?php echo htmlspecialchars($_SESSION['install_data']['admin']['admin_email'] ?? 'admin@example.com'); ?>">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="credential-item">
                            <label>Пароль:</label>
                            <div class="credential-value">
                                <code id="passwordField">••••••••••</code>
                                <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="togglePassword()" id="showPasswordBtn">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="credentials-note">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Важливо:</strong> Збережіть ці дані в надійному місці. Пароль більше не буде показаний в открытому вигляді.
                </div>
            </div>
        </div>

        <!-- Дії -->
        <div class="action-buttons">
            <div class="row">
                <div class="col-md-6">
                    <a href="../index.php" class="btn btn-primary btn-lg w-100">
                        <i class="fas fa-home me-2"></i>
                        Перейти на головну сторінку
                    </a>
                </div>
                
                <div class="col-md-6">
                    <a href="../admin/login.php" class="btn btn-success btn-lg w-100">
                        <i class="fas fa-shield-alt me-2"></i>
                        Перейти в адмін-панель
                    </a>
                </div>
            </div>
        </div>

        <!-- Попередження безпеки -->
        <div class="security-warning">
            <div class="warning-header">
                <i class="fas fa-exclamation-triangle"></i>
                <h5>Важливе попередження про безпеку</h5>
            </div>
            <div class="warning-content">
                <p>
                    <strong>Обов'язково видаліть папку <code>/install/</code> з сервера!</strong>
                </p>
                <p>
                    Залишення інсталяційних файлів на сервері створює серйозну загрозу безпеці вашого сайту. 
                    Зловмисники можуть використати їх для переустановки або пошкодження сайту.
                </p>
                
                <div class="delete-actions">
                    <button type="button" class="btn btn-danger" onclick="deleteInstallFolder()">
                        <i class="fas fa-trash me-2"></i>
                        Видалити папку install зараз
                    </button>
                    <small class="text-muted d-block mt-2">
                        Або видаліть папку вручну через FTP/файловий менеджер
                    </small>
                </div>
            </div>
        </div>

        <!-- Наступні кроки -->
        <div class="next-steps">
            <h4><i class="fas fa-rocket me-2"></i>Рекомендовані наступні кроки</h4>
            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h6>Налаштуйте SSL</h6>
                        <p>Встановіть SSL сертифікат для безпечного з'єднання (HTTPS)</p>
                    </div>
                </div>
                
                <div class="step-card">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h6>Створіть резервну копію</h6>
                        <p>Зробіть повну резервну копію файлів та бази даних</p>
                    </div>
                </div>
                
                <div class="step-card">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h6>Налаштуйте email</h6>
                        <p>Налаштуйте SMTP для відправки повідомлень з сайту</p>
                    </div>
                </div>
                
                <div class="step-card">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <h6>Додайте контент</h6>
                        <p>Заповніть сайт початковим контентом та категоріями</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Підтримка -->
        <div class="support-section">
            <div class="support-content">
                <h5><i class="fas fa-life-ring me-2"></i>Потрібна допомога?</h5>
                <p>Якщо у вас виникли питання або проблеми, ознайомтесь з документацією або зверніться до розробника.</p>
                <div class="support-links">
                    <a href="#" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-book me-2"></i>Документація
                    </a>
                    <a href="#" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-question-circle me-2"></i>FAQ
                    </a>
                    <a href="#" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-envelope me-2"></i>Підтримка
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.completion-container {
    max-width: 900px;
    margin: 0 auto;
}

.success-header {
    position: relative;
}

.success-icon {
    width: 120px;
    height: 120px;
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 60px;
    color: white;
    box-shadow: 0 10px 30px rgba(40, 167, 69, 0.3);
    animation: bounceIn 1s ease;
}

.success-title {
    color: #28a745;
    font-weight: bold;
    margin-bottom: 10px;
}

.success-subtitle {
    color: #6c757d;
    font-size: 1.1em;
}

.installation-info {
    margin-bottom: 40px;
}

.info-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    padding: 20px;
    height: 100%;
    display: flex;
    align-items: center;
    transition: all 0.3s ease;
    margin-bottom: 15px;
}

.info-card:hover {
    border-color: #667eea;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.info-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    font-size: 20px;
    color: #667eea;
    flex-shrink: 0;
}

.info-content h5 {
    margin-bottom: 5px;
    color: #495057;
}

.info-content p {
    margin-bottom: 0;
    color: #6c757d;
    font-size: 0.9em;
}

.credentials-section {
    background: #f8f9fa;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 30px;
    border: 1px solid #e9ecef;
}

.credentials-section h4 {
    color: #495057;
    margin-bottom: 20px;
}

.credentials-box {
    background: white;
    border-radius: 10px;
    padding: 20px;
    border: 1px solid #dee2e6;
}

.credential-item {
    margin-bottom: 15px;
}

.credential-item label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 5px;
    display: block;
}

.credential-value {
    display: flex;
    align-items: center;
}

.credential-value code {
    background: #e9ecef;
    padding: 8px 12px;
    border-radius: 6px;
    flex: 1;
    font-family: 'Courier New', monospace;
    color: #495057;
}

.credentials-note {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 8px;
    padding: 15px;
    margin-top: 20px;
    color: #856404;
}

.action-buttons {
    margin-bottom: 40px;
}

.action-buttons .btn {
    padding: 15px;
    font-size: 1.1em;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.action-buttons .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.security-warning {
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.1) 0%, rgba(255, 193, 7, 0.1) 100%);
    border: 2px solid #dc3545;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 30px;
    text-align: center;
}

.warning-header {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 15px;
}

.warning-header i {
    font-size: 24px;
    color: #dc3545;
    margin-right: 10px;
}

.warning-header h5 {
    color: #dc3545;
    margin: 0;
    font-weight: bold;
}

.warning-content p {
    color: #721c24;
    margin-bottom: 10px;
}

.warning-content code {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
    padding: 2px 6px;
    border-radius: 4px;
}

.delete-actions {
    margin-top: 20px;
}

.next-steps {
    margin-bottom: 30px;
}

.next-steps h4 {
    color: #495057;
    margin-bottom: 25px;
}

.steps-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.step-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    padding: 20px;
    display: flex;
    align-items: flex-start;
    transition: all 0.3s ease;
}

.step-card:hover {
    border-color: #667eea;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.step-number {
    width: 30px;
    height: 30px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    margin-right: 15px;
    flex-shrink: 0;
}

.step-content h6 {
    color: #495057;
    margin-bottom: 5px;
}

.step-content p {
    color: #6c757d;
    margin-bottom: 0;
    font-size: 0.9em;
}

.support-section {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
    border-radius: 15px;
    padding: 25px;
    text-align: center;
    border: 1px solid rgba(102, 126, 234, 0.1);
}

.support-content h5 {
    color: #495057;
    margin-bottom: 15px;
}

.support-content p {
    color: #6c757d;
    margin-bottom: 20px;
}

.support-links .btn {
    margin: 0 5px 5px 0;
}

@keyframes bounceIn {
    0% {
        transform: scale(0.3);
        opacity: 0;
    }
    50% {
        transform: scale(1.05);
    }
    70% {
        transform: scale(0.9);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.animate-fadeIn {
    animation: fadeIn 0.8s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Анімація появи карток
    const cards = document.querySelectorAll('.info-card, .step-card');
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.classList.add('animate-fadeIn');
        }, index * 100);
    });
    
    // Збереження паролю для показу
    const savedPassword = '<?php echo htmlspecialchars($_SESSION['install_data']['admin']['admin_password'] ?? ''); ?>';
});

// Функція копіювання в буфер обміну
function copyToClipboard(button) {
    const text = button.getAttribute('data-text');
    navigator.clipboard.writeText(text).then(() => {
        const originalIcon = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check text-success"></i>';
        setTimeout(() => {
            button.innerHTML = originalIcon;
        }, 2000);
    }).catch(() => {
        // Fallback для старих браузерів
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        
        const originalIcon = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check text-success"></i>';
        setTimeout(() => {
            button.innerHTML = originalIcon;
        }, 2000);
    });
}

// Функція показу/приховування паролю
function togglePassword() {
    const passwordField = document.getElementById('passwordField');
    const button = document.getElementById('showPasswordBtn');
    const savedPassword = '<?php echo htmlspecialchars($_SESSION['install_data']['admin']['admin_password'] ?? ''); ?>';
    
    if (passwordField.textContent === '••••••••••') {
        passwordField.textContent = savedPassword;
        button.innerHTML = '<i class="fas fa-eye-slash"></i>';
    } else {
        passwordField.textContent = '••••••••••';
        button.innerHTML = '<i class="fas fa-eye"></i>';
    }
}

// Функція видалення папки install
function deleteInstallFolder() {
    if (confirm('Ви впевнені, що хочете видалити папку install? Цю дію неможливо скасувати.')) {
        fetch('?action=delete_install', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Папка install успішно видалена!');
                document.querySelector('.security-warning').style.display = 'none';
            } else {
                alert('Помилка при видаленні папки: ' + data.message);
            }
        })
        .catch(error => {
            alert('Помилка: Видаліть папку вручну через FTP або файловий менеджер.');
        });
    }
}

// Автоматичне підсвічування коду
document.querySelectorAll('code').forEach(code => {
    code.addEventListener('click', function() {
        const text = this.textContent;
        const range = document.createRange();
        range.selectNode(this);
        window.getSelection().removeAllRanges();
        window.getSelection().addRange(range);
    });
});

// Очистити дані сесії після завершення
setTimeout(() => {
    fetch('../../install/index.php?action=clear_session', {
        method: 'POST'
    });
}, 10000); // Очищаємо через 10 секунд після завантаження сторінки
</script>

<?php
// Очищаємо дані інсталяції якщо встановлення завершено
if (isset($_SESSION['install_data']['installation_completed'])) {
    // Очищаємо тільки через деякий час після показу сторінки
    if (!isset($_SESSION['clear_install_data_time'])) {
        $_SESSION['clear_install_data_time'] = time() + 30; // через 30 секунд
    }
}
?>