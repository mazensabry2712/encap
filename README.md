# ENCAP — Agricultural Production & Export

ENCAP is a bilingual company website built with Laravel 13, Blade and a MySQL-compatible database. The application includes a public product-category catalogue, category-specific detail pages, central SEO settings, and admin inboxes for export quote requests and general contact messages.

## Start here

Read the project-specific documentation in the [doc folder](doc/README.md) before making changes.

- [Website scope](doc/WEBSITE-SCOPE.md)
- [Admin and central SEO](doc/ADMIN-SEO.md)
- [Performance and smoothness](doc/PERFORMANCE.md)
- [Testing and release checklist](doc/QUALITY-CHECKLIST.md)

## Main stack

- Laravel + Blade server-rendered pages
- Eloquent and database migrations
- English/Arabic public content with RTL support
- Small authenticated Blade admin
- Pest feature tests and GitHub Actions checks

No SPA or public API is required for the current scope.

## New environment setup

1. Install Composer and npm dependencies and configure the environment.
2. Set a trusted APP_URL, app key, database and public filesystem settings.
3. Run php artisan migrate.
4. Run php artisan db:seed --class=CategorySeeder to insert the starter catalogue in a new/approved database.
5. Run php artisan storage:link.
6. Create an administrator interactively with php artisan admin:create.
7. Run npm ci and npm run build if Vite assets are required.
8. Run php artisan test --compact.

Never run migrations or seeders against existing production data without reviewing the changes and taking a suitable backup. Do not commit .env files, secrets or default admin credentials.

## Status

The repository contains the initial public site, bilingual routes, category management, central SEO/site settings, contact and quote inboxes, and a CI workflow. Treat the application as unverified until GitHub Actions is green and environment-specific deployment, mail and performance checks have been completed.
