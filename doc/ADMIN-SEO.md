# Admin Panel and Central SEO

## Admin route map

| Route | Purpose |
|---|---|
| /admin/login | Sign in |
| /admin/categories | Category list and publication status |
| /admin/categories/create | Create a category |
| /admin/seo | Central SEO and company contact settings |
| /admin/quote-requests | Export quote inbox |
| /admin/contact-messages | General contact inbox |

All mutation forms use CSRF protection. Admin routes are behind Laravel authentication; guest requests are redirected to the admin sign-in page. Public account registration is not provided.

### Creating the first admin

After environment setup and migrations, run this in an interactive terminal:

    php artisan admin:create --name="ENCAP Admin" --email="your-real-admin-email"

The command asks for a password twice, requires at least 12 characters, rejects a mismatch, and refuses to overwrite an existing account. Never put the password in Git, a command-line option, or documentation. No production user should be created by automatically seeding a default password.

## Category management

Categories have English/Arabic names and descriptions, a unique slug, an optional main image and image gallery, formats and packaging fields, publication status and display order, plus English/Arabic SEO overrides. Uploaded files are validated by type and size and stored on Laravel's public disk. Configure the public storage link before using uploaded imagery.

The catalogue is intentionally category-led for now. Do not add inventory, pricing, variants, stock or order management until the real company workflow requires it.

## Enquiry handling

Export quote requests and general contact messages are stored separately. Public form fields are server-validated, public POST routes have a rate limit, and the general contact form includes a honeypot. Admin inboxes provide status updates. No claim of email notification should be made until SMTP credentials and delivery are configured and tested.

## Central SEO behavior

A single shared Blade layout renders global metadata consistently. Global settings include:
- Default site title and meta description in English and Arabic.
- Default robots directive.
- Organization name and official URL.
- Default social-sharing image.
- Contact email, phone, WhatsApp number and address in both languages.

Static public pages can also have English/Arabic title and description overrides from the same central settings screen. Category pages retain their own title and description overrides in category management. Blank fields fall back to the page's sensible title/description or the central site defaults. Store the official production URL in APP_URL; canonical and sitemap URLs use that configured base rather than untrusted request host headers.

### Technical behavior

- Canonical, Open Graph and Twitter-card metadata are emitted in server-rendered HTML.
- Organization and WebSite JSON-LD use configured organization data; category detail pages emit breadcrumb JSON-LD. No fake Product pricing, availability, reviews or certifications are generated.
- /sitemap.xml includes public indexable routes and published categories, with category alternates only when the Arabic category content exists.
- /robots.txt is served by Laravel and points to the sitemap. The old static starter public/robots.txt is removed so it cannot shadow the dynamic route on common web servers.
- Quote form paths are marked noindex,follow and disallowed in robots.txt; admin endpoints are never listed in the sitemap.
- Arabic pages use RTL direction and localized content where available; language alternatives use stable locale-prefixed URLs.
- All Blade content is escaped by default.

## Security and release notes

- Keep APP_DEBUG=false in production and never commit a populated .env.
- Review migrations and back up the target database before production schema changes.
- Run tests and the production asset build before deploying.
- Confirm uploaded-image permissions, logs, canonical URLs and sitemap on the actual domain after deployment.
- Admin currently uses a single trusted-user model. Add roles/policies only if staff-level permissions become a requirement.
