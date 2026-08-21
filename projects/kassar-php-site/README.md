# Kassar — PHP site

A plain PHP + HTML/CSS/JS conversion of the original Next.js/React "Kassar"
trading-platform site. No build step, no framework — just PHP includes for
shared layout/data and vanilla JS for interactivity.

## Running locally

Requires PHP 8+ (no extensions beyond the default build).

```bash
cd projects/kassar-php-site
php -S localhost:8000
```

Then open http://localhost:8000/index.php in a browser.

## Deploying

Upload the whole `kassar-php-site/` folder to any Apache/Nginx host with
PHP support, pointed at `index.php` as the site root. No database, no
Composer install, no build step required.

- `.htaccess` adds an optional pretty URL for brand pages
  (`/brands/forgeline-tools` → `brand.php?slug=forgeline-tools`) and blocks
  direct access to `storage/`. Safe to delete if your host doesn't run
  Apache/mod_rewrite — every page still works via its plain `.php` URL.
- Make sure the `storage/` directory is writable by PHP (used to log
  enquiry-form submissions to `storage/enquiries.log`).

## Structure

```
index.php, shop.php, categories.php, brands.php, brand.php, cart.php,
contact.php, about.php, buy-in-bulk.php, supply-to-us.php   — pages
includes/            — shared header/footer, helper functions
data/                 — product/brand/category/site content as PHP arrays
assets/css/style.css  — all styling (hand-written, no framework)
assets/js/cart.js     — localStorage-backed cart + add-to-cart buttons
assets/js/main.js     — mobile nav, scroll-reveal, brand filter, enquiry forms
api/enquiry.php       — enquiry-form submission handler
storage/              — enquiry submissions get appended here as JSON lines
```

## What's carried over from the original site

- All 9 pages, same content and copy.
- The retail cart (add to cart, quantity, remove, subtotal) — still
  client-side only, backed by the browser's `localStorage`, same as the
  original React version. Checkout is a UI-only placeholder, same as before.
- The brand directory's category filter + search, and the contact page's
  three enquiry modes (buyer / supplier / retail).
- The enquiry form now actually persists submissions server-side (appended
  to `storage/enquiries.log` as JSON) instead of only logging to the Next.js
  dev console. To email submissions instead, set `NOTIFY_EMAIL` in
  `api/enquiry.php`.

## Known simplifications

- Tailwind's utility classes were replaced with a hand-written stylesheet
  (`assets/css/style.css`) organised by component rather than by utility —
  colors, spacing and layout are recreated to match, not byte-for-byte
  copied from Tailwind's generated CSS.
- No client-side router: every link is a normal `<a>` navigation (full page
  load), as is standard for a PHP multi-page site.
