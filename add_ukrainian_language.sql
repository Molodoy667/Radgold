-- Добавление украинского языка в базу данных Radgold
-- Выполните этот скрипт в вашей базе данных

INSERT INTO `languages` (`name`, `code`, `icon`, `direction`, `created_at`, `updated_at`) 
VALUES ('Українська', 'uk', 'flag-icon-ua', 'ltr', NOW(), NOW());

-- Проверка добавления
SELECT * FROM `languages` WHERE `code` = 'uk';