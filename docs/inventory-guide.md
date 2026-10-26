# Inventory guide

Stock is managed from **Admin → Inventory**. Every stock change is recorded with the user, time, reason, and note.

## Stock source of truth

| Product type | Stock source | Notes |
| --- | --- | --- |
| Simple product (no variants) | `products.stock_quantity` | Adjusted from the Inventory screen. |
| Product with variants | Sum of active variant `stock_quantity` values | Every variant must be adjusted individually. |

If a product has at least one variant (active or not), the product-level stock field is ignored for availability and ordering. The product form shows a hint instead of allowing direct stock edits.

## Low-stock and out-of-stock

- **Out of stock**: total stock is 0.
- **Low stock**: total stock is greater than 0 and less than or equal to the product’s `low_stock_threshold` (minimum 1).
- For variant products, the total is the sum of active variants. A single low variant is not flagged on its own; review the variant breakdown on the product inventory page.
- The dashboard and inventory filters use these totals.

## Make an adjustment

1. Open **Admin → Inventory**.
2. Search by name/SKU or filter by low/out/in stock.
3. Open the product’s inventory page.
4. Select a variant when the product has variants (required).
5. Choose a mode:
   - **Add** — increase by the quantity.
   - **Remove** — decrease by the quantity; negative results are rejected.
   - **Set** — set the exact quantity (zero is allowed).
6. Choose a reason: stock received, correction, damaged, return, or other.
7. Add an optional note (max 255 characters) for context.
8. Submit. The list below shows the audit history.

A transaction with `lockForUpdate()` protects against simultaneous adjustments. If the resulting quantity equals the current quantity, no audit row is created and the UI reports “no change”.

## Recommended process after a WhatsApp order

WhatsApp clicks do **not** reserve or decrement stock. The app cannot know whether a customer sent the message, paid, or cancelled.

1. Confirm the order manually in WhatsApp.
2. After the sale is confirmed, adjust stock in the admin panel using **Remove** with reason `other` and a note such as the WhatsApp order reference, or use `correction`.
3. If an order is cancelled, no adjustment is needed; if stock was already removed, add it back with reason `return` or `correction`.
4. Periodically compare the admin stock with the physical count and use **Set** for corrections.

If you want stricter reservation behaviour, that requires a new order/reservation workflow; it is intentionally not part of this WhatsApp-only build.

## Order-time validation

When a customer submits the order form, the server:

1. Rejects inactive products (404).
2. Requires an active variant for variant products.
3. Verifies the variant belongs to the product and is active.
4. Checks the requested quantity is between 1 and `store.orders.max_quantity` (default 20).
5. Checks stock for the selected variant or simple product.
6. Recomputes the price from the database (variant override or product price) before building the message.

A browser-supplied price is never trusted. A failed check returns a validation error without opening WhatsApp.

## Audit history and deletion

`inventory_adjustments` stores before/change/after values, reason, note, user, and timestamp. Products with stock history cannot be deleted (to preserve the audit trail); deactivate them instead.

## Importing or bulk-changing stock

There is no CSV import. For large initial catalogs, either:

- Seed/import at the database level (development or a controlled migration), then verify in the panel, or
- Add stock through the inventory screen, one product/variant at a time.

If you import programmatically, write corresponding inventory adjustment rows when an audit trail is required. Direct database changes bypass validation and locking.

## Troubleshooting

| Symptom | Fix |
| --- | --- |
| “Variant required” | The product has variants; choose one before adjusting. |
| Negative stock error | Reduce the remove amount or use Set. |
| Product shows out of stock despite stock on the product form | It has variants; adjust the active variants instead. |
| Product cannot be deleted | It has inventory history. Deactivate it. |
| Stock differs from the physical count | Use Set with reason `correction` and add a note. |
