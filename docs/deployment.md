# Deployment guide

**Deployment status: not performed.** Hosting and credentials were not available. This document contains the configuration and the remaining manual steps. Do not treat the project as live until those steps are completed and verified.

## 1. Production environment

Create a dedicated `.env` on the server (never commit it):

```dotenv
APP_NAME=SQUAD
APP_ENV=production
APP_KEY=base64:...            # php artisan key:generate
APP_DEBUG=false
APP_URL=https://store.example.com
APP_TIMEZONE=Asia/Riyadh

APP_LOCALE=ar
APP_FALLBACK_LOCALE=en

LOG_CHANNEL=stack
LOG_LEVEL=warning

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=squad_store
DB_USERNAME=squad
DB_PASSWORD=...

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

CACHE_STORE=database
QUEUE_CONNECTION=database

MEDIA_DISK=public             # or "s3"
TRUSTED_PROXIES=              # e.g. 10.0.0.10 or "*" only behind a trusted proxy

# Only when MEDIA_DISK=s3
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=
AWS_BUCKET=
AWS_URL=
AWS_ENDPOINT=
AWS_USE_PATH_STYLE_ENDPOINT=false

MAIL_MAILER=...
MAIL_FROM_ADDRESS=...
```

Notes:

- `APP_URL` must be the public HTTPS origin. It is used for canonical URLs, media URLs, and the session cookie domain behaviour.
- Keep `APP_DEBUG=false`. Debug pages can expose secrets and stack traces.
- Set `TRUSTED_PROXIES` when TLS terminates at a load balancer/reverse proxy. Without it, Laravel may not detect HTTPS, which disables HSTS and can generate `http://` URLs. Use specific proxy IPs where possible; `*` trusts every client and should only be used when the app cannot be reached directly.
- `SESSION_SECURE_COOKIE=true` requires HTTPS.
- The app has no queued jobs or scheduled tasks. A queue worker and cron entry are not required. `QUEUE_CONNECTION` may remain `database`.

## 2. Server requirements

- PHP 8.3+ (PHP 8.4 recommended) with the extensions listed in [installation](installation.md).
- MySQL 8+/MariaDB 10.6+ (recommended for production; SQLite is acceptable for small/demo deployments).
- Nginx or Apache with the document root pointing at `public/`.
- Node.js only if assets are built on the server; otherwise build in CI and upload `public/build/`.
- PHP upload limits above the app limits: images up to 4 MB and 3D models up to 20 MB. Set `upload_max_filesize=24M`, `post_max_size=25M`, and a matching web-server body limit.

## 3. Deploy steps

```bash
# 1. Put the application in maintenance mode (optional but recommended)
php artisan down --secret="one-time-bypass-token"

# 2. Install PHP dependencies without dev packages
composer install --no-dev --optimize-autoloader --no-interaction

# 3. Configure .env (see above) if not already present

# 4. Run migrations only
php artisan migrate --force

# 5. Create the public storage link (skip for S3)
php artisan storage:link

# 6. Build frontend assets
npm ci
npm run build

# 7. Cache configuration, routes, events, and views
php artisan optimize

# 8. Bring the site back
php artisan up
```

Do **not** run `php artisan migrate:fresh`, `db:wipe`, or the demo seeders against a live database. The demo seeders create placeholder catalog and content records. To create the first admin on production:

```bash
SEED_ADMIN_EMAIL=owner@example.com SEED_ADMIN_PASSWORD='temporary-strong-password' \
  php artisan db:seed --class=AdminUserSeeder
```

Then remove `SEED_ADMIN_PASSWORD` from the environment and change the password in the admin profile. Public registration is disabled.

### File permissions

The web/PHP user needs write access to:

- `storage/`
- `bootstrap/cache/`

Uploaded media lives in `storage/app/public/` when `MEDIA_DISK=public`. Do not make the whole project writable.

## 4. Web server

Nginx example:

```nginx
server {
    listen 443 ssl http2;
    server_name store.example.com;

    root /var/www/squad-store/public;
    index index.php;
    client_max_body_size 25m;

    # TLS configuration is managed here or by the hosting provider.

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

The app sends `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy`, COOP, and (on HTTPS) HSTS.

**No Content-Security-Policy is emitted by the application.** If your threat model requires CSP, add it at the web server/CDN and allow the Vite assets, `wa.me` navigation, and `model-viewer` resources. Test it in report-only mode first.

## 5. Object storage (optional)

Set `MEDIA_DISK=s3` and the `AWS_*` variables. The same disk is used for product images, banners, logos, and 3D models.

- Bucket permissions must allow public reads for media.
- Set `AWS_URL` when using a CDN or a custom endpoint.
- **Configure bucket CORS** — the 3D viewer fetches model files with `fetch()`, so a cross-origin S3 bucket without CORS fails even though images work. Example CORS policy:

```json
[
  {
    "AllowedOrigins": ["https://store.example.com"],
    "AllowedMethods": ["GET", "HEAD"],
    "AllowedHeaders": ["*"],
    "ExposeHeaders": ["ETag"],
    "MaxAgeSeconds": 3600
  }
]
```

- Verify one image upload and one 3D model after switching disks. Existing local files are not migrated automatically.

## 6. Post-deploy verification

- [ ] `https://<domain>/up` returns a healthy response.
- [ ] Home page, catalog, a category, a product page, About, Contact, FAQ, and a policy page render over HTTPS.
- [ ] Switch to English from the header, confirm the layout is LTR and the choice persists after reload, then switch back to Arabic (RTL). Repeat once in the admin sidebar.
- [ ] `/sitemap.xml` and `/robots.txt` resolve with the production domain.
- [ ] Admin login works; an incorrect password is throttled.
- [ ] Create/edit a product, upload an image, and confirm it displays on the storefront.
- [ ] Confirm a product with stock opens WhatsApp with the correct prefilled message, then verify stock/variant validation by attempting an unavailable combination.
- [ ] Contact form submits and the message appears in **Admin → Messages**.
- [ ] Upload a valid GLB/glTF and confirm the 3D viewer loads (or confirm it is omitted when no model exists).
- [ ] Check `storage/logs/laravel.log` for errors after the smoke test.
- [ ] Verify backups and log rotation.

## 7. Updates and rollback

1. Take a database and media backup.
2. Deploy code and run `composer install --no-dev --optimize-autoloader`.
3. Run `php artisan migrate --force`.
4. Rebuild assets if frontend files changed.
5. Run `php artisan optimize` (clear old caches if configuration structure changed).
6. Smoke-test.
7. Roll back code and restore the database/media if a migration is not backwards compatible.

`php artisan optimize:clear` clears cached config/routes/views when troubleshooting; re-run `php artisan optimize` afterwards.

## 8. Security checklist

- [ ] `APP_DEBUG=false`, `APP_ENV=production`.
- [ ] Strong, unique `APP_KEY`; database and S3 credentials limited to this app.
- [ ] HTTPS with a valid certificate; secure session cookie enabled.
- [ ] Trusted proxies configured correctly if behind a proxy/CDN.
- [ ] Demo admin removed or password rotated; no seeding credentials left in the environment.
- [ ] Demo catalog/content replaced with real data.
- [ ] Policy pages contain real legal text; contact details are real and configurable.
- [ ] `storage/` and `bootstrap/cache/` permissions are minimal.
- [ ] Backups for database and media, with restore tested.
- [ ] Log rotation and alerting configured.
- [ ] CSP evaluated at the edge if required.

## 9. Remaining manual steps

Deployment is **not** complete. Remaining work depends on the hosting environment:

1. Choose hosting, domain, and TLS.
2. Provision the database and S3 bucket (if used).
3. Create production `.env` with real values.
4. Deploy code, run migrations, build assets, and configure the web server.
5. Create the real admin user and remove seeding credentials.
6. Replace all demo content and configure WhatsApp/contact details.
7. Run the verification checklist above.

See [final-report.md](final-report.md) for what was verified locally.
