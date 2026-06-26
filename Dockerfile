FROM php:8.5-fpm

RUN apt-get update && apt-get install -y \
    libpq-dev \
    zip \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql zip

WORKDIR /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN cp .env.example .env

RUN composer install --no-interaction --optimize-autoloader --no-dev

EXPOSE 10000

CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 --port=10000"]
