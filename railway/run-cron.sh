#!/bin/bash
# Make sure this file has executable permissions, run `chmod +x railway/run-cron.sh`

# Generate .env file from environment variables
chmod +x ./railway/generate-env.sh && sh ./railway/generate-env.sh

# This block of code runs the Laravel scheduler every minute
while [ true ]
    do
        echo "Running the scheduler..."
        php artisan schedule:run --verbose --no-interaction &
        sleep 60
    done

