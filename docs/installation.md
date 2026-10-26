# Installation guide

This guide covers a clean local installation. The application is Laravel 12 and requires PHP 8.3 or newer (verified with PHP 8.4.5).

## 1. Prerequisites

- PHP 8.3+ with extensions: `ctype`, `curl`, `dom`, `fileinfo`, `gd`, `json`, `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`.
- Composer 2.
- Node.js 20+ and npm.
- SQLite (default) or MySQL 8+/MariaDB 10.6+.
- Git (optional, for version control).

Check versions:

```bash
php -v
composer -V
node -v
npm -v
```

If several PHP versions are installed, make sure the one on your `PATH` is 8.3+ before running Composer or Artisan. On Windows you can invoke a specific binary:

```powershell
& "C:\Program Files\php\php.exe" artisan --version
```

## 2. Install dependencies

```bash
composer install
```

If Composer refuses the platform requirement, it is using an older PHP. Run Composer with the correct binary:

```bash
"/path/to/php8.4" /path/to/composer.phar install
```

## 3. Create the environment file

```bash
cp .env.example .env
php artisan key:generate
```

On Windows PowerShell use `Copy-Item .env.example .env`.

Edit `.env`:

| Variable | Notes |
| --- | --- |
| `APP_URL` | Must match the URL used in the browser, including port. |
| `APP_ENV` | `local` for development. |
| `APP_DEBUG` | `true` locally; `false` in production. |
| `APP_TIMEZONE` | Defaults to `Asia/Riyadh`. |
| `APP_LOCALE` | Default interface locale when a visitor has not chosen one: `ar` (Arabic RTL) or `en` (English LTR). |
| `SEED_ADMIN_EMAIL` | Optional; required only to create the first admin. |
| `SEED_ADMIN_PASSWORD` | Optional; use a strong temporary password and remove afterwards. |
| `STORE_WHATSAPP_NUMBER` | Optional; can be set later in admin settings. |
| `MEDIA_DISK` | `public` locally, `s3` for object storage. |
| `TRUSTED_PROXIES` | Leave blank unless behind a trusted reverse proxy. |

Never commit `.env` or real credentials.

## 4. Database

### SQLite (default)

```bash
touch database/database.sqlite
php artisan migrate --seed
```

PowerShell: `New-Item database/database.sqlite`.

### MySQL / MariaDB

Create an empty database with `utf8mb4`:

```sql
CREATE DATABASE squad_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'squad'@'localhost' IDENTIFIED BY 'replace-with-a-strong-password';
GRANT ALL PRIVILEGES ON squad_store.* TO 'squad'@'localhost';
FLUSH PRIVILEGES;
```

In `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=squad_store
DB_USERNAME=squad
DB_PASSWORD=replace-with-a-strong-password
```

Then:

```bash
php artisan migrate --seed
```

### Seeding options

`php artisan db:seed` runs:

1. `AdminUserSeeder` — only creates a user when `SEED_ADMIN_EMAIL` and `SEED_ADMIN_PASSWORD` are set.
2. `StoreSettingsSeeder` — store defaults and seeded homepage images.
3. `CategorySeeder`, `ProductSeeder`, `ContentSeeder` — **demo catalog and content**.

For a real store, seed only what you need. For example:

```bash
php artisan migrate
php artisan db:seed --class=StoreSettingsSeeder
```

Create the first admin explicitly:

```bash
SEED_ADMIN_EMAIL=owner@example.com SEED_ADMIN_PASSWORD='temporary-strong-password' \
  php artisan db:seed --class=AdminUserSeeder
```

Then remove `SEED_ADMIN_PASSWORD` from `.env` and change the password in the admin profile.

> Demo products, images, pages, and FAQs are placeholders. Replace them before launch. The project does not include fabricated sales counts or customer reviews.

## 5. Storage link

```bash
php artisan storage:link
```

Uploaded media is served from `/storage/...`. Without this link, images return 404.

For S3-compatible storage, set `MEDIA_DISK=s3` and the `AWS_*` variables instead. See [deployment](deployment.md).

## 6. Frontend build

```bash
npm install
npm run build
```

For development with hot reload:

```bash
npm run dev
```

## 7. Run locally

```bash
php artisan serve
```

Open <http://localhost:8000>. Admin login: `/login`, panel: `/admin`.

Alternatively run the server and Vite together:

```bash
composer run dev
```

If `composer run dev` cannot find a suitable PHP binary, use the two-terminal method with the correct PHP path.

## 8. First-run checklist

- [ ] `APP_URL` opens the storefront without mixed-content warnings.
- [ ] Demo credentials are known and stored securely; `SEED_ADMIN_PASSWORD` is removed from `.env`.
- [ ] WhatsApp number is set in **Admin → Settings → General** and the order button opens WhatsApp with a prefilled message.
- [ ] Contact phone/email/address/social URLs are real (or left blank — do not invent them).
- [ ] Policy pages contain real legal text for the business.
- [ ] A test image upload succeeds and appears in the storefront.
- [ ] Product variants and stock are correct.
- [ ] `php artisan test` passes.

## Troubleshooting

| Symptom | Cause / fix |
| --- | --- |
| Composer platform error | Composer is using PHP < 8.3. Run it with PHP 8.4. |
| `Database file at path ... does not exist` | Create `database/database.sqlite` or fix `DB_DATABASE`. |
| Images 404 | Run `php artisan storage:link`; check `APP_URL` and `MEDIA_DISK`. |
| Styles missing | Run `npm run build` (production) or `npm run dev` (development). |
| 419 page expired | Session/CSRF issue; clear cookies and check `APP_URL`/session config. |
| WhatsApp button disabled | `whatsapp_number` is empty or invalid; set it in admin settings. |
| 3D viewer does not load | The model must be a valid GLB/glTF. For S3, configure bucket CORS. |
| Admin 403 | The user’s role lacks the required permission; check **Admin → Users**. |
| Permission denied writing | Make `storage/` and `bootstrap/cache/` writable by the web/PHP user. |

## Related guides

- [Admin guide](admin-guide.md)
- [Content guide](content-guide.md)
- [Inventory guide](inventory-guide.md)
- [WhatsApp guide](whatsapp-guide.md)
- [Deployment](deployment.md)
