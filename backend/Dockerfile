## Multi-stage Dockerfile for Laravel + Inertia (Vue) app

# 1) Composer deps
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader \
    --ignore-platform-req=ext-pcntl --ignore-platform-req=ext-posix --no-scripts

# 2) Build assets (Vite)
FROM node:20-alpine AS assets
WORKDIR /app
ARG SKIP_ASSETS=1
COPY package.json package-lock.json* ./
RUN if [ "$SKIP_ASSETS" != "1" ]; then npm ci --legacy-peer-deps; else echo "Skipping npm ci"; fi
COPY resources ./resources
COPY vite.config.js ./vite.config.js
COPY public ./public
ENV SKIP_PWA=1
RUN if [ "$SKIP_ASSETS" != "1" ]; then npm run build || npm run build:ci; else mkdir -p public/build; fi

# 3) Runtime (PHP-FPM)
FROM php:8.2-fpm AS app

ARG UID=1000
ARG GID=1000

RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        git unzip curl libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
        libzip-dev libonig-dev libicu-dev libxml2-dev \
        mariadb-client default-mysql-client \
        cron; \
    docker-php-ext-configure gd --with-freetype --with-jpeg; \
    docker-php-ext-install -j$(nproc) pdo pdo_mysql zip gd bcmath exif intl pcntl; \
    rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Copy code and built artifacts
COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

# Permissions
RUN addgroup --gid ${GID} appgroup || true; \
    adduser --disabled-password --gecos "" --uid ${UID} --gid ${GID} appuser || true; \
    chown -R appuser:appgroup /var/www/html

COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

USER appuser

EXPOSE 9000
ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]
