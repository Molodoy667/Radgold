<?php $title = 'Редактирование профиля'; ?>

<div class="container">
    <div class="form-header">
        <h1>Редактирование профиля</h1>
        <a href="/profile" class="btn btn-secondary">← Назад к профилю</a>
    </div>

    <form class="profile-form" method="POST" action="/profile/update" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        
        <div class="form-section">
            <h2>Основная информация</h2>
            
            <div class="form-group">
                <label for="username">Имя пользователя</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="avatar">Аватар</label>
                <input type="file" id="avatar" name="avatar" accept="image/*">
                <?php if (!empty($user['avatar'])): ?>
                    <div class="current-avatar">
                        <img src="<?= $user['avatar'] ?>" alt="Текущий аватар">
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="form-section">
            <h2>Безопасность</h2>
            
            <div class="form-group">
                <label for="current_password">Текущий пароль</label>
                <input type="password" id="current_password" name="current_password">
                <small>Оставьте пустым, если не хотите менять пароль</small>
            </div>
            
            <div class="form-group">
                <label for="new_password">Новый пароль</label>
                <input type="password" id="new_password" name="new_password">
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Подтверждение пароля</label>
                <input type="password" id="confirm_password" name="confirm_password">
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
            <a href="/profile" class="btn btn-secondary">Отмена</a>
        </div>
    </form>
</div>

<style>
.form-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.profile-form {
    max-width: 600px;
    margin: 0 auto;
}

.form-section {
    background: var(--card-bg);
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
}

.form-section h2 {
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

.form-group input {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid var(--border-color);
    border-radius: 8px;
    background: var(--bg-color);
    color: var(--text-primary);
    transition: border-color 0.3s ease;
}

.form-group input:focus {
    outline: none;
    border-color: var(--accent-color);
}

.form-group small {
    color: var(--text-secondary);
    font-size: 0.85rem;
    margin-top: 0.25rem;
    display: block;
}

.current-avatar {
    margin-top: 0.5rem;
}

.current-avatar img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
}
</style>