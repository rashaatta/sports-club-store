export default function catalogFilters() {
    return {
        filtersOpen: false,
        lastFocused: null,

        init() {
            this.$watch('filtersOpen', (open) => {
                document.body.style.overflow = open ? 'hidden' : '';

                if (open) {
                    this.$nextTick(() => {
                        this.$refs.filterPanel
                            ?.querySelector('button, a[href], input, select')
                            ?.focus();
                    });
                }
            });
        },

        open() {
            this.lastFocused = document.activeElement;
            this.filtersOpen = true;
        },

        close() {
            if (! this.filtersOpen) {
                return;
            }

            this.filtersOpen = false;
            this.$nextTick(() => this.lastFocused?.focus?.());
        },

        trapFocus(event) {
            const focusables = Array.from(
                event.currentTarget.querySelectorAll(
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
