    </div> <!-- End Main Content Container -->

    <?php
    // Helper functions for footer (if not already defined)
    if (!function_exists('getSiteUrl')) {
        function getSiteUrl($path = '') {
            $baseUrl = defined('SITE_URL') ? SITE_URL : 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
            return $baseUrl . ($path ? '/' . ltrim($path, '/') : '');
        }
    }
    
    if (!function_exists('getSiteName')) {
        function getSiteName() {
            return defined('SITE_NAME') ? SITE_NAME : 'AdBoard Pro';
        }
    }
    
    if (!function_exists('getSiteDescription')) {
        function getSiteDescription() {
            return defined('SITE_DESCRIPTION') ? SITE_DESCRIPTION : 'Рекламна компанія та дошка оголошень';
        }
    }
    ?>

    <!-- Footer -->
    <footer class="footer mt-5 py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="footer-brand">
                        <img src="<?php echo isset($metaTags['logo']) ? $metaTags['logo'] : 'images/default_logo.svg'; ?>" alt="<?php echo getSiteName(); ?>" height="50" class="mb-3">
                        <h5><?php echo getSiteName(); ?></h5>
                        <p class="text-muted"><?php echo getSiteDescription(); ?></p>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="text-uppercase fw-bold">Навігація</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo getSiteUrl(); ?>" class="text-decoration-none">Головна</a></li>
                        <li><a href="<?php echo getSiteUrl('ads'); ?>" class="text-decoration-none">Оголошення</a></li>
                        <li><a href="<?php echo getSiteUrl('services'); ?>" class="text-decoration-none">Послуги</a></li>
                        <li><a href="<?php echo getSiteUrl('about'); ?>" class="text-decoration-none">Про нас</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="text-uppercase fw-bold">Послуги</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo getSiteUrl('advertising'); ?>" class="text-decoration-none">Реклама</a></li>
                        <li><a href="<?php echo getSiteUrl('marketing'); ?>" class="text-decoration-none">Маркетинг</a></li>
                        <li><a href="<?php echo getSiteUrl('design'); ?>" class="text-decoration-none">Дизайн</a></li>
                        <li><a href="<?php echo getSiteUrl('consulting'); ?>" class="text-decoration-none">Консалтинг</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="text-uppercase fw-bold">Підтримка</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo getSiteUrl('help'); ?>" class="text-decoration-none">Допомога</a></li>
                        <li><a href="<?php echo getSiteUrl('faq'); ?>" class="text-decoration-none">FAQ</a></li>
                        <li><a href="<?php echo getSiteUrl('contact'); ?>" class="text-decoration-none">Контакти</a></li>
                        <li><a href="<?php echo getSiteUrl('privacy'); ?>" class="text-decoration-none">Конфіденційність</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="text-uppercase fw-bold">Соціальні мережі</h6>
                    <div class="social-links">
                        <a href="#" class="text-decoration-none me-3"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-decoration-none me-3"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" class="text-decoration-none me-3"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-decoration-none me-3"><i class="fab fa-linkedin fa-lg"></i></a>
                        <a href="#" class="text-decoration-none"><i class="fab fa-youtube fa-lg"></i></a>
                    </div>
                    
                    <div class="newsletter mt-3">
                        <h6 class="text-uppercase fw-bold">Підписка</h6>
                        <form class="newsletter-form">
                            <div class="input-group">
                                <input type="email" class="form-control" placeholder="Ваш email">
                                <button class="btn gradient-bg text-white" type="submit">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-muted">
                        &copy; <?php echo date('Y'); ?> <?php echo getSiteName(); ?>. Всі права захищені.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="<?php echo getSiteUrl('terms'); ?>" class="text-decoration-none text-muted me-3">Умови використання</a>
                    <a href="<?php echo getSiteUrl('privacy'); ?>" class="text-decoration-none text-muted">Політика конфіденційності</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button class="back-to-top" id="backToTop">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner">
            <div class="spinner-border gradient-bg" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- AOS Animation Library -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Custom JavaScript -->
    <script src="themes/script.js"></script>
    
    <!-- Theme Management Script -->
    <script>
        // Initialize theme management
        $(document).ready(function() {
            // Initialize AOS
            AOS.init({
                duration: 800,
                easing: 'ease-in-out',
                once: true
            });
            
            // Theme sidebar toggle
            $('#themeToggle').click(function() {
                $('#themeSidebar').addClass('active');
                $('#sidebarOverlay').addClass('active');
                $('body').addClass('sidebar-open');
            });
            
            $('#closeSidebar, #sidebarOverlay').click(function() {
                $('#themeSidebar').removeClass('active');
                $('#sidebarOverlay').removeClass('active');
                $('body').removeClass('sidebar-open');
            });
            
            // Theme change
            $('input[name="theme"]').change(function() {
                const theme = $(this).val();
                changeTheme(theme);
            });
            
            // Gradient change
            $('.gradient-option').click(function() {
                const gradient = $(this).data('gradient');
                $('.gradient-option').removeClass('active').find('i').remove();
                $(this).addClass('active').append('<i class="fas fa-check"></i>');
                changeGradient(gradient);
            });
            
            // Back to top button
            $(window).scroll(function() {
                if ($(this).scrollTop() > 100) {
                    $('#backToTop').addClass('show');
                } else {
                    $('#backToTop').removeClass('show');
                }
            });
            
            $('#backToTop').click(function() {
                $('html, body').animate({scrollTop: 0}, 800);
            });
        });
        
        function changeTheme(theme) {
            $.ajax({
                url: 'ajax/change_theme.php',
                method: 'POST',
                data: {
                    action: 'change_theme',
                    theme: theme
                },
                success: function(response) {
                    $('html').attr('data-theme', theme);
                    $('body').removeClass('light-theme dark-theme').addClass(theme + '-theme');
                    updateCSSVariables(theme);
                },
                error: function() {
                    Swal.fire('Помилка', 'Не вдалося змінити тему', 'error');
                }
            });
        }
        
        function changeGradient(gradient) {
            $.ajax({
                url: 'ajax/change_theme.php',
                method: 'POST',
                data: {
                    action: 'change_gradient',
                    gradient: gradient
                },
                success: function(response) {
                    $('html').attr('data-gradient', gradient);
                    updateGradientCSS(gradient);
                },
                error: function() {
                    Swal.fire('Помилка', 'Не вдалося змінити градієнт', 'error');
                }
            });
        }
        
        function updateCSSVariables(theme) {
            const root = document.documentElement;
            if (theme === 'dark') {
                root.style.setProperty('--theme-bg', '#1a1a1a');
                root.style.setProperty('--theme-text', '#ffffff');
                root.style.setProperty('--theme-bg-secondary', '#2d2d2d');
                root.style.setProperty('--theme-border', '#404040');
            } else {
                root.style.setProperty('--theme-bg', '#ffffff');
                root.style.setProperty('--theme-text', '#333333');
                root.style.setProperty('--theme-bg-secondary', '#f8f9fa');
                root.style.setProperty('--theme-border', '#dee2e6');
            }
        }
        
        function updateGradientCSS(gradient) {
            // This will be updated via AJAX response with actual gradient CSS
            location.reload(); // Temporary solution, better to update CSS dynamically
        }
        
        // Global AJAX setup
        $.ajaxSetup({
            beforeSend: function() {
                $('#loadingOverlay').fadeIn(200);
            },
            complete: function() {
                $('#loadingOverlay').fadeOut(200);
            }
        });
    </script>
</body>
</html>
