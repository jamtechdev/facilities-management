# Railway .env File Generation

## Overview

The `.env` file is now automatically generated from Railway environment variables when the container starts. This ensures that all services (App, Worker, Cron) have the correct environment configuration.

## How It Works

### Script: `railway/generate-env.sh`

This script:
1. Reads environment variables from Railway
2. Generates a `.env` file with all required Laravel configuration
3. Uses default values if variables are not set
4. Runs automatically before app initialization

### Execution Order

1. **Container starts**
2. **`generate-env.sh`** runs → Creates `.env` file
3. **`init-app.sh`** runs → Migrations, seeding, caching
4. **Apache starts** → Application ready

## Environment Variables Required

Make sure these are set in Railway dashboard:

### Critical Variables
```
APP_KEY=base64:your-generated-key
DB_HOST=centerbeam.proxy.rlwy.net (or ${{MySQL.MYSQLHOST}})
DB_PORT=28953 (or ${{MySQL.MYSQLPORT}})
DB_DATABASE=railway (or ${{MySQL.MYSQLDATABASE}})
DB_USERNAME=root (or ${{MySQL.MYSQLUSER}})
DB_PASSWORD=your-password (or ${{MySQL.MYSQLPASSWORD}})
APP_URL=https://your-app.up.railway.app
```

### Optional Variables (with defaults)
- `APP_NAME` (default: "Facilities Management")
- `APP_ENV` (default: "production")
- `APP_DEBUG` (default: "false")
- `LOG_CHANNEL` (default: "stderr")
- `QUEUE_CONNECTION` (default: "database")
- And many more...

## Services Using .env Generation

All services automatically generate `.env`:

1. **App Service** - Via `init-app.sh`
2. **Worker Service** - Via `run-worker.sh`
3. **Cron Service** - Via `run-cron.sh`

## Verification

After deployment, check logs to see:
```
.env file generated successfully
[.env file contents displayed]
```

## Benefits

✅ No need to manually create .env file
✅ Environment variables automatically synced
✅ Works across all services (App, Worker, Cron)
✅ Default values provided for optional variables
✅ Secure - passwords from Railway variables only

## Troubleshooting

### .env file not generated
- Check that `generate-env.sh` has executable permissions
- Verify environment variables are set in Railway
- Check deployment logs for errors

### Missing variables
- Add missing variables to Railway dashboard
- Script will use defaults for optional variables
- Critical variables (DB_*, APP_KEY) must be set

### Wrong values in .env
- Update variables in Railway dashboard
- Redeploy service to regenerate .env

