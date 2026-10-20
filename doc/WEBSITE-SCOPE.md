# Website Scope and Content

## Brand and positioning

**Name:** ENCAP  
**Expanded name:** Egyptian National Agricultural Production Company  
**Business:** Own-farm agricultural production, sourcing from partner farms, preparation/processing, and export to multiple international markets.

**Suggested message:** “From Our Farms to Global Markets.” Treat this as draft marketing copy, not a legal claim.

## Public pages

1. **Home** — clear hero, short company introduction, featured categories, farm-to-export process, quality/processing overview, and contact/quote calls to action.
2. **About ENCAP** — company overview, mission/vision if confirmed, and a transparent explanation of own production plus partner-farm sourcing.
3. **Products** — responsive category grid, with category name and image, and optional short description.
4. **Category details** — dedicated stable URL for each category; main image, gallery, description, available forms (fresh/frozen/processed), packaging information when confirmed, related categories, and Request a Quote action.
5. **Farms and Production** — describe own farms and crop production. Use real company images and verified details when supplied.
6. **Quality and Processing** — explain actual sorting, grading, handling, packaging, and processing capabilities only after they are confirmed.
7. **Export** — describe the enquiry and export workflow without claiming unverified destinations or certifications.
8. **Contact / Request a Quote** — contact details and a focused B2B quote form.

Keep navigation short and predictable. Avoid adding separate pages that only repeat the same information.

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

The first eight/nine categories can use dedicated records. “Other Fruits & Vegetables” should be a flexible category, not a dumping ground for unsupported claims.

## Category content fields

Minimum fields:
- Name
- URL slug
- Short description
- Full description
- Main image
- Additional gallery images (optional)
- Published/draft status
- Display order
- SEO title (optional override)
- SEO description (optional override)

If the website is bilingual, store English and Arabic fields consistently. Do not store translated content in ambiguous mixed-language fields.

Optional details should only be added if the company can maintain them: seasonality, sizes/grades, packaging options, origin, certifications, or fresh/frozen/processed formats.

## Category interaction

- The entire category card should be a normal accessible link.
- Use a stable URL such as `/products/mango`; avoid query-string-only detail pages.
- The detail page must still work when opened directly or refreshed.
- Quote CTA should preselect the category on the quote form.
- Gallery thumbnails should be keyboard accessible and have meaningful alt text.
- Images should have a sensible fallback if a file is missing.

## Quote form

Keep the form practical for an importer:
- Contact name
- Company name (optional if not available)
- Business email
- Country
- Product/category
- Estimated quantity and unit (optional)
- Product form: fresh, frozen, or processed
- Packaging requirements (optional)
- Message

Validate on the server, rate-limit public submission, protect against spam proportionately, and show a clear success/error message. Persist enquiries in the database. Email notification is only complete after SMTP is configured and delivery is tested. Never expose private enquiry data publicly.

## Languages

- English is the default language.
- Arabic is supported with proper RTL direction, translated navigation, labels, validation messages, and category content.
- Language switching should preserve the current page/category when a translation exists; otherwise fall back gracefully.
- Each language version needs a stable URL and correct SEO metadata.

## Admin essentials

Keep admin focused:
- Sign-in protected by authentication.
- Category list/create/edit/publish/unpublish/delete with validation.
- Main image and gallery upload/removal.
- Global site and SEO defaults.
- Per-category SEO overrides.
- Quote-request list and status update.
- Basic contact/settings fields only when needed.

Do not build roles/permissions complexity for a single trusted administrator unless multiple staff roles become a real requirement.
