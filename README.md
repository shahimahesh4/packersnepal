# Packers Nepal — Packing Services Management

A Laravel 12 application for a specialist packing business serving households, offices, retailers, and moving companies. The business prepares and protects goods; transport is outside the service scope.

**Status:** working local application with public website and role-controlled administration. Production deployment is not configured.

## Working features

- Responsive Tailwind website with packing service cards, About, and How it works.
- Livewire quote-request form with validation, consent, rate limiting, duplicate protection, and reference acknowledgment.
- Filament dashboard with live inquiry, service, and page statistics plus a recent-requests table.
- Separate tabbed administration for website pages, service detail pages, SEO, visibility, and website settings.
- Quote requests and contact messages stored in the database with unique references, filters, status workflow, and staff notes.
- General, contact, social-media, and default SEO settings connected to the public website.
- CMS draft editing, separate publication action, published snapshots, and publication history.
- Owner, Website Manager, Page Editor, Page Publisher, and Sales Manager roles.
- Owner-controlled staff accounts and per-page view/edit/publish grants, enforced by policies and scoped queries.
- Audit entries for staff/access changes and page publication; no shared default account.

## Run locally

Dependencies: PHP 8.2+ with required Laravel extensions, Composer, Node/npm. SQLite is configured for local development; the installed dependencies are locked.

```powershell
composer install
Copy-Item .env.example .env # First setup only; preserve an existing .env
php artisan key:generate # First setup only; preserve an existing key
php artisan migrate --seed
npm ci
npm run build
php artisan packers:create-owner
php artisan serve --host=127.0.0.1 --port=8012
```

The local application uses the XAMPP MySQL/MariaDB database named `packersnepal`. Configure `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` in `.env`, then run `php artisan migrate --seed`. Run `packers:create-owner` in your terminal only when the database has no Owner: it privately prompts for name, email, and password and provisions the first Owner.

Website: `http://127.0.0.1:8012`. Administration: `/stnapanel`. For frontend development after setup, use `composer dev`; stop a separately started server first to free port 8012. This development command avoids Pail, whose process requirements are unsuitable for this Windows environment.

In **Staff and access**, create a Page Editor and assign a page through **Page access**. Roles and page grants must both permit the action. Assign Website Manager for all-page, service, and website-setting control; this does not grant inquiry or staff access. The ordinary staff forms cannot edit Owner accounts or the signed-in Owner. Sales Managers can review quotes and messages, update their status, and keep internal notes.

## Verify

```powershell
php artisan test
php vendor/bin/pint --test
npm run build
composer check-platform-reqs
```

Feature tests cover the public forms, database storage, website settings, dashboard widgets, validation, duplicate submission, page scoping, draft/publication separation, forbidden access, account suspension, role injection, and staff administration.

## Remaining delivery phases

Customer registration/login and ownership linking; assessment and quote revisions/acceptance; booking and capacity scheduling; materials/inventory; invoices/payments; notifications; CMS block builder and menu editor; approval workflow and revision restoration; MFA and invitations; production deployment and recovery checks.

The page CMS uses safe plain text with separate draft and publish actions. Publication snapshots are retained in `page_revisions`; history browsing and rollback are still planned. Service catalog changes are immediate. Page slugs are fixed after creation to keep links stable. The homepage layout is maintained in Blade; its service cards and detail pages come from the backend. Sales Manager currently manages all inquiries, with assigned-sales scoping deferred to the customer/CRM phase. No outbound email is sent in this version.

## Documentation

- [Project specification and business process](docs/PROJECT-SPECIFICATION.md)
- [Roles, permissions, and page access](docs/ACCESS-CONTROL.md)
- [Technical architecture and implementation roadmap](docs/IMPLEMENTATION.md)
- [Word project manual](docs/Packers-Nepal-Project-Manual.docx)

Installed stack: Laravel 12, Filament 5, Livewire 4, Tailwind CSS 4, Spatie Laravel Permission 6, SQLite for development. Exact releases are in the lockfiles.

Start with the project specification for scope and owner decisions; use the access-control document as the authorization contract and the implementation document as the delivery checklist.

The Word manual contains the full target specification, not a claim that every module is implemented. Its OOXML and accessibility structure were checked; visual pagination could not be verified because the provided Windows runtime lacks a LibreOffice renderer. `docs/build_word.py` regenerates it using python-docx.
