(() => {
    'use strict';

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

    document.querySelectorAll('.primary-mobile-panel a').forEach((link) => {
        link.addEventListener('click', () => {
            const menu = link.closest('details');
            if (menu) {
                menu.open = false;
            }
        });
    });

    const gallery = document.querySelector('.product-gallery-main');
    const galleryButtons = document.querySelectorAll('[data-gallery-thumb]');

    if (gallery && galleryButtons.length) {
        galleryButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const src = button.dataset.imageSrc;
                if (!src) {
                    return;
                }

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
                galleryButtons.forEach((item) => {
                    item.setAttribute('aria-pressed', item === button ? 'true' : 'false');
                });
            });
        });
    }
})();
