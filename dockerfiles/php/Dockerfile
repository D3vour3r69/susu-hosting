FROM php:8.2-fpm-alpine

# Установка зависимостей (Alpine)
RUN apk update && apk add --no-cache \
    git \
    unzip \
    postgresql-dev \
    libzip-dev \
    zip \
    && docker-php-ext-install pdo pdo_pgsql zip

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Рабочая директория
WORKDIR /var/www

# Копируем ТОЛЬКО зависимости
COPY composer.json composer.lock ./

# Установка зависимостей
RUN composer install --no-scripts --no-autoloader --no-dev

# Копируем весь проект
COPY . .

# Генерируем автозагрузку
RUN composer dump-autoload --optimize

# Права для Laravel
RUN chown -R www-data:www-data /var/www/storage
RUN chmod -R 775 /var/www/storage