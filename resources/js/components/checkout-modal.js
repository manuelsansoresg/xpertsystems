export default (packages = {}, initialSlug = null) => ({
    visible: false,
    action: '',
    packageSlug: '',
    packageName: '',
    packagePrice: '',
    init() {
        if (initialSlug && packages[initialSlug]) this.openCheckout(initialSlug);
    },
    openCheckout(slug) {
        const selected = packages[slug];
        if (!selected) return;
        this.action = selected.action;
        this.packageSlug = slug;
        this.packageName = selected.name;
        this.packagePrice = selected.price;
        this.visible = true;
        document.body.classList.add('menu-locked');
    },
    close() {
        this.visible = false;
        document.body.classList.remove('menu-locked');

        const url = new URL(window.location.href);
        if (url.searchParams.has('checkout')) {
            url.searchParams.delete('checkout');
            if (url.hash === '#paquetes') url.hash = '';
            window.history.replaceState({}, '', url);
        }
    },
});
