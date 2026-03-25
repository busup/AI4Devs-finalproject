# =============================================================
# ms-router — Dockerfile
# PHP 8.3 CLI + Composer + necessary extensions for Laravel 11
# =============================================================
FROM php:8.3-cli

# ------------------------------------------------------------------
# System dependencies
# ------------------------------------------------------------------
RUN apt-get update && apt-get install -y --no-install-recommends \
        curl \
        git \
        unzip \
        libzip-dev \
        libonig-dev \
        libxml2-dev \
        default-mysql-client \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mbstring \
        zip \
        bcmath \
        xml \
    && rm -rf /var/lib/apt/lists/*

# ------------------------------------------------------------------
# Composer
# ------------------------------------------------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ------------------------------------------------------------------
# Working directory
# ------------------------------------------------------------------
WORKDIR /var/www/html

# ------------------------------------------------------------------
# Install PHP dependencies
# (composer.json + composer.lock copied first for layer caching)
# ------------------------------------------------------------------
COPY composer.json composer.lock* ./
RUN composer install \
        --no-scripts \
        --no-autoloader \
        --prefer-dist \
        --no-interaction

# ------------------------------------------------------------------
# Copy the rest of the application source
# ------------------------------------------------------------------
COPY . .

# Generate optimized autoloader
RUN composer dump-autoload --optimize

# ------------------------------------------------------------------
# Storage permissions (bootstrap/cache & storage/logs)
# ------------------------------------------------------------------
RUN mkdir -p storage/framework/{sessions,views,cache} \
             storage/logs \
             bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# ------------------------------------------------------------------
# Entrypoint: generate key if missing, then serve
# ------------------------------------------------------------------
EXPOSE 8000

CMD ["/bin/sh", "-c", "php artisan key:generate --no-interaction --quiet 2>/dev/null || true && php artisan serve --host=0.0.0.0 --port=8000"]
