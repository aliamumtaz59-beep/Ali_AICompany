# Warehouse Order Management System

Simple PHP 8.2+ / MySQL 8+ MVP for recording and reporting warehouse orders.

## Setup

1. Create the database: `mysql -u root -p < database/schema.sql`
2. Set DB credentials via environment variables (or edit `config/config.php` defaults):
   `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`
3. Point your web server document root to this folder (or `php -S localhost:8000` for local testing).
4. Login with `admin` / `Admin@123` (change this password immediately via Users).

## Structure

- `config/` — DB connection and app config
- `includes/` — auth, CSRF, helpers, layout partials
- `models/` — Product, Order, User data access (PDO, prepared statements)
- `api/` — small JSON endpoints (order number generation)
- `database/schema.sql` — full schema + seed data
- Root `*.php` — pages (dashboard, orders, products, reports, users)

## Notes

- Orders do not affect inventory/stock — this version only records and reports orders, by design (see business rule in project brief).
- Architecture leaves room for future modules (multi-warehouse, stock, suppliers, purchase/sales orders, etc.) without breaking the current schema.
- Order attachments (support files/images) are capped at 10MB per file in-app, but PHP's own `upload_max_filesize` and `post_max_size` (in `php.ini`) may cap uploads lower by default (often 2MB) — raise both if needed. Uploaded files are stored under `uploads/orders/<order_id>/` with a `.htaccess` denying direct web access; they're only served through `api/attachment_download.php` (login required).
