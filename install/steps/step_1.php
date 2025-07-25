<div class="text-center mb-4">
    <i class="fas fa-file-contract fa-3x text-primary mb-3"></i>
    <h3>Ліцензійна угода</h3>
    <p class="text-muted">Будь ласка, прочитайте та прийміть ліцензійну угоду для продовження установки</p>
</div>

<div class="license-text">
    <h5><strong>ЛІЦЕНЗІЙНА УГОДА AdBoard Pro</strong></h5>
    
    <p><strong>1. ЗАГАЛЬНІ ПОЛОЖЕННЯ</strong></p>
    <p>Ця ліцензійна угода ("Угода") є юридичною угодою між вами (як фізичною, так і юридичною особою) та розробниками AdBoard Pro щодо використання програмного забезпечення AdBoard Pro.</p>
    
    <p><strong>2. НАДАННЯ ЛІЦЕНЗІЇ</strong></p>
    <p>Розробники надають вам невиключну ліцензію на використання AdBoard Pro відповідно до умов цієї Угоди. Ви маєте право:</p>
    <ul>
        <li>Встановлювати та використовувати програмне забезпечення на одному сервері</li>
        <li>Створювати резервні копії програмного забезпечення</li>
        <li>Модифікувати програмне забезпечення відповідно до ваших потреб</li>
    </ul>
    
    <p><strong>3. ОБМЕЖЕННЯ</strong></p>
    <p>Ви НЕ маєте права:</p>
    <ul>
        <li>Перепродавати, розповсюджувати або передавати програмне забезпечення третім особам</li>
        <li>Видаляти авторські права або інші повідомлення про права власності</li>
        <li>Здійснювати зворотну інженерію програмного забезпечення</li>
    </ul>
    
    <p><strong>4. ВІДМОВА ВІД ГАРАНТІЙ</strong></p>
    <p>Програмне забезпечення надається "як є" без будь-яких гарантій. Розробники не несуть відповідальності за будь-які збитки, що можуть виникнути від використання програмного забезпечення.</p>
    
    <p><strong>5. ПІДТРИМКА</strong></p>
    <p>Розробники зобов'язуються надавати технічну підтримку та оновлення програмного забезпечення протягом періоду дії ліцензії.</p>
    
    <p><strong>6. ПРИПИНЕННЯ УГОДИ</strong></p>
    <p>Ця угода діє до її припинення. Угода автоматично припиняється у разі порушення будь-яких умов цієї Угоди.</p>
    
    <p><strong>7. ЗАСТОСОВНЕ ПРАВО</strong></p>
    <p>Ця угода регулюється законодавством України.</p>
    
    <p class="mt-4"><small><em>Останнє оновлення: <?php echo date('d.m.Y'); ?></em></small></p>
</div>

<form method="POST" class="mt-4">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" value="1" id="acceptLicense" name="accept_license" required>
        <label class="form-check-label" for="acceptLicense">
            <strong>Я прочитав та приймаю умови ліцензійної угоди</strong>
        </label>
    </div>
    
    <div class="navigation-buttons">
        <div></div> <!-- Порожній div для вирівнювання -->
        <button type="submit" class="btn btn-next" id="nextBtn" disabled>
            Прийняти та продовжити <i class="fas fa-arrow-right ms-2"></i>
        </button>
    </div>
</form>

<script>
$(document).ready(function() {
    $('#acceptLicense').change(function() {
        $('#nextBtn').prop('disabled', !$(this).is(':checked'));
        
        if ($(this).is(':checked')) {
            $('#nextBtn').addClass('animate__animated animate__pulse');
        } else {
            $('#nextBtn').removeClass('animate__animated animate__pulse');
        }
    });
    
    // Обробка форми
    $('form').on('submit', function(e) {
        const $submitBtn = $('#nextBtn');
        const originalText = $submitBtn.html();
        
        // Спершу показуємо стан завантаження
        $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Обробка...');
        
        // Потім перевіряємо валідацію
        if (!$('#acceptLicense').is(':checked')) {
            e.preventDefault();
            alert('Будь ласка, прийміть ліцензійну угоду для продовження');
            // Скидаємо стан кнопки при помилці
            $submitBtn.prop('disabled', false).html(originalText);
            return;
        }
        
        // Якщо все ОК, залишаємо стан завантаження
    });
});
</script>
