# --- Stage 1 : build des assets front (Vite) ---
FROM node:20-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY resources ./resources
COPY vite.config.js ./

RUN npm run build

# --- Stage 2 : application PHP ---
FROM php:8.4-cli-alpine AS app

RUN apk add --no-cache \
        icu-dev \
        oniguruma-dev \
        libzip-dev \
        sqlite-dev \
    && docker-php-ext-install \
        pdo_sqlite \
        mbstring \
        bcmath \
        intl \
        zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker/php.ini /usr/local/etc/php/conf.d/security.ini

WORKDIR /var/www/html

COPY . .
COPY --from=assets /app/public/build ./public/build

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress \
    && chmod +x docker/entrypoint.sh \
    && mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80

ENTRYPOINT ["docker/entrypoint.sh"]
