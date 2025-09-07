FROM php:8.3-fpm-alpine

# Dependencies & PHP extensions
RUN apk add --no-cache \
    bash git curl libpng-dev libjpeg-turbo-dev libwebp-dev libzip-dev \
    oniguruma-dev icu-dev postgresql-dev \
    freetype-dev autoconf g++ make shadow \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
    pdo pdo_mysql pdo_pgsql mbstring intl zip bcmath gd opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && usermod -u 1000 www-data && groupmod -g 1000 www-data

# Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
CMD ["php-fpm", "-F"]


# Tambah Node.js & npm (Alpine)
RUN apk add --no-cache nodejs npm

RUN composer install && composer update
RUN npm install && npm run build
