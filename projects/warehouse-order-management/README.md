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
- Product stock is tracked via `products.quantity_pcs` (opening/total quantity); "Remaining Quantity" is always computed live (quantity_pcs minus everything ordered so far) rather than stored, so it's automatically correct after any order is added, edited, or deleted.
- Product images are stored under `public/uploads/products/` (publicly servable, unlike order attachments — product photos aren't sensitive) and shown as thumbnails on the Products list and Order view page.
- All `<select>` dropdowns are searchable (type to filter) via Tom Select, loaded from CDN in `includes/header.php`/`footer.php`.
- Reports has a "Detailed Report (Line Items)" type showing every order line (date, order #, shop, shop ID, product, barcode, quantity, unit) filterable by shop and product, in addition to the existing daily/monthly/product/custom reports.
- The dashboard is clickable throughout: KPI cards, chart bars/segments/points, and table rows link to the relevant filtered Orders/Products/Shops list.
