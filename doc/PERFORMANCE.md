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

The homepage uses a representative agricultural landscape image hosted by Unsplash as a visual direction, not a claim that the pictured land belongs to ENCAP. Replace it with an optimized, company-approved image when available; self-hosting an optimized asset would also remove the third-party image dependency. Product/category imagery should come from ENCAP-approved uploads.

## Measure rather than assume

Performance and accessibility improvements are goals, not measured results. Before calling the release complete:

1. Run npm ci and npm run build where the deployment pipeline requires Vite assets.
2. Run php artisan test --compact.
3. Verify public routes and the admin in a browser at desktop and mobile widths.
4. Check image loading, navigation, form validation/error messages and RTL layout.
5. Run Lighthouse or equivalent checks on the deployed environment and record measured results.
6. Recheck after adding real photography or changing fonts/assets.
