<?php $title = 'Сообщения'; ?>

<div class="container">
    <div class="page-header">
        <h1>Сообщения</h1>
        <span class="unread-count"><?= $unread_count ?? 0 ?> непрочитанных</span>
    </div>

    <?php if (empty($conversations)): ?>
        <div class="empty-state">
            <i class="icon-mail"></i>
            <h2>У вас пока нет сообщений</h2>
            <p>Начните общение с другими пользователями!</p>
            <a href="/catalog" class="btn btn-primary">Перейти в каталог</a>
        </div>
    <?php else: ?>
        <div class="messages-container">
            <div class="conversations-list">
                <?php foreach ($conversations as $conversation): ?>
                    <div class="conversation-item <?= $conversation['unread'] ? 'unread' : '' ?>" 
                         onclick="openChat(<?= $conversation['user_id'] ?>)">
                        <div class="conversation-avatar">
                            <img src="<?= $conversation['avatar'] ?? '/assets/images/default-avatar.svg' ?>" 
                                 alt="<?= htmlspecialchars($conversation['username']) ?>">
                            <?php if ($conversation['online'] ?? false): ?>
                                <div class="online-indicator"></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="conversation-info">
                            <div class="conversation-header">
                                <h3 class="username"><?= htmlspecialchars($conversation['username']) ?></h3>
                                <span class="time"><?= date('H:i', strtotime($conversation['last_message_time'])) ?></span>
                            </div>
                            <p class="last-message">
                                <?php if ($conversation['last_message_sender'] === $_SESSION['user_id']): ?>
                                    <span class="sender-indicator">Вы: </span>
                                <?php endif; ?>
                                <?= htmlspecialchars(substr($conversation['last_message'], 0, 50)) ?>
                                <?= strlen($conversation['last_message']) > 50 ? '...' : '' ?>
                            </p>
                        </div>
                        
                        <?php if ($conversation['unread']): ?>
                            <div class="unread-badge"><?= $conversation['unread_count'] ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="chat-container" id="chatContainer">
                <div class="chat-placeholder">
                    <i class="icon-mail"></i>
                    <h3>Выберите диалог</h3>
                    <p>Выберите пользователя из списка, чтобы начать общение</p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.unread-count {
    color: var(--accent-color);
    font-weight: bold;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: var(--card-bg);
    border-radius: 12px;
}

.empty-state i {
    font-size: 4rem;
    color: var(--text-secondary);
    margin-bottom: 1rem;
}

.messages-container {
    display: grid;
    grid-template-columns: 400px 1fr;
    gap: 1rem;
    height: 70vh;
    background: var(--card-bg);
    border-radius: 12px;
    overflow: hidden;
}

.conversations-list {
    border-right: 2px solid var(--border-color);
    overflow-y: auto;
}

.conversation-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    cursor: pointer;
    transition: background 0.3s ease;
    border-bottom: 1px solid var(--border-color);
}

.conversation-item:hover {
    background: var(--bg-color);
}

.conversation-item.unread {
    background: rgba(139, 92, 246, 0.1);
}

.conversation-item.active {
    background: var(--accent-color);
    color: white;
}

.conversation-avatar {
    position: relative;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
}

.conversation-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.online-indicator {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 12px;
    height: 12px;
    background: #22c55e;
    border: 2px solid white;
    border-radius: 50%;
}

.conversation-info {
    flex: 1;
    min-width: 0;
}

.conversation-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.25rem;
}

.username {
    margin: 0;
    font-size: 1rem;
    color: var(--text-primary);
    font-weight: 500;
}

.time {
    font-size: 0.8rem;
    color: var(--text-secondary);
}

.last-message {
    margin: 0;
    font-size: 0.9rem;
    color: var(--text-secondary);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.sender-indicator {
    color: var(--accent-color);
    font-weight: 500;
}

.unread-badge {
    background: var(--accent-color);
    color: white;
    font-size: 0.8rem;
    font-weight: bold;
    padding: 0.25rem 0.5rem;
    border-radius: 10px;
    min-width: 20px;
    text-align: center;
}

.chat-container {
    display: flex;
    flex-direction: column;
}

.chat-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: var(--text-secondary);
    text-align: center;
}

.chat-placeholder i {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.chat-placeholder h3 {
    margin: 0 0 0.5rem;
    color: var(--text-primary);
}

@media (max-width: 768px) {
    .messages-container {
        grid-template-columns: 1fr;
        height: auto;
    }
    
    .chat-container {
        display: none;
    }
    
    .conversation-item {
        padding: 1.5rem 1rem;
    }
    
    .conversation-avatar {
        width: 60px;
        height: 60px;
    }
}
</style>

<script>
function openChat(userId) {
    // Убираем активный класс со всех диалогов
    document.querySelectorAll('.conversation-item').forEach(item => {
        item.classList.remove('active');
    });
    
    // Добавляем активный класс к выбранному диалогу
    event.currentTarget.classList.add('active');
    
    // Убираем класс непрочитанного
    event.currentTarget.classList.remove('unread');
    
    // На мобильных устройствах переходим на страницу чата
    if (window.innerWidth <= 768) {
        window.location.href = `/messages/${userId}`;
        return;
    }
    
    // Загружаем чат
    fetch(`/messages/${userId}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('chatContainer').innerHTML = html;
            // Отмечаем сообщения как прочитанные
            markAsRead(userId);
        })
        .catch(error => {
            console.error('Ошибка загрузки чата:', error);
        });
}

function markAsRead(userId) {
    fetch(`/messages/${userId}/read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '<?= csrf_token() ?>'
        }
    });
}

// Автообновление списка диалогов
setInterval(function() {
    fetch('/messages/updates')
        .then(response => response.json())
        .then(data => {
            if (data.new_messages) {
                // Обновляем счетчик непрочитанных
                document.querySelector('.unread-count').textContent = data.unread_count + ' непрочитанных';
                
                // Можно добавить обновление списка диалогов
                // location.reload();
            }
        })
        .catch(error => {
            console.error('Ошибка проверки обновлений:', error);
        });
}, 30000); // Проверяем каждые 30 секунд
</script>