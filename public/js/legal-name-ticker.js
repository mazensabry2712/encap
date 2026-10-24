(() => {
    'use strict';

    const tickers = document.querySelectorAll('[data-legal-ticker-track]');
    if (!tickers.length) return;

    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

    tickers.forEach((track) => {
        const ticker = track.closest('.encap-legal-ticker');
        const sourceGroup = track.querySelector('[data-legal-ticker-source]');
        const seed = sourceGroup?.querySelector('.encap-legal-ticker-item');

        if (!ticker || !sourceGroup || !seed || typeof track.animate !== 'function') return;

        // Keep one untouched copy of the seed; all visible repeats are generated from it.
        const itemTemplate = seed.cloneNode(true);
        let animation = null;
        let frame = 0;
        let previousViewportWidth = -1;
        let forceNextBuild = false;

        const rebuild = (force = false) => {
            frame = 0;
            if (!ticker.isConnected) {
                animation?.cancel();
                animation = null;
                return;
            }

            const viewportWidth = ticker.clientWidth;
            if (!viewportWidth) return;

            if (!force && animation && Math.abs(viewportWidth - previousViewportWidth) < 1) return;
            previousViewportWidth = viewportWidth;

            animation?.cancel();
            animation = null;

            // Remove any old clone and rebuild from a single seed every time.
            track.replaceChildren(sourceGroup);
            sourceGroup.replaceChildren();

            let itemCount = 0;
            do {
                sourceGroup.appendChild(itemTemplate.cloneNode(true));
                itemCount += 1;
            } while (sourceGroup.getBoundingClientRect().width < viewportWidth + 1 && itemCount < 300);

            // The second group is an exact clone, so it fills the first group's track
            // position as the animation reaches the measured group width.
            const duplicate = sourceGroup.cloneNode(true);
            duplicate.removeAttribute('data-legal-ticker-source');
            duplicate.setAttribute('aria-hidden', 'true');
            track.appendChild(duplicate);

            const distance = sourceGroup.getBoundingClientRect().width;
            if (!Number.isFinite(distance) || distance <= viewportWidth - 1) return;

            const pixelsPerSecond = reducedMotion.matches ? 36 : 72;
            animation = track.animate(
                [
                    { transform: 'translate3d(0, 0, 0)' },
                    { transform: `translate3d(${-distance}px, 0, 0)` }
                ],
                {
                    duration: Math.max(1000, (distance / pixelsPerSecond) * 1000),
                    easing: 'linear',
                    iterations: Infinity
                }
            );
        };

        const scheduleRebuild = (force = false) => {
            forceNextBuild = forceNextBuild || force;
            if (frame) window.cancelAnimationFrame(frame);
            frame = window.requestAnimationFrame(() => {
                const shouldForce = forceNextBuild;
                forceNextBuild = false;
                rebuild(shouldForce);
            });
        };

        window.addEventListener('resize', () => scheduleRebuild(), { passive: true });

        if ('ResizeObserver' in window) {
            const observer = new ResizeObserver(() => scheduleRebuild());
            observer.observe(ticker);
        }

        if (document.fonts?.ready) {
            document.fonts.ready.then(() => scheduleRebuild(true)).catch(() => {});
        }

        if (typeof reducedMotion.addEventListener === 'function') {
            reducedMotion.addEventListener('change', () => scheduleRebuild(true));
        } else if (typeof reducedMotion.addListener === 'function') {
            reducedMotion.addListener(() => scheduleRebuild(true));
        }

        scheduleRebuild(true);
    });
})();
