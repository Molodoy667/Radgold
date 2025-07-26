<?php $title = 'Чат с ' . htmlspecialchars($recipient['username']); ?>

<div class="container">
    <div class="chat-wrapper">
        <div class="chat-header">
            <div class="chat-user-info">
                <a href="/messages" class="back-btn">← Назад</a>
                <div class="chat-avatar">
                    <img src="<?= $recipient['avatar'] ?? '/assets/images/default-avatar.svg' ?>" 
                         alt="<?= htmlspecialchars($recipient['username']) ?>">
                    <?php if ($recipient['online'] ?? false): ?>
                        <div class="online-indicator"></div>
                    <?php endif; ?>
                </div>
                <div class="chat-user-details">
                    <h2><?= htmlspecialchars($recipient['username']) ?></h2>
                    <span class="user-status">
                        <?= ($recipient['online'] ?? false) ? 'В сети' : 'Был в сети ' . date('d.m.Y H:i', strtotime($recipient['last_seen'] ?? $recipient['created_at'])) ?>
                    </span>
                </div>
            </div>
            <div class="chat-actions">
                <a href="/user/<?= $recipient['id'] ?>" class="btn btn-sm btn-secondary">Профиль</a>
            </div>
        </div>

        <div class="chat-messages" id="chatMessages">
            <?php if (empty($messages)): ?>
                <div class="empty-chat">
                    <i class="icon-mail"></i>
                    <p>Начните общение! Напишите первое сообщение.</p>
                </div>
            <?php else: ?>
                <?php foreach ($messages as $message): ?>
                    <div class="message <?= $message['sender_id'] === $_SESSION['user_id'] ? 'message-own' : 'message-other' ?>">
                        <div class="message-content">
                            <p><?= nl2br(htmlspecialchars($message['content'])) ?></p>
                            <div class="message-meta">
                                <span class="message-time"><?= date('H:i', strtotime($message['created_at'])) ?></span>
                                <?php if ($message['sender_id'] === $_SESSION['user_id']): ?>
                                    <span class="message-status">
                                        <?= $message['is_read'] ? '✓✓' : '✓' ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="chat-input">
            <form id="messageForm" class="message-form">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="recipient_id" value="<?= $recipient['id'] ?>">
                <div class="input-group">
                    <textarea id="messageText" name="message" placeholder="Введите сообщение..." rows="1"></textarea>
                    <button type="submit" class="send-btn">
                        <i class="icon-send"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.chat-wrapper {
    max-width: 800px;
    margin: 0 auto;
    background: var(--card-bg);
    border-radius: 12px;
    overflow: hidden;
    height: 70vh;
    display: flex;
    flex-direction: column;
}

.chat-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    background: var(--bg-color);
    border-bottom: 2px solid var(--border-color);
}

.chat-user-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.back-btn {
    color: var(--accent-color);
    text-decoration: none;
    font-weight: 500;
    padding: 0.5rem;
    border-radius: 8px;
    transition: background 0.3s ease;
}

.back-btn:hover {
    background: var(--border-color);
}

.chat-avatar {
    position: relative;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    overflow: hidden;
}

.chat-avatar img {
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

.chat-user-details h2 {
    margin: 0;
    font-size: 1.1rem;
    color: var(--text-primary);
}

.user-status {
    font-size: 0.85rem;
    color: var(--text-secondary);
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 1rem;
    background: var(--bg-color);
}

.empty-chat {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: var(--text-secondary);
}

.empty-chat i {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.message {
    margin-bottom: 1rem;
    display: flex;
}

.message-own {
    justify-content: flex-end;
}

.message-other {
    justify-content: flex-start;
}

.message-content {
    max-width: 70%;
    padding: 0.75rem 1rem;
    border-radius: 18px;
    position: relative;
}

.message-own .message-content {
    background: var(--accent-color);
    color: white;
    border-bottom-right-radius: 4px;
}

.message-other .message-content {
    background: var(--card-bg);
    color: var(--text-primary);
    border-bottom-left-radius: 4px;
}

.message-content p {
    margin: 0 0 0.5rem;
    line-height: 1.4;
}

.message-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.75rem;
    opacity: 0.8;
}

.message-own .message-meta {
    color: rgba(255, 255, 255, 0.8);
}

.message-other .message-meta {
    color: var(--text-secondary);
}

.message-status {
    color: #22c55e;
}

.chat-input {
    padding: 1rem;
    background: var(--card-bg);
    border-top: 2px solid var(--border-color);
}

.input-group {
    display: flex;
    gap: 0.75rem;
    align-items: flex-end;
}

.input-group textarea {
    flex: 1;
    padding: 0.75rem;
    border: 2px solid var(--border-color);
    border-radius: 20px;
    background: var(--bg-color);
    color: var(--text-primary);
    resize: none;
    min-height: 40px;
    max-height: 120px;
    font-family: inherit;
    transition: border-color 0.3s ease;
}

.input-group textarea:focus {
    outline: none;
    border-color: var(--accent-color);
}

.send-btn {
    width: 40px;
    height: 40px;
    border: none;
    border-radius: 50%;
    background: var(--accent-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.send-btn:hover {
    background: #8b5cf6;
    transform: scale(1.05);
}

.send-btn:disabled {
    background: var(--border-color);
    cursor: not-allowed;
    transform: none;
}

@media (max-width: 768px) {
    .chat-wrapper {
        height: calc(100vh - 80px);
        border-radius: 0;
    }
    
    .chat-header {
        padding: 1rem;
    }
    
    .chat-user-info {
        gap: 0.75rem;
    }
    
    .chat-avatar {
        width: 40px;
        height: 40px;
    }
    
    .message-content {
        max-width: 85%;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageForm = document.getElementById('messageForm');
    const messageText = document.getElementById('messageText');
    const chatMessages = document.getElementById('chatMessages');
    const sendBtn = document.querySelector('.send-btn');
    
    // Прокрутка к последнему сообщению
    scrollToBottom();
    
    // Автоизменение размера textarea
    messageText.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });
    
    // Отправка сообщения по Enter (без Shift)
    messageText.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
    
    // Отправка формы
    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        sendMessage();
    });
    
    function sendMessage() {
        const message = messageText.value.trim();
        if (!message) return;
        
        sendBtn.disabled = true;
        
        fetch('/messages/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': '<?= csrf_token() ?>'
            },
            body: JSON.stringify({
                recipient_id: <?= $recipient['id'] ?>,
                message: message
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Добавляем сообщение в чат
                addMessageToChat(message, true);
                messageText.value = '';
                messageText.style.height = 'auto';
                scrollToBottom();
            } else {
                alert('Ошибка отправки сообщения');
            }
        })
        .catch(error => {
            console.error('Ошибка:', error);
            alert('Ошибка отправки сообщения');
        })
        .finally(() => {
            sendBtn.disabled = false;
        });
    }
    
    function addMessageToChat(message, isOwn) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${isOwn ? 'message-own' : 'message-other'}`;
        
        const now = new Date();
        const time = now.toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' });
        
        messageDiv.innerHTML = `
            <div class="message-content">
                <p>${message.replace(/\n/g, '<br>')}</p>
                <div class="message-meta">
                    <span class="message-time">${time}</span>
                    ${isOwn ? '<span class="message-status">✓</span>' : ''}
                </div>
            </div>
        `;
        
        chatMessages.appendChild(messageDiv);
    }
    
    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    // Автообновление сообщений
    let lastMessageId = <?= !empty($messages) ? end($messages)['id'] : 0 ?>;
    
    setInterval(function() {
        fetch(`/messages/<?= $recipient['id'] ?>/updates?last_id=${lastMessageId}`)
            .then(response => response.json())
            .then(data => {
                if (data.new_messages && data.new_messages.length > 0) {
                    data.new_messages.forEach(message => {
                        addMessageToChat(message.content, false);
                        lastMessageId = message.id;
                    });
                    scrollToBottom();
                }
            })
            .catch(error => {
                console.error('Ошибка проверки новых сообщений:', error);
            });
    }, 5000); // Проверяем каждые 5 секунд
    
    // Отмечаем сообщения как прочитанные
    fetch(`/messages/<?= $recipient['id'] ?>/read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '<?= csrf_token() ?>'
        }
    });
});
</script>