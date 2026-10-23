# Quality and Release Checklist

Use this for each meaningful change. Mark an item complete only after it has actually been checked in the target environment.

## Functional

- [ ] Home, About, Farms, Quality, Export, Gallery, Contact, Products, Category Detail and Quote pages render.
- [ ] The ten initial categories are inserted in a new database by the category seeder.
- [ ] Category links open the correct details page and work on direct refresh.
- [ ] Main image and gallery render; missing images fail gracefully.
- [ ] English/Arabic page content, language switching and RTL/LTR direction work.
- [ ] Quote form validates and persists a request.
- [ ] Contact form validates and persists a message; the honeypot rejects filled submissions.
- [ ] Admin can review and update the status of both enquiry types.
- [ ] Guest requests to admin routes redirect to /admin/login.
- [ ] Admin can create/edit/publish/unpublish/delete categories.
- [ ] Invalid/oversized image uploads are rejected.
- [ ] Unpublished categories return 404 on public detail routes.

## SEO

- [ ] Central defaults save and render from server-generated HTML.
- [ ] Static page SEO overrides render correctly in both languages.
- [ ] Category SEO overrides render correctly.
- [ ] Title/description/canonical tags are not duplicated.
- [ ] Canonical URLs use configured APP_URL, not a request host header.
- [ ] Open Graph, Twitter card and valid structured data are present.
- [ ] Sitemap is valid XML and contains only public, published/indexable categories.
- [ ] robots.txt resolves through Laravel and links to sitemap.
- [ ] Quote form is `noindex,follow`, omitted from the sitemap, and not blocked in `robots.txt`; admin paths remain disallowed.
- [ ] Open Graph and Twitter large-card metadata include a usable social image even when no image is configured in admin settings.
- [ ] English/Arabic alternates are emitted only when appropriate.
- [ ] Missing public routes return HTTP 404.

## Performance and accessibility

- [ ] Production asset build passes.
- [ ] Static CSS is independently cacheable.
- [ ] Below-the-fold images lazy-load; the primary image is not unnecessarily delayed.
- [ ] `php artisan encap:images:prepare` completes successfully and every curated stock image exists under `encap-img/`.
- [ ] Existing category/gallery/SEO uploads are copied into `encap-img/`, and the original files remain intact until visual verification is complete.
- [ ] The authenticated admin dashboard can prepare the image library without shell access; image URLs use the `/encap-img/...` Laravel route and return HTTP 200.
- [ ] Images have dimensions and descriptive alt text.
- [ ] Mobile widths have no horizontal overflow.
- [ ] Keyboard focus is visible; controls have labels.
- [ ] Reduced-motion preference is respected.
- [ ] Lighthouse runs are recorded with URL, date, device mode and score; do not claim a score until measured.

## Engineering

- [ ] Relevant Pest feature tests pass in CI.
- [ ] Existing tests remain green.
- [ ] No secrets or default admin credentials are committed.
- [ ] Composer/npm dependencies were not changed without a requirement.
- [ ] Logs and browser console were checked after smoke tests.
- [ ] Documentation matches implemented behavior.

## Release safety

- [ ] Confirm target branch and current commit.
- [ ] Review full diff before deployment.
- [ ] Back up production data before schema changes.
- [ ] Review each migration and seeder before running it on an existing database.
- [ ] Do not run destructive database commands.
- [ ] Verify health, public pages, form persistence, admin sign-in and logs after deployment.
- [ ] Keep a rollback path.

## Status convention

Report checks as Passed, Failed, or Not run. Do not claim CI, Lighthouse, deployment, or email delivery succeeded without actual evidence.


## Visual QA — current ENCAP redesign

- [ ] Home hero and product-card imagery reviewed on desktop and mobile.
- [ ] Category index numbers remain correct on paginated product pages.
- [ ] Product gallery buttons update the main image and expose the selected state.
- [ ] Gallery cards navigate to the correct published product category.
- [ ] Arabic RTL layout verified for navigation, buttons, cards, product formats and forms.
- [ ] Reduced-motion behavior verified with the operating-system preference enabled.
- [ ] No horizontal scrolling at 390px mobile width.
- [ ] Production deploy and real-world image loading verified separately from CI.
