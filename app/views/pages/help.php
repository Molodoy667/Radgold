<?php $title = 'Помощь и FAQ'; ?>

<div class="container">
    <div class="page-header">
        <h1>Центр помощи</h1>
        <p>Найдите ответы на часто задаваемые вопросы или свяжитесь с нашей службой поддержки</p>
    </div>

    <div class="help-content">
        <div class="search-section">
            <div class="search-box">
                <input type="text" id="helpSearch" placeholder="Поиск по базе знаний...">
                <button type="button" onclick="searchHelp()">
                    <i class="icon-search"></i>
                </button>
            </div>
        </div>

        <div class="help-categories">
            <div class="category-card" onclick="showCategory('account')">
                <i class="icon-user"></i>
                <h3>Аккаунт и регистрация</h3>
                <p>Создание аккаунта, настройки профиля, безопасность</p>
            </div>
            
            <div class="category-card" onclick="showCategory('buying')">
                <i class="icon-shopping-cart"></i>
                <h3>Покупка товаров</h3>
                <p>Как покупать, способы оплаты, гарантии</p>
            </div>
            
            <div class="category-card" onclick="showCategory('selling')">
                <i class="icon-tag"></i>
                <h3>Продажа товаров</h3>
                <p>Создание объявлений, управление товарами</p>
            </div>
            
            <div class="category-card" onclick="showCategory('payment')">
                <i class="icon-credit-card"></i>
                <h3>Платежи и выводы</h3>
                <p>Способы оплаты, комиссии, вывод средств</p>
            </div>
            
            <div class="category-card" onclick="showCategory('security')">
                <i class="icon-shield"></i>
                <h3>Безопасность</h3>
                <p>Защита аккаунта, избежание мошенничества</p>
            </div>
            
            <div class="category-card" onclick="showCategory('disputes')">
                <i class="icon-alert-circle"></i>
                <h3>Споры и жалобы</h3>
                <p>Решение конфликтов, возврат средств</p>
            </div>
        </div>

        <div class="faq-sections">
            <!-- Аккаунт и регистрация -->
            <div class="faq-category" id="account">
                <h2>Аккаунт и регистрация</h2>
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <h3>Как создать аккаунт на GameMarket Pro?</h3>
                        <i class="icon-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Для создания аккаунта:</p>
                        <ol>
                            <li>Нажмите кнопку "Регистрация" в правом верхнем углу</li>
                            <li>Заполните форму: имя пользователя, email, пароль</li>
                            <li>Подтвердите согласие с пользовательским соглашением</li>
                            <li>Проверьте email и перейдите по ссылке подтверждения</li>
                        </ol>
                        <p>После этого вы сможете войти в систему и начать пользоваться платформой.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <h3>Я забыл пароль. Как его восстановить?</h3>
                        <i class="icon-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Для восстановления пароля:</p>
                        <ol>
                            <li>На странице входа нажмите "Забыли пароль?"</li>
                            <li>Введите email, указанный при регистрации</li>
                            <li>Проверьте почту и перейдите по ссылке</li>
                            <li>Создайте новый пароль</li>
                        </ol>
                        <p>Если письмо не пришло, проверьте папку "Спам" или обратитесь в поддержку.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <h3>Можно ли изменить имя пользователя?</h3>
                        <i class="icon-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Да, имя пользователя можно изменить в настройках профиля. Обратите внимание:</p>
                        <ul>
                            <li>Изменение возможно раз в 30 дней</li>
                            <li>Новое имя должно быть уникальным</li>
                            <li>Старые ссылки на профиль могут перестать работать</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Покупка товаров -->
            <div class="faq-category" id="buying">
                <h2>Покупка товаров</h2>
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <h3>Как купить товар на платформе?</h3>
                        <i class="icon-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Процесс покупки:</p>
                        <ol>
                            <li>Найдите нужный товар через поиск или каталог</li>
                            <li>Изучите описание и отзывы о продавце</li>
                            <li>Нажмите кнопку "Купить"</li>
                            <li>Выберите способ оплаты и оплатите</li>
                            <li>Дождитесь передачи товара от продавца</li>
                            <li>Подтвердите получение товара</li>
                        </ol>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <h3>Как работает система гарантий?</h3>
                        <i class="icon-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Система гарантий защищает покупателей:</p>
                        <ul>
                            <li>Средства блокируются на нашем счете до завершения сделки</li>
                            <li>Продавец получает деньги только после подтверждения покупателем</li>
                            <li>Если товар не соответствует описанию, возможен возврат</li>
                            <li>Автоматическое завершение сделки через 72 часа без споров</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <h3>Что делать, если товар не соответствует описанию?</h3>
                        <i class="icon-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Если товар не соответствует описанию:</p>
                        <ol>
                            <li>НЕ подтверждайте получение товара</li>
                            <li>Откройте спор в разделе "Мои покупки"</li>
                            <li>Предоставьте доказательства (скриншоты, описание проблемы)</li>
                            <li>Дождитесь решения модератора</li>
                        </ol>
                        <p>При обоснованной жалобе средства будут возвращены.</p>
                    </div>
                </div>
            </div>

            <!-- Продажа товаров -->
            <div class="faq-category" id="selling">
                <h2>Продажа товаров</h2>
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <h3>Как создать объявление о продаже?</h3>
                        <i class="icon-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Для создания объявления:</p>
                        <ol>
                            <li>Войдите в аккаунт и нажмите "Продать"</li>
                            <li>Выберите категорию товара</li>
                            <li>Заполните подробное описание</li>
                            <li>Добавьте качественные фотографии</li>
                            <li>Укажите честную цену</li>
                            <li>Опубликуйте объявление</li>
                        </ol>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <h3>Какие товары можно продавать?</h3>
                        <i class="icon-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Разрешено продавать:</p>
                        <ul>
                            <li>Игровые аккаунты с честно полученными достижениями</li>
                            <li>Внутриигровую валюту и предметы</li>
                            <li>Услуги прокачки и бустинга</li>
                            <li>Игровые ключи и подписки</li>
                        </ul>
                        <p><strong>Запрещено:</strong> краденые аккаунты, читы, нарушение правил игр.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <h3>Когда я получу деньги за проданный товар?</h3>
                        <i class="icon-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Средства поступают на ваш баланс:</p>
                        <ul>
                            <li>После подтверждения покупателем получения товара</li>
                            <li>Автоматически через 72 часа, если нет споров</li>
                            <li>После положительного решения спора в вашу пользу</li>
                        </ul>
                        <p>Комиссия платформы составляет 5% от суммы сделки.</p>
                    </div>
                </div>
            </div>

            <!-- Платежи -->
            <div class="faq-category" id="payment">
                <h2>Платежи и выводы</h2>
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <h3>Какие способы оплаты поддерживаются?</h3>
                        <i class="icon-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Доступные способы оплаты:</p>
                        <ul>
                            <li><strong>Банковские карты:</strong> Visa, MasterCard, МИР</li>
                            <li><strong>Электронные кошельки:</strong> WebMoney, Qiwi, ЮMoney</li>
                            <li><strong>Криптовалюты:</strong> Bitcoin, Ethereum, USDT</li>
                            <li><strong>Мобильные платежи:</strong> Apple Pay, Google Pay</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <h3>Как вывести деньги с баланса?</h3>
                        <i class="icon-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Для вывода средств:</p>
                        <ol>
                            <li>Перейдите в раздел "Баланс"</li>
                            <li>Нажмите "Вывести средства"</li>
                            <li>Выберите способ вывода</li>
                            <li>Укажите реквизиты</li>
                            <li>Подтвердите операцию</li>
                        </ol>
                        <p>Минимальная сумма вывода - 100 рублей. Комиссия зависит от способа вывода.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <h3>Сколько времени занимает вывод средств?</h3>
                        <i class="icon-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Сроки вывода:</p>
                        <ul>
                            <li><strong>Банковские карты:</strong> 1-3 рабочих дня</li>
                            <li><strong>Электронные кошельки:</strong> до 24 часов</li>
                            <li><strong>Криптовалюты:</strong> до 2 часов</li>
                            <li><strong>Банковский перевод:</strong> 3-5 рабочих дней</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Безопасность -->
            <div class="faq-category" id="security">
                <h2>Безопасность</h2>
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <h3>Как защитить свой аккаунт?</h3>
                        <i class="icon-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Рекомендации по безопасности:</p>
                        <ul>
                            <li>Используйте сложный уникальный пароль</li>
                            <li>Включите двухфакторную аутентификацию</li>
                            <li>Не передавайте данные входа третьим лицам</li>
                            <li>Регулярно проверяйте активность аккаунта</li>
                            <li>Выходите из аккаунта на чужих устройствах</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <h3>Как распознать мошенников?</h3>
                        <i class="icon-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Признаки мошенничества:</p>
                        <ul>
                            <li>Просьба оплатить вне платформы</li>
                            <li>Слишком низкие цены</li>
                            <li>Требование предоплаты без гарантий</li>
                            <li>Давление и спешка</li>
                            <li>Плохие отзывы или их отсутствие</li>
                        </ul>
                        <p><strong>Всегда используйте встроенную систему платежей!</strong></p>
                    </div>
                </div>
            </div>

            <!-- Споры -->
            <div class="faq-category" id="disputes">
                <h2>Споры и жалобы</h2>
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <h3>Как открыть спор?</h3>
                        <i class="icon-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Для открытия спора:</p>
                        <ol>
                            <li>Перейдите в раздел "Мои покупки" или "Мои продажи"</li>
                            <li>Найдите проблемную сделку</li>
                            <li>Нажмите "Открыть спор"</li>
                            <li>Опишите проблему и приложите доказательства</li>
                            <li>Дождитесь рассмотрения модератором</li>
                        </ol>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <h3>Сколько времени рассматривается спор?</h3>
                        <i class="icon-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Сроки рассмотрения споров:</p>
                        <ul>
                            <li><strong>Стандартные споры:</strong> 3-5 рабочих дней</li>
                            <li><strong>Сложные случаи:</strong> до 10 рабочих дней</li>
                            <li><strong>Срочные споры:</strong> 24-48 часов</li>
                        </ul>
                        <p>Решение модератора является окончательным.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="contact-support">
            <h2>Не нашли ответ?</h2>
            <p>Наша служба поддержки готова помочь вам 24/7</p>
            <div class="support-options">
                <a href="/contact" class="support-option">
                    <i class="icon-mail"></i>
                    <h3>Написать в поддержку</h3>
                    <p>Ответ в течение 2-4 часов</p>
                </a>
                
                <a href="#" class="support-option" onclick="openLiveChat()">
                    <i class="icon-message-circle"></i>
                    <h3>Онлайн чат</h3>
                    <p>Мгновенные ответы</p>
                </a>
                
                <div class="support-option">
                    <i class="icon-phone"></i>
                    <h3>Telegram бот</h3>
                    <p>@GameMarketProBot</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.help-content {
    max-width: 1000px;
    margin: 0 auto;
}

.search-section {
    margin-bottom: 3rem;
}

.search-box {
    position: relative;
    max-width: 600px;
    margin: 0 auto;
}

.search-box input {
    width: 100%;
    padding: 1rem 3rem 1rem 1rem;
    border: 2px solid var(--border-color);
    border-radius: 25px;
    background: var(--card-bg);
    color: var(--text-primary);
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.search-box input:focus {
    outline: none;
    border-color: var(--accent-color);
}

.search-box button {
    position: absolute;
    right: 0.5rem;
    top: 50%;
    transform: translateY(-50%);
    width: 40px;
    height: 40px;
    border: none;
    border-radius: 50%;
    background: var(--accent-color);
    color: white;
    cursor: pointer;
    transition: background 0.3s ease;
}

.search-box button:hover {
    background: #8b5cf6;
}

.help-categories {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3rem;
}

.category-card {
    background: var(--card-bg);
    padding: 2rem;
    border-radius: 12px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.category-card:hover {
    transform: translateY(-5px);
    border-color: var(--accent-color);
}

.category-card i {
    font-size: 3rem;
    color: var(--accent-color);
    margin-bottom: 1rem;
}

.category-card h3 {
    margin: 0 0 0.5rem;
    color: var(--text-primary);
}

.category-card p {
    margin: 0;
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.faq-sections {
    background: var(--card-bg);
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 3rem;
}

.faq-category {
    margin-bottom: 3rem;
}

.faq-category:last-child {
    margin-bottom: 0;
}

.faq-category h2 {
    color: var(--text-primary);
    border-bottom: 2px solid var(--accent-color);
    padding-bottom: 0.5rem;
    margin-bottom: 2rem;
}

.faq-item {
    margin-bottom: 1rem;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    overflow: hidden;
}

.faq-question {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    background: var(--bg-color);
    cursor: pointer;
    transition: background 0.3s ease;
}

.faq-question:hover {
    background: var(--border-color);
}

.faq-question h3 {
    margin: 0;
    color: var(--text-primary);
    font-size: 1rem;
}

.faq-question i {
    color: var(--accent-color);
    transition: transform 0.3s ease;
}

.faq-question.active i {
    transform: rotate(180deg);
}

.faq-answer {
    padding: 0 1.5rem;
    max-height: 0;
    overflow: hidden;
    transition: all 0.3s ease;
}

.faq-answer.open {
    padding: 1.5rem;
    max-height: 500px;
}

.faq-answer p {
    color: var(--text-secondary);
    line-height: 1.6;
    margin-bottom: 1rem;
}

.faq-answer ul,
.faq-answer ol {
    color: var(--text-secondary);
    line-height: 1.6;
    padding-left: 1.5rem;
}

.faq-answer li {
    margin-bottom: 0.5rem;
}

.contact-support {
    background: var(--card-bg);
    border-radius: 12px;
    padding: 3rem;
    text-align: center;
}

.contact-support h2 {
    margin: 0 0 1rem;
    color: var(--text-primary);
}

.contact-support p {
    margin: 0 0 2rem;
    color: var(--text-secondary);
}

.support-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.support-option {
    background: var(--bg-color);
    padding: 2rem;
    border-radius: 12px;
    text-decoration: none;
    color: var(--text-primary);
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.support-option:hover {
    transform: translateY(-2px);
    border-color: var(--accent-color);
    color: var(--text-primary);
}

.support-option i {
    font-size: 2.5rem;
    color: var(--accent-color);
    margin-bottom: 1rem;
}

.support-option h3 {
    margin: 0 0 0.5rem;
    color: var(--text-primary);
}

.support-option p {
    margin: 0;
    color: var(--text-secondary);
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .help-categories {
        grid-template-columns: 1fr;
    }
    
    .support-options {
        grid-template-columns: 1fr;
    }
    
    .faq-sections {
        padding: 1.5rem;
    }
    
    .faq-question {
        padding: 1rem;
    }
    
    .faq-answer.open {
        padding: 1rem;
    }
}
</style>

<script>
function toggleFaq(element) {
    const answer = element.nextElementSibling;
    const isOpen = answer.classList.contains('open');
    
    // Закрываем все открытые FAQ
    document.querySelectorAll('.faq-answer.open').forEach(item => {
        item.classList.remove('open');
    });
    
    document.querySelectorAll('.faq-question.active').forEach(item => {
        item.classList.remove('active');
    });
    
    // Открываем текущий, если он был закрыт
    if (!isOpen) {
        answer.classList.add('open');
        element.classList.add('active');
    }
}

function showCategory(categoryId) {
    const category = document.getElementById(categoryId);
    if (category) {
        category.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function searchHelp() {
    const query = document.getElementById('helpSearch').value.toLowerCase();
    const faqItems = document.querySelectorAll('.faq-item');
    
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question h3').textContent.toLowerCase();
        const answer = item.querySelector('.faq-answer').textContent.toLowerCase();
        
        if (question.includes(query) || answer.includes(query)) {
            item.style.display = 'block';
            if (query.length > 2) {
                // Подсвечиваем найденный элемент
                item.style.borderColor = 'var(--accent-color)';
                setTimeout(() => {
                    item.style.borderColor = 'var(--border-color)';
                }, 3000);
            }
        } else {
            item.style.display = query.length > 2 ? 'none' : 'block';
        }
    });
    
    // Если поиск пустой, показываем все
    if (query.length === 0) {
        faqItems.forEach(item => {
            item.style.display = 'block';
        });
    }
}

function openLiveChat() {
    alert('Функция онлайн-чата будет доступна в ближайшее время!');
}

// Поиск в реальном времени
document.getElementById('helpSearch').addEventListener('input', function() {
    if (this.value.length > 2 || this.value.length === 0) {
        searchHelp();
    }
});

// Поиск по Enter
document.getElementById('helpSearch').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        searchHelp();
    }
});
</script>