#!/bin/bash
# Don't exit on error - we want Apache to start even if some steps fail
set +e

# Ensure output goes to stdout/stderr for Railway logs
exec 1>&2

echo "🚀 Starting Laravel setup..."

# Change to app directory
cd /var/www/html || exit 1

# Generate .env file from environment variables
echo "📝 Generating .env file..."
cat > /var/www/html/.env <<EOF
APP_NAME="${APP_NAME:-Facilities Management}"
APP_ENV="${APP_ENV:-production}"
APP_KEY="${APP_KEY:-}"
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

# Wait for database to be ready (but don't fail if it doesn't connect)
echo "⏳ Waiting for database connection..."
echo "DB_HOST: ${DB_HOST}"
echo "DB_PORT: ${DB_PORT}"
echo "DB_DATABASE: ${DB_DATABASE}"

MAX_ATTEMPTS=10
ATTEMPT=0
DB_CONNECTED=false

while [ $ATTEMPT -lt $MAX_ATTEMPTS ]; do
    php -r "
    try {
        \$pdo = new PDO(
            'mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'),
            getenv('DB_USERNAME'),
            getenv('DB_PASSWORD'),
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 5]
        );
        exit(0);
    } catch (PDOException \$e) {
        exit(1);
    }
    " 2>&1 > /dev/null
    
    if [ $? -eq 0 ]; then
        DB_CONNECTED=true
        echo "✅ Database connected!"
        break
    fi
    
    ATTEMPT=$((ATTEMPT + 1))
    echo "  Attempt $((ATTEMPT + 1))/$MAX_ATTEMPTS - waiting for database..."
    sleep 2
done

if [ "$DB_CONNECTED" = false ]; then
    echo "⚠️  Database connection failed, but continuing..."
fi

# Run database operations only if connected
if [ "$DB_CONNECTED" = true ]; then
    # Clear caches
    echo "🧹 Clearing caches..."
    php artisan optimize:clear 2>&1 || true
    
    # Run migrations
    echo "🔄 Running migrations..."
    php artisan migrate --force 2>&1 || echo "⚠️  Migrations failed, continuing..."
    
    # Run seeders
    echo "🌱 Seeding database..."
    php artisan db:seed --force 2>&1 || echo "⚠️  Seeding failed, continuing..."
    
    # Optimize Laravel
    echo "⚡ Optimizing application..."
    php artisan config:cache 2>&1 || true
    php artisan route:cache 2>&1 || true
    php artisan view:cache 2>&1 || true
else
    echo "⚠️  Skipping database operations due to connection failure"
fi

# Create storage link (always try this)
echo "🔗 Creating storage link..."
php artisan storage:link 2>&1 || true

# Ensure storage directories exist
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
chmod -R 775 storage
chmod -R 775 bootstrap/cache

echo "✅ Setup completed! Starting Apache..."

# Start Apache (this must always run)
exec apache2-foreground

