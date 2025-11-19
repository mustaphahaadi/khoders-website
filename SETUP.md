# XAMPP Setup Guide for Khoders World

## Prerequisites
- Windows 10/11, macOS, or Linux
- Minimum 2GB RAM
- 1GB free disk space

---

## Step 1: Install XAMPP

### Windows
1. Download XAMPP from https://www.apachefriends.org/
2. Run installer as Administrator
3. Select components: Apache, MySQL, PHP, phpMyAdmin
4. Install to default location: `C:\xampp`
5. Complete installation

### macOS
```bash
# Download from apachefriends.org
# Install to /Applications/XAMPP
```

### Linux
```bash
# Download installer
chmod +x xampp-linux-x64-installer.run
sudo ./xampp-linux-x64-installer.run
```

---

## Step 2: Configure PHP

### Edit php.ini
Location: `C:\xampp\php\php.ini`

```ini
# Increase upload limits
upload_max_filesize = 10M
post_max_size = 12M
max_execution_time = 300

# Enable required extensions
extension=mysqli
extension=pdo_mysql
extension=gd
extension=mbstring
extension=openssl

# Error reporting (development)
display_errors = On
error_reporting = E_ALL

# Timezone
date.timezone = America/New_York
```

---

## Step 3: Start Services

### Using XAMPP Control Panel
1. Open XAMPP Control Panel
2. Click "Start" for Apache
3. Click "Start" for MySQL
4. Verify ports: Apache (80, 443), MySQL (3306)

### Common Port Conflicts
- **Port 80 in use:** Change Apache port in `httpd.conf`
- **Port 3306 in use:** Change MySQL port in `my.ini`

---

## Step 4: Create Database

### Using phpMyAdmin
1. Open: http://localhost/phpmyadmin
2. Click "New" in left sidebar
3. Database name: `khoders_world`
4. Collation: `utf8mb4_general_ci`
5. Click "Create"

### Using MySQL CLI
```bash
# Open MySQL CLI from XAMPP
cd C:\xampp\mysql\bin
mysql -u root

# Create database
CREATE DATABASE khoders_world CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
exit
```

---

## Step 5: Import Database Schema

### Method 1: phpMyAdmin
1. Open phpMyAdmin
2. Select `khoders_world` database
3. Click "Import" tab
4. Choose file: `database/schema.sql`
5. Click "Go"
6. Repeat for migration files:
   - `skills_table.sql`
   - `resources_table.sql`
   - `add_featured_flags.sql`

### Method 2: MySQL CLI
```bash
cd C:\xampp\htdocs\khoders-website\database
mysql -u root khoders_world < schema.sql
mysql -u root khoders_world < skills_table.sql
mysql -u root khoders_world < resources_table.sql
mysql -u root khoders_world < add_featured_flags.sql
```

---

## Step 6: Configure Environment

### Create .env file
```bash
cd C:\xampp\htdocs\khoders-website
copy .env.example .env
```

### Edit .env
```env
# Database Configuration
DB_HOST=localhost
DB_PORT=3306
DB_NAME=khoders_world
DB_USER=root
DB_PASSWORD=

# Application
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost/khoders-website

# Session
SESSION_LIFETIME=120

# Email (for production)
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=your-email@gmail.com
SMTP_PASS=your-password
```

---

## Step 7: Set Permissions

### Windows
```cmd
# Right-click folders > Properties > Security
# Set write permissions for:
icacls "C:\xampp\htdocs\khoders-website\public\uploads" /grant Everyone:F
icacls "C:\xampp\htdocs\khoders-website\logs" /grant Everyone:F
```

### Linux/macOS
```bash
chmod -R 755 public/uploads
chmod -R 755 logs
chown -R daemon:daemon public/uploads
```

---

## Step 8: Verify Installation

### Test URLs
- **Homepage:** http://localhost/khoders-website/
- **phpMyAdmin:** http://localhost/phpmyadmin
- **Admin Panel:** http://localhost/khoders-website/admin/

### Check PHP Info
Create `info.php` in htdocs:
```php
<?php phpinfo(); ?>
```
Visit: http://localhost/info.php
Delete file after verification.

---

## Step 9: Admin Account Setup

### Default Credentials
- **Username:** admin
- **Password:** admin123

### Change Password (REQUIRED)
1. Login to admin panel
2. Go to Settings (if available) OR
3. Update via phpMyAdmin:
   - Table: `admin_users`
   - Use password hash generator

---

## Troubleshooting

### Apache Won't Start
```
Error: Port 80 in use
Solution:
1. Stop IIS/Skype
2. OR change Apache port:
   - Edit httpd.conf
   - Change: Listen 8080
   - Access: http://localhost:8080
```

### MySQL Won't Start
```
Error: Port 3306 in use
Solution:
1. Stop other MySQL instances
2. OR change MySQL port in my.ini
```

### Database Connection Failed
```
Check:
1. MySQL service running
2. .env credentials correct
3. Database exists
4. User has permissions
```

### "Access Denied" Error
```sql
-- Grant permissions
GRANT ALL PRIVILEGES ON khoders_world.* TO 'root'@'localhost';
FLUSH PRIVILEGES;
```

### Upload Directory Errors
```
Solution:
1. Create public/uploads folder
2. Set write permissions
3. Check php.ini upload_max_filesize
```

---

## Security Configuration (Production)

### Disable phpMyAdmin
```
# Rename phpmyadmin folder
mv C:\xampp\phpmyadmin C:\xampp\phpmyadmin_disabled
```

### Secure MySQL
```sql
-- Set root password
ALTER USER 'root'@'localhost' IDENTIFIED BY 'strong_password';

-- Remove anonymous users
DELETE FROM mysql.user WHERE User='';

-- Remove test database
DROP DATABASE IF EXISTS test;
DELETE FROM mysql.db WHERE Db='test' OR Db='test\\_%';

-- Reload privileges
FLUSH PRIVILEGES;
```

### Enable SSL (Optional)
1. Generate certificates
2. Edit httpd-ssl.conf
3. Enable SSL in XAMPP control panel

---

## Performance Optimization

### PHP Configuration
```ini
# Increase memory
memory_limit = 256M

# Enable opcache
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
```

### MySQL Configuration
```ini
# my.ini
innodb_buffer_pool_size = 256M
query_cache_size = 32M
```

---

## Backup & Restore

### Database Backup
```bash
# Backup
mysqldump -u root -p khoders_world > backup.sql

# Restore
mysql -u root -p khoders_world < backup.sql
```

### Full Backup
```bash
# Backup entire site
xcopy /E/I C:\xampp\htdocs\khoders-website C:\backup\khoders-website
```

---

## Development vs Production

### Development (.env)
```env
APP_ENV=development
APP_DEBUG=true
display_errors=On
```

### Production (.env)
```env
APP_ENV=production
APP_DEBUG=false
display_errors=Off
```

---

## Next Steps

1. ✅ Verify all services running
2. ✅ Import database successfully
3. ✅ Test homepage loads
4. ✅ Login to admin panel
5. ✅ Change default password
6. ✅ Add initial content
7. ✅ Test member registration
8. ✅ Configure email (production)

---

**Need Help?**
- Check XAMPP forums: https://community.apachefriends.org
- PHP documentation: https://php.net
- MySQL documentation: https://dev.mysql.com

**Success!** Your Khoders World platform is now running on XAMPP! 🚀
