export default function productModel({ src = '', alt = '', poster = null } = {}) {
    return {
        src,
        alt,
        poster,
        open: false,
        ready: false,
        failed: false,
        lastFocused: null,

        init() {
            this.$watch('open', (open) => {
                document.body.style.overflow = open ? 'hidden' : '';

                if (open) {
                    this.$nextTick(() => this.$refs.closeButton?.focus());
                }
            });
        },

        async show() {
            this.lastFocused = document.activeElement;
            this.open = true;

            if (this.ready || this.failed) {
                return;
            }

            try {
                await import('@google/model-viewer');
                this.ready = true;
            } catch (error) {
                this.failed = true;
            }
        },

        hide() {
            if (! this.open) {
                return;
            }

            this.open = false;
            this.$nextTick(() => this.lastFocused?.focus?.());
        },

        trapFocus(event) {
            const focusables = Array.from(
                event.currentTarget.querySelectorAll(
                    'a[href], button:not([disabled]), input:not([disabled]), [tabindex]:not([tabindex="-1"])',
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
