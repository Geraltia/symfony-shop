FROM php:8.2-fpm

# Установка зависимостей для pdo_pgsql и xml
RUN apt-get update && apt-get install -y libpq-dev git zip libxml2-dev \
    && docker-php-ext-install pdo pdo_pgsql dom xml xmlwriter

# Установка Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
