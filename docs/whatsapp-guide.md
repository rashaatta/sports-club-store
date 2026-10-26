# WhatsApp ordering guide

Ordering is intentionally WhatsApp-only. There is no checkout, payment gateway, cart, customer account, or order database. The application prepares a prefilled message and sends the customer to the official `wa.me` domain.

## Configure the number

Set it in **Admin → Settings → General → WhatsApp number**, or set `STORE_WHATSAPP_NUMBER` before seeding.

- Use the international format without a leading `+` or `00`: `9665XXXXXXXX`.
- The service removes spaces, dashes, and other non-digit characters.
- `00` at the start is stripped.
- Numbers starting with `0` are rejected (they are local, not international).
- Length must be 8–15 digits.
- If the number is blank or invalid, order buttons are disabled and the UI shows a “WhatsApp not configured” message. Contact links are hidden instead of pointing to a broken URL.

Only official `https://wa.me/<number>` links are generated. No WhatsApp Business API, browser automation, scraping, or unofficial integration is used.

## What the customer sees

On a product page:

1. Choose size/color when the product has required variants.
2. Choose a quantity (1 up to the smallest of available stock, the configured maximum, and 20 by default).
3. Press the order button.
4. The browser opens a blank tab, then POSTs the selection to the server.
5. On success, the blank tab navigates to WhatsApp with a prefilled message in the customer's active interface language (Arabic or English). If popups are blocked, the current tab is used.
6. On failure, the tab closes and an inline error explains the problem.

For simple products, stock and price come from the product. For variant products, price comes from the variant override when present, otherwise from the product.

## Message content

The prefilled message contains:

- Greeting.
- Product name and public URL.
- SKU (variant SKU when applicable).
- Displayed price at the time of the request.
- Size and color when applicable.
- Quantity.
- A request to confirm availability, delivery cost, and cash-on-delivery payment.

The text is defined in `lang/ar/storefront.php` under `whatsapp`. Change it if your process requires different details, and keep it factual (no delivery times or payment promises that are not true).

## Server-side validation

The POST endpoint `/products/{product}/order` is protected by CSRF and rate limiting (30 requests/minute per client). Before returning a URL, the server:

- Rejects inactive products.
- Requires an active variant when the product uses variants, and verifies it belongs to the product.
- Rejects a variant ID for simple products.
- Validates quantity (integer, 1–20 by default).
- Checks stock against the database.
- Recomputes the price from the database; the browser cannot set the price.
- Returns a 422 JSON validation error when anything is unavailable.

The URL is generated only after these checks pass.

## Rate limits and abuse controls

| Endpoint | Limit |
| --- | --- |
| Product order (`POST`) | 30 requests/minute |
| Contact form (`POST`) | 5 requests/minute |
| Login (`POST`) | Laravel Breeze defaults |
| Password reset (`POST`) | 5 requests/minute |

Add a WAF/CDN rate limit in front of production if needed. Rate limiting uses the configured cache store; use Redis for multiple app servers.

## Contact form

The contact page stores messages in the database for **Admin → Messages**.

- Name, email, and message are required; phone is optional.
- A hidden honeypot field (`website`) silently accepts bot submissions without storing them.
- Messages are sanitized/escaped on output; plain text only.
- There is no email notification. Staff must check the admin panel.
- No CAPTCHA is included; the honeypot plus rate limit reduce casual spam. Add a provider if spam becomes a problem.

## Analytics: clicks, not sales

Every successful order-link generation records an `analytics_events` row (`whatsapp_order_click`) with product, optional variant, source, and timestamp. The dashboard/analytics page reports clicks and top products over 30 days.

These numbers are **not** sales, revenue, conversion, or delivery metrics. A click may be cancelled, repeated, or never sent. Do not publish them as sales figures.

No customer name, phone, or message is stored for WhatsApp orders. The contact form does store the data the visitor submits.

## Operational flow

1. Customer chooses a product and opens WhatsApp.
2. Staff confirm availability, size, delivery cost, and payment method in WhatsApp.
3. Stock is **not** reduced automatically. After the sale is confirmed, adjust stock in **Admin → Inventory** (see the [inventory guide](inventory-guide.md)).
4. If a customer reports that the link does not open, check the number format and whether the device has WhatsApp installed.

## Troubleshooting

| Symptom | Cause / fix |
| --- | --- |
| Button disabled | WhatsApp number is missing/invalid, options are incomplete, or stock is zero. |
| “Choose size and color” error | Required variant options were not selected. |
| “Selected option is unavailable” | Variant was deactivated or stock changed; refresh the page. |
| Link opens WhatsApp but no text | Some clients strip prefilled text; the message is in the link. |
| Popup blocked | The app falls back to navigating the current tab. |
| 429 response | Rate limit hit; wait and retry. |
| No messages in admin | The form has no email delivery; check **Admin → Messages** and the logs. |

## Privacy and compliance

- Product URLs, SKU, and prices are included in the message; do not include internal notes or cost prices.
- Do not add tracking parameters containing personal data to the product URL.
- Contact-form data retention/deletion is managed by staff from the admin panel; define a retention policy for your business.
- Display accurate privacy policy text that reflects WhatsApp communication and stored contact messages.
