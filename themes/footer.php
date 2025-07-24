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
    
    if (!function_exists('safeTranslate')) {
        function safeTranslate($key, $default = '') {
            return $default;
        }
    }
    ?>

    <!-- Footer -->
    <footer class="footer-gradient">
        <div class="footer-content">
            <div class="container">
                <!-- Main Footer Content -->
                <div class="row">
                    <!-- Brand Section with Logo and Cat -->
                    <div class="col-lg-4 col-md-6 mb-5">
                        <div class="footer-brand-section">
                            <div class="brand-with-cat">
                                <div class="logo-container">
                                    <img src="<?php echo isset($metaTags['logo']) ? $metaTags['logo'] : 'images/default_logo.svg'; ?>" alt="<?php echo getSiteName(); ?>" class="footer-logo">
                                </div>
                                <!-- Animated Cat -->
                                <div class="cat-container">
                                    <div class="cat-body">
                                        <div class="cat-head">
                                            <div class="cat-ears">
                                                <div class="ear left"></div>
                                                <div class="ear right"></div>
                                            </div>
                                            <div class="cat-eyes">
                                                <div class="eye left"></div>
                                                <div class="eye right"></div>
                                            </div>
                                            <div class="cat-nose"></div>
                                            <div class="cat-mouth"></div>
                                        </div>
                                        <div class="cat-body-main">
                                            <div class="cat-stripes">
                                                <div class="stripe"></div>
                                                <div class="stripe"></div>
                                                <div class="stripe"></div>
                                            </div>
                                            <div class="cat-legs">
                                                <div class="leg front-left"></div>
                                                <div class="leg front-right"></div>
                                                <div class="leg back-left"></div>
                                                <div class="leg back-right"></div>
                                            </div>
                                        </div>
                                        <div class="cat-tail">
                                            <div class="tail-segment segment1"></div>
                                            <div class="tail-segment segment2"></div>
                                            <div class="tail-segment segment3"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h4 class="brand-title"><?php echo getSiteName(); ?></h4>
                            <p class="brand-description"><?php echo getSiteDescription(); ?></p>
                            
                            <!-- Social Links with Touch Effects -->
                            <div class="social-links-container">
                                <h6 class="section-title"><?php echo safeTranslate('social_media', 'Соціальні мережі'); ?></h6>
                                <div class="social-links">
                                    <a href="#" class="social-link facebook" data-platform="Facebook">
                                        <i class="fab fa-facebook-f"></i>
                                        <span class="social-tooltip">Facebook</span>
                                    </a>
                                    <a href="#" class="social-link twitter" data-platform="Twitter">
                                        <i class="fab fa-twitter"></i>
                                        <span class="social-tooltip">Twitter</span>
                                    </a>
                                    <a href="#" class="social-link instagram" data-platform="Instagram">
                                        <i class="fab fa-instagram"></i>
                                        <span class="social-tooltip">Instagram</span>
                                    </a>
                                    <a href="#" class="social-link linkedin" data-platform="LinkedIn">
                                        <i class="fab fa-linkedin-in"></i>
                                        <span class="social-tooltip">LinkedIn</span>
                                    </a>
                                    <a href="#" class="social-link youtube" data-platform="YouTube">
                                        <i class="fab fa-youtube"></i>
                                        <span class="social-tooltip">YouTube</span>
                                    </a>
                                    <a href="#" class="social-link telegram" data-platform="Telegram">
                                        <i class="fab fa-telegram-plane"></i>
                                        <span class="social-tooltip">Telegram</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Navigation Links -->
                    <div class="col-lg-2 col-md-6 mb-4">
                        <h6 class="section-title"><?php echo safeTranslate('navigation', 'Навігація'); ?></h6>
                        <ul class="footer-links">
                            <li><a href="<?php echo getSiteUrl(); ?>" class="footer-link"><?php echo safeTranslate('home', 'Головна'); ?></a></li>
                            <li><a href="<?php echo getSiteUrl('ads'); ?>" class="footer-link"><?php echo safeTranslate('ads', 'Оголошення'); ?></a></li>
                            <li><a href="<?php echo getSiteUrl('categories'); ?>" class="footer-link"><?php echo safeTranslate('categories', 'Категорії'); ?></a></li>
                            <li><a href="<?php echo getSiteUrl('services'); ?>" class="footer-link"><?php echo safeTranslate('services', 'Послуги'); ?></a></li>
                            <li><a href="<?php echo getSiteUrl('about'); ?>" class="footer-link"><?php echo safeTranslate('about', 'Про нас'); ?></a></li>
                        </ul>
                    </div>
                    
                    <!-- Services -->
                    <div class="col-lg-2 col-md-6 mb-4">
                        <h6 class="section-title"><?php echo safeTranslate('services', 'Послуги'); ?></h6>
                        <ul class="footer-links">
                            <li><a href="<?php echo getSiteUrl('advertising'); ?>" class="footer-link"><?php echo safeTranslate('advertising', 'Реклама'); ?></a></li>
                            <li><a href="<?php echo getSiteUrl('marketing'); ?>" class="footer-link"><?php echo safeTranslate('marketing', 'Маркетинг'); ?></a></li>
                            <li><a href="<?php echo getSiteUrl('design'); ?>" class="footer-link"><?php echo safeTranslate('design', 'Дизайн'); ?></a></li>
                            <li><a href="<?php echo getSiteUrl('consulting'); ?>" class="footer-link"><?php echo safeTranslate('consulting', 'Консалтинг'); ?></a></li>
                            <li><a href="<?php echo getSiteUrl('analytics'); ?>" class="footer-link"><?php echo safeTranslate('analytics', 'Аналітика'); ?></a></li>
                        </ul>
                    </div>
                    
                    <!-- Support -->
                    <div class="col-lg-2 col-md-6 mb-4">
                        <h6 class="section-title"><?php echo safeTranslate('support', 'Підтримка'); ?></h6>
                        <ul class="footer-links">
                            <li><a href="<?php echo getSiteUrl('help'); ?>" class="footer-link"><?php echo safeTranslate('help', 'Допомога'); ?></a></li>
                            <li><a href="<?php echo getSiteUrl('faq'); ?>" class="footer-link"><?php echo safeTranslate('faq', 'FAQ'); ?></a></li>
                            <li><a href="<?php echo getSiteUrl('contact'); ?>" class="footer-link"><?php echo safeTranslate('contact', 'Контакти'); ?></a></li>
                            <li><a href="<?php echo getSiteUrl('privacy'); ?>" class="footer-link"><?php echo safeTranslate('privacy', 'Приватність'); ?></a></li>
                            <li><a href="<?php echo getSiteUrl('terms'); ?>" class="footer-link"><?php echo safeTranslate('terms', 'Умови'); ?></a></li>
                        </ul>
                    </div>
                    
                    <!-- Newsletter Subscription -->
                    <div class="col-lg-2 col-md-6 mb-4">
                        <div class="newsletter-section">
                            <h6 class="section-title"><?php echo safeTranslate('newsletter', 'Розсилка'); ?></h6>
                            <p class="newsletter-description"><?php echo safeTranslate('newsletter_desc', 'Підпишіться та отримуйте останні новини'); ?></p>
                            
                            <form class="newsletter-form" id="newsletterForm">
                                <div class="form-group">
                                    <div class="input-wrapper">
                                        <input type="email" 
                                               class="newsletter-input" 
                                               id="newsletterEmail" 
                                               placeholder="<?php echo safeTranslate('your_email', 'Ваш email'); ?>" 
                                               required>
                                        <div class="input-decoration"></div>
                                    </div>
                                </div>
                                <button type="submit" class="newsletter-btn">
                                    <span class="btn-text"><?php echo safeTranslate('subscribe', 'Підписатись'); ?></span>
                                    <span class="btn-icon">
                                        <i class="fas fa-paper-plane"></i>
                                    </span>
                                    <div class="btn-ripple"></div>
                                </button>
                            </form>
                            
                            <!-- Newsletter Success Animation -->
                            <div class="newsletter-success" id="newsletterSuccess">
                                <div class="success-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <p class="success-text"><?php echo safeTranslate('subscribed', 'Дякуємо за підписку!'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Footer Bottom -->
                <div class="footer-bottom">
                    <div class="footer-divider">
                        <div class="divider-line"></div>
                        <div class="divider-decoration">
                            <div class="decoration-dot"></div>
                            <div class="decoration-dot"></div>
                            <div class="decoration-dot"></div>
                        </div>
                        <div class="divider-line"></div>
                    </div>
                    
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="copyright-section">
                                <p class="copyright-text">
                                    &copy; <?php echo date('Y'); ?> 
                                    <span class="brand-highlight"><?php echo getSiteName(); ?></span>
                                    <span class="separator">•</span>
                                    <?php echo safeTranslate('all_rights_reserved', 'Всі права захищені'); ?>
                                </p>
                                <p class="made-with-love">
                                    <?php echo safeTranslate('made_with', 'Зроблено з'); ?> 
                                    <span class="heart">❤️</span> 
                                    <?php echo safeTranslate('in_ukraine', 'в Україні'); ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="footer-legal-links">
                                <a href="<?php echo getSiteUrl('terms'); ?>" class="legal-link">
                                    <?php echo safeTranslate('terms_of_use', 'Умови використання'); ?>
                                </a>
                                <span class="link-separator">|</span>
                                <a href="<?php echo getSiteUrl('privacy'); ?>" class="legal-link">
                                    <?php echo safeTranslate('privacy_policy', 'Політика конфіденційності'); ?>
                                </a>
                                <span class="link-separator">|</span>
                                <a href="<?php echo getSiteUrl('cookies'); ?>" class="legal-link">
                                    <?php echo safeTranslate('cookies', 'Cookies'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Floating Elements -->
        <div class="floating-elements">
            <div class="floating-shape shape1"></div>
            <div class="floating-shape shape2"></div>
            <div class="floating-shape shape3"></div>
            <div class="floating-shape shape4"></div>
        </div>
    </footer>

    <!-- Back to Top Button with Cat -->
    <button class="back-to-top-cat" id="backToTop">
        <div class="cat-icon">
            <div class="cat-silhouette">
                <div class="cat-head-simple"></div>
                <div class="cat-body-simple"></div>
                <div class="cat-tail-simple"></div>
            </div>
            <div class="arrow-up">
                <i class="fas fa-chevron-up"></i>
            </div>
        </div>
        <span class="back-to-top-text"><?php echo safeTranslate('back_to_top', 'Вгору'); ?></span>
    </button>

    <!-- Footer Styles -->
    <style>
        /* Footer Gradient Background */
        .footer-gradient {
            background: var(--current-gradient);
            position: relative;
            overflow: hidden;
            margin-top: 100px;
        }
        
        .footer-content {
            position: relative;
            z-index: 2;
            padding: 80px 0 30px;
        }
        
        /* Brand Section with Cat */
        .footer-brand-section {
            text-align: center;
        }
        
        .brand-with-cat {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
            gap: 20px;
        }
        
        .footer-logo {
            height: 60px;
            width: auto;
            border-radius: 50%;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }
        
        .footer-logo:hover {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.3);
        }
        
        /* Animated Cat */
        .cat-container {
            position: relative;
        }
        
        .cat-body {
            position: relative;
            cursor: pointer;
        }
        
        .cat-head {
            width: 40px;
            height: 35px;
            background: #ff8c00;
            border-radius: 50% 50% 40% 40%;
            position: relative;
            margin-bottom: -5px;
            animation: catBreathe 3s ease-in-out infinite;
        }
        
        .cat-ears {
            position: absolute;
            top: -8px;
            width: 100%;
        }
        
        .ear {
            width: 12px;
            height: 15px;
            background: #ff8c00;
            border-radius: 50% 50% 0 50%;
            position: absolute;
            animation: earTwitch 4s ease-in-out infinite;
        }
        
        .ear.left {
            left: 5px;
            transform: rotate(-20deg);
        }
        
        .ear.right {
            right: 5px;
            transform: rotate(20deg);
        }
        
        .cat-eyes {
            position: absolute;
            top: 12px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
        }
        
        .eye {
            width: 6px;
            height: 8px;
            background: #000;
            border-radius: 50%;
            animation: catBlink 3s ease-in-out infinite;
        }
        
        .cat-nose {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: 4px;
            height: 3px;
            background: #ff6b9d;
            border-radius: 50%;
        }
        
        .cat-mouth {
            position: absolute;
            top: 24px;
            left: 50%;
            transform: translateX(-50%);
            width: 8px;
            height: 4px;
            border: 1px solid #000;
            border-top: none;
            border-radius: 0 0 50% 50%;
        }
        
        .cat-body-main {
            width: 50px;
            height: 40px;
            background: #ff8c00;
            border-radius: 50% 50% 30% 30%;
            position: relative;
            margin: 0 auto;
        }
        
        .cat-stripes {
            position: absolute;
            top: 8px;
            left: 50%;
            transform: translateX(-50%);
        }
        
        .stripe {
            width: 30px;
            height: 2px;
            background: #e67300;
            margin: 4px 0;
            border-radius: 2px;
        }
        
        .cat-legs {
            position: absolute;
            bottom: -15px;
            width: 100%;
        }
        
        .leg {
            width: 8px;
            height: 15px;
            background: #ff8c00;
            border-radius: 4px;
            position: absolute;
        }
        
        .leg.front-left { left: 8px; }
        .leg.front-right { right: 8px; }
        .leg.back-left { left: 15px; }
        .leg.back-right { right: 15px; }
        
        .cat-tail {
            position: absolute;
            right: -25px;
            top: 15px;
            z-index: -1;
        }
        
        .tail-segment {
            width: 6px;
            height: 20px;
            background: #ff8c00;
            border-radius: 3px;
            margin: 2px;
            transform-origin: top center;
        }
        
        .segment1 {
            animation: tailWag1 2s ease-in-out infinite;
        }
        
        .segment2 {
            animation: tailWag2 2s ease-in-out infinite 0.1s;
        }
        
        .segment3 {
            animation: tailWag3 2s ease-in-out infinite 0.2s;
        }
        
        /* Cat Animations */
        @keyframes catBreathe {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        @keyframes catBlink {
            0%, 45%, 55%, 100% { transform: scaleY(1); }
            50% { transform: scaleY(0.1); }
        }
        
        @keyframes earTwitch {
            0%, 90%, 100% { transform: rotate(-20deg); }
            95% { transform: rotate(-30deg); }
        }
        
        .ear.right {
            animation: earTwitchRight 4s ease-in-out infinite;
        }
        
        @keyframes earTwitchRight {
            0%, 90%, 100% { transform: rotate(20deg); }
            95% { transform: rotate(30deg); }
        }
        
        @keyframes tailWag1 {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-15deg); }
            75% { transform: rotate(15deg); }
        }
        
        @keyframes tailWag2 {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-20deg); }
            75% { transform: rotate(20deg); }
        }
        
        @keyframes tailWag3 {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-25deg); }
            75% { transform: rotate(25deg); }
        }
        
        /* Cat Hover Effects */
        .cat-container:hover .cat-head {
            animation-duration: 1s;
        }
        
        .cat-container:hover .tail-segment {
            animation-duration: 0.5s;
        }
        
        .cat-container:hover .eye {
            animation-duration: 0.8s;
        }
        
        /* Brand Title */
        .brand-title {
            color: white;
            font-weight: 800;
            font-size: 2rem;
            margin-bottom: 15px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            background: linear-gradient(45deg, rgba(255,255,255,0.9), rgba(255,255,255,0.7));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .brand-description {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.1rem;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        /* Section Titles */
        .section-title {
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            padding-bottom: 10px;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 30px;
            height: 2px;
            background: rgba(255, 255, 255, 0.6);
            border-radius: 2px;
        }
        
        /* Footer Links */
        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .footer-links li {
            margin-bottom: 12px;
        }
        
        .footer-link {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            padding-left: 15px;
            font-weight: 500;
        }
        
        .footer-link::before {
            content: '→';
            position: absolute;
            left: 0;
            opacity: 0;
            transform: translateX(-5px);
            transition: all 0.3s ease;
        }
        
        .footer-link:hover {
            color: white;
            transform: translateX(5px);
        }
        
        .footer-link:hover::before {
            opacity: 1;
            transform: translateX(0);
        }
        
        /* Social Links */
        .social-links-container {
            margin-top: 30px;
        }
        
        .social-links {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .social-link {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: white;
            font-size: 1.2rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }
        
        .social-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            opacity: 0;
            transition: all 0.3s ease;
            transform: scale(0);
        }
        
        .social-link.facebook::before { background: #3b5998; }
        .social-link.twitter::before { background: #1da1f2; }
        .social-link.instagram::before { background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); }
        .social-link.linkedin::before { background: #0077b5; }
        .social-link.youtube::before { background: #ff0000; }
        .social-link.telegram::before { background: #0088cc; }
        
        .social-link:hover {
            transform: scale(1.1) translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }
        
        .social-link:hover::before {
            opacity: 1;
            transform: scale(1);
        }
        
        .social-tooltip {
            position: absolute;
            bottom: -40px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.8rem;
            opacity: 0;
            transition: all 0.3s ease;
            pointer-events: none;
            white-space: nowrap;
        }
        
        .social-link:hover .social-tooltip {
            opacity: 1;
            bottom: -35px;
        }
        
        /* Newsletter Section */
        .newsletter-section {
            text-align: center;
        }
        
        .newsletter-description {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 25px;
            font-size: 0.95rem;
        }
        
        .newsletter-form {
            position: relative;
        }
        
        .input-wrapper {
            position: relative;
            margin-bottom: 15px;
        }
        
        .newsletter-input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 25px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .newsletter-input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .newsletter-input:focus {
            outline: none;
            border-color: rgba(255, 255, 255, 0.6);
            background: rgba(255, 255, 255, 0.15);
            transform: scale(1.02);
        }
        
        .input-decoration {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 25px;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            opacity: 0;
            transition: all 0.3s ease;
            pointer-events: none;
        }
        
        .newsletter-input:focus + .input-decoration {
            opacity: 1;
            animation: inputShimmer 2s ease-in-out infinite;
        }
        
        @keyframes inputShimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        .newsletter-btn {
            width: 100%;
            padding: 15px 25px;
            border: none;
            border-radius: 25px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }
        
        .newsletter-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }
        
        .btn-text, .btn-icon {
            transition: all 0.3s ease;
            position: relative;
            z-index: 2;
        }
        
        .btn-icon {
            opacity: 0;
            transform: translateX(10px);
        }
        
        .newsletter-btn:hover .btn-text {
            transform: translateX(-10px);
        }
        
        .newsletter-btn:hover .btn-icon {
            opacity: 1;
            transform: translateX(0);
        }
        
        .btn-ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
        }
        
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
        
        /* Newsletter Success */
        .newsletter-success {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(40, 167, 69, 0.9);
            border-radius: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transform: scale(0.8);
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .newsletter-success.show {
            opacity: 1;
            transform: scale(1);
        }
        
        .success-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            color: #28a745;
            font-size: 1.5rem;
            animation: successPulse 0.6s ease;
        }
        
        @keyframes successPulse {
            0% { transform: scale(0); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        
        .success-text {
            color: white;
            font-weight: 600;
            text-align: center;
            margin: 0;
        }
        
        /* Footer Bottom */
        .footer-bottom {
            margin-top: 60px;
            padding-top: 30px;
        }
        
        .footer-divider {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
        }
        
        .divider-line {
            flex: 1;
            height: 1px;
            background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.3), transparent);
        }
        
        .divider-decoration {
            display: flex;
            gap: 8px;
            margin: 0 20px;
        }
        
        .decoration-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.6);
            animation: dotPulse 2s ease-in-out infinite;
        }
        
        .decoration-dot:nth-child(2) { animation-delay: 0.2s; }
        .decoration-dot:nth-child(3) { animation-delay: 0.4s; }
        
        @keyframes dotPulse {
            0%, 100% { opacity: 0.6; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.3); }
        }
        
        /* Copyright */
        .copyright-section {
            text-align: center;
        }
        
        .copyright-text {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 8px;
            font-size: 0.95rem;
        }
        
        .brand-highlight {
            color: white;
            font-weight: 700;
        }
        
        .separator {
            margin: 0 8px;
            opacity: 0.6;
        }
        
        .made-with-love {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
            margin: 0;
        }
        
        .heart {
            color: #ff6b9d;
            animation: heartBeat 1.5s ease-in-out infinite;
        }
        
        @keyframes heartBeat {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }
        
        /* Legal Links */
        .footer-legal-links {
            text-align: center;
        }
        
        .legal-link {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .legal-link:hover {
            color: white;
        }
        
        .link-separator {
            margin: 0 8px;
            color: rgba(255, 255, 255, 0.5);
        }
        
        /* Floating Elements */
        .floating-elements {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }
        
        .floating-shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 10s ease-in-out infinite;
        }
        
        .shape1 {
            width: 60px;
            height: 60px;
            top: 10%;
            left: 5%;
            animation-delay: 0s;
        }
        
        .shape2 {
            width: 40px;
            height: 40px;
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }
        
        .shape3 {
            width: 80px;
            height: 80px;
            bottom: 20%;
            left: 10%;
            animation-delay: 4s;
        }
        
        .shape4 {
            width: 30px;
            height: 30px;
            top: 30%;
            right: 5%;
            animation-delay: 6s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); opacity: 0.3; }
            50% { transform: translateY(-20px) rotate(180deg); opacity: 0.7; }
        }
        
        /* Back to Top Cat Button */
        .back-to-top-cat {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border: none;
            border-radius: 50%;
            background: var(--current-gradient);
            color: white;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            z-index: 1000;
            opacity: 0;
            transform: translateY(100px);
        }
        
        .back-to-top-cat.show {
            opacity: 1;
            transform: translateY(0);
        }
        
        .back-to-top-cat:hover {
            transform: scale(1.1) translateY(-5px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.3);
        }
        
        .cat-icon {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .cat-silhouette {
            width: 20px;
            height: 15px;
            position: relative;
            margin-bottom: 3px;
        }
        
        .cat-head-simple {
            width: 12px;
            height: 10px;
            background: white;
            border-radius: 50% 50% 40% 40%;
            position: relative;
        }
        
        .cat-head-simple::before,
        .cat-head-simple::after {
            content: '';
            position: absolute;
            top: -3px;
            width: 4px;
            height: 6px;
            background: white;
            border-radius: 50% 50% 0 50%;
        }
        
        .cat-head-simple::before {
            left: 1px;
            transform: rotate(-20deg);
        }
        
        .cat-head-simple::after {
            right: 1px;
            transform: rotate(20deg);
        }
        
        .cat-body-simple {
            width: 16px;
            height: 8px;
            background: white;
            border-radius: 50%;
            margin-top: -2px;
        }
        
        .cat-tail-simple {
            position: absolute;
            right: -6px;
            top: 8px;
            width: 2px;
            height: 8px;
            background: white;
            border-radius: 2px;
            transform: rotate(20deg);
            animation: miniTailWag 1s ease-in-out infinite;
        }
        
        @keyframes miniTailWag {
            0%, 100% { transform: rotate(20deg); }
            50% { transform: rotate(-10deg); }
        }
        
        .arrow-up {
            font-size: 0.7rem;
            opacity: 0.8;
        }
        
        .back-to-top-text {
            position: absolute;
            bottom: -25px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.7rem;
            opacity: 0;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        
        .back-to-top-cat:hover .back-to-top-text {
            opacity: 1;
            bottom: -20px;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .footer-content {
                padding: 60px 0 30px;
            }
            
            .brand-with-cat {
                flex-direction: column;
                gap: 15px;
            }
            
            .brand-title {
                font-size: 1.5rem;
            }
            
            .social-links {
                justify-content: center;
            }
            
            .social-link {
                width: 45px;
                height: 45px;
                font-size: 1rem;
            }
            
            .newsletter-input,
            .newsletter-btn {
                font-size: 0.9rem;
                padding: 12px 18px;
            }
            
            .copyright-section,
            .footer-legal-links {
                text-align: center;
                margin-bottom: 20px;
            }
            
            .back-to-top-cat {
                width: 50px;
                height: 50px;
                bottom: 20px;
                right: 20px;
            }
            
            .cat-container {
                transform: scale(0.8);
            }
        }
        
        @media (max-width: 480px) {
            .footer-content {
                padding: 40px 0 20px;
            }
            
            .brand-title {
                font-size: 1.3rem;
            }
            
            .section-title {
                font-size: 1rem;
            }
            
            .social-links {
                gap: 10px;
            }
            
            .social-link {
                width: 40px;
                height: 40px;
                font-size: 0.9rem;
            }
        }
    </style>

    <!-- Footer JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Newsletter form submission
            const newsletterForm = document.getElementById('newsletterForm');
            const newsletterSuccess = document.getElementById('newsletterSuccess');
            
            if (newsletterForm) {
                newsletterForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const email = document.getElementById('newsletterEmail').value;
                    if (!email) return;
                    
                    // Show loading on button
                    const btn = this.querySelector('.newsletter-btn');
                    const originalText = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Підписуємо...';
                    btn.disabled = true;
                    
                    // Simulate API call
                    setTimeout(() => {
                        // Show success animation
                        newsletterSuccess.classList.add('show');
                        
                        // Reset form after delay
                        setTimeout(() => {
                            newsletterSuccess.classList.remove('show');
                            this.reset();
                            btn.innerHTML = originalText;
                            btn.disabled = false;
                        }, 3000);
                    }, 1500);
                });
            }
            
            // Ripple effect for newsletter button
            const newsletterBtn = document.querySelector('.newsletter-btn');
            if (newsletterBtn) {
                newsletterBtn.addEventListener('click', function(e) {
                    const ripple = this.querySelector('.btn-ripple');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;
                    
                    ripple.style.width = ripple.style.height = size + 'px';
                    ripple.style.left = x + 'px';
                    ripple.style.top = y + 'px';
                    ripple.style.transform = 'scale(0)';
                    ripple.style.animation = 'ripple 0.6s linear';
                });
            }
            
            // Cat click interaction
            const catContainer = document.querySelector('.cat-container');
            if (catContainer) {
                catContainer.addEventListener('click', function() {
                    // Add excited animation class
                    this.classList.add('cat-excited');
                    
                    // Play meow sound (if available)
                    if ('speechSynthesis' in window) {
                        const utterance = new SpeechSynthesisUtterance('Мяу!');
                        utterance.rate = 2;
                        utterance.pitch = 2;
                        speechSynthesis.speak(utterance);
                    }
                    
                    // Remove excited class after animation
                    setTimeout(() => {
                        this.classList.remove('cat-excited');
                    }, 2000);
                });
            }
            
            // Back to top functionality
            const backToTopBtn = document.getElementById('backToTop');
            if (backToTopBtn) {
                window.addEventListener('scroll', function() {
                    if (window.pageYOffset > 300) {
                        backToTopBtn.classList.add('show');
                    } else {
                        backToTopBtn.classList.remove('show');
                    }
                });
                
                backToTopBtn.addEventListener('click', function() {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            }
            
            // Social links click tracking
            const socialLinks = document.querySelectorAll('.social-link');
            socialLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const platform = this.dataset.platform;
                    
                    // Add click animation
                    this.style.transform = 'scale(0.9)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                    
                    // Show message (in real app, this would open the actual social link)
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: `${platform}`,
                            text: `Відкриваємо ${platform}...`,
                            icon: 'info',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        alert(`Відкриваємо ${platform}...`);
                    }
                });
            });
            
            // Intersection Observer for footer animations
            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('animate-in');
                        }
                    });
                }, { threshold: 0.1 });
                
                // Observe footer sections
                const footerSections = document.querySelectorAll('.col-lg-4, .col-lg-2, .newsletter-section');
                footerSections.forEach(section => {
                    observer.observe(section);
                });
            }
        });
        
        // CSS for cat excited state
        const excitedCatCSS = `
            .cat-excited .cat-head {
                animation: catExcited 0.3s ease-in-out 6;
            }
            
            .cat-excited .tail-segment {
                animation-duration: 0.2s !important;
            }
            
            .cat-excited .eye {
                animation: catWink 0.5s ease-in-out 3;
            }
            
            @keyframes catExcited {
                0%, 100% { transform: scale(1) rotate(0deg); }
                50% { transform: scale(1.1) rotate(5deg); }
            }
            
            @keyframes catWink {
                0%, 70%, 100% { transform: scaleY(1); }
                85% { transform: scaleY(0.1); }
            }
            
            .animate-in {
                animation: slideInUp 0.6s ease-out forwards;
            }
            
            @keyframes slideInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        `;
        
        // Add excited cat CSS
        const style = document.createElement('style');
        style.textContent = excitedCatCSS;
        document.head.appendChild(style);
    </script>

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

</body>
</html>
