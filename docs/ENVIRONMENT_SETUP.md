# Environment Configuration Guide - KHODERS WORLD

**Last Updated:** Current Session  
**Status:** Production Ready  
**Scope:** Database, Email, Security, API, File Upload

---

## Overview

The KHODERS WORLD application uses environment variables to manage sensitive configuration without hardcoding values in the source code. This guide explains how to set up and use environment variables for secure deployment.

### Configuration Methods

1. **`.env` File** (Development)

   - Create `.env` from `.env.example`
   - Loaded by `config/env.php`
   - Not committed to version control

2. **System Environment Variables** (Production)

   - Set via server configuration (Apache, Nginx)
   - Docker environment variables
   - Container orchestration platforms

3. **PHP Configuration** (Fallback)
   - Default values in `config/database.php`
   - Used if environment variables not set

---

## Setting Up Your Environment

### Step 1: Copy Example Configuration

```bash
# In project root directory
cp .env.example .env
```

### Step 2: Update Environment Variables

Edit `.env` with your actual configuration:

```dotenv
# Database Configuration
DB_HOST=your-db-server.com
DB_NAME=your_database_name
DB_USER=your_db_user
DB_PASS=your_db_password

# Application Settings
APP_ENV=production
APP_DEBUG=false
APP_URL=https://khodersworld.com
```

### Step 3: Secure the File

```bash
# Restrict file permissions (Linux/Mac)
chmod 600 .env

# Verify permissions
ls -la .env
# Should show: -rw------- (owner only)
```

### Step 4: Test Connection

Run initialization script to verify configuration:

```bash
php config/init.php
```

Expected output:

```
Database tables created successfully!
Sample events inserted successfully!
```

---

## Configuration Variables Reference

### Database Configuration

#### `DB_HOST` (Required)

- **Description:** MySQL/MariaDB server hostname or IP
- **Default:** `localhost`
- **Examples:**
  - `localhost` - Local development
  - `127.0.0.1` - Local IP
  - `db.example.com` - Remote server
  - `db-service` - Docker container name

#### `DB_NAME` (Required)

- **Description:** Database name
- **Default:** `khoders_db`
- **Constraints:** MySQL naming rules apply
- **Example:** `khoders_db`, `khoders_prod`, `khoders_staging`

#### `DB_USER` (Required)

- **Description:** Database username
- **Default:** `root` (development only)
- **Security Note:** Use dedicated application user in production
- **Example:** `khoders_app`, `app_user`, `www-data`

#### `DB_PASS` (Required)

- **Description:** Database password
- **Default:** Empty string (for root, not recommended)
- **Security Note:** Use strong, randomly generated password
- **Format:** Can contain any characters; enclose in quotes if special chars present

**Example Secure Setup:**

```dotenv
DB_HOST=db.khodersworld.com
DB_NAME=khoders_prod
DB_USER=khoders_app
DB_PASS=P@ssw0rd!#$%^&*_SecurePassword123
```

---

### Application Settings

#### `APP_ENV` (Required)

- **Description:** Application environment
- **Allowed Values:** `development`, `production`, `staging`, `testing`
- **Default:** `development`
- **Impact:**
  - `development`: Verbose errors, debug logging enabled
  - `production`: Generic error messages, minimal logging
  - `staging`: Production-like with enhanced logging
  - `testing`: Test mode with fixtures

**Security:** Always use `production` in production deployments!

#### `APP_DEBUG` (Optional)

- **Description:** Enable debug mode
- **Allowed Values:** `true`, `false`
- **Default:** `false` (if APP_ENV is production)
- **Impact:** Controls error message verbosity

**Warning:** Never enable in production (security risk)

#### `APP_URL` (Required)

- **Description:** Application's public URL
- **Examples:**
  - Development: `http://localhost`
  - Staging: `https://staging.khodersworld.com`
  - Production: `https://khodersworld.com`
- **Used For:** Email links, CORS headers, redirects

---

### Email Configuration

#### `MAIL_HOST` (Required for Email)

- **Description:** SMTP server hostname
- **Default:** `smtp.gmail.com`
- **Common Values:**
  - `smtp.gmail.com` - Gmail SMTP
  - `smtp.SendGrid.net` - SendGrid
  - `mail.yourdomain.com` - Custom mail server
  - `smtp.mailtrap.io` - Mailtrap (testing)

#### `MAIL_PORT` (Required for Email)

- **Description:** SMTP server port
- **Default:** `587`
- **Common Values:**
  - `587` - TLS encryption (recommended)
  - `465` - SSL encryption
  - `25` - Plain text (not recommended)
  - `2525` - Alternative TLS

#### `MAIL_USERNAME` (Required for Email)

- **Description:** SMTP authentication username
- **Example:** `your-email@gmail.com`

#### `MAIL_PASSWORD` (Required for Email)

- **Description:** SMTP authentication password
- **Note:** For Gmail, use App-specific password, not account password
- **How to Create Gmail App Password:**
  1. Enable 2-factor authentication on Google account
  2. Go to myaccount.google.com/apppasswords
  3. Generate app-specific password
  4. Use generated password here

#### `MAIL_FROM_ADDRESS` (Required for Email)

- **Description:** Sender email address
- **Example:** `noreply@khodersworld.com`
- **Must Be:** Valid email address, ideally from your domain

#### `MAIL_FROM_NAME` (Required for Email)

- **Description:** Sender display name
- **Example:** `KHODERS World`, `KHODERS Support`
- **Displayed In:** Email client "From" field

**Complete Email Configuration Example:**

```dotenv
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=khoders@gmail.com
MAIL_PASSWORD=xyzabc123xyz456abc
MAIL_FROM_ADDRESS=noreply@khodersworld.com
MAIL_FROM_NAME="KHODERS World Support"
```

---

### Security Configuration

#### `JWT_SECRET` (Optional)

- **Description:** Secret key for JWT token signing
- **Required If:** Using JWT authentication
- **Generation:** Use strong random string
  ```bash
  # Generate random key
  php -r "echo base64_encode(random_bytes(32));"
  ```
- **Length:** Minimum 32 characters

#### `ENCRYPTION_KEY` (Optional)

- **Description:** Key for data encryption
- **Required If:** Encrypting sensitive data
- **Length:** Must be exactly 32 characters (256-bit)
- **Generation:**
  ```bash
  # Generate 32-character key
  php -r "echo bin2hex(random_bytes(16));"
  ```

---

### API & Rate Limiting

#### `RATE_LIMIT_REQUESTS` (Optional)

- **Description:** Maximum requests allowed
- **Default:** `100`
- **Range:** Any positive integer
- **Example:**
  - `5` - Very strict (for login attempts)
  - `100` - Standard API rate limit
  - `1000` - Generous for public endpoints

#### `RATE_LIMIT_WINDOW` (Optional)

- **Description:** Time window in seconds
- **Default:** `3600` (1 hour)
- **Examples:**
  - `60` - Per minute rate limiting
  - `300` - Per 5 minutes
  - `3600` - Per hour

**Example Rate Limiting:**

```dotenv
# Allow 3 contact submissions per 5 minutes
RATE_LIMIT_REQUESTS=3
RATE_LIMIT_WINDOW=300
```

---

### File Upload Settings

#### `MAX_FILE_SIZE` (Optional)

- **Description:** Maximum file upload size in bytes
- **Default:** `5242880` (5MB)
- **Common Values:**
  - `1048576` - 1MB
  - `5242880` - 5MB
  - `10485760` - 10MB
  - `104857600` - 100MB
- **Also Check:** Server limits in php.ini

#### `ALLOWED_FILE_TYPES` (Optional)

- **Description:** Comma-separated list of allowed file extensions
- **Default:** `jpg,jpeg,png,gif,pdf,doc,docx`
- **Security Note:** Validates file extension, not MIME type

**File Extension Examples:**

```dotenv
# Images only
ALLOWED_FILE_TYPES=jpg,jpeg,png,gif,webp,svg

# Documents only
ALLOWED_FILE_TYPES=pdf,doc,docx,xlsx,ppt,pptx

# All common types
ALLOWED_FILE_TYPES=jpg,jpeg,png,gif,pdf,doc,docx,xlsx,ppt,pptx,txt,zip
```

---

## Environment-Specific Configurations

### Development Environment

```dotenv
# Development Configuration
DB_HOST=localhost
DB_NAME=khoders_dev
DB_USER=root
DB_PASS=

APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8000

MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_FROM_ADDRESS=dev@khodersworld.local
MAIL_FROM_NAME="KHODERS Dev"

RATE_LIMIT_REQUESTS=1000
RATE_LIMIT_WINDOW=3600
MAX_FILE_SIZE=104857600
ALLOWED_FILE_TYPES=jpg,jpeg,png,gif,pdf,doc,docx,xlsx,zip
```

### Staging Environment

```dotenv
# Staging Configuration
DB_HOST=staging-db.khodersworld.com
DB_NAME=khoders_staging
DB_USER=staging_app
DB_PASS=Staging@P@ssw0rd123!

APP_ENV=staging
APP_DEBUG=false
APP_URL=https://staging.khodersworld.com

MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=staging@khodersworld.com
MAIL_PASSWORD=xxx_app_password_xxx
MAIL_FROM_ADDRESS=staging@khodersworld.com
MAIL_FROM_NAME="KHODERS Staging"

RATE_LIMIT_REQUESTS=100
RATE_LIMIT_WINDOW=3600
MAX_FILE_SIZE=10485760
ALLOWED_FILE_TYPES=jpg,jpeg,png,gif,pdf,doc,docx
```

### Production Environment

```dotenv
# Production Configuration (NEVER commit to repo!)
DB_HOST=prod-db.khodersworld.com
DB_NAME=khoders_prod
DB_USER=khoders_prod_app
DB_PASS=SuperSecure!P@ssw0rd#2024$%^&*

APP_ENV=production
APP_DEBUG=false
APP_URL=https://khodersworld.com

MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=SG.xxx_sendgrid_api_key_xxx
MAIL_FROM_ADDRESS=noreply@khodersworld.com
MAIL_FROM_NAME="KHODERS World"

RATE_LIMIT_REQUESTS=50
RATE_LIMIT_WINDOW=3600
MAX_FILE_SIZE=5242880
ALLOWED_FILE_TYPES=jpg,jpeg,png,gif,pdf
```

---

## Loading Environment Variables

### In PHP Code

```php
<?php
// Load environment variables
require_once 'config/env.php';

// Access variables
$db_host = getenv('DB_HOST');
$db_name = getenv('DB_NAME');
$mail_host = getenv('MAIL_HOST');

// With default fallback
$port = getenv('MAIL_PORT') ?: 587;
?>
```

### In Configuration Classes

```php
<?php
// config/database.php
$host = getenv('DB_HOST') ?: 'localhost';
$db_name = getenv('DB_NAME') ?: 'khoders_db';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
?>
```

---

## Validation & Startup Checks

### Configuration Validation Script

Create `config/validate-env.php`:

```php
<?php
/**
 * Environment Configuration Validator
 * Checks for required and recommended configuration
 */

require_once __DIR__ . '/env.php';

class EnvironmentValidator {
    private $required = [
        'DB_HOST' => 'Database host',
        'DB_NAME' => 'Database name',
        'DB_USER' => 'Database user',
        'APP_ENV' => 'Application environment'
    ];

    private $optional = [
        'MAIL_HOST' => 'Mail server (required for email features)',
        'MAIL_PORT' => 'Mail server port',
        'JWT_SECRET' => 'JWT secret (if using JWT auth)'
    ];

    public function validate() {
        $errors = [];
        $warnings = [];

        // Check required variables
        foreach ($this->required as $var => $description) {
            if (empty(getenv($var))) {
                $errors[] = "Missing required: $var ($description)";
            }
        }

        // Check optional variables
        foreach ($this->optional as $var => $description) {
            if (empty(getenv($var))) {
                $warnings[] = "Missing optional: $var ($description)";
            }
        }

        // Validate specific values
        if (getenv('APP_ENV') && !in_array(getenv('APP_ENV'), ['development', 'production', 'staging', 'testing'])) {
            $errors[] = "Invalid APP_ENV value: " . getenv('APP_ENV');
        }

        if (getenv('DB_PASS') === '') {
            $warnings[] = "Database password is empty (development only)";
        }

        // Display results
        if (!empty($errors)) {
            echo "CONFIGURATION ERRORS:\n";
            foreach ($errors as $error) {
                echo "  ✗ $error\n";
            }
            return false;
        }

        if (!empty($warnings)) {
            echo "CONFIGURATION WARNINGS:\n";
            foreach ($warnings as $warning) {
                echo "  ⚠ $warning\n";
            }
        }

        echo "✓ Configuration validated successfully\n";
        return true;
    }
}

// Run validation
$validator = new EnvironmentValidator();
if (!$validator->validate()) {
    exit(1);
}
?>
```

---

## Docker Configuration

### Docker Environment Variables

```dockerfile
# Dockerfile
FROM php:8.1-apache

# Set environment variables
ENV DB_HOST=db
ENV DB_NAME=khoders_db
ENV DB_USER=app_user
ENV DB_PASS=secure_password
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV APP_URL=https://khodersworld.com
```

### Docker Compose

```yaml
# docker-compose.yml
version: "3.8"

services:
  web:
    image: php:8.1-apache
    environment:
      - DB_HOST=db
      - DB_NAME=khoders_db
      - DB_USER=app_user
      - DB_PASS=secure_password
      - APP_ENV=production
      - MAIL_HOST=smtp.sendgrid.net
      - MAIL_PORT=587
    ports:
      - "80:80"
    depends_on:
      - db

  db:
    image: mysql:8.0
    environment:
      - MYSQL_DATABASE=khoders_db
      - MYSQL_USER=app_user
      - MYSQL_PASSWORD=secure_password
      - MYSQL_ROOT_PASSWORD=root_password
    volumes:
      - db_data:/var/lib/mysql

volumes:
  db_data:
```

---

## Server Configuration

### Apache (.htaccess)

```apache
# .htaccess
# Set environment variables for Apache
SetEnv DB_HOST "your-db-server.com"
SetEnv DB_NAME "khoders_db"
SetEnv DB_USER "app_user"
SetEnv DB_PASS "secure_password"
SetEnv APP_ENV "production"
```

### Nginx (PHP-FPM)

```nginx
# nginx.conf or site config
server {
    listen 80;
    server_name khodersworld.com;

    location ~ \.php$ {
        fastcgi_pass php-fpm:9000;
        fastcgi_param DB_HOST "your-db-server.com";
        fastcgi_param DB_NAME "khoders_db";
        fastcgi_param DB_USER "app_user";
        fastcgi_param DB_PASS "secure_password";
        fastcgi_param APP_ENV "production";
    }
}
```

---

## Security Best Practices

### ✅ DO

- Use strong, random passwords (minimum 16 characters)
- Use HTTPS URLs in production (https://, not http://)
- Restrict `.env` file permissions (chmod 600)
- Store sensitive values in environment variables
- Use dedicated database users (not root)
- Rotate passwords regularly
- Use different credentials per environment
- Keep `.env` out of version control (add to .gitignore)

### ❌ DON'T

- Commit `.env` files to Git
- Use generic passwords (password123, admin, etc.)
- Share credentials across environments
- Use root database user in production
- Log sensitive values
- Email configurations in plain text
- Use weak encryption keys
- Enable debug mode in production

---

## Troubleshooting

### Issue: "Database connection failed"

**Check:**

1. `DB_HOST` - Can you reach the server?
   ```bash
   ping your-db-server.com
   ```
2. `DB_USER` and `DB_PASS` - Are credentials correct?
3. Database exists - Does `DB_NAME` database exist?
4. Port - Is MySQL running on correct port?

**Solution:**

```bash
# Test MySQL connection
mysql -h your-db-server.com -u app_user -p your_db_name
```

### Issue: "Email not sending"

**Check:**

1. `MAIL_HOST` - Is SMTP server accessible?
2. `MAIL_PORT` - Is port correct? (587 for TLS, 465 for SSL)
3. `MAIL_USERNAME` and `MAIL_PASSWORD` - Are credentials correct?
4. Firewall - Is port 587/465 open?

**Solution:**

```bash
# Test SMTP connection
telnet your-smtp-server.com 587
```

### Issue: "Environment variables not loading"

**Check:**

1. `.env` file exists in project root
2. `config/env.php` is included
3. File permissions allow reading
4. Check with:
   ```php
   <?php
   var_dump(getenv('DB_HOST'));
   var_dump($_ENV);
   ?>
   ```

---

## Command Reference

### Generate Secure Passwords

```bash
# Generate random 32-character password
openssl rand -base64 24

# Generate random 64-character password
openssl rand -base64 32

# PHP method
php -r "echo bin2hex(random_bytes(16));"
```

### Copy .env Template

```bash
cp .env.example .env
```

### Test Configuration

```bash
php config/init.php
```

### Validate Environment

```bash
php config/validate-env.php
```

---

## References

- [PHP Environment Variables](https://www.php.net/manual/en/reserved.variables.environment.php)
- [MySQL Connection String](https://dev.mysql.com/doc/connector-python/en/connector-python-connectargs.html)
- [SMTP Configuration Guide](https://www.smtpclient.com/)
- [Docker Environment Variables](https://docs.docker.com/compose/environment-variables/)

---

## Support

For configuration issues:

1. Check this guide first
2. Review error logs in `logs/` directory
3. Run validation script: `php config/validate-env.php`
4. Contact your system administrator

---

**Status:** Production Ready  
**Last Updated:** Current Session  
**Maintained By:** KHODERS Development Team
