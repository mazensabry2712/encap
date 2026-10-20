# Performance and Smoothness

Speed is a core requirement, not a final polish task. These are implementation choices and measurement targets; a score is not considered achieved until it has been measured.

## Current implementation choices

- Blade server-side rendering for the website; no SPA framework or client-side router.
- Native links and forms for navigation and submissions.
- Shared CSS in public/css/website.css, allowing the browser to cache styling independently of page HTML.
- No third-party font, analytics, chat or map scripts by default.
- Homepage and product listing query only published categories and cap the homepage list at ten items. Product listings paginate.
- Admin lists paginate; quote requests eager-load their category relationship.
- Category and gallery images reserve dimensions and lazy-load below-the-fold images. The primary category image is not lazy-loaded.
- Reduced-motion preference and visible keyboard focus states are supported.
- Metadata defaults are cached briefly and explicitly invalidated after admin changes.
- No image-processing dependency or thumbnail pipeline has been added yet; administrators should upload optimized, appropriately sized source images.

## Principles

1. Prefer simple server-rendered HTML over JavaScript-heavy interactions.
2. Use appropriately sized WebP/AVIF/JPEG images; never upload unnecessarily huge originals for small cards.
3. Keep image width/height or aspect ratio stable to reduce layout shift.
4. Avoid loading all gallery images on the initial category page.
5. Keep database queries predictable and paginate growing lists.
6. Add long-lived cache headers for versioned/static files at the web server/CDN level where supported.
7. Avoid speculative abstractions and dependencies that do not materially improve measured behavior.
8. Test on mobile widths and respect reduced-motion settings.

## Practical targets

These are targets rather than current verified results:
- Lighthouse Performance: aim for 90+ on key public pages in a consistent test environment.
- Core Web Vitals field goals when sufficient real-user data exists: LCP <= 2.5s, INP <= 200ms and CLS <= 0.1 at the 75th percentile.
- No avoidable blocking JavaScript for core content/navigation.
- No horizontal overflow at common mobile widths.
- No major layout shift during initial render.
- No N+1 query on the public category listing/detail routes.

## Image delivery

- Upload only company-approved imagery or label temporary illustrations clearly.
- Validate image type and size on the server.
- Configure Laravel's public disk/storage link before expecting uploaded images to be visible.
- Use meaningful alt text. Keep actual ENCAP farm/packing/export photography separate from generic placeholders.
- A responsive image/thumbnail pipeline may be added later if image volume or field measurements justify it.

## Production checks

Only during an approved deployment:
- Confirm production environment settings and APP_DEBUG=false.
- Install production Composer dependencies, build frontend assets if required, and review route/config/view cache behavior.
- Confirm storage permissions and check logs after smoke tests.
- Use the configured trusted APP_URL for canonical links and sitemap output.
- Do not run destructive commands or unreviewed migrations on production.

## Measurement checklist

Test Home, Products, a Category Detail and Contact/Quote pages on desktop and mobile. Record test date, URL, device mode and scores. Inspect network payloads, image transfers, CSS/JS size and browser console. Measure before/after substantial optimizations instead of assuming they improved performance.
