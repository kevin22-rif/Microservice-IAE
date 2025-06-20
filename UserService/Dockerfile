# Gunakan image Laravel Sail yang sudah dioptimalkan
FROM laravelsail/php82-composer:latest

# Instal dependensi sistem tambahan yang mungkin diperlukan oleh beberapa ekstensi
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    pkg-config \
    libmariadb-dev \
    libpq-dev \
    zip \
    unzip \
    build-essential \
    librabbitmq-dev \ 
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean

# Instal ekstensi PHP
RUN docker-php-ext-install \
    pdo pdo_mysql gd exif pcntl bcmath opcache zip sockets \
    && pecl install amqp \
    && docker-php-ext-enable amqp

# SET WORKING DIRECTORY KEMBALI KE /var/www (konsisten dengan volumes)
WORKDIR /var/www

# Salin semua kode aplikasi ke /var/www
COPY . /var/www

# Jalankan composer install
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Set permissions for storage and bootstrap/cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Expose port 9000
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
