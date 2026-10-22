(() => {
    'use strict';

    const root = document.documentElement;
    const body = document.body;
    if (!body || !body.classList.contains('site-body')) return;

    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
    root.classList.add('site-motion-ready');

    // One light scroll-progress indicator shared by every public route.
    const progressBar = document.createElement('div');
    progressBar.className = 'site-scroll-progress';
    progressBar.setAttribute('aria-hidden', 'true');
    body.prepend(progressBar);

    const revealSelector = [
        '.breadcrumbs', '.encap-service-visual-figure', '.encap-service-visual-copy',
        '.encap-service-detail-heading', '.encap-service-card', '.encap-service-workflow-heading',
        '.encap-service-steps > li', '.encap-service-cta', '.encap-service-related',
        '.content-story-marker', '.product-gallery-thumbs', '.encap-catalog-footer',
        '.footer-top > div', '.footer-bottom', '.form-card form .field', '.form-card form .form-submit'
    ].join(', ');

    const revealTargets = Array.from(document.querySelectorAll(revealSelector))
        .filter((element) => !element.closest('[data-encap-hero]') && !element.classList.contains('reveal-on-scroll'));

    if (!reducedMotion && 'IntersectionObserver' in window && revealTargets.length) {
        revealTargets.forEach((element, index) => {
            element.classList.add('site-motion-reveal');
            const parent = element.parentElement;
            const siblings = parent ? Array.from(parent.children).filter((child) => child.matches(revealSelector)) : [];
            const siblingIndex = siblings.indexOf(element);
            const delay = (siblingIndex >= 0 ? siblingIndex % 5 : index % 4) * 65;
            element.style.setProperty('--site-motion-delay', delay + 'ms');
        });

        root.classList.add('has-site-motion');
        const observer = new IntersectionObserver((entries, currentObserver) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-in-view');
                    currentObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.08, rootMargin: '0px 0px -28px 0px' });

        revealTargets.filter((element) => element.classList.contains('site-motion-reveal'))
            .forEach((element) => observer.observe(element));
    } else {
        root.classList.add('has-site-motion');
    }

    if (!reducedMotion && finePointer) {
        const tiltSelector = [
            '.category', '.encap-product-card', '.encap-service-hub-card', '.encap-service-card',
            '.encap-principle-card', '.gallery-card', '.encap-final-panel', '.form-card',
            '.contact-details', '.product-gallery-main', '.encap-service-visual-figure',
            '.feature-panel', '.dark-feature', '.cta'
        ].join(', ');

        document.querySelectorAll(tiltSelector).forEach((card) => {
            if (card.closest('[data-encap-hero]')) return;
            card.classList.add('site-motion-tilt');
            let frame = 0;

            card.addEventListener('pointermove', (event) => {
                if (frame) return;
                frame = window.requestAnimationFrame(() => {
                    const rect = card.getBoundingClientRect();
                    if (rect.width && rect.height) {
                        const x = Math.max(0, Math.min(1, (event.clientX - rect.left) / rect.width)) - 0.5;
                        const y = Math.max(0, Math.min(1, (event.clientY - rect.top) / rect.height)) - 0.5;
                        card.style.setProperty('--site-tilt-x', (y * -6).toFixed(2) + 'deg');
                        card.style.setProperty('--site-tilt-y', (x * 7).toFixed(2) + 'deg');
                        card.style.setProperty('--site-lift', '-4px');
                    }
                    frame = 0;
                });
            }, { passive: true });

            card.addEventListener('pointerleave', () => {
                if (frame) window.cancelAnimationFrame(frame);
                frame = 0;
                card.style.setProperty('--site-tilt-x', '0deg');
                card.style.setProperty('--site-tilt-y', '0deg');
                card.style.setProperty('--site-lift', '0px');
            });
        });

        document.querySelectorAll('.primary-quote, .encap-button, .hero-actions .button, .cta .button, .form-submit .button')
            .forEach((button) => {
                button.classList.add('site-motion-magnetic');
                button.addEventListener('pointermove', (event) => {
                    const rect = button.getBoundingClientRect();
                    const x = event.clientX - rect.left - rect.width / 2;
                    const y = event.clientY - rect.top - rect.height / 2;
                    button.style.setProperty('--site-magnet-x', (x * 0.045).toFixed(2) + 'px');
                    button.style.setProperty('--site-magnet-y', (y * 0.045).toFixed(2) + 'px');
                }, { passive: true });
                button.addEventListener('pointerleave', () => {
                    button.style.setProperty('--site-magnet-x', '0px');
                    button.style.setProperty('--site-magnet-y', '0px');
                });
            });
    }

    // Give photos a small viewport-relative depth shift, limited to visible images.
    const photoSelector = [
        '.category-image img', '.encap-product-image img', '.encap-service-card-media img',
        '.encap-service-visual-figure img', '.encap-story-art > img', '.product-gallery-main img',
        '.gallery-card-link img'
    ].join(', ');
    const photos = Array.from(document.querySelectorAll(photoSelector));
    const activePhotos = new Set();

    photos.forEach((photo) => photo.classList.add('site-motion-photo'));

    let scrollFrame = 0;
    const updateScrollMotion = () => {
        const scrollable = Math.max(1, document.documentElement.scrollHeight - window.innerHeight);
        root.style.setProperty('--site-scroll-progress', Math.max(0, Math.min(1, window.scrollY / scrollable)).toFixed(4));

        if (!reducedMotion && activePhotos.size) {
            const middle = window.innerHeight / 2;
            activePhotos.forEach((photo) => {
                const rect = photo.getBoundingClientRect();
                const relative = Math.max(-0.5, Math.min(0.5, (middle - (rect.top + rect.height / 2)) / Math.max(1, window.innerHeight)));
                photo.style.setProperty('--site-photo-offset', (relative * 16).toFixed(2) + 'px');
            });
        }
        scrollFrame = 0;
    };
    const requestScrollMotion = () => {
        if (scrollFrame) return;
        scrollFrame = window.requestAnimationFrame(updateScrollMotion);
    };

    if (!reducedMotion && 'IntersectionObserver' in window && photos.length) {
        const photoObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) activePhotos.add(entry.target);
                else activePhotos.delete(entry.target);
            });
            requestScrollMotion();
        }, { rootMargin: '100px 0px 100px 0px', threshold: 0 });
        photos.forEach((photo) => photoObserver.observe(photo));
    }

    window.addEventListener('scroll', requestScrollMotion, { passive: true });
    window.addEventListener('resize', requestScrollMotion, { passive: true });
    updateScrollMotion();
})();