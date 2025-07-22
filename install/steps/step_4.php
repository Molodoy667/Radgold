<?php
// Крок 4: Базові налаштування сайту
$siteData = $_SESSION['install_data']['site'] ?? [];
?>

<div class="step-content animate__animated animate__fadeIn">
    <div class="step-header">
        <h3><i class="fas fa-globe me-3"></i>Базові налаштування сайту</h3>
        <p class="text-muted">Налаштуйте основні параметри вашого сайту</p>
    </div>

    <form method="POST" id="siteForm" novalidate>
        <input type="hidden" name="step" value="4">
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-4">
                    <label for="site_name" class="form-label required">
                        <i class="fas fa-tag me-2"></i>Назва сайту
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="site_name" 
                           name="site_name" 
                           value="<?php echo htmlspecialchars($siteData['site_name'] ?? 'AdBoard Pro'); ?>" 
                           required 
                           placeholder="Введіть назву сайту">
                    <div class="invalid-feedback">Назва сайту обов'язкова</div>
                    <small class="form-text text-muted">Відображається в заголовку браузера та на сайті</small>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="mb-4">
                    <label for="site_url" class="form-label required">
                        <i class="fas fa-link me-2"></i>URL сайту
                    </label>
                    <input type="url" 
                           class="form-control" 
                           id="site_url" 
                           name="site_url" 
                           value="<?php echo htmlspecialchars($siteData['site_url'] ?? 'http://' . $_SERVER['HTTP_HOST']); ?>" 
                           required 
                           placeholder="https://example.com">
                    <div class="invalid-feedback">Введіть коректний URL</div>
                    <small class="form-text text-muted">Основний URL вашого сайту (без / в кінці)</small>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <label for="site_description" class="form-label required">
                <i class="fas fa-align-left me-2"></i>Опис сайту
            </label>
            <textarea class="form-control" 
                      id="site_description" 
                      name="site_description" 
                      rows="3" 
                      required 
                      placeholder="Короткий опис вашого сайту для пошукових систем"
                      maxlength="160"><?php echo htmlspecialchars($siteData['site_description'] ?? 'Рекламна компанія та дошка оголошень'); ?></textarea>
            <div class="invalid-feedback">Опис сайту обов'язковий</div>
            <small class="form-text text-muted"><span id="descLength">0</span>/160 символів</small>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-4">
                    <label for="site_keywords" class="form-label">
                        <i class="fas fa-tags me-2"></i>Ключові слова
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="site_keywords" 
                           name="site_keywords" 
                           value="<?php echo htmlspecialchars($siteData['site_keywords'] ?? 'реклама, оголошення, дошка оголошень'); ?>" 
                           placeholder="ключове слово, інше слово, ще одне">
                    <small class="form-text text-muted">Розділяйте ключові слова комами</small>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="mb-4">
                    <label for="contact_email" class="form-label required">
                        <i class="fas fa-envelope me-2"></i>Контактний email
                    </label>
                    <input type="email" 
                           class="form-control" 
                           id="contact_email" 
                           name="contact_email" 
                           value="<?php echo htmlspecialchars($siteData['contact_email'] ?? ''); ?>" 
                           required 
                           placeholder="admin@example.com">
                    <div class="invalid-feedback">Введіть коректний email</div>
                    <small class="form-text text-muted">Основний контактний email</small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-4">
                    <label for="timezone" class="form-label">
                        <i class="fas fa-clock me-2"></i>Часовий пояс
                    </label>
                    <select class="form-select" id="timezone" name="timezone">
                        <?php
                        $timezones = [
                            'Europe/Kiev' => 'Київ (UTC+2)',
                            'Europe/London' => 'Лондон (UTC+0)',
                            'Europe/Berlin' => 'Берлін (UTC+1)',
                            'Europe/Moscow' => 'Москва (UTC+3)',
                            'America/New_York' => 'Нью-Йорк (UTC-5)',
                            'America/Los_Angeles' => 'Лос-Анджелес (UTC-8)',
                            'Asia/Tokyo' => 'Токіо (UTC+9)'
                        ];
                        $selectedTimezone = $siteData['timezone'] ?? 'Europe/Kiev';
                        foreach ($timezones as $value => $label):
                        ?>
                            <option value="<?php echo $value; ?>" <?php echo $value === $selectedTimezone ? 'selected' : ''; ?>>
                                <?php echo $label; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="mb-4">
                    <label for="language" class="form-label">
                        <i class="fas fa-language me-2"></i>Мова за замовчуванням
                    </label>
                    <select class="form-select" id="language" name="language">
                        <?php
                        $languages = [
                            'uk' => 'Українська',
                            'en' => 'English',
                            'ru' => 'Русский',
                            'de' => 'Deutsch',
                            'fr' => 'Français'
                        ];
                        $selectedLanguage = $siteData['language'] ?? 'uk';
                        foreach ($languages as $value => $label):
                        ?>
                            <option value="<?php echo $value; ?>" <?php echo $value === $selectedLanguage ? 'selected' : ''; ?>>
                                <?php echo $label; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="info-box">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Важливо:</strong> Ці налаштування можна буде змінити пізніше в адміністративній панелі.
        </div>

        <div class="navigation-buttons">
            <a href="?step=3" class="btn btn-outline-secondary">
                <i class="fas fa-chevron-left me-2"></i>Назад
            </a>
            <button type="submit" class="btn btn-primary">
                Далі<i class="fas fa-chevron-right ms-2"></i>
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('siteForm');
    const descTextarea = document.getElementById('site_description');
    const descLength = document.getElementById('descLength');
    
    // Оновлення лічильника символів
    function updateDescLength() {
        const length = descTextarea.value.length;
        descLength.textContent = length;
        descLength.style.color = length > 160 ? '#dc3545' : length > 140 ? '#ffc107' : '#6c757d';
    }
    
    descTextarea.addEventListener('input', updateDescLength);
    updateDescLength();
    
    // Валідація форми
    form.addEventListener('submit', function(e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add('was-validated');
    });
    
    // Валідація URL в реальному часі
    const urlInput = document.getElementById('site_url');
    urlInput.addEventListener('blur', function() {
        const url = this.value.trim();
        if (url && !isValidUrl(url)) {
            this.setCustomValidity('Введіть коректний URL (наприклад: https://example.com)');
        } else {
            this.setCustomValidity('');
        }
    });
});
</script>