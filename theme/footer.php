    </main>
    
    <!-- Подвал сайта -->
    <footer class="bg-dark text-light py-5 mt-5">
        <div class="container">
            <div class="row">
                <!-- О компании -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5 class="text-gradient mb-3">
                        <img src="<?php echo themeUrl('images/logo-white.png'); ?>" 
                             alt="<?php echo h(SITE_NAME); ?>" 
                             width="30" 
                             height="30" 
                             class="me-2">
                        <?php echo h(SITE_NAME); ?>
                    </h5>
                    <p class="text-light-secondary"><?php echo h(SITE_DESCRIPTION); ?></p>
                    
                    <!-- Социальные сети -->
                    <div class="social-links mt-3">
                        <h6 class="mb-2">Мы в социальных сетях:</h6>
                        <div class="d-flex gap-2">
                            <a href="https://vk.com/marketplace" 
                               class="icon-3d" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               title="ВКонтакте"
                               aria-label="Наша группа ВКонтакте">
                                <i class="fab fa-vk"></i>
                            </a>
                            <a href="https://t.me/marketplace" 
                               class="icon-3d" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               title="Telegram"
                               aria-label="Наш канал в Telegram">
                                <i class="fab fa-telegram"></i>
                            </a>
                            <a href="https://instagram.com/marketplace" 
                               class="icon-3d" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               title="Instagram"
                               aria-label="Наш Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="https://youtube.com/@marketplace" 
                               class="icon-3d" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               title="YouTube"
                               aria-label="Наш YouTube канал">
                                <i class="fab fa-youtube"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Полезные ссылки -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="text-white mb-3">Покупателям</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="/how-to-buy" class="text-light-secondary footer-link">
                                <i class="fas fa-shopping-cart me-1"></i>
                                Как купить
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="/delivery" class="text-light-secondary footer-link">
                                <i class="fas fa-truck me-1"></i>
                                Доставка
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="/payment" class="text-light-secondary footer-link">
                                <i class="fas fa-credit-card me-1"></i>
                                Оплата
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="/returns" class="text-light-secondary footer-link">
                                <i class="fas fa-undo me-1"></i>
                                Возврат
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="/warranty" class="text-light-secondary footer-link">
                                <i class="fas fa-shield-alt me-1"></i>
                                Гарантия
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- Продавцам -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="text-white mb-3">Продавцам</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="/seller/register" class="text-light-secondary footer-link">
                                <i class="fas fa-store me-1"></i>
                                Стать продавцом
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="/seller/guide" class="text-light-secondary footer-link">
                                <i class="fas fa-book me-1"></i>
                                Руководство
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="/seller/fees" class="text-light-secondary footer-link">
                                <i class="fas fa-percentage me-1"></i>
                                Тарифы
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="/seller/support" class="text-light-secondary footer-link">
                                <i class="fas fa-headset me-1"></i>
                                Поддержка
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- Компания -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="text-white mb-3">Компания</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="/about" class="text-light-secondary footer-link">
                                <i class="fas fa-info-circle me-1"></i>
                                О нас
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="/news" class="text-light-secondary footer-link">
                                <i class="fas fa-newspaper me-1"></i>
                                Новости
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="/careers" class="text-light-secondary footer-link">
                                <i class="fas fa-briefcase me-1"></i>
                                Карьера
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="/contact" class="text-light-secondary footer-link">
                                <i class="fas fa-envelope me-1"></i>
                                Контакты
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- Контакты -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="text-white mb-3">Связь с нами</h6>
                    <div class="contact-info">
                        <div class="mb-2">
                            <i class="fas fa-phone text-primary me-2"></i>
                            <a href="tel:+78001234567" class="text-light-secondary footer-link">
                                8 (800) 123-45-67
                            </a>
                        </div>
                        <div class="mb-2">
                            <i class="fas fa-envelope text-primary me-2"></i>
                            <a href="mailto:info@marketplace.ru" class="text-light-secondary footer-link">
                                info@marketplace.ru
                            </a>
                        </div>
                        <div class="mb-2">
                            <i class="fas fa-clock text-primary me-2"></i>
                            <span class="text-light-secondary">Ежедневно 9:00-21:00</span>
                        </div>
                        <div class="mb-2">
                            <i class="fas fa-map-marker-alt text-primary me-2"></i>
                            <span class="text-light-secondary">Москва, Россия</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr class="my-4 bg-secondary">
            
            <!-- Нижняя часть футера -->
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-light-secondary">
                        &copy; <?php echo date('Y'); ?> <?php echo h(SITE_NAME); ?>. Все права защищены.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="footer-links">
                        <a href="/privacy" class="text-light-secondary footer-link me-3">
                            Политика конфиденциальности
                        </a>
                        <a href="/terms" class="text-light-secondary footer-link me-3">
                            Пользовательское соглашение
                        </a>
                        <a href="/sitemap.xml" class="text-light-secondary footer-link">
                            Карта сайта
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Кнопка "Наверх" -->
    <button id="back-to-top" 
            class="btn btn-3d position-fixed d-none" 
            style="bottom: 20px; right: 20px; z-index: 1000;"
            title="Наверх"
            aria-label="Прокрутить страницу наверх">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <!-- Селектор градиентов -->
    <div class="gradient-selector">
        <div class="selector-title">Градиенты</div>
        <div class="gradient-options">
            <div class="gradient-option active" data-gradient="ocean" data-name="Ocean" title="Ocean Blue"></div>
            <div class="gradient-option" data-gradient="sunset" data-name="Sunset" title="Sunset Orange"></div>
            <div class="gradient-option" data-gradient="purple" data-name="Purple" title="Purple Rain"></div>
            <div class="gradient-option" data-gradient="forest" data-name="Forest" title="Forest Green"></div>
            <div class="gradient-option" data-gradient="fire" data-name="Fire" title="Fire Red"></div>
            <div class="gradient-option" data-gradient="arctic" data-name="Arctic" title="Arctic Blue"></div>
            <div class="gradient-option" data-gradient="cosmic" data-name="Cosmic" title="Cosmic Purple"></div>
            <div class="gradient-option" data-gradient="peach" data-name="Peach" title="Peach Pink"></div>
            <div class="gradient-option" data-gradient="mint" data-name="Mint" title="Mint Green"></div>
            <div class="gradient-option" data-gradient="royal" data-name="Royal" title="Royal Purple"></div>
        </div>
    </div>
    
    <!-- JavaScript Libraries -->
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" 
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" 
            crossorigin="anonymous"></script>
    
    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" 
            integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" 
            crossorigin="anonymous"></script>
    
    <!-- Materialize -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js" 
            crossorigin="anonymous"></script>
    
    <!-- Intersection Observer Polyfill для старых браузеров -->
    <script>
        if (!('IntersectionObserver' in window)) {
            document.write('<script src="https://cdn.jsdelivr.net/npm/intersection-observer@0.12.2/intersection-observer.js"><\/script>');
        }
    </script>
    
    <!-- jQuery Easing для плавной анимации -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js" 
            integrity="sha256-H3cjtrm/ztDeuhCN9I4yh4iN2Ybx/y1RM7rMmAesA0k=" 
            crossorigin="anonymous"></script>
    
    <!-- Основной JavaScript -->
    <script src="<?php echo themeUrl('js/main.js?v=' . filemtime(THEME_PATH . '/js/main.js')); ?>"></script>
    <script src="<?php echo themeUrl('js/pagination.js?v=' . filemtime(THEME_PATH . '/js/pagination.js')); ?>"></script>
    <script src="<?php echo themeUrl('js/filters.js?v=' . filemtime(THEME_PATH . '/js/filters.js')); ?>"></script>
    
    <!-- Обработчик кнопки "Наверх" -->
    <script>
        $(document).ready(function() {
            // Показ/скрытие кнопки "Наверх"
            $(window).scroll(function() {
                if ($(this).scrollTop() > 300) {
                    $('#back-to-top').removeClass('d-none').addClass('animate-fade-in');
                } else {
                    $('#back-to-top').addClass('d-none').removeClass('animate-fade-in');
                }
            });
            
            // Обработчик клика по кнопке "Наверх"
            $('#back-to-top').click(function() {
                $('html, body').animate({
                    scrollTop: 0
                }, 600, 'easeInOutCubic');
                return false;
            });
            
            // Удаление класса loading после полной загрузки
            $(window).on('load', function() {
                $('body').removeClass('loading').addClass('animate-fade-in');
            });
            
            // Обработчик изменения темы для системных компонентов
            $(document).on('themeChanged', function(e, theme) {
                // Обновление цвета theme-color для браузера
                $('meta[name="theme-color"]').attr('content', theme === 'dark' ? '#1a1a1a' : '#2196F3');
                
                // Уведомление об изменении темы
                console.log(`🎨 Theme changed to: ${theme}`);
            });
            
            // Обработчик клавиатурных сокращений
            $(document).keydown(function(e) {
                // Ctrl + K для фокуса на поиске
                if (e.ctrlKey && e.key === 'k') {
                    e.preventDefault();
                    $('.search-input').focus();
                }
                
                // Escape для закрытия модальных окон и подсказок
                if (e.key === 'Escape') {
                    $('.search-suggestions').fadeOut();
                    $('.custom-tooltip').remove();
                }
            });
            
            // Предварительная загрузка критических изображений
            const criticalImages = [
                '<?php echo themeUrl("images/logo.png"); ?>',
                '<?php echo themeUrl("images/favicon.ico"); ?>'
            ];
            
            criticalImages.forEach(src => {
                const img = new Image();
                img.src = src;
            });
            
            // Service Worker для кэширования (если поддерживается)
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function() {
                    navigator.serviceWorker.register('/sw.js')
                        .then(function(registration) {
                            console.log('SW registered: ', registration);
                        })
                        .catch(function(registrationError) {
                            console.log('SW registration failed: ', registrationError);
                        });
                });
            }
        });
    </script>
    
    <!-- Дополнительные скрипты для страниц -->
    <?php if (isset($additional_scripts) && !empty($additional_scripts)): ?>
        <?php foreach ($additional_scripts as $script): ?>
            <script src="<?php echo h($script); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Inline JavaScript для страниц -->
    <?php if (isset($inline_js) && !empty($inline_js)): ?>
        <script>
            <?php echo $inline_js; ?>
        </script>
    <?php endif; ?>
    
</body>
</html>