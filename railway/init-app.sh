#!/bin/bash
# Make sure this file has executable permissions, run `chmod +x railway/init-app.sh`

# Exit the script if any command fails
set -e

# Generate .env file from environment variables
chmod +x ./railway/generate-env.sh && sh ./railway/generate-env.sh

# Test database connection before proceeding
echo "Testing database connection..."
chmod +x ./railway/test-db-connection.sh && sh ./railway/test-db-connection.sh

# Run migrations
echo "Running database migrations..."
php artisan migrate --force

# Run database seeders
php artisan db:seed --force

# Clear cache
php artisan optimize:clear

# Cache the various components of the Laravel application
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

