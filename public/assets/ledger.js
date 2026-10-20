(() => {
    const group = document.querySelector('.density');
    if (group) {
        group.hidden = false;
        const apply = value => {
            document.documentElement.dataset.rowDensity = value;
            group.querySelectorAll('button').forEach(button => button.setAttribute('aria-pressed', String(button.dataset.density === value)));
        };
        let stored;
        try { stored = localStorage.getItem('ledger-density'); } catch (_) { /* Preference is optional. */ }
        apply(stored === 'compact' ? 'compact' : 'comfortable');
        group.addEventListener('click', event => {
            const button = event.target.closest('[data-density]');
            if (!button) return;
            apply(button.dataset.density);
            try { localStorage.setItem('ledger-density', button.dataset.density); } catch (_) { /* In-memory setting still works. */ }
        });
    }
    const status = document.getElementById('copy-status');
    const toast = document.getElementById('copy-toast');
    let timeout;
    document.querySelectorAll('[data-copy]').forEach(button => {
        button.hidden = false;
        button.addEventListener('click', async () => {
            if (button.disabled) return;
            button.disabled = true;
            let message;
            try {
                if (!navigator.clipboard?.writeText) throw new Error('Clipboard unavailable');
                await navigator.clipboard.writeText(button.dataset.copy);
                message = 'Copied to clipboard.';
                button.dataset.copied = 'true';
                button.textContent = 'Copied';
                setTimeout(() => {
                    delete button.dataset.copied;
                    button.textContent = 'Copy';
                }, 1800);
            } catch (_) {
                message = 'Couldn’t copy. Select the value beside this button and copy it manually.';
            } finally {
                button.disabled = false;
            }
            clearTimeout(timeout);
            status.textContent = '';
            requestAnimationFrame(() => { status.textContent = message; });
            toast.textContent = message;
            toast.hidden = false;
            timeout = setTimeout(() => { toast.hidden = true; }, 6000);
        });
    });
})();
