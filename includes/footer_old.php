    </div> <!-- Закрываем main-content -->
    
    <!-- Footer -->
    <footer class="mt-5 py-4" style="background: var(--card-bg); border-top: 1px solid var(--border-color);">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-gradient"><?php echo htmlspecialchars(Settings::get('site_name', 'Дошка Оголошень')); ?></h6>
                    <p class="text-muted mb-2"><?php echo htmlspecialchars(Settings::get('site_description', 'Безкоштовна дошка оголошень')); ?></p>
                    <p class="text-muted small mb-0">
                        © <?php echo date('Y'); ?> Всі права захищені
                    </p>
                </div>
                <div class="col-md-6 text-end">
                    <div class="d-flex justify-content-end align-items-center">
                        <small class="text-muted me-3">Тема:</small>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm theme-toggle" onclick="toggleDarkMode()">
                                <i id="theme-icon" class="fas fa-<?php echo Theme::getCurrentTheme()['dark_mode'] ? 'sun' : 'moon'; ?>"></i>
                            </button>
                            <button type="button" class="btn btn-sm theme-toggle dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                                <span class="visually-hidden">Налаштування теми</span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><h6 class="dropdown-header">Колірні теми</h6></li>
                                <?php 
                                $gradients = Theme::getGradients();
                                $current_gradient = Theme::getCurrentTheme()['gradient'];
                                $count = 0;
                                foreach ($gradients as $key => $gradient): 
                                    if ($count >= 10) break; // Показываем только первые 10
                                ?>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center <?php echo $current_gradient === $key ? 'active' : ''; ?>" 
                                           href="#" onclick="changeTheme('<?php echo $key; ?>')">
                                            <div class="me-2" style="width: 20px; height: 20px; border-radius: 3px; background: linear-gradient(135deg, <?php echo $gradient[0]; ?> 0%, <?php echo $gradient[1]; ?> 100%);"></div>
                                            <?php echo htmlspecialchars($gradient[2]); ?>
                                        </a>
                                    </li>
                                <?php 
                                    $count++;
                                endforeach; 
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Theme Management -->
    <script>
        // Управление темами
        function toggleDarkMode() {
            const currentTheme = getCurrentThemeFromCSS();
            const newDarkMode = !currentTheme.dark_mode;
            changeTheme(currentTheme.gradient, newDarkMode);
        }
        
        function changeTheme(gradient, darkMode = null) {
            const currentTheme = getCurrentThemeFromCSS();
            const newDarkMode = darkMode !== null ? darkMode : currentTheme.dark_mode;
            
            fetch('ajax/theme.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=change_theme&gradient=${gradient}&dark_mode=${newDarkMode ? 1 : 0}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    applyTheme(data.css, data.theme);
                    updateThemeUI(data.theme);
                    showToast('Тему змінено!', 'success');
                } else {
                    showToast(data.message || 'Помилка зміни теми', 'error');
                }
            })
            .catch(error => {
                console.error('Theme change error:', error);
                showToast('Помилка з\'єднання з сервером', 'error');
            });
        }
        
        function getCurrentThemeFromCSS() {
            // Простое определение текущей темы из CSS переменных
            const root = document.documentElement;
            const isDark = getComputedStyle(root).getPropertyValue('--theme-mode').trim() === 'dark';
            return {
                dark_mode: isDark,
                gradient: 'gradient-2' // По умолчанию
            };
        }
        
        function applyTheme(css, theme) {
            // Удаляем предыдущие динамические стили
            const existingStyle = document.getElementById('dynamic-theme-styles');
            if (existingStyle) {
                existingStyle.remove();
            }
            
            // Добавляем новые стили
            const style = document.createElement('style');
            style.id = 'dynamic-theme-styles';
            style.textContent = css;
            document.head.appendChild(style);
        }
        
        function updateThemeUI(theme) {
            // Обновляем иконку темы
            const themeIcon = document.getElementById('theme-icon');
            if (themeIcon) {
                themeIcon.className = `fas fa-${theme.dark_mode ? 'sun' : 'moon'}`;
            }
            
            // Обновляем активную тему в dropdown
            document.querySelectorAll('.dropdown-item').forEach(item => {
                item.classList.remove('active');
            });
        }
        
        // Toast notifications
        function showToast(message, type = 'info') {
            // Создаем контейнер для тостов если его нет
            let toastContainer = document.getElementById('toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toast-container';
                toastContainer.style.position = 'fixed';
                toastContainer.style.top = '20px';
                toastContainer.style.right = '20px';
                toastContainer.style.zIndex = '9999';
                document.body.appendChild(toastContainer);
            }
            
            // Создаем toast
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white border-0 ${type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-info'}`;
            toast.setAttribute('role', 'alert');
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            
            toastContainer.appendChild(toast);
            
            // Показываем toast
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
            
            // Удаляем toast после скрытия
            toast.addEventListener('hidden.bs.toast', () => {
                toast.remove();
            });
        }
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