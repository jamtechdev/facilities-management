#!/bin/bash
# Test database connection before running migrations
# This script verifies that the database is accessible

set -e

echo "Testing database connection..."

# Wait for database to be ready (max 30 seconds)
MAX_ATTEMPTS=30
ATTEMPT=0

while [ $ATTEMPT -lt $MAX_ATTEMPTS ]; do
    echo "Attempt $((ATTEMPT + 1))/$MAX_ATTEMPTS: Testing connection to ${DB_HOST}:${DB_PORT}..."
    
    # Test MySQL connection using PHP
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
    " && break || sleep 2
    
    ATTEMPT=$((ATTEMPT + 1))
done

if [ $ATTEMPT -eq $MAX_ATTEMPTS ]; then
    echo "ERROR: Could not connect to database after $MAX_ATTEMPTS attempts"
    echo "DB_HOST: ${DB_HOST}"
    echo "DB_PORT: ${DB_PORT}"
    echo "DB_DATABASE: ${DB_DATABASE}"
    echo "DB_USERNAME: ${DB_USERNAME}"
    exit 1
fi

echo "Database connection verified successfully!"
echo "Host: ${DB_HOST}:${DB_PORT}"
echo "Database: ${DB_DATABASE}"
echo "Username: ${DB_USERNAME}"

