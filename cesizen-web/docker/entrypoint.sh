#!/bin/sh
set -e

mkdir -p database
touch database/database.sqlite

php artisan config:clear
php artisan migrate --force
php artisan db:seed --force

exec php artisan serve --host=0.0.0.0 --port=8080
