# Railway Laravel Deployment Guide

This guide follows the official Railway Laravel deployment documentation.

## Architecture Overview

This setup uses a "majestic monolith" architecture with separate services:

1. **App Service** - Handles HTTP requests
2. **Worker Service** - Processes background jobs
3. **Cron Service** - Runs scheduled tasks
4. **MySQL Database** - Stores application data

## Files Created

### Railway Scripts

- `railway/init-app.sh` - App initialization (migrations, caching)
- `railway/run-worker.sh` - Queue worker process
- `railway/run-cron.sh` - Laravel scheduler

### Configuration Files

- `railway.json` - Railway deployment configuration
- `railway-production.env` - Production environment variables
- `Dockerfile` - Docker configuration for Laravel

## Deployment Steps

### Step 1: Create MySQL Database Service

1. Go to Railway dashboard
2. Click **"New"** → **"Database"** → **"Add MySQL"**
3. Railway will automatically create the database

### Step 2: Create App Service

1. Click **"New"** → **"GitHub Repo"**
2. Select your repository
3. Go to **Settings**:
   - **Build Command**: `npm run build`
   - **Pre-Deploy Command**: `chmod +x ./railway/init-app.sh && sh ./railway/init-app.sh`
4. Go to **Variables** and add all variables from `railway-production.env`
5. Important variables:
   - `APP_KEY`: Generate with `php artisan key:generate`
   - `DB_CONNECTION`: `mysql`
   - `DB_HOST`: `centerbeam.proxy.rlwy.net` (or `${{MySQL.MYSQLHOST}}` if linked)
   - `DB_PORT`: `28953` (or `${{MySQL.MYSQLPORT}}` if linked)
   - `DB_DATABASE`: `railway` (or `${{MySQL.MYSQLDATABASE}}` if linked)
   - `DB_USERNAME`: `root` (or `${{MySQL.MYSQLUSER}}` if linked)
   - `DB_PASSWORD`: Your MySQL password (or `${{MySQL.MYSQLPASSWORD}}` if linked)
   - `QUEUE_CONNECTION`: `database`
   - `LOG_CHANNEL`: `stderr`
   - `LOG_STDERR_FORMATTER`: `\Monolog\Formatter\JsonFormatter`
6. Click **Deploy**

### Step 3: Create Worker Service (Optional)

For background job processing:

1. Click **"New"** → **"GitHub Repo"**
2. Select the same repository
3. Go to **Settings**:
   - **Custom Start Command**: `chmod +x ./railway/run-worker.sh && sh ./railway/run-worker.sh`
4. Go to **Variables** and add the same variables as App Service
5. Click **Deploy**

### Step 4: Create Cron Service (Optional)

For scheduled tasks:

1. Click **"New"** → **"GitHub Repo"**
2. Select the same repository
3. Go to **Settings**:
   - **Custom Start Command**: `chmod +x ./railway/run-cron.sh && sh ./railway/run-cron.sh`
4. Go to **Variables** and add the same variables as App Service
5. Click **Deploy**

### Step 5: Generate Public Domain

1. Go to App Service → **Settings** → **Networking**
2. Click **"Generate Domain"**
3. Update `APP_URL` variable with the generated domain

## Environment Variables

### Required Variables

Copy all variables from `railway-production.env` to Railway dashboard.

### Key Variables for MySQL

**Option 1: Direct Values (Current Setup)**
```
DB_CONNECTION=mysql
DB_HOST=centerbeam.proxy.rlwy.net
DB_PORT=28953
DB_DATABASE=railway
DB_USERNAME=root
DB_PASSWORD=AgcaHrPGJfJfqrtrHJTdJkiLstVSHrUf
```

**Option 2: Template Variables (If Services Linked)**
```
DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQLHOST}}
DB_PORT=${{MySQL.MYSQLPORT}}
DB_DATABASE=${{MySQL.MYSQLDATABASE}}
DB_USERNAME=${{MySQL.MYSQLUSER}}
DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}
```

### Logging Configuration

For Railway console logs:
```
LOG_CHANNEL=stderr
LOG_STDERR_FORMATTER=\Monolog\Formatter\JsonFormatter
```

## Scripts Overview

### init-app.sh
- Runs database migrations
- Seeds database
- Clears and rebuilds cache
- Caches config, routes, views, events

### run-worker.sh
- Runs Laravel queue worker
- Processes background jobs

### run-cron.sh
- Runs Laravel scheduler every minute
- Handles scheduled tasks

## Troubleshooting

### Database Connection Issues

If you see `mysql.railway.internal failed: Name or service not known`:

1. **Option A**: Link MySQL service to App service:
   - Go to App Service → Variables
   - Click "Add a Variable Reference"
   - Select MySQL service
   - Use template variables: `${{MySQL.MYSQLHOST}}` etc.

2. **Option B**: Use public hostname:
   - Use `centerbeam.proxy.rlwy.net` as `DB_HOST`
   - Use port `28953` as `DB_PORT`

### Logs Not Appearing

Make sure these variables are set:
```
LOG_CHANNEL=stderr
LOG_STDERR_FORMATTER=\Monolog\Formatter\JsonFormatter
```

### Migrations Not Running

Check that `init-app.sh` has executable permissions:
- The script runs automatically via Pre-Deploy Command
- Verify in deployment logs

## Next Steps

1. Set up custom domain (if needed)
2. Configure email service (SMTP)
3. Set up monitoring and alerts
4. Configure backups for database

## References

- [Railway Laravel Documentation](https://docs.railway.app/guides/laravel)
- [Laravel Documentation](https://laravel.com/docs)

