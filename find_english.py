#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import json
import re

def find_english_terms():
    """Находит все английские термины в файле uk.json"""
    
    with open('resources/lang/uk.json', 'r', encoding='utf-8') as f:
        data = json.load(f)
    
    english_terms = []
    
    for key, value in data.items():
        if isinstance(value, str):
            # Проверяем если строка содержит английские буквы
            if re.search(r'[A-Za-z]', value):
                # Исключаем украинские строки с английскими вкраплениями (SEO, FAQ, etc.)
                # Проверяем если строка ПРЕИМУЩЕСТВЕННО английская
                english_chars = len(re.findall(r'[A-Za-z]', value))
                total_chars = len(re.findall(r'[A-Za-zА-Яа-яІіЇїЄєҐґ]', value))
                
                if total_chars > 0 and english_chars / total_chars > 0.7:
                    english_terms.append((key, value))
    
    return english_terms

def create_translation_mapping(english_terms):
    """Создает маппинг для перевода английских терминов"""
    
    # Словарь для часто встречающихся английских терминов
    common_translations = {
        # Website sections
        "over": "ПОНАД",
        "live_ads": "ЖИВІ ОГОЛОШЕННЯ", 
        "popular_category": "Популярна категорія",
        "newest": "Найновіший",
        "choose_pricing_plan": "Оберіть тарифний план",
        "download_our_app": "Завантажте наш додаток",
        "design_by": "Дизайн від",
        "supports": "Підтримує",
        "quick_links": "Швидкі посилання",
        "about_us": "Про нас",
        "know_more_about_adlisting": "Дізнатися більше про розміщення оголошень",
        "what_people_says": "Що кажуть люди",
        "supported_by": "Підтримується",
        
        # Account and profile
        "account_information": "Інформація про акаунт",
        "about_seller": "Про продавця",
        "website_links": "Посилання веб-сайтів",
        "delete_account": "Видалити акаунт",
        "account_setting": "Налаштування акаунту",
        "posted_ads": "Розміщені оголошення",
        "posted_within": "Розміщено протягом",
        "last_7_days": "Останні 7 днів",
        "last_30_days": "Останні 30 днів",
        "promoted_ads": "Рекламовані оголошення",
        "my_ads": "Мої оголошення",
        "favorite_ads": "Улюблені оголошення",
        "messages": "Повідомлення",
        "my_listing": "Мої оголошення",
        
        # Navigation and actions
        "browse_all": "Переглянути все",
        "view_all_category": "Переглянути всі категорії",
        "send_message": "Надіслати повідомлення",
        "report_this_ad": "Поскаржитися на це оголошення",
        "mark_as_favorite": "Позначити як улюблене",
        "share_this_ad": "Поділитися цим оголошенням",
        "view_phone": "Переглянути телефон",
        "view_email": "Переглянути email",
        "contact_seller": "Зв'язатися з продавцем",
        "related_ads": "Схожі оголошення",
        "similar_ads": "Подібні оголошення",
        "other_ads_by": "Інші оголошення від",
        "more_ads_by": "Більше оголошень від",
        
        # Filters and search
        "all_category": "Всі категорії",
        "all_location": "Всі локації",
        "all_condition": "Всі стани",
        "all_price_range": "Всі цінові діапазони",
        "min_price": "Мінімальна ціна",
        "max_price": "Максимальна ціна",
        "price_range": "Ціновий діапазон",
        "condition": "Стан",
        "brand_new": "Новий",
        "like_new": "Як новий",
        "excellent": "Відмінний",
        "good": "Добрий",
        "fair": "Задовільний",
        "poor": "Поганий",
        "salvage": "Після аварії",
        
        # Listing details
        "ad_details": "Деталі оголошення",
        "listing_details": "Деталі оголошення",
        "product_description": "Опис товару",
        "seller_information": "Інформація про продавця",
        "contact_information": "Контактна інформація",
        "location_details": "Деталі розташування",
        "safety_tips": "Поради безпеки",
        "delivery_options": "Варіанти доставки",
        "payment_methods": "Способи оплати",
        "return_policy": "Політика повернення",
        "warranty_information": "Інформація про гарантію",
        
        # Time and dates
        "posted_on": "Розміщено",
        "updated_on": "Оновлено",
        "expires_on": "Закінчується",
        "just_now": "Щойно",
        "minutes_ago": "хвилин тому",
        "hours_ago": "годин тому",
        "days_ago": "днів тому",
        "weeks_ago": "тижнів тому",
        "months_ago": "місяців тому",
        "years_ago": "років тому",
        
        # Status and states
        "available": "Доступний",
        "sold": "Продано",
        "reserved": "Зарезервовано",
        "pending": "В очікуванні",
        "expired": "Прострочений",
        "under_review": "На розгляді",
        "approved": "Затверджено",
        "rejected": "Відхилено",
        "featured": "Рекомендоване",
        "promoted": "Рекламоване",
        "urgent": "Терміново",
        "hot_deal": "Гаряча пропозиція",
        "price_reduced": "Ціна знижена",
        "negotiable": "Договірна",
        "fixed_price": "Фіксована ціна",
        
        # Common actions
        "edit": "Редагувати",
        "delete": "Видалити",
        "update": "Оновити",
        "save": "Зберегти",
        "cancel": "Скасувати",
        "submit": "Надіслати",
        "apply": "Застосувати",
        "reset": "Скинути",
        "clear": "Очистити",
        "refresh": "Оновити",
        "reload": "Перезавантажити",
        "print": "Друкувати",
        "share": "Поділитися",
        "copy": "Копіювати",
        "paste": "Вставити",
        "cut": "Вирізати",
        "undo": "Скасувати",
        "redo": "Повторити",
        
        # Form elements
        "required": "Обов'язкове",
        "optional": "Необов'язкове",
        "select": "Вибрати",
        "choose": "Обрати",
        "upload": "Завантажити",
        "browse": "Переглянути",
        "preview": "Попередній перегляд",
        "download": "Завантажити",
        "remove": "Видалити",
        "add": "Додати",
        "insert": "Вставити",
        "attach": "Прикріпити",
        "detach": "Від'єднати",
        
        # Messages and alerts
        "success": "Успіх",
        "error": "Помилка",
        "warning": "Попередження",
        "info": "Інформація",
        "notice": "Повідомлення",
        "alert": "Сповіщення",
        "confirmation": "Підтвердження",
        "validation": "Перевірка",
        "loading": "Завантаження",
        "processing": "Обробка",
        "please_wait": "Будь ласка, зачекайте",
        "try_again": "Спробуйте знову",
        "contact_support": "Зв'язатися з підтримкою",
        
        # Business and finance
        "business": "Бізнес",
        "finance": "Фінанси",
        "investment": "Інвестиції",
        "profit": "Прибуток",
        "revenue": "Дохід",
        "expense": "Витрати",
        "budget": "Бюджет",
        "cost": "Вартість",
        "price": "Ціна",
        "discount": "Знижка",
        "sale": "Розпродаж",
        "offer": "Пропозиція",
        "deal": "Угода",
        "contract": "Контракт",
        "agreement": "Угода",
        "terms": "Умови",
        "conditions": "Умови",
        "policy": "Політика",
        "rules": "Правила",
        "guidelines": "Керівні принципи",
        "procedures": "Процедури",
        "process": "Процес",
        "workflow": "Робочий процес",
        "management": "Управління",
        "administration": "Адміністрування",
        "operation": "Операція",
        "service": "Послуга",
        "support": "Підтримка",
        "assistance": "Допомога",
        "help": "Допомога",
        "guide": "Посібник",
        "tutorial": "Урок",
        "training": "Навчання",
        "education": "Освіта",
        "learning": "Навчання",
        "development": "Розробка",
        "improvement": "Покращення",
        "enhancement": "Удосконалення",
        "optimization": "Оптимізація",
        "performance": "Продуктивність",
        "efficiency": "Ефективність",
        "quality": "Якість",
        "standard": "Стандарт",
        "premium": "Преміум",
        "professional": "Професійний",
        "enterprise": "Корпоративний",
        "advanced": "Розширений",
        "basic": "Базовий",
        "simple": "Простий",
        "complex": "Складний",
        "custom": "Власний",
        "default": "За замовчуванням"
    }
    
    return common_translations

def apply_translations():
    """Применяет переводы к файлу"""
    
    # Загружаем файл
    with open('resources/lang/uk.json', 'r', encoding='utf-8') as f:
        data = json.load(f)
    
    english_terms = find_english_terms()
    translations = create_translation_mapping(english_terms)
    changes_made = 0
    
    print(f"Found {len(english_terms)} English terms to translate")
    print("Applying translations...")
    
    # Применяем переводы
    for key, value in data.items():
        if isinstance(value, str) and value in translations:
            data[key] = translations[value]
            changes_made += 1
            print(f"Translated: {key}: '{value}' -> '{translations[value]}'")
    
    # Сохраняем файл
    with open('resources/lang/uk.json', 'w', encoding='utf-8') as f:
        json.dump(data, f, ensure_ascii=False, indent=4)
    
    print(f"\n✅ Translation completed! Made {changes_made} changes.")
    
    # Показываем несколько примеров непереведенных терминов
    remaining_english = [term for key, term in english_terms if key in data and data[key] == term]
    if remaining_english:
        print(f"\nRemaining English terms ({len(remaining_english)}):")
        for i, term in enumerate(remaining_english[:10]):
            print(f"{i+1}. {term}")
        if len(remaining_english) > 10:
            print(f"... and {len(remaining_english) - 10} more")
    
    return changes_made

if __name__ == "__main__":
    apply_translations()