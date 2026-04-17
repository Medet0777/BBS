#!/bin/sh
set -e

mkdir -p /var/www/html/storage/app/public/basset
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

php artisan migrate --force

BARBERSHOP_COUNT=$(php artisan tinker --execute="echo \App\Models\Barbershop::count();" 2>/dev/null || echo "0")
if [ "$BARBERSHOP_COUNT" = "0" ]; then
    php artisan db:seed --force 2>/dev/null || true
fi

php artisan storage:link 2>/dev/null || true
php artisan l5-swagger:generate 2>/dev/null || true
rm -rf /var/www/html/storage/app/public/basset/* 2>/dev/null || true
php artisan basset:clear 2>/dev/null || true
php artisan basset:cache 2>/dev/null || true

exec /usr/bin/supervisord -c /etc/supervisord.conf
