(() => {
    'use strict';

    const tickers = document.querySelectorAll('.encap-legal-ticker');
    if (!tickers.length || typeof Element.prototype.animate !== 'function') return;

    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

    tickers.forEach((ticker) => {
        const track = ticker.querySelector('.encap-legal-ticker-track');
        const sequences = track
            ? Array.from(track.children).filter((child) => child.classList.contains('encap-legal-ticker-sequence'))
            : [];

        if (!track || sequences.length < 2) return;

        let animation = null;
        let frame = 0;

        const startLoop = () => {
            frame = 0;
            if (!track.isConnected) {
                animation?.cancel();
                animation = null;
                return;
            }

            // Move by the measured width of exactly one repeated sequence.
            // Sequence two then occupies sequence one's former position with no gap.
            const distance = sequences[0].getBoundingClientRect().width;
            if (!Number.isFinite(distance) || distance <= 0) return;

            animation?.cancel();
            track.style.animation = 'none';
            track.style.transform = 'translate3d(0, 0, 0)';

            const pixelsPerSecond = reducedMotion.matches ? 34 : 72;
            animation = track.animate(
                [
                    { transform: 'translate3d(0, 0, 0)' },
                    { transform: `translate3d(${-distance}px, 0, 0)` }
                ],
                {
                    duration: (distance / pixelsPerSecond) * 1000,
                    easing: 'linear',
                    iterations: Infinity
                }
            );
        };

        const scheduleRestart = () => {
            if (frame) window.cancelAnimationFrame(frame);
            frame = window.requestAnimationFrame(startLoop);
        };

        window.addEventListener('resize', scheduleRestart, { passive: true });

        if ('ResizeObserver' in window) {
            const observer = new ResizeObserver(scheduleRestart);
            observer.observe(sequences[0]);
            observer.observe(ticker);
        }

        if (document.fonts?.ready) {
            document.fonts.ready.then(scheduleRestart).catch(() => {});
        }

        if (typeof reducedMotion.addEventListener === 'function') {
            reducedMotion.addEventListener('change', scheduleRestart);
        } else if (typeof reducedMotion.addListener === 'function') {
            reducedMotion.addListener(scheduleRestart);
        }

        scheduleRestart();
    });
})();
