# KHODERS Coding Club Website

Modern, responsive website for KHODERS Coding Club built with HTML, CSS, JS, and PHP.

## Quick Start

1. **Setup Database**
   ```bash
   mysql -u root -p khoders_db < database/schema.sql
   ```

2. **Configure Environment**
   ```bash
   cp .env.example .env
   # Edit .env with your settings
   ```

3. **Access Site**
   - Frontend: `http://localhost/khoders-website/`
   - Admin: `http://localhost/khoders-website/admin/`
   - Default admin: `admin` / `Admin@2024!`

## Project Structure

```
khoders-website/
├── admin/          # Admin panel
├── api/            # API endpoints
├── assets/         # CSS, JS, images
├── config/         # Configuration files
├── database/       # Database schema
├── docs/           # Documentation
├── forms/          # Form handlers
├── includes/       # PHP includes
├── logs/           # Application logs
├── pages/          # Page templates
└── public/         # Public uploads
```

## Documentation

See [docs/README.md](docs/README.md) for complete documentation.

## Key Features

- Responsive design
- Secure forms with CSRF protection
- Admin panel
- Database integration
- Event management
- Blog system
- Course/program management

## Tech Stack

- PHP 7.4+
- MySQL
- Bootstrap 5.3.x
- JavaScript (Vanilla)

## License

All rights reserved. Property of KHODERS Coding Club.
