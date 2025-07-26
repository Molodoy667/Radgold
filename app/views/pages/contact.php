<?php $title = 'Контакты'; ?>

<div class="container">
    <div class="page-header">
        <h1>Свяжитесь с нами</h1>
        <p>Мы всегда готовы помочь! Выберите удобный способ связи.</p>
    </div>

    <div class="contact-content">
        <div class="contact-info-section">
            <div class="contact-methods">
                <div class="contact-method">
                    <div class="method-icon">
                        <i class="icon-mail"></i>
                    </div>
                    <div class="method-info">
                        <h3>Email поддержка</h3>
                        <p>support@radgold.online</p>
                        <span class="response-time">Ответ в течение 2-4 часов</span>
                    </div>
                </div>

                <div class="contact-method">
                    <div class="method-icon">
                        <i class="icon-message-circle"></i>
                    </div>
                    <div class="method-info">
                        <h3>Онлайн чат</h3>
                        <p>Доступен 24/7</p>
                        <button class="btn btn-primary" onclick="openLiveChat()">Начать чат</button>
                    </div>
                </div>

                <div class="contact-method">
                    <div class="method-icon">
                        <i class="icon-help-circle"></i>
                    </div>
                    <div class="method-info">
                        <h3>База знаний</h3>
                        <p>Ответы на частые вопросы</p>
                        <a href="/help" class="btn btn-secondary">Перейти в FAQ</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="contact-form-section">
            <div class="form-container">
                <h2>Отправить сообщение</h2>
                <form class="contact-form" method="POST" action="/contact/send">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Имя *</label>
                            <input type="text" id="name" name="name" required 
                                   value="<?= isset($user) ? htmlspecialchars($user['username']) : '' ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" required
                                   value="<?= isset($user) ? htmlspecialchars($user['email']) : '' ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Тема *</label>
                        <select id="subject" name="subject" required>
                            <option value="">Выберите тему</option>
                            <option value="general">Общие вопросы</option>
                            <option value="technical">Техническая поддержка</option>
                            <option value="billing">Вопросы по оплате</option>
                            <option value="dispute">Споры и жалобы</option>
                            <option value="partnership">Партнерство</option>
                            <option value="other">Другое</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Сообщение *</label>
                        <textarea id="message" name="message" rows="6" required 
                                  placeholder="Опишите вашу проблему или вопрос подробно..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="priority">Приоритет</label>
                        <select id="priority" name="priority">
                            <option value="low">Низкий</option>
                            <option value="medium" selected>Средний</option>
                            <option value="high">Высокий</option>
                            <option value="urgent">Срочный</option>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="icon-send"></i> Отправить сообщение
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="additional-info">
            <div class="info-cards">
                <div class="info-card">
                    <h3>Рабочие часы</h3>
                    <div class="schedule">
                        <div class="schedule-item">
                            <span class="day">Понедельник - Пятница</span>
                            <span class="hours">9:00 - 21:00 (MSK)</span>
                        </div>
                        <div class="schedule-item">
                            <span class="day">Суббота - Воскресенье</span>
                            <span class="hours">10:00 - 18:00 (MSK)</span>
                        </div>
                    </div>
                </div>

                <div class="info-card">
                    <h3>Частые вопросы</h3>
                    <ul class="faq-list">
                        <li><a href="/help#account">Как создать аккаунт?</a></li>
                        <li><a href="/help#payment">Способы оплаты</a></li>
                        <li><a href="/help#security">Безопасность сделок</a></li>
                        <li><a href="/help#disputes">Как открыть спор?</a></li>
                    </ul>
                </div>

                <div class="info-card">
                    <h3>Социальные сети</h3>
                    <div class="social-links">
                        <a href="#" class="social-link">
                            <i class="icon-brand-vk"></i>
                            <span>VKontakte</span>
                        </a>
                        <a href="#" class="social-link">
                            <i class="icon-brand-telegram"></i>
                            <span>Telegram</span>
                        </a>
                        <a href="#" class="social-link">
                            <i class="icon-brand-discord"></i>
                            <span>Discord</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.contact-content {
    max-width: 1200px;
    margin: 0 auto;
}

.contact-info-section {
    margin-bottom: 3rem;
}

.contact-methods {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.contact-method {
    display: flex;
    gap: 1.5rem;
    padding: 2rem;
    background: var(--card-bg);
    border-radius: 12px;
    transition: transform 0.3s ease;
}

.contact-method:hover {
    transform: translateY(-2px);
}

.method-icon {
    width: 80px;
    height: 80px;
    background: var(--accent-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    flex-shrink: 0;
}

.method-info h3 {
    margin: 0 0 0.5rem;
    color: var(--text-primary);
}

.method-info p {
    margin: 0 0 1rem;
    color: var(--text-secondary);
}

.response-time {
    font-size: 0.9rem;
    color: var(--accent-color);
    font-weight: 500;
}

.contact-form-section {
    margin-bottom: 3rem;
}

.form-container {
    background: var(--card-bg);
    padding: 3rem;
    border-radius: 12px;
    max-width: 800px;
    margin: 0 auto;
}

.form-container h2 {
    margin: 0 0 2rem;
    color: var(--text-primary);
    text-align: center;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
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
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid var(--border-color);
    border-radius: 8px;
    background: var(--bg-color);
    color: var(--text-primary);
    transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--accent-color);
}

.form-group textarea {
    resize: vertical;
    min-height: 120px;
}

.form-actions {
    text-align: center;
    margin-top: 2rem;
}

.additional-info {
    margin-top: 3rem;
}

.info-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.info-card {
    background: var(--card-bg);
    padding: 2rem;
    border-radius: 12px;
}

.info-card h3 {
    margin: 0 0 1.5rem;
    color: var(--text-primary);
    border-bottom: 2px solid var(--border-color);
    padding-bottom: 0.5rem;
}

.schedule-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
    color: var(--text-secondary);
}

.day {
    font-weight: 500;
}

.hours {
    color: var(--accent-color);
}

.faq-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.faq-list li {
    margin-bottom: 0.75rem;
}

.faq-list a {
    color: var(--text-secondary);
    text-decoration: none;
    transition: color 0.3s ease;
}

.faq-list a:hover {
    color: var(--accent-color);
}

.social-links {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.social-link {
    display: flex;
    align-items: center;
    gap: 1rem;
    color: var(--text-secondary);
    text-decoration: none;
    transition: color 0.3s ease;
}

.social-link:hover {
    color: var(--accent-color);
}

.social-link i {
    font-size: 1.5rem;
    width: 30px;
}

@media (max-width: 768px) {
    .contact-methods {
        grid-template-columns: 1fr;
    }
    
    .contact-method {
        flex-direction: column;
        text-align: center;
    }
    
    .form-container {
        padding: 2rem;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .info-cards {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function openLiveChat() {
    // Здесь можно интегрировать реальный чат (например, Tawk.to, Intercom, etc.)
    alert('Функция онлайн-чата будет доступна в ближайшее время!');
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.contact-form');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Простая валидация
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const subject = document.getElementById('subject').value;
        const message = document.getElementById('message').value.trim();
        
        if (!name || !email || !subject || !message) {
            alert('Пожалуйста, заполните все обязательные поля');
            return;
        }
        
        // Отправка формы
        const formData = new FormData(form);
        
        fetch('/contact/send', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Сообщение отправлено! Мы свяжемся с вами в ближайшее время.');
                form.reset();
            } else {
                alert('Ошибка при отправке сообщения. Попробуйте позже.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ошибка при отправке сообщения. Попробуйте позже.');
        });
    });
});
</script>