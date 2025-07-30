#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import json
import re

def get_mass_translations():
    """Массовый словарь переводов реальных терминов из файла"""
    return {
        # Navigation and listing actions
        "over": "ПОНАД",
        "live_ads": "ЖИВІ ОГОЛОШЕННЯ",
        "newest_ads": "Найновіші оголошення",
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
        
        # Report and feedback
        "create_ad_report_category": "Створити категорію скарг на оголошення",
        "report_for_ad": "Поскаржитися на це оголошення",
        "report_description": "Опис скарги",
        "suspicious_ad": "Підозріле оголошення",
        "fake_ad": "Підробне оголошення",
        "duplicate_ad": "Дублікат оголошення",
        "wrong_category": "Неправильна категорія",
        "spam_ad": "Спам оголошення",
        "inappropriate_content": "Неприйнятний контент",
        "offensive_language": "Образлива мова",
        "scam_or_fraud": "Шахрайство або обман",
        "copyright_violation": "Порушення авторських прав",
        "other_reason": "Інша причина",
        
        # Time periods
        "last_7_days": "Останні 7 днів",
        "last_30_days": "Останні 30 днів",
        "last_week": "Минулий тиждень",
        "last_month": "Минулий місяць",
        "this_week": "Цього тижня",
        "this_month": "Цього місяця",
        "today": "Сьогодні",
        "yesterday": "Вчора",
        "tomorrow": "Завтра",
        "now": "Зараз",
        "soon": "Скоро",
        "later": "Пізніше",
        "never": "Ніколи",
        "always": "Завжди",
        "sometimes": "Іноді",
        "often": "Часто",
        "rarely": "Рідко",
        
        # Status and conditions
        "new": "Новий",
        "used": "Вживаний",
        "refurbished": "Відновлений",
        "damaged": "Пошкоджений",
        "broken": "Зламаний",
        "working": "Працює",
        "not_working": "Не працює",
        "excellent_condition": "Відмінний стан",
        "good_condition": "Добрий стан",
        "fair_condition": "Задовільний стан",
        "poor_condition": "Поганий стан",
        "like_new": "Як новий",
        "mint_condition": "Ідеальний стан",
        
        # Listing management
        "my_listings": "Мої оголошення",
        "active_listings": "Активні оголошення",
        "inactive_listings": "Неактивні оголошення",
        "expired_listings": "Прострочені оголошення",
        "draft_listings": "Чернетки оголошень",
        "sold_listings": "Продані оголошення",
        "pending_listings": "Оголошення в очікуванні",
        "featured_listings": "Рекомендовані оголошення",
        "promoted_listings": "Рекламовані оголошення",
        "urgent_listings": "Термінові оголошення",
        "highlighted_listings": "Виділені оголошення",
        "top_listings": "Топ оголошення",
        
        # Search and filter
        "search_results": "Результати пошуку",
        "no_results_found": "Результатів не знайдено",
        "refine_search": "Уточнити пошук",
        "advanced_search": "Розширений пошук",
        "search_by_keyword": "Пошук за ключовим словом",
        "search_by_category": "Пошук за категорією",
        "search_by_location": "Пошук за місцезнаходженням",
        "search_by_price": "Пошук за ціною",
        "filter_by": "Фільтрувати за",
        "sort_by": "Сортувати за",
        "price_low_to_high": "Ціна від низької до високої",
        "price_high_to_low": "Ціна від високої до низької",
        "newest_first": "Спочатку нові",
        "oldest_first": "Спочатку старі",
        "most_relevant": "Найбільш релевантні",
        "most_popular": "Найпопулярніші",
        "distance": "Відстань",
        "alphabetical": "За алфавітом",
        
        # Contact and communication
        "contact_seller": "Зв'язатися з продавцем",
        "send_message": "Надіслати повідомлення",
        "call_now": "Подзвонити зараз",
        "email_seller": "Написати продавцю",
        "show_phone_number": "Показати номер телефону",
        "hide_phone_number": "Приховати номер телефону",
        "chat_with_seller": "Чат з продавцем",
        "make_an_offer": "Зробити пропозицію",
        "negotiate_price": "Домовитися про ціну",
        "ask_question": "Поставити питання",
        "request_more_info": "Запросити більше інформації",
        "schedule_viewing": "Запланувати перегляд",
        "arrange_meeting": "Домовитися про зустріч",
        
        # Pricing and payments
        "fixed_price": "Фіксована ціна",
        "negotiable_price": "Договірна ціна",
        "best_offer": "Найкраща пропозиція",
        "price_on_request": "Ціна за запитом",
        "free": "Безкоштовно",
        "swap_exchange": "Обмін",
        "trade_in": "Трейд-ін",
        "cash_only": "Тільки готівка",
        "card_payment": "Оплата картою",
        "bank_transfer": "Банківський переказ",
        "online_payment": "Онлайн оплата",
        "installments": "Розстрочка",
        "financing_available": "Фінансування доступне",
        
        # Location and delivery
        "pickup_only": "Тільки самовивіз",
        "delivery_available": "Доставка доступна",
        "free_delivery": "Безкоштовна доставка",
        "local_delivery": "Місцева доставка",
        "nationwide_delivery": "Доставка по всій країні",
        "international_shipping": "Міжнародна доставка",
        "express_delivery": "Експрес доставка",
        "same_day_delivery": "Доставка в той же день",
        "next_day_delivery": "Доставка наступного дня",
        "standard_delivery": "Стандартна доставка",
        "courier_delivery": "Кур'єрська доставка",
        "post_delivery": "Поштова доставка",
        
        # Safety and trust
        "verified_seller": "Верифікований продавець",
        "trusted_seller": "Надійний продавець",
        "new_seller": "Новий продавець",
        "power_seller": "Потужний продавець",
        "business_seller": "Бізнес продавець",
        "private_seller": "Приватний продавець",
        "safety_tips": "Поради безпеки",
        "meet_in_public": "Зустрічайтеся в публічних місцях",
        "inspect_before_buying": "Перевіряйте перед покупкою",
        "beware_of_scams": "Остерігайтеся шахрайства",
        "report_suspicious": "Повідомити про підозріле",
        "secure_payment": "Безпечна оплата",
        "buyer_protection": "Захист покупця",
        "seller_protection": "Захист продавця",
        "money_back_guarantee": "Гарантія повернення грошей",
        
        # User interface
        "view_details": "Переглянути деталі",
        "more_details": "Більше деталей",
        "full_description": "Повний опис",
        "show_more": "Показати більше",
        "show_less": "Показати менше",
        "expand": "Розгорнути",
        "collapse": "Згорнути",
        "zoom_in": "Збільшити",
        "zoom_out": "Зменшити",
        "full_screen": "Повний екран",
        "slideshow": "Слайд-шоу",
        "gallery_view": "Вигляд галереї",
        "list_view": "Вигляд списку",
        "grid_view": "Вигляд сітки",
        "map_view": "Вигляд карти",
        
        # Actions and buttons
        "add_to_favorites": "Додати в обране",
        "remove_from_favorites": "Видалити з обраного",
        "add_to_watchlist": "Додати до списку спостереження",
        "add_to_cart": "Додати в кошик",
        "buy_now": "Купити зараз",
        "reserve_now": "Зарезервувати зараз",
        "book_now": "Забронювати зараз",
        "inquire_now": "Запитати зараз",
        "get_quote": "Отримати цитату",
        "request_callback": "Запросити зворотний дзвінок",
        "subscribe_to_alerts": "Підписатися на сповіщення",
        "save_search": "Зберегти пошук",
        "share_listing": "Поділитися оголошенням",
        "print_listing": "Роздрукувати оголошення",
        "report_listing": "Поскаржитися на оголошення",
        "flag_inappropriate": "Позначити як неприйнятне",
        
        # Membership and plans
        "upgrade_membership": "Оновити членство",
        "choose_plan": "Оберіть план",
        "compare_plans": "Порівняти плани",
        "current_plan": "Поточний план",
        "recommended_plan": "Рекомендований план",
        "most_popular": "Найпопулярніший",
        "best_value": "Найкраща вартість",
        "limited_time_offer": "Обмежена пропозиція за часом",
        "special_discount": "Спеціальна знижка",
        "first_month_free": "Перший місяць безкоштовно",
        "annual_discount": "Річна знижка",
        "no_commitment": "Без зобов'язань",
        "cancel_anytime": "Скасувати в будь-який час",
        
        # Notifications and messages
        "notification_settings": "Налаштування сповіщень",
        "email_notifications": "Email сповіщення",
        "sms_notifications": "SMS сповіщення",
        "push_notifications": "Push сповіщення",
        "new_message": "Нове повідомлення",
        "unread_messages": "Непрочитані повідомлення",
        "mark_as_read": "Позначити як прочитане",
        "mark_as_unread": "Позначити як непрочитане",
        "delete_message": "Видалити повідомлення",
        "reply": "Відповісти",
        "forward": "Переслати",
        "compose_message": "Написати повідомлення",
        "inbox": "Вхідні",
        "outbox": "Вихідні",
        "sent_messages": "Надіслані повідомлення",
        "draft_messages": "Чернетки повідомлень",
        "archived_messages": "Архівовані повідомлення",
        "spam_messages": "Спам повідомлення",
        "blocked_users": "Заблоковані користувачі",
        
        # Help and support
        "help_center": "Центр допомоги",
        "customer_support": "Підтримка клієнтів",
        "contact_us": "Зв'язатися з нами",
        "frequently_asked_questions": "Часті питання",
        "user_guide": "Посібник користувача",
        "video_tutorials": "Відео уроки",
        "knowledge_base": "База знань",
        "troubleshooting": "Усунення неполадок",
        "technical_support": "Технічна підтримка",
        "billing_support": "Підтримка з рахунків",
        "report_bug": "Повідомити про помилку",
        "suggest_feature": "Запропонувати функцію",
        "leave_feedback": "Залишити відгук",
        "rate_our_service": "Оцініть наш сервіс",
        "satisfaction_survey": "Опитування задоволеності",
        
        # Legal and policies
        "terms_of_service": "Умови обслуговування",
        "privacy_policy": "Політика конфіденційності",
        "cookie_policy": "Політика файлів cookie",
        "disclaimer": "Відмова від відповідальності",
        "copyright_notice": "Повідомлення про авторські права",
        "intellectual_property": "Інтелектуальна власність",
        "user_agreement": "Угода користувача",
        "acceptable_use": "Прийнятне використання",
        "prohibited_content": "Заборонений контент",
        "community_guidelines": "Правила спільноти",
        "code_of_conduct": "Кодекс поведінки",
        "dispute_resolution": "Вирішення спорів",
        "limitation_of_liability": "Обмеження відповідальності",
        "indemnification": "Відшкодування",
        "governing_law": "Застосовне право"
    }

def apply_mass_translations():
    """Применяет массовые переводы к файлу"""
    
    # Загружаем файл
    with open('resources/lang/uk.json', 'r', encoding='utf-8') as f:
        data = json.load(f)
    
    translations = get_mass_translations()
    changes_made = 0
    
    print(f"Applying {len(translations)} potential translations...")
    
    # Применяем переводы
    for key, value in data.items():
        if isinstance(value, str) and value in translations:
            data[key] = translations[value]
            changes_made += 1
            print(f"Translated: {key}: '{value}' -> '{translations[value]}'")
    
    # Сохраняем файл
    with open('resources/lang/uk.json', 'w', encoding='utf-8') as f:
        json.dump(data, f, ensure_ascii=False, indent=4)
    
    print(f"\n✅ Mass translation completed! Made {changes_made} changes.")
    return changes_made

if __name__ == "__main__":
    apply_mass_translations()