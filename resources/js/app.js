// Give every present and future same-origin website link Livewire SPA navigation.
document.addEventListener('click', (event) => {
    if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
        return;
    }

    const link = event.target.closest('a[href]');

    if (! link || link.hasAttribute('download') || link.target === '_blank' || link.hasAttribute('wire:navigate')) {
        return;
    }

    const href = link.getAttribute('href');

    if (! href || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:')) {
        return;
    }

    const destination = new URL(link.href, window.location.href);

    if (destination.origin !== window.location.origin) {
        return;
    }

    event.preventDefault();
    window.Livewire.navigate(destination.href);
});
