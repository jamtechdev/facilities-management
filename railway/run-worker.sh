#!/bin/bash
# Make sure this file has executable permissions, run `chmod +x railway/run-worker.sh`

# Generate .env file from environment variables
chmod +x ./railway/generate-env.sh && sh ./railway/generate-env.sh

# This command runs the queue worker.
# An alternative is to use the php artisan queue:listen command
php artisan queue:work

