FROM php:8.3-fpm

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    unzip \
    nodejs \
    npm \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libicu-dev \
    libonig-dev \
    libpq-dev \
    libzip-dev \
    zlib1g-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        pgsql \
        gd \
        intl \
        zip \
        bcmath \
    && curl -sS https://getcomposer.org/installer | php -- \
        --install-dir=/usr/local/bin \
        --filename=composer \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www

# نسخ ملفات الاعتماد أولًا للاستفادة من Docker cache
COPY composer.json composer.lock ./
COPY package.json package-lock.json ./

# لا تشغّل Laravel scripts لأن artisan لم يُنسخ بعد
RUN composer install \
        --no-interaction \
        --prefer-dist \
        --no-scripts \
    && npm ci --ignore-scripts

# نسخ المشروع كاملًا، ومن ضمنه artisan
COPY . /var/www

# تشغيل Composer scripts بعد توفر artisan
RUN composer dump-autoload --optimize --no-interaction \
    && php artisan package:discover --ansi \
    && npm run build \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
