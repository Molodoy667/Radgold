<!-- Chat Widget -->
<div id="chatWidget" class="chat-widget">
    <!-- Chat Toggle Button -->
    <div id="chatToggle" class="chat-toggle">
        <div class="chat-toggle-content">
            <div class="chat-icon">
                <i class="fas fa-comments"></i>
            </div>
            <div class="chat-badge" id="chatBadge" style="display: none;">
                <span id="chatBadgeCount">0</span>
            </div>
        </div>
        <div class="chat-toggle-text">
            <span>Потрібна допомога?</span>
        </div>
    </div>

    <!-- Chat Window -->
    <div id="chatWindow" class="chat-window" style="display: none;">
        <!-- Chat Header -->
        <div class="chat-header">
            <div class="chat-header-info">
                <div class="chat-avatar">
                    <img src="/images/support-avatar.png" alt="Support" onerror="this.style.display='none'">
                    <i class="fas fa-headset"></i>
                </div>
                <div class="chat-header-text">
                    <h6>Підтримка AdBoard Pro</h6>
                    <small class="text-success">
                        <i class="fas fa-circle online-indicator"></i>
                        Онлайн
                    </small>
                </div>
            </div>
            <div class="chat-header-actions">
                <button class="btn btn-sm btn-ghost" id="chatMinimize">
                    <i class="fas fa-minus"></i>
                </button>
                <button class="btn btn-sm btn-ghost" id="chatClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <!-- Chat Messages -->
        <div class="chat-messages" id="chatMessages">
            <div class="chat-welcome">
                <div class="welcome-message">
                    <div class="welcome-avatar">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="welcome-text">
                        <h6>Вітаємо в AdBoard Pro! 👋</h6>
                        <p>Як ми можемо вам допомогти сьогодні?</p>
                    </div>
                </div>
                
                <!-- Quick Questions -->
                <div class="quick-questions">
                    <button class="quick-question-btn" data-question="Як подати оголошення?">
                        <i class="fas fa-bullhorn"></i>
                        Як подати оголошення?
                    </button>
                    <button class="quick-question-btn" data-question="Проблеми з реєстрацією">
                        <i class="fas fa-user-plus"></i>
                        Проблеми з реєстрацією
                    </button>
                    <button class="quick-question-btn" data-question="Платні послуги">
                        <i class="fas fa-credit-card"></i>
                        Платні послуги
                    </button>
                    <button class="quick-question-btn" data-question="Технічна підтримка">
                        <i class="fas fa-cog"></i>
                        Технічна підтримка
                    </button>
                </div>
            </div>

            <!-- Messages will be loaded here -->
            <div id="chatMessagesList"></div>
        </div>

        <!-- Chat Input -->
        <div class="chat-input">
            <form id="chatForm">
                <div class="chat-input-group">
                    <input 
                        type="text" 
                        id="chatMessageInput" 
                        placeholder="Введіть повідомлення..." 
                        autocomplete="off"
                        maxlength="500"
                    >
                    <button type="submit" class="chat-send-btn" id="chatSendBtn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
                <div class="chat-input-actions">
                    <small class="text-muted">
                        <span id="typingIndicator" style="display: none;">
                            <i class="fas fa-circle typing-dot"></i>
                            <i class="fas fa-circle typing-dot"></i>
                            <i class="fas fa-circle typing-dot"></i>
                            Агент друкує...
                        </span>
                    </small>
                    <div class="chat-actions">
                        <button type="button" class="btn btn-sm btn-ghost" id="chatAttach" title="Прикріпити файл">
                            <i class="fas fa-paperclip"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-ghost" id="chatEmoji" title="Емодзі">
                            <i class="fas fa-smile"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Chat Status -->
        <div class="chat-status" id="chatStatus">
            <div class="chat-connection-status">
                <span class="status-indicator" id="connectionStatus">
                    <i class="fas fa-circle text-success"></i>
                    Підключено
                </span>
            </div>
        </div>
    </div>
</div>

<style>
.chat-widget {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.chat-toggle {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50px;
    padding: 15px 20px;
    cursor: pointer;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 10px;
    color: white;
    max-width: 250px;
    position: relative;
}

.chat-toggle:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
}

.chat-toggle-content {
    position: relative;
    display: flex;
    align-items: center;
}

.chat-icon {
    font-size: 20px;
}

.chat-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #ff4757;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: bold;
}

.chat-toggle-text {
    font-weight: 500;
    white-space: nowrap;
}

.chat-window {
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 350px;
    height: 500px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    animation: chatSlideUp 0.3s ease;
}

@keyframes chatSlideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.chat-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.chat-header-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.chat-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.chat-avatar img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}

.chat-avatar i {
    font-size: 18px;
}

.chat-header-text h6 {
    margin: 0;
    font-weight: 600;
    font-size: 14px;
}

.chat-header-text small {
    font-size: 12px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.online-indicator {
    font-size: 8px;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.chat-header-actions {
    display: flex;
    gap: 5px;
}

.btn-ghost {
    background: transparent;
    border: none;
    color: white;
    padding: 5px;
    border-radius: 4px;
    cursor: pointer;
    opacity: 0.8;
    transition: opacity 0.2s;
}

.btn-ghost:hover {
    opacity: 1;
    background: rgba(255, 255, 255, 0.1);
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 0;
    background: #f8f9fa;
}

.chat-welcome {
    padding: 20px;
    background: white;
    border-bottom: 1px solid #eee;
}

.welcome-message {
    display: flex;
    gap: 12px;
    margin-bottom: 20px;
}

.welcome-avatar {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
}

.welcome-text h6 {
    margin: 0 0 5px 0;
    font-weight: 600;
    color: #333;
    font-size: 14px;
}

.welcome-text p {
    margin: 0;
    color: #666;
    font-size: 13px;
}

.quick-questions {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.quick-question-btn {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 10px 12px;
    text-align: left;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 13px;
    color: #495057;
    display: flex;
    align-items: center;
    gap: 8px;
}

.quick-question-btn:hover {
    background: #e9ecef;
    border-color: #667eea;
    color: #667eea;
}

.quick-question-btn i {
    color: #667eea;
    width: 16px;
}

.chat-message {
    margin-bottom: 15px;
    display: flex;
    align-items: flex-start;
    gap: 8px;
    padding: 0 15px;
}

.chat-message.user {
    flex-direction: row-reverse;
}

.chat-message.user .message-content {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 18px 18px 4px 18px;
}

.chat-message.agent .message-content {
    background: white;
    color: #333;
    border: 1px solid #e9ecef;
    border-radius: 18px 18px 18px 4px;
}

.message-avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    flex-shrink: 0;
}

.message-content {
    max-width: 250px;
    padding: 10px 15px;
    font-size: 13px;
    line-height: 1.4;
    word-wrap: break-word;
}

.message-time {
    font-size: 11px;
    color: #999;
    margin-top: 5px;
    text-align: center;
}

.chat-input {
    padding: 15px;
    background: white;
    border-top: 1px solid #e9ecef;
}

.chat-input-group {
    display: flex;
    align-items: center;
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 25px;
    padding: 5px;
}

.chat-input-group input {
    flex: 1;
    border: none;
    background: transparent;
    padding: 8px 15px;
    font-size: 14px;
    outline: none;
}

.chat-send-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 50%;
    width: 35px;
    height: 35px;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s;
}

.chat-send-btn:hover {
    transform: scale(1.05);
}

.chat-send-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.chat-input-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 8px;
}

.chat-actions {
    display: flex;
    gap: 5px;
}

.chat-actions .btn-ghost {
    color: #666;
    font-size: 14px;
}

.typing-indicator {
    display: flex;
    align-items: center;
    gap: 3px;
}

.typing-dot {
    font-size: 6px;
    animation: typingPulse 1.4s infinite;
}

.typing-dot:nth-child(2) { animation-delay: 0.2s; }
.typing-dot:nth-child(3) { animation-delay: 0.4s; }

@keyframes typingPulse {
    0%, 60%, 100% { opacity: 0.3; }
    30% { opacity: 1; }
}

.chat-status {
    background: #f8f9fa;
    padding: 8px 15px;
    border-top: 1px solid #e9ecef;
    font-size: 11px;
}

.status-indicator {
    display: flex;
    align-items: center;
    gap: 5px;
    color: #666;
}

.chat-messages::-webkit-scrollbar {
    width: 4px;
}

.chat-messages::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.chat-messages::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 2px;
}

.chat-messages::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .chat-widget {
        bottom: 10px;
        right: 10px;
    }
    
    .chat-window {
        width: 300px;
        height: 450px;
        bottom: 70px;
    }
    
    .chat-toggle {
        padding: 12px 16px;
    }
    
    .chat-toggle-text {
        font-size: 14px;
    }
}

@media (max-width: 480px) {
    .chat-window {
        width: calc(100vw - 20px);
        height: 400px;
        right: 10px;
    }
}
</style>

<script>
class ChatWidget {
    constructor() {
        this.isOpen = false;
        this.messages = [];
        this.unreadCount = 0;
        this.isTyping = false;
        this.connectionStatus = 'connected';
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.loadChatHistory();
        this.startHeartbeat();
    }
    
    bindEvents() {
        // Toggle chat
        document.getElementById('chatToggle').addEventListener('click', () => {
            this.toggleChat();
        });
        
        // Close chat
        document.getElementById('chatClose').addEventListener('click', () => {
            this.closeChat();
        });
        
        // Minimize chat
        document.getElementById('chatMinimize').addEventListener('click', () => {
            this.minimizeChat();
        });
        
        // Send message
        document.getElementById('chatForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.sendMessage();
        });
        
        // Quick questions
        document.querySelectorAll('.quick-question-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const question = e.currentTarget.dataset.question;
                this.sendQuickQuestion(question);
            });
        });
        
        // Input events
        const input = document.getElementById('chatMessageInput');
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });
        
        input.addEventListener('input', () => {
            this.handleTyping();
        });
    }
    
    toggleChat() {
        const chatWindow = document.getElementById('chatWindow');
        const chatToggle = document.getElementById('chatToggle');
        
        if (this.isOpen) {
            this.closeChat();
        } else {
            this.openChat();
        }
    }
    
    openChat() {
        document.getElementById('chatWindow').style.display = 'flex';
        document.getElementById('chatToggle').style.display = 'none';
        this.isOpen = true;
        this.markAllAsRead();
        
        // Focus input
        setTimeout(() => {
            document.getElementById('chatMessageInput').focus();
        }, 300);
    }
    
    closeChat() {
        document.getElementById('chatWindow').style.display = 'none';
        document.getElementById('chatToggle').style.display = 'flex';
        this.isOpen = false;
    }
    
    minimizeChat() {
        this.closeChat();
    }
    
    sendMessage() {
        const input = document.getElementById('chatMessageInput');
        const message = input.value.trim();
        
        if (!message) return;
        
        // Add user message
        this.addMessage('user', message);
        input.value = '';
        
        // Send to server
        this.sendToServer(message);
        
        // Simulate agent response (for demo)
        setTimeout(() => {
            this.simulateAgentResponse(message);
        }, 1000 + Math.random() * 2000);
    }
    
    sendQuickQuestion(question) {
        // Hide quick questions
        document.querySelector('.quick-questions').style.display = 'none';
        
        // Send message
        this.addMessage('user', question);
        this.sendToServer(question);
        
        // Simulate response
        setTimeout(() => {
            this.simulateAgentResponse(question);
        }, 1500);
    }
    
    addMessage(type, content, timestamp = null) {
        const messagesContainer = document.getElementById('chatMessagesList');
        const messageTime = timestamp || new Date().toLocaleTimeString('uk-UA', {
            hour: '2-digit',
            minute: '2-digit'
        });
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `chat-message ${type}`;
        messageDiv.innerHTML = `
            <div class="message-avatar">
                ${type === 'user' ? '<i class="fas fa-user"></i>' : '<i class="fas fa-headset"></i>'}
            </div>
            <div>
                <div class="message-content">${this.escapeHtml(content)}</div>
                <div class="message-time">${messageTime}</div>
            </div>
        `;
        
        messagesContainer.appendChild(messageDiv);
        this.scrollToBottom();
        
        // Update unread count if chat is closed
        if (!this.isOpen && type === 'agent') {
            this.unreadCount++;
            this.updateBadge();
        }
        
        this.messages.push({
            type,
            content,
            timestamp: new Date().toISOString()
        });
    }
    
    simulateAgentResponse(userMessage) {
        this.showTyping(true);
        
        setTimeout(() => {
            this.showTyping(false);
            
            let response = this.getAutoResponse(userMessage);
            this.addMessage('agent', response);
        }, 1000 + Math.random() * 1000);
    }
    
    getAutoResponse(message) {
        const responses = {
            'Як подати оголошення?': 'Для подачі оголошення натисніть кнопку "Подати оголошення" у верхньому меню. Заповніть всі необхідні поля: назву, опис, ціну, категорію та додайте фото. Після модерації оголошення буде опубліковано.',
            'Проблеми з реєстрацією': 'Якщо у вас виникли проблеми з реєстрацією, перевірте правильність введеного email та пароля. Пароль повинен містити мінімум 6 символів. Також перевірте папку "Спам" для листа підтвердження.',
            'Платні послуги': 'Ми пропонуємо різні платні послуги для просування оголошень: виділення кольором, закріплення зверху, термінове розміщення та інші. Детальніше у розділі "Мої оголошення".',
            'Технічна підтримка': 'Наша технічна підтримка працює цілодобово. Опишіть детально вашу проблему, і ми обов\'язково допоможемо її вирішити.'
        };
        
        // Простий пошук по ключовим словам
        for (const [key, value] of Object.entries(responses)) {
            if (message.toLowerCase().includes(key.toLowerCase()) || 
                key.toLowerCase().includes(message.toLowerCase())) {
                return value;
            }
        }
        
        // Пошук по ключовим словам
        const keywords = {
            'оголошення': 'Щоб подати оголошення, зареєструйтеся на сайті та натисніть "Подати оголошення". Заповніть форму та дочекайтеся модерації.',
            'реєстрація': 'Для реєстрації натисніть "Реєстрація" у верхньому меню. Введіть email, пароль та підтвердіть свій email.',
            'пароль': 'Якщо забули пароль, натисніть "Забули пароль?" на сторінці входу. Ми надішлемо інструкції на ваш email.',
            'оплата': 'Ми приймаємо оплату картками Visa/Mastercard, PayPal та банківськими переказами.',
            'модерація': 'Модерація оголошень зазвичай займає до 24 годин. Ми перевіряємо відповідність правилам сайту.',
            'видалити': 'Щоб видалити оголошення, перейдіть у "Мої оголошення" та натисніть кнопку видалення.',
        };
        
        for (const [keyword, response] of Object.entries(keywords)) {
            if (message.toLowerCase().includes(keyword)) {
                return response;
            }
        }
        
        // Стандартна відповідь
        const defaultResponses = [
            'Дякуємо за ваше повідомлення! Наш агент скоро з вами зв\'яжеться.',
            'Я передам ваше питання нашому спеціалісту. Зачекайте, будь ласка.',
            'Цікаве питання! Зараз знайду для вас найкращу відповідь.',
            'Одну хвилинку, перевіряю інформацію для вас...'
        ];
        
        return defaultResponses[Math.floor(Math.random() * defaultResponses.length)];
    }
    
    showTyping(show) {
        const indicator = document.getElementById('typingIndicator');
        indicator.style.display = show ? 'flex' : 'none';
        this.isTyping = show;
    }
    
    handleTyping() {
        // TODO: Відправити інформацію про друкування на сервер
    }
    
    scrollToBottom() {
        const messages = document.getElementById('chatMessages');
        messages.scrollTop = messages.scrollHeight;
    }
    
    markAllAsRead() {
        this.unreadCount = 0;
        this.updateBadge();
    }
    
    updateBadge() {
        const badge = document.getElementById('chatBadge');
        const count = document.getElementById('chatBadgeCount');
        
        if (this.unreadCount > 0) {
            count.textContent = this.unreadCount > 99 ? '99+' : this.unreadCount;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    }
    
    sendToServer(message) {
        // TODO: Відправити повідомлення на сервер через WebSocket або AJAX
        fetch('/ajax/chat-support.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'send_message',
                message: message,
                timestamp: new Date().toISOString()
            })
        }).catch(error => {
            console.error('Error sending message:', error);
        });
    }
    
    loadChatHistory() {
        // TODO: Завантажити історію чату з сервера
    }
    
    startHeartbeat() {
        // Перевірка з'єднання кожні 30 секунд
        setInterval(() => {
            this.checkConnection();
        }, 30000);
    }
    
    checkConnection() {
        const statusIndicator = document.getElementById('connectionStatus');
        
        fetch('/ajax/chat-support.php?action=ping')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.updateConnectionStatus('connected');
                } else {
                    this.updateConnectionStatus('disconnected');
                }
            })
            .catch(() => {
                this.updateConnectionStatus('disconnected');
            });
    }
    
    updateConnectionStatus(status) {
        const statusIndicator = document.getElementById('connectionStatus');
        
        if (status === 'connected') {
            statusIndicator.innerHTML = '<i class="fas fa-circle text-success"></i> Підключено';
        } else {
            statusIndicator.innerHTML = '<i class="fas fa-circle text-danger"></i> Немає з\'єднання';
        }
        
        this.connectionStatus = status;
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Ініціалізуємо чат після завантаження сторінки
document.addEventListener('DOMContentLoaded', function() {
    window.chatWidget = new ChatWidget();
});
</script>