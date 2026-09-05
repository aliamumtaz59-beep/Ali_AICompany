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
- "Detailed Report" (`report_detailed.php`) is a standalone page (own sidebar link) showing every order line (date, order #, shop, shop ID, product, barcode, quantity, unit), filterable by shop and product.
- The dashboard is clickable throughout: KPI cards, chart bars/segments/points, and table rows link to the relevant filtered Orders/Products/Shops list.
- All filter forms (Orders, Products, Shops, Reports, Detailed Report) auto-apply on every change — no "Apply"/"Filter" click needed — and fetch results via AJAX instead of reloading the page, via a small reusable engine in `public/js/app.js` (`initLiveFilter`). Each page detects the AJAX request (`is_ajax_request()` in `includes/functions.php`, checking the `X-Requested-With` header) and returns just the results-table fragment instead of the full page. The Dashboard's charts refresh the same way via a dedicated JSON endpoint (`api/dashboard_data.php`) instead of a fragment swap, since chart data can't be `innerHTML`'d in. All of this degrades gracefully to a normal full-page GET submit if JavaScript is unavailable.
- `public/css/style.css`, `public/js/app.js`, and `public/favicon.svg` are all loaded through `asset_url()` (`includes/functions.php`), which appends a `?v=<file mtime>` cache-buster — every deploy is automatically a new URL, so browsers (mobile especially) can't keep serving a stale cached copy after an update.

## Permissions

Access is no longer just admin/user. Every account has a `role` (`admin` or `user`) plus a `permissions` list (`users.permissions`, stored as a JSON array). **Admins always have every permission, regardless of what's stored.** For `user`-role accounts, access to each module is exactly whatever's been checked on their Users → Edit page:

| Permission | Grants |
|---|---|
| `products.view` | See the Products page/nav link |
| `products.manage` | Add/edit products, toggle active status |
| `orders.view` | See the Orders page/nav link, view individual orders |
| `orders.manage` | Create/edit orders, upload/remove attachments |
| `orders.delete` | Delete orders |
| `shops.view` | See the Shops page/nav link |
| `shops.manage` | Add/edit shops, toggle active status |
| `reports.view` | See Reports and Detailed Report |
| `users.manage` | See Users, create/edit accounts and their permissions (equivalent to admin over user management — a `user`-role account granted this can edit anyone's role/permissions, including escalating their own, so grant it carefully) |

The permission catalog lives in `includes/permissions.php` (`PERMISSIONS` constant); `user_has_permission($code)` checks it, `require_permission($code)` gates a page/action (403s otherwise). The Dashboard itself is intentionally ungated — it's a general overview available to any logged-in user.

**Upgrading an existing database:** run this once (existing `user`-role accounts will have no permissions until you edit them via Users and check the boxes you want — they previously had broad implicit access under the old model, so review each one):
```sql
ALTER TABLE users ADD COLUMN permissions TEXT NULL AFTER role;
```
