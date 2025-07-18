    <!-- Footer -->
    <footer class="bg-dark text-light py-5" style="background-color: var(--footer-bg) !important;">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="d-flex align-items-center">
                        <?php 
                        $logo_path = Settings::get('site_logo', '');
                        if (!empty($logo_path) && file_exists($logo_path)): 
                        ?>
                            <img src="<?php echo Settings::getLogoUrl(); ?>" alt="<?php echo htmlspecialchars(Settings::get('site_name')); ?>" class="me-2" style="max-height: 30px;">
                        <?php else: ?>
                            <i class="fas fa-bullhorn me-2"></i>
                        <?php endif; ?>
                        <?php echo htmlspecialchars(Settings::get('site_name', 'Дошка Оголошень')); ?>
                    </h5>
                    <p class="text-muted"><?php echo htmlspecialchars(Settings::get('site_description', 'Найкраща дошка оголошень в Україні')); ?></p>
                    
                    <!-- Соціальні мережі -->
                    <div class="social-links">
                        <?php 
                        $social_links = [
                            'social_facebook' => ['fab fa-facebook-f', 'Facebook'],
                            'social_instagram' => ['fab fa-instagram', 'Instagram'],
                            'social_telegram' => ['fab fa-telegram-plane', 'Telegram'],
                            'social_twitter' => ['fab fa-twitter', 'Twitter'],
                            'social_youtube' => ['fab fa-youtube', 'YouTube']
                        ];
                        
                        foreach ($social_links as $key => $data):
                            $url = Settings::get($key, '');
                            if (!empty($url)):
                        ?>
                            <a href="<?php echo htmlspecialchars($url); ?>" target="_blank" rel="noopener" 
                               class="text-light me-3" title="<?php echo $data[1]; ?>">
                                <i class="<?php echo $data[0]; ?> fa-lg"></i>
                            </a>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <h6>Навігація</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo getBaseUrl(); ?>/index.php" class="text-muted text-decoration-none" data-spa data-page="home">Головна</a></li>
                        <li><a href="<?php echo getBaseUrl(); ?>/pages/categories.php" class="text-muted text-decoration-none" data-spa data-page="categories">Категорії</a></li>
                        <?php if (Settings::get('enable_search', true)): ?>
                        <li><a href="<?php echo getBaseUrl(); ?>/pages/search.php" class="text-muted text-decoration-none" data-spa data-page="search">Пошук</a></li>
                        <?php endif; ?>
                        <li><a href="<?php echo getBaseUrl(); ?>/pages/add_ad.php" class="text-muted text-decoration-none" data-spa data-page="add_ad">Подати оголошення</a></li>
                    </ul>
                </div>
                
                <div class="col-md-2 mb-4">
                    <h6>Допомога</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo getBaseUrl(); ?>/pages/help.php" class="text-muted text-decoration-none" data-spa data-page="help">Довідка</a></li>
                        <li><a href="<?php echo getBaseUrl(); ?>/pages/rules.php" class="text-muted text-decoration-none" data-spa data-page="rules">Правила</a></li>
                        <li><a href="<?php echo getBaseUrl(); ?>/pages/privacy.php" class="text-muted text-decoration-none" data-spa data-page="privacy">Конфіденційність</a></li>
                        <li><a href="<?php echo getBaseUrl(); ?>/pages/contact.php" class="text-muted text-decoration-none" data-spa data-page="contact">Контакти</a></li>
                    </ul>
                </div>
                
                <div class="col-md-3 mb-4">
                    <h6>Контакти</h6>
                    <ul class="list-unstyled text-muted">
                        <?php 
                        $contact_phone = Settings::get('contact_phone', '');
                        if (!empty($contact_phone)): 
                        ?>
                        <li class="mb-2">
                            <i class="fas fa-phone me-2"></i>
                            <a href="tel:<?php echo str_replace([' ', '(', ')', '-'], '', $contact_phone); ?>" 
                               class="text-muted text-decoration-none">
                                <?php echo htmlspecialchars($contact_phone); ?>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php 
                        $contact_email = Settings::get('contact_email', '');
                        if (!empty($contact_email)): 
                        ?>
                        <li class="mb-2">
                            <i class="fas fa-envelope me-2"></i>
                            <a href="mailto:<?php echo htmlspecialchars($contact_email); ?>" 
                               class="text-muted text-decoration-none">
                                <?php echo htmlspecialchars($contact_email); ?>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php 
                        $contact_address = Settings::get('contact_address', '');
                        if (!empty($contact_address)): 
                        ?>
                        <li class="mb-2">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            <?php echo htmlspecialchars($contact_address); ?>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-muted mb-0">
                        &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(Settings::get('site_name', 'Дошка Оголошень')); ?>. 
                        Всі права захищені.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <small class="text-muted">
                        Створено з ❤️ в Україні
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button class="btn btn-primary btn-back-to-top" id="backToTop">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS Animation -->
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <!-- Custom JS -->
    <script src="<?php echo getBaseUrl(); ?>/assets/js/main.js"></script>
    <!-- SPA JS -->
    <script src="<?php echo getBaseUrl(); ?>/assets/js/spa.js"></script>

    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });
        
        // Set theme colors dynamically
        document.documentElement.style.setProperty('--theme-color', '<?php echo Settings::get('theme_color', '#007bff'); ?>');
        document.documentElement.style.setProperty('--theme-secondary', '<?php echo Settings::get('theme_secondary_color', '#6c757d'); ?>');
    </script>

    <?php if (Settings::get('debug_mode', false)): ?>
    <!-- Debug Info (only visible in debug mode) -->
    <div class="position-fixed bottom-0 start-0 bg-dark text-light p-2 small" style="z-index: 1000; opacity: 0.7;">
        Debug: PHP <?php echo phpversion(); ?> | 
        Memory: <?php echo round(memory_get_usage() / 1024 / 1024, 2); ?>MB |
        Time: <?php echo round((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000, 2); ?>ms
    </div>
    <?php endif; ?>

</body>
</html>