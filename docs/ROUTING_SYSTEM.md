# KHODERS WORLD Routing System Documentation

## Overview

The KHODERS WORLD website uses a unified PHP routing system that provides clean, maintainable URL patterns while maintaining full backward compatibility with legacy `.html` files.

## Routing Architecture

### Core Components

1. **Unified Entry Point**: `index.php` - Single entry point for all page requests
2. **Router Class**: `includes/router.php` - SiteRouter class handles page routing and content loading
3. **URL Rewriting**: `.htaccess` - Apache module rewrites `.html` requests to PHP routing
4. **Page Templates**: `pages/` - Mix of `.html` (static) and `.php` (dynamic) files

### URL Patterns

#### Clean PHP Routing (Primary)

```
http://khoders.local/index.php?page=about
http://khoders.local/index.php?page=contact
http://khoders.local/index.php?page=blog
```

#### Index Page

```
http://khoders.local/
http://khoders.local/index.php
```

#### Dynamic Content Pages

```
http://khoders.local/index.php?page=events   # Loads from api/events-list.php + pages/events-template.php
http://khoders.local/index.php?page=projects # Loads from api/projects-list.php + pages/projects-template.php
http://khoders.local/index.php?page=team     # Loads from api/team-list.php + pages/team-template.php
```

#### Legacy HTML Files (Backward Compatible)

```
http://khoders.local/pages/about.html  # Rewrites to index.php?page=about
http://khoders.local/pages/contact.html # Rewrites to index.php?page=contact
```

## Router Implementation Details

### SiteRouter Class (includes/router.php)

**Key Methods:**

- `init()` - Initializes page routing table with file existence checks

  - Prefers `.php` files over `.html` (allows gradual migration)
  - Defines page titles and meta descriptions
  - Sets up dynamic page handlers

- `route($page)` - Routes request to appropriate page

  - Handles index page specially (extracts main content)
  - Handles dynamic pages (events, projects, team) by loading API data
  - Handles static pages (extracts content from HTML/PHP files)
  - Returns 404 for invalid pages
  - Uses template system for consistent layout

- `getUrl($page)` - Returns proper URL for a page
  - Returns `index.php` for home page
  - Returns `index.php?page=xxx` for other pages
  - Properly URL-encodes page names

### Page Content Extraction

The router intelligently extracts page content using multiple strategies:

```php
// Strategy 1: Extract <main> content (preferred)
preg_match('/<main[^>]*>(.*?)<\/main>/s', $html_content, $matches);

// Strategy 2: Extract <body> content (fallback)
preg_match('/<body.*?>(.*?)<\/body>/s', $html_content, $matches);
```

This prevents duplicate headers, navigation, and footers when pages are included in the template system.

### Dynamic Pages

Three pages load content from the database:

1. **Events** - `pages/events-template.php` + `api/events-list.php`
2. **Projects** - `pages/projects-template.php` + `api/projects-list.php`
3. **Team** - `pages/team-template.php` + `api/team-list.php`

If API fails, gracefully falls back to static HTML.

## Link Migration (Phase 2)

### Migration Process

All 23 HTML files were migrated from hardcoded `.html` links to unified PHP routing using `tools/migrate-routing.php`:

```bash
# Dry-run (preview changes)
php tools/migrate-routing.php

# Apply changes
php tools/migrate-routing.php --force

# Restore from backup if needed
php tools/migrate-routing.php --restore
```

### Changes Made

- **Total Files Migrated**: 23 HTML files
- **Total Links Updated**: 661 hardcoded links
- **Link Types Converted**:
  - `href="index.html"` → `href="index.php"`
  - `href="about.html"` → `href="index.php?page=about"`
  - `href="contact.html?id=123"` → `href="index.php?page=contact&id=123"`

### Backup and Recovery

All original HTML files are backed up in `backups/routing-migration/`:

```
backups/routing-migration/
├── 404.html.backup
├── about.html.backup
├── blog.html.backup
├── contact.html.backup
└── ... (23 total backups)
```

To restore: `php tools/migrate-routing.php --restore`

## Apache .htaccess Configuration

### URL Rewriting Rules

```apache
# Route .html files through PHP router
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.+)\.html$ index.php?page=$1 [QSA,L]
```

This rule:

- Checks that the file doesn't physically exist (`!-f`)
- Checks that the directory doesn't physically exist (`!-d`)
- Rewrites requests to `something.html` → `index.php?page=something`
- Preserves query strings (`QSA` flag)
- Stops processing subsequent rules (`L` flag)

### Benefits

1. **Backward Compatibility** - Old `.html` links still work
2. **Clean URLs** - New links use PHP routing
3. **SEO Friendly** - Unified routing prevents duplicate content
4. **Maintainable** - Single entry point for all pages

## Available Pages

### Static Pages (Loaded from .html or .php files)

| Page             | URL                               |
| ---------------- | --------------------------------- |
| Home             | `index.php`                       |
| About            | `index.php?page=about`            |
| Blog             | `index.php?page=blog`             |
| Blog Details     | `index.php?page=blog-details`     |
| Careers          | `index.php?page=careers`          |
| Code of Conduct  | `index.php?page=code-of-conduct`  |
| Contact          | `index.php?page=contact`          |
| Courses          | `index.php?page=courses`          |
| FAQ              | `index.php?page=faq`              |
| Instructors      | `index.php?page=instructors`      |
| Join Program     | `index.php?page=join-program`     |
| Membership Tiers | `index.php?page=membership-tiers` |
| Mentor Profile   | `index.php?page=mentor-profile`   |
| Privacy Policy   | `index.php?page=privacy-policy`   |
| Program Details  | `index.php?page=program-details`  |
| Resources        | `index.php?page=resources`        |
| Services         | `index.php?page=services`         |
| Terms of Service | `index.php?page=terms-of-service` |

### Dynamic Pages (Database-driven)

| Page     | URL                       | Data Source                        |
| -------- | ------------------------- | ---------------------------------- |
| Events   | `index.php?page=events`   | `api/events-list.php` + template   |
| Projects | `index.php?page=projects` | `api/projects-list.php` + template |
| Team     | `index.php?page=team`     | `api/team-list.php` + template     |
| Register | `index.php?page=register` | Form submission to API             |

### Admin Pages

Admin pages use their own routing system in `admin/routes.php` and are not part of the public routing:

- `admin/dashboard.php`
- `admin/blog.php`
- `admin/courses.php`
- `admin/events.php`
- `admin/projects.php`
- `admin/team.php`
- `admin/members.php`
- `admin/contacts.php`
- `admin/newsletter.php`
- `admin/form-logs.php`
- `admin/profile.php`
- `admin/settings.php`

## Template System Integration

The routing system integrates with `includes/template.php` for consistent page rendering:

```php
// Render page with template
echo render_page($html_content, $title, $meta_data);
```

This function:

1. Injects page title
2. Sets meta descriptions
3. Wraps content in standard layout
4. Includes header, navigation, footer
5. Loads CSS and JavaScript assets

## Error Handling

### 404 Pages

Invalid page requests are handled gracefully:

1. Check if page exists in routing table
2. If not found:
   - Set HTTP 404 header
   - Load `pages/404.html` or `pages/404.php`
   - Display 404 error page
   - Log to form_logs for debugging

### API Failures

Dynamic pages gracefully degrade if API calls fail:

```php
// Try to load data from API
if ($apiData && $apiData['success']) {
    // Use dynamic template with data
} else {
    // Fall back to static HTML
}
```

## Security Measures

### File Access Protection

- `includes/.htaccess` - Prevents direct access to PHP/HTML files
- `logs/.htaccess` - Prevents log file enumeration
- Security headers in main `.htaccess`:
  - X-Content-Type-Options: nosniff
  - X-XSS-Protection: 1; mode=block
  - X-Frame-Options: SAMEORIGIN

### Input Validation

The `$_GET['page']` parameter is validated against the pages table:

```php
$page = isset($_GET['page']) && !empty($_GET['page']) ? $_GET['page'] : 'index';

// Validation happens in if (isset(self::$pages[$page]))
```

Only pages defined in the routing table are processed.

## Development Guidelines

### Adding a New Page

1. **Create page file** - Either `.html` or `.php` in `pages/` directory
2. **Add to routing table** - Update `SiteRouter::init()`
3. **Define title** - Add entry to `self::$titles`
4. **Add meta data** - Add entry to `self::$meta` if needed
5. **Create links** - Use `SiteRouter::getUrl('page-name')` or direct URL

Example:

```php
// In includes/router.php init() method
self::$pages['about'] = file_exists('pages/about.php') ? 'pages/about.php' : 'pages/about.html';
self::$titles['about'] = 'About - KHODERS WORLD';

// In HTML/PHP files
<a href="index.php?page=about">About</a>

// Or use router method
<a href="<?php echo SiteRouter::getUrl('about'); ?>">About</a>
```

### Adding a Dynamic Page

For pages that load from the database:

1. **Create API endpoint** - `api/page-name.php`
2. **Create template** - `pages/page-name-template.php`
3. **Add to dynamicPages** array in `SiteRouter::route()`
4. **Return JSON from API** with success/data structure

Example API response:

```json
{
  "success": true,
  "data": [
    { "id": 1, "title": "Event 1", "date": "2025-01-15" },
    { "id": 2, "title": "Event 2", "date": "2025-01-20" }
  ]
}
```

### Creating Internal Links

Always use the router to generate URLs:

```php
// Good - Uses router
<a href="<?php echo SiteRouter::getUrl('about'); ?>">About</a>

// Also good - Direct URL with proper encoding
<a href="index.php?page=<?php echo urlencode('about'); ?>">About</a>

// Avoid - Hardcoded links
<a href="about.html">About</a>
```

## Performance Considerations

### Caching

Static pages are cached with long expiration via `.htaccess`:

```apache
<FilesMatch "\.(ico|pdf|jpg|jpeg|png|webp|gif|html|htm|xml|txt|xsl|css|js)$">
  Header set Cache-Control "max-age=31536000, public"
</FilesMatch>
```

### Database Queries

Dynamic pages use efficient queries via `api/*-list.php`:

- Single query per page type
- Proper indexing on database tables
- Results cached in PHP session if needed

## Troubleshooting

### Pages Not Found

**Problem**: Getting 404 for valid page

**Solution**:

1. Check page file exists in `pages/`
2. Verify page name in routing table
3. Check `.htaccess` is enabled (test with `php -S localhost:8000`)
4. Verify page parameter is spelled correctly (case-sensitive)

### Links Not Working

**Problem**: Navigation links return 404

**Solution**:

1. Check if using hardcoded `.html` links (should be `index.php?page=xxx`)
2. Run migration tool if old links: `php tools/migrate-routing.php --force`
3. Clear browser cache
4. Verify Apache mod_rewrite is enabled

### Dynamic Content Not Loading

**Problem**: Events/Projects/Team pages show no content

**Solution**:

1. Check API endpoint exists and returns valid JSON
2. Test API directly: `curl index.php?api=events-list`
3. Check database connection
4. Review server error logs
5. Verify template file exists in `pages/`

## Future Improvements

### Planned Enhancements

1. **Clean URLs** - Remove `index.php` using advanced `.htaccess` rewriting

   ```
   Before: index.php?page=about
   After:  /about/
   ```

2. **Slug-based URLs** - Use URL slugs instead of query strings

   ```
   Before: index.php?page=blog-details&id=5
   After:  /blog/my-awesome-post-5/
   ```

3. **Lazy Loading** - Defer loading of above-the-fold content
4. **API Caching** - Cache API responses to reduce database queries
5. **Static Site Generation** - Pre-generate static pages for performance

## References

- **Router Class**: `includes/router.php`
- **Template System**: `includes/template.php`
- **Migration Tool**: `tools/migrate-routing.php`
- **Admin Routing**: `admin/routes.php`
- **Apache Configuration**: `.htaccess`

## Support

For routing issues or questions:

1. Check this documentation
2. Review `includes/router.php` comments
3. Check server error logs: `logs/error.log`
4. Run migration tool in dry-run mode to preview changes
