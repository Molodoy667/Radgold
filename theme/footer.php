    </main>
    
    <!-- Подвал сайта -->
    <footer class="bg-dark text-light py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5><?php echo h(SITE_NAME); ?></h5>
                    <p><?php echo h(SITE_DESCRIPTION); ?></p>
                    <div class="social-links">
                        <a href="#" class="text-light me-3"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <h5>Навигация</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo SITE_URL; ?>" class="text-light">Главная</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/catalog" class="text-light">Каталог</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/about" class="text-light">О нас</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/contact" class="text-light">Контакты</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/privacy" class="text-light">Политика конфиденциальности</a></li>
                    </ul>
                </div>
                
                <div class="col-md-4">
                    <h5>Контакты</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-map-marker-alt"></i> г. Москва, ул. Примерная, д. 123</li>
                        <li><i class="fas fa-phone"></i> +7 (999) 123-45-67</li>
                        <li><i class="fas fa-envelope"></i> info@marketplace.ru</li>
                    </ul>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; <?php echo date('Y'); ?> <?php echo h(SITE_NAME); ?>. Все права защищены.</p>
                </div>
                <div class="col-md-6 text-end">
                    <p>Разработано с ❤️ для вашего бизнеса</p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script src="<?php echo themeUrl('js/bootstrap.bundle.min.js'); ?>"></script>
    <script src="<?php echo themeUrl('js/jquery-3.6.0.min.js'); ?>"></script>
    <script src="<?php echo themeUrl('js/main.js'); ?>"></script>
    
    <!-- Font Awesome для иконок -->
    <script src="https://kit.fontawesome.com/your-kit-id.js" crossorigin="anonymous"></script>
    
    <?php if (isset($additional_scripts)): ?>
        <?php foreach ($additional_scripts as $script): ?>
            <script src="<?php echo h($script); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>