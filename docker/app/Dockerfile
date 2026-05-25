# Gunakan image PHP 8.3 FPM dengan Alpine Linux agar sangat ringan dan cepat
FROM php:8.3-fpm-alpine

# Tentukan direktori kerja di dalam kontainer
WORKDIR /var/www/html

# Instal dependensi sistem yang dibutuhkan untuk ekstensi PHP dan Composer
RUN apk update && apk add --no-cache \
    curl \
    git \
    unzip \
    icu-dev \
    postgresql-dev \
    libxml2-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    bash

# Konfigurasi dan instal ekstensi PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    intl \
    pdo \
    pdo_pgsql \
    pgsql \
    gd \
    zip \
    opcache \
    mbstring \
    xml

# Salin Composer resmi dari image composer terbaru
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Salin php.ini kustom ke dalam direktori konfigurasi PHP kontainer
COPY docker/app/php.ini /usr/local/etc/php/php.ini

# Atur izin direktori agar PHP-FPM (www-data) dapat menulis ke folder writable milik CodeIgniter
COPY --chown=www-data:www-data . /var/www/html

# Ekspos port 9000 untuk koneksi PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
