# Known limitations

This document separates intentional scope decisions from gaps to address before or after launch.

## Delivery status

- **Not deployed.** No hosting, domain, database, or object-storage credentials were provided. See [deployment](deployment.md) for the remaining manual steps.
- No CI/CD pipeline, infrastructure-as-code, or production monitoring is included. Build/test commands are documented for manual or CI use.
- Browser QA was performed in headless Chrome (mobile 390×844 and desktop 1440×900). Safari, Firefox, and real-device testing were not performed.
- Accessibility was reviewed and exercised with keyboard-focused automated checks, but no formal WCAG audit or assistive-technology user testing was completed.

## Content and catalogue

- The seeded catalog, images, banners, FAQ, About, and policy pages are **demo placeholders**. Replace them with real business content and legally reviewed policies.
- No sales counts, customer reviews, ratings, certifications, or performance claims are included. Do not add unsupported claims.
- The storefront/admin UI is Arabic-only. Locale configuration and logical CSS properties are in place, but `lang/en` does not include storefront or admin translations, and there is no language switcher. English support requires translation and a locale-selection mechanism.
- There is no product import/export (CSV) and no bulk-edit UI.
- One store-wide currency is configured by code/symbol; amounts are not converted and products cannot use different currencies.
- Search uses SQL `LIKE` over name, SKU, brand, and short description. There is no full-text index, typo tolerance, or synonym handling; very large catalogs may need a search service.
- Social/contact fields are blank until configured. Nothing is invented by default.

## Commerce model

- **No online payments, checkout, cart, customer accounts, or order history** — by design.
- WhatsApp is the only order channel. The app creates an official `wa.me` link; it does not send messages on the customer’s behalf.
- A generated order link records a click event only. **Clicks are not sales**, and there is no conversion or revenue reporting.
- **Stock is not reserved or decremented automatically** when a customer opens WhatsApp. Staff must adjust inventory after confirming the sale.
- No order records, invoices, shipping labels, or tracking integrations exist.
- The contact form stores messages in the database. It does not send email or WhatsApp notifications, and there is no CAPTCHA — only a honeypot and rate limiting.
- Password-reset emails require a real mail transport. The default `MAIL_MAILER=log` writes emails to the log and will not deliver them.

## Security and operations

- The application sends common security headers but **no Content-Security-Policy**. Add CSP at the web server/CDN if required and test it in report-only mode.
- HSTS is sent only when Laravel detects HTTPS. Behind a reverse proxy, configure `TRUSTED_PROXIES`; otherwise HTTPS detection, HSTS, and generated URLs may be wrong.
- Rate limiting uses the configured cache store. Multiple app servers need a shared store (for example Redis) for consistent limits.
- Inventory has an audit trail; other admin edits (products, pages, users) do not have a general change log.
- Models use hard deletes; use the active/inactive flags to retire content. Product deletion is blocked when inventory history exists.
- Uploaded 3D models receive structural validation (GLB header/glTF JSON with meshes), not exhaustive glTF-spec or geometry validation.
- No 2FA or SSO for admin accounts.
- No automated malware scanning for uploaded files beyond type/content validation and image re-encoding.

## Performance

- The 3D viewer is a lazy-loaded chunk of roughly **1,025 kB (291 kB gzip)**. It is only requested when a visitor opens a product with a valid model, but it is still a large download on slow connections.
- Images are pre-generated at 480/960/1920 px in WebP. There is no on-the-fly image resizing or CDN transformation pipeline.
- The sitemap is generated per request. This is fine for a moderate catalog; very large catalogs should cache it or generate it with a scheduled job.
- SQLite is the default local database. Use MySQL/MariaDB for production concurrency; SQLite is acceptable for small/demo deployments.
- No queue worker or scheduler is required, but also no background jobs exist for tasks like image processing or sitemap generation.

## Storage

- Local uploads require `php artisan storage:link`. Without it, media returns 404.
- With S3, 3D models require bucket CORS because `model-viewer` fetches them. Images may appear fine while models fail if CORS is missing.
- Switching disks does not migrate existing files.
- Logos/favicons are stored as uploaded (no responsive variants). Product/category/banner/hero images are converted to WebP.

## UX and content management

- No drag-and-drop ordering; ordering uses numeric fields.
- Rich text is sanitized to a safe subset (`allowSafeElements` plus safe links); embeds such as iframes are removed.
- Filtered and search catalog pages are `noindex,follow`; they are intended for users, not search indexing.
- There is no cookie-consent UI. Add one if jurisdiction/privacy requirements demand it.
- Contact details, policy text, and delivery/payment wording must be reviewed by the business. The app cannot verify their accuracy.
- The admin interface is responsive, but extensive product editing is optimized for desktop.

## Before launch checklist

1. Deploy using [deployment.md](deployment.md) and verify every item there.
2. Replace demo content, imagery, and policy text.
3. Configure WhatsApp, real contact details, mail transport, and backups.
4. Rotate the demo admin password/remove seeding credentials.
5. Decide on Redis/shared cache for multi-server deployments.
6. Add CSP/WAF/consent tooling if required.
7. Run the automated tests and a manual smoke test on the production host.
