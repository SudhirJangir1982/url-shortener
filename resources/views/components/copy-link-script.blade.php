@once
    @push('scripts')
        <script>
            document.addEventListener('click', function (event) {
                const button = event.target.closest('.copy-invitation-link');
                if (!button) {
                    return;
                }

                const text = button.getAttribute('data-copy-url')
                    ?? document.getElementById(button.getAttribute('data-copy-target') ?? '')?.textContent?.trim()
                    ?? '';

                if (!text) {
                    return;
                }

                const copiedLabel = @json(__('Copied!'));
                const copyLabel = @json(__('Copy link'));

                const showCopied = () => {
                    button.textContent = copiedLabel;
                    button.classList.add('border-green-400', 'text-green-800');
                    window.setTimeout(() => {
                        button.textContent = copyLabel;
                        button.classList.remove('border-green-400', 'text-green-800');
                    }, 2000);
                };

                if (navigator.clipboard?.writeText) {
                    navigator.clipboard.writeText(text).then(showCopied).catch(() => {
                        window.prompt(@json(__('Copy this link:')), text);
                    });
                } else {
                    window.prompt(@json(__('Copy this link:')), text);
                    showCopied();
                }
            });
        </script>
    @endpush
@endonce
