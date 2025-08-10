# KHODERS Backend Setup

## Requirements
- PHP 7.4+
- MySQL 5.7+
- Apache/Nginx with mod_rewrite

## Installation

1. **Database Setup**
```bash
mysql -u root -p < database/schema.sql
```

2. **Configure Database**
Edit `config/database.php` with your database credentials.

3. **Set Permissions**
```bash
chmod 755 api/
chmod 644 api/*.php
```

## API Endpoints

### Contact Form
- **POST** `/api/contact.php`
- Body: `{name, email, subject, message}`

### Member Registration  
- **POST** `/api/register.php`
- Body: `{name, email, level, interests[]}`

### Newsletter Subscription
- **POST** `/api/newsletter.php`
- Body: `{email}`

### Get Events
- **GET** `/api/events.php`

### Get Projects
- **GET** `/api/projects.php`

## Admin Dashboard
Access at `/admin/` to view statistics.

## Security Features
- Input validation
- SQL injection protection
- CORS headers
- Rate limiting ready