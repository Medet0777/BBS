#!/bin/sh
set -e

php artisan migrate --force
php artisan storage:link 2>/dev/null || true
php artisan l5-swagger:generate 2>/dev/null || true
php artisan basset:clear 2>/dev/null || true
php artisan basset:cache 2>/dev/null || true

exec /usr/bin/supervisord -c /etc/supervisord.conf
