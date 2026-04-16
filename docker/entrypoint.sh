#!/bin/sh
set -e

# Set default PORT if not provided
export PORT=${PORT:-8080}

# Substitute $PORT in nginx config
envsubst '${PORT}' < /etc/nginx/http.d/default.conf > /etc/nginx/http.d/default.conf.tmp
mv /etc/nginx/http.d/default.conf.tmp /etc/nginx/http.d/default.conf

php artisan migrate --force
php artisan storage:link 2>/dev/null || true
php artisan l5-swagger:generate 2>/dev/null || true

exec /usr/bin/supervisord -c /etc/supervisord.conf
