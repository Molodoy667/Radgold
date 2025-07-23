<?php
// Крок 5: Додаткові налаштування
$additionalData = $_SESSION['install_data']['additional'] ?? [];
?>

<div class="step-content animate__animated animate__fadeIn">
    <div class="step-header">
        <h3><i class="fas fa-cogs me-3"></i>Додаткові налаштування</h3>
        <p class="text-muted">Налаштуйте мову, часовий пояс та візуальні ефекти</p>
    </div>

    <form method="POST" id="additionalForm">
        <input type="hidden" name="step" value="5">
        
        <!-- Мова сайту -->
        <div class="settings-section mb-5">
            <h5 class="mb-4">
                <i class="fas fa-globe me-2"></i>Мова сайту за замовчуванням
            </h5>
            <div class="row">
                <div class="col-md-4">
                    <div class="language-option">
                        <input type="radio" 
                               id="lang_uk" 
                               name="default_language" 
                               value="uk" 
                               <?php echo ($additionalData['default_language'] ?? 'uk') === 'uk' ? 'checked' : ''; ?>>
                        <label for="lang_uk" class="language-card">
                            <div class="flag-icon">🇺🇦</div>
                            <div class="language-info">
                                <h6>Українська</h6>
                                <p class="mb-0">Ukrainian</p>
                            </div>
                        </label>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="language-option">
                        <input type="radio" 
                               id="lang_ru" 
                               name="default_language" 
                               value="ru" 
                               <?php echo ($additionalData['default_language'] ?? 'uk') === 'ru' ? 'checked' : ''; ?>>
                        <label for="lang_ru" class="language-card">
                            <div class="flag-icon">🇷🇺</div>
                            <div class="language-info">
                                <h6>Русский</h6>
                                <p class="mb-0">Russian</p>
                            </div>
                        </label>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="language-option">
                        <input type="radio" 
                               id="lang_en" 
                               name="default_language" 
                               value="en" 
                               <?php echo ($additionalData['default_language'] ?? 'uk') === 'en' ? 'checked' : ''; ?>>
                        <label for="lang_en" class="language-card">
                            <div class="flag-icon">🇺🇸</div>
                            <div class="language-info">
                                <h6>English</h6>
                                <p class="mb-0">English</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Часовий пояс -->
        <div class="settings-section mb-5">
            <h5 class="mb-4">
                <i class="fas fa-clock me-2"></i>Часовий пояс
            </h5>
            <div class="row">
                <div class="col-md-12">
                    <select class="form-select form-select-lg" name="timezone" id="timezone">
                        <option value="">Оберіть часовий пояс</option>
                        <optgroup label="Європа">
                            <option value="Europe/Kiev" <?php echo ($additionalData['timezone'] ?? 'Europe/Kiev') === 'Europe/Kiev' ? 'selected' : ''; ?>>
                                🇺🇦 Київ (UTC+2)
                            </option>
                            <option value="Europe/Warsaw" <?php echo ($additionalData['timezone'] ?? '') === 'Europe/Warsaw' ? 'selected' : ''; ?>>
                                🇵🇱 Варшава (UTC+1)
                            </option>
                            <option value="Europe/Berlin" <?php echo ($additionalData['timezone'] ?? '') === 'Europe/Berlin' ? 'selected' : ''; ?>>
                                🇩🇪 Берлін (UTC+1)
                            </option>
                            <option value="Europe/London" <?php echo ($additionalData['timezone'] ?? '') === 'Europe/London' ? 'selected' : ''; ?>>
                                🇬🇧 Лондон (UTC+0)
                            </option>
                            <option value="Europe/Paris" <?php echo ($additionalData['timezone'] ?? '') === 'Europe/Paris' ? 'selected' : ''; ?>>
                                🇫🇷 Париж (UTC+1)
                            </option>
                            <option value="Europe/Rome" <?php echo ($additionalData['timezone'] ?? '') === 'Europe/Rome' ? 'selected' : ''; ?>>
                                🇮🇹 Рим (UTC+1)
                            </option>
                            <option value="Europe/Madrid" <?php echo ($additionalData['timezone'] ?? '') === 'Europe/Madrid' ? 'selected' : ''; ?>>
                                🇪🇸 Мадрид (UTC+1)
                            </option>
                            <option value="Europe/Amsterdam" <?php echo ($additionalData['timezone'] ?? '') === 'Europe/Amsterdam' ? 'selected' : ''; ?>>
                                🇳🇱 Амстердам (UTC+1)
                            </option>
                            <option value="Europe/Stockholm" <?php echo ($additionalData['timezone'] ?? '') === 'Europe/Stockholm' ? 'selected' : ''; ?>>
                                🇸🇪 Стокгольм (UTC+1)
                            </option>
                            <option value="Europe/Helsinki" <?php echo ($additionalData['timezone'] ?? '') === 'Europe/Helsinki' ? 'selected' : ''; ?>>
                                🇫🇮 Гельсінкі (UTC+2)
                            </option>
                        </optgroup>
                        
                        <optgroup label="Америка">
                            <option value="America/New_York" <?php echo ($additionalData['timezone'] ?? '') === 'America/New_York' ? 'selected' : ''; ?>>
                                🇺🇸 Нью-Йорк (UTC-5)
                            </option>
                            <option value="America/Los_Angeles" <?php echo ($additionalData['timezone'] ?? '') === 'America/Los_Angeles' ? 'selected' : ''; ?>>
                                🇺🇸 Лос-Анджелес (UTC-8)
                            </option>
                            <option value="America/Chicago" <?php echo ($additionalData['timezone'] ?? '') === 'America/Chicago' ? 'selected' : ''; ?>>
                                🇺🇸 Чикаго (UTC-6)
                            </option>
                            <option value="America/Toronto" <?php echo ($additionalData['timezone'] ?? '') === 'America/Toronto' ? 'selected' : ''; ?>>
                                🇨🇦 Торонто (UTC-5)
                            </option>
                            <option value="America/Vancouver" <?php echo ($additionalData['timezone'] ?? '') === 'America/Vancouver' ? 'selected' : ''; ?>>
                                🇨🇦 Ванкувер (UTC-8)
                            </option>
                            <option value="America/Mexico_City" <?php echo ($additionalData['timezone'] ?? '') === 'America/Mexico_City' ? 'selected' : ''; ?>>
                                🇲🇽 Мехіко (UTC-6)
                            </option>
                            <option value="America/Sao_Paulo" <?php echo ($additionalData['timezone'] ?? '') === 'America/Sao_Paulo' ? 'selected' : ''; ?>>
                                🇧🇷 Сан-Паулу (UTC-3)
                            </option>
                            <option value="America/Buenos_Aires" <?php echo ($additionalData['timezone'] ?? '') === 'America/Buenos_Aires' ? 'selected' : ''; ?>>
                                🇦🇷 Буенос-Айрес (UTC-3)
                            </option>
                        </optgroup>
                        
                        <optgroup label="Азія">
                            <option value="Asia/Tokyo" <?php echo ($additionalData['timezone'] ?? '') === 'Asia/Tokyo' ? 'selected' : ''; ?>>
                                🇯🇵 Токіо (UTC+9)
                            </option>
                            <option value="Asia/Shanghai" <?php echo ($additionalData['timezone'] ?? '') === 'Asia/Shanghai' ? 'selected' : ''; ?>>
                                🇨🇳 Шанхай (UTC+8)
                            </option>
                            <option value="Asia/Seoul" <?php echo ($additionalData['timezone'] ?? '') === 'Asia/Seoul' ? 'selected' : ''; ?>>
                                🇰🇷 Сеул (UTC+9)
                            </option>
                            <option value="Asia/Singapore" <?php echo ($additionalData['timezone'] ?? '') === 'Asia/Singapore' ? 'selected' : ''; ?>>
                                🇸🇬 Сінгапур (UTC+8)
                            </option>
                            <option value="Asia/Dubai" <?php echo ($additionalData['timezone'] ?? '') === 'Asia/Dubai' ? 'selected' : ''; ?>>
                                🇦🇪 Дубай (UTC+4)
                            </option>
                            <option value="Asia/Mumbai" <?php echo ($additionalData['timezone'] ?? '') === 'Asia/Mumbai' ? 'selected' : ''; ?>>
                                🇮🇳 Мумбай (UTC+5:30)
                            </option>
                            <option value="Asia/Bangkok" <?php echo ($additionalData['timezone'] ?? '') === 'Asia/Bangkok' ? 'selected' : ''; ?>>
                                🇹🇭 Бангкок (UTC+7)
                            </option>
                            <option value="Asia/Jakarta" <?php echo ($additionalData['timezone'] ?? '') === 'Asia/Jakarta' ? 'selected' : ''; ?>>
                                🇮🇩 Джакарта (UTC+7)
                            </option>
                        </optgroup>
                        
                        <optgroup label="Австралія/Океанія">
                            <option value="Australia/Sydney" <?php echo ($additionalData['timezone'] ?? '') === 'Australia/Sydney' ? 'selected' : ''; ?>>
                                🇦🇺 Сідней (UTC+10)
                            </option>
                            <option value="Australia/Melbourne" <?php echo ($additionalData['timezone'] ?? '') === 'Australia/Melbourne' ? 'selected' : ''; ?>>
                                🇦🇺 Мельбурн (UTC+10)
                            </option>
                            <option value="Australia/Perth" <?php echo ($additionalData['timezone'] ?? '') === 'Australia/Perth' ? 'selected' : ''; ?>>
                                🇦🇺 Перт (UTC+8)
                            </option>
                            <option value="Pacific/Auckland" <?php echo ($additionalData['timezone'] ?? '') === 'Pacific/Auckland' ? 'selected' : ''; ?>>
                                🇳🇿 Окленд (UTC+12)
                            </option>
                        </optgroup>
                        
                        <optgroup label="Африка">
                            <option value="Africa/Cairo" <?php echo ($additionalData['timezone'] ?? '') === 'Africa/Cairo' ? 'selected' : ''; ?>>
                                🇪🇬 Каїр (UTC+2)
                            </option>
                            <option value="Africa/Lagos" <?php echo ($additionalData['timezone'] ?? '') === 'Africa/Lagos' ? 'selected' : ''; ?>>
                                🇳🇬 Лагос (UTC+1)
                            </option>
                            <option value="Africa/Johannesburg" <?php echo ($additionalData['timezone'] ?? '') === 'Africa/Johannesburg' ? 'selected' : ''; ?>>
                                🇿🇦 Йоганнесбург (UTC+2)
                            </option>
                            <option value="Africa/Casablanca" <?php echo ($additionalData['timezone'] ?? '') === 'Africa/Casablanca' ? 'selected' : ''; ?>>
                                🇲🇦 Касабланка (UTC+1)
                            </option>
                        </optgroup>
                    </select>
                    <div class="form-text">
                        <i class="fas fa-info-circle me-1"></i>
                        Поточний час буде відображатися згідно з обраним часовим поясом
                    </div>
                </div>
            </div>
        </div>

        <!-- Візуальні ефекти -->
        <div class="settings-section mb-5">
            <h5 class="mb-4">
                <i class="fas fa-magic me-2"></i>Візуальні ефекти
            </h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="effect-option">
                        <div class="form-check form-switch form-switch-lg">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="enable_animations" 
                                   name="enable_animations" 
                                   value="1"
                                   <?php echo ($additionalData['enable_animations'] ?? '1') === '1' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="enable_animations">
                                <div class="effect-info">
                                    <h6><i class="fas fa-play-circle me-2"></i>Анімації</h6>
                                    <p class="mb-0">Плавні переходи між сторінками та елементами</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="effect-option">
                        <div class="form-check form-switch form-switch-lg">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="enable_particles" 
                                   name="enable_particles" 
                                   value="1"
                                   <?php echo ($additionalData['enable_particles'] ?? '0') === '1' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="enable_particles">
                                <div class="effect-info">
                                    <h6><i class="fas fa-sparkles me-2"></i>Частинки на фоні</h6>
                                    <p class="mb-0">Інтерактивні частинки для динамічного ефекту</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="effect-option">
                        <div class="form-check form-switch form-switch-lg">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="smooth_scroll" 
                                   name="smooth_scroll" 
                                   value="1"
                                   <?php echo ($additionalData['smooth_scroll'] ?? '1') === '1' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="smooth_scroll">
                                <div class="effect-info">
                                    <h6><i class="fas fa-arrows-alt-v me-2"></i>Плавна прокрутка</h6>
                                    <p class="mb-0">М'яка прокрутка сторінки для кращого UX</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="effect-option">
                        <div class="form-check form-switch form-switch-lg">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="enable_tooltips" 
                                   name="enable_tooltips" 
                                   value="1"
                                   <?php echo ($additionalData['enable_tooltips'] ?? '1') === '1' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="enable_tooltips">
                                <div class="effect-info">
                                    <h6><i class="fas fa-comment-dots me-2"></i>Підказки</h6>
                                    <p class="mb-0">Корисні підказки при наведенні на елементи</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Кнопки навігації -->
        <div class="step-navigation">
            <a href="?step=4" class="btn btn-outline-secondary btn-lg">
                <i class="fas fa-chevron-left me-2"></i>Назад
            </a>
            <button type="submit" class="btn btn-primary btn-lg">
                Далі<i class="fas fa-chevron-right ms-2"></i>
            </button>
        </div>
    </form>
</div>

<style>
.settings-section {
    background: var(--bs-card-bg);
    border: 1px solid var(--bs-border-color);
    border-radius: 12px;
    padding: 2rem;
}

.language-option {
    position: relative;
    margin-bottom: 1rem;
}

.language-card {
    display: block;
    border: 2px solid var(--bs-border-color);
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: var(--bs-body-bg);
    height: 100%;
}

.language-card:hover {
    border-color: var(--bs-primary);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.language-option input[type="radio"]:checked + .language-card {
    border-color: var(--bs-primary);
    background: rgba(var(--bs-primary-rgb), 0.1);
}

.flag-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.language-info h6 {
    margin-bottom: 0.25rem;
    color: var(--bs-body-color);
}

.language-info p {
    color: var(--bs-text-muted);
    font-size: 0.875rem;
}

.effect-option {
    background: var(--bs-body-bg);
    border: 1px solid var(--bs-border-color);
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.effect-option:hover {
    border-color: var(--bs-primary);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.form-switch .form-check-input:checked + .form-check-label .effect-option {
    background: rgba(var(--bs-primary-rgb), 0.1);
}

.effect-info h6 {
    margin-bottom: 0.5rem;
    color: var(--bs-body-color);
}

.effect-info p {
    color: var(--bs-text-muted);
    font-size: 0.875rem;
}

.form-switch-lg .form-check-input {
    width: 3rem;
    height: 1.5rem;
}

.form-switch-lg .form-check-label {
    padding-left: 4rem;
}

.language-option input[type="radio"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

#timezone {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Попередній перегляд ефектів
    const animationsToggle = document.getElementById('enable_animations');
    const particlesToggle = document.getElementById('enable_particles');
    const smoothScrollToggle = document.getElementById('smooth_scroll');
    const tooltipsToggle = document.getElementById('enable_tooltips');
    
    // Оновлення часу для обраного часового поясу
    const timezoneSelect = document.getElementById('timezone');
    
    function updateTimezonePreview() {
        const selectedTimezone = timezoneSelect.value;
        if (selectedTimezone) {
            try {
                const now = new Date();
                const formatter = new Intl.DateTimeFormat('uk-UA', {
                    timeZone: selectedTimezone,
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
                
                const timeString = formatter.format(now);
                const helpText = timezoneSelect.parentNode.querySelector('.form-text');
                helpText.innerHTML = `<i class="fas fa-info-circle me-1"></i>Поточний час: ${timeString}`;
            } catch (e) {
                console.error('Помилка форматування часу:', e);
            }
        }
    }
    
    timezoneSelect.addEventListener('change', updateTimezonePreview);
    updateTimezonePreview(); // Початкове оновлення
    
    // Оновлення кожну секунду
    setInterval(updateTimezonePreview, 1000);
    
    // Валідація форми
    document.getElementById('additionalForm').addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        const language = document.querySelector('input[name="default_language"]:checked');
        const timezone = document.getElementById('timezone').value;
        
        if (!language) {
            e.preventDefault();
            alert('Будь ласка, оберіть мову за замовчуванням');
            // Скидаємо стан кнопки
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Далі<i class="fas fa-chevron-right ms-2"></i>';
            }
            return;
        }
        
        if (!timezone) {
            e.preventDefault();
            alert('Будь ласка, оберіть часовий пояс');
            // Скидаємо стан кнопки
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Далі<i class="fas fa-chevron-right ms-2"></i>';
            }
            return;
        }
        
        // Показуємо стан завантаження при успішній валідації
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Обробка...';
        }
    });
});
</script>