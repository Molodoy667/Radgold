<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сообщения - Game Marketplace</title>
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
                <h1>Сообщения</h1>
                <div class="header-stats">
                    <div class="stat-card">
                        <span class="stat-icon">💬</span>
                        <span class="stat-value"><?= $totalConversations ?></span>
                        <span class="stat-label">Диалогов</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-icon">📨</span>
                        <span class="stat-value"><?= $unreadMessages ?></span>
                        <span class="stat-label">Непрочитанных</span>
                    </div>
                </div>
            </div>
            
            <div class="chat-container">
                <div class="conversations-list">
                    <div class="conversations-header">
                        <h3>Диалоги</h3>
                        <div class="search-box">
                            <input type="text" id="searchConversations" placeholder="Поиск по пользователям..." 
                                   onkeyup="searchConversations(this.value)">
                        </div>
                    </div>
                    
                    <?php if (empty($conversations)): ?>
                        <div class="empty-conversations">
                            <div class="empty-icon">💬</div>
                            <h4>У вас пока нет диалогов</h4>
                            <p>Начните общение с продавцами или покупателями!</p>
                        </div>
                    <?php else: ?>
                        <div class="conversations">
                            <?php foreach ($conversations as $conversation): ?>
                                <div class="conversation-item <?= $conversation['is_active'] ? 'active' : '' ?>" 
                                     onclick="loadConversation(<?= $conversation['user_id'] ?>, '<?= htmlspecialchars($conversation['user_login']) ?>')">
                                    <div class="conversation-avatar">
                                        <?php if ($conversation['user_avatar']): ?>
                                            <img src="<?= htmlspecialchars($conversation['user_avatar']) ?>" 
                                                 alt="<?= htmlspecialchars($conversation['user_login']) ?>">
                                        <?php else: ?>
                                            <div class="avatar-placeholder">
                                                <?= strtoupper(substr($conversation['user_login'], 0, 1)) ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($conversation['unread_count'] > 0): ?>
                                            <span class="unread-badge"><?= $conversation['unread_count'] ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="conversation-info">
                                        <div class="conversation-header">
                                            <span class="user-name"><?= htmlspecialchars($conversation['user_login']) ?></span>
                                            <span class="last-message-time"><?= date('H:i', strtotime($conversation['last_message_time'])) ?></span>
                                        </div>
                                        <div class="last-message">
                                            <?= htmlspecialchars(substr($conversation['last_message'], 0, 50)) ?>
                                            <?= strlen($conversation['last_message']) > 50 ? '...' : '' ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="chat-messages">
                    <div class="chat-header">
                        <div class="chat-user-info">
                            <div class="user-avatar">
                                <div class="avatar-placeholder" id="currentUserAvatar">?</div>
                            </div>
                            <div class="user-details">
                                <h3 id="currentUserName">Выберите диалог</h3>
                                <span class="user-status" id="currentUserStatus">Онлайн</span>
                            </div>
                        </div>
                        <div class="chat-actions">
                            <button onclick="clearChat()" class="btn-secondary" id="clearChatBtn" style="display: none;">Очистить</button>
                        </div>
                    </div>
                    
                    <div class="messages-container" id="messagesContainer">
                        <div class="no-conversation">
                            <div class="no-conversation-icon">💬</div>
                            <h3>Выберите диалог</h3>
                            <p>Выберите пользователя из списка слева, чтобы начать общение</p>
                        </div>
                    </div>
                    
                    <div class="message-input-container" id="messageInputContainer" style="display: none;">
                        <form id="messageForm" class="message-form">
                            <div class="input-group">
                                <input type="text" id="messageInput" placeholder="Введите сообщение..." 
                                       maxlength="1000" autocomplete="off">
                                <button type="submit" class="btn-send">📤</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <script>
        let currentConversationUserId = null;
        let messagePollingInterval = null;
        
        function searchConversations(query) {
            const conversations = document.querySelectorAll('.conversation-item');
            conversations.forEach(item => {
                const userName = item.querySelector('.user-name').textContent.toLowerCase();
                if (userName.includes(query.toLowerCase())) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        }
        
        function loadConversation(userId, userName) {
            currentConversationUserId = userId;
            
            // Обновляем UI
            document.getElementById('currentUserName').textContent = userName;
            document.getElementById('currentUserAvatar').textContent = userName.charAt(0).toUpperCase();
            document.getElementById('messageInputContainer').style.display = 'block';
            document.getElementById('clearChatBtn').style.display = 'block';
            
            // Убираем активный класс со всех диалогов
            document.querySelectorAll('.conversation-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Добавляем активный класс к выбранному диалогу
            event.currentTarget.classList.add('active');
            
            // Загружаем сообщения
            fetchMessages(userId);
            
            // Запускаем опрос новых сообщений
            startMessagePolling(userId);
        }
        
        function fetchMessages(userId) {
            fetch(`/get-messages?user_id=${userId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayMessages(data.messages);
                        markMessagesAsRead(userId);
                    } else {
                        console.error('Ошибка загрузки сообщений:', data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
        
        function displayMessages(messages) {
            const container = document.getElementById('messagesContainer');
            
            if (messages.length === 0) {
                container.innerHTML = `
                    <div class="no-messages">
                        <div class="no-messages-icon">💬</div>
                        <h3>Нет сообщений</h3>
                        <p>Начните разговор первым!</p>
                    </div>
                `;
                return;
            }
            
            let messagesHtml = '';
            messages.forEach(message => {
                const isOwn = message.sender_id == <?= $_SESSION['user']['id'] ?? 0 ?>;
                const messageClass = isOwn ? 'message own' : 'message other';
                const time = new Date(message.created_at).toLocaleTimeString('ru-RU', { 
                    hour: '2-digit', 
                    minute: '2-digit' 
                });
                
                messagesHtml += `
                    <div class="${messageClass}">
                        <div class="message-content">
                            <div class="message-text">${escapeHtml(message.message)}</div>
                            <div class="message-time">${time}</div>
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = messagesHtml;
            scrollToBottom();
        }
        
        function scrollToBottom() {
            const container = document.getElementById('messagesContainer');
            container.scrollTop = container.scrollHeight;
        }
        
        function markMessagesAsRead(userId) {
            fetch('/mark-messages-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ user_id: userId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Обновляем счетчик непрочитанных
                    updateUnreadCount();
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
        
        function updateUnreadCount() {
            // Обновляем счетчик в заголовке
            fetch('/get-unread-count')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.querySelector('.stat-card:nth-child(2) .stat-value').textContent = data.count;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
        
        function startMessagePolling(userId) {
            // Останавливаем предыдущий опрос
            if (messagePollingInterval) {
                clearInterval(messagePollingInterval);
            }
            
            // Запускаем новый опрос каждые 3 секунды
            messagePollingInterval = setInterval(() => {
                if (currentConversationUserId === userId) {
                    fetchMessages(userId);
                }
            }, 3000);
        }
        
        function clearChat() {
            if (confirm('Очистить историю сообщений?')) {
                fetch('/clear-chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ user_id: currentConversationUserId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        fetchMessages(currentConversationUserId);
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
        
        // Отправка сообщения
        document.getElementById('messageForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const messageInput = document.getElementById('messageInput');
            const message = messageInput.value.trim();
            
            if (!message || !currentConversationUserId) return;
            
            fetch('/send-message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    receiver_id: currentConversationUserId,
                    message: message
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageInput.value = '';
                    fetchMessages(currentConversationUserId);
                } else {
                    alert('Ошибка: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Произошла ошибка');
            });
        });
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Автообновление непрочитанных сообщений
        setInterval(updateUnreadCount, 10000);
    </script>
    
    <style>
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .page-header h1 {
            margin: 0;
            color: var(--text-primary);
        }
        
        .header-stats {
            display: flex;
            gap: 20px;
        }
        
        .stat-card {
            background: var(--bg-secondary);
            padding: 15px 20px;
            border-radius: 8px;
            text-align: center;
            min-width: 120px;
        }
        
        .stat-icon {
            font-size: 24px;
            display: block;
            margin-bottom: 5px;
        }
        
        .stat-value {
            display: block;
            font-size: 18px;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .stat-label {
            display: block;
            font-size: 12px;
            color: var(--text-secondary);
            margin-top: 2px;
        }
        
        .chat-container {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 20px;
            height: 70vh;
            background: var(--bg-secondary);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .conversations-list {
            background: var(--bg-primary);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
        }
        
        .conversations-header {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .conversations-header h3 {
            margin: 0 0 15px 0;
            color: var(--text-primary);
        }
        
        .search-box input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: var(--bg-secondary);
            color: var(--text-primary);
        }
        
        .conversations {
            flex: 1;
            overflow-y: auto;
        }
        
        .conversation-item {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid var(--border-color);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .conversation-item:hover {
            background: var(--bg-secondary);
        }
        
        .conversation-item.active {
            background: var(--primary-color);
            color: white;
        }
        
        .conversation-item.active .user-name,
        .conversation-item.active .last-message {
            color: white;
        }
        
        .conversation-avatar {
            position: relative;
            margin-right: 15px;
        }
        
        .conversation-avatar img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .avatar-placeholder {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 18px;
        }
        
        .unread-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
        }
        
        .conversation-info {
            flex: 1;
            min-width: 0;
        }
        
        .conversation-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }
        
        .user-name {
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .last-message-time {
            font-size: 12px;
            color: var(--text-secondary);
        }
        
        .last-message {
            font-size: 14px;
            color: var(--text-secondary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .empty-conversations {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-secondary);
        }
        
        .empty-conversations .empty-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .empty-conversations h4 {
            margin: 0 0 10px 0;
            color: var(--text-primary);
        }
        
        .empty-conversations p {
            margin: 0;
            font-size: 14px;
        }
        
        .chat-messages {
            display: flex;
            flex-direction: column;
            background: var(--bg-primary);
        }
        
        .chat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            background: var(--bg-secondary);
        }
        
        .chat-user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-avatar {
            position: relative;
        }
        
        .user-details h3 {
            margin: 0 0 5px 0;
            color: var(--text-primary);
        }
        
        .user-status {
            font-size: 12px;
            color: var(--success-color);
        }
        
        .messages-container {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .no-conversation,
        .no-messages {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-secondary);
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .no-conversation-icon,
        .no-messages-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }
        
        .no-conversation h3,
        .no-messages h3 {
            margin: 0 0 10px 0;
            color: var(--text-primary);
        }
        
        .no-conversation p,
        .no-messages p {
            margin: 0;
            font-size: 14px;
        }
        
        .message {
            display: flex;
            margin-bottom: 10px;
        }
        
        .message.own {
            justify-content: flex-end;
        }
        
        .message.other {
            justify-content: flex-start;
        }
        
        .message-content {
            max-width: 70%;
            padding: 12px 16px;
            border-radius: 18px;
            position: relative;
        }
        
        .message.own .message-content {
            background: var(--primary-color);
            color: white;
            border-bottom-right-radius: 4px;
        }
        
        .message.other .message-content {
            background: var(--bg-secondary);
            color: var(--text-primary);
            border-bottom-left-radius: 4px;
        }
        
        .message-text {
            margin-bottom: 5px;
            line-height: 1.4;
        }
        
        .message-time {
            font-size: 11px;
            opacity: 0.7;
            text-align: right;
        }
        
        .message-input-container {
            padding: 20px;
            border-top: 1px solid var(--border-color);
            background: var(--bg-secondary);
        }
        
        .message-form {
            display: flex;
        }
        
        .input-group {
            display: flex;
            flex: 1;
            gap: 10px;
        }
        
        .message-form input {
            flex: 1;
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            border-radius: 25px;
            background: var(--bg-primary);
            color: var(--text-primary);
            font-size: 14px;
        }
        
        .message-form input:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        
        .btn-send {
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 45px;
            height: 45px;
            cursor: pointer;
            font-size: 18px;
            transition: all 0.3s ease;
        }
        
        .btn-send:hover {
            background: var(--primary-dark);
            transform: scale(1.05);
        }
        
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .header-stats {
                justify-content: center;
            }
            
            .chat-container {
                grid-template-columns: 1fr;
                height: 80vh;
            }
            
            .conversations-list {
                border-right: none;
                border-bottom: 1px solid var(--border-color);
                max-height: 200px;
            }
            
            .conversation-item {
                padding: 10px 15px;
            }
            
            .conversation-avatar img,
            .avatar-placeholder {
                width: 40px;
                height: 40px;
                font-size: 16px;
            }
        }
    </style>
</body>
</html>