# KHODERS Website - Quick Start Guide

## For Developers

### 1. Local Setup (5 minutes)

```bash
# 1. Clone or download the project
cd c:\xampp\htdocs\khoders-website

# 2. Configure environment
copy .env.example .env
# Edit .env with your database credentials

# 3. Create database
mysql -u root -p
CREATE DATABASE khoders_db;
CREATE USER 'khoders_user'@'localhost' IDENTIFIED BY 'khoders123';
GRANT ALL PRIVILEGES ON khoders_db.* TO 'khoders_user'@'localhost';
FLUSH PRIVILEGES;
exit;

# 4. Import schema
php database/migrate.php

# 5. Start Apache and MySQL
# (XAMPP Control Panel or: sudo service apache2 start)

# 6. Visit http://localhost/khoders-website
```

### 2. Create Admin User

```sql
-- Connect to database
mysql -u khoders_user -p khoders_db

-- Create admin account (password: admin123)
INSERT INTO users (username, email, password, role, created_at) 
VALUES (
    'admin', 
    'admin@khodersclub.com', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin', 
    NOW()
);
```

### 3. Access Admin Panel

- URL: `http://localhost/khoders-website/admin/`
- Username: `admin`
- Password: `admin123`
- **Change password immediately after first login!**

## For Content Managers

### Adding Content via Admin Panel

1. **Login**: Visit `/admin/` and login
2. **Dashboard**: View statistics and recent activity
3. **Members**: View registered members
4. **Contacts**: View contact form submissions
5. **Newsletter**: View newsletter subscribers
6. **Events**: Manage events (currently admin-only)
7. **Projects**: Manage projects (currently admin-only)
8. **Form Logs**: View all form submissions

### Current Limitations

⚠️ **Important**: The admin panel is currently separate from the frontend:
- Events/Projects managed in admin don't appear on website
- Website shows hardcoded content from HTML files
- To make admin changes visible, frontend needs to be converted to PHP

## Project Structure

```
khoders-website/
├── admin/              # Admin panel
│   ├── index.php       # Dashboard
│   ├── members.php     # Member management
│   ├── events.php      # Event management
│   └── ...
├── api/                # REST API endpoints
│   ├── contact.php     # Contact form API
│   ├── register.php    # Registration API
│   └── newsletter.php  # Newsletter API
├── assets/             # Frontend assets
│   ├── css/
│   ├── js/
│   └── img/
├── config/             # Configuration files
│   ├── database.php    # Database connection
│   ├── auth.php        # Authentication
│   └── security.php    # Security functions
├── database/           # Database files
│   ├── schema.sql      # Database schema
│   └── migrate.php     # Migration script
├── forms/              # Legacy form handlers
├── pages/              # Frontend HTML pages
└── logs/               # Application logs
```

## Common Tasks

### Test Database Connection
```bash
# No longer available (test-db.php was removed for security)
# Use migrate.php instead:
php database/migrate.php
```

### View Logs
```bash
# Contact form submissions
tail -f logs/contacts.log

# Registration submissions
tail -f logs/registrations.log

# Newsletter subscriptions
tail -f logs/subscriptions.log
```

### Clear Logs
```bash
# Windows
del logs\*.log

# Linux/Mac
rm logs/*.log
```

### Backup Database
```bash
mysqldump -u khoders_user -p khoders_db > backup.sql
```

### Restore Database
```bash
mysql -u khoders_user -p khoders_db < backup.sql
```

## Form Submission

### Two Systems Available

**Option 1: Legacy Forms** (Current)
- Forms submit to `/forms/contact.php`, etc.
- Uses PHP_Email_Form library
- Sends emails and logs to database

**Option 2: Modern API** (Recommended)
- Forms submit to `/api/contact.php`, etc.
- JSON REST API
- Better security and validation
- Rate limiting included

To switch to API, update form action URLs in HTML files.

## Troubleshooting

### "Database connection failed"
- Check `.env` credentials
- Verify MySQL is running
- Test: `mysql -u khoders_user -p`

### "404 Not Found"
- Check `.htaccess` exists
- Verify mod_rewrite enabled
- Check file permissions

### "Admin login fails"
- Verify user exists in database
- Check password hash
- Clear browser cookies

### "Forms not submitting"
- Check browser console for errors
- Verify form action URL
- Check logs in `logs/` directory

## Development Tips

### Making Changes

1. **Frontend**: Edit files in `pages/` directory
2. **Admin Panel**: Edit files in `admin/` directory
3. **API**: Edit files in `api/` directory
4. **Database**: Update `database/schema.sql` and run migrate.php

### Testing

1. Test all navigation links
2. Test all forms (contact, register, newsletter)
3. Test admin login and features
4. Check browser console for errors
5. Review logs for issues

### Before Committing

- [ ] Remove any test files
- [ ] Clear sensitive data from logs
- [ ] Update documentation if needed
- [ ] Test on clean database
- [ ] Verify no hardcoded credentials

## Security Reminders

✅ **Already Fixed:**
- SQL injection vulnerabilities
- Exposed test files
- Broken CDN URLs
- Database credential conflicts

⚠️ **Remember:**
- Change default admin password
- Use strong passwords in production
- Set `APP_ENV=production` when deploying
- Never commit `.env` file
- Keep logs directory secure

## Getting Help

- **Full Analysis**: See `COMPLETE_PROJECT_ANALYSIS.md`
- **All Fixes**: See `FIXES_COMPLETE.md`
- **Deployment**: See `DEPLOYMENT.md`
- **Admin Info**: See `admin/README.md`

## Next Steps

1. ✅ Setup complete - website is functional
2. ⏭️ Customize content in HTML files
3. ⏭️ Add your own images to `assets/img/`
4. ⏭️ Update contact information
5. ⏭️ Configure email settings
6. ⏭️ Test all features thoroughly
7. ⏭️ Deploy to production

---

**Status**: ✅ Ready to use  
**Last Updated**: December 2024
