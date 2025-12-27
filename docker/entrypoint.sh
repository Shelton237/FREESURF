#!/bin/sh
set -e

cd /var/www/html

# Ensure .env exists
if [ ! -f .env ]; then
  if [ -f .env.example ]; then
    cp .env.example .env
  fi
fi

# Laravel setup
php artisan key:generate --force || true
php artisan storage:link || true
php artisan migrate --force || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

exec "$@"

