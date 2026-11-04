# CRM Audit - Today's State

Date: 2026-11-04
Scope: Seven Strokes CRM v2 (CodeIgniter 3, PHP 8.2, MySQL), branch `claude/crm-ui-ux-redesign-1b4c97`, merged into `main`.

## How this audit was done

- Read the controllers, models, views, config and routes.
- Ran the app locally (`php -S` on port 8091 with the local `crm7strokes` database).
- Logged in as a test admin and opened all 72 links in the top navigation. 70 loaded without errors, 1 failed, 1 redirected.
- Sent requests without a login to public and API endpoints to check what they allow.
- Did NOT test as every role. Only the admin role was used for the page crawl. Role checks below come from reading code.
- Did NOT test real 3CX calls, real WhatsApp messages, real email sending or real Groq AI calls (no live credentials in this environment).

Words used in this file:

- **Working**: tested in the browser or by request and it did what it should.
- **Built, not verified**: code exists and looks complete, but it was not tested end to end.
- **Placeholder**: shows fixed text or fixed numbers instead of real data.
- **Broken**: tested and it fails.

Priority:

- **P0**: fix before any customer uses the system. Security hole, data exposure, or core page broken.
- **P1**: fix soon. Important feature broken or risky.
- **P2**: should fix. Quality, consistency, maintainability.
- **P3**: future improvement.

---

## 1. Summary

The CRM has a wide set of working modules: database and leads, pipeline to orders, back office order processing, KAM, tasks, tickets, meetings, HR, reports, roles and permissions, a browser dialer with auto and manual modes, and the new dialer campaigns. Most pages load and use real data.

It is **not ready to be sold as a high-end enterprise SaaS product** (target price USD 1,250,000 per year). The main blockers are:

1. Security gaps that allow data exposure or unauthorized actions (section 3).
2. No multi-tenant design. The whole system is built for one company (section 9).
3. No automated tests, no proper database migrations, no monitoring (section 8).
4. Several integrations that cannot work as wired today (3CX webhooks) (section 5).
5. Weak password storage (unsalted SHA-1) and a placeholder encryption key.

Fixing the P0 items in section 3 should come before any other work.

---

## 2. What is working today

| Area | State | Notes | Main files |
|---|---|---|---|
| Login, logout, forgot and reset password | Working | New shared design. Single active session per user. 5 minute idle logout. | `controllers/ci_admin.php`, `views/manager/loginpage.php`, `forgot.php`, `reset.php`, `assets/dist/css/crm-auth.css` |
| Top navigation | Working | Role based menus. LIVE badge overlap fixed. Kept as top navigation on purpose. | `views/manager/elements/header.php`, `assets/dist/css/crm-theme.css` |
| Dashboard | Working | Original approved colors restored. KPI cards, Target vs Achievement and Target vs Projection charts, My Leads and My Orders donuts, Operational Activity. | `views/manager/dashboard.php`, `models/ci_admin_model.php` |
| My Orders donut data | Working | Now counts the same orders as the My Orders page, including Pending Sales Approval and Follow Up. | `ci_admin_model::dashboard_myorder_donut_data`, `my_orders_scope_sql` |
| Add Database / View Database edit | Working | Manual Dialer fields shown first, all other fields in Additional Details. No fields removed. | `views/manager/leads/add-database.php`, `editleads.php`, `assets/dist/js/crm-lead-form.js` |
| View Database list, import, export, transfer | Loads | Pages load. Import and transfer not tested end to end. | `controllers/ci_admin_leads.php` |
| Lead preview access control | Working (code) | Owner, collaborator or back office only. | `ci_admin_leads::view` |
| Orders: My Orders, Open, Follow-up, Completed, Closed, Sales | Loads | Tables load with real data. | `controllers/ci_admin_orders.php`, `views/manager/orders/index.php` |
| Add Follow-Up for orders | Working | Now a full page, not a popup. Saves follow-up, meeting and history. | `ci_admin_orders::add_followup`, `views/manager/orders/add_followup.php` |
| Dialer: auto and manual dialing UI | Working (UI) | Custom start and end record, skip already called records, resume position. Manual Dialer is one compact card. | `controllers/ci_admin_calls.php`, `views/manager/calls/dashboard.php` |
| Dialer campaigns | Working (UI and data) | Create, edit, list, details, filters, participants, progress. Calls get a campaign tag. Not tested with real 3CX calls. | `models/dialer_campaign_model.php`, `ci_admin_calls::ajax_campaigns`, `ajax_save_campaign` |
| Profile page | Working | Opens from the avatar. Attendance, shifts, leave, events read real data. | `views/manager/setting/profile.php`, `ci_admin_model::hr_*` |
| Roles and permissions | Working (code) | Controller checks admin or `roles` permission in the constructor. | `controllers/ci_admin_roles.php` |
| KAM, tasks, tickets, meetings, verticals, reports, system pages | Load | All nav pages open without PHP errors for admin. Workflows not tested step by step. | various controllers |

---

## 3. Security and data protection (task list)

### SEC-1. Full database dump is in git and can be downloaded from the web
- **State**: `database/localhost.sql` (3.4 MB) is tracked in git and pushed to GitHub. It contains real CRM data, including users and password hashes. On Apache, `.htaccess` only rewrites missing files, so any existing file is served. Locally `GET /database/localhost.sql` returned HTTP 200 with the full file.
- **Problem**: anyone who can reach the server or the GitHub repo can read all customer, lead and user data.
- **Fix**: remove the dump from the repo and from git history, keep a schema-only file without data, rotate all user passwords and any API keys that were inside it, and block web access to `database/`, `*.sql`, `*.md`, `.env*`, `application/`, `system/` in `.htaccess` (or move the web root to a `public/` folder).
- **Priority**: P0
- **Files**: `database/localhost.sql`, `.htaccess`

### SEC-2. User status can be changed without logging in
- **State**: `Ci_admin_user::ajax_edit_status` has no login or role check. The controller constructor does not call `if_not_login()`. CSRF protection is on, but any visitor can get a valid CSRF token from the login page.
- **Problem**: an outsider can enable or disable any user account.
- **Fix**: call `if_not_login()` (admin only) in the constructor of `Ci_admin_user`, or at the start of every public method. Also check `get_state` and `check_name`.
- **Priority**: P0
- **Files**: `application/controllers/ci_admin_user.php`

### SEC-3. WhatsApp send endpoint is public
- **State**: `Ci_api::send_whatsapp` answers requests without a login (tested: HTTP 200 without a session).
- **Problem**: if a WhatsApp token is configured, anyone can send messages from the company number.
- **Fix**: require a logged-in user with the right permission, or a signed server-to-server key. Remove the hardcoded fallback verify token `crm_whatsapp_secret` in `whatsapp_webhook`.
- **Priority**: P0
- **Files**: `application/controllers/ci_api.php`

### SEC-4. Other actions without a login check
- **State**: these public methods have no login check in their body or constructor: `ci_admin_leads`: `view_leads`, `transfer_to_lead`, `ajax_get_state`, `ajax_search_companies`; `ci_admin_team`: `index`, `orders`, `sales`; `ci_admin_tasks`: `index`, `compute_occurrences`; `ci_admin_report`: `adminReports`; `ci_admin_terms`: `getSubCatAjax`; `ci_admin`: `index`, `reset`, `ping`, `sitemap_xml`; `ci_cron`: all methods.
- **Problem**: some of these only redirect or call another guarded method, but this was not verified for each one. `ajax_search_companies` may return company names to anyone. `ci_cron/run` can be triggered by anyone (tested: HTTP 200).
- **Fix**: add a base controller (for example `MY_Controller`) that checks login and permissions once for every manager controller. Allow cron only from the command line (`is_cli()`) or with a secret key.
- **Priority**: P0 for `transfer_to_lead` and `ajax_search_companies` after checking them, P1 for the rest
- **Files**: listed controllers, `application/core/` (new base controller)

### SEC-5. Weak password storage
- **State**: passwords are stored as plain SHA-1 with no salt (`sha1($password)` in login, reset and user forms).
- **Problem**: leaked hashes can be cracked quickly.
- **Fix**: switch to `password_hash()` and `password_verify()`. Upgrade each user's hash on their next successful login.
- **Priority**: P0
- **Files**: `controllers/ci_admin.php`, `controllers/ci_admin_user.php`, `controllers/ci_admin_setting.php`

### SEC-6. Placeholder encryption key and insecure cookie settings
- **State**: `encryption_key = 'abcdef123456'`, `cookie_secure = FALSE`, `log_threshold = 0` (no logs).
- **Problem**: sessions and encrypted values are easy to forge, cookies can travel over HTTP, and errors are not logged.
- **Fix**: read a long random key from `.env`, set `cookie_secure` and `cookie_httponly` to TRUE in production, enable error logging to a private folder.
- **Priority**: P0 (key), P1 (cookies, logs)
- **Files**: `application/config/config.php`, `.env.example`

### SEC-7. API keys stored as plain text in the database
- **State**: Groq, 3CX, WhatsApp, SMTP settings are saved in `ci_integration_settings` as plain text.
- **Problem**: anyone with database access (or the dump in SEC-1) gets all third-party keys.
- **Fix**: encrypt secrets at rest with a key from `.env`, never send full keys back to the browser, show only the last 4 characters.
- **Priority**: P1
- **Files**: `controllers/ci_admin_integrations.php`, `views/manager/system/integrations.php`

### SEC-8. Mass assignment through `extract($_POST)`
- **State**: about 19 places call `extract($_POST)` (leads, meetings, roles, settings, terms, users, verticals, login).
- **Problem**: any posted field becomes a PHP variable. This makes it easy to overwrite variables by mistake and hard to review.
- **Fix**: read only the expected fields with `$this->input->post('field', true)` and validate them.
- **Priority**: P1
- **Files**: `ci_admin_leads.php`, `ci_admin_meetings.php`, `ci_admin_roles.php`, `ci_admin_setting.php`, `ci_admin_terms.php`, `ci_admin_user.php`, `ci_admin_verticals.php`, `ci_admin.php`

### SEC-9. SQL built by string concatenation
- **State**: many queries put values directly into SQL strings (for example `"... WHERE lead_assign = {$manager_id}"`). Most values are cast to int first, but not all were checked.
- **Problem**: any missed cast is an SQL injection risk.
- **Fix**: use query bindings (`$this->db->query($sql, $params)`) or the query builder everywhere. Start with any query that uses GET or POST values.
- **Priority**: P1
- **Files**: `models/ci_admin_model.php`, most controllers

### SEC-10. Dialer and KAM scope for admins and KAM role
- **State**: in the dialer, users with role 1 (Admin) or 4 (KAM) who have no allotted leads see all leads with phone numbers.
- **Problem**: this may be intended for admins, but KAM users may see leads that belong to other salespeople.
- **Fix**: confirm the business rule with the owner and limit role 4 to their own and their team's leads if needed.
- **Priority**: P1
- **Files**: `ci_admin_calls::_apply_lead_filters`, `ajax_get_queue`, `ajax_get_lead`

### SEC-11. Third-party link in the Reports menu
- **State**: Reports menu has "Generate Data" linking to `https://7strokes.maxvendor47.workers.dev/`.
- **Problem**: an unknown external tool inside an enterprise CRM. Unclear who controls it and what data is sent there.
- **Fix**: confirm ownership. Remove it or move it into the product with proper access control.
- **Priority**: P1
- **Files**: `views/manager/elements/header.php`

### SEC-12. Upload checks
- **State**: avatar upload checks the file extension only.
- **Problem**: extension checks alone are weak.
- **Fix**: check MIME type and image size with `getimagesize()`, rename files randomly, block script execution in `assets/uploads/` with `.htaccess`.
- **Priority**: P2
- **Files**: `controllers/ci_admin.php` (avatar upload)

---

## 4. Broken features (task list)

### BUG-1. My Zone > View Leads page fails with a database error
- **State**: Broken. `GET /manager/leads/view_leads` returns HTTP 500: `Unknown column 'P.order_ref_no' in 'where clause'`.
- **Problem**: the query filters on `ci_pipeline.order_ref_no`, but no code or migration creates this column.
- **Fix**: either add the column with a migration and fill it when an order is created, or remove the filter and join `ci_orders` on `lead_id` to exclude converted leads.
- **Priority**: P0
- **Files**: `models/ci_admin_model.php` (around line 1101), `controllers/ci_admin_leads.php`

### BUG-2. 3CX call record and recording webhooks cannot receive data
- **State**: Broken. `POST /api/calls/cdr_webhook` redirects to the login page (HTTP 302). The webhooks live in `Ci_admin_calls`, whose constructor requires a logged-in user. They would also be blocked by CSRF.
- **Problem**: call duration, recordings, transcription and AI summaries from 3CX can never arrive automatically.
- **Fix**: move `cdr_webhook` and `recording_webhook` into a separate public controller, add them to `csrf_exclude_uris`, and protect them with a shared secret or signature from 3CX.
- **Priority**: P1
- **Files**: `controllers/ci_admin_calls.php`, `config/config.php`, `config/routes.php`

### BUG-3. Inbound WhatsApp webhook is likely blocked by CSRF
- **State**: Built, not verified. Meta sends POST requests without a CSRF token and there is no CSRF exclusion.
- **Problem**: inbound WhatsApp messages probably never get saved.
- **Fix**: add the webhook URI to `csrf_exclude_uris` and verify Meta's `X-Hub-Signature-256` header instead.
- **Priority**: P1
- **Files**: `controllers/ci_api.php`, `config/config.php`

### BUG-4. Schema created at runtime
- **State**: several models create tables or columns while handling page requests (`ALTER TABLE` in `dashboard_countdata`, `log_admin_action`, campaign tables in `dialer_campaign_model`).
- **Problem**: the database can differ between servers (BUG-1 is an example). Slow pages and failed requests can happen during schema changes.
- **Fix**: move all schema changes into versioned migration files run by `migration_runner.php`. Remove runtime `ALTER TABLE` calls.
- **Priority**: P1
- **Files**: `models/ci_admin_model.php`, `models/dialer_campaign_model.php`, `migration_runner.php`

---

## 5. Integrations (task list)

| ID | Integration | State | What to do | Priority |
|---|---|---|---|---|
| INT-1 | 3CX click to call | Built, not verified | Test with a real PBX. Confirm call log is created and linked to lead and campaign. | P1 |
| INT-2 | 3CX CDR and recordings | Broken (BUG-2) | Fix routing and auth. | P1 |
| INT-3 | Groq transcription and AI summary | Built, not verified | Depends on INT-2. Add retry limits and cost controls. | P2 |
| INT-4 | WhatsApp send and receive | Built, not verified, unsafe (SEC-3, BUG-3) | Secure and test. | P0 / P1 |
| INT-5 | SMTP email | Built, not verified | Test reset password email end to end. | P1 |
| INT-6 | Cron jobs | Built, public (SEC-4) | Protect, document the cron schedule for cPanel. | P1 |

---

## 6. Placeholders and fake data (task list)

### FAKE-1. HR Search Topics shows fixed policy text
- **State**: Placeholder. `Ci_admin_hr::search_topics` uses a hardcoded list of policy topics and summaries.
- **Problem**: users may believe these are real company policies.
- **Fix**: create a `ci_hr_topics` table with admin editing, or hide the page until real content exists.
- **Priority**: P1
- **Files**: `controllers/ci_admin_hr.php` (around line 1049), `views/manager/hr/search_topics.php`

### FAKE-2. Training has no data source
- **State**: the old fake modules were removed today. The page now shows "No training modules have been assigned to you yet." There is no training table.
- **Problem**: Training is an empty feature.
- **Fix**: design training tables (modules, assignments, progress) and admin screens, or hide the menu item.
- **Priority**: P2
- **Files**: `controllers/ci_admin_hr.php::training`, `views/manager/hr/training.php`, `views/manager/setting/profile.php`

### FAKE-3. Events and Plans
- **State**: now reads `ci_events` (was hardcoded before today). The local database has 0 events, so the page is empty.
- **Problem**: none in code. Needs data and a way to add events from HR.
- **Fix**: confirm the events admin screen (`manager/events`) is the place to add them, and link it from HR.
- **Priority**: P3

### FAKE-4. Leave balances
- **State**: Profile now shows real leave taken and pending requests. There is no leave entitlement data (for example yearly days allowed), so "remaining days" cannot be shown.
- **Fix**: add leave policy per user or role, then calculate the remaining balance.
- **Priority**: P2

No other hardcoded demo data was found in controllers. Random IDs (`mt_rand`) are used for ticket and task numbers, which is fine but can collide; use database sequences instead (P3).

---

## 7. UI and UX (task list)

### UI-1. Dashboard colors are intentionally different from the rest of the CRM
- **State**: Working. The dashboard keeps the boss-approved colors. The rest of the CRM uses the Ocean Blue theme in `crm-theme.css`. The theme skips anything inside `.full-screen-dash`.
- **Fix**: none now. Keep this rule when changing styles.
- **Priority**: info

### UI-2. Many views still use inline styles
- **State**: most pages set colors, padding and fonts with `style="..."`.
- **Problem**: hard to keep consistent and hard to theme.
- **Fix**: move repeated inline styles into shared CSS classes page by page.
- **Priority**: P2
- **Files**: all of `application/views/manager/`

### UI-3. Wide tables on phones
- **State**: tables use horizontal scrolling on Android.
- **Problem**: works, but long tables are hard to use on a phone.
- **Fix**: add card-style rows for key tables (orders, leads, follow-ups) on small screens.
- **Priority**: P2

### UI-4. Collapsed sections hide required fields
- **State**: Working. Add Database and the View Database edit form collapse secondary sections. If a required field inside is empty, the section opens automatically on submit.
- **Fix**: none. Re-test after any form change.
- **Priority**: info

### UI-5. Navigation stays on top
- **State**: top navigation kept on purpose. A sidebar is planned for a later phase.
- **Fix**: none now.
- **Priority**: P3

### UI-6. Accessibility
- **State**: not audited in depth. Many icon-only buttons have only a `title`.
- **Fix**: add `aria-label`s, check color contrast, and make sure every flow works with the keyboard.
- **Priority**: P2

---

## 8. Engineering and operations (task list)

| ID | Item | State | Fix | Priority |
|---|---|---|---|---|
| ENG-1 | Automated tests | None | Add PHPUnit for models and controllers, plus browser tests for login, add lead, convert to order, dial. | P1 |
| ENG-2 | Database migrations | Partial (see BUG-4) | Versioned migrations only. | P1 |
| ENG-3 | Error logging and monitoring | Logging off (`log_threshold = 0`) | Enable logs, add uptime and error alerts. | P1 |
| ENG-4 | Backups | Not in code | Daily encrypted backups with restore tests. | P1 |
| ENG-5 | Framework | CodeIgniter 3 (end of life) | Plan an upgrade path (CodeIgniter 4 or Laravel). Not urgent, but needed for long-term security support. | P2 |
| ENG-6 | Audit log | Exists (`ci_admin_logs`) | Make it read-only for non-admins and cover all data changes. | P2 |
| ENG-7 | Rate limiting | None | Limit login attempts and API calls per IP and user. | P1 |
| ENG-8 | Two-factor login | None | Add TOTP based 2FA, required for admins. | P1 |
| ENG-9 | Docs in repo | Several notes files (`audit.md`, `current_work.md`, `value.md`) are served publicly | Move to a non-public folder (see SEC-1). | P1 |

---

## 9. Enterprise SaaS readiness (target USD 1,250,000 per year)

| Requirement | Today | Gap |
|---|---|---|
| Multi-tenant (many client companies, data fully separated) | No. One company per install. | Add tenant ID to every table and every query, or one database per tenant. Large project. |
| Single sign-on (SAML, Microsoft Entra ID, Google) | No | Needed by enterprise buyers. |
| Strong authentication (2FA, password policy, lockout) | No | ENG-8, ENG-7, SEC-5 |
| Encryption of sensitive data at rest | No | SEC-7, plus database or disk encryption |
| Security certifications (SOC 2, ISO 27001) | No | Needs policies, logging, access reviews, pen tests |
| Data export and deletion per customer (GDPR, UAE PDPL) | Partial (exports exist) | Add per-record deletion and data retention rules |
| Public API with keys and documentation | No (only internal AJAX) | Versioned REST API with scoped tokens |
| Uptime, backups, disaster recovery | Not defined | ENG-3, ENG-4, hosting beyond shared cPanel |
| Automated tests and release process | No | ENG-1, CI pipeline |
| Configurable workflows, fields and roles per customer | Partly (roles yes, fields no) | Admin screens for custom fields and pipelines |
| Reporting and analytics | Basic reports and dashboard | Scheduled reports, export to BI tools |
| Mobile | Responsive web only | Optional native or PWA app |

**Verdict**: today the product is a working internal CRM for one company. It is not ready to be sold at enterprise SaaS level. Fixing section 3 and section 4 is the first step. Multi-tenancy, SSO, 2FA, tests and compliance are the main longer-term projects.

---

## 10. Recommended order of work

1. SEC-1, SEC-2, SEC-3, SEC-5, SEC-6 key, BUG-1 (P0).
2. Base controller with login and permission checks for all manager controllers (SEC-4).
3. 3CX and WhatsApp webhooks (BUG-2, BUG-3), then test integrations (section 5).
4. Migrations, logging, backups, tests (BUG-4, ENG-1 to ENG-4).
5. Placeholders (FAKE-1, FAKE-2), then UI cleanup (section 7).
6. Enterprise roadmap (section 9).
