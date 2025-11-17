# KHODERS Website - Priority Fixes Report

## IMMEDIATE (Security) - CRITICAL

### 1. Path Traversal in forms/*.php files
**Files:** `forms/contact.php`, `forms/register.php`, `forms/newsletter.php`
**Issue:** Using `file_exists()` with user-controlled paths without proper validation
**Lines:** contact.php:22, register.php:22, newsletter.php:22

**Current Code:**
```php
if( file_exists($php_email_form = '../assets/vendor/php-email-form/php-email-form.php' )) {
    include( $php_email_form );
}
```

**Fix:** Use whitelist validation
```php
$allowed_file = realpath(__DIR__ . '/../assets/vendor/php-email-form/php-email-form.php');
$base_dir = realpath(__DIR__ . '/../assets/vendor/');
if ($allowed_file && $base_dir && strpos($allowed_file, $base_dir) === 0 && file_exists($allowed_file)) {
    include $allowed_file;
} else {
    die('Unable to load the PHP Email Form Library!');
}
```

---

### 2. XSS in Admin Form Outputs
**Files:** `admin/pages/form-logs.php`, `admin/pages/admin-users.php`, `admin/pages/contacts.php`
**Issue:** Direct output of user data without HTML escaping

**Example - form-logs.php line 253:**
```php
echo $row['email'];  // VULNERABLE
```

**Fix:** Use htmlspecialchars()
```php
echo htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8');
```

**Affected Lines in form-logs.php:**
- 253, 260, 308, 326, 335, 339, 343-354, 375-403, 491-519

**Affected Lines in admin-users.php:**
- 92-93

**Affected Lines in contacts.php:**
- 156-179

---

### 3. CORS Header Configuration
**File:** `config/api-wrapper.php`
**Issue:** Line 47 sets `Access-Control-Allow-Origin: *` as fallback, allowing any origin

**Current Code (line 47):**
```php
header('Access-Control-Allow-Origin: *');
```

**Fix:** Remove wildcard fallback
```php
// Only set CORS header if origin is in whitelist
if (in_array($requestOrigin, $allowedOrigins)) {
    header('Access-Control-Allow-Origin: ' . $requestOrigin);
} else {
    // Don't set CORS header for non-whitelisted origins
    http_response_code(403);
    exit('CORS policy violation');
}
```

---

### 4. File Include Validation with Whitelist
**File:** `includes/router.php` and `admin/includes/router.php`
**Issue:** Dynamic file inclusion without proper whitelist validation

**includes/router.php - Lines 64-88:**
```php
// VULNERABLE: No whitelist validation
$filePath = self::$pages[$page];
if (file_exists($filePath)) {
    include $filePath;
}
```

**Fix:** Implement whitelist validation
```php
// Define whitelist of allowed pages
$allowedPages = [
    'about', 'blog', 'blog-details', 'careers', 'code-of-conduct',
    'contact', 'courses', 'course-details', 'enroll', 'events',
    'faq', 'instructors', 'join-program', 'login', 'membership-tiers',
    'mentor-profile', 'privacy-policy', 'programs', 'program-details',
    'projects', 'register', 'resources', 'services', 'team',
    'terms-of-service', '404'
];

if (!in_array($page, $allowedPages, true)) {
    header('HTTP/1.0 404 Not Found');
    exit;
}

$filePath = self::$pages[$page];
$realPath = realpath($filePath);
$basePath = realpath('pages');

if ($realPath && $basePath && strpos($realPath, $basePath) === 0 && file_exists($filePath)) {
    include $filePath;
}
```

---

## HIGH (Critical)

### 5. Database Column Mismatches
**File:** `admin/includes/api.php`
**Issue:** Column names inconsistency between code and database schema

**Lines 166-167 (createMember):**
```php
// Code uses: first_name, last_name, student_id, additional_info
// But database may expect: firstName, lastName, studentId, notes
```

**Fix:** Standardize column names in database schema to snake_case:
```sql
ALTER TABLE members 
CHANGE COLUMN firstName first_name VARCHAR(100),
CHANGE COLUMN lastName last_name VARCHAR(100),
CHANGE COLUMN studentId student_id VARCHAR(50),
CHANGE COLUMN notes additional_info TEXT;
```

---

### 6. Missing Error Responses (Blank Pages)
**Files:** Multiple pages with `die()` statements
**Issue:** Using `die()` without proper error responses

**Examples:**
- `forms/contact.php:24` - `die('CSRF token validation failed...')`
- `forms/register.php:14` - `die('CSRF token validation failed...')`
- `forms/newsletter.php:14` - `die('CSRF token validation failed...')`

**Fix:** Return proper JSON error responses
```php
// Instead of die()
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST[CSRFToken::getTokenName()]) || !CSRFToken::validate()) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'CSRF token validation failed']);
        exit;
    }
}
```

---

### 7. File Upload Validation
**File:** `admin/includes/api.php`
**Issue:** No file type validation in upload handlers

**Lines 123-124, 166-167, 212-213, 246-247, 258-259, 287-288, 404-405, 623-624, 731-732:**
Missing validation for:
- File MIME type
- File size limits
- File extension whitelist

**Fix:** Add validation function
```php
private function validateUpload($file) {
    $maxSize = 5 * 1024 * 1024; // 5MB
    $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
    $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
    
    if ($file['size'] > $maxSize) {
        return ['valid' => false, 'error' => 'File too large'];
    }
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime, $allowedMimes)) {
        return ['valid' => false, 'error' => 'Invalid file type'];
    }
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExts)) {
        return ['valid' => false, 'error' => 'Invalid file extension'];
    }
    
    return ['valid' => true];
}
```

---

### 8. Form Image Path Handling
**File:** `admin/pages/form-logs.php`
**Issue:** Direct output of file paths without validation

**Lines 398-403:**
```php
echo $row['image_path'];  // Could expose system paths
```

**Fix:** Sanitize and validate paths
```php
$imagePath = $row['image_path'] ?? '';
if (!empty($imagePath)) {
    // Ensure path is within public directory
    $realPath = realpath($imagePath);
    $publicDir = realpath(__DIR__ . '/../../public');
    if ($realPath && $publicDir && strpos($realPath, $publicDir) === 0) {
        echo htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8');
    }
}
```

---

## MEDIUM (Code Quality)

### 9. Duplicate API Endpoints
**File:** `admin/includes/api.php`
**Issue:** Duplicate handler methods for similar resources

**Duplicates Found:**
- `getMembers()` / `getMember()` - Lines 120-140
- `getContacts()` / `getContact()` - Lines 320-340
- `getEvents()` / `getEvent()` - Lines 380-400
- `getProjects()` / `getProject()` - Lines 480-500

**Fix:** Consolidate into single generic handler
```php
private function getResource($resource, $id = null) {
    try {
        if ($id) {
            $stmt = $this->db->prepare("SELECT * FROM $resource WHERE id = ?");
            $stmt->execute([$id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            return $data ? $this->success("$resource retrieved", $data) 
                         : $this->error("$resource not found", 404);
        } else {
            $stmt = $this->db->query("SELECT * FROM $resource ORDER BY created_at DESC");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $this->success("$resource retrieved", $data);
        }
    } catch (PDOException $e) {
        return $this->error("Failed to retrieve $resource: " . $e->getMessage());
    }
}
```

---

### 10. Inconsistent Naming Conventions
**Files:** Multiple
**Issue:** Mixed camelCase and snake_case

**Examples:**
- Database: `first_name` vs Code: `firstName`
- Database: `student_id` vs Code: `studentId`
- Database: `additional_info` vs Code: `notes`

**Fix:** Standardize to snake_case throughout:
```php
// Consistent naming
$firstName = $data['first_name'] ?? '';
$lastName = $data['last_name'] ?? '';
$studentId = $data['student_id'] ?? '';
```

---

### 11. Exception Handling (die() usage)
**Files:** Multiple
**Issue:** Using `die()` instead of proper exception handling

**Examples:**
- `forms/contact.php:24` - `die('CSRF token validation failed...')`
- `forms/register.php:14` - `die('CSRF token validation failed...')`
- `database/setup.php:112` - `die('Database setup failed')`

**Fix:** Use exceptions
```php
// Instead of die()
throw new Exception('CSRF token validation failed');

// In calling code
try {
    // form processing
} catch (Exception $e) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}
```

---

### 12. HTML to PHP Migration Status
**Files:** `includes/router.php`, `pages/` directory
**Issue:** Mixed HTML and PHP files without clear migration strategy

**Current State:**
- Static pages: `.html` files (about.html, careers.html, faq.html)
- Dynamic pages: `.php` files (blog.php, courses.php, events.php)

**Recommendation:** Document decision in `docs/ARCHITECTURE.md`:
```markdown
## Page Architecture Decision

### Current Approach: Hybrid
- Static content pages: `.html` files
- Dynamic/database-driven pages: `.php` files

### Rationale:
- Static pages don't require database queries
- Dynamic pages need PHP for data retrieval
- Reduces unnecessary database calls

### Future: Consider full PHP migration for:
- Consistent template engine usage
- Unified error handling
- Easier maintenance
```

---

## LOW (Polish)

### 13. Pagination Controls
**Files:** `admin/pages/form-logs.php`, `admin/pages/contacts.php`
**Issue:** No pagination for large datasets

**Fix:** Add pagination
```php
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

$stmt = $this->db->query("SELECT * FROM form_logs LIMIT $perPage OFFSET $offset");
$total = $this->db->query("SELECT COUNT(*) FROM form_logs")->fetchColumn();
$totalPages = ceil($total / $perPage);
```

---

### 14. Session Timeout Implementation
**File:** `config/auth.php`
**Issue:** No session timeout configured

**Fix:** Add session timeout
```php
// In config/auth.php
define('SESSION_TIMEOUT', 1800); // 30 minutes

class Auth {
    public static function check() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        // Check timeout
        if (isset($_SESSION['last_activity']) && 
            (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
            session_destroy();
            return false;
        }
        
        $_SESSION['last_activity'] = time();
        return true;
    }
}
```

---

### 15. Session Key Configuration
**File:** `config/csrf.php`
**Issue:** Hardcoded session keys

**Current Code (line 9-10):**
```php
private static $tokenName = 'csrf_token';
private static $sessionKey = '_csrf_token';
```

**Fix:** Make configurable
```php
// In config/session.php
define('CSRF_TOKEN_NAME', getenv('CSRF_TOKEN_NAME') ?: 'csrf_token');
define('CSRF_SESSION_KEY', getenv('CSRF_SESSION_KEY') ?: '_csrf_token');
define('SESSION_TIMEOUT', getenv('SESSION_TIMEOUT') ?: 1800);

// In config/csrf.php
private static $tokenName = CSRF_TOKEN_NAME;
private static $sessionKey = CSRF_SESSION_KEY;
```

---

### 16. Routing Documentation
**File:** `docs/ROUTING.md` (create new)
**Issue:** No documentation of routing choices

**Create:** `docs/ROUTING.md`
```markdown
# Routing Architecture

## Frontend Routing (includes/router.php)
- Uses query parameter: `?page=blog`
- Supports both HTML and PHP files
- Implements path traversal protection with realpath()

## Admin Routing (admin/includes/router.php)
- Uses query parameter: `?route=dashboard`
- Supports middleware and role-based access
- Includes 404 handler

## API Routing (admin/includes/api.php)
- RESTful endpoints: `/api/members`, `/api/events`, etc.
- Supports GET, POST, PUT, DELETE methods
- Returns JSON responses

## Future Improvements
- Consider URL rewriting for cleaner URLs
- Implement route caching for performance
- Add route versioning for API
```

---

## Summary of Changes

| Priority | Category | Files | Status |
|----------|----------|-------|--------|
| IMMEDIATE | Path Traversal | forms/*.php | 3 files |
| IMMEDIATE | XSS | admin/pages/*.php | 5+ files |
| IMMEDIATE | CORS | config/api-wrapper.php | 1 file |
| IMMEDIATE | File Includes | includes/router.php, admin/includes/router.php | 2 files |
| HIGH | DB Columns | admin/includes/api.php | 1 file |
| HIGH | Error Responses | forms/*.php, database/*.php | 5+ files |
| HIGH | File Upload | admin/includes/api.php | 1 file |
| HIGH | Image Paths | admin/pages/form-logs.php | 1 file |
| MEDIUM | Duplicates | admin/includes/api.php | 1 file |
| MEDIUM | Naming | Multiple | 10+ files |
| MEDIUM | Exceptions | Multiple | 10+ files |
| MEDIUM | Documentation | docs/ | 1 file |
| LOW | Pagination | admin/pages/*.php | 2 files |
| LOW | Session Timeout | config/auth.php | 1 file |
| LOW | Config | config/csrf.php | 1 file |
| LOW | Documentation | docs/ROUTING.md | 1 file |

---

## Implementation Priority

1. **Week 1:** Fix IMMEDIATE security issues (path traversal, XSS, CORS)
2. **Week 2:** Fix HIGH critical issues (DB columns, error responses, file uploads)
3. **Week 3:** Refactor MEDIUM code quality issues (duplicates, naming, exceptions)
4. **Week 4:** Polish LOW items (pagination, session timeout, documentation)

