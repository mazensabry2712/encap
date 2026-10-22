(() => {
    'use strict';

    const root = document.documentElement;
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const revealTargets = document.querySelectorAll(
        '.editorial-heading, .editorial-summary, .proof-item, .section-head, .category, ' +
        '.approach-item, .dark-feature, .cta, .page-intro-grid, .feature-panel, ' +
        '.product-gallery-main, .product-detail-copy, .form-card, .contact-details, .gallery-grid figure'
    );

    if (!reduceMotion && 'IntersectionObserver' in window && revealTargets.length) {
        revealTargets.forEach((element, index) => {
            element.classList.add('reveal-on-scroll');
            if (element.classList.contains('category') || element.classList.contains('approach-item')) {
                element.style.setProperty('--reveal-delay', String((index % 4) * 65) + 'ms');
            }
        });

        root.classList.add('has-reveal');
        const observer = new IntersectionObserver((entries, currentObserver) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    currentObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -35px 0px' });

        revealTargets.forEach((element) => observer.observe(element));
    }

    const header = document.querySelector('.site-header');
    if (header) {
        let scheduled = false;
        const updateHeader = () => {
            header.classList.toggle('is-scrolled', window.scrollY > 12);
            scheduled = false;
        };
        window.addEventListener('scroll', () => {
            if (!scheduled) {
                scheduled = true;
                window.requestAnimationFrame(updateHeader);
            }
        }, { passive: true });
        updateHeader();
    }

    document.querySelectorAll('.mobile-nav-panel a').forEach((link) => {
        link.addEventListener('click', () => {
            const menu = link.closest('details');
            if (menu) menu.open = false;
        });
    });

    const gallery = document.querySelector('.product-gallery-main');
    const galleryButtons = document.querySelectorAll('[data-gallery-thumb]');
    if (gallery && galleryButtons.length) {
        galleryButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const src = button.dataset.imageSrc;
                if (!src) return;

                let image = gallery.querySelector('img');
                if (!image) {
                    image = document.createElement('img');
                    image.width = 900;
                    image.height = 760;
                    image.loading = 'eager';
                    image.decoding = 'async';
                    gallery.replaceChildren(image);
                }

                image.src = src;
                image.alt = button.dataset.imageAlt || '';
                image.removeAttribute('role');
                galleryButtons.forEach((item) => item.setAttribute('aria-pressed', item === button ? 'true' : 'false'));
            });
        });
    }
})();