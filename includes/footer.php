    <!-- Footer -->
    <footer class="bg-dark text-light py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5><i class="fas fa-bullhorn me-2"></i><?php echo SITE_NAME; ?></h5>
                    <p class="text-muted">Найкраща дошка оголошень в Україні. Купуйте та продавайте товари та послуги легко та безпечно.</p>
                    <div class="social-links">
                        <a href="#" class="text-light me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-telegram"></i></a>
                    </div>
                </div>
                
                <div class="col-md-2 mb-4">
                    <h6>Розділи</h6>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-muted text-decoration-none">Головна</a></li>
                        <li><a href="pages/categories.php" class="text-muted text-decoration-none">Категорії</a></li>
                        <li><a href="pages/search.php" class="text-muted text-decoration-none">Пошук</a></li>
                        <li><a href="pages/add_ad.php" class="text-muted text-decoration-none">Подати оголошення</a></li>
                    </ul>
                </div>
                
                <div class="col-md-3 mb-4">
                    <h6>Популярні категорії</h6>
                    <ul class="list-unstyled">
                        <li><a href="pages/category.php?id=1" class="text-muted text-decoration-none">Транспорт</a></li>
                        <li><a href="pages/category.php?id=2" class="text-muted text-decoration-none">Нерухомість</a></li>
                        <li><a href="pages/category.php?id=6" class="text-muted text-decoration-none">Електроніка</a></li>
                        <li><a href="pages/category.php?id=3" class="text-muted text-decoration-none">Робота</a></li>
                    </ul>
                </div>
                
                <div class="col-md-3 mb-4">
                    <h6>Контакти</h6>
                    <ul class="list-unstyled text-muted">
                        <li><i class="fas fa-envelope me-2"></i>info@example.com</li>
                        <li><i class="fas fa-phone me-2"></i>+38 (050) 123-45-67</li>
                        <li><i class="fas fa-map-marker-alt me-2"></i>Київ, Україна</li>
                    </ul>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-muted mb-0">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Всі права захищені.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="text-muted text-decoration-none me-3">Правила користування</a>
                    <a href="#" class="text-muted text-decoration-none">Політика конфіденційності</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button id="backToTop" class="btn btn-primary btn-floating d-none">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS Animation -->
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/main.js"></script>
    
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });
    </script>
</body>
</html>