#!/bin/bash

echo "🇺🇦 Массовый перевод файла uk.json на украинский язык..."

# Переводим основные интерфейсные элементы
sed -i '
s/"view_details": "View Details"/"view_details": "Переглянути деталі"/g
s/"contact_seller": "Contact Seller"/"contact_seller": "Зв'\''язатися з продавцем"/g
s/"phone_number": "Phone Number"/"phone_number": "Номер телефону"/g
s/"email_address": "Email Address"/"email_address": "Електронна пошта"/g
s/"full_name": "Full Name"/"full_name": "Повне ім'\''я"/g
s/"first_name": "First Name"/"first_name": "Ім'\''я"/g
s/"last_name": "Last Name"/"last_name": "Прізвище"/g
s/"username": "Username"/"username": "Ім'\''я користувача"/g
s/"password": "Password"/"password": "Пароль"/g
s/"confirm_password": "Confirm Password"/"confirm_password": "Підтвердіть пароль"/g
s/"forgot_password": "Forgot Password"/"forgot_password": "Забули пароль"/g
s/"reset_password": "Reset Password"/"reset_password": "Скинути пароль"/g
s/"change_password": "Change Password"/"change_password": "Змінити пароль"/g
s/"old_password": "Old Password"/"old_password": "Старий пароль"/g
s/"new_password": "New Password"/"new_password": "Новий пароль"/g
s/"login": "Login"/"login": "Увійти"/g
s/"register": "Register"/"register": "Зареєструватися"/g
s/"remember_me": "Remember Me"/"remember_me": "Запам'\''ятати мене"/g
s/"submit": "Submit"/"submit": "Надіслати"/g
s/"cancel": "Cancel"/"cancel": "Скасувати"/g
s/"save": "Save"/"save": "Зберегти"/g
s/"edit": "Edit"/"edit": "Редагувати"/g
s/"delete": "Delete"/"delete": "Видалити"/g
s/"update": "Update"/"update": "Оновити"/g
s/"create": "Create"/"create": "Створити"/g
s/"add": "Add"/"add": "Додати"/g
s/"remove": "Remove"/"remove": "Видалити"/g
s/"upload": "Upload"/"upload": "Завантажити"/g
s/"download": "Download"/"download": "Скачати"/g
s/"publish": "Publish"/"publish": "Опублікувати"/g
s/"unpublish": "Unpublish"/"unpublish": "Скасувати публікацію"/g
s/"active": "Active"/"active": "Активний"/g
s/"inactive": "Inactive"/"inactive": "Неактивний"/g
s/"status": "Status"/"status": "Статус"/g
s/"actions": "Actions"/"actions": "Дії"/g
s/"title": "Title"/"title": "Заголовок"/g
s/"description": "Description"/"description": "Опис"/g
s/"price": "Price"/"price": "Ціна"/g
s/"condition": "Condition"/"condition": "Стан"/g
s/"brand": "Brand"/"brand": "Бренд"/g
s/"model": "Model"/"model": "Модель"/g
s/"year": "Year"/"year": "Рік"/g
s/"color": "Color"/"color": "Колір"/g
s/"size": "Size"/"size": "Розмір"/g
s/"weight": "Weight"/"weight": "Вага"/g
' resources/lang/uk.json

echo "✅ Переведено основні терміни. Продовжуємо..."

# Переводим статусы и состояния
sed -i '
s/"new": "New"/"new": "Новий"/g
s/"used": "Used"/"used": "Вживаний"/g
s/"excellent": "Excellent"/"excellent": "Відмінний"/g
s/"good": "Good"/"good": "Хороший"/g
s/"fair": "Fair"/"fair": "Задовільний"/g
s/"poor": "Poor"/"poor": "Поганий"/g
s/"pending": "Pending"/"pending": "Очікування"/g
s/"approved": "Approved"/"approved": "Схвалено"/g
s/"rejected": "Rejected"/"rejected": "Відхилено"/g
s/"expired": "Expired"/"expired": "Закінчився"/g
s/"sold": "Sold"/"sold": "Продано"/g
s/"available": "Available"/"available": "Доступно"/g
s/"unavailable": "Unavailable"/"unavailable": "Недоступно"/g
s/"featured": "Featured"/"featured": "Рекомендовано"/g
s/"urgent": "Urgent"/"urgent": "Терміново"/g
s/"premium": "Premium"/"premium": "Преміум"/g
s/"highlight": "Highlight"/"highlight": "Виділити"/g
s/"promoted": "Promoted"/"promoted": "Просунуто"/g
s/"standard": "Standard"/"standard": "Стандартний"/g
s/"free": "Free"/"free": "Безкоштовно"/g
s/"paid": "Paid"/"paid": "Платний"/g
' resources/lang/uk.json

echo "✅ Переведено статуси. Продовжуємо з формами..."

# Переводим элементы форм и уведомления
sed -i '
s/"required": "Required"/"required": "Обов'\''язково"/g
s/"optional": "Optional"/"optional": "Необов'\''язково"/g
s/"choose_file": "Choose File"/"choose_file": "Оберіть файл"/g
s/"no_file_chosen": "No File Chosen"/"no_file_chosen": "Файл не обрано"/g
s/"browse": "Browse"/"browse": "Переглянути"/g
s/"select": "Select"/"select": "Обрати"/g
s/"choose": "Choose"/"choose": "Вибрати"/g
s/"confirm": "Confirm"/"confirm": "Підтвердити"/g
s/"yes": "Yes"/"yes": "Так"/g
s/"no": "No"/"no": "Ні"/g
s/"ok": "OK"/"ok": "Гаразд"/g
s/"close": "Close"/"close": "Закрити"/g
s/"next": "Next"/"next": "Далі"/g
s/"previous": "Previous"/"previous": "Попередній"/g
s/"back": "Back"/"back": "Назад"/g
s/"continue": "Continue"/"continue": "Продовжити"/g
s/"finish": "Finish"/"finish": "Завершити"/g
s/"loading": "Loading"/"loading": "Завантаження"/g
s/"please_wait": "Please Wait"/"please_wait": "Будь ласка, зачекайте"/g
s/"processing": "Processing"/"processing": "Обробка"/g
s/"success": "Success"/"success": "Успішно"/g
s/"error": "Error"/"error": "Помилка"/g
s/"warning": "Warning"/"warning": "Попередження"/g
s/"info": "Info"/"info": "Інформація"/g
s/"notice": "Notice"/"notice": "Повідомлення"/g
s/"message": "Message"/"message": "Повідомлення"/g
s/"notification": "Notification"/"notification": "Сповіщення"/g
' resources/lang/uk.json

echo "🎉 Скрипт завершено! Перевірка результату..."
echo "Залишилося непереведених рядків:"
grep -c ": \"[A-Z]" resources/lang/uk.json