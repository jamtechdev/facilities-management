# Railway Deployment Guide

This guide will help you deploy your Laravel Facilities Management application to Railway.

## Prerequisites

1. A GitHub account with your code repository
2. A Railway account (sign up at [railway.app](https://railway.app))
3. Your Laravel application code pushed to GitHub

## Step 1: Create Railway Account

1. Go to [railway.app](https://railway.app)
2. Sign up with your GitHub account
3. You'll receive $5 in free credits to start

## Step 2: Create a New Project

1. Click "New Project" in Railway dashboard
2. Select "Deploy from GitHub repo"
3. Choose your repository
4. Railway will automatically detect the Dockerfile

## Step 3: Add PostgreSQL Database

1. In your Railway project, click "New"
2. Select "Database" → "Add PostgreSQL"
3. Railway will create a PostgreSQL database automatically
4. The database connection details will be available as environment variables

## Step 4: Configure Environment Variables

In your Railway project, go to the "Variables" tab and add the following environment variables:

### Required Variables

```
APP_NAME="Facilities Management"
APP_ENV=production
APP_KEY=                    # Generate with: php artisan key:generate
APP_DEBUG=false
APP_URL=                    # Will be provided by Railway (e.g., https://yourapp.up.railway.app)

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=pgsql
DB_HOST=${{Postgres.PGHOST}}
DB_PORT=${{Postgres.PGPORT}}
DB_DATABASE=${{Postgres.PGDATABASE}}
DB_USERNAME=${{Postgres.PGUSER}}
DB_PASSWORD=${{Postgres.PGPASSWORD}}

BROADCAST_CONNECTION=log
CACHE_STORE=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=database
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Important Notes:

- **APP_KEY**: Generate this locally with `php artisan key:generate` and copy the value
- **APP_URL**: Railway will provide this after deployment, or you can set a custom domain
- **Database Variables**: Use Railway's template variables (shown above) that automatically connect to your PostgreSQL database

## Step 5: Deploy

1. Railway will automatically start building and deploying your application
2. You can monitor the build logs in the Railway dashboard
3. Once deployed, Railway will provide you with a public URL

## Step 6: Run Migrations

After the first deployment, you may need to run migrations:

1. Go to your service in Railway
2. Click on the service → "Deployments" tab
3. Click on the latest deployment → "View Logs"
4. Or use Railway CLI: `railway run php artisan migrate --force`

Alternatively, migrations will run automatically on each deployment due to the start command in `railway.json`.

## Step 7: Run Seeders (Optional)

If you want to seed your database with initial data:

1. Use Railway CLI: `railway run php artisan db:seed`
2. Or add to your deployment process

## Step 8: Set Up Custom Domain (Optional)

1. In Railway, go to your service → "Settings"
2. Click "Generate Domain" or add your custom domain
3. Update `APP_URL` environment variable with your custom domain

## Troubleshooting

### Build Fails

- Check build logs in Railway dashboard
- Ensure all dependencies are in `composer.json`
- Verify Dockerfile syntax

### Database Connection Issues

- Verify database environment variables are set correctly
- Check that PostgreSQL service is running
- Ensure `DB_CONNECTION=pgsql` is set

### Application Errors

- Check application logs: Railway dashboard → Service → Logs
- Verify `APP_KEY` is set
- Ensure `APP_DEBUG=false` in production
- Check file permissions for storage and cache directories

### Assets Not Loading

- Ensure `npm run build` completed successfully
- Check that `public/build` directory exists
- Verify `APP_URL` is set correctly

## Railway CLI (Optional)

Install Railway CLI for easier management:

```bash
npm i -g @railway/cli
railway login
railway link
railway up
```

## Cost Management

- Railway provides $5 free credit monthly
- Monitor usage in Railway dashboard
- Set up usage alerts to avoid unexpected charges
- Free tier is suitable for small to medium applications

## Additional Resources

- [Railway Documentation](https://docs.railway.app)
- [Laravel Deployment Guide](https://laravel.com/docs/deployment)
- [Railway Discord Community](https://discord.gg/railway)

## Support

If you encounter issues:
1. Check Railway logs
2. Review Laravel logs in `storage/logs`
3. Check Railway status page
4. Contact Railway support

