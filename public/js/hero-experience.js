(() => {
    'use strict';

    const scene = document.querySelector('[data-encap-hero]');
    if (!scene) return;

    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const precisePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
    const stage = scene.querySelector('[data-hero-tilt]');
    const background = scene.querySelector('[data-hero-background]');
    const layers = Array.from(scene.querySelectorAll('[data-hero-layer]'));

    if (!reducedMotion) {
        scene.classList.add('is-motion-ready');
        window.requestAnimationFrame(() => {
            window.requestAnimationFrame(() => scene.classList.add('is-entered'));
        });
    }

    const resetParallax = () => {
        if (stage) {
            stage.style.setProperty('--hero-tilt-x', '0deg');
            stage.style.setProperty('--hero-tilt-y', '0deg');
        }
        layers.forEach((layer) => {
            layer.style.setProperty('--hero-layer-x', '0px');
            layer.style.setProperty('--hero-layer-y', '0px');
        });
        if (background) {
            background.style.setProperty('--hero-bg-x', '0px');
            background.style.setProperty('--hero-bg-y', '0px');
        }
    };

    if (!reducedMotion && precisePointer && stage) {
        let pointerFrame = 0;
        stage.addEventListener('pointermove', (event) => {
            if (pointerFrame) return;
            pointerFrame = window.requestAnimationFrame(() => {
                const bounds = stage.getBoundingClientRect();
                if (bounds.width <= 0 || bounds.height <= 0) {
                    pointerFrame = 0;
                    return;
                }

                const x = Math.max(0, Math.min(1, (event.clientX - bounds.left) / bounds.width)) - 0.5;
                const y = Math.max(0, Math.min(1, (event.clientY - bounds.top) / bounds.height)) - 0.5;

                stage.style.setProperty('--hero-tilt-x', (y * -4).toFixed(2) + 'deg');
                stage.style.setProperty('--hero-tilt-y', (x * 4.5).toFixed(2) + 'deg');

                layers.forEach((layer) => {
                    const depth = Number(layer.dataset.heroDepth || 12);
                    const ratio = Math.min(1.5, Math.max(0.2, depth / 28));
                    layer.style.setProperty('--hero-layer-x', (x * -20 * ratio).toFixed(2) + 'px');
                    layer.style.setProperty('--hero-layer-y', (y * -16 * ratio).toFixed(2) + 'px');
                });

                if (background) {
                    background.style.setProperty('--hero-bg-x', (x * -6).toFixed(2) + 'px');
                    background.style.setProperty('--hero-bg-y', (y * -4).toFixed(2) + 'px');
                }

                pointerFrame = 0;
            });
        }, { passive: true });

        stage.addEventListener('pointerleave', () => {
            if (pointerFrame) window.cancelAnimationFrame(pointerFrame);
            pointerFrame = 0;
            resetParallax();
        });
    }

    let scrollFrame = 0;
    const updateScrollDepth = () => {
        const bounds = scene.getBoundingClientRect();
        const progress = bounds.top < 0 && bounds.bottom > 0
            ? Math.min(1, Math.max(0, -bounds.top / Math.max(1, bounds.height)))
            : 0;

        if (background) background.style.setProperty('--hero-scroll-y', (progress * 28).toFixed(2) + 'px');
        if (stage) stage.style.setProperty('--hero-scroll-stage', (progress * -14).toFixed(2) + 'px');
        scrollFrame = 0;
    };
    const requestScrollDepth = () => {
        if (scrollFrame) return;
        scrollFrame = window.requestAnimationFrame(updateScrollDepth);
    };

    if (!reducedMotion) {
        window.addEventListener('scroll', requestScrollDepth, { passive: true });
        window.addEventListener('resize', requestScrollDepth, { passive: true });
        updateScrollDepth();
    }
})();
