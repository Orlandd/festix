FROM dunglas/frankenphp:php8.3

ENV SERVER_NAME=":80"

WORKDIR /app

COPY . /app

# Install zip, sqlite, gd dan dependencies
RUN apt update && apt install -y \
    zip \
    libzip-dev \
    libsqlite3-dev \
    sqlite3 \               
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-install zip pdo_sqlite gd \
    && docker-php-ext-enable zip pdo_sqlite gd

# RUN apt update && apt install -y \
#     zip \
#     libzip-dev \
#     libpng-dev \
#     libjpeg-dev \
#     libfreetype6-dev \
#     default-mysql-client \
#     && docker-php-ext-install zip pdo_mysql gd \
#     && docker-php-ext-enable zip pdo_mysql gd

# Copy Composer
COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

# Install Laravel dependencies
RUN composer install
