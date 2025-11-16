# Database Directory

## Setup

Run `schema.sql` to create all database tables and default data:

```bash
mysql -u root -p khoders_db < schema.sql
```

Or import via phpMyAdmin.

## Files

- **schema.sql** - Complete database schema (use this)
- **setup.php** - Database setup script
- **migrate.php** - Migration utilities
- **db_functions.php** - Database helper functions
- **config.php** - Database configuration
- **check_and_insert_data.php** - Data verification script
- **run_updates.php** - Update runner

## Default Admin

- Username: `admin`
- Password: `Admin@2024!`
