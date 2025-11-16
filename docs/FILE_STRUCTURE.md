# KHODERS Website - Clean File Structure

## ✅ ACTIVE FILES (In Use)

### Frontend Pages (pages/)
**Dynamic PHP Pages:**
- `blog.php` - Blog listing
- `blog-details.php` - Individual blog post
- `courses.php` - Courses listing
- `course-details.php` - Individual course
- `programs.php` - Programs listing
- `program-details.php` - Individual program
- `events.php` - Events listing
- `projects.php` - Projects showcase
- `team.php` - Team members
- `enroll.php` - Enrollment form
- `login.php` - Member login
- `register.php` - Member signup
- `contact.php` - Contact form
- `about.php` - About page

**Static HTML Pages (Fallbacks):**
- All `.html` files serve as fallbacks if `.php` doesn't exist
- Router checks for `.php` first, then falls back to `.html`

### Admin Pages (admin/pages/)
**Management Pages:**
- `dashboard.php` - Admin dashboard
- `events.php` + `event-editor.php` - Events management
- `blog.php` + `blog-editor.php` - Blog management
- `courses.php` + `course-editor.php` - Courses management
- `programs.php` + `program-editor.php` - Programs management
- `projects.php` + `project-editor.php` - Projects management
- `team.php` + `team-editor.php` - Team management
- `members.php` - Members list
- `contacts.php` - Contact submissions
- `newsletter.php` - Newsletter subscribers
- `enrollments.php` - Enrollment submissions
- `form-logs.php` - Form logs
- `admin-users.php` - Admin users
- `site-settings.php` - Site settings
- `profile.php` - Admin profile

### Configuration (config/)
- `database.php` - Database connection
- `auth.php` - Authentication
- `security.php` - CSRF & security
- `file-upload.php` - File upload handler
- `error-handler.php` - Error handling

### Database (database/)
- `schema.sql` - Main database schema
- `create_*.sql` - Table creation scripts
- `insert_*.sql` - Data insertion scripts
- `add_*.sql` - Column addition scripts

### Includes (includes/)
- `router.php` - Frontend router
- `template.php` - Page template system
- `navigation.php` - Navigation menu
- `header.php` - HTML header
- `footer.php` - HTML footer

### Admin Includes (admin/includes/)
- `router.php` - Admin router
- `admin_helpers.php` - Helper functions
- `dashboard.php` - Dashboard logic

### Admin Partials (admin/partials/)
- `_navbar.php` - Top navigation
- `_sidebar.php` - Side menu
- `_footer.php` - Footer

## 🗑️ REMOVED FILES
- `events-template.php` - Consolidated into events.php
- `projects-template.php` - Consolidated into projects.php
- `team-template.php` - Consolidated into team.php

## 📁 FOLDER STRUCTURE
```
khoders-website/
├── admin/                  # Admin panel
│   ├── pages/             # Admin pages
│   ├── includes/          # Admin logic
│   ├── partials/          # Admin UI components
│   ├── assets/            # Admin assets
│   ├── index.php          # Admin entry point
│   └── routes.php         # Admin routes
├── assets/                # Frontend assets
│   ├── css/              # Stylesheets
│   ├── img/              # Images
│   ├── js/               # JavaScript
│   └── vendor/           # Third-party libraries
├── config/               # Configuration files
├── database/             # Database scripts
├── forms/                # Form handlers
├── includes/             # Frontend logic
├── pages/                # Frontend pages
├── public/               # Public uploads
│   └── uploads/          # User uploaded files
├── logs/                 # Application logs
├── backups/              # Backup files (safe to keep)
└── index.php             # Frontend entry point
```

## 🎯 NAMING CONVENTIONS

### Pages
- Main listing: `{name}.php` (e.g., `events.php`)
- Detail page: `{name}-details.php` (e.g., `event-details.php`)
- Editor: `{name}-editor.php` (e.g., `event-editor.php`)

### Database Tables
- Plural names: `events`, `courses`, `programs`, `projects`
- Compound names: `team_members`, `blog_posts`, `form_logs`

### Routes
- Frontend: `index.php?page={name}`
- Admin: `admin/index.php?route={name}`
- Actions: `&action=edit&id=1`

## ✨ CLEAN & ORGANIZED
All files follow consistent naming and structure!
