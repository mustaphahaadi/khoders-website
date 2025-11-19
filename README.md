# 🚀 KHODERS WORLD - Campus Coding Club Platform

> A comprehensive, modern platform for managing a campus coding club with member management, event registration, course enrollment, and dynamic content management.

![Version](https://img.shields.io/badge/version-1.0.0-blue)
![PHP](https://img.shields.io/badge/PHP-8.0+-purple)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-blue)
![License](https://img.shields.io/badge/license-MIT-green)

---

## ✨ Features

### 👥 Member Management
- **Secure Authentication** - Argon2 password hashing, session management
- **Member Dashboard** - Personalized view with enrollments and profile
- **Profile Management** - Update info, change password, manage preferences

### 📚 Content Management (Admin Panel)
- **8 Content Types** - Events, Courses, Programs, Projects, Blog, Team, Skills, Resources
- **WYSIWYG Editors** - TinyMCE on all content forms
- **Featured System** - Control homepage prominence
- **Advanced Dashboard** - 30+ metrics with charts
- **Ratings & Reviews** - 5-star rating system for all content with admin moderation

### 🎓 Learning Platform
- **Dynamic Courses** - Database-driven with enrollment
- **Programs** - Multi-week training programs
- **Resources Library** - Filterable learning materials
- **Skill Tracking** - Technology areas and proficiency

### 🎉 Events & Engagement
- **Event Registration** - Members can register for workshops/hackathons
- **Calendar Integration** - Upcoming events display
- **Attendance Tracking** - Simple check-in system
- **Email Notifications** - Registration confirmations

### 📊 Analytics & Reporting
- **Member Growth Trends** - 6-month charts
- **Enrollment Analytics** - By type and time
- **Content Distribution** - Visual breakdown
- **Engagement Metrics** - Views, registrations, activity

---

## 🛠️ Tech Stack

- **Backend:** PHP 8.0+
- **Database:** MySQL 8.0+ / MariaDB 10.6+
- **Frontend:** HTML5, CSS3, JavaScript (ES6+)
- **Framework:** Bootstrap 5.3
- **Editor:** TinyMCE 6
- **Charts:** Chart.js 4
- **Icons:** Bootstrap Icons

---

## 📋 Prerequisites

- **XAMPP** 8.0+ (includes PHP 8.0+ and MySQL 8.0+)
- **Composer** (optional, for future dependencies)
- **Modern Browser** (Chrome, Firefox, Safari, Edge)

---

## 🚀 Quick Start

### 1. Clone/Download
```bash
# Clone to XAMPP htdocs
cd C:\xampp\htdocs
git clone <repository-url> khoders-website

# OR download and extract to C:\xampp\htdocs\khoders-website
```

### 2. Database Setup
```bash
# Start XAMPP (Apache + MySQL)
# Open phpMyAdmin: http://localhost/phpmyadmin

# Create database
CREATE DATABASE khoders_world;

# Import schema
# Import: database/schema.sql

# Run migrations (in order)
1. database/skills_table.sql
2. database/resources_table.sql
3. database/add_featured_flags.sql
```

### 3. Configuration
```bash
# Copy environment file
cp .env.example .env

# Edit .env with your database credentials
DB_HOST=localhost
DB_NAME=khoders_world
DB_USER=root
DB_PASSWORD=
```

### 4. Access the Platform
- **Homepage:** http://localhost/khoders-website/
- **Admin Panel:** http://localhost/khoders-website/admin/
  - Default: `admin` / `admin123` (Change immediately!)

---

## 📁 Project Structure

```
khoders-website/
├── admin/                  # Admin panel
│   ├── pages/             # Admin page views
│   ├── includes/          # Admin helpers
│   └── assets/            # Admin-specific assets
├── api/                   # REST API endpoints
├── assets/                # Public assets (CSS, JS, images)
├── config/                # Configuration files
├── database/              # SQL schemas and migrations
├── includes/              # Shared PHP includes
├── pages/                 # Public page views
├── public/                # Upload directory
├── .env                   # Environment config (create from .env.example)
├── index.php              # Main entry point
└── README.md             # This file
```

---

## 🔐 Security Features

- ✅ **CSRF Protection** - All forms protected
- ✅ **SQL Injection Prevention** - Prepared statements
- ✅ **XSS Protection** - Input sanitization
- ✅ **Password Security** - Argon2 hashing
- ✅ **Session Management** - Secure session handling
- ✅ **File Upload Validation** - Type and size checks

---

## 📖 User Guide

### For Members
1. **Register** - Visit homepage, click "Join Now"
2. **Login** - Access member dashboard
3. **Enroll** - Browse courses/programs and enroll
4. **Register for Events** - Click "Register" on event cards
5. **Manage Profile** - Update info in member dashboard

### For Admins
1. **Login** - Visit `/admin/`
2. **Dashboard** - View comprehensive metrics
3. **Manage Content** - Use editors for all content types
4. **Mark as Featured** - Check "Featured" checkbox to display on homepage
5. **View Analytics** - Check enrollment trends and member growth

---

## 🎨 Customization

### Branding
- Logo: `assets/img/khoders/logo.png`
- Colors: Edit `assets/css/main.css` (Primary: `#136ad5`)
- Site Name: Update in `includes/navigation.php`

### Email Templates
- Located in `api/` files (contact.php, register.php, etc.)
- Customize sender name and messages

---

## 🔧 Maintenance

### Backups
```bash
# Database backup (via phpMyAdmin or CLI)
mysqldump -u root khoders_world > backup_YYYY-MM-DD.sql

# File backup
# Copy entire khoders-website folder
```

### Updates
- **Database:** Apply new migrations from `database/` folder
- **Code:** Pull latest changes from repository
- **Dependencies:** Run `composer update` if using Composer

---

## 📊 Database Schema

- `members` - User accounts
- `events` - Workshops, hackathons, seminars
- `courses` - Individual courses
- `programs` - Multi-week training programs
- `projects` - Showcase projects
- `blog_posts` - Blog articles
- `team_members` - Leadership team
- `skills` - Technology areas
- `resources` - Learning materials
- `enrollments` - Member registrations

---

## 🐛 Troubleshooting

### Database Connection Error
- Check `.env` file credentials
- Ensure MySQL is running in XAMPP
- Verify database name exists

### Admin Login Not Working
- Check `admin_users` table has admin account
- Reset password via phpMyAdmin
- Clear browser cache

### Images Not Uploading
- Check `public/uploads/` folder permissions
- Verify upload size in `php.ini` (`upload_max_filesize = 10M`)
- Check `config/file-upload.php` settings

---

## 📚 API Documentation

### Public Endpoints
- `POST /api/contact.php` - Contact form
- `POST /api/newsletter.php` - Newsletter subscription
- `POST /api/register.php` - Member registration

### Member Endpoints (Requires Auth)
- `POST /api/enroll.php` - Course/program enrollment
- `POST /api/event-register.php` - Event registration

---

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

---

## 📄 License

This project is licensed under the MIT License - see LICENSE file for details.

---

## 👥 Authors

- **Khoders World Team** - Initial work and development

---

## 🙏 Acknowledgments

- Bootstrap team for the UI framework
- TinyMCE for the WYSIWYG editor
- Chart.js for analytics visualizations
- All contributors and community members

---

## 📞 Support

- **Email:** support@khodersworld.com
- **Website:** https://khodersworld.com
- **Documentation:** See `/docs` folder
- **Issues:** GitHub Issues page

---

**Made with ❤️ by Khoders World Team**
