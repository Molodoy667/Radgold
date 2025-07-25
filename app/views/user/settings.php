<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="windows-1251">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Настройки - Game Marketplace</title>
    <link rel="stylesheet" href="/assets/css/theme.css">
    <link rel="stylesheet" href="/assets/css/products.css">
    <link rel="stylesheet" href="/assets/css/profile.css">
    <script src="/assets/js/theme.js"></script>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <h1>🎮 Game Marketplace</h1>
            </div>
            <nav class="nav">
                <a href="/">Главная</a>
                <a href="/products">Каталог</a>
                <?php if (isset($_SESSION['user'])): ?>
                    <a href="/profile">Личный кабинет</a>
                    <a href="/logout">Выйти</a>
                <?php else: ?>
                    <a href="/login">Войти</a>
                <?php endif; ?>
            </nav>
            <div class="header-actions">
                <button onclick="toggleTheme()" class="btn-theme">🌙</button>
            </div>
        </div>
    </header>
    
    <main class="main-content">
        <div class="container">
            <div class="page-header">
                <h1>Настройки</h1>
                <p>Управляйте своим профилем и предпочтениями</p>
            </div>
            
            <div class="settings-container">
                <div class="settings-sidebar">
                    <nav class="settings-nav">
                        <a href="#profile" class="nav-item active" onclick="showSection('profile')">
                            <span class="nav-icon">👤</span>
                            <span class="nav-text">Профиль</span>
                        </a>
                        <a href="#security" class="nav-item" onclick="showSection('security')">
                            <span class="nav-icon">🔒</span>
                            <span class="nav-text">Безопасность</span>
                        </a>
                        <a href="#notifications" class="nav-item" onclick="showSection('notifications')">
                            <span class="nav-icon">🔔</span>
                            <span class="nav-text">Уведомления</span>
                        </a>
                        <a href="#preferences" class="nav-item" onclick="showSection('preferences')">
                            <span class="nav-icon">⚙️</span>
                            <span class="nav-text">Предпочтения</span>
                        </a>
                        <a href="#privacy" class="nav-item" onclick="showSection('privacy')">
                            <span class="nav-icon">🛡️</span>
                            <span class="nav-text">Приватность</span>
                        </a>
                    </nav>
                </div>
                
                <div class="settings-content">
                    <!-- Профиль -->
                    <div id="profile" class="settings-section active">
                        <div class="section-header">
                            <h2>Профиль</h2>
                            <p>Обновите информацию о себе</p>
                        </div>
                        
                        <form id="profileForm" class="settings-form">
                            <div class="avatar-section">
                                <div class="avatar-preview">
                                    <?php if ($user['avatar']): ?>
                                        <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Аватар" id="avatarPreview">
                                    <?php else: ?>
                                        <div class="avatar-placeholder" id="avatarPreview">
                                            <?= strtoupper(substr($user['login'], 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="avatar-actions">
                                    <label for="avatarInput" class="btn-secondary">Изменить аватар</label>
                                    <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display: none;">
                                    <?php if ($user['avatar']): ?>
                                        <button type="button" onclick="removeAvatar()" class="btn-danger">Удалить</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Логин:</label>
                                    <input type="text" name="login" value="<?= htmlspecialchars($user['login']) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Email:</label>
                                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>О себе:</label>
                                <textarea name="bio" placeholder="Расскажите о себе..." maxlength="500" rows="4"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                                <span class="char-count">0/500</span>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn-primary">Сохранить изменения</button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Безопасность -->
                    <div id="security" class="settings-section">
                        <div class="section-header">
                            <h2>Безопасность</h2>
                            <p>Управляйте безопасностью аккаунта</p>
                        </div>
                        
                        <form id="passwordForm" class="settings-form">
                            <div class="form-group">
                                <label>Текущий пароль:</label>
                                <input type="password" name="current_password" required>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Новый пароль:</label>
                                    <input type="password" name="new_password" required minlength="6">
                                </div>
                                <div class="form-group">
                                    <label>Подтвердите пароль:</label>
                                    <input type="password" name="confirm_password" required minlength="6">
                                </div>
                            </div>
                            
                            <div class="password-strength" id="passwordStrength">
                                <div class="strength-bar">
                                    <div class="strength-fill" id="strengthFill"></div>
                                </div>
                                <span class="strength-text" id="strengthText">Введите пароль</span>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn-primary">Изменить пароль</button>
                            </div>
                        </form>
                        
                        <div class="security-section">
                            <h3>Двухфакторная аутентификация</h3>
                            <div class="security-item">
                                <div class="security-info">
                                    <span class="security-label">2FA</span>
                                    <span class="security-status <?= $user['two_factor_enabled'] ? 'enabled' : 'disabled' ?>">
                                        <?= $user['two_factor_enabled'] ? 'Включена' : 'Отключена' ?>
                                    </span>
                                </div>
                                <button onclick="toggle2FA()" class="btn-secondary">
                                    <?= $user['two_factor_enabled'] ? 'Отключить' : 'Включить' ?>
                                </button>
                            </div>
                        </div>
                        
                        <div class="security-section">
                            <h3>Активные сессии</h3>
                            <div class="sessions-list">
                                <?php foreach ($activeSessions as $session): ?>
                                    <div class="session-item">
                                        <div class="session-info">
                                            <div class="session-device">
                                                <span class="device-icon"><?= $session['device_type'] === 'mobile' ? '📱' : '💻' ?></span>
                                                <span class="device-name"><?= htmlspecialchars($session['device_name']) ?></span>
                                            </div>
                                            <div class="session-details">
                                                <span class="session-ip"><?= htmlspecialchars($session['ip_address']) ?></span>
                                                <span class="session-date"><?= date('d.m.Y H:i', strtotime($session['created_at'])) ?></span>
                                            </div>
                                        </div>
                                        <?php if ($session['is_current']): ?>
                                            <span class="current-session">Текущая</span>
                                        <?php else: ?>
                                            <button onclick="terminateSession(<?= $session['id'] ?>)" class="btn-danger">Завершить</button>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Уведомления -->
                    <div id="notifications" class="settings-section">
                        <div class="section-header">
                            <h2>Уведомления</h2>
                            <p>Настройте уведомления о важных событиях</p>
                        </div>
                        
                        <form id="notificationsForm" class="settings-form">
                            <div class="notification-group">
                                <h3>Email уведомления</h3>
                                
                                <div class="notification-item">
                                    <div class="notification-info">
                                        <span class="notification-label">Новые сообщения</span>
                                        <span class="notification-desc">Уведомления о новых сообщениях в чате</span>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="email_messages" <?= ($settings['email_messages'] ?? true) ? 'checked' : '' ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                                
                                <div class="notification-item">
                                    <div class="notification-info">
                                        <span class="notification-label">Обновления диспутов</span>
                                        <span class="notification-desc">Уведомления об изменениях в диспутах</span>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="email_disputes" <?= ($settings['email_disputes'] ?? true) ? 'checked' : '' ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                                
                                <div class="notification-item">
                                    <div class="notification-info">
                                        <span class="notification-label">Продажи</span>
                                        <span class="notification-desc">Уведомления о продажах ваших товаров</span>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="email_sales" <?= ($settings['email_sales'] ?? true) ? 'checked' : '' ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                                
                                <div class="notification-item">
                                    <div class="notification-info">
                                        <span class="notification-label">Новые отзывы</span>
                                        <span class="notification-desc">Уведомления о новых отзывах на ваши товары</span>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="email_reviews" <?= ($settings['email_reviews'] ?? true) ? 'checked' : '' ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="notification-group">
                                <h3>Push уведомления</h3>
                                
                                <div class="notification-item">
                                    <div class="notification-info">
                                        <span class="notification-label">Включить push уведомления</span>
                                        <span class="notification-desc">Получать уведомления в браузере</span>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="push_enabled" <?= ($settings['push_enabled'] ?? false) ? 'checked' : '' ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn-primary">Сохранить настройки</button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Предпочтения -->
                    <div id="preferences" class="settings-section">
                        <div class="section-header">
                            <h2>Предпочтения</h2>
                            <p>Настройте интерфейс под себя</p>
                        </div>
                        
                        <form id="preferencesForm" class="settings-form">
                            <div class="preference-group">
                                <h3>Внешний вид</h3>
                                
                                <div class="preference-item">
                                    <div class="preference-info">
                                        <span class="preference-label">Тема оформления</span>
                                        <span class="preference-desc">Выберите светлую или темную тему</span>
                                    </div>
                                    <select name="theme" onchange="updateTheme(this.value)">
                                        <option value="auto" <?= ($settings['theme'] ?? 'auto') === 'auto' ? 'selected' : '' ?>>Автоматически</option>
                                        <option value="light" <?= ($settings['theme'] ?? '') === 'light' ? 'selected' : '' ?>>Светлая</option>
                                        <option value="dark" <?= ($settings['theme'] ?? '') === 'dark' ? 'selected' : '' ?>>Темная</option>
                                    </select>
                                </div>
                                
                                <div class="preference-item">
                                    <div class="preference-info">
                                        <span class="preference-label">Язык интерфейса</span>
                                        <span class="preference-desc">Выберите язык сайта</span>
                                    </div>
                                    <select name="language">
                                        <option value="ru" <?= ($settings['language'] ?? 'ru') === 'ru' ? 'selected' : '' ?>>Русский</option>
                                        <option value="en" <?= ($settings['language'] ?? '') === 'en' ? 'selected' : '' ?>>English</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="preference-group">
                                <h3>Отображение</h3>
                                
                                <div class="preference-item">
                                    <div class="preference-info">
                                        <span class="preference-label">Товаров на странице</span>
                                        <span class="preference-desc">Количество товаров в каталоге</span>
                                    </div>
                                    <select name="items_per_page">
                                        <option value="12" <?= ($settings['items_per_page'] ?? '12') === '12' ? 'selected' : '' ?>>12</option>
                                        <option value="24" <?= ($settings['items_per_page'] ?? '') === '24' ? 'selected' : '' ?>>24</option>
                                        <option value="48" <?= ($settings['items_per_page'] ?? '') === '48' ? 'selected' : '' ?>>48</option>
                                    </select>
                                </div>
                                
                                <div class="preference-item">
                                    <div class="preference-info">
                                        <span class="preference-label">Показывать цены в</span>
                                        <span class="preference-desc">Валюта для отображения цен</span>
                                    </div>
                                    <select name="currency">
                                        <option value="RUB" <?= ($settings['currency'] ?? 'RUB') === 'RUB' ? 'selected' : '' ?>>Рубли (₽)</option>
                                        <option value="USD" <?= ($settings['currency'] ?? '') === 'USD' ? 'selected' : '' ?>>Доллары ($)</option>
                                        <option value="EUR" <?= ($settings['currency'] ?? '') === 'EUR' ? 'selected' : '' ?>>Евро (€)</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn-primary">Сохранить предпочтения</button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Приватность -->
                    <div id="privacy" class="settings-section">
                        <div class="section-header">
                            <h2>Приватность</h2>
                            <p>Управляйте видимостью вашей информации</p>
                        </div>
                        
                        <form id="privacyForm" class="settings-form">
                            <div class="privacy-group">
                                <h3>Профиль</h3>
                                
                                <div class="privacy-item">
                                    <div class="privacy-info">
                                        <span class="privacy-label">Показывать email</span>
                                        <span class="privacy-desc">Другие пользователи смогут видеть ваш email</span>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="show_email" <?= ($settings['show_email'] ?? false) ? 'checked' : '' ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                                
                                <div class="privacy-item">
                                    <div class="privacy-info">
                                        <span class="privacy-label">Показывать статистику</span>
                                        <span class="privacy-desc">Отображать рейтинг и количество продаж</span>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="show_stats" <?= ($settings['show_stats'] ?? true) ? 'checked' : '' ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                                
                                <div class="privacy-item">
                                    <div class="privacy-info">
                                        <span class="privacy-label">Показывать дату регистрации</span>
                                        <span class="privacy-desc">Отображать когда вы зарегистрировались</span>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="show_registration_date" <?= ($settings['show_registration_date'] ?? true) ? 'checked' : '' ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="privacy-group">
                                <h3>Активность</h3>
                                
                                <div class="privacy-item">
                                    <div class="privacy-info">
                                        <span class="privacy-label">Показывать онлайн статус</span>
                                        <span class="privacy-desc">Другие пользователи увидят когда вы онлайн</span>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="show_online_status" <?= ($settings['show_online_status'] ?? true) ? 'checked' : '' ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                                
                                <div class="privacy-item">
                                    <div class="privacy-info">
                                        <span class="privacy-label">Показывать последнюю активность</span>
                                        <span class="privacy-desc">Отображать время последнего входа</span>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="show_last_activity" <?= ($settings['show_last_activity'] ?? false) ? 'checked' : '' ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn-primary">Сохранить настройки приватности</button>
                            </div>
                        </form>
                        
                        <div class="danger-zone">
                            <h3>Опасная зона</h3>
                            <div class="danger-item">
                                <div class="danger-info">
                                    <span class="danger-label">Удалить аккаунт</span>
                                    <span class="danger-desc">Это действие нельзя отменить. Все ваши данные будут удалены навсегда.</span>
                                </div>
                                <button onclick="deleteAccount()" class="btn-danger">Удалить аккаунт</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <script>
        // Переключение между разделами
        function showSection(sectionId) {
            // Скрываем все разделы
            document.querySelectorAll('.settings-section').forEach(section => {
                section.classList.remove('active');
            });
            
            // Убираем активный класс со всех пунктов навигации
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Показываем выбранный раздел
            document.getElementById(sectionId).classList.add('active');
            
            // Добавляем активный класс к выбранному пункту навигации
            event.target.classList.add('active');
        }
        
        // Обработка загрузки аватара
        document.getElementById('avatarInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('avatarPreview');
                    if (preview.tagName === 'IMG') {
                        preview.src = e.target.result;
                    } else {
                        preview.innerHTML = `<img src="${e.target.result}" alt="Аватар">`;
                    }
                };
                reader.readAsDataURL(file);
            }
        });
        
        // Удаление аватара
        function removeAvatar() {
            if (confirm('Удалить аватар?')) {
                const preview = document.getElementById('avatarPreview');
                const login = '<?= $user['login'] ?>';
                preview.innerHTML = login.charAt(0).toUpperCase();
                document.getElementById('avatarInput').value = '';
            }
        }
        
        // Подсчет символов в текстовом поле
        document.querySelector('textarea[name="bio"]').addEventListener('input', function() {
            const count = this.value.length;
            const max = 500;
            this.nextElementSibling.textContent = `${count}/${max}`;
            
            if (count > max * 0.9) {
                this.nextElementSibling.style.color = 'var(--warning-color)';
            } else {
                this.nextElementSibling.style.color = 'var(--text-secondary)';
            }
        });
        
        // Проверка силы пароля
        document.querySelector('input[name="new_password"]').addEventListener('input', function() {
            const password = this.value;
            const strength = checkPasswordStrength(password);
            updatePasswordStrength(strength);
        });
        
        function checkPasswordStrength(password) {
            let score = 0;
            
            if (password.length >= 8) score++;
            if (/[a-z]/.test(password)) score++;
            if (/[A-Z]/.test(password)) score++;
            if (/[0-9]/.test(password)) score++;
            if (/[^A-Za-z0-9]/.test(password)) score++;
            
            return score;
        }
        
        function updatePasswordStrength(strength) {
            const fill = document.getElementById('strengthFill');
            const text = document.getElementById('strengthText');
            
            const colors = ['#ff4444', '#ff8800', '#ffbb33', '#00C851', '#007E33'];
            const texts = ['Очень слабый', 'Слабый', 'Средний', 'Сильный', 'Очень сильный'];
            
            fill.style.width = `${(strength / 5) * 100}%`;
            fill.style.backgroundColor = colors[strength - 1] || colors[0];
            text.textContent = texts[strength - 1] || 'Введите пароль';
        }
        
        // Обновление темы
        function updateTheme(theme) {
            if (theme === 'auto') {
                // Автоматическое определение темы
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                document.body.className = prefersDark ? 'dark-theme' : 'light-theme';
            } else {
                document.body.className = theme + '-theme';
            }
        }
        
        // Отправка форм
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            e.preventDefault();
            submitForm(this, '/update-profile');
        });
        
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const newPassword = this.querySelector('input[name="new_password"]').value;
            const confirmPassword = this.querySelector('input[name="confirm_password"]').value;
            
            if (newPassword !== confirmPassword) {
                alert('Пароли не совпадают');
                return;
            }
            
            submitForm(this, '/change-password');
        });
        
        document.getElementById('notificationsForm').addEventListener('submit', function(e) {
            e.preventDefault();
            submitForm(this, '/update-notifications');
        });
        
        document.getElementById('preferencesForm').addEventListener('submit', function(e) {
            e.preventDefault();
            submitForm(this, '/update-preferences');
        });
        
        document.getElementById('privacyForm').addEventListener('submit', function(e) {
            e.preventDefault();
            submitForm(this, '/update-privacy');
        });
        
        function submitForm(form, url) {
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('Настройки успешно сохранены', 'success');
                } else {
                    showMessage('Ошибка: ' + data.error, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('Произошла ошибка', 'error');
            });
        }
        
        function showMessage(message, type) {
            // Создаем уведомление
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            // Удаляем через 3 секунды
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
        
        // Дополнительные функции
        function toggle2FA() {
            if (confirm('Изменить настройки двухфакторной аутентификации?')) {
                fetch('/toggle-2fa', {
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
        
        function terminateSession(sessionId) {
            if (confirm('Завершить эту сессию?')) {
                fetch('/terminate-session', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ session_id: sessionId })
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
        
        function deleteAccount() {
            const password = prompt('Введите ваш пароль для подтверждения:');
            if (password) {
                if (confirm('Вы уверены, что хотите удалить аккаунт? Это действие нельзя отменить.')) {
                    fetch('/delete-account', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ password: password })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Аккаунт удален. Вы будете перенаправлены на главную страницу.');
                            window.location.href = '/';
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
        }
    </script>
    
    <style>
        .page-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .page-header h1 {
            margin: 0 0 10px 0;
            color: var(--text-primary);
        }
        
        .page-header p {
            margin: 0;
            color: var(--text-secondary);
            font-size: 16px;
        }
        
        .settings-container {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .settings-sidebar {
            background: var(--bg-secondary);
            border-radius: 12px;
            padding: 20px;
            height: fit-content;
        }
        
        .settings-nav {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 8px;
            text-decoration: none;
            color: var(--text-primary);
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .nav-item:hover {
            background: var(--bg-primary);
        }
        
        .nav-item.active {
            background: var(--primary-color);
            color: white;
        }
        
        .nav-icon {
            font-size: 18px;
        }
        
        .nav-text {
            font-weight: 500;
        }
        
        .settings-content {
            background: var(--bg-secondary);
            border-radius: 12px;
            padding: 30px;
        }
        
        .settings-section {
            display: none;
        }
        
        .settings-section.active {
            display: block;
        }
        
        .section-header {
            margin-bottom: 30px;
        }
        
        .section-header h2 {
            margin: 0 0 10px 0;
            color: var(--text-primary);
        }
        
        .section-header p {
            margin: 0;
            color: var(--text-secondary);
        }
        
        .settings-form {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }
        
        .avatar-section {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 20px;
            background: var(--bg-primary);
            border-radius: 12px;
        }
        
        .avatar-preview {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            overflow: hidden;
            flex-shrink: 0;
        }
        
        .avatar-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .avatar-placeholder {
            width: 100%;
            height: 100%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 600;
        }
        
        .avatar-actions {
            display: flex;
            gap: 10px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            position: relative;
        }
        
        .form-group label {
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: var(--bg-primary);
            color: var(--text-primary);
            font-size: 14px;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .char-count {
            position: absolute;
            bottom: -20px;
            right: 0;
            font-size: 12px;
            color: var(--text-secondary);
        }
        
        .password-strength {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .strength-bar {
            flex: 1;
            height: 6px;
            background: var(--border-color);
            border-radius: 3px;
            overflow: hidden;
        }
        
        .strength-fill {
            height: 100%;
            width: 0;
            transition: all 0.3s ease;
        }
        
        .strength-text {
            font-size: 14px;
            color: var(--text-secondary);
            min-width: 100px;
        }
        
        .security-section {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid var(--border-color);
        }
        
        .security-section h3 {
            margin: 0 0 20px 0;
            color: var(--text-primary);
        }
        
        .security-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: var(--bg-primary);
            border-radius: 8px;
            margin-bottom: 10px;
        }
        
        .security-info {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .security-label {
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .security-status {
            font-size: 14px;
        }
        
        .security-status.enabled {
            color: var(--success-color);
        }
        
        .security-status.disabled {
            color: var(--text-secondary);
        }
        
        .sessions-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .session-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: var(--bg-primary);
            border-radius: 8px;
        }
        
        .session-info {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .session-device {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .device-icon {
            font-size: 16px;
        }
        
        .device-name {
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .session-details {
            display: flex;
            gap: 15px;
            font-size: 12px;
            color: var(--text-secondary);
        }
        
        .current-session {
            background: var(--success-color);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        
        .notification-group,
        .preference-group,
        .privacy-group {
            margin-bottom: 30px;
        }
        
        .notification-group h3,
        .preference-group h3,
        .privacy-group h3 {
            margin: 0 0 20px 0;
            color: var(--text-primary);
        }
        
        .notification-item,
        .preference-item,
        .privacy-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: var(--bg-primary);
            border-radius: 8px;
            margin-bottom: 10px;
        }
        
        .notification-info,
        .preference-info,
        .privacy-info {
            display: flex;
            flex-direction: column;
            gap: 5px;
            flex: 1;
        }
        
        .notification-label,
        .preference-label,
        .privacy-label {
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .notification-desc,
        .preference-desc,
        .privacy-desc {
            font-size: 14px;
            color: var(--text-secondary);
        }
        
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }
        
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: var(--border-color);
            transition: .4s;
            border-radius: 24px;
        }
        
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .toggle-slider {
            background-color: var(--primary-color);
        }
        
        input:checked + .toggle-slider:before {
            transform: translateX(26px);
        }
        
        .danger-zone {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid var(--danger-color);
        }
        
        .danger-zone h3 {
            margin: 0 0 20px 0;
            color: var(--danger-color);
        }
        
        .danger-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background: var(--bg-primary);
            border: 1px solid var(--danger-color);
            border-radius: 8px;
        }
        
        .danger-info {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .danger-label {
            font-weight: 600;
            color: var(--danger-color);
        }
        
        .danger-desc {
            font-size: 14px;
            color: var(--text-secondary);
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }
        
        .btn-primary,
        .btn-secondary,
        .btn-danger {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
        }
        
        .btn-secondary {
            background: var(--bg-primary);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }
        
        .btn-secondary:hover {
            background: var(--bg-secondary);
        }
        
        .btn-danger {
            background: var(--danger-color);
            color: white;
        }
        
        .btn-danger:hover {
            background: var(--danger-dark);
        }
        
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            z-index: 1000;
            animation: slideIn 0.3s ease;
        }
        
        .notification-success {
            background: var(--success-color);
        }
        
        .notification-error {
            background: var(--danger-color);
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @media (max-width: 768px) {
            .settings-container {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .settings-sidebar {
                order: 2;
            }
            
            .settings-nav {
                flex-direction: row;
                overflow-x: auto;
                padding-bottom: 10px;
            }
            
            .nav-item {
                flex-shrink: 0;
                min-width: 120px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .notification-item,
            .preference-item,
            .privacy-item,
            .security-item,
            .session-item,
            .danger-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .avatar-section {
                flex-direction: column;
                text-align: center;
            }
            
            .avatar-actions {
                justify-content: center;
            }
        }
    </style>
</body>
</html>