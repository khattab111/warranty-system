#!/bin/sh

set -e

cd /var/www

php artisan config:clear
php artisan route:clear
php artisan view:clear

php artisan storage:link || true

php artisan migrate --force --seed

exec php artisan serve \
    --host=0.0.0.0 \
    --port="${PORT:-10000}"
