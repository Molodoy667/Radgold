// Основний JavaScript файл для дошки оголошень

document.addEventListener('DOMContentLoaded', function() {
    
    // Back to Top Button
    const backToTopBtn = document.getElementById('backToTop');
    
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopBtn.classList.remove('d-none');
            backToTopBtn.classList.add('animate__animated', 'animate__fadeInUp');
        } else {
            backToTopBtn.classList.add('d-none');
        }
    });
    
    if (backToTopBtn) {
        backToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
    
    // Анімація при наведенні на картки
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Плавна анімація для навігації
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Додаємо ефект риплу
            const ripple = document.createElement('span');
            ripple.className = 'ripple';
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
    
    // Автокомплит для пошуку
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length >= 2) {
                searchTimeout = setTimeout(() => {
                    performAutoComplete(query);
                }, 300);
            } else {
                hideAutoComplete();
            }
        });
    }
    
    // Функція автокомпліту
    function performAutoComplete(query) {
        // AJAX запит для автокомпліту
        fetch(`ajax/autocomplete.php?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                showAutoComplete(data);
            })
            .catch(error => {
                console.error('Помилка автокомпліту:', error);
            });
    }
    
    // Показати список автокомпліту
    function showAutoComplete(suggestions) {
        const searchInput = document.getElementById('searchInput');
        let autocompleteList = document.getElementById('autocompleteList');
        
        if (!autocompleteList) {
            autocompleteList = document.createElement('div');
            autocompleteList.id = 'autocompleteList';
            autocompleteList.className = 'autocomplete-list position-absolute w-100 bg-white border rounded shadow-sm';
            autocompleteList.style.zIndex = '1000';
            searchInput.parentNode.appendChild(autocompleteList);
        }
        
        autocompleteList.innerHTML = '';
        
        suggestions.forEach(suggestion => {
            const item = document.createElement('div');
            item.className = 'autocomplete-item p-2 border-bottom cursor-pointer';
            item.textContent = suggestion.title;
            item.addEventListener('click', function() {
                searchInput.value = suggestion.title;
                hideAutoComplete();
            });
            autocompleteList.appendChild(item);
        });
        
        autocompleteList.style.display = 'block';
    }
    
    // Сховати автокомпліт
    function hideAutoComplete() {
        const autocompleteList = document.getElementById('autocompleteList');
        if (autocompleteList) {
            autocompleteList.style.display = 'none';
        }
    }
    
    // Вподобання
    const favoriteButtons = document.querySelectorAll('.favorite-btn');
    favoriteButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const adId = this.dataset.adId;
            toggleFavorite(adId, this);
        });
    });
    
    // Функція переключення вподобань
    function toggleFavorite(adId, button) {
        fetch('ajax/toggle_favorite.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ ad_id: adId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                button.classList.toggle('active');
                const icon = button.querySelector('i');
                
                if (button.classList.contains('active')) {
                    icon.className = 'fas fa-heart';
                    button.style.animation = 'pulse 0.3s ease';
                } else {
                    icon.className = 'far fa-heart';
                }
                
                setTimeout(() => {
                    button.style.animation = '';
                }, 300);
            }
        })
        .catch(error => {
            console.error('Помилка вподобань:', error);
        });
    }
    
    // Завантаження зображень з попереднім переглядом
    const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
    imageInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            handleImagePreview(e.target.files, this);
        });
    });
    
    // Функція попереднього перегляду зображень
    function handleImagePreview(files, input) {
        const previewContainer = input.parentNode.querySelector('.image-preview') || 
                                createPreviewContainer(input);
        
        previewContainer.innerHTML = '';
        
        Array.from(files).forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imageDiv = document.createElement('div');
                    imageDiv.className = 'preview-image position-relative d-inline-block m-2';
                    imageDiv.innerHTML = `
                        <img src="${e.target.result}" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle" 
                                style="width: 20px; height: 20px; padding: 0; transform: translate(50%, -50%);"
                                onclick="removePreviewImage(this)">
                            <i class="fas fa-times" style="font-size: 10px;"></i>
                        </button>
                    `;
                    previewContainer.appendChild(imageDiv);
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Створити контейнер для попереднього перегляду
    function createPreviewContainer(input) {
        const container = document.createElement('div');
        container.className = 'image-preview mt-2';
        input.parentNode.insertBefore(container, input.nextSibling);
        return container;
    }
    
    // Анімація форм
    const formInputs = document.querySelectorAll('.form-control, .form-select');
    formInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentNode.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            if (!this.value) {
                this.parentNode.classList.remove('focused');
            }
        });
    });
    
    // Lazy loading для зображень
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
    
    // Валідація форм
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
    
    // Ефект ripple для кнопок
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple-effect');
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
    
    // Фільтри та сортування
    const filterButtons = document.querySelectorAll('.filter-btn');
    filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.dataset.filter;
            applyFilter(filter);
        });
    });
    
    // Модальні вікна
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('show.bs.modal', function() {
            this.style.animation = 'fadeIn 0.3s ease';
        });
    });
    
    // Tooltip ініціалізація
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Popover ініціалізація
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
});

// Функція видалення попереднього перегляду зображення
function removePreviewImage(button) {
    button.parentNode.remove();
}

// Функція застосування фільтру
function applyFilter(filter) {
    const items = document.querySelectorAll('.filterable-item');
    items.forEach(item => {
        if (filter === 'all' || item.dataset.category === filter) {
            item.style.display = 'block';
            item.style.animation = 'fadeIn 0.5s ease';
        } else {
            item.style.display = 'none';
        }
    });
}

// Функція для показу/приховування пароля
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.nextElementSibling.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Функція для показу повідомлень
function showAlert(message, type = 'info') {
    const alertContainer = document.getElementById('alertContainer') || createAlertContainer();
    
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    alertContainer.appendChild(alert);
    
    setTimeout(() => {
        alert.remove();
    }, 5000);
}

// Створити контейнер для повідомлень
function createAlertContainer() {
    const container = document.createElement('div');
    container.id = 'alertContainer';
    container.className = 'position-fixed top-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
}

// Функція для показу/приховування пароля
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.nextElementSibling.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Функція виходу з системи
function handleLogout() {
    if (confirm('Ви дійсно хочете вийти з системи?')) {
        fetch('ajax/auth.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=logout'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                // Перезавантажуємо сторінку для оновлення навігації
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Помилка виходу:', error);
            showAlert('Помилка з\'єднання', 'danger');
        });
    }
}

// CSS стилі для ripple ефекту
const style = document.createElement('style');
style.textContent = `
    .ripple-effect {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.6);
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
    
    .autocomplete-item:hover {
        background-color: #f8f9fa;
    }
    
    .focused .form-label {
        color: var(--primary-color);
        transform: translateY(-5px);
    }
`;
document.head.appendChild(style);