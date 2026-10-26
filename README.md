# SQUAD — Arabic-first sports store with WhatsApp ordering

A production-oriented, Arabic-first RTL e-commerce storefront for a sports club shop (footwear, jerseys and tees, training sets, accessories), paired with a role-based admin panel. Orders are completed through WhatsApp: there is **no checkout, payment gateway, or customer account area**.

The application was built and verified locally on Windows with PHP 8.4.5. It has **not** been deployed; deployment configuration and manual steps are documented in [docs/deployment.md](docs/deployment.md).

## Stack

| Layer | Technology |
| --- | --- |
| PHP | 8.4.5 tested; Composer constraint `^8.3` |
| Framework | Laravel 12.70.1 |
| Auth | Laravel Breeze-derived session auth (login, password reset only) |
| Database | SQLite locally; MySQL/MariaDB supported |
| Templates | Blade, server-rendered |
| CSS | Tailwind CSS 4.3.3 (logical properties, RTL-first) |
| JavaScript | Alpine.js 3.17.5, Vite 8.3.5 |
| Images | Intervention Image 4.4 (GD driver), WebP at 480/960/1920 |
| 3D | `@google/model-viewer` 4.4, lazy-loaded only when a product has a valid model |
| Tests | Pest 4.7.8 / PHPUnit |

## What is included

### Storefront

- Arabic RTL homepage with configurable hero, featured categories, featured/new/sale products, about section, and WhatsApp CTA.
- All-products catalog with server-side search, category, price, size, color, availability, featured and sale filters, sorting, pagination, active filter chips, and clear-all.
- Nested category pages (a parent category includes descendants).
- Product page with responsive gallery, click-to-zoom dialog, variant selection (size/color), stock state, required-option validation, share links, specifications, related products, and optional 3D viewer.
- WhatsApp order flow: the server validates the product, variant, price source, stock, and quantity, then redirects to an official `wa.me` link with a prefilled Arabic message.
- About, Contact (validated, honeypot-protected form stored in the database), FAQ, and configurable policy pages.
- Branded 404/403/419/429/500/503 pages, sitemap, robots, canonical/OG metadata, and Product/Breadcrumb JSON-LD.
- Mobile-first layout with keyboard-accessible overlays, focus management, scroll locking, and reduced-motion support.

### Admin panel

- Dashboard and WhatsApp-click analytics (clicks, not sales).
- Products: CRUD, slug/SKU uniqueness, pricing, SEO, specifications, image gallery, variants, optional GLB/glTF model upload, active/featured/new flags.
- Categories with parent/child hierarchy and ordering.
- Inventory adjustments with row locking, transaction safety, and an audit trail.
- Content: pages, banners, FAQs, homepage and store settings.
- Media library, contact messages, user/role management, profile and password.
- Five roles: Super Admin, Store Manager, Product Manager, Inventory Manager, Content Manager. Every admin route is authorized server-side.

### Security and SEO

- CSRF on state-changing requests, hashed passwords, mass-assignment protection, policy/gate checks, rate limiting on login-adjacent and public POST endpoints.
- Upload validation by real MIME type and dimensions, content validation for 3D files, sanitized rich text, escaped Blade output, safe external URL handling.
- Security headers (nosniff, frame options, referrer policy, permissions policy, COOP, HSTS when HTTPS).
- Indexable pages use canonical URLs and metadata; filtered/search pages are `noindex,follow`. Inactive and deleted records are excluded from the storefront and sitemap.

## Requirements

- PHP 8.3+ with `pdo`, `mbstring`, `gd`, `fileinfo`, `openssl`, `tokenizer`, `xml`, `ctype`, `json` (Laravel 12 requirements).
- Composer 2.
- Node.js 20+ and npm.
- SQLite (default) or MySQL/MariaDB.
- A writable `storage/` and `bootstrap/cache/` directory.

## Quick start (local)

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Configure `.env`:

- `APP_URL` must match the URL you browse (for example `http://localhost:8000`).
- For SQLite, create the file: `touch database/database.sqlite`.
- For MySQL, uncomment the `DB_*` values and create an empty database with `utf8mb4`.
- Optionally set `SEED_ADMIN_EMAIL` and `SEED_ADMIN_PASSWORD` to create the first Super Admin.
- Optionally set `STORE_WHATSAPP_NUMBER`; a real number can also be set in the admin settings.

Then:

```bash
php artisan migrate --seed
php artisan storage:link
npm install
npm run build
php artisan serve
```

Open <http://localhost:8000>. The admin panel is at `/admin` and login is at `/login`.

> The seeders create demo categories, products, images, and content pages. Replace them with real catalog data before launch. If `SEED_ADMIN_EMAIL`/`SEED_ADMIN_PASSWORD` are blank, no admin user is created.

### Windows note

If multiple PHP versions are installed, run commands with the PHP 8.3+ binary explicitly, for example:

```powershell
& "C:\path\to\php-8.4\php.exe" artisan migrate --seed
```

Do not use a PHP 8.2 binary — it does not satisfy the Composer platform requirement.

## Development

Run the Laravel server and Vite in one command:

```bash
composer run dev
```

Or use two terminals:

```bash
php artisan serve
npm run dev
```

Build production assets with `npm run build`. There are no queued jobs or scheduled tasks required for the storefront or admin flows; `QUEUE_CONNECTION` can remain `database` without a worker.

## First admin user

1. Set `SEED_ADMIN_EMAIL` and a strong `SEED_ADMIN_PASSWORD` in `.env` (never commit them).
2. Run `php artisan db:seed --class=AdminUserSeeder`.
3. Remove `SEED_ADMIN_PASSWORD` from `.env` after the user exists, and change the password from the admin profile screen.

Additional admin users are created from **Admin → Users** by a Super Admin. Public registration is disabled.

## WhatsApp setup

Set the real number in **Admin → Settings → General** (international format, digits only, for example `9665XXXXXXXX`) or via `STORE_WHATSAPP_NUMBER` before seeding. The service normalizes digits, rejects local/invalid formats, and builds `https://wa.me/<number>?text=...` links only. No unofficial APIs or automation are used.

A WhatsApp click is recorded as an analytics event; it is **not** a sale and does not reduce stock. See [docs/whatsapp-guide.md](docs/whatsapp-guide.md).

## Storage

- Default: Laravel `public` disk + `php artisan storage:link`.
- S3-compatible: set `MEDIA_DISK=s3` and the `AWS_*` variables. Configure bucket CORS for 3D model loading.
- Uploaded images are converted to WebP with responsive variants; logos/favicons are stored raw; 3D files are validated before storage.

## Testing and checks

```bash
php artisan test
vendor/bin/pint --test
npm run build
```

Last verified locally: **101 tests, 493 assertions passing** (Pest), plus 48 automated browser checks at 390×844 and 1440×900 and a separate 3D/gallery browser check. Details are in [docs/final-report.md](docs/final-report.md).

## Documentation

- [Installation](docs/installation.md)
- [Deployment](docs/deployment.md)
- [Admin guide](docs/admin-guide.md)
- [Content guide](docs/content-guide.md)
- [Inventory guide](docs/inventory-guide.md)
- [WhatsApp guide](docs/whatsapp-guide.md)
- [Known limitations](docs/known-limitations.md)
- [Final report](docs/final-report.md)

## Known limitations (summary)

- Not deployed; hosting credentials were not provided.
- Demo catalog/content must be replaced; no customer reviews or sales figures are included.
- Arabic UI only. Locale configuration and RTL/LTR direction handling exist, but English storefront/admin translation files are not provided.
- No online payments, checkout, customer accounts, or automatic stock decrement (by design).
- No Content-Security-Policy is set by the app; configure one at the web server/CDN if required.
- The 3D `model-viewer` chunk is ~1,025 kB (~291 kB gzip) and is loaded only when a visitor opens a product 3D view.

See [docs/known-limitations.md](docs/known-limitations.md) for the full list.
