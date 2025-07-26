<?php

return [
    'app_name' => 'GameMarket Pro',
    'app_url' => 'https://radgold.online',
    'debug' => true,
    'timezone' => 'Europe/Moscow',
    
    // Настройки безопасности
    'security' => [
        'password_min_length' => 6,
        'session_lifetime' => 86400, // 24 часа
        'max_login_attempts' => 5,
        'csrf_token_lifetime' => 3600,
    ],
    
    // Настройки файлов
    'upload' => [
        'max_file_size' => 5 * 1024 * 1024, // 5MB
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'webp'],
        'upload_path' => 'storage/uploads/',
    ],
    
    // Настройки пагинации
    'pagination' => [
        'per_page' => 20,
        'max_per_page' => 100,
    ],
    
    // Настройки комиссии
    'commission' => [
        'default_rate' => 0.05, // 5%
        'premium_rate' => 0.03,  // 3% для премиум пользователей
    ],
    
    // Настройки валют
    'currencies' => [
        'RUB' => '₽',
        'USD' => '$',
        'EUR' => '€',
    ],
    
    // Типы товаров
    'product_types' => [
        'account' => 'Игровой аккаунт',
        'service' => 'Игровая услуга',
        'boost' => 'Бустинг',
        'rent' => 'Аренда аккаунта',
        'farm' => 'Фарм ресурсов',
        'coaching' => 'Обучение',
    ],
    
    // Популярные игры
    'games' => [
        'valorant' => 'Valorant',
        'csgo' => 'CS:GO',
        'dota2' => 'Dota 2',
        'lol' => 'League of Legends',
        'wow' => 'World of Warcraft',
        'genshin' => 'Genshin Impact',
        'apex' => 'Apex Legends',
        'overwatch' => 'Overwatch 2',
        'pubg' => 'PUBG',
        'fortnite' => 'Fortnite',
    ],
];