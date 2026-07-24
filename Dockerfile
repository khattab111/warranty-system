FROM php:8.3-fpm

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y --no-install-recommends \
    git curl libpng-dev libjpeg-dev libfreetype6-dev libicu-dev libonig-dev libpq-dev libzip-dev unzip zlib1g-dev nodejs npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql pgsql gd intl zip bcmath \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www

COPY composer.json composer.lock ./
COPY package.json package-lock.json ./
RUN composer install --no-interaction --prefer-dist \
    && npm install --ignore-scripts

COPY . /var/www

RUN composer dump-autoload --no-interaction \
    && php artisan package:discover --ansi || true \
    && npm run build || true

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
