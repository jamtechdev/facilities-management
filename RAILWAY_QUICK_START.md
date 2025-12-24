# Railway Quick Start Guide

## ✅ Files Ready for Deployment

All necessary files have been created according to Railway Laravel documentation:

### 📁 Railway Scripts
- ✅ `railway/init-app.sh` - App initialization script
- ✅ `railway/run-worker.sh` - Queue worker script  
- ✅ `railway/run-cron.sh` - Cron scheduler script

### 📄 Configuration Files
- ✅ `railway.json` - Railway deployment config
- ✅ `railway-production.env` - Production environment variables
- ✅ `Dockerfile` - Docker configuration
- ✅ `.dockerignore` - Docker ignore file

## 🚀 Quick Deployment Steps

### 1. Push to GitHub
```bash
git add .
git commit -m "Add Railway deployment configuration"
git push origin main
```

### 2. Create Services on Railway

#### A. MySQL Database
1. Railway Dashboard → **New** → **Database** → **Add MySQL**
2. Note the connection details

#### B. App Service
1. Railway Dashboard → **New** → **GitHub Repo**
2. Select your repository
3. **Settings**:
   - Build Command: `npm run build`
   - Pre-Deploy Command: `chmod +x ./railway/init-app.sh && sh ./railway/init-app.sh`
4. **Variables**: Copy from `railway-production.env`
5. **Networking**: Generate domain

#### C. Worker Service (Optional)
1. **New** → **GitHub Repo** (same repo)
2. **Settings**:
   - Custom Start Command: `chmod +x ./railway/run-worker.sh && sh ./railway/run-worker.sh`
3. **Variables**: Same as App Service

#### D. Cron Service (Optional)
1. **New** → **GitHub Repo** (same repo)
2. **Settings**:
   - Custom Start Command: `chmod +x ./railway/run-cron.sh && sh ./railway/run-cron.sh`
3. **Variables**: Same as App Service

## 🔑 Critical Environment Variables

### Database (Choose One)

**Option 1: Direct Values**
```
DB_CONNECTION=mysql
DB_HOST=centerbeam.proxy.rlwy.net
DB_PORT=28953
DB_DATABASE=railway
DB_USERNAME=root
DB_PASSWORD=AgcaHrPGJfJfqrtrHJTdJkiLstVSHrUf
```

**Option 2: Template Variables** (if services linked)
```
DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQLHOST}}
DB_PORT=${{MySQL.MYSQLPORT}}
DB_DATABASE=${{MySQL.MYSQLDATABASE}}
DB_USERNAME=${{MySQL.MYSQLUSER}}
DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}
```

### Logging (Required for Railway)
```
LOG_CHANNEL=stderr
LOG_STDERR_FORMATTER=\Monolog\Formatter\JsonFormatter
```

### App Configuration
```
APP_KEY=base64:nFMcLz/Na7RBsJd0NS9/G1T9ZvmhvhoKFDjL+EIAGwE=
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app-name.up.railway.app
```

## 📋 Checklist

Before deploying:

- [ ] All code pushed to GitHub
- [ ] MySQL database created on Railway
- [ ] Environment variables copied to Railway
- [ ] `APP_KEY` generated and set
- [ ] `APP_URL` updated after domain generation
- [ ] Database credentials correct
- [ ] Logging variables set (`LOG_CHANNEL=stderr`)

## 🔍 Verify Deployment

1. Check App Service logs for:
   - ✅ Migrations completed
   - ✅ Cache built successfully
   - ✅ Apache started

2. Check Worker Service logs for:
   - ✅ Queue worker running

3. Check Cron Service logs for:
   - ✅ Scheduler running every minute

## 📚 Documentation

- Full guide: `RAILWAY_LARAVEL_SETUP.md`
- Database fix: `RAILWAY_DATABASE_FIX.md`
- Original deployment: `RAILWAY_DEPLOYMENT.md`

## 🆘 Common Issues

### Database Connection Error
- Use public hostname: `centerbeam.proxy.rlwy.net:28953`
- Or link services and use template variables

### Logs Not Showing
- Set `LOG_CHANNEL=stderr`
- Set `LOG_STDERR_FORMATTER=\Monolog\Formatter\JsonFormatter`

### Migrations Not Running
- Check Pre-Deploy Command is set correctly
- Verify `init-app.sh` has executable permissions

## 🎉 Next Steps

1. Deploy and test
2. Set up custom domain
3. Configure email service
4. Set up monitoring

