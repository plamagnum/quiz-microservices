# Використовуємо офіційний образ PHP 8.2-FPM
FROM php:8.2-fpm

# Встановлюємо системні залежності для pecl та curl
RUN apt-get update && apt-get install -y \
    libssl-dev \
    pkg-config \
    libcurl4-openssl-dev \
    git \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Встановлюємо розширення MongoDB
# pecl install mongodb завантажує та компілює драйвер [11, 12]
# docker-php-ext-enable mongodb активує його [13]
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# Встановлюємо розширення cURL, критично важливе для міжсервісної комунікації 
RUN docker-php-ext-install curl

# Встановлюємо Composer (глобально) [11, 14]
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

# Встановлюємо робочу директорію
WORKDIR /var/www/html