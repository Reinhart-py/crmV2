# Changelog

All notable changes, features, bug fixes, and refinements for the Rolexto CRM repository are documented in this file.

---

### [Commit 36b3231] — Ticket Section Overhaul, Multi-User Assignment, Support Review & Close Gate, Audit History, Multiple Follow-ups, and Single-Device Login Enforcement
- **Commit Hash**: `36b3231` (`36b32313aa60f45d26cc6d9c5868e005b972f3a3`)
- **Tree Code**: `7449fd190bdb96776acdef13c60833d3c9532f01`
- **Author**: Reinhart-py
- **Date & Time**: `2026-10-15 17:26:01 +0530`

#### Changes & Additions:
- **Comprehensive Ticket Section Architecture**:
  - **Client Type**: Added `Onboarded` and `Non-Onboarded` selector with color-coded badges in ticket lists and detail views.
  - **Company Dropdown**: Integrated live company dropdown populated dynamically from all unique companies available in the CRM database (`ci_leads.customer`) with searchable select2 typeahead and custom entry support.
  - **Multi-Role Ticket Raising**: Any authorized user from Sales, Back Office, Operations, IT, HR, Support, and other departments can raise tickets.
  - **Department & Categorization**: Added configurable Department (`Sales`, `Back Office`, `Operations`, `IT`, `HR`, `Support`, `Accounts`), Ticket Type (`Bug / Technical Issue`, `Feature Request`, `System Access / Permission`, `Billing / Invoice Inquiry`, `Client Query / Onboarding`, `Operational Support`, `Hardware / Equipment`, `General Inquiry`, `Escalation`), and Category dropdowns.
  - **Priority & Status**: Configured Priority (`Low`, `Medium`, `High`, `Urgent`) and extended Ticket Statuses (`Open`, `Assigned`, `In Progress`, `Pending`, `On Hold`, `Resolved`, `Closed`).
  - **Multi-User Assignment**: Enabled assignment of one ticket to multiple staff members simultaneously via comma-separated storage (`assigned_users`) with interactive user badge chips and individual user filtering.
  - **Notify / Tag Users**: Implemented user tagging option (`tagged_users`) to alert relevant colleagues about tickets.
  - **Deadlines & Overdue Detection**: Added `deadline_date` (when action must be taken) and `expected_close_date`. Highlighted overdue tickets with prominent red `<span class="label label-danger">OVERDUE</span>` badges and amber `<span class="label label-warning">Due Soon</span>` badges for tickets approaching deadlines (&le; 48 hours).
  - **Full Tracking & Audit Fields**: Persisted `raised_by`, `created_date`, `last_updated_date`, and `last_updated_by`.
- **Support-Only Resolution Review & Close Gate**:
  - Assigned users and staff can update status to `Open`, `Assigned`, `In Progress`, `Pending`, `On Hold`, or `Resolved`, record progress remarks, and add follow-ups.
  - **Ticket closure is strictly restricted to users with the Support role (or Admin)**.
  - When closing a ticket, the system requires Support personnel to provide resolution/closing remarks and check a mandatory review confirmation checkbox (`confirm_review = 1`), verifying that the resolution has been manually reviewed and verified with the client/department.
  - Logged `resolution_reviewed_by` and `resolution_confirmed_at` timestamps.
- **Complete Ticket History Audit Log**:
  - Created `ci_ticket_history` database table to track every event in the ticket lifecycle: creation, field edits, reassignments, status transitions (`old_status` &rarr; `new_status`), progress remarks, and resolution closures with exact timestamps, user names, and role identifiers.
- **Multiple Follow-ups Storage**:
  - Created `ci_ticket_followups` database table supporting unlimited sequential follow-up entries per ticket (`followup_date`, `next_followup_date`, `assigned_user_id`, `remarks`, `status`, `next_action`) without overwriting previous records.
  - Updated `manager/tickets/followups` and `manager/tickets/view` to display complete follow-up chronologies connected directly to database records.
- **Attachment & Internal Notes**:
  - Integrated file attachment uploads stored in `assets/admin/tickets/` with download buttons, confidential `internal_notes` for internal staff discussion, and resolution summaries.
- **Top Metrics Cards & Comprehensive Filters**:
  - Added summary KPI cards at the top of the ticket list: Total Tickets, Open & Assigned, In Progress & Pending, Overdue Tickets (in red), Resolved, and Closed.
  - Filter by Client Type, Company, Ticket Type, Category, Status, Priority, Assigned User, Raised By, Department, Overdue toggle, Date Range, and Keyword search.
- **Strict Separation of Tasks and Tickets**:
  - Separated `Task` and `Ticket` in both desktop navigation bar and Android mobile drawer into dedicated, independent top-level menus.
- **Single-Device Login Enforcement & IP Tracking**:
  - Enforced single-device login per user account by saving a unique `current_session_token` upon login. Active session token verification runs on every request (`ci_admin_model::check_auth_timeout`). If a user logs in from another device or browser, previous active sessions are immediately terminated with a redirect to `/manager/login?conflict=1`.
  - Extracted real client IP across reverse proxies and Cloudflare via `get_client_ip()` (`HTTP_CF_CONNECTING_IP`, `HTTP_X_FORWARDED_FOR`, `HTTP_CLIENT_IP`, `REMOTE_ADDR`) and logged with all admin actions.
  - Integrated live Online/Offline status indicators in `admin_logs.php` with session overview metrics and online/offline status filter.

---

### [Commit 253d0e3] — Consolidate Alternative Contact Fields and Standardize DNCR/DND & DNCR Date Terminology
- **Commit Hash**: `253d0e3` (`253d0e357691269ed6b4e04be867f1318a87af0a`)
- **Tree Code**: `d4e536115aaa36f6ac883d6e5fc1ee72bc25b765`
- **Author**: Reinhart-py
- **Date & Time**: `2026-10-15 16:20:57 +0530`

#### Changes & Additions:
- **Consolidated Alternative Contact Number**:
  - Removed duplicate `alt_mobile` ("Alternative Mobile Number") field from lead creation and edit forms.
  - Retained a single unified field: **"Alternative Contact Number"** (`name="alt_contact"`) in both `application/views/manager/leads/addleads.php` and `application/views/manager/leads/editleads.php`.
  - In `application/controllers/ci_admin_leads.php`, synchronized both `alt_contact` and legacy `alt_mobile` database columns on `add()` and `edit()` to preserve full backward compatibility with historical records and database queries.
  - In `application/controllers/ci_admin_leads.php::import_action()`, updated CSV import to map aliases (`alternativecontactnumber`, `altcontact`, `alternatecontact`, `alternativemobilenumber`, `altmobile`, `alternatemobile`) into `alt_contact` and `alt_mobile`.
  - In `download_sample_csv()`, removed `'Alternative Mobile Number'` and retained `'Alternative Contact Number'` with updated sample data.
- **Standardized DNCR/DND and DNCR Date Terminology**:
  - Replaced all legacy `TNCR/TND` and `TNCR Check Date` / `DNCR / DND Check Date` labels across the system with strictly **"DNCR/DND"** and **"DNCR Date"**.
  - Updated `addleads.php`, `editleads.php`, and `single_view.php` to display **DNCR/DND** and **DNCR Date** uniformly.
  - Updated CSV sample download headers (`download_sample_csv()`) and CSV import mapping (`import_action()`) to recognize `dncrdnd`, `dncr`, and `dncrdate`.
- **Lead Single View Details Expansion**:
  - Enhanced `application/views/manager/leads/single_view.php` to prominently display **Alternative Contact Number**, **Email**, **DNCR/DND**, and **DNCR Date** in the customer details table.

---

### [Commit 317e3b2] — Admin Logs UI Matched to Team Leads, Dashboard Cleanup, Routing Fix, and Multi-Platform Hosting Documentation
- **Commit Hash**: `317e3b2` (`317e3b20732e6ef419f7d4d7f046e492fc1660f2`)
- **Tree Code**: `45fbe2f1e18ec3346d03caee198fe190b3aa2c65`
- **Author**: Reinhart-py
- **Date & Time**: `2026-10-15 16:03:10 +0530`

#### Changes & Additions:
- **Admin Logs Page UI & Options Matched to `Teams > Leads`**:
  - Re-architected `application/views/manager/system/admin_logs.php` to replicate the exact UI, styling, and options of `views/manager/team/leads.php`.
  - Added matching top filter toolbar (`.head_filter`) with:
    - Date range (`Create From` to `Create To`) using `.input-daterange input-group` and `.datepicker`.
    - Multi-select dropdown for `activity[]` (`login`, `logout`, `lead_create`, `lead_update`, `lead_delete`, etc.).
    - Multi-select dropdown for `status[]` (`SUCCESS`, `FAILED`).
    - Standard `.select2` user dropdown populated with all users from `ci_user`.
    - Text search input for instant `user_name` matching.
    - Standard CRM `Search` (`btn-success`) and `Reset` (`btn-danger`) buttons.
  - Implemented matching DataTables layout (`#example_lead`) with:
    - Columns: `ID`, `User`, `Activity`, `Status`, `IP Address`, `Logs`, and `Date & Time`.
    - Hidden chronological timestamp (`span.no_show`) for accurate date/time sorting.
    - Dynamic column footer filter dropdowns (`initComplete`) on User, Activity, and Status columns.
  - Removed all arbitrary inline rainbow badge colors, custom red card borders, and dark pills in favor of standard AdminLTE/CRM theme styles.
  - Updated `application/controllers/ci_admin_system.php` to handle multi-filtering by `user_id`, `user_name`, `activity[]`, `status[]`, and date range (`d1`, `d2`).
- **Dashboard Cleanup & Operational Metrics Alignment**:
  - Reverted operational metrics rows in `application/views/manager/dashboard.php` back to clean 4-card grids (`col-sm-3 col-xs-12`) for both personal and team metrics, eliminating visual crowding from dummy tasks.
  - Restored "My Lead Stats" and "Team Lead Stats" as compact `.crm-modern-stat-card` elements directly above the Projections charts with real status chips and counts from the database (`$dashboard_lead`, `$dashboard_lead_team`).
- **PHP Built-in Server Router**:
  - Updated `index.php` to act as a router for PHP CLI development server (`php -S localhost:8080 index.php`), resolving direct controller routing for `/manager/login` and `/manager/dashboard` without 404s.
- **Git & Environment Cleanliness**:
  - Added `cookie.txt` and temporary session files to `.gitignore`.
- **Clutter-Free Hosting & Architecture Guide**:
  - Completely redesigned `README.md` in the clean, structured style of `alichtman/shallow-backup#readme`.
  - Documented complete deployment instructions for Local (PHP CLI & XAMPP), cPanel, Linux/VPS, Cloudflare Tunnel, and Netlify, detailing where data is stored and where to check logs/status on each platform.

---

### [Commit b49a421] — Comprehensive CHANGELOG.md Documentation
- **Commit Hash**: `b49a421` (`b49a42197c4a8069e6a1a7b7243777edbdb46cb3`)
- **Tree Code**: `2f3edf64450fd9dd0325b298a3801326f0f8f285`
- **Author**: Reinhart-py
- **Date & Time**: `2026-10-14 11:46:07 +0530`

#### Changes & Additions:
- Created repository `CHANGELOG.md` detailing every commit, commit hash, tree code, author, date & time, and practical changes made from initial commit to present.

---

### [Commit aeb1858] — Tickets Action Workflow, Dedicated Ticket View, Unassigned Leads Queue, Support Role Permissions, Android Mobile Drawer Fix, and Docs Update
- **Commit Hash**: `aeb1858` (`aeb18583f28dc056ee32719db793ab1cda994e6c`)
- **Tree Code**: `5acf6a0dcfba3e1e8e45cd2fdd04d0a298d2b2cb`
- **Author**: Reinhart-py
- **Date & Time**: `2026-10-14 11:44:34 +0530`

#### Changes & Additions:
- **Dedicated Ticket View & Conversation Page**:
  - Created `application/views/manager/tickets/view.php` providing a dedicated full-page discussion thread for each customer support ticket (`manager/tickets/view/{id}`).
  - Displayed complete message histories, ticket details, client information, and a dedicated response box (`#reply-box`).
- **Standardized Ticket Action Dropdowns**:
  - Replaced plain text links on `application/views/manager/tickets/index.php` with a unified split `Action` button.
  - Implemented dropdown actions: **View Ticket**, **Reply**, **Mark In Progress**, **Clear / Mark Resolved**, **Mark as Closed**, **Re-Open Ticket**, and **Delete Ticket** (strictly restricted to Admin users).
  - Added matching action dropdown on the dedicated ticket view header.
- **Backend Ticket Status & Lifecycle Endpoints**:
  - Implemented `status($id, $target_status)` and `delete($id)` controller methods in `application/controllers/ci_admin_tickets.php`.
  - Registered clean URL routes in `application/config/routes.php` (`manager/tickets/status/(:num)/(:any)` and `manager/tickets/delete/(:num)`).
- **Navigation Bar Cleanup (Removed Help Desk from Top Navbar)**:
  - Removed "Help Desk" menu item from the top horizontal navigation pill bar (`ul.munk_menu`) and the mobile sidebar menu in `application/views/manager/elements/header.php`.
  - Retained quick, unobtrusive 1-click access via the persistent floating help icon (`#floatingHelpFab`) at the bottom-left corner of the screen.
- **Android & Mobile Navigation Fixes**:
  - Diagnosed and resolved the issue where tapping the 3-line hamburger button on Android failed to open the sidebar.
  - Removed duplicate capture event listener in `header.php` that was simultaneously toggling `sidebar-open` on and off within milliseconds.
  - Standardized the mobile drawer controller in `application/views/manager/elements/footer.php` with `e.stopImmediatePropagation()`.
  - Added an explicit touch-friendly close button (`#crmSidebarClose`) with the 7 Strokes logo inside the mobile drawer.
  - Added direct inline style toggling (`display: block` / `none`) for treeview submenus (Leads, Verticals, Team, System, Profile) to guarantee smooth accordion expansion on all mobile devices.
  - Expanded CSS responsive breakpoints in `assets/dist/css/crm-dashboard.css` to `991px` to support foldables, tablets, and high-DPI Android screens.
- **Unassigned Leads Queue & Support Role Permissions**:
  - Created dedicated unassigned leads filtering and assignment queue in `application/views/manager/team/assignleads.php`.
  - Granted Support role (`role_id == 4`) access to assign leads in `application/controllers/ci_admin_team.php`.
  - Allowed Support role to import leads (`manager/leads/import`) while restricting export (`manager/leads/export`) strictly to Administrators.
- **Documentation & README Cleanup**:
  - Rewrote `README.md` to include a direct, practical executive summary paragraph.
  - Added latest commands (`php -S 127.0.0.1:8080`) and eliminated all obsolete `preview.html` and static preview mentions.

---

### [Commit 5f9db40] — 7 Strokes Rebranding, Monochrome Badges, Unified Tight Footer, Android Mobile 3-Line Hamburger Bar, and Dark Mode Visibility
- **Commit Hash**: `5f9db40` (`5f9db4007f9933748e32df3fd428a47b6de95d83`)
- **Tree Code**: `df1dc7ca4d6d1329d9ac82faeacc50c0ebd13b41`
- **Author**: Reinhart-py
- **Date & Time**: `2026-10-13 15:25:30 +0530`

#### Changes & Additions:
- Replaced legacy Rolexto branding across the CRM with official **7 Strokes** high-resolution logos (`assets/7strokes-logo.png`, `assets/7strokes-logo-dark.png`, `assets/logo-7strokes.png`).
- Updated login, reset password, and forgot password pages (`loginpage.php`, `forgot.php`, `reset.php`) with modern 7 Strokes branding, high-contrast inputs, and crisp typography.
- Built Android mobile 3-line hamburger bar (`#crmMobileHamburger`) visible exclusively on mobile viewports.
- Added comprehensive dark mode CSS tokens and styling across tables, modals, cards, badges, and input elements in `assets/dist/css/crm-dashboard.css`.
- Replaced multicolored table status badges with a modern monochrome design system.
- Unified the application footer layout in `application/views/manager/elements/footer.php` with a compact, modern design.

---

### [Commit e594ac5] — Trade License Fields, Local Database Connection, and Close Date Constraint
- **Commit Hash**: `e594ac5` (`e594ac506ea2e9439e4930e8c4846becd894a5d3`)
- **Tree Code**: `03b07829c80f67750f1786146210773a07d95ec0`
- **Author**: Reinhart-py
- **Date & Time**: `2026-10-13 14:35:18 +0530`

#### Changes & Additions:
- Added Trade License metadata fields across lead forms: `addleads.php`, `editleads.php`, and `single_view.php`.
- Enforced client-side and server-side date validation ensuring Expected Close Date is greater than or equal to Follow-up Date.
- Configured local database connection in `application/config/database.php` for local testing.
- Added database seed file `database/localhost.sql`.

---

### [Commit 2ce9bc3] — Silence PHP 8 Warnings, Remove Sidebar-Toggle Icon, Align Logo with Navbar, Move Contact to Footer
- **Commit Hash**: `2ce9bc3` (`2ce9bc3e4df14179d0ade0626aa01e9e49a43c55`)
- **Tree Code**: `0b457a4cb3790092998db479deb8ad2aa8ecfbe8`
- **Author**: Reinhart-py
- **Date & Time**: `2026-10-13 12:50:49 +0530`

#### Changes & Additions:
- Silenced PHP 8 dynamic property deprecation warnings and strict type notices across views and index.php.
- Removed legacy AdminLTE sidebar toggle button (`.sidebar-toggle`) on desktop to maintain clean pill navbar appearance.
- Aligned brand logo vertically with the floating navigation pill container.
- Moved header support contact information into the consolidated footer.

---

### [Commit 25ef063] — Remove 111px Top Gap, Support Dark Mode Header Background, Pull Navbar Up
- **Commit Hash**: `25ef063` (`25ef0638b86e129618e1f880b9646c6b4fbe4592`)
- **Tree Code**: `657018901b91dba186806a1b0ac6cfcc868923b0`
- **Author**: Reinhart-py
- **Date & Time**: `2026-10-13 12:39:59 +0530`

#### Changes & Additions:
- Eliminated redundant 111px empty whitespace at the top of the viewport in `crm-dashboard.css` and `AdminLTE.min.css`.
- Fixed header background color blending in dark mode.
- Adjusted floating navbar positioning with compact top margins for optimal vertical screen space.

---

### [Commit b7f313d] — Prevent Menu Bubble Wrapping on Android, Proportionally Shrink Pill Bar
- **Commit Hash**: `b7f313d` (`b7f313d2719428eed7747672423371b55f892604`)
- **Tree Code**: `ae1fd8b2d359c37c33cce8afb473ec634b4f69f5`
- **Author**: Reinhart-py
- **Date & Time**: `2026-10-13 12:26:12 +0530`

#### Changes & Additions:
- Resolved horizontal menu bubble wrapping issues on Android screens.
- Added media query rules to scale navigation pill items proportionally on narrower viewports.

---

### [Commit 22e11b9] — Pin Logo to Top-Left Corner, Contact to Top-Right Corner, and Pull Centered Navbar Up
- **Commit Hash**: `22e11b9` (`22e11b9669816541c301be4121623f5ba9b12bb0`)
- **Tree Code**: `3c813614c952143f6bf2497f5ec19d61e42d51d5`
- **Author**: Reinhart-py
- **Date & Time**: `2026-10-13 12:19:05 +0530`

#### Changes & Additions:
- Reorganized desktop header elements: pinned brand logo to top-left and quick actions to top-right.
- Horizontally centered the floating pill navbar with clean margins.

---

### [Commit c15a8fc] — Center Floating Navbar, White Light Mode Styling, Reduce Header Gap, and Guard mysqli_result
- **Commit Hash**: `c15a8fc` (`c15a8fcd54eb903eac0cf20174cb6e0b22e62c1e`)
- **Tree Code**: `0ef5382250f043aa4ba96e60f2d0e52168cd2599`
- **Author**: Reinhart-py
- **Date & Time**: `2026-10-13 12:13:13 +0530`

#### Changes & Additions:
- Added clean white styling for light mode dashboard elements.
- Patched CodeIgniter `system/database/DB_driver.php` and `mysqli_result.php` to prevent PHP 8 `mysqli_result` fatal type errors.

---

### [Commit d1b062e] — Eliminate Connection Timeouts and Redundant Retries for Instant Page Loads
- **Commit Hash**: `d1b062e` (`d1b062e5b6198e41908dcb59e7bef7fd0cce4e5f`)
- **Tree Code**: `d8f9a29cbe57777fe945fa773c3fbe2e00ac0a97`
- **Author**: Reinhart-py
- **Date & Time**: `2026-10-13 12:04:42 +0530`

#### Changes & Additions:
- Optimized MySQL connection handling in `system/database/drivers/mysqli/mysqli_driver.php`.
- Removed connection retry loops that caused multi-second page load freezes during failed socket attempts.

---

### [Commit 2c2742e] — Enable Flexible Admin Login Credentials for Local Testing
- **Commit Hash**: `2c2742e` (`2c2742ec454dc8ef8383d2978bdfc91182271d06`)
- **Tree Code**: `74a04e0c727ece21009fc5ec66764c73a1767cdf`
- **Author**: Reinhart-py
- **Date & Time**: `2026-10-13 11:58:08 +0530`

#### Changes & Additions:
- Updated `application/controllers/ci_admin.php` login handler to allow local developer testing.

---

### [Commit 6858521] — Restore Original Logo and Branding, Link All Navbar Routes, and Ensure Zero Changes Outside Dashboard
- **Commit Hash**: `6858521` (`6858521e1cde9215b049576626f1decd0ec399a2`)
- **Tree Code**: `5bd4282c3686c73dc222101a3a9c4a7f68881b7b`
- **Author**: Reinhart-py
- **Date & Time**: `2026-10-13 11:51:57 +0530`

#### Changes & Additions:
- Restored original branding and integrated all existing CodeIgniter routes into the floating navigation bar.
- Added PHP development routing file `router.php`.
- Cleaned up temporary standalone mock files.

---

### [Commit 31c5d86] — Comprehensive README Guide, Standalone Preview SPA, Server Runner, and Netlify Config
- **Commit Hash**: `31c5d86` (`31c5d864302014c09ef63635b9aab1b43e0f96dd`)
- **Tree Code**: `a5bc21b40823b65e3be2c2814ecb62f04d3d8952`
- **Author**: Reinhart-py
- **Date & Time**: `2026-10-13 10:58:15 +0530`

#### Changes & Additions:
- Added initial documentation, preview SPA assets (`preview.html`, `_redirects`, `index.html`), and Python local runner (`server.py`).

---

### [Commit 75c8e80] — Modern Full-Screen Dashboard UI, Pill Navigation Styling, and Operational Drill-Down Views
- **Commit Hash**: `75c8e80` (`75c8e80b161056dbc07040ec3ff9693c28025fb7`)
- **Tree Code**: `a129b6f3675c32c8ede16384d00747304b82ce36`
- **Author**: Reinhart-py
- **Date & Time**: `2026-10-13 10:47:56 +0530`

#### Changes & Additions:
- Created modern full-screen executive dashboard view (`application/views/manager/dashboard.php`).
- Built complete design system and responsive tokens in `assets/dist/css/crm-dashboard.css`.
- Designed floating pill navigation bar in `application/views/manager/elements/header.php`.
- Implemented 5-range operational metrics breakdown and interactive drill-down drawer.

---

### [Commit d5d67fd] — Initial Commit of Original CRM Rolexto Codebase
- **Commit Hash**: `d5d67fd` (`d5d67fd1241815d739a3d49c1d45d4b02c55ddb8`)
- **Tree Code**: `965977e8d1037aeea5fde02ca416d1f33083d1fd`
- **Author**: Reinhart-py
- **Date & Time**: `2026-10-09 15:58:02 +0530`

#### Changes & Additions:
- Initial import of the core CodeIgniter 3 CRM application, modules, database drivers, libraries, helpers, and assets.
