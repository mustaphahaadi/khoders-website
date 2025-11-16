# AUDIT ACTION PLAN
## Prioritized Fixes with Implementation Details

---

## PHASE 1: CRITICAL FIXES (Day 1-2)

### 1.1 Database Configuration Consolidation

**File to Delete:**
- `database/config.php`

**Files to Update:**
- `database/db_functions.php`

**Changes:**
```php
// OLD (database/db_functions.php line 8):
require_once 'config.php';

// NEW:
require_once __DIR__ . '/../config/database.php';

// Replace getDBConnection() function:
function getDBConnection() {
    $database = Database::getInstance();
    return $database->getConnection();
}
```

### 1.2 Add Missing Admins Table

**File:** `database/schema_updates.sql`

```sql
-- Create admins table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'editor') DEFAULT 'editor',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
);

-- Insert default admin (password: ChangeMe123!)
INSERT INTO admins (username, email, password_hash, role) VALUES
('admin', 'admin@khodersclub.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
```

### 1.3 Secure Environment Configuration

**Actions:**
1. Add `.env` to `.gitignore` if not already there
2. Remove `.env` from git history:
```bash
git rm --cached .env
git commit -m "Remove .env from repository"
```

3. Update `.env.example` with clear instructions

### 1.4 Force Password Change on First Login

**File:** `admin/login.php`

Add after successful login:
```php
if (Auth::login($username, $password)) {
    // Check if using default password
    if ($password === 'admin123' || $password === 'ChangeMe123!') {
        $_SESSION['force_password_change'] = true;
        header('Location: index.php?route=profile&action=change-password');
        exit;
    }
    header('Location: index.php');
    exit;
}
```

---

## PHASE 2: DATABASE SCHEMA FIXES (Day 3-4)

### 2.1 Standardize Members Table

```sql
-- Remove duplicate name column
ALTER TABLE members DROP COLUMN IF EXISTS name;

-- Add updated_at if missing
ALTER TABLE members ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Ensure level column uses correct ENUM
ALTER TABLE members MODIFY COLUMN level ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner';

-- Rename experience to level for consistency
ALTER TABLE members CHANGE COLUMN experience level ENUM('beginner', 'intermediate', 'advanced');
```

### 2.2 Standardize Team Members Table

```sql
-- Remove duplicate photo_url column
ALTER TABLE team_members DROP COLUMN IF EXISTS photo_url;

-- Rename profile_image to image_url for consistency
ALTER TABLE team_members CHANGE COLUMN profile_image image_url VARCHAR(500);

-- Remove duplicate name column
ALTER TABLE team_members DROP COLUMN IF EXISTS name;

-- Add full_name computed column or generate from first_name + last_name
ALTER TABLE team_members ADD COLUMN full_name VARCHAR(200) GENERATED ALWAYS AS (CONCAT(first_name, ' ', last_name)) STORED;
```

### 2.3 Standardize Events Table

```sql
-- Remove duplicate date/time columns
ALTER TABLE events DROP COLUMN IF EXISTS date;
ALTER TABLE events DROP COLUMN IF EXISTS time;

-- Ensure event_date is the only datetime field
ALTER TABLE events MODIFY COLUMN event_date DATETIME NOT NULL;

-- Add category if missing
ALTER TABLE events ADD COLUMN IF NOT EXISTS category VARCHAR(100);
```

### 2.4 Add Missing Columns

```sql
-- Add slug to blog_posts for SEO
ALTER TABLE blog_posts ADD COLUMN slug VARCHAR(300) UNIQUE;
ALTER TABLE blog_posts ADD INDEX idx_slug (slug);

-- Add syllabus to courses
ALTER TABLE courses ADD COLUMN syllabus TEXT;
ALTER TABLE courses ADD COLUMN prerequisites TEXT;

-- Add site_settings table
CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('text', 'number', 'boolean', 'json') DEFAULT 'text',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_key (setting_key)
);

-- Insert default settings
INSERT INTO site_settings (setting_key, setting_value, setting_type) VALUES
('site_name', 'KHODERS WORLD', 'text'),
('site_email', 'info@khodersclub.com', 'text'),
('site_phone', '+233 50 123 4567', 'text'),
('site_address', 'Kumasi Technical University, Kumasi, Ghana', 'text'),
('facebook_url', '#', 'text'),
('twitter_url', '#', 'text'),
('instagram_url', '#', 'text'),
('linkedin_url', '#', 'text'),
('maintenance_mode', '0', 'boolean');
```

---

## PHASE 3: SECURITY ENHANCEMENTS (Day 5-7)

### 3.1 Add CSRF Tokens to All Forms

**Files to Update:**
- All `pages/*.html` files with forms
- All admin editor pages

**Template:**
```php
<?php 
require_once __DIR__ . '/../config/csrf.php';
$csrfToken = CSRFToken::generate();
?>

<form method="POST" action="...">
    <?php echo CSRFToken::getFieldHTML(); ?>
    <!-- rest of form -->
</form>
```

### 3.2 Add Input Validation Functions

**File:** `config/validation.php` (NEW)

```php
<?php
class Validator {
    public static function phone($phone) {
        // Ghana phone format: +233 XX XXX XXXX
        return preg_match('/^\+?233\d{9}$/', str_replace([' ', '-'], '', $phone));
    }
    
    public static function studentId($id) {
        // Format: XXXX-XXXX or similar
        return preg_match('/^[A-Z0-9]{4,}-?[A-Z0-9]{4,}$/i', $id);
    }
    
    public static function url($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
    
    public static function slug($slug) {
        return preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug);
    }
}
```

### 3.3 Implement Rate Limiting

**File:** `config/rate-limiter.php` (NEW)

```php
<?php
class RateLimiter {
    private static $store = [];
    
    public static function check($identifier, $maxAttempts = 5, $decayMinutes = 1) {
        $key = 'rate_limit_' . $identifier;
        $now = time();
        
        if (!isset(self::$store[$key])) {
            self::$store[$key] = ['attempts' => 0, 'reset_at' => $now + ($decayMinutes * 60)];
        }
        
        $data = self::$store[$key];
        
        if ($now > $data['reset_at']) {
            self::$store[$key] = ['attempts' => 1, 'reset_at' => $now + ($decayMinutes * 60)];
            return true;
        }
        
        if ($data['attempts'] >= $maxAttempts) {
            return false;
        }
        
        self::$store[$key]['attempts']++;
        return true;
    }
}
```

---

## PHASE 4: ADMIN PANEL COMPLETION (Day 8-12)

### 4.1 Create Admin User Management

**File:** `admin/pages/admin-users.php` (NEW)

Features to implement:
- List all admin users
- Add new admin
- Edit admin details
- Change admin password
- Delete admin (with confirmation)
- View last login time

### 4.2 Complete CRUD Operations

**Files to Update:**
- `admin/pages/blog-editor.php` - Add save functionality
- `admin/pages/course-editor.php` - Add save functionality
- `admin/pages/event-editor.php` - Verify save works
- `admin/pages/project-editor.php` - Verify save works
- `admin/pages/team-editor.php` - Verify save works

### 4.3 Add Bulk Operations

**Features:**
- Bulk delete (checkbox selection)
- Bulk export (CSV/Excel)
- Bulk status change

### 4.4 Create Dashboard Helper

**File:** `admin/includes/dashboard.php` (NEW)

```php
<?php
class Dashboard {
    private $db;
    
    public function __construct() {
        require_once __DIR__ . '/../../config/database.php';
        $database = Database::getInstance();
        $this->db = $database->getConnection();
    }
    
    public function getStats() {
        // Implementation
    }
    
    public function getRecentMembers($limit = 5) {
        // Implementation
    }
    
    public function getRecentLogs($limit = 5) {
        // Implementation
    }
    
    public function getMonthlyStats($months = 6) {
        // Implementation
    }
}
```

---

## PHASE 5: API STANDARDIZATION (Day 13-15)

### 5.1 Create API Response Helper

**File:** `api/ApiResponse.php` (NEW)

```php
<?php
class ApiResponse {
    public static function success($data = null, $message = '', $meta = []) {
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => $meta
        ]);
        exit;
    }
    
    public static function error($message, $code = 400, $errors = []) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ]);
        exit;
    }
}
```

### 5.2 Update All API Endpoints

Update to use ApiResponse class:
- `api/events-list.php`
- `api/team-list.php`
- `api/projects-list.php`
- `api/blog-list.php`
- `api/courses-list.php`

### 5.3 Add Missing API Endpoints

Create:
- `api/event-details.php?id=X`
- `api/blog-details.php?slug=X`
- `api/course-details.php?id=X`
- `api/search.php?q=query`

---

## PHASE 6: FRONTEND IMPROVEMENTS (Day 16-18)

### 6.1 Migrate HTML to PHP Templates

Convert all `.html` files to use template system:
1. Extract `<main>` content
2. Create corresponding `.php` file
3. Use `render_page()` function
4. Delete `.html` file

### 6.2 Create Missing Templates

**Files to Create:**
- `pages/blog-template.php`
- `pages/courses-template.php`
- `pages/blog-details-template.php`

### 6.3 Implement Site Settings

**File:** `includes/settings.php` (NEW)

```php
<?php
class SiteSettings {
    private static $settings = null;
    
    public static function get($key, $default = '') {
        if (self::$settings === null) {
            self::load();
        }
        return self::$settings[$key] ?? $default;
    }
    
    private static function load() {
        // Load from database
    }
}
```

Update footer.php and navigation.php to use SiteSettings::get()

---

## PHASE 7: MISSING FEATURES (Day 19-23)

### 7.1 Password Reset System

**Files to Create:**
- `forgot-password.php`
- `reset-password.php`
- `config/password-reset.php`

Add password_reset_tokens table:
```sql
CREATE TABLE password_reset_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_token (token),
    INDEX idx_email (email)
);
```

### 7.2 Search Functionality

**File:** `api/search.php` (NEW)

Implement full-text search across:
- Blog posts
- Events
- Courses
- Team members
- Projects

### 7.3 Export Functionality

**File:** `admin/includes/export.php` (NEW)

Add CSV export for:
- Members list
- Contacts
- Newsletter subscribers
- Form logs

### 7.4 Member Dashboard

**Files to Create:**
- `member/login.php`
- `member/dashboard.php`
- `member/profile.php`
- `member/events.php`

---

## TESTING CHECKLIST

After each phase, test:

### Database Tests:
- [ ] All tables exist
- [ ] No duplicate columns
- [ ] Foreign keys work
- [ ] Indexes improve query speed

### Security Tests:
- [ ] CSRF tokens validate
- [ ] SQL injection attempts fail
- [ ] XSS attempts sanitized
- [ ] Rate limiting works
- [ ] Password requirements enforced

### Functionality Tests:
- [ ] All forms submit successfully
- [ ] All CRUD operations work
- [ ] File uploads work
- [ ] Email sending works
- [ ] API endpoints return correct data

### UI/UX Tests:
- [ ] All pages load without errors
- [ ] Navigation works on all pages
- [ ] Forms show validation errors
- [ ] Success messages display
- [ ] Mobile responsive

---

## DEPLOYMENT CHECKLIST

Before production:
- [ ] Change all default passwords
- [ ] Set APP_ENV=production
- [ ] Set APP_DEBUG=false
- [ ] Configure SMTP settings
- [ ] Enable HTTPS redirect
- [ ] Set up database backups
- [ ] Configure error logging
- [ ] Test all critical paths
- [ ] Run security scan
- [ ] Update documentation

---

## MAINTENANCE PLAN

### Daily:
- Monitor error logs
- Check form submissions
- Review security logs

### Weekly:
- Database backup
- Update dependencies
- Review analytics

### Monthly:
- Security audit
- Performance review
- User feedback review
- Feature planning

---

**Document Version:** 1.0  
**Last Updated:** December 2024  
**Status:** READY FOR IMPLEMENTATION
