# Admin Panel and Central SEO

## Keep the admin small

Use Laravel + Blade and the project's existing authentication/build setup. Do not introduce a large admin framework unless the existing project cannot meet the requirements simply.

### Minimum admin features
- Authentication-protected dashboard.
- Category management: create, edit, publish/unpublish, delete, reorder.
- Image management: main image and optional gallery.
- Global SEO/site defaults.
- Category-specific SEO overrides.
- Quote-request inbox and status changes.

Validate all writes server-side. Protect routes with authentication and authorization. Use CSRF protection and safe image validation. Escape rendered content by default in Blade; do not render user-supplied HTML unescaped.

## Central SEO behavior

Central SEO means one shared Blade component/layout path that renders metadata consistently, with global defaults and optional per-page overrides. It does not mean creating a complex SEO subsystem.

### Global defaults
- Site name
- Default title
- Default meta description
- Default social-sharing image
- Default robots directive
- Canonical base URL from trusted app configuration

### Page/category overrides
- SEO title
- Meta description
- Optional social image
- Optional robots override only where justified

If an override is empty, use the global default or a sensible title derived from the page. Prevent duplicate title/description tags by rendering them in one shared layout/component.

### Technical SEO checklist
- Unique, meaningful title and description for Home, About, Products, and each published category.
- Canonical URLs use the configured canonical domain and correct language path.
- XML sitemap includes only published, indexable public pages/categories.
- `robots.txt` points to the sitemap and does not expose admin or private paths as indexable content.
- Open Graph and Twitter-compatible metadata for sharing.
- Breadcrumbs on category detail pages.
- Organization/WebSite/BreadcrumbList structured data only with accurate values.
- Product schema only if page content and business model genuinely qualify; do not invent prices, availability, or product offers.
- Arabic/English pages use correct language attributes, RTL/LTR direction, and reciprocal `hreflang` links when both translations exist.
- Uploaded images have descriptive alt text.
- 404 pages return an actual HTTP 404 status.

Do not add fake reviews, fake ratings, unsupported certifications, or unverified claims to structured data.

## Admin safety
- Never expose admin routes in navigation or sitemap.
- Require authentication for all admin mutations.
- Rate-limit sign-in and public enquiry forms appropriately.
- Keep credentials and API keys in environment/config, not in source control.
- Do not show debug details or stack traces to visitors in production.

## SEO acceptance
- Metadata appears in server-rendered HTML, not only after JavaScript runs.
- No duplicate canonical/title/description tags.
- Each published category has a stable URL and its own metadata.
- Unpublished categories are not publicly reachable as indexable content and are excluded from the sitemap.
- Sitemap output is valid XML and includes only canonical public URLs.
