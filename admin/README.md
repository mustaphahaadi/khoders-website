# Admin Panel Documentation

## Current Architecture

The admin panel uses **direct file access** pattern (not the routing system).

### Active Files:
- `index.php` - Dashboard
- `members.php` - Member management
- `events.php` - Event management
- `projects.php` - Project management
- `contacts.php` - Contact messages
- `newsletter.php` - Newsletter subscribers
- `form-logs.php` - Form submission logs
- `login.php` - Authentication
- `logout.php` - Logout handler

### Unused Files:
- `routes.php` - Routing system (defined but not used)
- `pages/` directory - Alternative implementations (not used)

### Note:
The routing system in `routes.php` and `pages/` directory exists but is not currently active. The admin panel uses direct file access instead. If you want to switch to the routing system, you would need to:

1. Update all sidebar links to use `?route=xxx` format
2. Change `index.php` to dispatch routes
3. Remove root-level admin files
4. Use only the `pages/` directory files

Currently, the direct access pattern is simpler and working correctly.
