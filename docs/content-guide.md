# Content guide

The storefront is Arabic-first and right-to-left. All storefront copy, contact information, and product data are editable from the admin panel; no code changes are needed for normal content updates.

## Where content lives

| Content | Location |
| --- | --- |
| Store name, tagline, announcement, contact details, social links, currency, logo, favicon, default SEO | **Settings → General** |
| Hero, homepage section headings/toggles, about block, WhatsApp CTA | **Settings → Homepage** |
| Categories and descriptions | **Categories** |
| Products, images, variants, specifications, SEO | **Products** |
| About and policy pages | **Pages** |
| Editorial promo images | **Banners** |
| FAQ questions and answers | **FAQs** |
| Navigation and footer | Categories (navigation), Pages (footer links), and settings |

## General settings

Fields are optional unless stated. Blank contact fields are simply not shown on the storefront — never invent a phone number, email, address, or social account.

- **Store name** is used in the header, titles, WhatsApp greeting, and defaults.
- **Announcement** appears in a dismissible top bar when enabled.
- **WhatsApp number** must be in international format. The service strips spaces/symbols and rejects numbers that start with `0` or are too short/long.
- **Currency code/symbol** affect formatted prices across the storefront and order message. Changing currency does not convert existing price values.
- **Logo** is stored as a raw image (SVG is not accepted; use PNG/WebP with transparency). **Favicon** likewise uses JPEG/PNG/WebP; a bundled SVG is the fallback.
- **SEO title/description** are fallbacks for pages that do not define their own metadata.

## Homepage

Each section can be hidden without deleting its content:

- Hero (image, eyebrow, title, highlighted title, subtitle, CTA label).
- Featured categories (uses categories marked **featured**, or the first four active root categories if none are featured).
- Featured products (products marked **featured**).
- New arrivals (products marked **new arrival**).
- Offers (products with a `compare_at_price` greater than `price`).
- About block (title, short text, image).
- Why-us block and WhatsApp CTA.

Use high-quality landscape or portrait photography consistent with the dark charcoal/white/volt palette. The homepage uses generated/demo imagery by default; replace it with your own store imagery.

## Pages

**Admin → Pages.**

- `about-us` is reserved: the `/about` route renders that page and `/pages/about-us` redirects to `/about`. Keep the slug if you want the About link to work. Deactivating it makes `/about` return 404.
- Policy pages (privacy, terms, shipping, returns) are seeded as placeholders. Replace the text with legally reviewed content for the business and jurisdiction.
- Content supports a safe subset of HTML. The sanitizer allows common safe elements plus relative links and `http`/`https`/`mailto` links; scripts, event handlers, iframes, and unsafe schemes are removed.
- **Show in footer** adds the page to the footer; **sort order** controls its position.
- **Active** controls public visibility. Inactive pages are excluded from the sitemap.
- Meta title and description are optional; content text is used as a fallback description.

## Banners

**Admin → Banners → Create.**

- Upload an image at least 800×400 px (max 6000×6000, 4 MB). Images are converted to responsive WebP.
- Title and subtitle are used as overlay copy; button label is optional.
- Link may be a relative path (`/products`) or an absolute `http(s)` URL. Other schemes are rejected.
- Placement is `home_promo`. Up to two active banners are shown on the homepage.
- Optional start/end dates control when the banner is active. The end date must be after the start date.

## FAQs

**Admin → FAQs.** Each entry has a question, answer, sort order, and active toggle. Answers are rendered as plain text; keep them concise. Ordering is controlled by sort order, then question.

## Products and categories

Product copy should answer: what it is, materials, fit, intended use, and care. Avoid unsupported performance claims. Specifications are key/value rows displayed as a definition list.

- Category names and descriptions affect navigation, category pages, and SEO fallbacks.
- Slugs are unique. Changing a slug changes the URL; update links accordingly.
- Product images: up to 10 per product, 400×6000 px, max 4 MB each. The first image is the primary gallery image. Add descriptive alt text for accessibility and SEO.
- SEO title/description override the defaults. Missing ones fall back to the product name and short description.

## Media library

**Admin → Media** stores reusable uploads. It accepts JPEG/PNG/WebP (400–6000 px, max 4 MB) and optimizes them to WebP. Files referenced by content cannot be deleted. 3D models are uploaded from the product form, not the media library.

## SEO checklist

- [ ] Each key page has a unique title and description.
- [ ] Product names are descriptive and not duplicated.
- [ ] Images have meaningful alt text.
- [ ] Policies and About reflect the real business.
- [ ] WhatsApp/contact details are correct.
- [ ] Inactive/test records are deactivated before launch.
- [ ] `APP_URL` is the production HTTPS domain so canonicals and the sitemap are correct.
- [ ] Submit `/sitemap.xml` to search engines after launch.

Filtered and search-result catalog URLs are set to `noindex,follow` automatically; the clean catalog and category pages remain indexable.

## Language, Arabic and RTL notes

- The interface is bilingual: visitors switch between Arabic (RTL) and English (LTR) from the header, mobile menu, login pages, and admin sidebar. The choice is remembered per browser.
- Catalog and page **content is single-language**. Names, descriptions, categories, pages, banners, FAQs, and SEO text are stored once and are not translated per locale. If you write them in Arabic, English visitors see Arabic content inside an English interface, and vice versa.
- Prices, SKUs, and Latin brand names render inside Arabic text with direction-aware utilities. The currency symbol changes from `ر.س` to `SAR` in English; amounts are not converted.
- Use Arabic punctuation and readable line lengths in Arabic content. Typography uses Cairo for body text and Anton for display headings in both directions.
- The layout uses CSS logical properties and LTR/RTL variants, so direction-sensitive icons and the admin sidebar adapt automatically. Test both directions when adding custom markup.

## What not to publish

- Invented reviews, sales counts, awards, certifications, or delivery promises.
- Contact details or social accounts that are not owned by the business.
- Placeholder policy text after launch.
- Unsupported product claims (medical, performance, or safety guarantees).
