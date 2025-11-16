# XAMPP Setup & Testing Guide - KHODERS Website

## Quick Start (5 Minutes)

### Step 1: Start XAMPP Services
1. Open XAMPP Control Panel
2. Start **Apache**
3. Start **MySQL**
4. Verify both show green "Running" status

### Step 2: Create Database
1. Open browser: `http://localhost/phpmyadmin`
2. Click "New" in left sidebar
3. Database name: `khoders_db`
4. Collation: `utf8mb4_unicode_ci`
5. Click "Create"

### Step 3: Run Database Setup
Open browser: `http://localhost/khoders-website/database/run_updates.php`

You should see:
```
Starting database schema updates...
✓ Executed successfully
✓ Executed successfully
...
=================================
Schema updates complete!
Success: X
Failed: 0
=================================

✅ All updates applied successfully!

You can now login to admin panel:
URL: /admin/
Username: admin
Password: Admin@2024!

⚠️  IMPORTANT: Change password after first login!
```

### Step 4: Test Website
**Frontend:** `http://localhost/khoders-website/`
- Should load homepage
- Navigation should work
- Forms should display

**Admin Panel:** `http://localhost/khoders-website/admin/`
- Login: `admin`
- Password: `Admin@2024!`
- Should redirect to change password

---

## Testing Checklist

### ✅ Frontend Tests

#### 1. Homepage
- [ ] Visit: `http://localhost/khoders-website/`
- [ ] Page loads without errors
- [ ] Navigation menu works
- [ ] Images display

#### 2. Contact Form
- [ ] Visit: `http://localhost/khoders-website/index.php?page=contact`
- [ ] Form displays
- [ ] Fill out form and submit
- [ ] Check success message
- [ ] Verify in database: `SELECT * FROM contacts`

#### 3. Registration Form
- [ ] Visit: `http://localhost/khoders-website/index.php?page=register`
- [ ] Form displays
- [ ] Fill out form and submit
- [ ] Check success message
- [ ] Verify in database: `SELECT * FROM members`

#### 4. Dynamic Pages
- [ ] Events: `http://localhost/khoders-website/index.php?page=events`
- [ ] Team: `http://localhost/khoders-website/index.php?page=team`
- [ ] Projects: `http://localhost/khoders-website/index.php?page=projects`

### ✅ Admin Panel Tests

#### 1. Login
- [ ] Visit: `http://localhost/khoders-website/admin/`
- [ ] Login with: admin / Admin@2024!
- [ ] Should force password change
- [ ] Change password to something secure
- [ ] Login again with new password

#### 2. Dashboard
- [ ] View dashboard statistics
- [ ] Check member count
- [ ] Check recent activity

#### 3. Admin Users (Admin Only)
- [ ] Click "Admin Users" in sidebar
- [ ] Create new admin user
- [ ] Test login with new user
- [ ] Delete test user

#### 4. Events Management
- [ ] Click "Events" in sidebar
- [ ] Click "Add New Event"
- [ ] Fill form and save
- [ ] Verify event appears in list
- [ ] Edit event
- [ ] Delete event

#### 5. Team Members
- [ ] Click "Team Members"
- [ ] Add new team member
- [ ] Upload photo
- [ ] Save and verify

#### 6. Members List
- [ ] Click "Members"
- [ ] View registered members
- [ ] Check member details

### ✅ API Tests

#### 1. Events API
```
http://localhost/khoders-website/api/events-list.php
```
Should return JSON:
```json
{
  "success": true,
  "message": "Events retrieved successfully",
  "data": [...],
  "meta": {...}
}
```

#### 2. Search API
```
http://localhost/khoders-website/api/search.php?q=test
```
Should return search results

#### 3. Event Details
```
http://localhost/khoders-website/api/event-details.php?id=1
```
Should return single event

---

## Troubleshooting

### Problem: "Database connection failed"
**Solution:**
1. Check MySQL is running in XAMPP
2. Verify database exists: `khoders_db`
3. Check `.env` file:
   ```
   DB_HOST=localhost
   DB_NAME=khoders_db
   DB_USER=root
   DB_PASS=
   ```

### Problem: "CSRF token validation failed"
**Solution:**
1. Clear browser cookies
2. Refresh page
3. Try again

### Problem: "Page not found"
**Solution:**
1. Check URL includes `/khoders-website/`
2. Verify `.htaccess` file exists
3. Enable mod_rewrite in Apache

### Problem: "Admin login fails"
**Solution:**
1. Run database updates again
2. Check admins table exists:
   ```sql
   SELECT * FROM admins;
   ```
3. If empty, insert manually:
   ```sql
   INSERT INTO admins (username, email, password_hash, role) 
   VALUES ('admin', 'admin@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
   ```

### Problem: "File upload fails"
**Solution:**
1. Check folder exists: `public/uploads/`
2. Set permissions (Windows: Full Control)
3. Check PHP settings:
   - `upload_max_filesize = 10M`
   - `post_max_size = 10M`

---

## Database Verification

### Check Tables Exist
```sql
SHOW TABLES;
```

Should show:
- admins
- blog_posts
- contacts
- courses
- events
- form_logs
- members
- newsletter
- projects
- site_settings
- team_members

### Check Admin User
```sql
SELECT * FROM admins;
```

Should show at least one admin user.

### Check Sample Data
```sql
SELECT COUNT(*) FROM events;
SELECT COUNT(*) FROM members;
SELECT COUNT(*) FROM contacts;
```

---

## Quick Commands

### Reset Database
```sql
DROP DATABASE khoders_db;
CREATE DATABASE khoders_db;
```
Then run: `http://localhost/khoders-website/database/run_updates.php`

### View Error Logs
Check: `c:\xampp\htdocs\khoders-website\logs\`

### Clear Sessions
Delete: `c:\xampp\tmp\sess_*`

---

## Test URLs Reference

### Frontend
- Homepage: `http://localhost/khoders-website/`
- About: `http://localhost/khoders-website/index.php?page=about`
- Contact: `http://localhost/khoders-website/index.php?page=contact`
- Register: `http://localhost/khoders-website/index.php?page=register`
- Events: `http://localhost/khoders-website/index.php?page=events`
- Team: `http://localhost/khoders-website/index.php?page=team`
- Projects: `http://localhost/khoders-website/index.php?page=projects`

### Admin
- Login: `http://localhost/khoders-website/admin/`
- Dashboard: `http://localhost/khoders-website/admin/index.php`
- Events: `http://localhost/khoders-website/admin/index.php?route=events`
- Team: `http://localhost/khoders-website/admin/index.php?route=team`
- Members: `http://localhost/khoders-website/admin/index.php?route=members`
- Admin Users: `http://localhost/khoders-website/admin/index.php?route=admin-users`

### API
- Events: `http://localhost/khoders-website/api/events-list.php`
- Team: `http://localhost/khoders-website/api/team-list.php`
- Projects: `http://localhost/khoders-website/api/projects-list.php`
- Search: `http://localhost/khoders-website/api/search.php?q=test`
- Event Details: `http://localhost/khoders-website/api/event-details.php?id=1`

### Database Tools
- phpMyAdmin: `http://localhost/phpmyadmin`
- Database Setup: `http://localhost/khoders-website/database/run_updates.php`

---

## Success Indicators

### ✅ Everything Working If:
1. Homepage loads without errors
2. Forms submit successfully
3. Admin login works
4. Dashboard shows statistics
5. Can create/edit/delete content
6. APIs return JSON responses
7. File uploads work
8. No PHP errors in browser

### 🎉 Ready for Testing!

If all checks pass, the system is fully functional and ready for use.

---

**Last Updated:** December 2024  
**Status:** Ready for Testing ✅
