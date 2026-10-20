# Performance and Smoothness

Speed is a core requirement, not a late polishing task. Use the simplest implementation that meets the targets below. Measure real output instead of assuming a page is fast because it has little JavaScript.

## Main principles

1. **Server-render with Blade.** Avoid SPA frameworks and large JavaScript dependencies.
2. **Keep JavaScript minimal.** Use native links/forms and CSS for most interactions. Add small JavaScript only where it materially improves the experience (for example, a category image gallery).
3. **Optimize images first.** Use appropriately sized WebP/AVIF where supported, preserve useful quality, define width/height or aspect ratio, and never load full-resolution originals for small cards.
4. **Lazy-load below-the-fold images.** Do not lazy-load the main above-the-fold/LCP image; prioritize it appropriately.
5. **Avoid layout shifts.** Reserve image dimensions, keep card heights consistent, and avoid injecting content above visible content after page load.
6. **Keep requests low.** Bundle/minify CSS and JS for production, avoid duplicate libraries, and use local assets where practical.
7. **Use caching sensibly.** Cache static assets with versioned filenames and long-lived browser cache headers. Add Laravel caching only where it helps and can be invalidated safely.
8. **Keep database work lean.** Paginate growing lists, select only needed fields, avoid N+1 queries, and index real lookup/filter columns when justified.
9. **Avoid third-party scripts by default.** Add analytics, maps, chat widgets, or remote fonts only when approved and necessary.
10. **Protect perceived smoothness.** Clear hover/focus states, responsive navigation, no oversized animations, no forced scroll effects, and respect reduced-motion preferences.

## Practical targets

Targets are goals to measure on production-like builds, not guarantees for every device/network:
- Lighthouse Performance: aim for 90+ on key public pages in a consistent test environment.
- Core Web Vitals field goals: LCP <= 2.5s, INP <= 200ms, CLS <= 0.1 at the 75th percentile when sufficient real-user data exists.
- Avoid shipping unnecessary frontend JavaScript; establish and review the actual production bundle size.
- Main category page should not download every full-size gallery image at once.
- No horizontal overflow at common mobile widths.
- Main content should remain readable and usable if non-essential JavaScript fails.

## Image delivery

- Store uploads using Laravel's supported filesystem configuration.
- Validate file type and size on the server; do not trust the filename or browser MIME type alone.
- Create appropriately sized variants/thumbnails if the hosting environment supports reliable image processing. Do not add a complicated image-processing pipeline unless the volume justifies it.
- Add alt text; use empty alt text only for purely decorative images.
- Avoid remote image hotlinking for production content.
- Use real ENCAP images when available. Clearly treat placeholder/reference images as placeholders until replaced.

## Laravel production checks

When the host is configured for production, confirm:
- `APP_DEBUG=false`
- production environment variables are not committed
- Composer dependencies are installed for production
- built frontend assets exist and are referenced correctly
- `php artisan config:cache`, `route:cache`, and `view:cache` work with the actual routes/config (run only as part of an approved deployment process)
- `storage` and cache directories have appropriate permissions
- logs are checked after smoke tests

Do not run destructive commands or change production settings blindly. Test first and use a rollback plan.

## How to measure

- Test Home, Products, a Category Detail, and Contact/Quote pages at mobile and desktop sizes.
- Run Lighthouse in a consistent environment and record the date, URL, device mode, and scores.
- Inspect the Network panel: image payload, number of requests, blocking resources, and JS/CSS transfer sizes.
- Check browser console and Laravel logs.
- Measure before/after significant performance changes; avoid optimizations with no measurable benefit.

## Performance acceptance

- No avoidable blocking JavaScript for core navigation/content.
- Correct image dimensions and responsive image sizes.
- Below-the-fold images lazy-load.
- No major layout shift during initial render.
- Core routes remain functional without JavaScript except explicitly progressive-enhancement features.
- No known N+1 query on public category listing/detail routes.
- Mobile navigation, category links, and quote submission feel immediate under normal conditions.
