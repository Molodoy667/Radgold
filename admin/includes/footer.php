    </div> <!-- Закрываем main-content -->
    
    <!-- Profile Modal -->
    <div class="modal fade" id="profileModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content profile-modal">
                <div class="modal-header profile-modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-edit me-2"></i>Редагування профілю
                    </h5>
                    <button type="button" class="btn-close profile-modal-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="profileForm" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <div class="mb-3">
                                    <?php if ($admin['avatar'] && file_exists('../' . $admin['avatar'])): ?>
                                        <img src="../<?php echo htmlspecialchars($admin['avatar']); ?>" 
                                             alt="Аватар" class="img-thumbnail mb-2" 
                                             style="width: 150px; height: 150px; object-fit: cover;" id="avatarPreview">
                                    <?php else: ?>
                                        <div class="bg-gradient text-white d-flex align-items-center justify-content-center mb-2" 
                                             style="width: 150px; height: 150px; border-radius: 8px; margin: 0 auto; font-size: 3rem;" id="avatarPreview">
                                            <?php echo strtoupper(substr($admin['username'], 0, 1)); ?>
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*" onchange="previewAvatar(this)">
                                    <small class="text-muted">JPG, PNG до 2MB</small>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Ім'я користувача</label>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           value="<?php echo htmlspecialchars($admin['username']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">Новий пароль (якщо змінюєте)</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="new_password" name="new_password" minlength="6">
                                        <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('new_password')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Підтвердження пароля</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="6">
                                        <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('confirm_password')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer profile-modal-footer">
                    <button type="button" class="btn btn-secondary profile-btn-secondary" data-bs-dismiss="modal">Скасувати</button>
                    <button type="button" class="btn btn-primary profile-btn-primary" onclick="saveProfile()">
                        <i class="fas fa-save me-1"></i>Зберегти
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Admin Scripts -->
    <script>
        // Sidebar Management
        function toggleSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            const indicator = document.querySelector('.swipe-indicator');
            
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
            
            // Hide/show swipe indicator
            if (sidebar.classList.contains('active')) {
                indicator.classList.add('hidden');
            } else {
                indicator.classList.remove('hidden');
            }
        }
        
        function closeSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            const indicator = document.querySelector('.swipe-indicator');
            
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            indicator.classList.remove('hidden');
        }

        // Touch support for mobile devices
        let touchStartX = 0;
        let touchStartY = 0;
        let touchCurrentX = 0;
        let touchCurrentY = 0;
        let isSwiping = false;
        let isSwipeToOpen = false;
        let isSwipeToClose = false;

        // Touch event handlers
        function handleTouchStart(e) {
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
            isSwiping = true;
            
            const sidebar = document.getElementById('adminSidebar');
            isSwipeToOpen = touchStartX < 50 && !sidebar.classList.contains('active');
            isSwipeToClose = sidebar.classList.contains('active') && touchStartX < 350;
        }

        function handleTouchMove(e) {
            if (!isSwiping) return;
            
            e.preventDefault();
            
            touchCurrentX = e.touches[0].clientX;
            touchCurrentY = e.touches[0].clientY;
            
            const deltaX = touchCurrentX - touchStartX;
            const deltaY = Math.abs(e.touches[0].clientY - touchStartY);
            
            if (deltaY > 50) {
                isSwiping = false;
                return;
            }
            
            const sidebar = document.getElementById('adminSidebar');
            
            if (isSwipeToOpen && deltaX > 0) {
                const progress = Math.min(deltaX / 350, 1);
                sidebar.style.transform = `translateX(${Math.max(-350 + deltaX, -350)}px)`;
                sidebar.style.transition = 'none';
                
                const overlay = document.querySelector('.sidebar-overlay');
                overlay.style.opacity = progress * 0.5;
                overlay.style.visibility = 'visible';
                overlay.style.transition = 'none';
                
            } else if (isSwipeToClose && deltaX < 0) {
                const progress = Math.max(1 + deltaX / 350, 0);
                sidebar.style.transform = `translateX(${Math.min(deltaX, 0)}px)`;
                sidebar.style.transition = 'none';
                
                const overlay = document.querySelector('.sidebar-overlay');
                overlay.style.opacity = progress * 0.5;
                overlay.style.transition = 'none';
            }
        }

        function handleTouchEnd(e) {
            if (!isSwiping) return;
            
            const deltaX = touchCurrentX - touchStartX;
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            
            sidebar.style.transition = 'all 0.3s ease';
            sidebar.style.transform = '';
            overlay.style.transition = 'all 0.3s ease';
            
            if (isSwipeToOpen && deltaX > 100) {
                sidebar.classList.add('active');
                overlay.classList.add('active');
                document.querySelector('.swipe-indicator').classList.add('hidden');
            } else if (isSwipeToClose && deltaX < -100) {
                closeSidebar();
            } else {
                if (sidebar.classList.contains('active')) {
                    overlay.style.opacity = '';
                    overlay.style.visibility = '';
                } else {
                    overlay.style.opacity = '0';
                    overlay.style.visibility = 'hidden';
                }
            }
            
            isSwiping = false;
            isSwipeToOpen = false;
            isSwipeToClose = false;
            touchStartX = 0;
            touchCurrentX = 0;
        }

        // Add touch event listeners
        document.addEventListener('touchstart', handleTouchStart, { passive: false });
        document.addEventListener('touchmove', handleTouchMove, { passive: false });
        document.addEventListener('touchend', handleTouchEnd, { passive: true });
        
        // Profile Management
        function openProfileModal() {
            const modal = new bootstrap.Modal(document.getElementById('profileModal'));
            modal.show();
        }
        
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }
        
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('avatarPreview');
                    if (preview.tagName === 'IMG') {
                        preview.src = e.target.result;
                    } else {
                        preview.outerHTML = `<img src="${e.target.result}" alt="Аватар" class="img-thumbnail mb-2" style="width: 150px; height: 150px; object-fit: cover;" id="avatarPreview">`;
                    }
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        function saveProfile() {
            const form = document.getElementById('profileForm');
            const formData = new FormData(form);
            
            // Валидация
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (newPassword && newPassword !== confirmPassword) {
                alert('Паролі не співпадають!');
                return;
            }
            
            // Отправка данных
            fetch('ajax/save_profile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Профіль успішно оновлено!', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('profileModal')).hide();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(data.message || 'Помилка оновлення профілю', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Помилка з\'єднання з сервером', 'error');
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
        
        // Инициализация темы при загрузке
        document.addEventListener('DOMContentLoaded', function() {
            // Проверяем текущую тему и обновляем интерфейс
            updateThemeInterface();
        });
        
        function updateThemeInterface() {
            // Здесь можно добавить логику обновления интерфейса при смене темы
            // Например, обновление иконок, цветов и т.д.
        }
    </script>
    
    <!-- Additional page-specific styles -->
    <style>
        /* Profile Modal Styles */
        .profile-modal {
            background: var(--card-bg) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 20px !important;
        }
        
        .profile-modal-header {
            background: var(--theme-gradient) !important;
            color: white !important;
            border-radius: 20px 20px 0 0 !important;
            border-bottom: none !important;
        }
        
        .profile-modal-header .modal-title {
            color: white !important;
        }
        
        .profile-modal-close {
            filter: brightness(0) invert(1);
        }
        
        .profile-modal .modal-body {
            background: var(--card-bg) !important;
            color: var(--text-color) !important;
        }
        
        .profile-modal .form-control {
            background: var(--surface-color) !important;
            border: 2px solid var(--border-color) !important;
            color: var(--text-color) !important;
            border-radius: 12px !important;
            padding: 0.75rem 1rem !important;
            transition: all 0.3s ease !important;
            position: relative !important;
        }
        
        .profile-modal .form-control:hover {
            border-color: transparent !important;
            background: var(--card-bg) !important;
            background-image: var(--theme-gradient) !important;
            background-size: 100% 2px !important;
            background-position: 0 100% !important;
            background-repeat: no-repeat !important;
            box-shadow: 0 4px 15px rgba(var(--theme-primary-rgb), 0.1) !important;
            transform: translateY(-1px) !important;
        }
        
        .profile-modal .form-control:focus {
            background: var(--card-bg) !important;
            border-color: transparent !important;
            color: var(--text-color) !important;
            box-shadow: 
                0 0 0 3px rgba(var(--theme-primary-rgb), 0.15) !important,
                0 8px 25px rgba(var(--theme-primary-rgb), 0.2) !important;
            transform: translateY(-2px) !important;
            background-image: var(--theme-gradient) !important;
            background-size: 100% 2px !important;
            background-position: 0 100% !important;
            background-repeat: no-repeat !important;
        }
        
        .profile-modal .form-label {
            color: var(--text-color) !important;
            font-weight: 600;
        }
        
        .profile-modal-footer {
            background: var(--surface-color) !important;
            border-top: 1px solid var(--border-color) !important;
            border-radius: 0 0 20px 20px !important;
        }
        
        .profile-btn-primary {
            background: var(--theme-gradient) !important;
            border: none !important;
            border-radius: 10px !important;
            transition: all 0.3s ease !important;
        }
        
        .profile-btn-primary:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 25px rgba(var(--theme-primary-rgb), 0.4) !important;
        }
        
        .profile-btn-secondary {
            background: var(--surface-color) !important;
            border: 1px solid var(--border-color) !important;
            color: var(--text-color) !important;
            border-radius: 10px !important;
        }
        
        .profile-btn-secondary:hover {
            background: var(--theme-primary) !important;
            border-color: var(--theme-primary) !important;
            color: white !important;
        }
    </style>
</body>
</html>