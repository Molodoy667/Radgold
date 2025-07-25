<?php
return [
    'name' => $_ENV['APP_NAME'] ?? 'Game Marketplace',
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => $_ENV['APP_DEBUG'] ?? false,
    'url' => $_ENV['APP_URL'] ?? 'http://localhost',
    
    'timezone' => 'Europe/Moscow',
    'locale' => 'ru',
    
    'key' => $_ENV['APP_KEY'] ?? 'your-secret-key-here',
    
    'session' => [
        'secure' => $_ENV['SESSION_SECURE'] ?? false,
        'http_only' => $_ENV['SESSION_HTTP_ONLY'] ?? true,
        'lifetime' => 120, // 2 часа
    ],
    
    'upload' => [
        'max_size' => $_ENV['UPLOAD_MAX_SIZE'] ?? 10485760, // 10MB
        'allowed_types' => explode(',', $_ENV['ALLOWED_IMAGE_TYPES'] ?? 'jpg,jpeg,png,gif,webp'),
        'path' => dirname(__DIR__, 2) . '/storage/uploads',
    ],
    
    'cache' => [
        'driver' => $_ENV['CACHE_DRIVER'] ?? 'file',
        'ttl' => $_ENV['CACHE_TTL'] ?? 3600,
    ],
    
    'logging' => [
        'level' => $_ENV['LOG_LEVEL'] ?? 'error',
        'channel' => $_ENV['LOG_CHANNEL'] ?? 'file',
        'path' => dirname(__DIR__, 2) . '/storage/logs',
    ],
    
    'mail' => [
        'host' => $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com',
        'port' => $_ENV['MAIL_PORT'] ?? 587,
        'username' => $_ENV['MAIL_USERNAME'] ?? '',
        'password' => $_ENV['MAIL_PASSWORD'] ?? '',
        'encryption' => $_ENV['MAIL_ENCRYPTION'] ?? 'tls',
    ],
];