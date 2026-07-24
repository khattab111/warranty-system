#!/bin/sh
set -e

if [ ! -f .env ]; then
    cp .env.example .env
fi

if [ ! -d vendor ]; then
    composer install --no-interaction --prefer-dist
fi

if [ ! -d node_modules ]; then
    npm install --ignore-scripts
fi

php artisan key:generate --force || true
php artisan storage:link || true
php artisan migrate --force || true

npm run build || true

exec php-fpm
