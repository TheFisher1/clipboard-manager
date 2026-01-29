# XAMPP Setup Guide

## Prerequisites
- XAMPP installed with Apache and MySQL
- PHP 7.4 or higher

## Installation Steps

### 1. Enable mod_rewrite in Apache

1. Open XAMPP Control Panel
2. Click "Config" button next to Apache
3. Select "httpd.conf"
4. Find the line: `#LoadModule rewrite_module modules/mod_rewrite.so`
5. Remove the `#` to uncomment it: `LoadModule rewrite_module modules/mod_rewrite.so`
6. Find all instances of `AllowOverride None` and change them to `AllowOverride All`
7. Save the file and restart Apache

### 2. Place the Application

1. Copy this entire project folder to `C:\xampp\htdocs\` (Windows) or `/Applications/XAMPP/htdocs/` (Mac)
2. Rename the folder to something simple like `clipboard` (optional)
3. Your app will be accessible at `http://localhost/clipboard/` (or `http://localhost/` if placed directly in htdocs)

### 3. Configure the Database

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Create a new database named `clipboard_system`
3. Import the database schema:
   - Click on the `clipboard_system` database
   - Go to "Import" tab
   - Choose file: `config/database.sql`
   - Click "Go"

### 4. Update Configuration (if needed)

The default configuration in `config/config.php` should work with XAMPP:
```php
DB_HOST: localhost
DB_NAME: clipboard_system
DB_USER: root
DB_PASS: (empty)
```

If you have a different MySQL password, update `config/config.php`.

### 5. Set Permissions (Important!)

Make sure the `uploads` folder is writable:
- Windows: Right-click folder → Properties → Security → Edit → Allow "Full control"
- Mac/Linux: `chmod 777 uploads`

### 6. Access the Application

- **Public Site**: `http://localhost/clipboard/` (or `http://localhost/` if in root)
- **Admin Panel**: `http://localhost/clipboard/admin/`
- **Install Page**: `http://localhost/clipboard/install.php` (first-time setup)

## Troubleshooting

### Issue: 404 errors or "Not Found"
**Solution**: Make sure mod_rewrite is enabled (see step 1)

### Issue: "Access forbidden" errors
**Solution**: Check that `AllowOverride All` is set in httpd.conf

### Issue: API calls failing
**Solution**: 
1. Check browser console for errors
2. Verify the API base URL in JavaScript files matches your setup
3. Make sure PHP sessions are working (check `php.ini` for session.save_path)

### Issue: Database connection errors
**Solution**:
1. Verify MySQL is running in XAMPP Control Panel
2. Check database credentials in `config/config.php`
3. Ensure the database `clipboard_system` exists

### Issue: File uploads not working
**Solution**:
1. Check `uploads` folder permissions
2. Verify `upload_max_filesize` and `post_max_size` in `php.ini`

## Testing the Setup

1. Visit `http://localhost/clipboard/install.php` to create the admin account
2. Register a new user at `http://localhost/clipboard/public/register.html`
3. Login and test creating a clipboard
4. Access admin panel with admin credentials

## Important Notes

- The `.htaccess` file is crucial for routing - don't delete it
- If you move the app to a subdirectory, the routing should work automatically
- For production deployment, disable `display_errors` in PHP configuration
- Change default database credentials for production use

## Need Help?

Check the main README.md for more information about the application features and API documentation.
