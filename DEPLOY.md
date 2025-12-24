# Railway Deployment Guide

## Quick Setup

### 1. Create MySQL Database
- Railway Dashboard → New → Database → Add MySQL

### 2. Deploy App
- Railway Dashboard → New → GitHub Repo → Select your repo

### 3. Set Environment Variables
Go to App Service → Variables and add:

```
APP_KEY=base64:your-generated-key
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app.up.railway.app

DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQLHOST}}
DB_PORT=${{MySQL.MYSQLPORT}}
DB_DATABASE=${{MySQL.MYSQLDATABASE}}
DB_USERNAME=${{MySQL.MYSQLUSER}}
DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}

LOG_CHANNEL=stderr
LOG_STDERR_FORMATTER=\Monolog\Formatter\JsonFormatter
```

**Note:** Link MySQL service first (click "Add a Variable Reference" in Variables tab)

### 4. Generate Domain
- App Service → Settings → Networking → Generate Domain
- Update `APP_URL` with the generated domain

## Files

- `Dockerfile` - Docker configuration
- `railway.json` - Railway deployment config
- `railway/init-app.sh` - Setup script (runs migrations, seeds, caching)

That's it! 🚀

