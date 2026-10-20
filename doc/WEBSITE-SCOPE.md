# Website Scope and Content

## Brand and positioning

**Name:** ENCAP  
**Expanded name:** Egyptian National Agricultural Production Company  
**Business:** Own-farm agricultural production, sourcing from partner farms, preparation/processing and export enquiries.

**Suggested message:** “From Our Farms to Global Markets.” Treat this as marketing copy, not a separate legal or performance claim.

## Public pages

- **Home** — introduction, categories and quote actions.
- **About** — company positioning and own-farm/partner-farm sourcing.
- **Farms & Production** — seasonality and production enquiry information; do not invent farm sizes or capacity.
- **Products** — responsive category grid, category names and images.
- **Category Detail** — stable URL, main image/gallery, descriptions, confirmed formats/packaging and quote CTA.
- **Quality & Processing** — capabilities only when confirmed for the requested product.
- **Export** — enquiry process without unsupported destination/certification claims.
- **Gallery** — approved uploaded category imagery; add real farm/packing photography only when supplied.
- **Contact** — validated general enquiry form and optional company contact details from Central SEO/Site Settings.
- **Request a Quote** — B2B product enquiry, category preselection, quantity/unit, form, packaging and destination.

Locale-prefixed URLs use paths such as /en/products/mango and /ar/products/mango. English is the default language; Arabic supports RTL.

## Initial categories

- Mango
- Citrus
- Grapes
- Potatoes
- Onions
- Garlic
- Pomegranates
- Peppers
- Strawberries
- Other Fruits & Vegetables

Run php artisan db:seed --class=CategorySeeder on a new/approved database to insert the starting catalogue. The seeder uses firstOrCreate, leaving existing records untouched.

## Category fields

- English/Arabic names and short/full descriptions.
- Unique stable slug.
- Main image and optional gallery.
- Published/draft state and sort order.
- Available forms and packaging details, only when known.
- English/Arabic SEO title and description overrides.

Do not fill fields with fabricated sizes, grades, packaging, certifications or seasonal availability. Keep these blank until the company confirms them.

## Quote form

- Contact name and business email.
- Company (optional), country, category (optional).
- Estimated quantity and unit (optional).
- Product form: fresh, frozen or processed (requested preference, not a promise of availability).
- Packaging requirements (optional) and free-text message.

Validate on the server, rate-limit submissions, persist requests in the database and keep inbox data behind authentication. Email notification is not considered implemented until SMTP credentials and real delivery are tested.

## General contact form

Name, email, optional company/phone, subject and message are saved separately from quote requests. A simple honeypot and request rate limit reduce basic spam; they are not a complete anti-bot service.

## Admin essentials

- Sign-in protected by Laravel authentication.
- Category create/edit/publish/unpublish/delete, main and gallery images.
- Global SEO defaults and company contact fields.
- Category SEO overrides.
- Quote and contact inboxes with status updates.

The first admin is created interactively with php artisan admin:create. No public registration or default admin password exists.

## Performance and accuracy

Prefer Blade SSR, native forms, cached static CSS and optimized company-approved images. The CSS hero illustration is a design placeholder, not a real farm photo. Do not present generic images as ENCAP-owned facilities. Lighthouse goals are targets and must be measured separately.
