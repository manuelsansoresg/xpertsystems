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
    next() { if (this.projects.length) this.active = (this.active + 1) % this.projects.length; },
    previous() { if (this.projects.length) this.active = (this.active - 1 + this.projects.length) % this.projects.length; },
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

    // Problem section animations
    const problemSection = document.querySelector('.problem');
    if (problemSection) {
        const kicker = problemSection.querySelector('.section-kicker');
        const headline = problemSection.querySelector('.problem__headline');
        const lead = problemSection.querySelector('.problem__lead');
        const signal = problemSection.querySelector('.problem__signal');
        const ring = problemSection.querySelector('.signal__ring');

        const tl = gsap.timeline({
            scrollTrigger: { trigger: problemSection, start: 'top 78%' },
        });

        if (kicker) {
            tl.from(kicker, { opacity: 0, y: 20, duration: .6, ease: 'power3.out' }, 0);
        }
        if (headline) {
            tl.from(headline, { clipPath: 'inset(0 0 100% 0)', y: 40, duration: 1, ease: 'power4.out' }, .1);
        }
        if (lead) {
            tl.from(lead, { opacity: 0, y: 24, duration: .8, ease: 'power3.out' }, .3);
        }
        if (ring) {
            tl.from(ring, { scale: .85, opacity: 0, duration: .9, ease: 'power3.out' }, .35);
        }
        if (signal) {
            tl.from(signal.querySelector('p'), { opacity: 0, y: 16, duration: .7, ease: 'power3.out' }, .5);
        }

        // Subtle parallax between headline and signal
        if (headline && signal) {
            gsap.to(headline, {
                yPercent: -4, ease: 'none',
                scrollTrigger: { trigger: problemSection, start: 'top bottom', end: 'bottom top', scrub: 1 },
            });
            gsap.to(signal, {
                yPercent: 3, ease: 'none',
                scrollTrigger: { trigger: problemSection, start: 'top bottom', end: 'bottom top', scrub: 1 },
            });
        }
    }

    document.querySelectorAll('[data-count]').forEach((number) => {
        const target = Number(number.dataset.count);
        const state = { value: 0 };
        gsap.to(state, {
            value: target, duration: 1.4, ease: 'power2.out',
            onUpdate: () => { number.textContent = Math.round(state.value); },
            scrollTrigger: { trigger: number, start: 'top 85%', once: true },
        });
    });

    // Pricing section animations - Enhanced
    const pricingSection = document.querySelector('.pricing');
    if (pricingSection) {
        const pricingHeader = pricingSection.querySelector('.pricing__header');
        const pricingCards = pricingSection.querySelectorAll('.pkg');
        const pricingGuide = pricingSection.querySelector('.pricing__guide');

        // Set initial state for cards
        gsap.set(pricingCards, { opacity: 0, y: 45 });

        const pricingTl = gsap.timeline({
            scrollTrigger: {
                trigger: pricingSection,
                start: 'top 75%',
                toggleActions: 'play none none none',
            },
        });

        // Animate header elements
        if (pricingHeader) {
            const kicker = pricingHeader.querySelector('.section-kicker');
            const heading = pricingHeader.querySelector('h2');
            const description = pricingHeader.querySelector('p');

            if (kicker) {
                gsap.set(kicker, { opacity: 0, y: 20 });
                pricingTl.to(kicker, { opacity: 1, y: 0, duration: 0.6, ease: 'power3.out' }, 0);
            }

            if (heading) {
                gsap.set(heading, { opacity: 0, y: 30 });
                pricingTl.to(heading, { opacity: 1, y: 0, duration: 0.8, ease: 'power3.out' }, 0.1);
            }

            if (description) {
                gsap.set(description, { opacity: 0, y: 20 });
                pricingTl.to(description, { opacity: 1, y: 0, duration: 0.7, ease: 'power3.out' }, 0.2);
            }
        }

        // Animate cards with stagger
        if (pricingCards.length > 0) {
            pricingCards.forEach((card, index) => {
                const isFeatured = card.classList.contains('pkg--featured');
                const delay = 0.3 + (index * 0.15);

                pricingTl.to(card, {
                    opacity: 1,
                    y: 0,
                    scale: isFeatured ? 1 : undefined,
                    duration: 0.8,
                    ease: 'power3.out',
                }, delay);

                // Featured card starts slightly smaller
                if (isFeatured) {
                    gsap.set(card, { scale: 0.97 });
                }
            });
        }

        // Animate guide section
        if (pricingGuide) {
            const guideText = pricingGuide.querySelector('.pricing__guide-text');
            const guideAction = pricingGuide.querySelector('.pricing__guide-action');

            if (guideText) {
                gsap.set(guideText, { opacity: 0, y: 20 });
                pricingTl.to(guideText, { opacity: 1, y: 0, duration: 0.7, ease: 'power3.out' }, 0.8);
            }

            if (guideAction) {
                gsap.set(guideAction, { opacity: 0, y: 20 });
                pricingTl.to(guideAction, { opacity: 1, y: 0, duration: 0.7, ease: 'power3.out' }, 0.95);
            }
        }
    }

    // Process section animations
    const processSection = document.querySelector('.process');
    if (processSection) {
        const processIntro = processSection.querySelector('.process__intro');
        const processSteps = processSection.querySelectorAll('.process-step');
        const progressLine = processSection.querySelector('.process__progress-line');

        // Timeline for intro
        if (processIntro) {
            const kicker = processIntro.querySelector('.section-kicker');
            const heading = processIntro.querySelector('h2');
            const description = processIntro.querySelector('p');

            const processTl = gsap.timeline({
                scrollTrigger: { trigger: processSection, start: 'top 75%' },
            });

            if (kicker) {
                gsap.set(kicker, { opacity: 0, y: 20 });
                processTl.to(kicker, { opacity: 1, y: 0, duration: 0.6, ease: 'power3.out' }, 0);
            }

            if (heading) {
                gsap.set(heading, { opacity: 0, y: 30 });
                processTl.to(heading, { opacity: 1, y: 0, duration: 0.8, ease: 'power3.out' }, 0.1);
            }

            if (description) {
                gsap.set(description, { opacity: 0, y: 20 });
                processTl.to(description, { opacity: 1, y: 0, duration: 0.7, ease: 'power3.out' }, 0.2);
            }

            // Animate progress line
            if (progressLine) {
                gsap.set(progressLine, { '--progress': '0%' });
                processTl.to(progressLine, {
                    '--progress': '100%',
                    duration: 1.2,
                    ease: 'power2.out',
                }, 0.3);
            }
        }

        // Animate steps with stagger
        if (processSteps.length > 0) {
            processSteps.forEach((step, index) => {
                gsap.set(step, { opacity: 0, y: 30 });
                gsap.to(step, {
                    opacity: 1,
                    y: 0,
                    duration: 0.7,
                    ease: 'power3.out',
                    delay: 0.4 + (index * 0.1),
                    scrollTrigger: {
                        trigger: step,
                        start: 'top 80%',
                        toggleActions: 'play none none none',
                    },
                });
            });
        }

        // Scroll-based progress line
        if (progressLine) {
            gsap.to(progressLine, {
                '--progress': '100%',
                ease: 'none',
                scrollTrigger: {
                    trigger: processSection,
                    start: 'top 60%',
                    end: 'bottom 40%',
                    scrub: 1,
                },
            });
        }

        // Active state on scroll
        if (processSteps.length > 0) {
            processSteps.forEach((step) => {
                ScrollTrigger.create({
                    trigger: step,
                    start: 'top 60%',
                    end: 'bottom 40%',
                    onEnter: () => step.classList.add('is-active'),
                    onLeave: () => step.classList.remove('is-active'),
                    onEnterBack: () => step.classList.add('is-active'),
                    onLeaveBack: () => step.classList.remove('is-active'),
                });
            });
        }
    }

    // FAQ section animations
    const faqSection = document.querySelector('.faq');
    if (faqSection) {
        const faqIntro = faqSection.querySelector('.faq__intro');
        const faqItems = faqSection.querySelectorAll('.faq-item');
        const faqCta = faqSection.querySelector('.faq__cta');

        const faqTl = gsap.timeline({
            scrollTrigger: { trigger: faqSection, start: 'top 75%' },
        });

        if (faqIntro) {
            const kicker = faqIntro.querySelector('.section-kicker');
            const heading = faqIntro.querySelector('h2');
            const description = faqIntro.querySelector('p');

            if (kicker) {
                gsap.set(kicker, { opacity: 0, y: 20 });
                faqTl.to(kicker, { opacity: 1, y: 0, duration: 0.6, ease: 'power3.out' }, 0);
            }

            if (heading) {
                gsap.set(heading, { opacity: 0, y: 30 });
                faqTl.to(heading, { opacity: 1, y: 0, duration: 0.8, ease: 'power3.out' }, 0.1);
            }

            if (description) {
                gsap.set(description, { opacity: 0, y: 20 });
                faqTl.to(description, { opacity: 1, y: 0, duration: 0.7, ease: 'power3.out' }, 0.2);
            }
        }

        if (faqItems.length > 0) {
            faqItems.forEach((item, index) => {
                gsap.set(item, { opacity: 0, y: 20 });
                faqTl.to(item, {
                    opacity: 1,
                    y: 0,
                    duration: 0.6,
                    ease: 'power3.out',
                }, 0.3 + (index * 0.08));
            });
        }

        if (faqCta) {
            gsap.set(faqCta, { opacity: 0, y: 20 });
            faqTl.to(faqCta, { opacity: 1, y: 0, duration: 0.7, ease: 'power3.out' }, 0.9);
        }
    }

    gsap.from('.final-cta h2 span,.final-cta h2 em', {
        yPercent: 100, opacity: 0, stagger: .1, duration: 1.1, ease: 'power4.out',
        scrollTrigger: { trigger: '.final-cta h2', start: 'top 82%' },
    });
    gsap.to('.final-cta__orb', {
        rotate: 24, scale: 1.14, ease: 'none',
        scrollTrigger: { trigger: '.final-cta', start: 'top bottom', end: 'bottom top', scrub: 1 },
    });

}
