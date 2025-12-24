# Railway Database Connection Setup

## Overview

This guide explains how the database connection is configured and tested for Railway deployment.

## Database Connection Flow

1. **Environment Variables** → Set in Railway dashboard
2. **generate-env.sh** → Creates `.env` file from variables
3. **test-db-connection.sh** → Verifies database is accessible
4. **init-app.sh** → Runs migrations after connection verified
5. **Application** → Uses database connection

## Required Environment Variables

Set these in Railway dashboard (App Service → Variables):

### Option 1: Direct Values (Current Setup)
```
DB_CONNECTION=mysql
DB_HOST=centerbeam.proxy.rlwy.net
DB_PORT=28953
DB_DATABASE=railway
DB_USERNAME=root
DB_PASSWORD=AgcaHrPGJfJfqrtrHJTdJkiLstVSHrUf
```

### Option 2: Template Variables (If Services Linked)
```
DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQLHOST}}
DB_PORT=${{MySQL.MYSQLPORT}}
DB_DATABASE=${{MySQL.MYSQLDATABASE}}
DB_USERNAME=${{MySQL.MYSQLUSER}}
DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}
```

## Connection Testing

### Script: `railway/test-db-connection.sh`

This script:
- ✅ Tests database connection before migrations
- ✅ Retries up to 30 times (60 seconds total)
- ✅ Provides clear error messages
- ✅ Exits if connection fails

### When It Runs

- **Before migrations** in `init-app.sh`
- **Prevents failed deployments** if database is not ready
- **Shows connection details** in logs

## Connection Process

```
1. Container starts
2. generate-env.sh → Creates .env file
3. test-db-connection.sh → Tests connection
   ├─ Success → Continue
   └─ Failure → Exit with error
4. init-app.sh → Run migrations
5. Apache starts → Application ready
```

## Verifying Connection

### In Railway Logs

Look for:
```
Testing database connection...
Attempt 1/30: Testing connection to centerbeam.proxy.rlwy.net:28953...
Database connection successful!
Host: centerbeam.proxy.rlwy.net:28953
Database: railway
Username: root
Database connection verified successfully!
```

### Common Issues

#### Connection Timeout
```
ERROR: Could not connect to database after 30 attempts
```

**Solutions:**
- Check MySQL service is running (green status)
- Verify `DB_HOST` and `DB_PORT` are correct
- Use public hostname if internal doesn't work
- Check Railway status for outages

#### Authentication Failed
```
Connection failed: Access denied for user...
```

**Solutions:**
- Verify `DB_USERNAME` and `DB_PASSWORD`
- Check credentials in MySQL service → Variables
- Ensure user has proper permissions

#### Database Not Found
```
Connection failed: Unknown database 'railway'
```

**Solutions:**
- Database will be created automatically by migrations
- Or create manually in Railway MySQL dashboard
- Verify `DB_DATABASE` name is correct

## Linking Services (Recommended)

For better connection reliability:

1. Go to **App Service** → **Variables**
2. Click **"Add a Variable Reference"**
3. Select **MySQL service**
4. Use template variables: `${{MySQL.MYSQLHOST}}` etc.

This ensures:
- ✅ Automatic connection if MySQL restarts
- ✅ Correct credentials always used
- ✅ No manual updates needed

## Testing Locally

To test connection script locally:

```bash
export DB_HOST=centerbeam.proxy.rlwy.net
export DB_PORT=28953
export DB_DATABASE=railway
export DB_USERNAME=root
export DB_PASSWORD=your-password

chmod +x railway/test-db-connection.sh
sh railway/test-db-connection.sh
```

## Database Configuration

The connection uses these MySQL settings:
- **Charset**: `utf8mb4`
- **Collation**: `utf8mb4_unicode_ci`
- **Strict Mode**: Enabled
- **Connection Timeout**: 5 seconds per attempt

## Next Steps

After successful connection:
1. ✅ Migrations will run automatically
2. ✅ Database tables will be created
3. ✅ Seeders will populate initial data
4. ✅ Application will be ready

## Troubleshooting Checklist

- [ ] MySQL service is online (green status)
- [ ] Environment variables are set correctly
- [ ] `DB_HOST` uses public hostname or template variable
- [ ] `DB_PORT` matches MySQL service port
- [ ] `DB_USERNAME` and `DB_PASSWORD` are correct
- [ ] Services are in the same Railway project
- [ ] Check Railway logs for connection errors

## Support

If connection issues persist:
1. Check Railway MySQL service logs
2. Verify network connectivity
3. Try using public hostname instead of internal
4. Contact Railway support if service is down

