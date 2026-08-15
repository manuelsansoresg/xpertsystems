import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

Alpine.plugin(collapse);

Alpine.data('siteShell', () => ({
    menuOpen: false,
    scrolled: false,
    initHeader() {
        const update = () => { this.scrolled = window.scrollY > 32; };
        update();
        window.addEventListener('scroll', update, { passive: true });
        this.$watch('menuOpen', value => document.body.classList.toggle('menu-locked', value));
    },
}));

Alpine.data('portfolioShowcase', (projects) => ({
    projects,
    active: 0,
    get current() { return this.projects[this.active] || {}; },
    next() { this.active = (this.active + 1) % this.projects.length; },
    previous() { this.active = (this.active - 1 + this.projects.length) % this.projects.length; },
}));

Alpine.data('quoteModal', () => ({
    visible: false,
    packageId: null,
    open(id) { this.packageId = id; this.visible = true; document.body.classList.add('menu-locked'); },
    close() { this.visible = false; document.body.classList.remove('menu-locked'); },
}));

window.Alpine = Alpine;
Alpine.start();

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

if (document.querySelector('.pricing')) {
    ScrollTrigger.create({
        trigger: '.pricing', start: 'top 80%', once: true,
        onEnter: () => trackEvent('view_packages'),
    });
}

const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

if (!reduceMotion) {
    document.querySelectorAll('.split-title').forEach((title) => {
        gsap.from(title, {
            clipPath: 'inset(0 0 100% 0)', y: 60, duration: 1.1, ease: 'power4.out',
            scrollTrigger: { trigger: title, start: 'top 82%' },
        });
    });

    gsap.from('.problem__list li', {
        xPercent: 12, opacity: 0, stagger: .12, duration: .9, ease: 'power3.out',
        scrollTrigger: { trigger: '.problem__list', start: 'top 78%' },
    });

    gsap.from('.problem blockquote', {
        backgroundSize: '0% 100%', opacity: .15, duration: 1.3,
        scrollTrigger: { trigger: '.problem blockquote', start: 'top 82%', end: 'center 55%', scrub: 1 },
    });

    document.querySelectorAll('[data-count]').forEach((number) => {
        const target = Number(number.dataset.count);
        const state = { value: 0 };
        gsap.to(state, {
            value: target, duration: 1.4, ease: 'power2.out',
            onUpdate: () => { number.textContent = Math.round(state.value); },
            scrollTrigger: { trigger: number, start: 'top 85%', once: true },
        });
    });

    gsap.from('.solution__visual', {
        clipPath: 'inset(12% 12% 12% 12%)', scale: .93, duration: 1.35, ease: 'power3.out',
        scrollTrigger: { trigger: '.solution', start: 'top 72%' },
    });
    gsap.to('.solution__window', {
        xPercent: -7, yPercent: -8, ease: 'none',
        scrollTrigger: { trigger: '.solution', start: 'top bottom', end: 'bottom top', scrub: 1 },
    });

    gsap.from('.portfolio__intro h2', {
        xPercent: -10, opacity: 0, duration: 1.2, ease: 'power4.out',
        scrollTrigger: { trigger: '.portfolio__intro', start: 'top 78%' },
    });
    gsap.from('.project-canvas', {
        clipPath: 'inset(0 100% 0 0)', duration: 1.25, ease: 'power4.inOut',
        scrollTrigger: { trigger: '.portfolio__showcase', start: 'top 72%' },
    });

    gsap.from('.package-reveal', {
        y: 100, rotateX: 8, opacity: 0, transformOrigin: '50% 100%', duration: 1.1,
        stagger: .15, ease: 'power3.out',
        scrollTrigger: { trigger: '.pricing__composition', start: 'top 78%' },
    });

    gsap.to('.process__timeline', {
        '--timeline-progress': '100%', ease: 'none',
        scrollTrigger: { trigger: '.process__timeline', start: 'top 65%', end: 'bottom 65%', scrub: true },
    });
    gsap.from('.process-step > div:not(.process-step__number)', {
        x: 45, opacity: 0, stagger: .15, duration: .8, ease: 'power3.out',
        scrollTrigger: { trigger: '.process__timeline', start: 'top 75%' },
    });

    gsap.from('.faq-item', {
        x: 45, opacity: 0, stagger: .05, duration: .6,
        scrollTrigger: { trigger: '.faq__list', start: 'top 80%' },
    });

    gsap.from('.final-cta h2 span,.final-cta h2 em', {
        yPercent: 100, opacity: 0, stagger: .1, duration: 1.1, ease: 'power4.out',
        scrollTrigger: { trigger: '.final-cta h2', start: 'top 82%' },
    });
    gsap.to('.final-cta__orb', {
        rotate: 24, scale: 1.14, ease: 'none',
        scrollTrigger: { trigger: '.final-cta', start: 'top bottom', end: 'bottom top', scrub: 1 },
    });

}
