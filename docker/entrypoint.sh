#!/bin/sh
set -e

cd /var/www/html

php artisan config:clear
php artisan package:discover --ansi

php artisan migrate --force

php artisan config:cache
php artisan route:cache
php artisan view:cache

chown www-data:www-data storage/app/public
exec docker-php-entrypoint "$@"
