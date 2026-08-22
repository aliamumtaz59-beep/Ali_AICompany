# Armadio — PHP site

A plain PHP + HTML/CSS/JS conversion of the original Next.js/React "Armadio"
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

## Design

The site uses a "Premium & Editorial" look — deep ink/charcoal panels, muted
brass/gold accents, warm paper background, serif display headlines (Fraunces)
with small-caps tracked labels, hairline rules instead of heavy shadows, and
full-bleed photo bands on the hero, page headers, and other feature sections.

### Photo checklist

Real photos aren't included yet — every photo slot below currently shows a
tasteful duotone gradient placeholder in the site's palette instead of a
broken-image icon. **Drop a JPG at the exact path listed and it appears
automatically** — no code changes needed. Recommended size in brackets.

| Path (under `assets/images/photos/`)              | Used on                          | Size (approx.) |
|-----------------------------------------------------|-----------------------------------|-----------------|
| `hero-home.jpg`                                      | Homepage hero (full-bleed)        | 1920×1200       |
| `two-sides-bulk.jpg`                                 | Homepage "Buy in Bulk" card       | 1200×1400       |
| `two-sides-retail.jpg`                               | Homepage "Shop Retail" card       | 1200×1400       |
| `cta-banner.jpg`                                     | "Ready to trade?" band (all pages)| 1920×900        |
| `category-power-tools-trade-supplies.jpg`            | Category card/row                 | 1200×900        |
| `category-beauty-personal-care.jpg`                  | Category card/row                 | 1200×900        |
| `category-grocery-fmcg.jpg`                          | Category card/row                 | 1200×900        |
| `category-home-lifestyle.jpg`                        | Category card/row                 | 1200×900        |
| `hero-shop.jpg`                                      | Shop page header                  | 1920×900        |
| `hero-categories.jpg`                                | Categories page header            | 1920×900        |
| `hero-brands.jpg`                                    | Brands page header                | 1920×900        |
| `hero-contact.jpg`                                   | Contact page header               | 1920×900        |
| `hero-about.jpg`                                     | About page header                 | 1920×900        |
| `hero-buy-in-bulk.jpg`                                | Buy in Bulk page header            | 1920×900        |
| `hero-supply-to-us.jpg`                               | Supply to Us page header           | 1920×900        |
| `about-story.jpg`                                    | About page, "Our Story"           | 1600×1000       |

Product photos (shop page) are unrelated to this checklist and already work —
they live in `assets/images/products/` and are wired up in `data/products.php`.

### Brand logos

Unlike the photo slots above, brand logos in `assets/images/brands/` and
`data/brands.php` are **real trademarked logos**, not placeholders — supplied
directly by the site owner for brands Armadio actually stocks (DeWalt,
Makita, Milwaukee, L'Oréal, Bioderma, JOICO, Lee Kum Kee, Mae Ploy, NATCO,
TRS). To add a brand, drop its official logo file into
`assets/images/brands/` and add an entry to the `$brands` array in
`data/brands.php` with the correct `slug`, `category`, `origin`, `moq` and
`logo` path. Only use a brand's real logo if it's an actual stocked brand —
using a company's trademark implies a business relationship with them.

The "Home & Lifestyle" category currently has no brand assigned, so it shows
an empty state on the Brands page until one is added.

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
