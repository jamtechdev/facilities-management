#!/bin/bash
set -e

echo "🚀 Starting Laravel application setup..."

# Wait for database to be ready (useful for Railway)
echo "⏳ Waiting for database connection..."
max_attempts=30
attempt=0

while [ $attempt -lt $max_attempts ]; do
    if php artisan migrate:status &> /dev/null 2>&1 || php artisan db:show &> /dev/null 2>&1; then
        echo "✅ Database is ready!"
        break
    fi
    
    attempt=$((attempt + 1))
    echo "⏳ Database is unavailable - attempt $attempt/$max_attempts - sleeping..."
    sleep 2
done

if [ $attempt -eq $max_attempts ]; then
    echo "⚠️  Warning: Could not connect to database after $max_attempts attempts. Proceeding anyway..."
fi

# Clear cache before migrations
echo "🧹 Clearing application cache..."
php artisan cache:clear || true
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Run migrations
echo "📦 Running database migrations..."
php artisan migrate --force

# Run seeders if RUN_SEEDERS environment variable is set
if [ "$RUN_SEEDERS" = "true" ] || [ "$RUN_SEEDERS" = "1" ]; then
    echo "🌱 Running database seeders..."
    php artisan db:seed --force
fi

# Optimize Laravel for production (optional, can be disabled with SKIP_OPTIMIZE)
if [ "$SKIP_OPTIMIZE" != "true" ] && [ "$SKIP_OPTIMIZE" != "1" ]; then
    echo "⚡ Optimizing Laravel for production..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

# Start PHP-FPM
echo "🎯 Starting PHP-FPM..."
exec php-fpm

