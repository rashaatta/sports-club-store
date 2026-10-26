export default function headerOverlay(storeKey, initialFocusSelector = null) {
    return {
        lastFocused: null,

        get isOpen() {
            return this.$store.ui[storeKey];
        },

        init() {
            this.$watch(() => this.isOpen, (open) => {
                if (open) {
                    this.lastFocused = document.activeElement;
                    this.$nextTick(() => {
                        const target = initialFocusSelector
                            ? this.$el.querySelector(initialFocusSelector)
                            : this.$el.querySelector(
                                'a[href], button:not([disabled]), input:not([disabled]), [tabindex]:not([tabindex="-1"])',
                            );

                        target?.focus();
                    });
                } else if (this.lastFocused) {
                    const trigger = this.lastFocused;
                    this.lastFocused = null;
                    this.$nextTick(() => trigger?.focus?.());
                }
            });
        },

        close() {
            if (! this.isOpen) {
                return;
            }

            this.$store.ui[storeKey] = false;
        },

        trapFocus(event) {
            const focusables = Array.from(
                this.$el.querySelectorAll(
                    'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])',
                ),
            ).filter((element) => element.offsetParent !== null);

            if (focusables.length === 0) {
                return;
            }

            const first = focusables[0];
            const last = focusables[focusables.length - 1];

            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (! event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        },
    };
}
