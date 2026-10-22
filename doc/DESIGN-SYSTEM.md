# ENCAP Visual Design System

## Design direction

ENCAP is presented as a premium Egyptian agricultural production, sourcing and export business. The site should feel editorial, precise and trustworthy—not like a generic ecommerce theme. The design is informed by corporate storytelling and agricultural export product catalogues, while retaining an original ENCAP identity.

## Visual tokens

The public stylesheet defines the interface tokens centrally:

- Deep forest green for the brand and prominent dark surfaces.
- A restrained citrus-gold accent for primary actions and important markers.
- Warm off-white/cream surfaces to give agricultural photography space.
- Muted greens for supporting text, dividers and quiet UI.
- Fine borders, modest corner radii and restrained shadows instead of multiple nested cards.

Typography uses DM Sans for English and IBM Plex Sans Arabic for Arabic UI. Arabic headings use their own scale and line-height so RTL pages do not inherit inappropriate Latin letter spacing.

## Layout principles

- The homepage opens with a two-column editorial hero on desktop and a vertically stacked hero on mobile.
- A compact proof strip communicates four parts of the supply approach without inventing sales numbers or certifications.
- Product cards prioritize imagery and category names; category details carry the fuller description and enquiry action.
- Product detail pages distinguish the primary image, gallery thumbnails, overview, available formats, packaging and quote action.
- Company service pages share one consistent intro template, breadcrumbs, and related enquiry actions.
- About has a distinct editorial hero, a four-stage production-to-export story, an enquiry-practice section, mission/vision/values, a database-backed preview of published product categories, and a quote CTA.
- About uses illustrative stock photography with a visible disclosure until company-approved photography is supplied.
- Contact and quote forms use visible labels, clear errors, focus states and an explanatory next-steps panel.
- The admin UI shares the green/gold identity but favors dense, legible operational data.

## Interaction principles

- Navigation and essential links work with native anchors; the mobile menu uses native details/summary.
- Product gallery thumbnails are native buttons with aria-pressed state.
- Scroll reveals and header transitions are progressive enhancements only.
- Honor prefers-reduced-motion; avoid autoplay video, large animation libraries and layout-shifting effects.
- Keyboard focus must remain visible on links, buttons and gallery thumbnails.
- Keep JavaScript dependency-free for these small interactions.

## CSS 3D and icon interactions

- The homepage hero uses a CSS-built globe, orbit rings and floating icon tiles to create a dimensional agriculture/export illustration without loading a WebGL renderer.
- A shared inline SVG symbol sprite keeps the icon system crisp, consistent and dependency-free.
- Fine-pointer devices support gentle hero parallax and product/gallery card tilt. Touch devices do not get cursor-dependent behavior.
- Hover effects are progressive enhancements; navigation remains native, and reduced-motion preferences disable the animated globe and tilt transitions.
- Do not replace this lightweight layer with a 3D library until an actual, tested 3D asset is necessary and its bundle/performance cost is justified.

## Responsive checkpoints

Review both locales at 1440px, 1024px, 768px and 390px. Confirm the hero hierarchy, card grid, product gallery, menu, form layouts, image crops and long Arabic strings. No page should require horizontal scrolling at mobile widths.

## Content and trust

- Use company-approved farm, harvest, product and packing photographs wherever available.
- Until approved company photography is uploaded, treat any generic hero photo as representative imagery—not proof of ENCAP-owned farms.
- Never invent export markets, facilities, certifications, customer logos, production volume or availability.
- Keep contact details and SEO defaults editable through the existing administration area.
