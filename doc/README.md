# ENCAP — Project Documentation

**Project:** Egyptian National Agricultural Production Company (ENCAP)  
**Repository:** https://github.com/mazensabry2712/encap  
**Application:** Laravel + Blade + MySQL  
**Primary branch:** `main`

This folder is the practical source of truth for project scope, implementation decisions, performance goals, and acceptance checks. Keep it updated when project behavior or scope changes.

## Project goal

Build a polished, fast, accessible, bilingual company website for an Egyptian agricultural producer and exporter. ENCAP grows crops on its own farms, also sources produce from partner farms, and handles preparation/processing and export to multiple international markets.

## Agreed scope

- Public company website built with Laravel server-rendered Blade templates.
- English as the default language, with Arabic support and correct RTL layout.
- Responsive layout for mobile, tablet, and desktop.
- Products landing page with image-led category cards.
- Every category opens its own details page with a main image, image gallery, description, available product forms, packaging information, and quote CTA.
- Initial categories: Mango, Citrus, Grapes, Potatoes, Onions, Garlic, Pomegranates, Peppers, Strawberries, and Other Fruits & Vegetables.
- Basic Blade admin area for managing categories and their images/descriptions, plus site/SEO settings and quote requests.
- Central SEO defaults with per-page/category overrides.
- Contact and export quote-request forms.
- Secure uploads, server-side validation, automated tests, and deployment checks.

## Keep it simple

Do not add a SPA, public API, repository pattern, service layer, complex CMS, or separate product/inventory system unless a real requirement demonstrates the need. Prefer Laravel conventions, focused controllers, Eloquent models, Blade components, Form Requests when useful, and built-in authorization/validation.

Start with categories as the main content entity. Add a separate products/variants model only when the actual business workflow requires multiple distinct products within one category.

## Documentation index

- [Website scope and content](WEBSITE-SCOPE.md)
- [Performance and smoothness](PERFORMANCE.md)
- [Admin and central SEO](ADMIN-SEO.md)
- [Testing and release checklist](QUALITY-CHECKLIST.md)

## Content accuracy

Do not invent company certifications, export destinations, production capacity, customer logos, addresses, or claims. Keep unknown company details editable and mark them as needing confirmation. Stock/reference imagery must not be presented as verified photographs of ENCAP farms or facilities.

## Definition of done

A feature is not complete just because its page renders. It should work end-to-end, be responsive, have validation and authorization where relevant, pass its tests, avoid obvious accessibility/performance regressions, and have its documentation updated when behavior or scope changes.
