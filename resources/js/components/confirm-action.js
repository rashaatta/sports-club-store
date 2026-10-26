export default function confirmAction() {
    return {
        open: false,
        confirmed: false,
        lastFocused: null,

        request() {
            if (this.confirmed) return true;
            this.lastFocused = document.activeElement;
            this.open = true;
            this.$nextTick(() => this.$refs.dialog?.focus());
            return false;
        },

        cancel() {
            this.open = false;
            this.$nextTick(() => this.lastFocused?.focus?.());
        },

        proceed() {
            this.open = false;
            this.confirmed = true;
            this.$refs.form?.submit();
        },
    };
}
