FROM php:8.1-cli-alpine

RUN apk add --no-cache \
    libzip-dev \
    zip \
    unzip \
    mysql-client \
    icu-dev \
    && docker-php-ext-install pdo_mysql zip intl pcntl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json ./
RUN composer install --no-dev --no-scripts --no-autoloader

COPY . .
RUN composer dump-autoload --optimize

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
