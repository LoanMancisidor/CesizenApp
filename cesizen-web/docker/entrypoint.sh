#!/bin/sh
set -e

if [ ! -f .env ]; then
    cp .env.example .env
fi

if ! grep -q "^APP_KEY=base64" .env; then
    php artisan key:generate --force
fi

echo "En attente de la base de donnees (${DB_HOST}:${DB_PORT})..."
until php artisan db:show > /dev/null 2>&1; do
    sleep 2
done

php artisan config:clear
php artisan migrate --force

exec php artisan serve --host=0.0.0.0 --port=80
