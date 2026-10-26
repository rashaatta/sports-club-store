export default function mediaPreview() {
    return {
        previews: [],
        dragging: false,

        init() {
            this.$watch('previews', () => {});
        },

        onChange(event) {
            this.clearUrls();
            this.previews = [...(event.target.files ?? [])].map((file) => ({
                name: file.name,
                size: file.size,
                url: URL.createObjectURL(file),
            }));
        },

        onDrop(event) {
            this.dragging = false;
            const input = this.$refs.input;
            if (!input || !event.dataTransfer?.files?.length) return;
            input.files = event.dataTransfer.files;
            this.onChange({ target: input });
        },

        remove(index) {
            const input = this.$refs.input;
            if (!input) return;
            const transfer = new DataTransfer();
            [...input.files].forEach((file, fileIndex) => {
                if (fileIndex !== index) transfer.items.add(file);
            });
            input.files = transfer.files;
            this.onChange({ target: input });
        },

        clearUrls() {
            this.previews.forEach((preview) => URL.revokeObjectURL(preview.url));
        },

        formatSize(bytes) {
            if (bytes < 1024 * 1024) return `${Math.ceil(bytes / 1024)} KB`;
            return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
        },
    };
}
