export default function copyLink({ text = '', copiedLabel = '', defaultLabel = '' } = {}) {
    return {
        text,
        copiedLabel,
        defaultLabel,
        copied: false,

        async copy() {
            let copied = false;

            if (navigator.clipboard && window.isSecureContext) {
                try {
                    await navigator.clipboard.writeText(this.text);
                    copied = true;
                } catch {
                    copied = false;
                }
            }

            if (!copied) {
                const input = document.createElement('textarea');
                input.value = this.text;
                input.setAttribute('readonly', '');
                input.style.position = 'fixed';
                input.style.opacity = '0';
                document.body.appendChild(input);
                input.select();

                try {
                    copied = document.execCommand('copy');
                } catch {
                    copied = false;
                }

                document.body.removeChild(input);
            }

            if (!copied) return;

            this.copied = true;
            window.setTimeout(() => {
                this.copied = false;
            }, 2000);
        },
    };
}
