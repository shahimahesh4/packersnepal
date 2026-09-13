# Technical architecture and delivery roadmap

Status: planned implementation. The repository initially contained only README and LICENSE. No Laravel application, migrations, dependencies, or automated tests exist yet. Commands below are setup guidance, not commands already executed.

## 1. Stack and compatibility

| Layer | Proposed choice | Purpose |
|---|---|---|
| Application | Laravel 12 | Routing, validation, domain logic, authorization, queues |
| Administration | Filament 5 panel at `/stnapanel` | Business resources, CMS, reporting |
| Frontend | Livewire 4 + Blade | Public forms and customer account |
| Styling | Tailwind CSS 4.1+ and Vite | Responsive public UI and asset builds |
| PHP | Supported PHP version compatible with all locked packages; minimum 8.2 for framework/panel | Runtime |
| Database | MySQL 8.x supported release or supported PostgreSQL release; choose one for development and production | Transactional storage |
| Permissions | Spatie Laravel Permission, Composer-compatible version | Roles and action permissions |
| Files | Private local disk in development; private object storage in production | Quotes, evidence, invoices |
| Queue/cache | Database drivers initially; Redis if operational scale requires it | Notifications, exports, scheduled work |
| Testing | Laravel feature tests plus Filament/Livewire component tests | Business and permission guarantees |

Filament 5 supports Laravel 12 and requires Livewire 4; use its installation guide's stricter Tailwind 4.1+ baseline. See [Filament installation](https://filamentphp.com/docs/5.x/introduction/installation) and [Filament upgrade requirements](https://filamentphp.com/docs/5.x/upgrade-guide). Do not mix Filament 3 examples or Livewire 3 APIs into this implementation.

Laravel 12 is explicitly requested. Review its support dates before production and budget a framework upgrade rather than treating the initial major version as permanent. See [Laravel 12 release and support policy](https://laravel.com/docs/12.x/releases).

Resolve exact compatible package versions at setup; commit `composer.lock` and `package-lock.json`. Use Composer's platform checks on the actual hosting runtime. Optional Filament plugins need explicit major-version compatibility checks.

## 2. Application structure

Use a modular Laravel monolith. Public pages, customer screens, and Filament call the same domain actions; business rules must not live only in form callbacks.

```text
app/
  Actions/{Sales,Bookings,Inventory,Billing,Publishing,Access}/
  Enums/
  Filament/{Resources,Pages,Widgets}/
  Http/{Controllers,Middleware,Requests}/
  Jobs/
  Livewire/{PublicSite,Customer}/
  Models/
  Notifications/
  Policies/
  Services/
resources/views/{components,pages,livewire}/
database/{migrations,factories,seeders}/
tests/{Feature,Unit}/
```

Routes: public named service/page routes, `/request-quote`, authenticated `/account/*`, authorized private document routes, and `/stnapanel`. Register any CMS slug catch-all last and reserve application slugs such as stnapanel, account, login, and request-quote. Public routes return only published revisions.

Suggested Livewire components: QuoteRequestForm, CustomerQuoteDetail, CustomerBookingDetail, CustomerInvoiceList, ProfileForm. Use reusable Blade components for public content blocks and navigation. Reauthorize every server action; client state and hidden IDs are untrusted. Standard Blade rendering should escape user data; sanitize approved rich text before publication.

## 3. Data model

Names below are domain suggestions. Add timestamps, foreign keys, explicit indexes, factories, and policies as appropriate.

| Tables | Essential fields and relationships |
|---|---|
| users | name, email unique, verified_at, password hash, active status; staff/customer identity |
| roles, permissions, pivots | Package-managed role/capability mappings |
| customers | user_id nullable unique, name, type, organization, phone, billing details |
| customer_addresses | customer_id, address, locality, service_area_id |
| services | name, slug unique, description, active, estimated duration, display ordering |
| service_areas | name, coverage notes, active |
| inquiries | reference unique, customer_id nullable, contact snapshot, service_id, address snapshot, preferred time, description, status, assigned_to |
| assessments | inquiry_id, assessor_id, scheduled_at, findings, labor estimate, access requirements |
| quotes | inquiry_id, reference unique, current_revision_id, status |
| quote_revisions | quote_id, revision_number, currency, totals, validity, terms snapshot, status, accepted_by, accepted_at, acceptance_method |
| quote_items | quote_revision_id, service/material reference nullable, description snapshot, quantity, unit, unit_price, discount, tax, line_total |
| bookings | reference unique, accepted_quote_revision_id unique, customer_id, status, scheduled_start/end, site snapshot |
| teams, team_members | Team definition and active membership |
| booking_assignments | booking_id, team_id/user_id, start/end, assigned_by |
| packing_items | booking_id, room/category, description, quantity, label_code, handling instructions, packed_at |
| checklist_entries | booking_id, requirement snapshot, completed_by, completed_at, notes |
| handovers | booking_id, acknowledged_by/name, acknowledgment_method, timestamp, exceptions |
| incidents | booking_id, reporter_id, type, description, resolution, status |
| change_orders, change_order_items | booking_id, revised charges/scope, approval and customer acceptance evidence |
| materials | SKU unique, name, unit, reference selling price, reorder threshold, active |
| stock_locations | Storage location names |
| stock_reservations | booking_id, material_id, location_id, reserved/released/consumed quantity |
| stock_movements | material_id, location_id, signed quantity, movement type, booking_id nullable, actor, unique operation key |
| invoices, invoice_items | customer_id, booking_id, number unique, immutable issued snapshots, currency, totals, due date |
| payments | customer_id, amount, currency, method, provider/reference, verification status, verified_by |
| payment_allocations | payment_id, invoice_id, allocated amount; permits a deposit recorded before invoice issue |
| refunds, credit_notes | Original payment/invoice reference, amount, reason, approval, reconciliation status |
| pages | immutable ID, slug unique, title, active published_revision_id |
| page_revisions | page_id, version, structured block JSON, SEO snapshot, status, author, reviewer, publish_at |
| page_user_permissions | user_id, page_id, action, granted_by, expires_at; unique user/page/action |
| menus, menu_items | Location, label, permitted target, order, parent_id |
| media_assets | Disk, path, MIME type, size, owner/context, visibility, consent metadata |
| faqs, testimonials, galleries | Public content, ordering, moderation/publication state |
| settings | Typed allowlisted public/business configuration, no raw credentials |
| status_histories, audit_events | Entity, actor, event, old/new state, reason, timestamp; append-only |
| notification_deliveries | Event/recipient idempotency key, channel, delivery status, retry metadata |

Use dedicated media association tables or constrained polymorphic associations with ownership policies. Avoid a public storage URL for inquiry photos and financial documents. Snapshot contact/address information on accepted business records; profile edits must not rewrite history.

### Core integrity rules

- Store money as integer minor units, with documented currency precision; quantities use fixed-precision decimals. Never use floating-point arithmetic for totals.
- Define one rounding rule for lines and totals. The server recomputes prices and ignores client-submitted totals.
- Available stock = posted on-hand quantity minus outstanding reservations. Consume stock and release the corresponding reservation in one transaction. Canceling a booking releases reservations without creating fictitious physical stock.
- Acceptance locks the quote and validates current revision/expiry before inserting a booking. Unique constraints and an idempotency key prevent duplicate results.
- Serialize conflicting scheduling operations using database locks on stable staff/capacity records; recheck overlap inside the transaction. Test against the production database engine.
- Published pages point to approved immutable revisions. Editing creates a draft; it must not mutate the public version.
- Issued invoices and verified payments use correction/credit workflows. Payment allocations cannot exceed verified available payment funds or invoice balance.
- A refund records an actual reconciled payout; an approval alone is not a completed refund.
- Quote expiry, scheduled publishing, reminder processing, and reservation cleanup run via the scheduler with overlap protection.
- Business writes commit before notifications are dispatched. Use retry-safe jobs; retries must not repeat acceptance, payments, or inventory movements.

## 4. Development setup procedure

1. Check PHP version and extensions, Composer, Node/npm, Git, and database availability. CLI PHP and web-server PHP must match supported versions. Verify Laravel's required extensions and the selected database PDO driver.
2. Scaffold Laravel 12 in a new empty temporary directory. This repository already contains files, so do not run `create-project` over it or overwrite its Git metadata.
3. Integrate the generated application into this repository while preserving LICENSE, documentation, and `.git`; review overlapping files manually.
4. Configure a local database and `.env`, then generate a unique application key. Commit only `.env.example` with placeholder values.
5. Install and lock compatible Filament, Livewire, permissions, and frontend dependencies. Use the package-provided migrations for permissions; add application-specific migrations separately.
6. Install the admin panel, register its provider, implement panel access and policies, and create the initial Owner through a local provisioning command with a private password prompt.
7. Configure Tailwind's Vite integration, Blade asset tags, and Livewire layout assets. Keep public styling and Filament theme configuration clear; avoid loading Alpine twice.
8. Run migrations and local-only demonstration seeders. Start Laravel, Vite, queue worker, and scheduler as development processes.

Illustrative PowerShell setup after choosing a new empty scaffold directory:

```powershell
php -v
composer --version
node --version
npm --version
composer create-project laravel/laravel packing-app "12.*"
Set-Location packing-app
composer require filament/filament:"~5.0" livewire/livewire:"~4.0"
php artisan filament:install --panels
composer require spatie/laravel-permission
npm install
npm install -D tailwindcss@"~4.1" @tailwindcss/vite@"~4.1"
composer check-platform-reqs
```

The unpinned permission-library install is an initial compatibility resolution, not a deployment practice; inspect the chosen release and commit its lockfile. Complete package configuration and migrations using that release's documentation before provisioning roles. Do not assume `make:filament-user` alone assigns Owner or implements production panel security.

Frontend integration reference: [Tailwind with Laravel and Vite](https://tailwindcss.com/docs/installation/framework-guides/laravel/vite). Livewire setup reference: [Livewire 4 installation](https://livewire.laravel.com/docs/4.x/installation).

## 5. Build phases and completion gates

These are planning estimates, not delivery commitments. For one experienced full-time developer, allow approximately 11–16 developer-weeks for the baseline, plus stakeholder review, content preparation, and integration delays. Re-estimate after confirming scope and designs.

| Phase | Work | Exit criteria | Indicative effort |
|---|---|---|---|
| 0. Scope and content | Confirm business defaults, service catalog, workflows, brand, acceptance criteria | Owner-reviewed specification and sample quote/invoice | 0.5–1 week |
| 1. Foundation and access | Laravel setup, authentication, staff invitations, roles, policies, audit | Customer/staff separation and page-scope tests pass | 1–1.5 weeks |
| 2. Website and CMS | Responsive templates, blocks, services, media, revision publishing, SEO | Assigned editor can maintain pages; public drafts inaccessible | 1.5–2 weeks |
| 3. Sales and customer area | Inquiry, assessment, quote revisions, acceptance, account ownership | Complete request-to-accepted-quote flow with retry tests | 2 weeks |
| 4. Operations and stock | Calendar, teams, booking transitions, packing checklist, inventory | No overbooking/over-reservation; packing and handover rehearsal passes | 2–3 weeks |
| 5. Billing and reports | Invoices, deposits, allocations, reconciliation, refunds, notifications | Financial scenarios reconcile and reports respect access | 1.5–2 weeks |
| 6. Release readiness | End-to-end QA, accessibility, performance, backups, staging review, staff training | Launch checklist passes and owner accepts staging | 2–3 weeks |

Dependencies: access control precedes every admin module; services precede inquiry forms; accepted quote revisions precede bookings; booking and stock rules precede completion; billing depends on agreed quote/change-order snapshots. Content gathering and hosting preparation can run alongside development.

Deliver each phase as reviewable code with migrations, factories, tests, and updated operator instructions. Do not defer all authorization work until the final phase.

## 6. Validation strategy

Run framework tests and the frontend production build in CI for each change. Add focused unit tests for price rounding and transition rules; feature tests for permissions, persistence, and workflows; Livewire/Filament tests for real user actions.

Required scenarios:

- Successful guest request; invalid contact, oversized uploads, spam throttling, duplicate submission.
- Guest-to-account linking only after verified contact ownership.
- Quote revision, expiry, supersession, discount approval, and two simultaneous acceptance attempts.
- Simultaneous overlapping crew assignment and stock reservation using production database semantics.
- Material receipt, reserve, consume, return, cancellation release, and authorized correction.
- Completed job with outstanding payment, partial payment, deposit allocation, overpayment rejection, partial refund, cancellation charges.
- Scoped pages, publication approval, revoked access, bulk operations, private downloads, and all tests in ACCESS-CONTROL.md.
- Failed email followed by retry without duplicate business writes.
- Public page cache invalidation after publishing; private content absent from cache and sitemap.
- Mobile and keyboard flow from request through customer quote acceptance.

For a future gateway, add server-side provider verification, signed callback validation, duplicate/out-of-order callback tests, currency/amount verification, and reconciliation. Never mark an invoice paid merely because the browser returns to a success page.

Use realistic factories and anonymized sample content. Test database restoration with a separate environment. Performance targets should be agreed against representative data; a provisional target is responsive public pages and sub-two-second common admin lists under normal expected load, measured in staging rather than asserted without testing.

## 7. Production deployment and operation

### Required infrastructure

Domain and HTTPS; supported PHP application hosting; database; persistent private storage; outbound mail; queue worker supervisor; scheduler running every minute; backups; error monitoring; and a staging environment. The web root must point to Laravel's `public` directory.

Set production environment variables through hosting secrets: APP_KEY, APP_URL, database connection, mail credentials, storage credentials, queue/cache configuration, and approved integration secrets. Keep debug disabled. Restrict filesystem write access to required runtime directories. Avoid exposing `.env`, source files, and private storage through the web server.

### Release procedure

1. CI runs tests, dependency checks, and `npm ci` / `npm run build`; retain the release artifact and lockfiles.
2. Deploy the same artifact to staging, apply migrations, and run the full business rehearsal.
3. Record owner acceptance, take and verify a database backup, and confirm the rollback procedure.
4. Deploy production dependencies with `composer install --no-dev --prefer-dist --optimize-autoloader`; validate platform requirements.
5. Apply reviewed migrations with `php artisan migrate --force`. Favor backward-compatible schema changes.
6. Build/cache configuration, routes and views using commands supported by the installed app; ensure all environment reads occur in configuration files where appropriate.
7. Switch the release, restart queue workers, ensure the scheduler and mail operate, and smoke-test public, customer, and staff access.
8. Monitor errors, failed jobs, invoice totals, and job scheduling. Roll back application code if safe; do not blindly reverse migrations that would remove production data.

### Operational responsibilities

| Frequency | Responsible role | Task |
|---|---|---|
| Daily | Operations | New requests, upcoming jobs, assignments, failed notifications |
| Daily | Finance | Payment verification, outstanding balances, refund reconciliation |
| Automated daily | Infrastructure owner | Encrypted database/media backup and failure alerts |
| Weekly | Business manager | Conversion, cancellations, team load, low stock |
| Monthly | Owner | Staff and page-access review, inactive accounts, content accuracy |
| Scheduled | Technical maintainer | Dependency/security updates, logs, capacity, restore rehearsal |

Provisional recovery targets: at most 24 hours of data loss and recovery within 4 hours. Confirm these targets and choose backup frequency/infrastructure accordingly. Store backups separately from the application host, restrict access, define retention, and test restores. Private photos and contact records need an owner-approved retention and deletion policy that also considers archived records and backups.

## 8. Definition of complete

- [ ] Business scope, service descriptions, pricing units, payment terms, and policies confirmed.
- [ ] Laravel 12 application and compatible dependencies installed with lockfiles.
- [ ] Responsive public website and customer area implemented.
- [ ] CMS revisions, page assignments, publication controls, and public settings implemented.
- [ ] Role/policy test matrix passes, including direct-request and data-leak checks.
- [ ] Sales, packing operations, stock, finance, and reports work end to end.
- [ ] Notifications, queues, scheduler, and failure handling verified.
- [ ] Automated tests and production frontend build pass.
- [ ] Production credentials configured privately and staff accounts individually provisioned.
- [ ] Staging accepted, backup restored successfully, release and rollback rehearsed.
- [ ] Staff trained using real business scenarios; owner receives operator and maintenance guides.
- [ ] Production smoke checks pass and a named maintainer owns ongoing support.

## 9. Remaining decisions

The most consequential unanswered question is whether the company offers only on-site packing or also sells packing materials independently. The baseline supports on-site services and material inquiries; a full retail checkout adds order fulfillment, payment, stock, and returns requirements.

Also confirm unpacking/custom crating, service locations, staff capacity, deposit rules, who approves quotes, exact public languages, account requirements, invoice/tax configuration, and whether partner moving companies need multiple users under one business account. The baseline uses one customer identity per business account; organization membership and team sharing need explicit additional policies.
