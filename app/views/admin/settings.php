<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="windows-1251">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Настройки сайта - Админ панель</title>
    <link rel="stylesheet" href="/assets/css/theme.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
    <script src="/assets/js/theme.js"></script>
</head>
<body>
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h2>🎮 Админ панель</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="/admin" class="nav-item">
                    <span class="nav-icon">📊</span>
                    <span class="nav-text">Дашборд</span>
                </a>
                <a href="/admin/users" class="nav-item">
                    <span class="nav-icon">👥</span>
                    <span class="nav-text">Пользователи</span>
                </a>
                <a href="/admin/products" class="nav-item">
                    <span class="nav-icon">📦</span>
                    <span class="nav-text">Товары</span>
                </a>
                <a href="/admin/disputes" class="nav-item">
                    <span class="nav-icon">⚠️</span>
                    <span class="nav-text">Диспуты</span>
                </a>
                <a href="/admin/reviews" class="nav-item">
                    <span class="nav-icon">⭐</span>
                    <span class="nav-text">Отзывы</span>
                </a>
                <a href="/admin/settings" class="nav-item active">
                    <span class="nav-icon">⚙️</span>
                    <span class="nav-text">Настройки</span>
                </a>
                <a href="/" class="nav-item">
                    <span class="nav-icon">🏠</span>
                    <span class="nav-text">На сайт</span>
                </a>
            </nav>
        </aside>
        
        <main class="admin-main">
            <header class="admin-header">
                <h1>Настройки сайта</h1>
                <div class="header-actions">
                    <span class="admin-info">Админ: <?= htmlspecialchars($_SESSION['user']['login']) ?></span>
                </div>
            </header>
            
            <div class="admin-content">
                <div class="settings-container">
                    <form id="settingsForm" class="settings-form">
                        <div class="settings-section">
                            <h2>Основные настройки</h2>
                            <div class="form-group">
                                <label for="site_title">Название сайта</label>
                                <input type="text" id="site_title" name="site_title" 
                                       value="<?= htmlspecialchars($settings['site_title'] ?? 'Game Marketplace') ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="site_description">Описание сайта</label>
                                <textarea id="site_description" name="site_description" rows="3"><?= htmlspecialchars($settings['site_description'] ?? 'Современный маркетплейс для игровых аккаунтов и услуг') ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="contact_email">Контактный email</label>
                                <input type="email" id="contact_email" name="contact_email" 
                                       value="<?= htmlspecialchars($settings['contact_email'] ?? 'support@game-marketplace.com') ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="support_phone">Телефон поддержки</label>
                                <input type="text" id="support_phone" name="support_phone" 
                                       value="<?= htmlspecialchars($settings['support_phone'] ?? '+7 (999) 123-45-67') ?>">
                            </div>
                        </div>
                        
                        <div class="settings-section">
                            <h2>Финансовые настройки</h2>
                            <div class="form-group">
                                <label for="commission_percent">Комиссия сайта (%)</label>
                                <input type="number" id="commission_percent" name="commission_percent" 
                                       value="<?= htmlspecialchars($settings['commission_percent'] ?? '5') ?>" 
                                       min="0" max="50" step="0.1" required>
                                <small>Процент от каждой продажи, который удерживает сайт</small>
                            </div>
                            <div class="form-group">
                                <label for="min_withdrawal">Минимальная сумма вывода</label>
                                <input type="number" id="min_withdrawal" name="min_withdrawal" 
                                       value="<?= htmlspecialchars($settings['min_withdrawal'] ?? '100') ?>" 
                                       min="0" step="1" required>
                                <small>Минимальная сумма для вывода средств</small>
                            </div>
                            <div class="form-group">
                                <label for="default_currency">Основная валюта</label>
                                <select id="default_currency" name="default_currency" required>
                                    <option value="RUB" <?= ($settings['default_currency'] ?? 'RUB') === 'RUB' ? 'selected' : '' ?>>Рубли (RUB)</option>
                                    <option value="USD" <?= ($settings['default_currency'] ?? 'RUB') === 'USD' ? 'selected' : '' ?>>Доллары (USD)</option>
                                    <option value="EUR" <?= ($settings['default_currency'] ?? 'RUB') === 'EUR' ? 'selected' : '' ?>>Евро (EUR)</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="settings-section">
                            <h2>Настройки безопасности</h2>
                            <div class="form-group">
                                <label for="max_login_attempts">Максимум попыток входа</label>
                                <input type="number" id="max_login_attempts" name="max_login_attempts" 
                                       value="<?= htmlspecialchars($settings['max_login_attempts'] ?? '5') ?>" 
                                       min="1" max="20" required>
                                <small>Количество неудачных попыток входа перед блокировкой</small>
                            </div>
                            <div class="form-group">
                                <label for="session_timeout">Время сессии (минуты)</label>
                                <input type="number" id="session_timeout" name="session_timeout" 
                                       value="<?= htmlspecialchars($settings['session_timeout'] ?? '60') ?>" 
                                       min="15" max="1440" required>
                                <small>Время неактивности перед автоматическим выходом</small>
                            </div>
                            <div class="form-group">
                                <label for="enable_captcha">Включить капчу</label>
                                <select id="enable_captcha" name="enable_captcha" required>
                                    <option value="1" <?= ($settings['enable_captcha'] ?? '1') == '1' ? 'selected' : '' ?>>Да</option>
                                    <option value="0" <?= ($settings['enable_captcha'] ?? '1') == '0' ? 'selected' : '' ?>>Нет</option>
                                </select>
                                <small>Показывать капчу при регистрации и входе</small>
                            </div>
                        </div>
                        
                        <div class="settings-section">
                            <h2>Настройки модерации</h2>
                            <div class="form-group">
                                <label for="auto_approve_products">Автоодобрение товаров</label>
                                <select id="auto_approve_products" name="auto_approve_products" required>
                                    <option value="0" <?= ($settings['auto_approve_products'] ?? '0') == '0' ? 'selected' : '' ?>>Нет (требуется модерация)</option>
                                    <option value="1" <?= ($settings['auto_approve_products'] ?? '0') == '1' ? 'selected' : '' ?>>Да (автоматически)</option>
                                </select>
                                <small>Автоматически одобрять новые товары</small>
                            </div>
                            <div class="form-group">
                                <label for="auto_approve_reviews">Автоодобрение отзывов</label>
                                <select id="auto_approve_reviews" name="auto_approve_reviews" required>
                                    <option value="0" <?= ($settings['auto_approve_reviews'] ?? '0') == '0' ? 'selected' : '' ?>>Нет (требуется модерация)</option>
                                    <option value="1" <?= ($settings['auto_approve_reviews'] ?? '0') == '1' ? 'selected' : '' ?>>Да (автоматически)</option>
                                </select>
                                <small>Автоматически одобрять новые отзывы</small>
                            </div>
                            <div class="form-group">
                                <label for="max_images_per_product">Максимум изображений на товар</label>
                                <input type="number" id="max_images_per_product" name="max_images_per_product" 
                                       value="<?= htmlspecialchars($settings['max_images_per_product'] ?? '5') ?>" 
                                       min="1" max="20" required>
                                <small>Максимальное количество изображений для одного товара</small>
                            </div>
                        </div>
                        
                        <div class="settings-section">
                            <h2>Настройки уведомлений</h2>
                            <div class="form-group">
                                <label for="email_notifications">Email уведомления</label>
                                <select id="email_notifications" name="email_notifications" required>
                                    <option value="1" <?= ($settings['email_notifications'] ?? '1') == '1' ? 'selected' : '' ?>>Включены</option>
                                    <option value="0" <?= ($settings['email_notifications'] ?? '1') == '0' ? 'selected' : '' ?>>Отключены</option>
                                </select>
                                <small>Отправлять уведомления на email</small>
                            </div>
                            <div class="form-group">
                                <label for="admin_notifications">Уведомления админов</label>
                                <select id="admin_notifications" name="admin_notifications" required>
                                    <option value="1" <?= ($settings['admin_notifications'] ?? '1') == '1' ? 'selected' : '' ?>>Включены</option>
                                    <option value="0" <?= ($settings['admin_notifications'] ?? '1') == '0' ? 'selected' : '' ?>>Отключены</option>
                                </select>
                                <small>Уведомлять админов о новых диспутах и товарах</small>
                            </div>
                        </div>
                        
                        <div class="settings-section">
                            <h2>Настройки регистрации</h2>
                            <div class="form-group">
                                <label for="allow_registration">Разрешить регистрацию</label>
                                <select id="allow_registration" name="allow_registration" required>
                                    <option value="1" <?= ($settings['allow_registration'] ?? '1') == '1' ? 'selected' : '' ?>>Да</option>
                                    <option value="0" <?= ($settings['allow_registration'] ?? '1') == '0' ? 'selected' : '' ?>>Нет</option>
                                </select>
                                <small>Разрешить новым пользователям регистрироваться</small>
                            </div>
                            <div class="form-group">
                                <label for="require_email_verification">Требовать подтверждение email</label>
                                <select id="require_email_verification" name="require_email_verification" required>
                                    <option value="1" <?= ($settings['require_email_verification'] ?? '1') == '1' ? 'selected' : '' ?>>Да</option>
                                    <option value="0" <?= ($settings['require_email_verification'] ?? '1') == '0' ? 'selected' : '' ?>>Нет</option>
                                </select>
                                <small>Требовать подтверждение email при регистрации</small>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn-primary">Сохранить настройки</button>
                            <button type="button" onclick="resetSettings()" class="btn-secondary">Сбросить к умолчаниям</button>
                        </div>
                    </form>
                    
                    <div id="settingsMessage" class="message" style="display: none;"></div>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        document.getElementById('settingsForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const settings = {};
            
            for (let [key, value] of formData.entries()) {
                settings[key] = value;
            }
            
            fetch('/admin/settings/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(settings)
            })
            .then(response => response.json())
            .then(data => {
                const messageDiv = document.getElementById('settingsMessage');
                messageDiv.style.display = 'block';
                
                if (data.success) {
                    messageDiv.className = 'message success';
                    messageDiv.textContent = 'Настройки успешно сохранены!';
                } else {
                    messageDiv.className = 'message error';
                    messageDiv.textContent = 'Ошибка: ' + data.error;
                }
                
                setTimeout(() => {
                    messageDiv.style.display = 'none';
                }, 5000);
            })
            .catch(error => {
                console.error('Error:', error);
                const messageDiv = document.getElementById('settingsMessage');
                messageDiv.style.display = 'block';
                messageDiv.className = 'message error';
                messageDiv.textContent = 'Произошла ошибка при сохранении настроек';
                
                setTimeout(() => {
                    messageDiv.style.display = 'none';
                }, 5000);
            });
        });
        
        function resetSettings() {
            if (confirm('Сбросить все настройки к значениям по умолчанию?')) {
                fetch('/admin/settings/reset', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Ошибка: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Произошла ошибка');
                });
            }
        }
    </script>
    
    <style>
        .settings-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .settings-form {
            background: var(--bg-secondary);
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .settings-section {
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .settings-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .settings-section h2 {
            color: var(--text-primary);
            margin-bottom: 20px;
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            background: var(--bg-primary);
            color: var(--text-primary);
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.1);
        }
        
        .form-group small {
            display: block;
            margin-top: 6px;
            font-size: 12px;
            color: var(--text-secondary);
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid var(--border-color);
        }
        
        .btn-primary,
        .btn-secondary {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-primary {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-secondary {
            background: var(--secondary-color);
            color: var(--text-primary);
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(var(--primary-rgb), 0.3);
        }
        
        .btn-secondary:hover {
            background: var(--secondary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        
        .message {
            margin-top: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            font-weight: 600;
        }
        
        .message.success {
            background: rgba(76, 175, 80, 0.1);
            color: #4caf50;
            border: 1px solid rgba(76, 175, 80, 0.3);
        }
        
        .message.error {
            background: rgba(244, 67, 54, 0.1);
            color: #f44336;
            border: 1px solid rgba(244, 67, 54, 0.3);
        }
        
        @media (max-width: 768px) {
            .settings-form {
                padding: 20px;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .btn-primary,
            .btn-secondary {
                width: 100%;
            }
        }
    </style>
</body>
</html>