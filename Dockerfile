# FROM dunglas/frankenphp:php8.3

# ENV SERVER_NAME=":80"

# WORKDIR /app

# COPY . /app

# # Install zip, sqlite, gd dan dependencies
# RUN apt update && apt install -y \
#     zip \
#     libzip-dev \
#     libsqlite3-dev \
#     sqlite3 \               
#     libpng-dev \
#     libjpeg-dev \
#     libfreetype6-dev \
#     && docker-php-ext-install zip pdo_sqlite gd \
#     && docker-php-ext-enable zip pdo_sqlite gd

# # RUN apt update && apt install -y \
# #     zip \
# #     libzip-dev \
# #     libpng-dev \
# #     libjpeg-dev \
# #     libfreetype6-dev \
# #     default-mysql-client \
# #     && docker-php-ext-install zip pdo_mysql gd \
# #     && docker-php-ext-enable zip pdo_mysql gd

# # Copy Composer
# COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

# # Install Laravel dependencies
# RUN composer install

# FROM dunglas/frankenphp:php8.3

# ENV SERVER_NAME=":80"
# WORKDIR /var/www

# COPY . /var/www

# RUN apt update && apt install -y \
#     zip \
#     sqlite3 \
#     libzip-dev \
#     libsqlite3-dev \
#     libpng-dev \
#     libjpeg-dev \
#     libfreetype6-dev \
#     libonig-dev \
#     libxml2-dev \
#     nginx \
#     supervisor \
#     && docker-php-ext-configure gd --with-freetype --with-jpeg \
#     && docker-php-ext-install pdo_sqlite mbstring exif pcntl bcmath gd zip

# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# RUN composer install --no-dev --no-interaction --optimize-autoloader

# RUN php artisan storage:link || true

# COPY docker-compose/nginx/default.conf /etc/nginx/conf.d/default.conf
# COPY docker-compose/supervisord.conf /etc/supervisord.conf

# RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# EXPOSE 80

# CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]


# ========== Build Stage ==========
FROM php:8.2-fpm AS builder

# Install build dependencies
RUN apt update && apt install -y \
    zip \
    sqlite3 \
    libzip-dev \
    libsqlite3-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    unzip \
    curl \
    && apt clean && rm -rf /var/lib/apt/lists/*

# Install PHP Extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_sqlite mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy Laravel project files (seluruh isi folder saat ini)
COPY . /var/www

# Install Composer dependencies
RUN composer install --no-ansi --no-dev --no-interaction --no-plugins --no-progress --no-scripts --optimize-autoloader

# ========== Runtime Stage ==========
FROM php:8.2-fpm-alpine

# Install runtime dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    sqlite \
    libpng \
    libjpeg-turbo \
    freetype \
    libxml2 \
    libzip \
    oniguruma \
    && apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libxml2-dev \
    libzip-dev \
    oniguruma-dev \
    sqlite-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_sqlite mbstring exif pcntl bcmath gd zip \
    && apk del .build-deps

# Set working directory
WORKDIR /var/www

# Copy built app and composer
COPY --from=builder /var/www /var/www
COPY --from=builder /usr/bin/composer /usr/bin/composer

# Copy nginx and supervisor configs
COPY docker-compose/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker-compose/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY docker-compose/supervisord.conf /etc/supervisord.conf

# Ensure SQLite file exists
RUN mkdir -p /var/www/database && touch /var/www/database/database.sqlite

# Generate storage link and set permissions
RUN php artisan storage:link || true \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/vendor /var/www/database

# Expose port
EXPOSE 80

# Start services
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]



# FROM dunglas/frankenphp:latest

# # Set working directory
# WORKDIR /app

# # Install system dependencies
# RUN apt update && apt install -y \
#     sqlite3 \
#     libsqlite3-dev \
#     unzip \
#     git \
#     libpng-dev \
#     libjpeg-dev \
#     libfreetype6-dev \
#     libzip-dev \
#     libonig-dev \
#     libxml2-dev \
#     && rm -rf /var/lib/apt/lists/*

# # Install PHP extensions required by Laravel
# RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
#     && docker-php-ext-install \
#     pdo \
#     pdo_sqlite \
#     mbstring \
#     bcmath \
#     exif \
#     zip \
#     gd

# # Copy all application files
# COPY . .

# # Install Composer
# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# # Install Laravel dependencies
# RUN composer install --no-dev --optimize-autoloader

# # Laravel setup
# RUN php artisan storage:link \
#     && mkdir -p database && touch database/database.sqlite \
#     && chown -R www-data:www-data /app

# # Copy FrankenPHP config
# COPY frankenphp.yaml /etc/frankenphp.yaml

# # Expose HTTP port
# EXPOSE 80
