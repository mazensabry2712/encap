# ENCAP — Project Documentation

**Repository:** https://github.com/mazensabry2712/encap  
**Stack:** Laravel 13 + Blade + MySQL-compatible database  
**Source of truth:** main  
**Languages:** English (default) and Arabic (RTL)

This folder documents project scope, implementation decisions, performance targets, testing and release safety. Keep it aligned with the application.

## Goal

A polished, responsive agricultural company website for ENCAP. The company produces agricultural products on its own farms and also sources from partner farms for preparation, processing and export enquiries. Do not invent certifications, export countries, customer logos, farm imagery, contact details, quantities or other business claims.

## Deliberately simple architecture

- Server-rendered Blade pages; no SPA and no public API.
- Eloquent models and focused controllers for categories, enquiries and shared SEO/site settings.
- MySQL-compatible migrations; SQLite in-memory database for automated tests.
- Small authenticated Blade admin area; no public registration and no role/permission framework until multiple staff roles are actually needed.
- One central SEO settings record with per-page and per-category SEO overrides.
- Static, cacheable CSS file; native HTML links and forms; minimal JavaScript.
- No extra Composer/npm dependencies were added for the first implementation.

## Public pages and functions

- Home and category overview.
- About, Farms & Production, Quality & Processing, Export, Gallery and Contact.
- Product category detail routes such as /en/products/mango and /ar/products/mango.
- Export quote form saves an enquiry and preselects a category when opened from a category page.
- General contact form saves a message.
- English/Arabic navigation and RTL/LTR layout.

Initial catalogue categories are Mango, Citrus, Grapes, Potatoes, Onions, Garlic, Pomegranates, Peppers, Strawberries, and Other Fruits & Vegetables. The CategorySeeder uses firstOrCreate, so it does not overwrite edited categories when rerun.

## Admin

- /admin/login: sign-in.
- /admin/categories: list, create, edit, publish/unpublish and delete categories; manage main and gallery images; edit category SEO.
- /admin/seo: site SEO defaults, robots directive, Organization fields, social image and company contact details.
- /admin/quote-requests: review export enquiries and update status.
- /admin/contact-messages: review general messages and update status.
- php artisan admin:create --name="ENCAP Admin" --email="admin@example.com": securely prompts for a password and creates a user if that email does not already exist. No default admin password is committed.

All admin routes require authentication. Public enquiry endpoints are validated server-side and rate-limited.

## Setup on a new environment

1. Install PHP dependencies and configure .env with a trusted APP_URL, application key, database and filesystem settings.
2. Run php artisan migrate.
3. Run php artisan db:seed --class=CategorySeeder to insert the ten initial categories. Only run seeding after confirming the target database.
4. Run php artisan storage:link so uploaded public images can be served.
5. Run php artisan admin:create --name="ENCAP Admin" --email="your-real-admin-email" from an interactive terminal.
6. Build assets with npm ci and npm run build if using the included Vite assets.
7. In an approved production release, review migrations first, take an appropriate database backup, and configure Laravel's production caches only after a successful build and test run.

Do not run migrations or seeders blindly against an existing production database. The project has not been declared production-ready merely because files exist in GitHub.

## Central SEO rules

The shared Blade layout outputs title, description, robots, canonical, Open Graph/Twitter card fields and Organization/WebSite JSON-LD. Home uses central title/description defaults; category detail pages can override title and description. Sitemap is generated from configured APP_URL and published categories. Admin and quote form paths are excluded from the sitemap/crawl targets.

SMTP delivery is not yet part of the verified workflow. Both enquiry types are stored in the database and can be reviewed in the admin. Configure and test mail before promising email notifications.

## Performance approach

Use server-rendered content, low request count, a separately cacheable CSS file, reserved image dimensions, lazy loading for non-critical images and pagination. The editorial landscape and packing images are downloaded into the root `encap-img/` directory for local serving; they remain representative stock photography, not proof of ENCAP-owned farms or facilities. Category images must be supplied by the company. Lighthouse and Core Web Vitals are targets to measure, not claimed results.

## Documentation index

- Visual design system: DESIGN-SYSTEM.md
- Website scope and content: WEBSITE-SCOPE.md
- Performance and smoothness: PERFORMANCE.md
- Admin and central SEO: ADMIN-SEO.md
- Testing and release checklist: QUALITY-CHECKLIST.md

## Verification status

GitHub Actions is configured to run the Pest feature suite and Vite production build on pushes/pull requests to main. Treat tests and builds as not verified until the corresponding GitHub Actions run has completed successfully. Deployment, real email delivery, image optimization, and Lighthouse scores require separate environment-specific verification.


## Site-wide motion system

The public Blade layout loads `public/css/site-motion.css` and `public/js/site-motion.js` on all English and Arabic marketing pages (not the admin panel). The shared system includes a scroll-progress line, native page transitions where supported, staggered reveals for inner-page content, pointer-responsive 3D tilt on desktop cards, restrained photo parallax, magnetic primary actions, and slowly moving decorative rings. The homepage keeps its dedicated hero module as an additional layer.

Motion is built with native CSS and small vanilla JavaScript; no animation package or 3D runtime is required. Keep movement bounded, keep content readable and clickable throughout, test RTL and small screens, and respect `prefers-reduced-motion`. Avoid nesting multiple reveal wrappers on the same component or moving neighbouring elements through one another. The automated test `site-wide motion assets are loaded on public English and Arabic page templates` protects shared-layout coverage.


## Image storage

All static editorial photos, the brand logo, the favicon and uploaded category/SEO images are served from `encap-img/` at the project root, outside `public`. The `public/encap-img` symbolic link maps the URL prefix to that folder. Run `php artisan encap:images:prepare` after deploying the code: it downloads the site photo set and copies current image uploads from the legacy public disk while keeping the old copies for rollback. New uploads go directly to `encap-img/categories` or `encap-img/seo`. Runtime uploads are excluded from Git.
