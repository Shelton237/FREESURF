#!/bin/sh
set -e

cd /var/www/html

if [ ! -f .env ]; then
  if [ -f .env.example ]; then
    cp .env.example .env
  fi
fi

rm -f bootstrap/cache/packages.php bootstrap/cache/services.php || true
if [ -z "$APP_KEY" ]; then
  php artisan key:generate --force || true
fi
php artisan package:discover --ansi || true
php artisan storage:link || true
php artisan migrate --force || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

exec "$@"
