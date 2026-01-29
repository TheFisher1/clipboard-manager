# 🚀 Quick Start Guide for XAMPP

## Step 1: Enable mod_rewrite

1. Open XAMPP Control Panel
2. Click **Config** next to Apache → Select **httpd.conf**
3. Find this line (around line 169):
   ```
   #LoadModule rewrite_module modules/mod_rewrite.so
   ```
4. Remove the `#` to make it:
   ```
   LoadModule rewrite_module modules/mod_rewrite.so
   ```
5. Find all instances of `AllowOverride None` and change to `AllowOverride All`
6. Save and close

## Step 2: Place the Application

1. Copy this entire folder to:
   - **Windows**: `C:\xampp\htdocs\clipboard\`
   - **Mac**: `/Applications/XAMPP/htdocs/clipboard/`
   - **Linux**: `/opt/lampp/htdocs/clipboard/`

## Step 3: Setup Database

1. Start Apache and MySQL in XAMPP Control Panel
2. Open phpMyAdmin: http://localhost/phpmyadmin
3. Click **New** to create a database
4. Name it: `clipboard_system`
5. Click **Import** tab
6. Choose file: `config/database.sql` from this folder
7. Click **Go**

## Step 4: Restart Apache

Click **Stop** then **Start** for Apache in XAMPP Control Panel

## Step 5: Test Your Setup

Visit these URLs in order:

1. **Debug Page**: http://localhost/clipboard/debug.php
   - This shows if everything is configured correctly
   - Fix any red errors before continuing

2. **Install Page**: http://localhost/clipboard/install.php
   - Create your admin account
   - Follow the on-screen instructions

3. **Home Page**: http://localhost/clipboard/
   - Should show the main application

4. **Register**: http://localhost/clipboard/public/register.html
   - Create a regular user account

5. **Login**: http://localhost/clipboard/public/login.html
   - Login with your account

## Common Issues

### Issue: "404 Not Found" or blank pages
**Solution**: 
- Make sure mod_rewrite is enabled (Step 1)
- Restart Apache after making changes
- Check that .htaccess file exists in the folder

### Issue: "Database connection failed"
**Solution**:
- Make sure MySQL is running in XAMPP
- Check database name is `clipboard_system`
- Default credentials: user=`root`, password=`(empty)`

### Issue: "Access forbidden"
**Solution**:
- Make sure `AllowOverride All` is set in httpd.conf
- Restart Apache

### Issue: File uploads not working
**Solution**:
- Right-click `uploads` folder → Properties → Security
- Give "Full control" permissions (Windows)
- Or run: `chmod 777 uploads` (Mac/Linux)

## Need More Help?

Run the debug page to see detailed diagnostics:
```
http://localhost/clipboard/debug.php
```

It will tell you exactly what's wrong and how to fix it.

## Default Folder Structure

Your app should be at:
```
C:\xampp\htdocs\clipboard\
├── .htaccess          ← Important for routing!
├── index.php          ← Main entry point
├── debug.php          ← Run this first
├── install.php        ← Run this second
├── api/
├── public/
├── admin/
├── config/
└── src/
```

## Quick Test

After setup, test the API:
```
http://localhost/clipboard/api/auth/me
```

Should return JSON (might say "not authenticated" - that's OK!)

---

**✅ If everything works**: You should see the home page at http://localhost/clipboard/

**❌ If something fails**: Run debug.php and follow the recommendations
