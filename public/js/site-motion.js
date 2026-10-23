(() => {
    'use strict';

    const root = document.documentElement;
    const body = document.body;
    if (!body || !body.classList.contains('site-body')) {
        return;
    }

    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
    root.classList.add('site-motion-ready');

    const progressBar = document.createElement('div');
    progressBar.className = 'site-scroll-progress';
    progressBar.setAttribute('aria-hidden', 'true');
    body.prepend(progressBar);

    const revealSelector = [
        '.breadcrumbs', '.editorial-heading', '.editorial-summary', '.proof-item', '.section-head',
        '.category', '.approach-item', '.dark-feature', '.cta', '.page-intro-grid', '.feature-panel',
        '.product-gallery-main', '.product-gallery-thumbs', '.product-detail-copy', '.form-card',
        '.contact-details', '.gallery-grid figure', '.content-story-marker', '.encap-trust-item',
        '.encap-service-hub-heading', '.encap-service-hub-card', '.encap-story-art', '.encap-story-copy',
        '.encap-section-heading', '.encap-product-card', '.encap-process-heading', '.encap-process-row',
        '.encap-quality-content', '.encap-final-panel', '.encap-about-principles-heading', '.encap-principle-card',
        '.encap-service-visual-figure', '.encap-service-visual-copy', '.encap-service-detail-heading',
        '.encap-service-card', '.encap-service-workflow-heading', '.encap-service-steps > li',
        '.encap-service-cta', '.encap-service-related', '.encap-catalog-footer',
        '.encap-about-journey-heading', '.encap-about-journey-card', '.encap-about-practice-visual',
        '.encap-about-practice-copy', '.encap-about-catalog-heading', '.encap-about-category-card',
        '.encap-catalog-heading', '.encap-catalog-card', '.encap-catalog-cta',
        '.encap-about-final-panel', '.encap-contact-hero-copy', '.encap-contact-hero-visual',
        '.encap-contact-form-panel', '.encap-contact-details', '.encap-contact-next-heading',
        '.encap-contact-next-card', '.footer-top > div', '.footer-bottom'
    ].join(', ');

    // Only animate the outermost matching component; nested reveal wrappers made motion stack and obscure content.
    const revealTargets = Array.from(document.querySelectorAll(revealSelector))
        .filter((element) => {
            if (element.closest('[data-encap-hero]')) {
                return false;
            }

            return !element.parentElement?.closest(revealSelector);
        });

    if (!reducedMotion && 'IntersectionObserver' in window && revealTargets.length) {
        revealTargets.forEach((element) => {
            element.classList.add('site-motion-reveal');
            const parent = element.parentElement;
            const siblings = parent
                ? Array.from(parent.children).filter((child) => child.matches(revealSelector))
                : [];
            const siblingIndex = siblings.indexOf(element);
            element.style.setProperty('--site-motion-delay', (Math.max(0, siblingIndex) % 5 * 65) + 'ms');
        });

        root.classList.add('has-site-motion');
        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-in-view');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.08, rootMargin: '0px 0px -28px 0px' });

        revealTargets.forEach((element) => revealObserver.observe(element));
    }

    if (!reducedMotion && finePointer) {
        const tiltSelector = [
            '.category', '.encap-product-card', '.encap-service-hub-card', '.encap-service-card',
            '.encap-principle-card', '.encap-about-journey-card', '.encap-about-category-card', '.encap-catalog-card',
            '.encap-contact-channel', '.encap-contact-next-card',
            '.encap-process-row', '.encap-contact-details', '.encap-story-art',
            '.gallery-card', '.encap-service-visual-figure',
            '.product-gallery-main', '.form-card', '.contact-details', '.feature-panel',
            '.dark-feature', '.proof-item', '.approach-item', '.encap-trust-item'
        ].join(', ');

        document.querySelectorAll(tiltSelector).forEach((card) => {
            if (card.closest('[data-encap-hero]')) {
                return;
            }

            card.classList.add('site-motion-tilt');
            let frame = 0;

            card.addEventListener('pointermove', (event) => {
                if (frame) {
                    return;
                }

                frame = window.requestAnimationFrame(() => {
                    const rect = card.getBoundingClientRect();
                    if (rect.width > 0 && rect.height > 0) {
                        const x = Math.max(0, Math.min(1, (event.clientX - rect.left) / rect.width)) - 0.5;
                        const y = Math.max(0, Math.min(1, (event.clientY - rect.top) / rect.height)) - 0.5;
                        const tiltX = (y * -6.5).toFixed(2) + 'deg';
                        const tiltY = (x * 7.5).toFixed(2) + 'deg';

                        card.style.setProperty('--site-tilt-x', tiltX);
                        card.style.setProperty('--site-tilt-y', tiltY);
                        // Shared pointer coordinates drive the soft highlight; there is no second tilt listener.
                        card.style.setProperty('--tilt-x', tiltX);
                        card.style.setProperty('--tilt-y', tiltY);
                        card.style.setProperty('--pointer-x', ((x + 0.5) * 100).toFixed(1) + '%');
                        card.style.setProperty('--pointer-y', ((y + 0.5) * 100).toFixed(1) + '%');
                        card.style.setProperty('--site-lift', '-5px');
                    }

                    frame = 0;
                });
            }, { passive: true });

            card.addEventListener('pointerleave', () => {
                if (frame) {
                    window.cancelAnimationFrame(frame);
                }

                frame = 0;
                card.style.setProperty('--site-tilt-x', '0deg');
                card.style.setProperty('--site-tilt-y', '0deg');
                card.style.setProperty('--tilt-x', '0deg');
                card.style.setProperty('--tilt-y', '0deg');
                card.style.setProperty('--pointer-x', '50%');
                card.style.setProperty('--pointer-y', '50%');
                card.style.setProperty('--site-lift', '0px');
            });
        });

        const magneticSelector = '.primary-quote, .encap-button, .hero-actions .button, .cta .button, .form-submit .button';
        document.querySelectorAll(magneticSelector).forEach((button) => {
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

    const photoSelector = [
        '.category-image img', '.encap-product-image img', '.encap-service-card-media img',
        '.encap-service-visual-figure img', '.encap-story-art > img', '.encap-catalog-card-media img', '.encap-catalog-hero-image img', '.product-gallery-main img',
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
            const viewportMiddle = window.innerHeight / 2;
            activePhotos.forEach((photo) => {
                const rect = photo.getBoundingClientRect();
                const relative = Math.max(
                    -0.5,
                    Math.min(0.5, (viewportMiddle - (rect.top + rect.height / 2)) / Math.max(1, window.innerHeight))
                );
                photo.style.setProperty('--site-photo-offset', (relative * 16).toFixed(2) + 'px');
            });
        }

        scrollFrame = 0;
    };

    const requestScrollMotion = () => {
        if (scrollFrame) {
            return;
        }

        scrollFrame = window.requestAnimationFrame(updateScrollMotion);
    };

    if (!reducedMotion && 'IntersectionObserver' in window && photos.length) {
        const photoObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    activePhotos.add(entry.target);
                } else {
                    activePhotos.delete(entry.target);
                }
            });

            requestScrollMotion();
        }, { rootMargin: '100px 0px 100px 0px', threshold: 0 });

        photos.forEach((photo) => photoObserver.observe(photo));
    }

    window.addEventListener('scroll', requestScrollMotion, { passive: true });
    window.addEventListener('resize', requestScrollMotion, { passive: true });
    updateScrollMotion();
})();
