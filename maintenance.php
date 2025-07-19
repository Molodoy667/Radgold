<?php
require_once 'config/config.php';

// Проверяем, включен ли режим обслуживания
if (!Settings::isMaintenanceMode()) {
    // Если режим не включен, перенаправляем на главную
    header('Location: index.php');
    exit();
}

// Проверяем, является ли пользователь администратором
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']) {
    // Администраторы имеют доступ к сайту
    header('Location: index.php');
    exit();
}

// Получаем настройки обслуживания
$maintenance_title = Settings::get('maintenance_title', 'Сайт на технічному обслуговуванні');
$maintenance_message = Settings::get('maintenance_message', 
    'Наразі ми проводимо технічні роботи для покращення роботи сайту. Вибачте за тимчасові незручності. Сайт буде доступний найближчим часом.');
$maintenance_end_time = Settings::get('maintenance_end_time', '');
$maintenance_contact_email = Settings::get('maintenance_contact_email', Settings::get('site_email', ''));
$maintenance_show_progress = Settings::get('maintenance_show_progress', true);
$maintenance_custom_html = Settings::get('maintenance_custom_html', '');

// Получаем тему и градиент
$current_theme = Theme::getCurrentTheme();
$theme_css = Theme::generateCSS();

// Возвращаем HTTP статус 503
http_response_code(503);
header('Retry-After: 3600'); // Повторить через час
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($maintenance_title); ?></title>
    
    <!-- Meta tags -->
    <meta name="robots" content="noindex, nofollow">
    <meta name="description" content="<?php echo htmlspecialchars($maintenance_message); ?>">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        <?php echo $theme_css; ?>
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--theme-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Animated background */
        .bg-animation {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }
        
        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
        }
        
        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }
        
        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            left: 80%;
            animation-delay: -2s;
        }
        
        .shape:nth-child(3) {
            width: 100px;
            height: 100px;
            top: 80%;
            left: 20%;
            animation-delay: -4s;
        }
        
        .shape:nth-child(4) {
            width: 60px;
            height: 60px;
            top: 30%;
            left: 70%;
            animation-delay: -1s;
        }
        
        .shape:nth-child(5) {
            width: 90px;
            height: 90px;
            top: 10%;
            left: 50%;
            animation-delay: -3s;
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
                opacity: 0.1;
            }
            50% {
                transform: translateY(-20px) rotate(180deg);
                opacity: 0.3;
            }
        }
        
        /* Main container */
        .maintenance-container {
            position: relative;
            z-index: 10;
            max-width: 600px;
            width: 100%;
            padding: 0 20px;
        }
        
        .maintenance-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            padding: 3rem 2.5rem;
            text-align: center;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: slideUp 0.8s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .maintenance-icon {
            width: 120px;
            height: 120px;
            margin: 0 auto 2rem auto;
            background: var(--theme-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
            position: relative;
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(var(--theme-primary-rgb), 0.7);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 0 0 20px rgba(var(--theme-primary-rgb), 0);
            }
        }
        
        .maintenance-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 1rem;
            background: var(--theme-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .maintenance-message {
            font-size: 1.2rem;
            color: #666;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        
        .countdown-container {
            background: var(--theme-gradient);
            border-radius: 20px;
            padding: 1.5rem;
            margin: 2rem 0;
            color: white;
        }
        
        .countdown-title {
            font-size: 1.1rem;
            margin-bottom: 1rem;
            opacity: 0.9;
        }
        
        .countdown {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }
        
        .time-unit {
            text-align: center;
            min-width: 60px;
        }
        
        .time-number {
            display: block;
            font-size: 2rem;
            font-weight: 700;
            line-height: 1;
        }
        
        .time-label {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-top: 0.2rem;
        }
        
        .progress-section {
            margin: 2rem 0;
        }
        
        .progress-title {
            font-size: 1rem;
            color: #666;
            margin-bottom: 1rem;
        }
        
        .progress-bar-container {
            background: #f0f0f0;
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
            position: relative;
        }
        
        .progress-bar-fill {
            height: 100%;
            background: var(--theme-gradient);
            border-radius: 4px;
            position: relative;
            animation: progressAnimation 3s ease-in-out infinite;
        }
        
        @keyframes progressAnimation {
            0%, 100% { width: 60%; }
            50% { width: 80%; }
        }
        
        .progress-bar-fill::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            animation: shimmer 2s ease-in-out infinite;
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        .contact-section {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #eee;
        }
        
        .contact-title {
            font-size: 1.1rem;
            color: #333;
            margin-bottom: 1rem;
        }
        
        .contact-email {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--theme-primary);
            text-decoration: none;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            background: rgba(var(--theme-primary-rgb), 0.1);
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        
        .contact-email:hover {
            background: var(--theme-primary);
            color: white;
            transform: translateY(-2px);
        }
        
        .admin-notice {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(255, 193, 7, 0.95);
            color: #856404;
            padding: 1rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            z-index: 1000;
            animation: slideInRight 0.5s ease-out;
        }
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        /* Custom HTML styles */
        .custom-content {
            margin-top: 2rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .maintenance-card {
                padding: 2rem 1.5rem;
            }
            
            .maintenance-title {
                font-size: 2rem;
            }
            
            .maintenance-icon {
                width: 100px;
                height: 100px;
                font-size: 2.5rem;
            }
            
            .countdown {
                gap: 0.5rem;
            }
            
            .time-number {
                font-size: 1.5rem;
            }
        }
        
        @media (max-width: 480px) {
            .maintenance-container {
                padding: 0 15px;
            }
            
            .maintenance-card {
                padding: 1.5rem 1rem;
            }
            
            .maintenance-title {
                font-size: 1.8rem;
            }
            
            .maintenance-message {
                font-size: 1rem;
            }
        }
    </style>
    
    <?php if (!empty($maintenance_custom_html)): ?>
        <?php echo $maintenance_custom_html; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Background Animation -->
    <div class="bg-animation">
        <div class="floating-shapes">
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="maintenance-container">
        <div class="maintenance-card">
            <div class="maintenance-icon">
                <i class="fas fa-tools"></i>
            </div>
            
            <h1 class="maintenance-title"><?php echo htmlspecialchars($maintenance_title); ?></h1>
            
            <p class="maintenance-message"><?php echo nl2br(htmlspecialchars($maintenance_message)); ?></p>
            
            <?php if (!empty($maintenance_end_time)): ?>
                <div class="countdown-container">
                    <div class="countdown-title">
                        <i class="fas fa-clock me-2"></i>
                        Очікуваний час завершення робіт
                    </div>
                    <div class="countdown" id="countdown">
                        <div class="time-unit">
                            <span class="time-number" id="days">00</span>
                            <div class="time-label">дні</div>
                        </div>
                        <div class="time-unit">
                            <span class="time-number" id="hours">00</span>
                            <div class="time-label">години</div>
                        </div>
                        <div class="time-unit">
                            <span class="time-number" id="minutes">00</span>
                            <div class="time-label">хвилини</div>
                        </div>
                        <div class="time-unit">
                            <span class="time-number" id="seconds">00</span>
                            <div class="time-label">секунди</div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($maintenance_show_progress): ?>
                <div class="progress-section">
                    <div class="progress-title">
                        <i class="fas fa-spinner me-2"></i>
                        Прогрес виконання робіт
                    </div>
                    <div class="progress-bar-container">
                        <div class="progress-bar-fill"></div>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($maintenance_contact_email)): ?>
                <div class="contact-section">
                    <div class="contact-title">
                        <i class="fas fa-envelope me-2"></i>
                        Маєте питання?
                    </div>
                    <a href="mailto:<?php echo htmlspecialchars($maintenance_contact_email); ?>" class="contact-email">
                        <i class="fas fa-envelope"></i>
                        <?php echo htmlspecialchars($maintenance_contact_email); ?>
                    </a>
                </div>
            <?php endif; ?>
            
            <div class="custom-content">
                <?php if (!empty($maintenance_custom_html)): ?>
                    <?php echo $maintenance_custom_html; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        <?php if (!empty($maintenance_end_time)): ?>
        // Countdown timer
        const endTime = new Date('<?php echo $maintenance_end_time; ?>').getTime();
        
        function updateCountdown() {
            const now = new Date().getTime();
            const distance = endTime - now;
            
            if (distance < 0) {
                document.getElementById('countdown').innerHTML = '<div class="time-unit"><span class="time-number">Завершено</span></div>';
                return;
            }
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            document.getElementById('days').textContent = days.toString().padStart(2, '0');
            document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
            document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
            document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
        }
        
        // Update countdown every second
        updateCountdown();
        setInterval(updateCountdown, 1000);
        <?php endif; ?>
        
        // Auto refresh page every 5 minutes to check if maintenance is over
        setTimeout(() => {
            window.location.reload();
        }, 5 * 60 * 1000);
        
        // Add floating animation to shapes
        document.querySelectorAll('.shape').forEach((shape, index) => {
            shape.style.animationDelay = `${index * 0.5}s`;
        });
        
        // Add subtle mouse movement effect
        document.addEventListener('mousemove', (e) => {
            const shapes = document.querySelectorAll('.shape');
            shapes.forEach((shape, index) => {
                const speed = (index + 1) * 0.01;
                const x = e.clientX * speed;
                const y = e.clientY * speed;
                shape.style.transform = `translate(${x}px, ${y}px)`;
            });
        });
    </script>
</body>
</html>