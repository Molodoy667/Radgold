-- cp1251
CREATE DATABASE IF NOT EXISTS marketplace CHARACTER SET cp1251 COLLATE cp1251_general_ci;
USE marketplace;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    login VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    avatar VARCHAR(255),
    status ENUM('active','banned') DEFAULT 'active',
    role ENUM('user','seller','admin') DEFAULT 'user',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

INSERT INTO users (email, login, password, avatar, status, role) VALUES
('test1@mail.ru', 'test1', '$2y$10$abcdefghijklmnopqrstuv', NULL, 'active', 'user'),
('seller@mail.ru', 'seller', '$2y$10$abcdefghijklmnopqrstuv', NULL, 'active', 'seller'),
('admin@mail.ru', 'admin', '$2y$10$abcdefghijklmnopqrstuv', NULL, 'active', 'admin');

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('account','service','rent') NOT NULL,
    game VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    currency VARCHAR(10) DEFAULT 'RUB',
    images TEXT,
    status ENUM('active','sold','banned') DEFAULT 'active',
    views INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

INSERT INTO products (user_id, type, game, description, price, currency, images, status, views) VALUES
(2, 'account', 'CS:GO', 'Prime аккаунт, 100 часов', 500.00, 'RUB', '', 'active', 10),
(2, 'service', 'Dota 2', 'Буст до Immortal', 1500.00, 'RUB', '', 'active', 5),
(2, 'rent', 'GTA V', 'Аренда аккаунта на 7 дней', 300.00, 'RUB', '', 'active', 2);