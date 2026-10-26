export default function productGallery({ images = [] } = {}) {
    return {
        images,
        active: 0,
        zoom: false,
        touchStartX: null,
        lastFocused: null,

        init() {
            this.$watch('zoom', (open) => {
                document.body.style.overflow = open ? 'hidden' : '';
                if (open) {
                    this.$nextTick(() => this.$refs.zoomClose?.focus());
                }
            });
        },

        get current() {
            return this.images[this.active] ?? { src: '', alt: '' };
        },

        select(index) {
            if (index >= 0 && index < this.images.length) {
                this.active = index;
            }
        },

        next() {
            this.active = (this.active + 1) % this.images.length;
        },

        previous() {
            this.active = (this.active - 1 + this.images.length) % this.images.length;
        },

        openZoom() {
            this.lastFocused = document.activeElement;
            this.zoom = true;
        },

        closeZoom() {
            if (! this.zoom) {
                return;
            }

            this.zoom = false;
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

        onTouchStart(event) {
            this.touchStartX = event.changedTouches[0]?.clientX ?? null;
        },

        onTouchEnd(event) {
            if (this.touchStartX === null) return;
            const delta = (event.changedTouches[0]?.clientX ?? 0) - this.touchStartX;
            if (Math.abs(delta) > 45) {
                delta > 0 ? this.previous() : this.next();
            }
            this.touchStartX = null;
        },
    };
}
