<?php $title = 'О нас'; ?>

<div class="container">
    <div class="page-header">
        <h1>О GameMarket Pro</h1>
    </div>

    <div class="about-content">
        <div class="hero-section">
            <div class="hero-text">
                <h2>Современный маркетплейс игрового контента</h2>
                <p class="lead">GameMarket Pro — это надежная платформа для покупки и продажи игровых аккаунтов, услуг бустинга, фарма и внутриигрового контента. Мы создали безопасную среду для геймеров, где каждая сделка защищена.</p>
            </div>
        </div>

        <div class="features-section">
            <h3>Наши преимущества</h3>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="icon-shield"></i>
                    </div>
                    <h4>Безопасность</h4>
                    <p>Все сделки проходят через систему гарантий. Ваши средства защищены до завершения сделки.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="icon-users"></i>
                    </div>
                    <h4>Сообщество</h4>
                    <p>Более 10,000 активных пользователей. Система рейтингов и отзывов помогает выбрать надежного продавца.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="icon-clock"></i>
                    </div>
                    <h4>Скорость</h4>
                    <p>Быстрые сделки и мгновенная передача цифровых товаров. Поддержка 24/7.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="icon-gamepad"></i>
                    </div>
                    <h4>Широкий выбор</h4>
                    <p>Аккаунты для всех популярных игр, услуги прокачки, редкие предметы и многое другое.</p>
                </div>
            </div>
        </div>

        <div class="stats-section">
            <h3>Наши достижения</h3>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">10,000+</div>
                    <div class="stat-label">Пользователей</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">50,000+</div>
                    <div class="stat-label">Успешных сделок</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">100+</div>
                    <div class="stat-label">Поддерживаемых игр</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">99.8%</div>
                    <div class="stat-label">Положительных отзывов</div>
                </div>
            </div>
        </div>

        <div class="mission-section">
            <div class="mission-content">
                <h3>Наша миссия</h3>
                <p>Мы стремимся создать самый безопасный и удобный маркетплейс для игрового сообщества. Наша цель — дать каждому геймеру возможность легко и безопасно торговать игровыми ценностями.</p>
                
                <h4>Что мы предлагаем:</h4>
                <ul class="mission-list">
                    <li><strong>Игровые аккаунты</strong> — готовые аккаунты с прокачкой для популярных игр</li>
                    <li><strong>Услуги бустинга</strong> — профессиональная прокачка рейтинга и достижений</li>
                    <li><strong>Внутриигровой контент</strong> — валюта, предметы, скины и другие цифровые товары</li>
                    <li><strong>Консультации</strong> — помощь от опытных игроков и тренеров</li>
                </ul>
            </div>
            <div class="mission-image">
                <i class="icon-target"></i>
            </div>
        </div>

        <div class="team-section">
            <h3>Команда</h3>
            <p>GameMarket Pro создан командой опытных разработчиков и геймеров, которые понимают потребности игрового сообщества. Мы работаем над постоянным улучшением платформы и внедрением новых функций.</p>
            
            <div class="contact-info">
                <h4>Свяжитесь с нами</h4>
                <div class="contact-grid">
                    <div class="contact-item">
                        <i class="icon-mail"></i>
                        <span>support@radgold.online</span>
                    </div>
                    <div class="contact-item">
                        <i class="icon-globe"></i>
                        <span>radgold.online</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.about-content {
    max-width: 1000px;
    margin: 0 auto;
}

.hero-section {
    background: var(--card-bg);
    border-radius: 12px;
    padding: 3rem;
    margin-bottom: 3rem;
    text-align: center;
}

.hero-text h2 {
    margin: 0 0 1rem;
    color: var(--text-primary);
    font-size: 2.5rem;
}

.lead {
    font-size: 1.2rem;
    color: var(--text-secondary);
    line-height: 1.6;
    margin: 0;
}

.features-section,
.stats-section,
.mission-section,
.team-section {
    margin-bottom: 3rem;
}

.features-section h3,
.stats-section h3,
.mission-section h3,
.team-section h3 {
    text-align: center;
    margin: 0 0 2rem;
    color: var(--text-primary);
    font-size: 2rem;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
}

.feature-card {
    background: var(--card-bg);
    padding: 2rem;
    border-radius: 12px;
    text-align: center;
    transition: transform 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-5px);
}

.feature-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1rem;
    background: var(--accent-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
}

.feature-card h4 {
    margin: 0 0 1rem;
    color: var(--text-primary);
    font-size: 1.3rem;
}

.feature-card p {
    color: var(--text-secondary);
    line-height: 1.5;
    margin: 0;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
}

.stat-item {
    background: var(--card-bg);
    padding: 2rem;
    border-radius: 12px;
    text-align: center;
}

.stat-number {
    font-size: 3rem;
    font-weight: bold;
    color: var(--accent-color);
    margin-bottom: 0.5rem;
}

.stat-label {
    color: var(--text-secondary);
    font-size: 1.1rem;
}

.mission-section {
    background: var(--card-bg);
    border-radius: 12px;
    padding: 3rem;
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 3rem;
    align-items: center;
}

.mission-content h3 {
    margin: 0 0 1rem;
    color: var(--text-primary);
    text-align: left;
}

.mission-content h4 {
    margin: 2rem 0 1rem;
    color: var(--text-primary);
}

.mission-content p {
    color: var(--text-secondary);
    line-height: 1.6;
    margin: 0 0 1rem;
}

.mission-list {
    color: var(--text-secondary);
    line-height: 1.8;
}

.mission-list li {
    margin-bottom: 0.5rem;
}

.mission-list strong {
    color: var(--accent-color);
}

.mission-image {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 8rem;
    color: var(--accent-color);
    opacity: 0.3;
}

.team-section {
    background: var(--card-bg);
    border-radius: 12px;
    padding: 3rem;
    text-align: center;
}

.team-section h3 {
    margin-bottom: 1rem;
}

.team-section p {
    color: var(--text-secondary);
    line-height: 1.6;
    margin: 0 0 2rem;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
}

.contact-info h4 {
    margin: 0 0 1rem;
    color: var(--text-primary);
}

.contact-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    max-width: 500px;
    margin: 0 auto;
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-secondary);
    justify-content: center;
}

.contact-item i {
    color: var(--accent-color);
}

@media (max-width: 768px) {
    .hero-text h2 {
        font-size: 2rem;
    }
    
    .lead {
        font-size: 1.1rem;
    }
    
    .features-grid,
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .mission-section {
        grid-template-columns: 1fr;
        text-align: center;
    }
    
    .mission-image {
        font-size: 4rem;
    }
    
    .hero-section,
    .mission-section,
    .team-section {
        padding: 2rem;
    }
}
</style>