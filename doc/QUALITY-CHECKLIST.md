# Quality and Release Checklist

Use this list for each meaningful change. Mark an item complete only after actually checking it.

## Functional
- [ ] Home, About, Products, Category Detail, Contact, and Quote pages render.
- [ ] Every initial category has a valid slug and detail page.
- [ ] Category card opens the correct detail page.
- [ ] Main image and gallery render; missing images fail gracefully.
- [ ] Arabic and English content and layout work.
- [ ] Quote form validates input and persists the request.
- [ ] Quote request can be reviewed/updated in the admin.
- [ ] Admin routes reject unauthenticated users.
- [ ] Invalid or oversized image uploads are rejected.
- [ ] Draft/unpublished categories are not publicly exposed.

## SEO
- [ ] Title and meta description are present and not duplicated.
- [ ] Canonical URL is correct for the current language/page.
- [ ] Open Graph values are populated.
- [ ] Sitemap contains published, indexable pages only.
- [ ] robots.txt and sitemap reference are correct.
- [ ] Breadcrumb and structured data contain accurate information.
- [ ] 404 pages return HTTP 404.
- [ ] Arabic/English alternates are correct when translations exist.

## Performance and accessibility
- [ ] Production assets are built and minified.
- [ ] No avoidable JavaScript blocks navigation or page content.
- [ ] Images have dimensions/aspect ratios and appropriate responsive sizes.
- [ ] Below-the-fold images lazy-load.
- [ ] Main hero image is not lazy-loaded when it is the LCP image.
- [ ] No major layout shift on initial load.
- [ ] Keyboard can reach links, navigation, gallery controls, and form fields.
- [ ] Visible focus states and meaningful labels exist.
- [ ] Mobile widths have no horizontal overflow.
- [ ] Reduced-motion preference is respected.

## Engineering
- [ ] Relevant Laravel tests pass.
- [ ] Existing tests remain green.
- [ ] No secrets or production credentials are committed.
- [ ] No unrelated files or user data were changed.
- [ ] Logs and browser console have been checked after smoke tests.
- [ ] Documentation updated when behavior or scope changed.

## Release safety
- [ ] Confirm target branch and current commit before deployment.
- [ ] Check repository status and review the diff.
- [ ] Back up production data before risky migrations.
- [ ] Do not run destructive database commands.
- [ ] Run migrations only after reviewing them and receiving deployment approval.
- [ ] Verify health, public pages, quote submission, admin sign-in, and logs after deployment.
- [ ] Keep a rollback path.

## Status convention
Report checks honestly as **Passed**, **Failed**, or **Not run**. Do not claim Lighthouse scores, tests, production deployment, or email delivery unless they were actually verified.
