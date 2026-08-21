const header = document.querySelector('[data-site-header]');
const menuButton = document.querySelector('[data-menu-toggle]');
const mobileMenu = document.querySelector('[data-mobile-menu]');

const closeMenu = () => {
    if (!menuButton || !mobileMenu) return;
    menuButton.setAttribute('aria-expanded', 'false');
    mobileMenu.hidden = true;
    document.body.classList.remove('menu-locked');
};

menuButton?.addEventListener('click', () => {
    const open = menuButton.getAttribute('aria-expanded') !== 'true';
    menuButton.setAttribute('aria-expanded', String(open));
    if (mobileMenu) mobileMenu.hidden = !open;
    document.body.classList.toggle('menu-locked', open);
});

mobileMenu?.querySelectorAll('a').forEach((link) => link.addEventListener('click', closeMenu));

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') closeMenu();
});

const updateHeader = () => header?.classList.toggle('is-scrolled', window.scrollY > 32);
updateHeader();
window.addEventListener('scroll', updateHeader, { passive: true });

const trackEvent = (name, parameters = {}) => {
    if (typeof window.gtag === 'function') window.gtag('event', name, parameters);
    if (typeof window.fbq === 'function') {
        const metaNames = { purchase: 'Purchase', begin_checkout: 'InitiateCheckout' };
        window.fbq('trackCustom', metaNames[name] || name, parameters);
    }
};

document.querySelectorAll('[data-analytics]').forEach((element) => {
    element.addEventListener('click', () => trackEvent(element.dataset.analytics, {
        package: element.dataset.package || undefined,
    }));
});
