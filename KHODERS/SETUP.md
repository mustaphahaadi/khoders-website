# KHODERS Website Setup Guide

## Quick Start

1. **Database Setup**
   ```bash
   # Create database
   mysql -u root -p
   CREATE DATABASE khoders_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   
   # Initialize tables
   php config/init.php
   ```

2. **Environment Configuration**
   ```bash
   # Copy environment template
   cp .env.example .env
   
   # Edit .env with your database credentials
   DB_HOST=localhost
   DB_NAME=khoders_db
   DB_USER=root
   DB_PASS=your_password
   ```

3. **Web Server**
   ```bash
   # Using PHP built-in server
   php -S localhost:8000
   
   # Or using Apache/Nginx
   # Point document root to the KHODERS directory
   ```

## Project Structure

```
KHODERS/
├── index.html              # Main homepage
├── style.css               # Consolidated styles (single source)
├── script.js               # Main JavaScript
├── sw.js                   # Service Worker
├── 
├── api/                    # Backend API endpoints
│   ├── contact.php         # Contact form handler
│   ├── register.php        # Member registration
│   ├── newsletter.php      # Newsletter subscription
│   ├── events.php          # Events API
│   └── projects.php        # Projects API
├── 
├── config/                 # Configuration files
│   ├── database.php        # Database connection
│   ├── env.php             # Lightweight .env loader
│   └── init.php            # Database initialization
├── 
├── admin/                  # Admin dashboard
│   └── index.php           # Admin interface
├── 
├── assets/                 # Static assets
│   ├── qwe.png             # Logo
│   ├── image-1.png         # Images
│   └── image-2.png
├── 
├── about.html              # Individual pages (organized at root)
├── services.html
├── projects.html
├── team.html
├── events.html
├── blog.html
├── contact.html
├── careers.html
├── faq.html
├── register.html
└── 
└── .env.example            # Environment template
```

## Features Fixed

### Frontend Improvements
- ✅ Consolidated CSS files (removed duplicates)
- ✅ Fixed CSS syntax errors
- ✅ Improved responsive design
- ✅ Enhanced accessibility features
- ✅ Optimized animations and transitions
- ✅ Added dark theme support
- ✅ Improved modal functionality

### Backend Enhancements
- ✅ Enhanced database configuration with environment variables
- ✅ Improved error handling and logging
- ✅ Added input validation and sanitization
- ✅ Implemented rate limiting
- ✅ Added proper HTTP status codes
- ✅ Enhanced security measures
- ✅ Created database schema with proper indexes

### API Endpoints

#### Contact Form (`/api/contact.php`)
- **Method**: POST
- **Fields**: name, email, subject, message
- **Features**: Rate limiting, validation, error handling

#### Member Registration (`/api/register.php`)
- **Method**: POST
- **Fields**: name, email, level, interests
- **Features**: Duplicate checking, interest validation

#### Newsletter (`/api/newsletter.php`)
- **Method**: POST
- **Fields**: email
- **Features**: Reactivation support, rate limiting

#### Events (`/api/events.php`)
- **Method**: GET
- **Parameters**: category, status, limit, offset
- **Features**: Filtering, pagination, formatted response

## Database Schema

### Members Table
```sql
CREATE TABLE members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    level ENUM('Beginner', 'Intermediate', 'Advanced') NOT NULL,
    interests JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Contacts Table
```sql
CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(500),
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Newsletter Table
```sql
CREATE TABLE newsletter (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    status ENUM('active', 'unsubscribed') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    unsubscribed_at TIMESTAMP NULL
);
```

### Events Table
```sql
CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    date DATE NOT NULL,
    time TIME,
    location VARCHAR(255),
    category ENUM('workshop', 'seminar', 'hackathon', 'meetup') DEFAULT 'workshop',
    max_participants INT DEFAULT 50,
    current_participants INT DEFAULT 0,
    status ENUM('upcoming', 'ongoing', 'completed', 'cancelled') DEFAULT 'upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## Security Features

- Input validation and sanitization
- SQL injection prevention with prepared statements
- Rate limiting on form submissions
- CORS headers properly configured
- Error logging without exposing sensitive information
- Environment-based configuration

## Performance Optimizations

- Consolidated CSS and JavaScript files
- Optimized database queries with indexes
- Lazy loading for images
- Compressed assets
- Efficient caching strategies

## Development Guidelines

1. **Code Style**: Follow PSR-12 for PHP, use consistent naming conventions
2. **Error Handling**: Always log errors, never expose sensitive information
3. **Security**: Validate all inputs, use prepared statements, implement rate limiting
4. **Performance**: Optimize queries, minimize HTTP requests, compress assets
5. **Accessibility**: Use semantic HTML, proper ARIA labels, keyboard navigation

## Troubleshooting

### Common Issues

1. **Database Connection Failed**
   - Check .env configuration
   - Verify MySQL service is running
   - Ensure database exists

2. **API Returns 500 Error**
   - Check PHP error logs
   - Verify database tables exist
   - Run `php config/init.php` to create tables

3. **CSS/JS Not Loading**
   - Check file paths in HTML
   - Verify web server configuration
   - Clear browser cache

### Logs Location
- PHP errors: Check your server's error log
- Application logs: Custom logs written to error_log()
- Rate limiting: Temporary files in system temp directory

## Deployment

### Production Checklist
- [ ] Set up SSL certificate
- [ ] Configure proper .env file
- [ ] Set up database backups
- [ ] Configure web server (Apache/Nginx)
- [ ] Enable PHP error logging
- [ ] Set up monitoring
- [ ] Test all API endpoints
- [ ] Verify email functionality

### Recommended Server Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache 2.4 or Nginx 1.18+
- SSL certificate
- At least 1GB RAM
- 10GB storage space
