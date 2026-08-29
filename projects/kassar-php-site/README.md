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
  enquiry-form submissions and paid orders to `storage/enquiries.log` /
  `storage/orders.log`).

## Payments — Stripe

Checkout is wired up for real, live card payments via [Stripe
Checkout](https://stripe.com/payments/checkout) — a Stripe-hosted payment
page, so no card details ever touch this server (keeps you out of PCI
scope). It's called directly over Stripe's REST API with cURL, no
Composer/SDK required, matching the rest of this build.

**1. Get your API keys**
Sign up / log in at [dashboard.stripe.com](https://dashboard.stripe.com),
switch on **Test mode** (top-right toggle) while you set things up, then go
to **Developers → API keys** and copy the **Secret key** (`sk_test_...`).

**2. Add it to the site**
Open `includes/stripe-config.php` and paste it in:
```php
const STRIPE_SECRET_KEY = 'sk_test_...';
```
This file ships with empty placeholders and is meant to be edited directly
on your server after upload — never commit real keys to version control.

**3. Test it**
With the secret key in place, add something to the cart and hit **Proceed
to Checkout** — you'll be redirected to a real Stripe Checkout page. Pay
with a [Stripe test card](https://docs.stripe.com/testing#cards), e.g.
`4242 4242 4242 4242`, any future expiry date, any CVC and postcode. You'll
land back on `checkout-success.php` with an order confirmation.

**4. Set up the webhook (recommended before going live)**
The redirect back to `checkout-success.php` is only for the customer's
confirmation screen — if they close the tab or lose connection right after
paying, that page never loads, even though Stripe took the payment. A
webhook is the reliable way to record every paid order regardless:
1. In the Stripe Dashboard, go to **Developers → Webhooks → Add endpoint**.
2. Endpoint URL: `https://yourdomain.com/api/stripe-webhook.php`
3. Event to send: `checkout.session.completed`.
4. Copy the **Signing secret** (`whsec_...`) it gives you into
   `includes/stripe-config.php` as `STRIPE_WEBHOOK_SECRET`.

Every completed order then gets appended as JSON to `storage/orders.log`
(session id, amount, currency, customer email, shipping address) —
`storage/` is blocked from direct browser access via `.htaccess`.

**5. Go live**
Switch the Dashboard out of Test mode, swap `STRIPE_SECRET_KEY` for your
`sk_live_...` key, and create a second webhook endpoint (same URL) while in
live mode to get a live `whsec_...` — test-mode and live-mode webhook
secrets are different.

By default Checkout only offers card payments and collects a UK
(`GB`) shipping address — both are adjustable in `api/checkout.php`
(`payment_method_types` and `shipping_address_collection`).

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
  original React version. Checkout now takes real payments via Stripe (see
  "Payments — Stripe" below).
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
