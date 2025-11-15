# KHODERS Website Local Setup with XAMPP

Use this guide to spin up the KHODERS website on a Windows machine using XAMPP. It covers installation, configuration, database provisioning, and sanity checks to confirm everything works.

---

## 1. Prerequisites

1. **Download & Install XAMPP**
   - Get the latest PHP 8.x build from [apachefriends.org](https://www.apachefriends.org/index.html).
   - During installation keep Apache and MySQL selected. Optional modules are not required.
2. **Choose an install location**
   - The default (`C:\xampp`) works well. Make sure the installation folder is writable.
3. **Project source**
   - Clone or copy the `khoders-website` project into `C:\xampp\htdocs\khoders-website`.
4. **Required PHP extensions** (enabled by default in XAMPP): `mysqli`, `pdo_mysql`, `gd`, `fileinfo`, `json`, `mbstring`.
   - If any are disabled, open `C:\xampp\php\php.ini` and remove the leading `;` from the corresponding `extension=` line.

---

## 2. Start XAMPP Services

1. Launch **XAMPP Control Panel** as Administrator (right-click → *Run as administrator*).
2. Start **Apache** and **MySQL**. The module names turn green when running.
   - If a service fails to start, click *Config → Service and Port Settings* and ensure the default ports (Apache: 80/443, MySQL: 3306) are free.
3. Confirm Apache serves content:
   - Visit `http://localhost/` in the browser. You should see the XAMPP welcome page.

---

## 3. Configure Environment Variables

1. In the project root (`C:\xampp\htdocs\khoders-website`), copy the sample environment file:
   ```powershell
   copy .env.example .env
   ```
2. Edit `.env` with the credentials you plan to use while testing on XAMPP. For the default XAMPP MySQL installation (user `root`, empty password) you can use:
   ```dotenv
   DB_HOST=localhost
   DB_NAME=khoders_db
   DB_USER=root
   DB_PASS=""

   ADMIN_USERNAME=admin
   ADMIN_PASSWORD=admin123

   APP_ENV=development
   APP_DEBUG=true
   ```
3. Save the file. If you change the admin password later, update both the `.env` file and your notes.

> **Tip:** XAMPP’s MySQL root account has no password by default. For better security create a dedicated user after testing (see Section 5).

---

## 4. Provision the Database

### Option A – phpMyAdmin (recommended)
1. Open `http://localhost/phpmyadmin/`.
2. Click **Databases → Create database** and enter `khoders_db` (Collation: `utf8mb4_general_ci`).
3. Choose the new database, then use **Import** to upload:
   1. `database/schema.sql`
   2. `database/schema_updates.sql`
4. phpMyAdmin should report successful execution for both imports.

### Option B – Built-in Setup Script
1. Visit `http://localhost/khoders-website/database/setup.php`.
2. The script creates the database, runs the schema migrations, and provisions a limited MySQL user (`khoders_user` / `khoders123`).
3. Remove or secure `database/setup.php` after a successful run (it is for installation only).

---

## 5. (Optional) Create a Dedicated MySQL User

If you prefer not to run the site as root:

1. In phpMyAdmin → **User accounts → Add user account**.
2. Use:
   - Username: `khoders_user`
   - Host: `localhost`
   - Password: `khoders123` (or a strong custom password).
3. Under **Global privileges**, uncheck everything and instead choose **Database-specific privileges** for `khoders_db` with `SELECT, INSERT, UPDATE, DELETE`.
4. Update `.env` to use the new credentials.

---

## 6. Verify the Application

1. **Database connection test**
   - Visit `http://localhost/khoders-website/admin/login.php` and attempt to log in with your test credentials.
   - Successful login confirms the database connection is working. If login fails, recheck `.env` credentials and ensure MySQL is running.
2. **Frontend**
   - Navigate to `http://localhost/khoders-website/index.html` and browse the site.
3. **Admin portal**
   - Visit `http://localhost/khoders-website/admin/login.php`.
   - Sign in using the credentials from `.env` (default `admin` / `admin123`).
   - After login you should land on the dashboard (`/admin/index.php`).
4. **Forms**
   - Submit the contact, registration, and newsletter forms.
   - Check `http://localhost/khoders-website/admin/form-logs.php` to confirm submissions appear.

---

## 7. Troubleshooting

| Issue | Fix |
| --- | --- |
| Apache won’t start | Close other services using port 80/443 (Skype, IIS) or change Apache’s port via XAMPP Control Panel → Config. |
| MySQL won’t start | Stop external MySQL services or change the configured port to 3307 (update `.env` accordingly). |
| “Access denied for user” | Verify `.env` credentials. If using root with no password, ensure `DB_PASS=""` (quoted empty string). |
| 403 on `/admin/` | Ensure you’re logged in via `/admin/login.php`. The dashboard now requires authentication. |
| Missing PHP extensions | Enable extensions in `php.ini`, restart Apache. |
| Form submissions not saved | Confirm database tables were imported and review `admin/form-logs.php` for errors. |

---

## 8. Clean Up & Next Steps

1. Change the default admin password once testing is complete.
2. Remove `database/setup.php` from the deployed environment.
3. Consider enabling SMTP settings for email features before production use.
4. Read `SECURITY.md` and `INSTALL.md` for additional hardening guidance.

You now have a fully functional KHODERS environment running locally with XAMPP. 🎉
