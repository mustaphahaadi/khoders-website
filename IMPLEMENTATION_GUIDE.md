# Implementation Guide - Priority Fixes

## Quick Start

1. Review `PRIORITY_FIXES.md` for detailed analysis
2. Follow implementation order below
3. Test each fix before moving to next
4. Use provided fixed examples as templates

---

## Phase 1: IMMEDIATE Security Fixes (Week 1)

### Step 1: Fix Path Traversal (30 min)

**Files to update:**
- `forms/contact.php`
- `forms/register.php`
- `forms/newsletter.php`

**Template:**
```php
// OLD (VULNERABLE)
if( file_exists($php_email_form = '../assets/vendor/php-email-form/php-email-form.php' )) {
    include( $php_email_form );
}

// NEW (FIXED)
$php_email_form = realpath(__DIR__ . '/../assets/vendor/php-email-form/php-email-form.php');
$base_dir = realpath(__DIR__ . '/../assets/vendor/');

if ($php_email_form && $base_dir && strpos($php_email_form, $base_dir) === 0 && file_exists($php_email_form)) {
    include $php_email_form;
} else {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unable to load library']);
    exit;
}
```

**Verification:**
```bash
# Test that forms still work
curl -X POST http://localhost/khoders-website/forms/contact.php
```

---

### Step 2: Fix XSS in Admin Pages (1 hour)

**Files to update:**
- `admin/pages/form-logs.php` (lines 253, 260, 308, 326, 335, 339, 343-354, 375-403, 491-519)
- `admin/pages/admin-users.php` (lines 92-93)
- `admin/pages/contacts.php` (lines 156-179)

**Template:**
```php
// OLD (VULNERABLE)
echo $row['email'];
echo $row['name'];

// NEW (FIXED)
echo htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8');
echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
```

**Bulk Replace Script:**
```php
// Create file: fix-xss.php
<?php
$files = [
    'admin/pages/form-logs.php',
    'admin/pages/admin-users.php',
    'admin/pages/contacts.php'
];

foreach ($files as $file) {
    $content = file_get_contents($file);
    // Replace echo $row['field'] with htmlspecialchars version
    $content = preg_replace(
        '/echo\s+\$row\[\'([^\']+)\'\]/',
        "echo htmlspecialchars(\$row['$1'], ENT_QUOTES, 'UTF-8')",
        $content
    );
    file_put_contents($file, $content);
    echo "Fixed: $file\n";
}
?>
```

**Verification:**
```bash
# Test admin pages load without errors
curl -X GET http://localhost/khoders-website/admin/pages/form-logs.php
```

---

### Step 3: Fix CORS Headers (15 min)

**File:** `config/api-wrapper.php`

**Change lines 40-50:**
```php
// OLD (VULNERABLE)
if (in_array($requestOrigin, $allowedOrigins) || in_array('*', $allowedOrigins)) {
    header('Access-Control-Allow-Origin: ' . $requestOrigin);
} else {
    header('Access-Control-Allow-Origin: *');  // VULNERABLE!
}

// NEW (FIXED)
if (in_array($requestOrigin, $allowedOrigins)) {
    header('Access-Control-Allow-Origin: ' . $requestOrigin);
} else {
    // Don't set CORS header for non-whitelisted origins
    http_response_code(403);
    exit('CORS policy violation');
}
```

**Verification:**
```bash
# Test CORS from non-whitelisted origin
curl -H "Origin: http://evil.com" http://localhost/khoders-website/api/members
# Should return 403
```

---

### Step 4: Fix File Include Validation (45 min)

**Files to update:**
- `includes/router.php` (lines 64-88)
- `admin/includes/router.php` (lines 138-160)

**Template for includes/router.php:**
```php
// Define whitelist
$allowedPages = [
    'about', 'blog', 'blog-details', 'careers', 'code-of-conduct',
    'contact', 'courses', 'course-details', 'enroll', 'events',
    'faq', 'instructors', 'join-program', 'login', 'membership-tiers',
    'mentor-profile', 'privacy-policy', 'programs', 'program-details',
    'projects', 'register', 'resources', 'services', 'team',
    'terms-of-service', '404'
];

// Validate page is in whitelist
if (!in_array($page, $allowedPages, true)) {
    header('HTTP/1.0 404 Not Found');
    $page = '404';
}

// Validate path is within pages directory
$filePath = self::$pages[$page];
$realPath = realpath($filePath);
$basePath = realpath('pages');

if (!($realPath && $basePath && strpos($realPath, $basePath) === 0 && file_exists($filePath))) {
    header('HTTP/1.0 404 Not Found');
    $page = '404';
}
```

**Verification:**
```bash
# Test valid page
curl http://localhost/khoders-website/index.php?page=blog
# Should load blog page

# Test invalid page
curl http://localhost/khoders-website/index.php?page=../../../etc/passwd
# Should return 404
```

---

## Phase 2: HIGH Critical Fixes (Week 2)

### Step 5: Fix Database Column Mismatches (1 hour)

**File:** `admin/includes/api.php`

**Database Migration:**
```sql
-- Run these commands to standardize column names
ALTER TABLE members 
CHANGE COLUMN firstName first_name VARCHAR(100),
CHANGE COLUMN lastName last_name VARCHAR(100),
CHANGE COLUMN studentId student_id VARCHAR(50),
CHANGE COLUMN notes additional_info TEXT;

-- Verify changes
DESCRIBE members;
```

**Code Update:**
```php
// In createMember() and updateMember() methods
// Change from:
$firstName = $data['firstName'] ?? '';

// To:
$firstName = $data['first_name'] ?? '';
```

---

### Step 6: Fix Error Responses (1 hour)

**Files to update:**
- `forms/contact.php` (line 24)
- `forms/register.php` (line 14)
- `forms/newsletter.php` (line 14)
- `database/setup.php` (line 112)

**Template:**
```php
// OLD (VULNERABLE - blank page)
die('CSRF token validation failed');

// NEW (FIXED - proper JSON response)
http_response_code(403);
header('Content-Type: application/json');
echo json_encode([
    'success' => false,
    'message' => 'CSRF token validation failed'
]);
exit;
```

---

### Step 7: Add File Upload Validation (1.5 hours)

**File:** `admin/includes/api.php`

**Add method:**
```php
private function validateUpload($file) {
    $maxSize = 5 * 1024 * 1024; // 5MB
    $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
    $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
    
    if (!isset($file['tmp_name']) || !isset($file['size'])) {
        return ['valid' => false, 'error' => 'Invalid file'];
    }
    
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

**Use in upload handlers:**
```php
// Before processing upload
$validation = $this->validateUpload($_FILES['image']);
if (!$validation['valid']) {
    return $this->error($validation['error']);
}
```

---

### Step 8: Fix Form Image Path Handling (30 min)

**File:** `admin/pages/form-logs.php` (lines 398-403)

**Template:**
```php
// OLD (VULNERABLE)
echo $row['image_path'];

// NEW (FIXED)
$imagePath = $row['image_path'] ?? '';
if (!empty($imagePath)) {
    $realPath = realpath($imagePath);
    $publicDir = realpath(__DIR__ . '/../../public');
    if ($realPath && $publicDir && strpos($realPath, $publicDir) === 0) {
        echo htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8');
    }
}
```

---

## Phase 3: MEDIUM Code Quality (Week 3)

### Step 9: Consolidate Duplicate API Endpoints (1 hour)

**File:** `admin/includes/api.php`

**Create generic handler:**
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

**Replace duplicate methods:**
```php
// OLD
private function getMembers() { ... }
private function getMember($id) { ... }

// NEW
private function getMembers() {
    return $this->getResource('members');
}

private function getMember($id) {
    return $this->getResource('members', $id);
}
```

---

### Step 10: Standardize Naming Conventions (2 hours)

**Create mapping file:** `config/field-mapping.php`
```php
<?php
// Database to code field mapping
return [
    'first_name' => 'firstName',
    'last_name' => 'lastName',
    'student_id' => 'studentId',
    'additional_info' => 'additionalInfo',
    'ip_address' => 'ipAddress',
    'user_agent' => 'userAgent',
    'created_at' => 'createdAt',
    'updated_at' => 'updatedAt'
];
?>
```

**Use consistently:**
```php
// Always use snake_case for database
$stmt = $this->db->prepare("SELECT first_name, last_name FROM members");

// Convert to camelCase for API responses
$data = $stmt->fetch(PDO::FETCH_ASSOC);
$response = [
    'firstName' => $data['first_name'],
    'lastName' => $data['last_name']
];
```

---

### Step 11: Replace die() with Exceptions (1.5 hours)

**Create exception handler:**
```php
// In config/exceptions.php
class FormException extends Exception {}
class DatabaseException extends Exception {}
class ValidationException extends Exception {}

// In forms/contact.php
try {
    if (!CSRFToken::validate()) {
        throw new ValidationException('CSRF token validation failed');
    }
    // ... rest of form processing
} catch (ValidationException $e) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}
```

---

### Step 12: Document Architecture (30 min)

**Create:** `docs/ARCHITECTURE.md`
```markdown
# KHODERS Website Architecture

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

## Routing Strategy

### Frontend: includes/router.php
- Query parameter: `?page=blog`
- Whitelist validation
- Path traversal protection

### Admin: admin/includes/router.php
- Query parameter: `?route=dashboard`
- Middleware support
- Role-based access control

### API: admin/includes/api.php
- RESTful endpoints
- JSON responses
- CORS protection
```

---

## Phase 4: LOW Polish (Week 4)

### Step 13: Add Pagination (1 hour)

**File:** `admin/pages/form-logs.php`

```php
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

$stmt = $this->db->query("SELECT * FROM form_logs LIMIT $perPage OFFSET $offset");
$total = $this->db->query("SELECT COUNT(*) FROM form_logs")->fetchColumn();
$totalPages = ceil($total / $perPage);

// In template
for ($i = 1; $i <= $totalPages; $i++) {
    echo '<a href="?page=' . $i . '">' . $i . '</a>';
}
```

---

### Step 14: Add Session Timeout (30 min)

**File:** `config/auth.php`

```php
define('SESSION_TIMEOUT', 1800); // 30 minutes

class Auth {
    public static function check() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
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

### Step 15: Make Session Keys Configurable (30 min)

**Create:** `config/session.php`
```php
<?php
define('CSRF_TOKEN_NAME', getenv('CSRF_TOKEN_NAME') ?: 'csrf_token');
define('CSRF_SESSION_KEY', getenv('CSRF_SESSION_KEY') ?: '_csrf_token');
define('SESSION_TIMEOUT', getenv('SESSION_TIMEOUT') ?: 1800);
define('SESSION_NAME', getenv('SESSION_NAME') ?: 'KHODERS_SESSION');
?>
```

**Update:** `config/csrf.php`
```php
require_once __DIR__ . '/session.php';

private static $tokenName = CSRF_TOKEN_NAME;
private static $sessionKey = CSRF_SESSION_KEY;
```

---

### Step 16: Create Routing Documentation (30 min)

**Create:** `docs/ROUTING.md`
```markdown
# Routing Architecture

## Frontend Routing
- File: `includes/router.php`
- Method: Query parameter `?page=blog`
- Features: Path traversal protection, whitelist validation

## Admin Routing
- File: `admin/includes/router.php`
- Method: Query parameter `?route=dashboard`
- Features: Middleware, role-based access, 404 handler

## API Routing
- File: `admin/includes/api.php`
- Method: RESTful endpoints
- Features: JSON responses, CORS protection, error handling

## Future Improvements
- URL rewriting for cleaner URLs
- Route caching for performance
- API versioning
```

---

## Testing Checklist

- [ ] Path traversal attempts return 404
- [ ] XSS payloads are escaped in output
- [ ] CORS rejects non-whitelisted origins
- [ ] File uploads validate MIME type and size
- [ ] Error responses return proper JSON
- [ ] Database queries use prepared statements
- [ ] Session timeout works after inactivity
- [ ] Admin pages load without errors
- [ ] Forms submit successfully
- [ ] API endpoints return correct responses

---

## Rollback Plan

If issues occur:

1. Keep backups of original files
2. Test fixes in development first
3. Deploy to staging for QA
4. Use git to revert if needed

```bash
# Backup before changes
cp -r khoders-website khoders-website.backup

# Revert if needed
git checkout -- forms/contact.php
```

---

## Timeline

- **Week 1:** IMMEDIATE security fixes (4 items)
- **Week 2:** HIGH critical fixes (4 items)
- **Week 3:** MEDIUM code quality (4 items)
- **Week 4:** LOW polish (4 items)

**Total Effort:** ~20 hours

