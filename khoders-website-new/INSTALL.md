# KHODERS Website Installation Guide

This document provides detailed instructions for setting up the KHODERS website on your server.

## System Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache or Nginx web server
- mod_rewrite enabled (for Apache)
- GD and Fileinfo PHP extensions

## Installation Steps

### 1. Server Setup

#### Apache Configuration
Make sure your Apache server has the following modules enabled:
- mod_rewrite
- mod_headers
- mod_expires

#### PHP Configuration
Required PHP extensions:
- mysqli or pdo_mysql
- gd
- fileinfo
- json
- mbstring

### 2. Database Setup

1. Create a MySQL database for KHODERS website
2. Import the database schema:
   ```
   # Option 1: Using the web interface
   Navigate to http://your-domain.com/database/setup.php
   
   # Option 2: Using MySQL command line
   mysql -u your_username -p your_database < database/schema.sql
   mysql -u your_username -p your_database < database/schema_updates.sql
   ```

3. Create a database user with appropriate permissions:
   ```sql
   CREATE USER 'khoders_user'@'localhost' IDENTIFIED BY 'your_secure_password';
   GRANT SELECT, INSERT, UPDATE, DELETE ON khoders_db.* TO 'khoders_user'@'localhost';
   FLUSH PRIVILEGES;
   ```

### 3. Application Configuration

1. Copy the environment configuration:
   ```
   cp .env.example .env
   ```

2. Edit the `.env` file with your database credentials:
   ```
   DB_HOST=localhost
   DB_NAME=khoders_db
   DB_USER=khoders_user
   DB_PASS=your_secure_password
   
   ADMIN_USERNAME=admin
   ADMIN_PASSWORD=your_secure_admin_password
   ```

3. Set appropriate file permissions:
   ```
   chmod 755 -R .
   chmod 644 -R *.html *.php *.css *.js
   chmod 755 -R assets/ forms/ database/ config/
   chmod 777 -R logs/
   ```

### 4. Web Server Configuration

#### Apache
Ensure your `.htaccess` file is properly configured:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    
    # Redirect to HTTPS (uncomment in production)
    # RewriteCond %{HTTPS} off
    # RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
    
    # Protect sensitive files
    RewriteRule ^(.*/)?\.env$ - [F,L]
    RewriteRule ^logs/.* - [F,L]
    
    # Handle non-existent files/directories
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ 404.html [L]
</IfModule>

# Additional security headers
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-XSS-Protection "1; mode=block"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>
```

#### Nginx
For Nginx, use the following configuration:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/khoders-website;
    index index.php index.html;
    
    location / {
        try_files $uri $uri/ /404.html;
    }
    
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
    }
    
    # Protect sensitive files
    location ~ /\.env {
        deny all;
    }
    
    location ~ /logs/ {
        deny all;
    }
    
    # Security headers
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Frame-Options "SAMEORIGIN";
    add_header Referrer-Policy "strict-origin-when-cross-origin";
}
```

### 5. Post-Installation Tasks

1. **Change Default Admin Password**
   - Log in to the admin panel at `http://your-domain.com/admin/`
   - Use the default credentials (admin/admin123)
   - Immediately change the password

2. **Configure Email Settings**
   - Update email recipients in form handlers
   - Set up SMTP if you want to send actual emails

3. **Test Forms**
   - Test the contact form
   - Test the registration form
   - Test the newsletter subscription form

4. **Review Security Settings**
   - See `SECURITY.md` for security best practices
   - Ensure `.env` file is not accessible from web

## Troubleshooting

### Form Submissions Not Working
- Check permissions on the logs directory
- Verify PHP Email Form library is properly included
- Check browser console for JavaScript errors

### Database Connection Issues
- Confirm database credentials in `.env` file
- Ensure MySQL server is running
- Check if the user has proper permissions

### Admin Panel Access Problems
- Clear browser cache and cookies
- Verify PHP session configuration
- Check file permissions on admin files

## Contact & Support

For additional help, contact:
- Technical Support: tech@khodersclub.com
- Admin: admin@khodersclub.com

## Updating

To update the website:
1. Backup your database and files
2. Replace the files with the new version
3. Run any database migrations
4. Clear the browser cache
