FROM php:8.2-fpm-alpine

# Установка зависимостей и расширений PHP
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql zip

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Рабочая директория
WORKDIR /var/www

# Копируем зависимости
COPY src/composer.json src/composer.lock ./
RUN composer install --no-scripts --no-autoloader

# Копируем весь проект
COPY src/ .

# Генерируем автозагрузку и оптимизируем
RUN composer dump-autoload --optimize

# Права для Laravel
RUN chown -R www-data:www-data /var/www/storage
RUN chmod -R 775 /var/www/storage