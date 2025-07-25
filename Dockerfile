# Game Marketplace Dockerfile
FROM php:8.1-apache

# Устанавливаем системные зависимости
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Устанавливаем PHP расширения
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    gd \
    pdo \
    pdo_mysql \
    zip \
    mbstring \
    opcache

# Включаем Apache модули
RUN a2enmod rewrite headers

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Устанавливаем рабочую директорию
WORKDIR /var/www/html

# Копируем файлы проекта
COPY . .

# Устанавливаем зависимости Composer
RUN composer install --no-dev --optimize-autoloader

# Создаем директории для storage
RUN mkdir -p storage/{uploads,logs,cache} \
    && chmod -R 755 storage

# Настраиваем права доступа
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Копируем конфигурацию Apache
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Копируем конфигурацию PHP
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Создаем health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/health || exit 1

# Открываем порт
EXPOSE 80

# Запускаем Apache
CMD ["apache2-foreground"]