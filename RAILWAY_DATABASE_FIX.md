# Railway Database Connection Fix

## Problem
Error: `php_network_getaddresses: getaddrinfo for mysql.railway.internal failed: Name or service not known`

## Solution Options

### Option 1: Link MySQL Service (RECOMMENDED)

1. Go to your **Application Service** in Railway dashboard
2. Click on **"Variables"** tab
3. Look for the purple banner that says: **"Trying to connect this database to a service? Add a Variable Reference"**
4. Click **"Add a Variable Reference"** button
5. Select your **MySQL service** from the dropdown
6. Railway will automatically add template variables

Then use these environment variables:
```
DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQLHOST}}
DB_PORT=${{MySQL.MYSQLPORT}}
DB_DATABASE=${{MySQL.MYSQLDATABASE}}
DB_USERNAME=${{MySQL.MYSQLUSER}}
DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}
```

### Option 2: Use Public Hostname (If Option 1 doesn't work)

If linking doesn't work, use the **PUBLIC** hostname from your MySQL service:

1. Go to your **MySQL service** in Railway
2. Go to **"Variables"** tab
3. Find `MYSQL_PUBLIC_URL` or use the public hostname
4. In your **Application service** → **Variables**, set:

```
DB_CONNECTION=mysql
DB_HOST=centerbeam.proxy.rlwy.net
DB_PORT=28953
DB_DATABASE=railway
DB_USERNAME=root
DB_PASSWORD=AgcaHrPGJfJfqrtrHJTdJkiLstVSHrUf
```

**Important:** Replace the values above with your actual MySQL credentials from Railway.

### Option 3: Check Service Names

Make sure:
1. Both services (App and MySQL) are in the **same project**
2. MySQL service is **running** (not stopped)
3. Service names match (if you renamed them)

## Steps to Fix Right Now

1. **Open Railway Dashboard**
2. **Go to your Application Service**
3. **Click "Variables" tab**
4. **Delete or update these variables:**
   - `DB_HOST` - Should be either `${{MySQL.MYSQLHOST}}` OR `centerbeam.proxy.rlwy.net`
   - `DB_PORT` - Should be either `${{MySQL.MYSQLPORT}}` OR `28953`
   - `DB_DATABASE` - Should be either `${{MySQL.MYSQLDATABASE}}` OR `railway`
   - `DB_USERNAME` - Should be either `${{MySQL.MYSQLUSER}}` OR `root`
   - `DB_PASSWORD` - Should be either `${{MySQL.MYSQLPASSWORD}}` OR your actual password

5. **If using template variables**, make sure MySQL service is linked:
   - Click "Add a Variable Reference" in the purple banner
   - Select your MySQL service

6. **Save and Redeploy**

## Verify Connection

After updating variables, check the logs. You should see:
- ✅ Migrations running successfully
- ✅ No "Name or service not known" errors
- ✅ Application starting normally

## Still Not Working?

1. Check MySQL service is running (green status)
2. Verify both services are in same project
3. Try using the public hostname (Option 2) instead of internal
4. Check Railway status page for any outages

