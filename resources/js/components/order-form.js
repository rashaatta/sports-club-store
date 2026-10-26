export default function orderForm(config = {}) {
    return {
        variants: config.variants ?? [],
        hasVariants: Boolean(config.hasVariants),
        simpleStock: Number(config.simpleStock ?? 0),
        maxOrderQuantity: Number(config.maxQuantity ?? 20),
        lowStockThreshold: Number(config.lowStockThreshold ?? 5),
        currency: config.currency ?? '',
        orderUrl: config.orderUrl ?? '',
        whatsappConfigured: Boolean(config.whatsappConfigured),
        messages: config.messages ?? {},

        size: null,
        color: null,
        quantity: 1,
        submitting: false,
        error: '',

        init() {
            if (this.hasVariants && this.variants.length === 1) {
                this.size = this.variants[0].size;
                this.color = this.variants[0].color;
            }
        },

        get sizes() {
            return [...new Set(this.variants.map((variant) => variant.size).filter(Boolean))];
        },

        get colors() {
            const seen = new Map();
            this.variants.forEach((variant) => {
                if (variant.color && !seen.has(variant.color)) {
                    seen.set(variant.color, variant.colorHex);
                }
            });
            return [...seen.entries()].map(([name, hex]) => ({ name, hex }));
        },

        get selectedVariant() {
            if (!this.hasVariants) return null;
            return (
                this.variants.find(
                    (variant) =>
                        (this.size === null || variant.size === this.size) &&
                        (this.color === null || variant.color === this.color),
                ) ?? null
            );
        },

        get displayPrice() {
            if (this.selectedVariant?.price !== null && this.selectedVariant?.price !== undefined) {
                return this.selectedVariant.priceFormatted;
            }
            return config.basePriceFormatted ?? '';
        },

        get availableStock() {
            return this.hasVariants ? Number(this.selectedVariant?.stock ?? 0) : this.simpleStock;
        },

        get maxQuantity() {
            const stock = this.availableStock;
            return stock > 0 ? Math.min(stock, this.maxOrderQuantity) : 0;
        },

        get canOrder() {
            return this.whatsappConfigured && this.selectionComplete && this.availableStock >= this.quantity;
        },

        get stockText() {
            if (!this.selectionComplete) return '';

            const stock = this.availableStock;

            if (stock <= 0) return this.messages.stockOut ?? '';

            const template = stock <= this.lowStockThreshold ? this.messages.stockLow : this.messages.stockAvailable;

            return (template ?? '').replace('__COUNT__', String(stock));
        },

        get maxHintText() {
            return (this.messages.maxHint ?? '').replace('__MAX__', String(this.maxQuantity));
        },

        sizeLabel(size) {
            return (this.messages.sizeAria ?? '').replace('__SIZE__', size ?? '');
        },

        colorLabel(name) {
            return (this.messages.colorAria ?? '').replace('__NAME__', name ?? '');
        },

        get selectionComplete() {
            if (!this.hasVariants) return true;
            if (this.sizes.length && !this.size) return false;
            if (this.colors.length && !this.color) return false;
            return Boolean(this.selectedVariant);
        },

        sizeAvailable(size) {
            return this.variants.some(
                (variant) =>
                    variant.size === size &&
                    variant.stock > 0 &&
                    (this.color === null || variant.color === this.color),
            );
        },

        colorAvailable(color) {
            return this.variants.some(
                (variant) =>
                    variant.color === color &&
                    variant.stock > 0 &&
                    (this.size === null || variant.size === this.size),
            );
        },

        chooseSize(size) {
            if (!this.sizeAvailable(size)) return;
            this.size = size;
            if (this.color && !this.variantFor(size, this.color)) {
                this.color = null;
            }
            this.quantity = 1;
            this.error = '';
        },

        chooseColor(color) {
            if (!this.colorAvailable(color)) return;
            this.color = color;
            if (this.size && !this.variantFor(this.size, color)) {
                this.size = null;
            }
            this.quantity = 1;
            this.error = '';
        },

        variantFor(size, color) {
            return (
                this.variants.find(
                    (variant) => (size === null || variant.size === size) && (color === null || variant.color === color),
                ) ?? null
            );
        },

        increment() {
            if (this.quantity < this.maxQuantity) this.quantity += 1;
        },

        decrement() {
            if (this.quantity > 1) this.quantity -= 1;
        },

        async submit(event) {
            this.error = '';

            if (!this.selectionComplete) {
                this.error = this.messages.selectOptions ?? this.messages.generic ?? '';
                return;
            }

            if (!this.canOrder) {
                this.error = this.messages.unavailable ?? this.messages.generic ?? '';
                return;
            }

            this.submitting = true;

            let whatsappWindow = null;
            try {
                whatsappWindow = window.open('about:blank', '_blank');
            } catch {
                whatsappWindow = null;
            }

            try {
                const response = await fetch(this.orderUrl, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                    },
                    body: JSON.stringify({
                        variant_id: this.selectedVariant?.id ?? null,
                        quantity: this.quantity,
                    }),
                });

                if (!response.ok) {
                    const payload = await response.json().catch(() => ({}));
                    throw new Error(
                        payload.errors
                            ? Object.values(payload.errors).flat()[0]
                            : (payload.message ?? this.messages.generic ?? ''),
                    );
                }

                const { url } = await response.json();

                if (!url) {
                    throw new Error(this.messages.generic ?? '');
                }

                if (whatsappWindow) {
                    whatsappWindow.location.href = url;
                } else {
                    window.location.href = url;
                }
            } catch (error) {
                if (whatsappWindow) whatsappWindow.close();
                this.error = error.message;
            } finally {
                this.submitting = false;
            }
        },

        nativeSubmit(event) {
            if (!this.canOrder) {
                event.preventDefault();
                this.error = this.selectionComplete
                    ? (this.messages.unavailable ?? this.messages.generic ?? '')
                    : (this.messages.selectOptions ?? this.messages.generic ?? '');
            }
        },
    };
}
