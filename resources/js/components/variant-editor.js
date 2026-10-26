export default function variantEditor({ variants = [], hasVariants = false, productSku = '' } = {}) {
    return {
        variants: variants.map((variant) => ({ ...variant })),
        hasVariants,

        init() {
            if (this.hasVariants && this.variants.length === 0) {
                this.add();
            }
        },

        add() {
            this.variants.push({
                size: '',
                color: '',
                color_hex: '#111111',
                price_override: '',
                stock_quantity: 0,
                sku: '',
                is_active: true,
            });
        },

        remove(index) {
            this.variants.splice(index, 1);
        },

        toggle() {
            this.hasVariants = !this.hasVariants;
            if (this.hasVariants && this.variants.length === 0) {
                this.add();
            }
        },

        suggestedSku(index) {
            const variant = this.variants[index];
            const parts = [productSku, variant.size, variant.color]
                .filter(Boolean)
                .map((part) =>
                    String(part)
                        .trim()
                        .replace(/\s+/g, '-')
                        .replace(/[^\p{L}\p{N}-]/gu, ''),
                );
            return parts.join('-');
        },
    };
}
