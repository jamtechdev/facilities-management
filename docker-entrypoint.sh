#!/bin/bash
set -e

# Ensure output goes to stdout/stderr for Railway logs
exec 1>&2

echo "🚀 Starting Laravel setup..."
echo "Current directory: $(pwd)"
echo "Working directory: /var/www/html"

# Generate .env file from environment variables
echo "📝 Generating .env file..."
cat > /var/www/html/.env <<EOF
APP_NAME="${APP_NAME:-Facilities Management}"
APP_ENV="${APP_ENV:-production}"
APP_KEY="${APP_KEY}"
APP_DEBUG="${APP_DEBUG:-false}"
APP_URL="${APP_URL:-http://localhost}"

LOG_CHANNEL="${LOG_CHANNEL:-stderr}"
LOG_STDERR_FORMATTER="${LOG_STDERR_FORMATTER:-\\Monolog\\Formatter\\JsonFormatter}"
LOG_LEVEL="${LOG_LEVEL:-error}"

DB_CONNECTION="${DB_CONNECTION:-mysql}"
DB_HOST="${DB_HOST:-ballast.proxy.rlwy.net}"
DB_PORT="${DB_PORT:-21988}"
DB_DATABASE="${DB_DATABASE:-railway}"
DB_USERNAME="${DB_USERNAME:-root}"
DB_PASSWORD="${DB_PASSWORD:-wKrHSSNUFwdyJiMgYPHfwnzOnRNQPgFM}"

SESSION_DRIVER="${SESSION_DRIVER:-database}"
QUEUE_CONNECTION="${QUEUE_CONNECTION:-database}"
CACHE_STORE="${CACHE_STORE:-database}"
FILESYSTEM_DISK="${FILESYSTEM_DISK:-local}"

MAIL_MAILER="${MAIL_MAILER:-log}"
MAIL_FROM_ADDRESS="${MAIL_FROM_ADDRESS:-hello@example.com}"
MAIL_FROM_NAME="${MAIL_FROM_NAME:-${APP_NAME}}"
EOF

# Wait for database to be ready
echo "⏳ Waiting for database connection..."
echo "DB_HOST: ${DB_HOST}"
echo "DB_PORT: ${DB_PORT}"
echo "DB_DATABASE: ${DB_DATABASE}"
echo "DB_USERNAME: ${DB_USERNAME}"

MAX_ATTEMPTS=30
ATTEMPT=0
while [ $ATTEMPT -lt $MAX_ATTEMPTS ]; do
    cd /var/www/html
    # Test MySQL connection directly
    php -r "
    try {
        \$pdo = new PDO(
            'mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'),
            getenv('DB_USERNAME'),
            getenv('DB_PASSWORD'),
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 5]
        );
        echo 'Database connection successful!' . PHP_EOL;
        exit(0);
    } catch (PDOException \$e) {
        echo 'Connection failed: ' . \$e->getMessage() . PHP_EOL;
        exit(1);
    }
    " 2>&1 && break || {
        ATTEMPT=$((ATTEMPT + 1))
        echo "  Attempt $((ATTEMPT + 1))/$MAX_ATTEMPTS - waiting for database..."
        sleep 2
    }
done

if [ $ATTEMPT -eq $MAX_ATTEMPTS ]; then
    echo "❌ Database connection failed after $MAX_ATTEMPTS attempts!"
    echo "Please check your database configuration."
    exit 1
fi

echo "✅ Database connected!"

# Change to app directory
cd /var/www/html

# Clear caches
echo "🧹 Clearing caches..."
php artisan optimize:clear 2>&1 || echo "Cache clear skipped"

# Check migration status
echo "📊 Checking migration status..."
php artisan migrate:status 2>&1 || echo "Migration status check skipped"

# Run migrations
echo "🔄 Running migrations..."
php artisan migrate --force 2>&1

# Run seeders
echo "🌱 Seeding database..."
php artisan db:seed --force 2>&1

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link 2>&1 || echo "Storage link already exists"

# Optimize Laravel
echo "⚡ Optimizing application..."
php artisan config:cache 2>&1
php artisan route:cache 2>&1
php artisan view:cache 2>&1

echo "✅ Setup completed! Starting Apache..."

# Start Apache
exec apache2-foreground

