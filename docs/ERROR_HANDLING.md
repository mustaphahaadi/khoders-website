# KHODERS WORLD Error Handling and Debug Info Hiding

## Overview

KHODERS WORLD implements environment-aware error handling that automatically adapts error messages and logging based on the deployment environment (development, staging, or production).

## Key Principles

1. **User-Friendly Errors**: Generic error messages shown to users in production
2. **Server-Side Logging**: Detailed error information logged on the server for debugging
3. **Environment-Aware**: Different behavior based on `APP_ENV` setting
4. **No Information Leakage**: Technical details never exposed to clients in production
5. **Backward Compatible**: Works with existing error handling code

## Environments

### Development

- Detailed error messages shown to users
- Stack traces displayed
- SQL queries logged
- All debug features enabled
- Console output allowed

### Staging

- Balanced approach
- Some error details shown
- Validation errors displayed
- Less sensitive information hidden
- Used for testing before production

### Production

- Generic error messages only
- No stack traces or technical details
- All errors logged server-side
- Maximum security
- Client sees friendly messages only

## Error Handler Class

### Location

`config/error-handler.php` (480+ lines)

### Key Features

1. **Automatic Error Capture**

   - PHP errors (warnings, notices, fatal)
   - Uncaught exceptions
   - Fatal shutdown errors

2. **Environment-Based Responses**

   - Production: Hide all technical details
   - Development: Show full information
   - Staging: Show non-sensitive details only

3. **Comprehensive Logging**

   - Error logs: `logs/error.log`
   - Exception logs: `logs/exception.log`
   - Database logs: `logs/database.log`
   - API logs: `logs/api.log`
   - Security logs: `logs/security.log`

4. **API-Friendly Responses**
   - Always returns JSON
   - Consistent response format
   - Appropriate HTTP status codes
   - Validation error support

## API Wrapper

### Location

`config/api-wrapper.php` (60 lines)

### Purpose

Provides centralized API initialization including:

- Error handler setup
- CORS configuration
- JSON headers
- Request logging
- Preflight handling

### Usage

```php
require_once __DIR__ . '/../config/api-wrapper.php';

// Now use ErrorHandler methods
ErrorHandler::apiSuccess($data);
ErrorHandler::apiError('Message', 400);
```

## Implementation Details

### Error Handler Configuration

```php
// Initialize in your application bootstrap
ErrorHandler::configure($environment, $logPath);

// Checks and sets:
// - environment variable (production/development/staging)
// - error_reporting level
// - display_errors setting
// - log file paths
// - handlers for all error types
```

### Environment Detection

The error handler uses the `APP_ENV` environment variable:

```php
// From .env
APP_ENV=production
```

Or defaults to `development` if not set.

### Error Response Format

#### Production Error Response

```json
{
  "success": false,
  "code": 500,
  "message": "An error occurred. Please try again later."
}
```

#### Development Error Response

```json
{
  "success": false,
  "code": 500,
  "message": "Database connection failed",
  "details": "SQLSTATE[28000]: Invalid authorization specification: 1045 Access denied for user 'wrong'@'localhost'"
}
```

#### Validation Error Response (All Environments)

```json
{
  "success": false,
  "code": 400,
  "message": "An error occurred. Please try again later.",
  "errors": ["Email is required", "Email format is invalid"]
}
```

## Usage Examples

### Basic API Error Handling

```php
require_once '../config/api-wrapper.php';
require_once '../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    if (!$db) {
        ErrorHandler::logDatabaseError('Connection failed');
        ErrorHandler::apiError('Unable to connect to database', 503);
    }

    // Process request
    $result = $db->query('SELECT ...');
    ErrorHandler::apiSuccess(['data' => $result]);

} catch (PDOException $e) {
    ErrorHandler::logDatabaseError('Query error', 'SELECT ...', []);
    ErrorHandler::apiError('Database query failed', 500);
} catch (Exception $e) {
    ErrorHandler::log($e->getMessage(), 'exception');
    ErrorHandler::apiError('An unexpected error occurred', 500);
}
```

### Validation Errors

```php
$errors = [];

if (empty($_POST['email'])) {
    $errors[] = 'Email is required';
} elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Email format is invalid';
}

if (!empty($errors)) {
    // Validation errors shown in all environments
    ErrorHandler::apiError('Validation failed', 400, $errors);
}
```

### Logging Security Events

```php
try {
    // Process sensitive operation
    if (!CSRFToken::validate()) {
        ErrorHandler::logSecurityEvent('CSRF_VALIDATION_FAILED', [
            'endpoint' => '/api/contact',
            'ip' => $_SERVER['REMOTE_ADDR']
        ]);
        ErrorHandler::apiError('Security validation failed', 403);
    }
} catch (Exception $e) {
    ErrorHandler::logSecurityEvent('SECURITY_ERROR', [
        'message' => $e->getMessage()
    ]);
}
```

### Database Error Handling

```php
try {
    $stmt = $db->prepare('INSERT INTO users (name, email) VALUES (?, ?)');
    $stmt->execute([$name, $email]);
} catch (PDOException $e) {
    ErrorHandler::logDatabaseError(
        'Insert failed for users table',
        'INSERT INTO users (name, email) VALUES (?, ?)',
        [$name, $email]
    );

    // Show generic error to user
    ErrorHandler::apiError('Unable to save record', 500);
}
```

## Log File Structure

### Format

Each log entry is JSON for easy parsing:

```json
{
  "timestamp": "2025-01-20 14:35:22.123456",
  "type": "error",
  "message": "Database connection failed",
  "context": {
    "query": "SELECT ...",
    "bindings": ["value1", "value2"]
  },
  "ip": "192.168.1.100"
}
```

### Log Files

1. **error.log** - PHP errors (warnings, notices, fatal)
2. **exception.log** - Uncaught exceptions
3. **database.log** - Database-specific errors
4. **api.log** - API endpoint errors
5. **security.log** - Security-related events (CSRF, rate limiting, etc.)

### Reading Logs

```bash
# View recent errors
tail -f logs/error.log

# See all errors with timestamps
grep "error" logs/error.log | tail -20

# Find specific database error
grep "database" logs/database.log

# View security events
grep "CSRF\|RATE_LIMIT\|LOGIN" logs/security.log
```

## Security Best Practices

### What NOT to Expose in Production

❌ **Never expose these in error messages:**

- Database connection strings
- SQL queries or table names
- File paths and directory structures
- Internal variable values
- Stack traces or function names
- Configuration details
- Third-party API details
- Error codes that leak information

### What TO Log Server-Side

✅ **Always log these for debugging:**

- Query text (for query optimization)
- Query parameters (sanitized)
- Stack traces (server-side only)
- Request headers (user-agent, IP)
- Response codes and times
- Error messages with full details
- Security events with context

## Configuration

### Environment Variables

```env
# Set the application environment
APP_ENV=production

# Application URL (for CORS)
APP_URL=https://khodersclub.com

# Log directory (optional, defaults to logs/)
LOG_PATH=/var/log/khoders
```

### PHP Configuration

The error handler automatically sets:

```php
// Production
error_reporting(E_ALL);
ini_set('display_errors', '0');  // Don't show errors to users
ini_set('log_errors', '1');      // Do log errors server-side

// Development
error_reporting(E_ALL);
ini_set('display_errors', '1');  // Show errors to users
ini_set('log_errors', '1');      // Also log server-side
```

## Migration Guide

### Updating Existing API Endpoints

#### Before (Problematic)

```php
<?php
try {
    // ... code ...
} catch (Exception $e) {
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
    // ❌ Exposes implementation details
}
?>
```

#### After (Secure)

```php
<?php
require_once '../config/api-wrapper.php';

try {
    // ... code ...
} catch (Exception $e) {
    ErrorHandler::log($e->getMessage(), 'exception');
    ErrorHandler::apiError('An unexpected error occurred', 500);
    // ✅ Logs details server-side, shows generic message to user
}
?>
```

### Files Needing Updates

The following files should be updated to use ErrorHandler:

1. `api/courses-list.php` ✅ (Updated in Phase 2)
2. `api/events-list.php` - Needs review
3. `api/projects-list.php` - Needs review
4. `api/team-list.php` - Needs review
5. `api/blog-list.php` - Needs review
6. `api/events.php` - Needs review
7. `api/projects.php` - Needs review
8. `api/register.php` - Needs review
9. `api/newsletter.php` - Needs review

### CLI Command to Update All

```bash
# Generate updated API files (future enhancement)
php tools/update-api-error-handling.php --force
```

## Testing Error Handling

### Development Mode Test

```bash
# Set environment
export APP_ENV=development

# Trigger error to see detailed message
curl -X GET http://localhost/api/invalid-endpoint
```

Expected response shows implementation details:

```json
{
  "success": false,
  "message": "Error details",
  "details": "Full stack trace..."
}
```

### Production Mode Test

```bash
# Set environment
export APP_ENV=production

# Same request
curl -X GET http://localhost/api/invalid-endpoint
```

Expected response hides details:

```json
{
  "success": false,
  "message": "An error occurred. Please try again later."
}
```

### Check Logs

```bash
# Verify detailed error was logged
tail logs/error.log
```

## Performance Impact

### Minimal Overhead

- Error handler setup: ~2ms per request
- JSON logging: ~1ms per error
- Log file I/O: ~5ms per write (with file locking)

### Optimization

For high-traffic production sites:

```php
// Option 1: Use syslog instead of files
// Option 2: Implement log batching
// Option 3: Use external logging service
//   - ELK Stack (Elasticsearch, Logstash, Kibana)
//   - Splunk
//   - Datadog
//   - New Relic
```

## Troubleshooting

### Logs Not Being Written

**Problem**: No error logs are created

**Solution**:

1. Check log directory exists: `logs/`
2. Check directory permissions: `chmod 755 logs/`
3. Verify `APP_ENV` is set correctly
4. Check disk space with `df -h`

### Errors Not Being Caught

**Problem**: Still seeing raw PHP errors

**Solution**:

1. Ensure `ErrorHandler::configure()` is called early
2. Check error handler is registered: `set_error_handler()`
3. Verify PHP error_reporting is not suppressed with `@`

### Log Files Growing Too Large

**Problem**: Disk space issues from large logs

**Solution**:

1. Implement log rotation:

   ```bash
   # Using logrotate
   /var/log/khoders/*.log {
       daily
       rotate 30
       compress
       missingok
   }
   ```

2. Or in PHP:
   ```php
   // Before logging
   if (filesize($logFile) > 100 * 1024 * 1024) { // 100MB
       rename($logFile, $logFile . '.backup');
   }
   ```

## Future Enhancements

### Planned Features

1. **Structured Logging** - Replace JSON with structured format
2. **Log Levels** - DEBUG, INFO, WARNING, ERROR, CRITICAL
3. **Log Aggregation** - Send logs to external service
4. **Alert System** - Notify admins of critical errors
5. **Error Dashboard** - Web UI to view recent errors
6. **Performance Monitoring** - Track error patterns
7. **Contextual Errors** - Include session/user information
8. **Automatic Cleanup** - Archive old logs

## References

- **Main Class**: `config/error-handler.php`
- **API Wrapper**: `config/api-wrapper.php`
- **Log Directory**: `logs/`
- **Environment Config**: `.env` (APP_ENV setting)
- **Security Policy**: `config/security.php`

## Support

For error handling questions:

1. Check this documentation
2. Review error logs: `logs/error.log`
3. Check `config/error-handler.php` comments
4. Review ErrorHandler method documentation
