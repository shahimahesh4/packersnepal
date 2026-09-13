# Roles, permissions, and page assignments

Status: implementation contract. Authorization must be implemented and tested; this document does not create user accounts or permissions.

## 1. Access model

Use roles as reusable permission bundles, and record scopes to restrict where those permissions apply. Default to no access. A staff user needs an active account, explicit admin-panel access, the requested action permission, and access to the target record. A customer uses a separate customer-area policy.

Laravel policies are the enforcement layer for both Filament and Livewire. Hiding a menu is only a presentation choice: direct URLs, Livewire actions, downloads, exports, bulk operations, search, and dashboard counts need the same authorization. See [Laravel authorization](https://laravel.com/docs/12.x/authorization).

Use a maintained role/permission library such as Spatie Laravel Permission, with its compatible version resolved during setup. Model record-specific page grants in application tables. Filament access plugins are optional; they do not replace policies.

## 2. Default roles

| Role | Allowed responsibilities | Boundary |
|---|---|---|
| Owner / Super Admin | All business modules, website, access administration, audit | Protected ownership operations and audit immutability still apply |
| Business Admin | Sales, operations, catalog, website, reports | Cannot assign Owner, change credentials, or grant access by default |
| Website Manager | All public pages, publishing, menus, media, SEO, public settings | No customer records, finance, or role management |
| Page Editor | View and edit assigned pages, submit revisions | Cannot publish, change menus, or edit unassigned pages |
| Page Publisher | Review and publish assigned pages | No website-wide access unless separately granted |
| Sales | Assigned inquiries/customers, assessments, quotes | Approval, discount override, and broad exports separate |
| Operations Manager | Bookings, teams, schedules, checklists, stock reservations | No role management or payment verification |
| Packing Team | Assigned job details, checklist, evidence and consumption entry | No pricing, unrelated jobs, customer lists, or finance |
| Inventory Manager | Materials, receipts, returns, permitted adjustments | No finance or customer administration |
| Accountant | Invoices, reconciliation, approved refunds, finance reports | No CMS or access administration |
| Customer | Own profile, requests, quotes, bookings, invoices | No Filament admin access or other customers' records |

Roles may be combined deliberately. Show combined effective access before saving. Account suspension invalidates sessions and blocks subsequent requests. Customer registration always creates a customer role; clients cannot submit staff role fields.

## 3. Permission catalog

Use stable permission keys seeded from code. Staff assign known permissions rather than inventing arbitrary keys that have no enforcement.

| Area | Example keys |
|---|---|
| Panel | `admin.access` |
| Access administration | `users.view`, `users.invite`, `users.suspend`, `roles.manage`, `access.assign` |
| CMS | `pages.view`, `pages.create`, `pages.update`, `pages.submit`, `pages.approve`, `pages.publish`, `pages.archive`, `pages.scope.all` |
| Website globals | `navigation.manage`, `media.manage`, `seo.manage`, `settings.public.update` |
| Sales | `inquiries.view`, `inquiries.assign`, `inquiries.update`, `quotes.create`, `quotes.approve`, `quotes.send`, `quotes.discount_override` |
| Operations | `bookings.view`, `bookings.confirm`, `bookings.schedule`, `bookings.cancel`, `jobs.update_assigned`, `jobs.complete` |
| Inventory | `materials.manage`, `inventory.receive`, `inventory.reserve`, `inventory.consume`, `inventory.adjust` |
| Finance | `invoices.view`, `invoices.issue`, `payments.verify`, `refunds.approve`, `finance.export` |
| Reporting | `reports.sales.view`, `reports.operations.view`, `reports.finance.view`, `audit.view` |

Separate each resource's read, create, update, archive, restore, and export permissions where those actions exist. Do not grant destructive operations simply because someone can edit. Financial records and audit events cannot be hard-deleted from the panel.

## 4. Assign control of one page

Example: give a staff member permission to edit About and FAQ, while another person publishes their revisions.

1. Owner opens **People & Access → Users → Invite staff** and enters name and email.
2. Assign **Page Editor**, which includes `admin.access`, `pages.view`, `pages.update`, and `pages.submit`.
3. In **Page access**, select the About and FAQ page records and allow view, update, and submit for each.
4. Leave approve, publish, archive, global scope, menus, and settings disabled.
5. Preview effective access and save. Send a time-limited invitation, never a shared password.
6. The editor sees only those pages; direct access to Home is denied server-side.
7. A Page Publisher with grants for About and FAQ can review and publish those revisions.

Page grants bind to immutable page IDs, not URL slugs or display names. Renaming a page preserves access. A newly created page grants its creator scoped draft access if they have `pages.create`; creation never grants publication rights.

For whole-website control, assign Website Manager. For full business-system control, assign Owner deliberately through the protected ownership workflow. These are different permissions.

## 5. Exact page authorization rule

For a non-owner staff user, allow a page action only when:

```text
account is active
AND user has admin.access
AND user has the specific pages.<action> capability
AND (
    user has pages.scope.all
    OR an unexpired page grant exists for this user, page ID, and action
)
```

Owner is an explicit audited exception to ordinary scopes, never to inactive-account checks or business data invariants. A record grant cannot grant an action absent from the user's role capabilities. Multiple role permissions combine by union; the baseline does not implement deny overrides. To remove broad access, remove the broad role or `pages.scope.all`; adding a narrow page grant cannot restrict an existing broad role. Show this warning in the effective-access preview.

Suggested `page_user_permissions`: id, user_id, page_id, action, granted_by, expires_at, created_at, updated_at. Unique index on (user_id, page_id, action). Use foreign keys and audit revocation. Read access is also required for edit/review screens. Do not offer standalone edit grants without view in the assignment UI.

Navigation management and reusable global blocks are separate global capabilities. Editing a page must not permit editing a shared footer, shared media file, or other global content used elsewhere. An editor can attach approved media or upload new media for their assigned page, but cannot overwrite globally shared assets.

## 6. Scope for other records

- Sales staff: assigned inquiries and related customer details; managers may have explicit all-record scope.
- Packing team: assigned jobs only, with the minimum contact/address details needed for work.
- Customers: records linked to their authenticated customer identity; never trust a submitted customer ID.
- Accountants: finance records and necessary billing information; internal packing photos remain restricted.
- Website staff: public CMS data only; no inquiries through global search or dashboard totals.

Scope queries before listing, filtering, searching, counting, exporting, or selecting relations. Recheck policies when mutating records. Private storage downloads must authorize the owning record before returning a short-lived URL or streamed file.

## 7. Escalation prevention and audits

Only an authorized Owner manages privileged role definitions and assignments in the baseline. Other admins may invite users only into a fixed low-privilege role if explicitly permitted. No user may elevate themselves, edit their own grants, assign an Owner, or remove the last active Owner through ordinary user forms. Ownership transfer requires a separate authenticated, audited action and recent authentication.

Store access-change audit events with actor, target, old/new role or grant, timestamp, and request identifier. Invalidate permission caches after changes, including long-running workers; recheck active status on privileged requests. Require MFA for privileged staff. Never log passwords, tokens, or payment secrets.

## 8. Required authorization tests

| Test | Expected result |
|---|---|
| Page editor updates assigned About page | Allowed; draft revision created |
| Same editor changes unassigned Home ID in a Livewire request | Denied; no writes |
| Editor triggers publish action directly | Denied |
| Publisher with no grant for Home publishes Home | Denied |
| Page grant exists but role lacks publish | Denied |
| Website manager attempts finance export or user-role edit | Denied |
| Team member guesses another booking ID | Denied |
| Customer changes invoice ID in a download URL | Denied |
| Suspended Owner accesses panel with old session | Denied |
| User edits own role/grants through crafted input | Denied |
| Bulk action mixes allowed and forbidden records | Entire request rejected before writes |
| Grant revoked during an open edit session | Next save denied |
| Restricted record queried through search, counts, or relations | Not disclosed |
| Slug changes on an assigned page | Access preserved by page ID |
| Last Owner removal is attempted | Rejected |

The launch criterion is backend enforcement of this matrix, not merely correct menu visibility.
