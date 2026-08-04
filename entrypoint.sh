#!/bin/sh
set -e

# Make sure storage/cache directories exist and are writable, even if a
# mounted volume reset ownership/permissions at runtime.
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/testing storage/framework/views bootstrap/cache
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# Clear caches. Non-fatal on purpose: a permission hiccup here should not
# stop the app from booting, since these commands only clean up stale
# cached files rather than being required for startup.
php artisan config:clear || echo "Warning: config:clear failed, continuing anyway."
php artisan cache:clear || echo "Warning: cache:clear failed, continuing anyway."

# Run migrations
php artisan migrate --force

# Run seeders
php artisan db:seed --force

# Start Laravel
exec php artisan serve --host=0.0.0.0 --port=$PORT