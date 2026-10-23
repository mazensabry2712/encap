# Performance and Smoothness

## Approach

ENCAP remains a server-rendered Laravel Blade application. Keep the runtime simple and reduce the amount of work needed to display the first screen.

- Use one cacheable stylesheet for the public site and one for the authenticated admin.
- Keep navigation and forms native; do not introduce a JavaScript framework for ordinary page interactions.
- Keep images responsive, define their dimensions, lazy-load below-the-fold catalogue images and prioritize the homepage hero.
- Avoid repeated, page-specific inline CSS; shared design tokens and reusable Blade layouts keep rendering consistent.
- Use pagination for catalogue categories and enquiry inboxes.
- Respect reduced-motion user preferences; transitions should be brief and never required to understand the interface.
- Preserve semantic landmarks, visible focus states, form labels and mobile-friendly tap targets.
- Keep the public pages bilingual and verify RTL layout, not just text direction.

## Visual design assets

Editorial agriculture, packing and logistics photography is downloaded once into the project-root `encap-img/` directory and served from the root-level `encap-img/` directory through a cacheable Laravel image route (no symlink required). The photographs are representative stock imagery, not evidence of ENCAP-owned farms or facilities; replace them with company-approved images when available. Product/category imagery should come from ENCAP-approved uploads.

## Measure rather than assume

Performance and accessibility improvements are goals, not measured results. Before calling the release complete:

1. Run npm ci and npm run build where the deployment pipeline requires Vite assets.
2. Run php artisan test --compact.
3. Verify public routes and the admin in a browser at desktop and mobile widths.
4. Check image loading, navigation, form validation/error messages and RTL layout.
5. Run Lighthouse or equivalent checks on the deployed environment and record measured results.
6. Recheck after adding real photography or changing fonts/assets.


## Interface behavior

- All public-facing photographs and uploaded product/SEO images resolve through the `encap_images` disk rooted at `base_path('encap-img')`. After deployment, use **Prepare image library** on the authenticated admin dashboard to copy current uploads and ensure the stock photo set is available locally. CLI-capable hosts may instead run `php artisan encap:images:prepare`.

`public/js/site-motion.js` is the single owner of public-page scroll reveals, pointer-based 3D card tilt, magnetic actions, image depth and the scroll-progress line.
- public/js/hero-experience.js adds the homepage's dedicated layered parallax and orbit effects; the site-wide controller deliberately skips that hero to avoid competing transforms.
- public/js/encap-interactions.js handles only functional interactions: the sticky-header state, mobile-menu close behavior and product-gallery thumbnail selection.
- Components receive at most one reveal wrapper, preventing nested reveal/transform effects that can obscure content or make elements feel like they collide.
- Motion respects prefers-reduced-motion. Reveals gracefully degrade when IntersectionObserver is not supported.
- Product catalogue images use fixed dimensions and lazy loading. The home hero is the only high-priority image.
- Navigation, forms, product pages and the gallery remain usable without JavaScript; scripts enhance the experience rather than control page access or enquiry submission.
- Fonts load via preconnect and one Google Fonts stylesheet. If network delivery is unreliable in the production market, assess self-hosting only after measuring.

## Verification

- CI checks JavaScript syntax and runs npm run build after Laravel feature tests pass.
- Test actual pages at 1440px, 1024px, 768px and 390px widths, in English/LTR and Arabic/RTL.
- Measure Lighthouse/Core Web Vitals only on a deployed build; do not infer performance scores from successful asset compilation.
