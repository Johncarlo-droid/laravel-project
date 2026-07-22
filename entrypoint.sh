#!/bin/sh
set -e

# Gumawa ng .env kung wala pa
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Generate APP_KEY kung wala pa
php artisan key:generate --force

# Clear caches
php artisan config:clear
php artisan cache:clear

# Run migrations
php artisan migrate --force

# Run seeders
php artisan db:seed --force

# Start Laravel
exec php artisan serve --host=0.0.0.0 --port=$PORT