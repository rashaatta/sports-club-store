# Admin guide

The admin panel lives at `/admin` and requires a user account created by a Super Admin (public registration is disabled).

## Roles and permissions

| Area | Super Admin | Store Manager | Product Manager | Inventory Manager | Content Manager |
| --- | :-: | :-: | :-: | :-: | :-: |
| Dashboard | ✓ | ✓ | ✓ | ✓ | ✓ |
| Products & categories | ✓ | ✓ | ✓ | — | — |
| Inventory | ✓ | ✓ | ✓ | ✓ | — |
| Content (pages, banners, FAQs, homepage settings) | ✓ | ✓ | — | — | ✓ |
| Store/general settings | ✓ | ✓ | — | — | — |
| Media library | ✓ | ✓ | ✓ | — | ✓ |
| Contact messages | ✓ | ✓ | — | — | — |
| Users | ✓ | — | — | — | — |
| Analytics | ✓ | ✓ | ✓ | — | ✓ |

Permissions are checked server-side on every route and controller. Hiding a menu item is not the only protection. A user without permission receives 403.

## Signing in

1. Visit `/login`.
2. Enter the email/password created by the Super Admin.
3. After login you land on `/admin`.
4. Use the link in the sidebar/header to open the storefront.
5. **Profile** (sidebar footer or `/admin/profile`) lets you update your name/email and change your password.

Use **Log out** at the bottom of the sidebar on shared devices.

## Dashboard

The dashboard shows active products, out-of-stock count, low-stock count, active categories, recent stock adjustments, unread messages, WhatsApp-click KPIs, and the most-clicked products. Cards are permission-aware; a user only sees data for areas they can access.

WhatsApp-click metrics are **clicks**, not orders or sales. A click can be cancelled, repeated, or never sent.

## Products

**Admin → Products → Create/Edit.**

- Category is required. Create categories first.
- Name and SKU are required. Slug is optional; a unique slug is generated from the name when left blank.
- Prices use decimal money; `compare_at_price` must be greater than `price` and is used to display a discount.
- Descriptions are plain/rich text; HTML is sanitized before storage.
- Add specifications as key/value rows. They appear on the product page.
- Upload up to 10 gallery images per product (JPEG/PNG/WebP, 400–6000 px, max 4 MB each). Images are converted to responsive WebP automatically. Alt text can be set per image after upload.
- Optional GLB/glTF model: max 20 MB. The file is validated (header/JSON structure), not just by extension. The 3D button appears only when a valid model exists.
- SEO title and description are optional; fallbacks come from the store/SEO settings.
- `is_featured`, `is_new_arrival`, and `is_active` control storefront placement and visibility. Inactive products return 404.
- Stock for simple products is managed through the **Inventory** screen, not typed directly on the product form. Once a product has variants, variant stock is the source of truth.

> Changing a product slug changes its URL. Update external links after renaming.

### Variants

On the product edit screen, add size/color combinations with optional price overrides and stock. Variant stock is edited through **Inventory**, so every change is audited. On the storefront, the customer must choose all required options before ordering; the server re-validates the variant and stock.

### Deleting products

Deletion is blocked when the product has inventory adjustment history. Prefer deactivating a product (`is_active = false`) to remove it from the storefront while keeping its history and URL handling predictable.

## Categories

**Admin → Categories.**

- Supports parent/child hierarchy; a parent category page includes products from its active descendants.
- Display order controls navigation ordering.
- Image, description, and SEO fields are optional.
- Slugs are unique and used in `/category/{slug}`.
- Deactivate a category to hide it and its products from category navigation; products remain available if they are active and reachable elsewhere.

Deleting a category with products is blocked. Reassign or deactivate products first.

## Inventory

See the dedicated [inventory guide](inventory-guide.md). In short: **Admin → Inventory**, choose a product, select a variant if applicable, and record a stock adjustment. The system locks the row, prevents negative stock, and writes an audit entry.

## Content

- **Pages** — About/policy/custom pages. Rich text is sanitized; links allow `http`, `https`, and `mailto`. Footer visibility and sort order are configurable.
- **Banners** — editorial images used on the storefront.
- **FAQs** — question/answer pairs shown on the FAQ page.
- **Settings → Homepage** — hero text/image, featured/new/sale/about/why-us/WhatsApp sections, and their headings. Toggles hide sections without deleting content.

See the [content guide](content-guide.md) for writing and SEO guidance.

## Media library

**Admin → Media** uploads reusable images (JPEG/PNG/WebP, 400–6000 px, max 4 MB). Access is limited to product/content managers. Deleting a file still referenced by products, pages, banners, or settings is blocked.

## Contact messages

**Admin → Messages** lists messages from the public contact form. Messages can be marked read/unread and deleted. The form has a honeypot and rate limiting, but no CAPTCHA. No email notification is sent; messages live in the database.

## Users

**Admin → Users** (Super Admin only) creates and edits admin users, assigns one of the five roles, and resets passwords. You cannot change your own role or delete your own account, and the last Super Admin cannot be demoted or deleted. Deleting a user revokes access immediately.

Passwords are hashed; existing passwords are never displayed.

## Store settings

**Admin → Settings.**

- **General** — store name, tagline, announcement, WhatsApp number, contact phone/email/address/business hours, social URLs, currency code/symbol, logo, favicon, default SEO title/description, footer note.
- **Homepage** — homepage copy, images, section toggles, and CTA text.

Only users with the relevant permission can open each group. Setting a blank contact field hides it on the storefront; do not enter invented contact details.

## Analytics

**Admin → Analytics** shows 30-day totals, today’s clicks, a 7-day series, and top products by WhatsApp clicks. Use it to understand interest, not revenue.

## Operational tips

- Replace demo products, images, pages, and policies before launch.
- Keep the WhatsApp number in international format (`9665XXXXXXXX`).
- Use deactivation instead of deletion when you need to preserve history.
- After bulk catalog changes, spot-check the catalog filters, search, and product pages.
- Check `storage/logs/laravel.log` if a save fails unexpectedly; validation errors appear inline in the panel.
