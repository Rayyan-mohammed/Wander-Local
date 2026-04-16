# Wander Local

## Project Overview
Wander Local is a modern local travel platform connecting travelers with native hosts for unique, authentic experiences. Built with pure PHP 8+, PDO, Bootstrap 5, and Alpine.js.

## Tech Stack
- **Backend:** PHP 8+ (Vanilla structure without frameworks)
- **Database:** MySQL (PDO used for secure interactions)
- **Frontend:** HTML5, CSS3, Bootstrap 5, Alpine.js (for interactivity)
- **Rich Text:** Quill.js integration for Blog CMS

## Features
- Complete Authentication with role-based redirection (Traveler/Host)
- Multi-step Onboarding flows via Alpine.js
- Host Dashboards and Experience management
- Public Blog CMS with Markdown/Rich Text support & Like system
- Robust Security: CSRF tokens, Prepared SQL Statements, Input Sanitization, XSS defense
- Custom Error Handling and Caching system
- Mobile-first approach with bottom navigation bars

## Local Setup (XAMPP / WAMP)
1. Clone the repository into your `htdocs` or `www` directory.
2. In PHPMyAdmin, create a database named `wander_local`.
3. Import the `database/schema.sql` file into your empty database.
4. Rename `config/config.example.php` to `config/config.php` (if applicable) or verify settings in `config/config.php` inside the actual repo.
5. Set `APP_ENV` to `'development'` in `config/config.php`.
6. Go to `http://localhost/Wander_Local` in your browser.

## Deployment Instructions (Hostinger / InfinityFree)
1. Upload all files into `public_html` via FTP or File Manager.
2. Create a MySQL database and user in your hosting control panel. Import `schema.sql`.
3. Update `config/db.php` with your live database credentials.
4. Update `config/config.php` to set `APP_ENV = 'production'`.
5. Ensure file directories `/uploads/` and `/cache/` and `/logs/` have permissions set to `chmod 755` (folders) and `chmod 644` (files).
6. Verify `.htaccess` is present in the root.

## Architecture Notes
- Uses `includes/security.php` for all CSRF and upload logic.
- Cache engine uses simple JSON file writing in `/cache/` to minimize heavy SQL queries in production.

*(Screenshots to be added here)*
