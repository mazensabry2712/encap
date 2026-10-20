(() => {
    'use strict';

    document.querySelectorAll('[data-language-switcher]').forEach((root) => {
        const trigger = root.querySelector('[data-language-trigger]');
        const menu = root.querySelector('[data-language-menu]');

        if (!trigger || !menu) {
            return;
        }

        const links = () => Array.from(menu.querySelectorAll('a[href]'));
        let pendingOpenFrame = 0;

        const open = (focusFirst = false) => {
            window.cancelAnimationFrame(pendingOpenFrame);
            menu.hidden = false;
            trigger.setAttribute('aria-expanded', 'true');
            pendingOpenFrame = window.requestAnimationFrame(() => {
                menu.classList.add('is-open');
            });

            if (focusFirst) {
                window.requestAnimationFrame(() => links()[0]?.focus());
            }
        };

        const close = (returnFocus = false) => {
            window.cancelAnimationFrame(pendingOpenFrame);
            menu.classList.remove('is-open');
            menu.hidden = true;
            trigger.setAttribute('aria-expanded', 'false');

            if (returnFocus) {
                trigger.focus();
            }
        };

        trigger.addEventListener('click', (event) => {
            const isOpen = trigger.getAttribute('aria-expanded') === 'true';

            if (isOpen) {
                close();
                return;
            }

            open(event.detail === 0);
        });

        document.addEventListener('pointerdown', (event) => {
            if (!root.contains(event.target)) {
                close();
            }
        });

        document.addEventListener('keydown', (event) => {
            const isOpen = trigger.getAttribute('aria-expanded') === 'true';

            if (event.key === 'Escape' && isOpen) {
                event.preventDefault();
                close(true);
            }
        });

        trigger.addEventListener('keydown', (event) => {
            if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                event.preventDefault();
                open(false);
                window.requestAnimationFrame(() => {
                    const items = links();
                    (event.key === 'ArrowUp' ? items[items.length - 1] : items[0])?.focus();
                });
            }
        });

        menu.addEventListener('keydown', (event) => {
            const items = links();
            const currentIndex = items.indexOf(document.activeElement);

            if (!items.length) {
                return;
            }

            let nextIndex = currentIndex;

            if (event.key === 'ArrowDown') {
                nextIndex = (currentIndex + 1) % items.length;
            } else if (event.key === 'ArrowUp') {
                nextIndex = (currentIndex - 1 + items.length) % items.length;
            } else if (event.key === 'Home') {
                nextIndex = 0;
            } else if (event.key === 'End') {
                nextIndex = items.length - 1;
            } else {
                return;
            }

            event.preventDefault();
            items[nextIndex]?.focus();
        });

        root.addEventListener('focusout', () => {
            window.requestAnimationFrame(() => {
                if (!root.contains(document.activeElement)) {
                    close();
                }
            });
        });
    });
})();
