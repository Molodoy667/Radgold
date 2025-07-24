-- Початкові дані для AdBoard Pro
-- Версія: 2.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- Вставка основних міст України
INSERT IGNORE INTO `locations` (`name`, `slug`, `region`, `latitude`, `longitude`, `sort_order`) VALUES
('Київ', 'kyiv', 'Київська область', 50.4501, 30.5234, 1),
('Харків', 'kharkiv', 'Харківська область', 49.9935, 36.2304, 2),
('Одеса', 'odesa', 'Одеська область', 46.4825, 30.7233, 3),
('Дніпро', 'dnipro', 'Дніпропетровська область', 48.4647, 35.0462, 4),
('Донецьк', 'donetsk', 'Донецька область', 48.0159, 37.8031, 5),
('Запоріжжя', 'zaporizhzhia', 'Запорізька область', 47.8388, 35.1396, 6),
('Львів', 'lviv', 'Львівська область', 49.8397, 24.0297, 7),
('Кривий Ріг', 'kryvyi-rih', 'Дніпропетровська область', 47.9077, 33.3820, 8),
('Миколаїв', 'mykolaiv', 'Миколаївська область', 46.9659, 32.0004, 9),
('Маріуполь', 'mariupol', 'Донецька область', 47.0956, 37.5432, 10),
('Луганськ', 'luhansk', 'Луганська область', 48.5740, 39.3078, 11),
('Вінниця', 'vinnytsia', 'Вінницька область', 49.2328, 28.4810, 12),
('Херсон', 'kherson', 'Херсонська область', 46.6354, 32.6169, 13),
('Полтава', 'poltava', 'Полтавська область', 49.5938, 34.5407, 14),
('Чернігів', 'chernihiv', 'Чернігівська область', 51.5057, 31.3059, 15),
('Черкаси', 'cherkasy', 'Черкаська область', 49.4285, 32.0806, 16),
('Житомир', 'zhytomyr', 'Житомирська область', 50.2649, 28.6767, 17),
('Суми', 'sumy', 'Сумська область', 50.9077, 34.7981, 18),
('Хмельницький', 'khmelnytskyi', 'Хмельницька область', 49.4229, 26.9871, 19),
('Чернівці', 'chernivtsi', 'Чернівецька область', 48.2915, 25.9358, 20),
('Рівне', 'rivne', 'Рівненська область', 50.6199, 26.2516, 21),
('Івано-Франківськ', 'ivano-frankivsk', 'Івано-Франківська область', 48.9226, 24.7111, 22),
('Кропивницький', 'kropyvnytskyi', 'Кіровоградська область', 48.5071, 32.2691, 23),
('Тернопіль', 'ternopil', 'Тернопільська область', 49.5535, 25.5948, 24),
('Луцьк', 'lutsk', 'Волинська область', 50.7593, 25.3424, 25),
('Ужгород', 'uzhhorod', 'Закарпатська область', 48.6170, 22.2943, 26);

-- Вставка базових категорій
INSERT IGNORE INTO `categories` (`name`, `slug`, `description`, `icon`, `sort_order`, `meta_title`, `meta_description`) VALUES
('Нерухомість', 'real-estate', 'Квартири, будинки, комерційна нерухомість', 'fas fa-home', 1, 'Нерухомість - оголошення про продаж та оренду', 'Купівля, продаж та оренда квартир, будинків, комерційної нерухомості'),
('Транспорт', 'transport', 'Автомобілі, мотоцикли, запчастини', 'fas fa-car', 2, 'Транспорт - автомобілі та мотоцикли', 'Продаж автомобілів, мотоциклів, запчастин та аксесуарів'),
('Робота', 'jobs', 'Вакансії та резюме', 'fas fa-briefcase', 3, 'Робота - вакансії та резюме', 'Пошук роботи, вакансії, резюме, працевлаштування'),
('Послуги', 'services', 'Різноманітні послуги', 'fas fa-tools', 4, 'Послуги - пропозиції різних послуг', 'Послуги ремонту, будівництва, навчання, дизайну'),
('Електроніка', 'electronics', 'Телефони, комп\'ютери, техніка', 'fas fa-laptop', 5, 'Електроніка - телефони та комп\'ютери', 'Продаж телефонів, комп\'ютерів, побутової техніки'),
('Мода та одяг', 'fashion', 'Одяг, взуття, аксесуари', 'fas fa-tshirt', 6, 'Мода та одяг - стильний гардероб', 'Одяг, взуття, аксесуари для чоловіків і жінок'),
('Дитячі товари', 'kids', 'Товари для дітей', 'fas fa-baby', 7, 'Дитячі товари - все для малюків', 'Іграшки, одяг, меблі та аксесуари для дітей'),
('Інше', 'other', 'Різне', 'fas fa-ellipsis-h', 8, 'Інше - різні товари та послуги', 'Різноманітні товари та послуги які не входять в інші категорії');

-- Підкатегорії для нерухомості
INSERT IGNORE INTO `categories` (`name`, `slug`, `description`, `icon`, `parent_id`, `sort_order`) VALUES
('Квартири', 'apartments', 'Продаж та оренда квартир', 'fas fa-building', 1, 1),
('Будинки', 'houses', 'Приватні будинки та котеджі', 'fas fa-house-user', 1, 2),
('Кімнати', 'rooms', 'Оренда кімнат', 'fas fa-bed', 1, 3),
('Комерційна', 'commercial', 'Офіси, магазини, склади', 'fas fa-store', 1, 4),
('Земельні ділянки', 'land', 'Продаж земельних ділянок', 'fas fa-map', 1, 5);

-- Підкатегорії для транспорту
INSERT IGNORE INTO `categories` (`name`, `slug`, `description`, `icon`, `parent_id`, `sort_order`) VALUES
('Легкові автомобілі', 'cars', 'Легкові автомобілі', 'fas fa-car', 2, 1),
('Вантажівки', 'trucks', 'Вантажний транспорт', 'fas fa-truck', 2, 2),
('Мотоцикли', 'motorcycles', 'Мотоцикли та скутери', 'fas fa-motorcycle', 2, 3),
('Запчастини', 'auto-parts', 'Автозапчастини', 'fas fa-cog', 2, 4),
('Водний транспорт', 'boats', 'Човни, яхти, катери', 'fas fa-ship', 2, 5);

-- Підкатегорії для роботи
INSERT IGNORE INTO `categories` (`name`, `slug`, `description`, `icon`, `parent_id`, `sort_order`) VALUES
('IT та програмування', 'it-programming', 'Робота в сфері IT', 'fas fa-code', 3, 1),
('Продажі', 'sales', 'Робота в продажах', 'fas fa-handshake', 3, 2),
('Медицина', 'medicine', 'Медичні професії', 'fas fa-user-md', 3, 3),
('Освіта', 'education', 'Робота в освіті', 'fas fa-graduation-cap', 3, 4),
('Будівництво', 'construction', 'Будівельні професії', 'fas fa-hard-hat', 3, 5);

-- Підкатегорії для послуг
INSERT IGNORE INTO `categories` (`name`, `slug`, `description`, `icon`, `parent_id`, `sort_order`) VALUES
('Ремонт та будівництво', 'repair-construction', 'Ремонтні та будівельні роботи', 'fas fa-hammer', 4, 1),
('Краса та здоров\'я', 'beauty-health', 'Послуги краси та здоров\'я', 'fas fa-spa', 4, 2),
('Навчання', 'tutoring', 'Репетиторство та навчання', 'fas fa-chalkboard-teacher', 4, 3),
('Організація подій', 'events', 'Організація весіль та свят', 'fas fa-calendar-alt', 4, 4),
('Домашні послуги', 'home-services', 'Прибирання, догляд', 'fas fa-broom', 4, 5);

-- Підкатегорії для електроніки
INSERT IGNORE INTO `categories` (`name`, `slug`, `description`, `icon`, `parent_id`, `sort_order`) VALUES
('Мобільні телефони', 'mobile-phones', 'Смартфони та мобільні телефони', 'fas fa-mobile-alt', 5, 1),
('Комп\'ютери', 'computers', 'ПК, ноутбуки, комплектуючі', 'fas fa-desktop', 5, 2),
('Побутова техніка', 'appliances', 'Холодильники, пральні машини', 'fas fa-blender', 5, 3),
('Аудіо та відео', 'audio-video', 'Телевізори, навушники, колонки', 'fas fa-tv', 5, 4),
('Ігрові консолі', 'gaming', 'PlayStation, Xbox, Nintendo', 'fas fa-gamepad', 5, 5);

-- Атрибути для категорії "Легкові автомобілі"
INSERT IGNORE INTO `category_attributes` (`category_id`, `name`, `type`, `options`, `is_required`, `is_filterable`, `sort_order`) VALUES
((SELECT id FROM categories WHERE slug = 'cars'), 'Марка', 'select', 
'["Audi", "BMW", "Mercedes-Benz", "Volkswagen", "Toyota", "Honda", "Nissan", "Ford", "Chevrolet", "Hyundai", "Kia", "Mazda", "Mitsubishi", "Opel", "Peugeot", "Renault", "Skoda", "Volvo", "Lexus", "Infiniti", "Acura", "Jaguar", "Land Rover", "Porsche", "Ferrari", "Lamborghini", "Bentley", "Rolls-Royce", "Maserati", "Alfa Romeo", "Fiat", "Citroën", "Seat", "Dacia", "Lada", "GAZ", "UAZ", "ZAZ", "Bogdan", "КрАЗ", "Інше"]', 
true, true, 1),

((SELECT id FROM categories WHERE slug = 'cars'), 'Рік випуску', 'number', NULL, true, true, 2),
((SELECT id FROM categories WHERE slug = 'cars'), 'Пробіг (км)', 'number', NULL, false, true, 3),
((SELECT id FROM categories WHERE slug = 'cars'), 'Тип палива', 'select', '["Бензин", "Дизель", "Газ/Бензин", "Гібрид", "Електро"]', false, true, 4),
((SELECT id FROM categories WHERE slug = 'cars'), 'Коробка передач', 'select', '["Механічна", "Автоматична", "Варіатор", "Робот"]', false, true, 5),
((SELECT id FROM categories WHERE slug = 'cars'), 'Тип кузова', 'select', '["Седан", "Хетчбек", "Універсал", "Купе", "Кабріолет", "Позашляховик", "Кросовер", "Мінівен", "Пікап", "Ліфтбек"]', false, true, 6),
((SELECT id FROM categories WHERE slug = 'cars'), 'Об\'єм двигуна (л)', 'number', NULL, false, true, 7),
((SELECT id FROM categories WHERE slug = 'cars'), 'Колір', 'select', '["Білий", "Чорний", "Сірий", "Срібний", "Червоний", "Синій", "Зелений", "Жовтий", "Коричневий", "Помаранчевий", "Фіолетовий", "Рожевий", "Інший"]', false, false, 8),
((SELECT id FROM categories WHERE slug = 'cars'), 'Привід', 'select', '["Передній", "Задній", "Повний"]', false, true, 9),
((SELECT id FROM categories WHERE slug = 'cars'), 'Технічний стан', 'select', '["Відмінний", "Хороший", "Задовільний", "Потребує ремонту", "Не на ходу"]', false, true, 10);

-- Атрибути для категорії "Квартири"
INSERT IGNORE INTO `category_attributes` (`category_id`, `name`, `type`, `options`, `is_required`, `is_filterable`, `sort_order`) VALUES
((SELECT id FROM categories WHERE slug = 'apartments'), 'Кількість кімнат', 'select', '["1", "2", "3", "4", "5", "6+"]', true, true, 1),
((SELECT id FROM categories WHERE slug = 'apartments'), 'Загальна площа (м²)', 'number', NULL, false, true, 2),
((SELECT id FROM categories WHERE slug = 'apartments'), 'Житлова площа (м²)', 'number', NULL, false, true, 3),
((SELECT id FROM categories WHERE slug = 'apartments'), 'Площа кухні (м²)', 'number', NULL, false, false, 4),
((SELECT id FROM categories WHERE slug = 'apartments'), 'Поверх', 'number', NULL, false, true, 5),
((SELECT id FROM categories WHERE slug = 'apartments'), 'Поверховість будинку', 'number', NULL, false, true, 6),
((SELECT id FROM categories WHERE slug = 'apartments'), 'Тип будинку', 'select', '["Панельний", "Цегляний", "Монолітний", "Сталінка", "Хрущовка", "Новобудова", "Елітний"]', false, true, 7),
((SELECT id FROM categories WHERE slug = 'apartments'), 'Стан квартири', 'select', '["Євроремонт", "Хороший", "Косметичний ремонт", "Після будівельників", "Потребує ремонту"]', false, true, 8),
((SELECT id FROM categories WHERE slug = 'apartments'), 'Меблі', 'select', '["Повністю мебльована", "Частково мебльована", "Без меблів"]', false, false, 9),
((SELECT id FROM categories WHERE slug = 'apartments'), 'Санвузол', 'select', '["Суміжний", "Роздільний", "2 санвузли", "3+ санвузли"]', false, false, 10);

-- Атрибути для категорії "Мобільні телефони"
INSERT IGNORE INTO `category_attributes` (`category_id`, `name`, `type`, `options`, `is_required`, `is_filterable`, `sort_order`) VALUES
((SELECT id FROM categories WHERE slug = 'mobile-phones'), 'Бренд', 'select', 
'["Apple", "Samsung", "Xiaomi", "Huawei", "OnePlus", "Google", "Sony", "LG", "Motorola", "Nokia", "Honor", "Realme", "Oppo", "Vivo", "Meizu", "Asus", "Blackberry", "Інший"]', 
true, true, 1),
((SELECT id FROM categories WHERE slug = 'mobile-phones'), 'Модель', 'text', NULL, false, true, 2),
((SELECT id FROM categories WHERE slug = 'mobile-phones'), 'Пам\'ять (ГБ)', 'select', '["16", "32", "64", "128", "256", "512", "1024"]', false, true, 3),
((SELECT id FROM categories WHERE slug = 'mobile-phones'), 'Операційна система', 'select', '["iOS", "Android", "Windows", "Інша"]', false, true, 4),
((SELECT id FROM categories WHERE slug = 'mobile-phones'), 'Стан', 'select', '["Новий", "Як новий", "Добрий", "Задовільний", "Поганий"]', false, true, 5),
((SELECT id FROM categories WHERE slug = 'mobile-phones'), 'Колір', 'select', '["Чорний", "Білий", "Сірий", "Золотий", "Рожевий", "Синій", "Червоний", "Зелений", "Фіолетовий", "Інший"]', false, false, 6);

-- Вставка базових платних послуг
INSERT IGNORE INTO `paid_services` (`name`, `description`, `price`, `duration_days`, `service_type`, `sort_order`) VALUES
('Виділити оголошення', 'Ваше оголошення буде виділено спеціальним кольором та рамкою', 50.00, 7, 'highlight', 1),
('Закріпити в топі', 'Оголошення з\'явиться в верхній частині списку', 100.00, 3, 'top', 2),
('Термінове оголошення', 'Позначка "Термінове" та спеціальна іконка', 30.00, 3, 'urgent', 3),
('Рекомендоване', 'Показ в блоці рекомендованих оголошень на головній', 150.00, 7, 'featured', 4),
('Автопродовження', 'Автоматичне оновлення дати публікації', 25.00, 30, 'republish', 5),
('Турбо-просування', 'Максимальна видимість: топ + виділення + термінове', 200.00, 7, 'boost', 6);

-- Вставка груп користувачів
INSERT IGNORE INTO `user_groups` (`name`, `description`, `permissions`, `color`, `sort_order`) VALUES
('Адміністратори', 'Повний доступ до всіх функцій системи', '["admin.full_access","users.manage","ads.manage","settings.manage","groups.manage","categories.manage","locations.manage","payments.manage","stats.view","logs.view","backups.manage"]', '#dc3545', 1),
('Модератори', 'Модерація контенту та користувачів', '["ads.moderate","users.moderate","comments.moderate","reports.view","stats.view"]', '#fd7e14', 2),
('Партнери', 'Розширені можливості для бізнес-користувачів', '["ads.create_unlimited","ads.featured","stats.own","analytics.basic"]', '#6f42c1', 3),
('Користувачі', 'Стандартні користувачі системи', '["ads.create","ads.edit_own","ads.delete_own","profile.edit","messages.send"]', '#28a745', 4),
('VIP Користувачі', 'Преміум користувачі з додатковими можливостями', '["ads.create_unlimited","ads.priority","ads.featured_discount","support.priority"]', '#ffc107', 5);

-- Початкові сповіщення для тесту (можна видалити після установки)
-- INSERT INTO `notifications` (`user_id`, `type`, `title`, `message`, `priority`) VALUES
-- (1, 'welcome', 'Ласкаво просимо!', 'Дякуємо за реєстрацію в AdBoard Pro. Почніть з створення вашого першого оголошення.', 'normal');

COMMIT;