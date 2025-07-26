<?php $title = 'Настройки'; ?>

<div class="container">
    <div class="page-header">
        <h1>Настройки аккаунта</h1>
    </div>

    <div class="settings-content">
        <div class="settings-nav">
            <a href="#profile" class="settings-tab active" data-tab="profile">
                <i class="icon-user"></i> Профиль
            </a>
            <a href="#security" class="settings-tab" data-tab="security">
                <i class="icon-lock"></i> Безопасность
            </a>
            <a href="#notifications" class="settings-tab" data-tab="notifications">
                <i class="icon-bell"></i> Уведомления
            </a>
            <a href="#privacy" class="settings-tab" data-tab="privacy">
                <i class="icon-shield"></i> Приватность
            </a>
        </div>

        <div class="settings-panels">
            <!-- Профиль -->
            <div class="settings-panel active" id="profile">
                <h2>Основная информация</h2>
                <form method="POST" action="/profile/update">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    
                    <div class="form-group">
                        <label for="username">Имя пользователя</label>
                        <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Телефон</label>
                        <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                </form>
            </div>

            <!-- Безопасность -->
            <div class="settings-panel" id="security">
                <h2>Безопасность</h2>
                <form method="POST" action="/profile/change-password">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    
                    <div class="form-group">
                        <label for="current_password">Текущий пароль</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password">Новый пароль</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Подтверждение пароля</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Изменить пароль</button>
                </form>
                
                <div class="security-info">
                    <h3>Активные сессии</h3>
                    <div class="session-item">
                        <div class="session-info">
                            <strong>Текущая сессия</strong>
                            <p>Последняя активность: <?= date('d.m.Y H:i') ?></p>
                        </div>
                        <span class="session-status current">Активна</span>
                    </div>
                </div>
            </div>

            <!-- Уведомления -->
            <div class="settings-panel" id="notifications">
                <h2>Настройки уведомлений</h2>
                <form method="POST" action="/profile/notifications">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    
                    <div class="form-group checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="email_notifications" <?= ($user['email_notifications'] ?? true) ? 'checked' : '' ?>>
                            <span class="checkmark"></span>
                            Email уведомления
                        </label>
                        <small>Получать уведомления на email о новых сообщениях и важных событиях</small>
                    </div>
                    
                    <div class="form-group checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="sms_notifications" <?= ($user['sms_notifications'] ?? false) ? 'checked' : '' ?>>
                            <span class="checkmark"></span>
                            SMS уведомления
                        </label>
                        <small>Получать SMS о важных событиях аккаунта</small>
                    </div>
                    
                    <div class="form-group checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="marketing_notifications" <?= ($user['marketing_notifications'] ?? false) ? 'checked' : '' ?>>
                            <span class="checkmark"></span>
                            Маркетинговые уведомления
                        </label>
                        <small>Получать информацию о новых функциях и предложениях</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Сохранить настройки</button>
                </form>
            </div>

            <!-- Приватность -->
            <div class="settings-panel" id="privacy">
                <h2>Настройки приватности</h2>
                <form method="POST" action="/profile/privacy">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    
                    <div class="form-group">
                        <label for="profile_visibility">Видимость профиля</label>
                        <select id="profile_visibility" name="profile_visibility">
                            <option value="public" <?= ($user['profile_visibility'] ?? 'public') === 'public' ? 'selected' : '' ?>>Публичный</option>
                            <option value="private" <?= ($user['profile_visibility'] ?? 'public') === 'private' ? 'selected' : '' ?>>Приватный</option>
                        </select>
                    </div>
                    
                    <div class="form-group checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="show_online_status" <?= ($user['show_online_status'] ?? true) ? 'checked' : '' ?>>
                            <span class="checkmark"></span>
                            Показывать статус "в сети"
                        </label>
                    </div>
                    
                    <div class="form-group checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="allow_messages" <?= ($user['allow_messages'] ?? true) ? 'checked' : '' ?>>
                            <span class="checkmark"></span>
                            Разрешить отправку личных сообщений
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Сохранить настройки</button>
                </form>
                
                <div class="danger-zone">
                    <h3>Опасная зона</h3>
                    <button class="btn btn-danger" onclick="confirmDeleteAccount()">
                        Удалить аккаунт
                    </button>
                    <small>Это действие нельзя отменить. Все ваши данные будут удалены навсегда.</small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.settings-content {
    display: grid;
    grid-template-columns: 250px 1fr;
    gap: 2rem;
    max-width: 1000px;
    margin: 0 auto;
}

.settings-nav {
    background: var(--card-bg);
    border-radius: 12px;
    padding: 1rem;
    height: fit-content;
}

.settings-tab {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    width: 100%;
    padding: 0.75rem 1rem;
    color: var(--text-secondary);
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s ease;
    margin-bottom: 0.5rem;
}

.settings-tab:hover,
.settings-tab.active {
    background: var(--accent-color);
    color: white;
}

.settings-panels {
    background: var(--card-bg);
    border-radius: 12px;
    padding: 2rem;
}

.settings-panel {
    display: none;
}

.settings-panel.active {
    display: block;
}

.settings-panel h2 {
    margin: 0 0 1.5rem;
    color: var(--text-primary);
    border-bottom: 2px solid var(--border-color);
    padding-bottom: 0.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: var(--text-primary);
    font-weight: 500;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid var(--border-color);
    border-radius: 8px;
    background: var(--bg-color);
    color: var(--text-primary);
    transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: var(--accent-color);
}

.checkbox-group {
    margin-bottom: 1rem;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    margin-bottom: 0.25rem;
}

.checkbox-label input[type="checkbox"] {
    width: auto;
    margin: 0;
}

.checkbox-group small {
    color: var(--text-secondary);
    font-size: 0.85rem;
    margin-left: 1.5rem;
}

.security-info {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 2px solid var(--border-color);
}

.security-info h3 {
    margin-bottom: 1rem;
    color: var(--text-primary);
}

.session-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: var(--bg-color);
    border-radius: 8px;
    margin-bottom: 0.5rem;
}

.session-status.current {
    background: #22c55e;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
}

.danger-zone {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 2px solid #ef4444;
}

.danger-zone h3 {
    color: #ef4444;
    margin-bottom: 1rem;
}

.danger-zone small {
    display: block;
    color: var(--text-secondary);
    margin-top: 0.5rem;
    font-size: 0.85rem;
}

@media (max-width: 768px) {
    .settings-content {
        grid-template-columns: 1fr;
    }
    
    .settings-nav {
        display: flex;
        overflow-x: auto;
        gap: 0.5rem;
    }
    
    .settings-tab {
        white-space: nowrap;
        margin-bottom: 0;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.settings-tab');
    const panels = document.querySelectorAll('.settings-panel');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Убираем активные классы
            tabs.forEach(t => t.classList.remove('active'));
            panels.forEach(p => p.classList.remove('active'));
            
            // Добавляем активный класс к кликнутой вкладке
            this.classList.add('active');
            
            // Показываем соответствующую панель
            const targetTab = this.dataset.tab;
            document.getElementById(targetTab).classList.add('active');
        });
    });
});

function confirmDeleteAccount() {
    if (confirm('Вы уверены, что хотите удалить свой аккаунт? Это действие нельзя отменить.')) {
        if (confirm('Последнее предупреждение! Все ваши данные будут удалены навсегда. Продолжить?')) {
            // Здесь должен быть запрос на удаление аккаунта
            fetch('/profile/delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': '<?= csrf_token() ?>'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '/';
                } else {
                    alert('Ошибка при удалении аккаунта');
                }
            })
            .catch(error => {
                alert('Ошибка при удалении аккаунта');
            });
        }
    }
}
</script>