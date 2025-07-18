<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AOS Animation Library -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <meta name="description" content="Дошка безкоштовних оголошень - купуйте та продавайте товари та послуги">
    <meta name="keywords" content="оголошення, купити, продати, безкоштовно, дошка оголошень">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-bullhorn me-2"></i><?php echo SITE_NAME; ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php" data-spa data-page="home">
                            <i class="fas fa-home me-1"></i>Головна
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pages/categories.php" data-spa data-page="categories">
                            <i class="fas fa-list me-1"></i>Категорії
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pages/search.php" data-spa data-page="search">
                            <i class="fas fa-search me-1"></i>Пошук
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i><?php echo $_SESSION['username']; ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="pages/profile.php" data-spa data-page="profile">
                                    <i class="fas fa-user-circle me-1"></i>Мій профіль
                                </a></li>
                                <li><a class="dropdown-item" href="pages/my_ads.php" data-spa data-page="my_ads">
                                    <i class="fas fa-list-ul me-1"></i>Мої оголошення
                                </a></li>
                                <li><a class="dropdown-item" href="pages/favorites.php" data-spa data-page="favorites">
                                    <i class="fas fa-heart me-1"></i>Вподобання
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#" onclick="handleLogout(); return false;">
                                    <i class="fas fa-sign-out-alt me-1"></i>Вихід
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="pages/login.php" data-spa data-page="login">
                                <i class="fas fa-sign-in-alt me-1"></i>Вхід
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="pages/register.php" data-spa data-page="register">
                                <i class="fas fa-user-plus me-1"></i>Реєстрація
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <li class="nav-item">
                        <a class="btn btn-warning ms-2 text-dark fw-bold" href="pages/add_ad.php" data-spa data-page="add_ad">
                            <i class="fas fa-plus me-1"></i>Подати оголошення
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->