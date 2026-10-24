# Changelog

All notable changes, features, bug fixes, and refinements for the Seven Strokes CRM repository are documented in this file.

### [v1.62.0] - 2026-10-24
- **Timestamp**: `2026-10-24 10:55:00 +0530`
- **Author**: Reinhart-py
- **Summary**: Refine navigation hierarchy (KAM, Review Lead and Orders, Team, My Zone), implement backend & frontend Lead Review and Qualification conversion system with hierarchy permission gating, enforce task creation permissions, and implement 3-letter AJAX company search
- **Detailed Changes & Technical Implementation**:
  - **1. Navigation Structure (`application/views/manager/elements/header.php`)**:
    - **KAM**: Streamlined to contain exclusively three items:
      - `My Client` (`manager/kam/my-client`)
      - `Onboard Client` (`manager/kam/onboard`)
      - `Create Schedule` (`manager/kam/schedule`) (labeled as "Create Schedule").
    - **Back Office**: Navigation section labeled **Back Office** containing exclusively two dropdown items:
      - `Review Lead` (`manager/leads/review_leads`)
      - `Order` (`manager/tasks/external`)
    - **Team**: Kept existing Team navigation and functionality completely unchanged (Members, Chart, Leads, Verticals, Follow up, Meetings, Assigned Meetings, Assign Database, Task, Tickets).
    - **My Zone**: Arranged in the exact 10-item required order:
      1. `Database Add` (`manager/leads/add`)
      2. `Database View` (`manager/leads`)
      3. `Database Follow-Up` (`manager/leads/followups`)
      4. `Database Meetings` (`manager/leads/meetings`)
      5. `Lead View` (`manager/leads/view_leads`)
      6. `Vertical Add` (`manager/verticals/add`)
      7. `Vertical View` (`manager/verticals`)
      8. `View Internal Task` (`manager/tasks/internal`)
      9. `View External Task` (`manager/tasks/external`)
      10. `Database Transfer` (`manager/database-transfer`)
    - **Admin & Hierarchy Visibility**: Ensured Admin (`$m_role === 1 || $current_user_id === 1`) and authorized managers have full, unconditional visibility across both desktop topbar (`ul.munk_menu`) and mobile drawer (`ul.sidebar-menu`).
  - **2. Lead Review & Qualification System (`ci_admin_leads.php`, `ci_admin_model.php`, `single_view.php`, `routes.php`)**:
    - Built the lead review process using existing CRM components without duplicate pages or tables.
    - **Review Leads Page (`manager/leads/review_leads`)**: Accessible to Admin, Support, and managers above team members in the reporting hierarchy (`fetchTeamList()`), listing submitted database records awaiting qualification review.
    - **Existing Details Page (`manager/leads/view/{id}`)**: Serves as the review interface with all record information displayed for review. Added a prominent **Convert to Lead** button.
    - **Strict Permission Enforcement**:
      - Visible and executable only by Admin (Role 1 or ID 1), Back Office / Support (Role 4), or the user's authorized manager in the reporting hierarchy (`in_array($lead_owner, fetchTeamList($cur_uid))`).
      - Ordinary sales users are prevented from converting their own records into Leads both in the UI and via backend validation.
    - **Conversion Behavior**:
      - Updates existing `ci_pipeline` record to `pipeline_stage = 'lead'`, `conversion_status = 'approved'`, `conversion_reviewed_by`, and `conversion_reviewed_at`.
      - Preserves original database record, ID, and history (no duplicate records).
      - Appends audit entry into `ci_pipeline_logs` and logs action in `ci_admin_logs`.
      - Displays "Converted to Lead" badge if already converted, preventing duplicate conversion attempts.
      - Displays pipeline conversion history table on the Details page.
  - **3. Task Creation Permissions & 3-Character Company Search (`ci_admin_tasks.php`, `tasks/add.php`, `tasks/edit.php`, `tasks/internal.php`, `tasks/external.php`)**:
    - Gated "+ Add Internal Task" and "+ Add External Task" buttons and backend creation in `Ci_admin_tasks::add()` strictly to Admin and Support (`role === 1 || role === 4 || manager_id === 1`).
    - Replaced full company dropdown loads in `tasks/add.php` and `tasks/edit.php` with Select2 AJAX search requiring at least 3 letters typed before querying (`minimumInputLength: 3`, endpoint `manager/tasks/ajax_search_companies`), auto-populating POC, phone, and email upon selection.
  - **4. Zero Comments Policy**:
    - Verified strict adherence to the zero comments rule across all modified PHP files with automated diff and AST token scanning.

### [v1.61.0] - 2026-10-23
- **Timestamp**: `2026-10-23 17:25:00 +0530`
- **Author**: Reinhart-py
- **Summary**: Merge Tasks and Tickets into My Zone navigation, remove standalone Tasks top pill, strictly separate Internal vs External task scope forms, clean company name parsing, normalize screen zoom and compact density, and add Add Database view
- **Detailed Changes & Technical Implementation**:
  - **1. Navigation & Header Consolidation (`application/views/manager/elements/header.php`)**:
    - Merged all task and ticket operations directly into the **My Zone** dropdown:
      - `Add Internal Task` (`manager/tasks/add?scope=internal`)
      - `Add External Task` (`manager/tasks/add?scope=external`)
      - `View Internal Task` (`manager/tasks/internal`)
      - `View External Task` (`manager/tasks/external`)
      - `Raise Ticket` (`manager/tickets/add`)
      - `Open Ticket` (`manager/tickets?status_filter=open`)
    - Removed the standalone **Tasks** top-level pill, streamlining the navigation to: `Dashboard`, `My Zone`, `Team`, `KAM`, `System`, and `Reports`.
    - Integrated a quick Phone Dialer / Calls launcher button directly inside `.header-quick-actions` beside the theme toggle button.
    - Removed redundant jQuery, Bootstrap, and UI duplicate scripts in header and footer.
  - **2. Dedicated Task Scopes (`tasks/add.php` & `tasks/edit.php`)**:
    - Completely removed the task scope radio toggle buttons (`Internal Task` / `External Task`).
    - Made task scope strictly determined by route parameters using hidden input (`<input type="hidden" name="task_scope" ...>`).
    - Internal tasks exclusively render internal configuration fields; External tasks exclusively render client configuration fields.
  - **3. Company Name Sanitization & Dropdown Fix**:
    - Cleaned legacy database rows where raw tab-delimited Excel rows were pasted into the `customer` column.
    - Updated `Ci_admin_tasks::add()`, `Ci_admin_tasks::edit()`, `Ci_admin_model::get_all_companies_dropdown()`, and `Ci_admin_model::search_companies()` to sanitize company names with regex and string splitting.
    - Updated company select options to display exclusively clean company names without multi-column table clutter.
  - **4. Screen Size & Zoom Normalization (`crm-dashboard.css`)**:
    - Fixed the oversized/zoomed-in feeling across all pages:
      - Set compact base typography (`font-size: 13px !important; line-height: 1.45 !important;`).
      - Applied desktop responsive scaling (`zoom: 0.93` on viewports `>= 1200px`).
      - Normalized form inputs to compact 34px height (`.form-control`, `select.form-control`, `.select2-selection--single`).
      - Removed AdminLTE's default `50px` left margin on `.content-wrapper`, `.right-side`, and `.main-footer`.
      - Hidden floating help widget FAB.
  - **5. Add Database View & Routing**:
    - Added `application/views/manager/leads/add-database.php` with title and heading "Add Database".
    - Updated `application/controllers/ci_admin_leads.php` `add()` to load `manager/leads/add-database`.
    - Maintained `addleads.php` delegating cleanly to `add-database.php`.
    - Added dedicated routes for `manager/database` and `manager/database/add`.
  - **6. KAM 12-Month Schedule**:
    - Updated heading in `application/views/manager/kam/schedule.php` to "12-Month Schedule".
    - Updated default schedule templates to versatile accounting deliverables (`VAT Return Filing`, `Corporate Tax Filing`, `Monthly Bookkeeping`, `Financial Statements`, `Document Col/Sub`, `Review & Compliance`, `Audit & Verification`).
  - **7. Zero Comments Policy**:
    - Confirmed zero comments across all 26 modified PHP files via automated AST token scan.

### [v1.60.9] - 2026-10-23
- **Timestamp**: `2026-10-23 13:48:00 +0530`
- **Author**: Reinhart-py
- **Summary**: Relocate Create Schedule exclusively to KAM navigation and restore original Back Office Updates section in My Zone > Database > Add/Edit/View
- **Detailed Changes & Technical Implementation**:
  - **1. My Zone > Database > Add / Edit / View**:
    - Restored original "Back Office Updates" section at the bottom of the form in `application/views/manager/leads/addleads.php` (`manager/leads/add`).
    - Restored original fields (`Category`, `Date`, `Status`, `Reference`, `Remarks`), layout, and functionality (`name="bo_cat[]"`, `bo_date[]`, `bo_status[]`, `bo_ref[]`, `bo_remark[]`).
    - Restored original "Back Office Update" section in `application/views/manager/leads/editleads.php` (`manager/leads/edit`) with hidden `bo_id[]` tracking for all 7 standard items (`SPR`, `Company Code`, `Lead Activity`, `Document Col/Sub`, `Pending Documents`, `Video Verification`, `Post Verification`).
    - Restored original "Meeting Field" table in `application/views/manager/leads/single_view.php` (`manager/leads/view`).
    - Restored backend persistence in `application/controllers/ci_admin_leads.php` (`add()` and `editleads()`) to save/update `ci_boleads` directly without schedule synchronization.
    - Completely removed the 12-month Create Schedule implementation from the Database module.
  - **2. KAM > Create Schedule (`manager/kam/schedule`)**:
    - Replaced the previous single-meeting schedule page with the comprehensive **Create Schedule** implementation under `manager/kam/schedule`.
    - Integrated Client / Company selection dropdown (`#client_select`), Target Year selector (`#sched_year`), Assigned Executive (`assigned_to`), and General Schedule Remarks.
    - Embedded the full 12-month schedule table with:
      - First column: `Schedule / Task Name` (text input, required).
      - 12 columns for calendar months in sequential order: `Jan`, `Feb`, `Mar`, `Apr`, `May`, `Jun`, `Jul`, `Aug`, `Sep`, `Oct`, `Nov`, `Dec`.
      - Monthly cell controls: Compact status dropdown (`Not Applicable`, `Pending`, `Approved`, `Expired`, `Other`) and month active checkbox.
      - Action column with remove row button (`.btn-remove-sched-row`).
      - Pre-populated with the standard 7 default schedules (`SPR`, `Company Code`, `Lead Activity`, `Document Col/Sub`, `Pending Documents`, `Video Verification`, `Post Verification`).
      - Dynamic row management: `+ Add Schedule Name` button dynamically appends new custom schedule rows.
      - Client schedule auto-load: Selecting a client automatically triggers AJAX fetch (`manager/kam/ajax_get_client_schedules`) to populate previously saved 12-month schedules for that client, falling back to the 7 defaults if none exist.
    - Controller persistence in `application/controllers/ci_admin_kam.php`:
      - `schedule()`: Saves 12-month schedule matrix into `ci_kam_schedules` (supporting `matrix_data`, `remarks`, `sched_year`, and calculated overall status) and synchronizes with `ci_kam_client_details.schedule_data`.
      - `ajax_get_client_schedules()`: Returns structured JSON with client's saved schedules and monthly statuses.
      - `delete_schedule()`: Allows deleting schedule rows with authorization checks.
    - Preserved existing navigation item in `application/views/manager/elements/header.php` linking to `manager/kam/schedule`.
  - **Testing & Verification**:
    - Zero Comments Rule: Verified 100% compliance across all touched PHP files with `verify_zero_comments.php`.
    - PHP Syntax Lint: `php -l` passed cleanly on all modified files.
    - End-to-End Verification: Tested `/manager/leads/add` (Back Office Updates present, Create Schedule removed) and `/manager/kam/schedule` (Create Schedule 12-month matrix present with AJAX endpoint) via HTTP suite.

---

### [v1.60.8] - 2026-10-23
- **Timestamp**: `2026-10-23 12:26:00 +0530`
- **Author**: Reinhart-py
- **Summary**: Implement distinct Internal Task vs External Task workflows, transform Back Office Updates to Create Schedule with 12-month calendar grid, remove KAM Report navigation, and enforce strict Ticket Closure permissions for Admin and Support
- **Detailed Changes & Technical Implementation**:
  - **1. Internal Task and External Task Workflows**:
    - Separated Internal Tasks (`manager/tasks/internal`) and External Tasks (`manager/tasks/external`) into two dedicated pages without tabs or shared switching headers.
    - **Internal Task Form Flow (`application/views/manager/tasks/add.php` & `edit.php`)**:
      - Department-first selection: Strictly scoped to the 7 core departments (`Procurement`, `Sales`, `HR`, `Admin`, `IT`, `Accounts`, `Operations`).
      - Does not ask for company or client name by default.
      - Includes "Person Task Is For / Employee Responsible" (`raised_by`) and "Assigned To" (`assigned_to`).
      - Department-specific conditional fields: Accounts invoicing, Operations details, HR/Admin/IT specifics.
      - 10 clean recurrence frequencies (`One-Time`, `Weekly`, `Twice a Week`, `Three Times a Week`, `Monthly`, `Twice a Month`, `Three Times a Month`, `Quarterly`, `Half-Yearly`, `Yearly`) removing ambiguous "Biweekly".
      - Strict weekday selection validation: exactly 1 day for Weekly, 2 days for Twice a Week, 3 days for Three Times a Week.
      - Live Upcoming Schedules list displaying all future dates and start times cleanly.
    - **External Task Form Flow**:
      - Client/company-first workflow: Company dropdown displays strictly the company name without address, contact, or tax clutter.
      - Automatically populates POC name, contact number, and email.
      - Clean link to complete client onboarding (`manager/kam/onboard`) if company is not yet onboarded.
      - General vs Telecom category selection: For telecom tasks, integrates product catalogue with new connection plans, quantity, and MRC (disallowing plan upgrades/downgrades).
    - **Accounts Department & Invoicing Logic**:
      - "Invoiced" selector (`Yes` / `No`).
      - When `No`: automatically classifies task as "To Be Invoiced".
      - When `Yes`: exposes Invoice Reference, Invoice Date, Invoice Amount, and Invoice Value fields.
      - Removed obsolete duplicate `INV-REF` / `INV RAF` fields across forms and views.
    - **Task Views & Controller Authorization (`ci_admin_tasks.php`, `internal.php`, `external.php`, `view.php`)**:
      - Strict role checks on creation: Internal Tasks restricted to Admin; External Tasks restricted to Admin & Support.
      - Standardized table layouts with proper columns, responsive design, and action buttons.
  - **2. My Zone > Database > Add/Edit > Create Schedule**:
    - Renamed legacy "Back Office Updates" section at bottom of Database Add (`addleads.php`), Database Edit (`editleads.php`), and Lead View (`single_view.php`) to **Create Schedule**.
    - Designed compact 12-month table grid: First column = Schedule / Task Name, followed by January through December in calendar order, plus row action.
    - Each monthly cell provides a compact status dropdown (`Not Applicable`, `Pending`, `Approved`, `Expired`, `Other`) and an active checkbox.
    - Pre-loaded with standard 7 default schedules: `SPR`, `Company Code`, `Lead Activity`, `Document Col/Sub`, `Pending Documents`, `Video Verification`, `Post Verification`.
    - Dynamic row management: Users can click `+ Add Schedule Name` to insert custom schedule rows, or delete existing rows.
    - Backend persistence (`ci_admin_leads.php`): Stores 12-month schedule status and checkbox data as structured JSON within `bo_remark` in `ci_boleads`, preserving complete backward compatibility with all 12,474 existing database records.
    - Reused KAM Create Schedule logic: Automatically synchronizes active/approved lead schedules with `ci_kam_schedules`.
    - Removed `Report` link from the KAM navigation dropdown in `application/views/manager/elements/header.php`, preserving all general CRM reports.
  - **3. Ticket Management and Closure Permissions**:
    - Kept `Raise Ticket` and `Open Ticket` as separate destinations under the Tasks navigation menu.
    - `Open Ticket` (`manager/tickets/followups`) lists all non-closed tickets (`t.status != 'Closed'`), preventing completed or resolved tickets from prematurely disappearing before administrative closure.
    - Added dedicated status filter dropdown in Open Tickets to filter among `Open`, `Assigned`, `In Progress`, `Pending`, `On Hold`, and `Resolved (Ready for Review)`.
    - Strict Ticket Closure Permissions: Only Admin (Role 1) and Support (Role 4) can close tickets. All other users cannot close tickets regardless of who created or is assigned to the ticket.
    - Backend security enforcement in `Ci_admin_tickets::update_status()` and `Ci_admin_tickets::status()`.
    - Prominent **Close Ticket** action provided in ticket details (`tickets/view.php`) and table dropdowns (`tickets/index.php`, `tickets/followups.php`), recording `resolution_reviewed_by` and `resolution_confirmed_at`.
    - Displays green verification alert banner on closed tickets showing reviewer name, confirmation date, and closure remarks.
  - **Testing & Verification**:
    - Verified all modified PHP files comply with the zero comments policy (`scratch/check_all_modified.php` returned PASS).
    - All touched PHP files passed `php -l` without errors.
    - Automated curl test suite verified HTTP 200 responses across all updated routes.

---

### [v1.60.7] - 2026-10-22
- **Timestamp**: `2026-10-22 16:18:00 +0530`
- **Author**: Reinhart-py
- **Summary**: Standardize Bootstrap Timepicker with clock icon and chevron stepper popup across all CRM time input sections
- **Detailed Changes & Technical Implementation**:
  - **Standardized Timepicker Component Everywhere**:
    - Replicated the Database Add (`application/views/manager/leads/addleads.php`) timepicker functionality across all sections where users input time:
      - **Calls Follow-up Schedule (`application/views/manager/calls/dashboard.php`)**: Replaced raw text input with `.input-group.bootstrap-timepicker.timepicker` with clock addon and initialized timepicker.
      - **Tasks Add (`application/views/manager/tasks/add.php`)**: Converted `followup_time` to Bootstrap Timepicker input-group.
      - **Tasks Edit (`application/views/manager/tasks/edit.php`)**: Converted `followup_time` to Bootstrap Timepicker input-group preserving existing task value.
      - **Tickets Add (`application/views/manager/tickets/add.php`)**: Converted `followup_time` to Bootstrap Timepicker input-group with current time default.
      - **Tickets Edit (`application/views/manager/tickets/edit.php`)**: Converted `followup_time` to Bootstrap Timepicker input-group preserving ticket value.
      - **KAM Schedule (`application/views/manager/kam/schedule.php`)**: Converted `start_time` and `end_time` into Bootstrap Timepicker input-groups with clock addons.
  - **Styles & Dropdown Layering (`assets/dist/css/crm-dashboard.css`)**:
    - Added high `z-index: 99999 !important;` to `.bootstrap-timepicker-widget.dropdown-menu` to ensure the timepicker dropdown always appears above slide drawers, panels, and modals.
    - Set `cursor: pointer` on `.bootstrap-timepicker .input-group-addon`.
  - **System Stability & Route Fixes**:
    - Mapped missing `$route['manager/tickets/edit/(:any)']` route in `routes.php`.
    - Added missing `getMeetings()` method in `Ci_admin_model`.
    - Fixed undefined variable `$can_close` notice in `tickets/edit.php`.
    - Fixed column key lookups in `meetings/index.php` (`m_poc`, `m_mobile`, `m_address`).
  - **Testing & Verification**:
    - All touched PHP files verified with `php -l` and `scratch/check_comments.php` (PASS: zero comments).
    - Executed live automated test suite across all 9 pages with time inputs, confirming HTTP 200 and zero PHP warnings/notices.

---

### [v1.60.6] - 2026-10-22
- **Timestamp**: `2026-10-22 15:52:00 +0530`
- **Author**: Reinhart-py
- **Summary**: Fix undefined variable $data and resolve all warnings in controllers/ci_admin_team.php
- **Detailed Changes & Technical Implementation**:
  - **Fixed Undefined Variable in `if_not_login()` (`application/controllers/ci_admin_team.php`)**:
    - Initialized `$data = array();` before `return $data;` inside `Ci_admin_team::if_not_login()`, fixing the PHP warning on line 28.
  - **Audit & Clean-Up Across Controller Methods**:
    - Added missing `index()` method redirecting cleanly to `manager/team/members`.
    - Set default optional parameter `$id = ''` for `membersdetails($id = '')`.
    - Set default parameter `$action = ''` for `followups($action = '', $type = 0)`.
    - Added defensive array checks on `chart()` for `$data['main_user']` to prevent undefined array key warnings.
    - Added defensive `is_array()` checks before `implode(',', ...)` for filter inputs.
    - Removed dead commented-out lines to maintain strict zero-comment compliance.
  - **Testing & Verification**:
    - Validated with `php -l` and `scratch/check_comments.php` (PASS: zero comments).
    - Executed live automated HTTP check against all Team controller endpoints (`members`, `leads`, `assignleads`, `followups`, `meetings`, `assignmeetings`, `chart`), verifying HTTP 200 with zero PHP notices or warnings.

---

### [v1.60.5] - 2026-10-22
- **Timestamp**: `2026-10-22 15:45:00 +0530`
- **Author**: Reinhart-py
- **Summary**: Implement calling backend awareness (prevent simulated calls when unconfigured), add dual phone selector (Primary and Alt/Secondary), remove speech synthesis IVR, and ensure responsive follow-up panel visibility across all viewports
- **Detailed Changes & Technical Implementation**:
  - **Backend & API Key Connection Awareness (`ci_admin_calls.php`, `dashboard.php`)**:
    - Removed synthetic speech text-to-speech robot IVR on outbound calls.
    - System checks `threecx_config` to determine if PBX FQDN and Client ID/API credentials are genuinely configured.
    - If unconfigured, the CRM does not fake a ringing call or count fake seconds; it displays a clear notification banner with a 1-click fallback button (`<a href="tel:...">Launch Phone Dialer (tel:)</a>`).
    - If configured, initiates real calling via 3CX WebClient / PBX gateway dispatch.
  - **Dual Contact Numbers Support (Primary & Alt / Secondary)**:
    - Displayed both contact numbers side by side in clean, selectable cards:
      - **Primary Contact**: Displays primary phone with active indicator pill.
      - **Secondary / Alt Contact**: Displays alternate phone number.
    - Clicking either card selects that number, highlights it, updates the dialer input, and prepares it for calling immediately.
  - **Responsive Follow-up Panel ("Side Filling")**:
    - Completely restructured `#followupSlidePanel` for 100% visibility on all screen resolutions and laptop heights (e.g. 768px, 900px, 1080p, and mobile/tablets):
      - Header and footer are fixed (`flex-shrink: 0`), with the footer sticky at the bottom of the viewport so action buttons (`Save & Next Contact` and `Save Details`) are never clipped off-screen.
      - Body is set to `flex: 1 1 auto; overflow-y: auto; -webkit-overflow-scrolling: touch` with streamlined field spacing.
      - On viewports `<= 600px`, panel width scales to `100vw` for mobile/tablet screens.
  - **Zero Comments Policy**: Verified 100% compliance across all modified PHP files with `scratch/check_comments.php`.

---

### [v1.60.4] - 2026-10-22
- **Timestamp**: `2026-10-22 15:35:00 +0530`
- **Author**: Reinhart-py
- **Summary**: Fix undefined active_lead variable in ci_admin_calls.php, remove decorative status pill, add strict rules against pill badges and fake calling, recreate smartphone Call Logs slide-drawer, and implement authentic telephone cadence & interactive IVR
- **Detailed Changes & Technical Implementation**:
  - **Fixed Undefined Variable in Controller (`application/controllers/ci_admin_calls.php`)**:
    - Resolved `Undefined variable $active_lead` on line 183 by explicitly initializing `$active_lead = !empty($queue) ? $queue[0] : null;`.
  - **Call Logs Past-Only Filter**:
    - Filtered `recent_logs` in `index()` and `ajax_get_call_logs()` with `where_not_in('call_status', ['Initiated', 'Dialing'])` and `created_at <= NOW()`.
    - Strictly prevents present, in-progress, or future entries from rendering in historical logs.
  - **Removed Status Pill & Added Rules ([crm_rules.md](file:///c:/Users/SRJ%20feb/Downloads/softwares/CRM/2026.10.08-1%20-%20crm.rolextogroup.com/crm.rolextogroup.com/.agents/rules/crm_rules.md))**:
    - Removed the decorative green "Ready" pill badge from the top navigation bar.
    - Updated rules to permanently ban decorative pill badges in system trays/topbars, require authentic call durations (zero fake connected seconds while dialing), and enforce smartphone-style Call Logs with 1-click actions.
  - **Smartphone Call Logs Slide-Drawer (`application/views/manager/calls/dashboard.php`)**:
    - Configured Call Logs toggle on the right side of the navigation bar.
    - Slides out from the right like a mobile phone Recents screen.
    - Each call entry includes:
      - Customer Name, Phone Number, Date/Time, Duration, and Outcome.
      - **Call Action**: 1-click load and instant dial.
      - **Copy Action**: 1-click clipboard copy with instant toast confirmation.
      - **View / Add Contact Action**: Opens lead profile or loads contact into dialer.
  - **Authentic Dialing Cadence & Interactive IVR**:
    - Dialing state plays standard dual-frequency telephone ringback (440Hz + 480Hz) with 2-second burst cadence; no fake connected seconds count while ringing.
    - Only after connection does the elapsed conversation timer start.
    - Real interactive IVR speech synthesis and DTMF handler: Key 1 routes to Sales, Key 2 to Accounts/VAT, Key 3 to Corporate Tax, Key 0 to Operator, Key 9 to Voicemail recording with fallback tone.
    - Fallback: Unanswered calls after maximum ring limit automatically terminate and log as Missed Call with 0 duration.
  - **Zero Comments Policy**: Verified 100% compliance across all modified PHP files with `scratch/check_comments.php`.

---

### [v1.60.3] - 2026-10-22
- **Timestamp**: `2026-10-22 15:25:00 +0530`
- **Author**: Reinhart-py
- **Summary**: Restore Team Members page to original DataTable state; Recreate Calls page with Apple-style glassmorphism dark UI, integrated calling, and automated next contact follow-up workflow
- **Detailed Changes & Technical Implementation**:
  - **Restored Team > Members Page (`application/views/manager/team/members.php`, `application/controllers/ci_admin_team.php`)**:
    - Reverted the Team > Members interface to its original clean DataTable layout with columns: S.N., Name, Designation, Department, Contact No, Email, Location, Manager, and Members action link.
    - Updated `ci_admin_team.php` `members()` and `membersdetails($id)` to restore the original page title "My Team Members" and load direct team records via `get_teamMember()`.
    - Removed all tree styling and scripts; verified zero PHP comments and flawless DataTable reordering and pagination.
  - **Recreated Apple-Style Glassmorphic Calls Page (`application/views/manager/calls/dashboard.php`)**:
    - Built a dark, minimal Apple-style calling interface (`#090a0f` background with frosted glass cards `rgba(18, 24, 38, 0.75)`, blur filter, and subtle white borders).
    - **Essential Client Identity Card**: Features prominent client name, company/category pill, phone number, POC, location, sales department, and previous call remarks quote box.
    - **Apple Circular Glass Keypad**: 1-9, *, 0, # with SF Pro letters and real DTMF dual-tone audio feedback via Web Audio API.
    - **Integrated In-CRM Calling**: Calls start and end directly in the browser with live ringing feedback and elapsed duration timer (`00:00`); calls automatically create and update records in `ci_call_logs` without leaving the CRM.
    - **Smooth Slide-Open Follow-up Panel**:
      - Automatically slides into view upon call termination.
      - Includes unified Call Status select, call duration badge, and discussion remarks.
      - Dynamically displays Next Follow-up Date and Time picker only when the selected status requires callback/follow-up.
      - Renders vertical-specific fields: Accounting & Tax displays VAT status + VAT TRN and Corporate Tax (CT) status + CT TRN; Telecom displays provider and line count/spend; General displays business category.
    - **Next Contact Workflow**:
      - "Save & Next Contact" button saves call log, updates lead status, remarks, and tax fields, closes the panel, and automatically advances the queue to load the next contact ready to dial in 1 click.
    - **Slide Drawers**:
      - Calling Queue drawer with instant search filtering.
      - Recent Calls drawer showing genuine historical records from `ci_call_logs`.
  - **Database Transfer Autocomplete Dropdown (`application/views/manager/leads/database_transfer.php`)**:
    - Compact transfer bar embedded directly into the table box header with 3-character minimum input filter dropdown for selecting team members.
  - **Zero Comments Policy**: Verified 100% compliance across all modified PHP files with `scratch/check_comments.php`.

---

### [v1.60.2] - 2026-10-22
- **Timestamp**: `2026-10-22 14:43:00 +0530`
- **Author**: Reinhart-py
- **Summary**: Fix inverted male/female vector profile avatar illustrations in assets/dist/img/
- **Detailed Changes & Technical Implementation**:
  - **Corrected Vector Avatar Mappings & Asset Files**:
    - Discovered `pfp_female_2.svg` had inadvertently contained a full mustache and beard (`translate(62 42)`), causing female profiles for user ID 1 to display with heavy masculine facial hair.
    - Concurrently, `pfp_male_2.svg` and `pfp_male_3.svg` possessed topknot buns, long curly locks, and pink garments, leading users selecting Male to view a feminine avatar.
    - Redesigned and replaced all 6 vector avatars (`assets/dist/img/pfp_male_*.svg`, `assets/dist/img/pfp_female_*.svg`) with clean, distinct, professional illustrations:
      - **Male Avatars (1, 2, 3)**: Sharp modern short haircuts (side-part, taper fade, textured crop), strong masculine facial structures, and professional corporate shirts/suits (navy `#1E3A8A`, slate `#334155`, forest `#065F46`).
      - **Female Avatars (1, 2, 3)**: Long styled waves, chic bob cut, and layered locks, with rose (`#E11D48`), purple (`#7C3AED`), and teal (`#0D9488`) blouses, feminine eyelashes, soft blush, and strictly zero facial hair.
    - Verified header mapping in [header.php](file:///c:/Users/SRJ%20feb/Downloads/softwares/CRM/2026.10.08-1%20-%20crm.rolextogroup.com/crm.rolextogroup.com/application/views/manager/elements/header.php): saving "Male" reliably renders masculine avatars and "Female" reliably renders feminine avatars across all user IDs.

---

### [v1.60.1] - 2026-10-22
- **Timestamp**: `2026-10-22 14:32:00 +0530`
- **Author**: Reinhart-py
- **Summary**: Refine organizational hierarchy tree: closed by default on all levels, stepped left-to-right indent with guide lines, and full-row click triggers
- **Detailed Changes & Technical Implementation**:
  - **Closed By Default Across All Levels (`application/views/manager/team/members.php`)**:
    - Configured every `.tree-sub-drawer` to start closed with `class="tree-sub-drawer closed" style="display:none;"`.
    - Initialized all toggle carets with `class="tree-toggle-caret collapsed"` and `fa-chevron-right` icon.
    - Preserved instant typeahead auto-expansion and "Expand All" / "Collapse All" global actions.
  - **Progressive Stepped Left-to-Right Downgrade**:
    - Each nested level drawer is indented by `margin-left: 28px` with a vertical connector line (`border-left: 2px solid #e2e8f0; padding-left: 8px`).
    - Visually clarifies the reporting lineage from left to right as the hierarchy descends through multiple sub-teams.
  - **Full Row Interaction**:
    - Clicking anywhere on a manager's row triggers their subordinate drawer slide-down/slide-up.
    - Chevron toggles dynamically between `fa-chevron-right` and `fa-chevron-down`.
    - Inner child branch clicks are isolated with event stopPropagation to prevent unintentional parent collapses.
  - **Auto-Adjust Responsive Viewport**:
    - Card viewport configured with `max-height: calc(100vh - 210px); min-height: 420px; overflow-x: auto; overflow-y: auto`.
    - Container styled with `min-width: max-content; width: 100%` so deeply nested trees and large teams scale horizontally and vertically without text squeezing or truncation.
  - **Zero Comments Policy**: Strictly verified—zero comments in PHP code.

---

### [v1.60.0] - 2026-10-22
- **Timestamp**: `2026-10-22 14:15:00 +0530`
- **Author**: Reinhart-py
- **Summary**: Implement hierarchical organizational tree drawers for Team Members, fix My/Team projection zero-result bug, separate My Tasks vs Team Tasks, upgrade Calls center with in-CRM softphone, and clean redundant transfer button
- **Detailed Changes & Technical Implementation**:
  - **Hierarchical Organizational Tree Drawers (`application/views/manager/team/members.php`, `application/models/ci_admin_model.php`, `application/controllers/ci_admin_team.php`)**:
    - Replaced flat member lists with an interactive expandable/collapsible organizational hierarchy drawer tree mimicking the intuitive `tree` command structure.
    - Implemented recursive `fetchTeamHierarchyTree($root_id)` model method with `$visited` loop prevention to construct a multi-level reporting structure (`manager -> subordinates`).
    - Clean slate/gray aesthetics with folder/user SVG vector badges, department tags, direct subordinate counters, and interactive caret drawers.
    - Fluid auto-adjustment with dual-axis scroll containers (`max-height: 78vh; overflow: auto`) to cleanly support hundreds or thousands of members across arbitrary nesting depths without horizontal blowout.
    - Global header controls with "Expand All", "Collapse All", and instant typeahead search filter with auto-expansion of matching descendants.
  - **Fixed Projection "No Data Found" Bug across My Projection & Team Projection (`application/controllers/ci_admin_leads.php`, `application/controllers/ci_admin_team.php`, `application/models/ci_admin_model.php`)**:
    - Diagnosed root cause: controller routines were overwriting `$d1 = $_GET['exp_d1']`, which caused CodeIgniter active record to filter leads created in future projection months (`A.created BETWEEN exp_d1 AND exp_d2`) rather than matching the expected closure date (`A.close_date`).
    - Cleanly separated `exp_d1`/`exp_d2` in `ci_admin_leads.php` and `ci_admin_team.php`.
    - Updated `ci_admin_model.php::get_Myleads` and `getTeamLeads` to filter strictly on `A.close_date BETWEEN exp_d1 AND exp_d2` when clicked from the projection bar charts.
  - **Segregated My Tasks vs Team Tasks on Dashboard (`application/views/manager/dashboard.php`)**:
    - Separated "My Tasks" (direct assignee/creator tasks) from "Team Tasks" (collaborator and team member tasks).
    - Fixed title, total count pill, and all time-period chip links (`Today`, `Upcoming`, `Overdue`, `Closed`) to route specifically to `manager/tasks?team=0` for My Tasks and `manager/tasks?team=1` for Team Tasks.
  - **Calls Center Overhaul & In-CRM Softphone Calling (`application/views/manager/calls/dashboard.php`)**:
    - Modernized typography with Inter font (`400`, `500`, `600`, `700`) and standardized color palette (`#2563EB` primary, `#10B981` emerald call button).
    - Standardized metadata labels (11px uppercase bold `#64748B`, letter-spacing `0.05em`) and values (14px 600 `#0F172A`).
    - Upgraded Recent Calls list to show all past call logs with call direction, answered/missed badges, formatted talk duration (`Xm Ys`), phone number, and timestamp.
    - Embedded softphone modal (`#crmCallEmbedModal`) with responsive iframe frame (`#crmEmbeddedCallFrame`) to keep 3CX WebClient calling securely inside the CRM interface.
  - **Removed Redundant Transfer Button on Database Transfer (`application/views/manager/leads/database_transfer.php`)**:
    - Removed the extra top-right blue `btnTransferLeads` button (`Transfer Selected (1)`) to avoid UI clutter, as inline transfer and row-level dropdown transfer handle this cleanly.
  - **Zero Comments Policy**: Strictly maintained across all modified PHP templates and controllers.

---

### [v1.59.3] - 2026-10-22
- **Timestamp**: `2026-10-22 12:53:00 +0530`
- **Author**: Reinhart-py
- **Summary**: Replace floating transfer modal with sleek inline transfer bar, add Transfer option to row Action dropdown, and enforce 3-alphabet team member search
- **Detailed Changes & Technical Implementation**:
  - **Eliminated Floating Modal Overlay (`application/views/manager/leads/database_transfer.php`)**:
    - Completely removed the invasive, full-screen red modal overlay (`#modalTransferLeads`) and native `<select>` covering the page.
    - Designed a sleek, modern inline transfer action bar positioned directly above the data table that slides in dynamically when records are selected or targeted.
  - **3-Alphabet Team Member Search Autocomplete**:
    - No team member records are displayed or visible until at least 3 alphabets are typed into the search field.
    - Instant client-side typeahead renders matching users with name and email, allowing 1-click selection and enabling the "Confirm Transfer" button.
  - **Row Action Button "Transfer" Option**:
    - Added direct "Transfer" option to every row's Action dropdown button (`Action` -> Preview, Edit, Transfer).
    - Clicking "Transfer" targets that specific lead, highlights it, scrolls smoothly to the inline transfer bar, and focuses the 3-letter team member search input.
    - Zero comments policy strictly maintained.

---

### [v1.59.2] - 2026-10-22
- **Timestamp**: `2026-10-22 12:50:00 +0530`
- **Author**: Reinhart-py
- **Summary**: Harmonize Add Task UI with Edit Task UI, remove private/public toggles, and enforce 3-alphabet user assignment autocomplete
- **Detailed Changes & Technical Implementation**:
  - **Tasks Add & Edit UI Harmonization (`application/views/manager/tasks/add.php`, `edit.php`)**:
    - Harmonized `add.php` and `edit.php` into the unified standard CRM box card layout with clean `box box-default`, matching header, breadcrumbs, back button, and footer action buttons.
    - Removed private and public visibility fields.
    - Cleaned and organized practical operational fields: Task Title / Customer Name (`customer`)*, POC Contact Person (`poc`), Contact Phone (`contact1`), Email (`email`), Department / Category (`category`), Task Type (`business_category`), Status (`status`), Priority (`dnd_status`), Follow-up Date (`followup_date`), Follow-up Time (`followup_time`), Due / Close Date (`close_date`), and Task Details / Instructions (`remark`).
  - **3-Alphabet User Assignment Autocomplete Component**:
    - User list dropdown remains completely hidden/invisible until user types at least 3 characters.
    - Once >= 3 characters are typed, dynamically queries and renders matching users with name and email.
    - Integrated across all user assignment forms:
      - Add Task (`application/views/manager/tasks/add.php`)
      - Edit Task (`application/views/manager/tasks/edit.php`)
      - KAM Create Schedule (`application/views/manager/kam/schedule.php`)
      - KAM Onboard Client (`application/views/manager/kam/onboard.php`)
    - Added quick-clear button (`fa fa-times`) to easily reset assignee.
    - Strictly maintained Zero Comments policy in all modified PHP files.

---

### [v1.59.1] - 2026-10-22
- **Timestamp**: `2026-10-22 12:42:00 +0530`
- **Author**: Reinhart-py
- **Summary**: Fix SQL Error 1054 Unknown column '1' in where clause across KAM My Client and leads model
- **Detailed Changes & Technical Implementation**:
  - **Resolved CodeIgniter Active Record Backtick Escaping**:
    - Fixed `Error Number: 1054 Unknown column '1' in 'where clause'` in `application/controllers/ci_admin_kam.php::my_client()` caused by unescaped `where('1 = 0')`.
    - Simplified non-admin condition in `ci_admin_kam.php` so all non-admin users (Back Office, Operations, assigned users) properly query records assigned to them or their executive ID using `$this->db->where('...', NULL, FALSE)`.
    - Replaced legacy `$this->db->where('1 = 0')` in `application/models/ci_admin_model.php` (`get_Myleads`, `get_MyleadsFollowups`, `getLeadsMeetings`) with `$this->db->where('A.id', -1)` to prevent SQL syntax errors across all non-sales roles.

---

### [v1.59.0] - 2026-10-22
- **Timestamp**: `2026-10-22 12:35:00 +0530`
- **Author**: Reinhart-py
- **Summary**: Align KAM My Client with Database View, implement Google-style KAM scheduling recurrence, build Google Calendar Task modal card UI, and standardize action buttons
- **Detailed Changes & Technical Implementation**:
  - **KAM My Client UI Alignment (`application/views/manager/kam/my_client.php`)**:
    - Aligned layout, filter toolbar (Date range, MRR range, Service Category, Lead Category, Status, User Name, Country, State, Search, Reset), and DataTables table to match My Zone > Database View (`leadsview.php`) 100%.
    - Updated `Ci_admin_kam::my_client()` to process all filter queries and pass lookup datasets (`c_list`, `st_list`, `p_cat`, `lc_list`, `u_list`).
    - Standardized action buttons to standard split dropdown (`Action` -> Preview, Edit).
  - **KAM Create Schedule UI & Google Recurrence (`application/views/manager/kam/schedule.php`)**:
    - Re-architected `manager/kam/schedule` to adopt the clean card layout of Database Add (`addleads.php`).
    - Added Google-style recurring schedule controls: Daily, Weekly (with day checkboxes Mon-Sun), Monthly (day of month), and Once (does not repeat).
    - Integrated client typeahead search, start/end dates, start/end times, and executive assignment.
    - Stripped bulky 96 daily schedule inputs from `onboard.php`, keeping onboarding clean and consolidating all schedules into `manager/kam/schedule`.
    - Added active schedule management table with standard split action buttons.
  - **Tasks > Add Task Google Calendar Modal Card UI (`application/views/manager/tasks/add.php`)**:
    - Re-architected Add Task view into a sleek centered card matching Google Calendar's task modal.
    - Included large clean title input, pill type switchers (`Event`, `Task`, `Appointment schedule`), Date and Start/End time pickers, `All day` checkbox, recurrence dropdown, deadline picker, and description textarea.
    - Added Department / Task list selector supporting Back Office Tasks, Operations Tasks, Sales Tasks, KAM / Client Tasks, IT & Tech Tasks, Accounts & Finance Tasks, HR & Admin Tasks, Support Tasks, and My Tasks.
    - Added Executive assignee, Availability/Priority (`Free`, `Busy`, `Urgent`), Status, Visibility (`Private`/`Public`), and Client/Company input.
    - Styled with rounded blue `Save` button, real FontAwesome icons, and zero AI clutter.
  - **Task View & Follow-ups UI Standardization (`application/views/manager/tasks/index.php`, `followups.php`)**:
    - Standardized action buttons in Task View (`manager/tasks`) to the CRM split dropdown (`Action` -> Preview, Edit).
    - Aligned Task Follow-ups (`manager/tasks/followups`) with Leads Follow-ups (`followupsview.php`), adding quick filter buttons (All Missed, Last 7 Days, Today, Next 7 Days, All Future) and split action dropdowns.
    - Removed all HTML/PHP comments across views to strictly comply with Zero Comments rule.

---

### [v1.58.0] - 2026-10-22
- **Timestamp**: `2026-10-22 12:00:00 +0530`
- **Author**: Reinhart-py
- **Summary**: Implement KAM Client & Sales Lead strict role isolation, fix role schema query, block transferred lead access, and catalog verification routes
- **Detailed Changes & Technical Implementation**:
  - **KAM My Client & Sales Leads Strict Domain Separation**:
    - Designated KAM My Client exclusively for Back Office and Operations roles (`role == 2 || role == 5`).
    - Enforced `INNER JOIN ci_kam_client_details` in `Ci_admin_kam::my_client()` so only onboarded KAM clients are retrieved.
    - Restricted Back Office / Operations view to records assigned to them or where they are designated collaborators (`lead_assign`, `agents`, or `executive_id`).
    - Excluded KAM onboarded records (`database_category != 'KAM Onboarded'`) from sales leads, follow-ups, meetings, and database transfer lists.
    - Completely separated Back Office and Operations from general sales leads in `ci_admin_model.php` (`get_Myleads`, `get_MyleadsFollowups`, `getLeadsMeetings`), preventing data mixing across departments.
    - Prevented non-admin users from transferring ownership of KAM clients in `bulk_transfer()` and `edit()` (`ci_admin_leads.php`), keeping default ownership fixed to the assigned person.
  - **Roles Table Query & Soft-Delete Fix**:
    - Fixed unknown column error `1054 Unknown column 'delete_status' in 'where clause'` in `application/controllers/ci_admin_roles.php` by querying active records (`status = 1`).
  - **Lead Transfer & Old Owner Invisibility**:
    - Updated query models in `ci_admin_model.php` to strictly check `lead_assign` over `created_by` for allotted leads.
    - Previous owners can no longer see transferred leads in their personal lists or follow-ups unless explicitly reassigned or added as a collaborator.
    - Added previous owner and transfer history tracking table `ci_lead_transfers` synced to `database/localhost.sql`.
  - **KAM Section Stability & 500 Fix**:
    - Removed non-existent `'price'` helper from `Ci_admin_kam` constructor, resolving 500 fatal crash.
    - Connected KAM navigation links directly to `base_url().'manager/kam/my-client'` across desktop and mobile headers.
  - **System Route Cataloging & Value Reference (`value.md`)**:
    - Appended exhaustive line-by-line testing URL list containing all 47 verified routes for self-testing.

---

### [v1.57.0] - 2026-10-21
- **Commit**: `1df3fce9ac93e7c37c31eb58634e892f1174a74d` (`1df3fce`)
- **Tree Code**: `9c2840ee8372b00e83499a3d5ad186c749ea2b0b` (`9c2840e`)
- **Timestamp**: `2026-10-21 17:00:00 +0530`
- **Author**: Reinhart-py
- **Summary**: Update README.md and CHANGELOG.md with standard versioning, commit hashes, tree codes, and strict local memory rules
- **Detailed Changes & Technical Implementation**:
  - **Permanent Workspace Memory & Rules**: Created `project_memory.md` and `.agents/rules/crm_rules.md` documenting user aesthetic preferences (clean minimal SaaS forms, zero AI clutter, no random human photos, strict standard palette), strict coding rules (zero comments in PHP files, `php -l` verification), and domain logic (unified status, collaborator vs owner distinction, main navigation order).
  - **Git Ignore Security**: Added `project_memory.md` and `.agents/` to `.gitignore` so local memory and rule files are preserved locally and never leaked or pushed to GitHub.
  - **Documentation Alignment**: Synchronized `README.md` and `CHANGELOG.md` to reflect Calling API settings, workstation calling workflow, universal reports navigation, and vector PFPs.

---

### [v1.56.0] - 2026-10-21
- **Commit**: `8ce5b0f32d448188c2e7a5e3876f5ba450c74700` (`8ce5b0f`)
- **Tree Code**: `05fcb92f91d558622c2f8975cc22cadb1027915c` (`05fcb92`)
- **Timestamp**: `2026-10-21 16:49:01 +0530`
- **Author**: Reinhart-py
- **Summary**: Refine Calling API UI, enable universal Reports navigation, add vector PFPs, standardize footer and unify CRM statuses
- **Detailed Changes & Technical Implementation**:
  - **Calling API Settings Streamlined UI (`application/views/manager/system/calling_api.php`)**:
    - Removed obsolete subtitle text and redundant "Open Calls Workstation" button.
    - Eliminated helper text clutter and annotations below input fields for a clean, minimal SaaS configuration experience.
    - Streamlined input placeholders and preserved 3CX PBX FQDN, Client ID, Client Secret, default extension, browser WebClient toggle, multi-line mapping, and test connection integration.
  - **Universal Reports Navigation Across All User Levels**:
    - Removed administrator-only role restriction (`m_role == 1 || m_role == 4`) from the **Reports** navigation bar in both desktop (`.munk_menu`) and mobile (`.sidebar-menu`) headers.
    - Enabled all team members and regular users to access reports, allowing them to view and track their own and their team's performance, database, leads, and quotes.
  - **Non-Human Vector Profile Pictures (PFPs)**:
    - Replaced human photographic avatar fallbacks with clean, modern vector-illustrated character avatars (`assets/dist/img/pfp_male_*.svg`, `assets/dist/img/pfp_female_*.svg`).
    - Completely eliminated real human photos from male and female profile avatars across the top-right header and profile dropdown.
  - **Footer Branding Standardization (`application/views/manager/elements/footer.php`)**:
    - Updated copyright line across all CRM pages to: `© 2026 Seven Strokes CRM Platform. All rights reserved.`
    - Removed outdated secondary company tagline from footer.
  - **Unified Call & Lead Status Architecture**:
    - Unified call outcome statuses and lead statuses under the CRM's primary status workflow (`ci_status`), eliminating duplicate and confusing redundant dropdowns across the CRM.
    - Aligned post-call outcomes and lead status updates in `ci_admin_calls.php` to synchronize disposition and status records cleanly.
  - **Calls Workstation Layout (`application/views/manager/calls/dashboard.php`)**:
    - Compact single-screen workstation matching reference design with no vertical scrolling.
    - Left column with Keypad (38px keys) and Recent Calls tab.
    - Right column with active call banner, 4-box meta summary, outcome/status dropdown, notes, and follow-up date/time.
    - Right slide-in drawer (`#nextCallsQueueDrawer`) tracking upcoming database records and remaining queue counter badge.
  - **Collaborator Assignment & Team Navigation**:
    - Renamed `Lead Assigned To` to **Database Assigned to Collaborators** in `My Zone > Database Add > More Category Fields` (`addleads.php`, `editleads.php`).
    - Primary creator/owner is default; collaborators are stored in `agents` excluding the current user.
    - Renamed **Assign Leads** under `Team` navigation to **Assign Database**; collaborator-assigned records appear in each collaborator's `Team > Assigned Database` list.
  - **Main Navigation Order & Profile Menu**:
    - Navigation order: **Dashboard, My Zone, Team, KAM, Tasks, System, Reports**.
    - Top-right Google-style profile menu with gender-based vector illustrated avatars and greeting `Hi, [User Name]`.

---

### [v1.55.0] - 2026-10-21
- **Commit**: `8a47fba1444f9c6b60b8d407c7a3e8c0c85380db` (`8a47fba`)
- **Tree Code**: `268e3b82fd7b7dddc54df367a251acdaaf77e4e4` (`268e3b8`)
- **Timestamp**: `2026-10-21 14:11:48 +0530`
- **Author**: Reinhart-py
- **Summary**: Filter sales database report to only include assigned database records and show assigned person name
- **Detailed Changes & Technical Implementation**:
  - Filtered sales database report to only include assigned database records.
  - Added assigned person's name as the primary column in pivot summaries (`Name` -> `Source` -> `Batch Ref` -> `Status`).
  - Removed `COUNTA of ` prefix from headers and streamlined table display.

---

### [v1.54.0] - 2026-10-21
- **Commit**: `224f19b9358b3f01930fad4f24ce1cd0f50c4f5c` (`224f19b`)
- **Tree Code**: `2a1bedd57bcb9c07d3e9492d72ccd414f66b84c9` (`2a1bedd`)
- **Timestamp**: `2026-10-21 13:50:45 +0530`
- **Author**: Reinhart-py
- **Summary**: Update admin logs UI to match team leads with responsive layout and plain black status text
- **Detailed Changes & Technical Implementation**:
  - Re-architected `application/views/manager/system/admin_logs.php` matching Leads View typography, table layout, and pagination controls.
  - Standardized status badges and eliminated extraneous banner cards.

---

### [v1.53.0] - 2026-10-21
- **Commit**: `89abc45f8f9418deeb46c7e9e464016c969e0264` (`89abc45`)
- **Tree Code**: `3091fd9d432cb4d1cf29f0dc035092a833c5d5fc` (`3091fd9`)
- **Timestamp**: `2026-10-21 12:05:18 +0530`
- **Author**: Reinhart-py
- **Summary**: Open report sections by default and partition team report sections by member name
- **Detailed Changes & Technical Implementation**:
  - Set both My Report and Team Report collapsible accordion sections to open by default.
  - Grouped and partitioned team report records by team member name with subtotals.

---

### [v1.52.1] - 2026-10-21
- **Commit**: `5a989e3d31a55767f051a313b3a8bc7769574af3` (`5a989e3`)
- **Tree Code**: `411ee8208f66a91ddb83ff6d36bb83d536357598` (`411ee82`)
- **Timestamp**: `2026-10-21 11:37:42 +0530`
- **Author**: Reinhart-py
- **Summary**: Align database report with Excel pivot structure and remove detailed view table
- **Detailed Changes & Technical Implementation**:
  - Re-structured Database Report pivot sheet to mirror Excel Pivot format (`Source` -> `Batch Ref` -> `Database Status`).
  - Removed redundant bottom detailed records table from report views, keeping pivot view fast and uncluttered.

---

### [v1.52.0] - 2026-10-21
- **Commit**: `32947b18873e428b8e05728e0e06a749d81a2228` (`32947b1`)
- **Tree Code**: `9949d811a51ad57a67ed908a90f77fea8b6ef8be` (`9949d81`)
- **Timestamp**: `2026-10-21 11:10:45 +0530`
- **Author**: Reinhart-py
- **Summary**: Implement two-section reports architecture, configurable column selector, non-allotted leads view fix, system admin logs UI standard, and documentation update
- **Detailed Changes & Technical Implementation**:
  - Implemented unified two-section collapsible pivot summaries (`My Report` and `Team Report`) across all 8 Sales reports.
  - Added PowerSheet configurable column selector for custom reports with instant preset buttons.
  - Fixed non-allotted database view to properly surface imported leads with `lead_assign = 0`.
  - Reordered main navigation: Dashboard, My Zone, Team, KAM, Tasks, System, Reports.
  - Re-architected `application/views/manager/system/admin_logs.php` to match the exact table layout, typography, filters, and spacing of `Leads -> View` (`leadsview.php`).
  - Removed redundant headers and banner clutter.
  - Standardized status badges (`Success`, `Timeout`, `Failed`) using matching Leads View badge styling.
  - Added clean routing aliases in `routes.php` for `manager/system/admin-logs` and `manager/system/admin_logs`.
- **Standardized Action Buttons**:
  - Standardized action buttons in `manager/database-transfer` and `manager/kam` to match the standard CRM split dropdown pattern.

---

### Lead Allotment, Assignment & Transfer Logic, Assigned Leads UI Alignment, Mobile Drawer & Reports Drilldown
- **Date & Time**: `2026-10-20 13:20:00 +0530`

#### Changes & Additions:
- **Lead Allotment, Assignment & Transfer Logic**:
  - Enforced strict separation between Allotment, Assignment, and Transfer operations.
  - **Allotment (Admin Only)**: Admin assigns unallotted records (`created_by = 0`) to owners by setting `created_by = $target_user_id` in `Ci_admin_leads::bulk_move_assign()`. The `created_by` field is the single canonical ownership field.
  - **Assignment (Collaborators)**: Lead owners share access with team members via `lead_assign` / `agents` without modifying original owner (`created_by`).
  - **Transfer (Ownership Handover)**: Handover changes ownership by updating both `created_by` and `lead_assign` to the target user in `Ci_admin_leads::bulk_transfer()`, recorded in administrative audit logs.
- **System > Allotted Database (`manager/assigned-leads`)**:
  - Replaced the view layout with the exact structure and styling of `Team > Leads` (`manager/team/leads`).
  - Added bulk select checkboxes, `Date` (`simple_date($li['created'])`), and `Created` column.
  - Leads without an allotted owner display a clean `Non-Allotted` label, while allotted leads display the owner's name with column-footer dropdown filtering.
  - Preserved identical DataTables export buttons and responsive table behavior.
- **Vertical Follow-up Route & Method**:
  - Added route `manager/verticals/followups` and controller method `Ci_admin_verticals::followups()`.
  - Fixed 404 Not Found error when clicking Vertical Follow-up under My Zone in top and mobile navigation.
- **KAM > My Client View**:
  - Created standardized view `application/views/manager/kam/my_client.php` matching the core CRM table layout and headers with an empty-state fallback.
- **Android & Mobile Navigation Drawer**:
  - Attached touch/click handlers in `application/views/manager/elements/footer.php` for `#crmMobileHamburger` and `#crmSidebarClose`.
  - Tapping the 3-line hamburger menu toggles `sidebar-open` on `body`, with backdrop dismiss and nested treeview accordion expansion.
- **Universal Back Buttons**:
  - Added global click handler in `footer.php` intercepting back buttons (`a.btn` containing "Back", `i.fa-arrow-left`) and invoking `window.history.back()`.
- **Reports Drilldown Precision**:
  - In `application/views/manager/report/admin/view.php`, restricted `<a>` drilldown links exclusively to numeric digits and currency amounts.
  - Department names, agent names, category titles, and total labels are rendered as unlinked plain text.
- **Dashboard Projections**:
  - Formatted month labels as `OCT 26`, `NOV 26` aligned directly beneath their respective bars with consistent sizing.

---

### Database Transfer Management, Calling Center Dashboard & Lead Field Additions
- **Date & Time**: `2026-10-19 15:30:00 +0530`

#### Changes & Additions:
- **Lead Management > Database Transfer**:
  - Created `manager/database-transfer` route, view, and bulk transfer action (`Ci_admin_leads::database_transfer()` and `Ci_admin_leads::bulk_transfer()`).
  - Added target user selection, batch record checkboxes, and action logging in `ci_admin_logs`.
- **Calling Center Dashboard**:
  - Added `manager/calls/dashboard` with call volume statistics, agent talk-time metrics, call disposition breakdowns, and recording player (`Ci_admin_calls`).
- **Trade License & Filter Options**:
  - Added Trade License filtering and expiration date range filters to lead search queries in `ci_admin_model.php`.
- **CRM Value Catalog**:
  - Created `value.md` cataloging term IDs, user role permissions, quote statuses, and database category constants.

---

### Reports Dynamic Drilldown, Admin Logs Status Filter & Route Stability
- **Date & Time**: `2026-10-17 18:30:00 +0530`

#### Changes & Additions:
- **Reports Pivot Table & Drilldowns**:
  - Updated `admin_reports_model.php` with dynamic drilldown filtering by month, department, vertical, and sales status.
  - Connected report cell values directly to `/manager/reports/drilldown` session queries.
  - Maintained CSV and Excel exports with complete column layouts.
- **Admin Logs Filtering**:
  - Added status filtering (`SUCCESS`, `FAILED`) and pagination to `manager/system/admin-logs`.
- **Route Error Handlers**:
  - Resolved fatal routing conflicts in CodeIgniter route definitions.

---

### CRM Update Checklist: Dashboard, Leads Add/Edit, Backend, Database & Workflow Overhaul
- **Date & Time**: `2026-10-16 15:55:00 +0530`

#### Changes & Additions:
- **Dashboard**:
  - Renamed "My Team Tasks" card header to **Team Tasks** while keeping both **My Tasks** and **Team Tasks** side-by-side on the manager dashboard (`application/views/manager/dashboard.php`).
- **Leads Add / Edit**:
  - Added **Quote Status** dropdown (`Not Given`, `Quote Given`, `Proposal Sent`, `Under Review`, `Revised Quote`, `Accepted`, `Rejected`).
  - Added **Quote Mode** dropdown (`Email`, `WhatsApp`, `SMS/Text`, `Phone Call/Verbal`, `Other`).
  - Added **Quote Amount** numeric field (`quote_amount`).
  - Renamed **Business Category** to **Industry Category** (`industry_category`) throughout the UI, with dual-write to `business_category` for seamless backward compatibility.
  - Added **Corporate Tax Status** dropdown (`Not Registered`, `Registered`, `Not Applicable`).
  - Added **VAT Status** dropdown (`Not Registered`, `Registered`, `Not Applicable`).
  - Added **Data Source** dropdown with configurable options (`Google Maps`, `GPS`, `Referral`, `Website`, `Social Media`, `Other`), populated dynamically from `ci_terms`.
  - Added **Data Batch Referral Number** text field (`data_batch_referral_no`).
  - Added **Database Category** dropdown (`New Database`, `Online`, `Reference`, `Ported Out`, `Competitor DB`, `Contract Agent`, `Raw / Unverified`, `Cleansed`, `High Value`, `Re-engaged`, `Other`), populated dynamically from `ci_terms`.
  - Renamed **Lost Reason** to **Status Category** (`status_category`), kept visible across all lead statuses with dynamic contextual reason presets (Lost, Invalid Entry, Database, etc.) and custom reason text entry.
  - Added **Status Remarks** multiline text area (`status_remarks`) for detailed rationale.
  - Retained all existing lead statuses, workflows, and database source configurations.
- **Database & Migrations**:
  - Added 11 database columns to `ci_leads`: `quote_status`, `quote_mode`, `quote_amount`, `industry_category`, `corporate_tax_status`, `vat_status`, `data_source`, `data_batch_referral_no`, `database_category`, `status_category`, and `status_remarks`.
  - Updated `migration_runner.php` with idempotent column additions using `SET SESSION innodb_strict_mode = 0;` and seeded default terms in `ci_terms` for `data_source` and `database_cat`.
- **Backend Controllers & Views**:
  - Updated `Ci_admin_leads::add()` and `Ci_admin_leads::edit()` to sanitize, validate, and persist all 11 fields.
  - Updated `Ci_admin_leads::export()` with all 11 new column headers and field exports.
  - Updated `Ci_admin_leads::import_action()` to map and import new fields from CSV files with header aliases and security cleansing.
  - Updated `Ci_admin_leads::download_sample_csv()` with all 11 columns and sample row data.
  - Updated `single_view.php` to display Industry Category, Tax & VAT status, Quote details, Data Source, Batch, and Status Category/Remarks.
  - Updated `leadsview.php`, `team/leads.php`, `assigned_leads.php`, and `followupsview.php` to display Status Category and Quote Status badges.
  - Verified end-to-end with automated database insert/update/retrieve tests.

---

### Tickets UI Alignment (Created By Header & Select Dropdown) & Open Tickets Renaming
- **Date & Time**: `2026-10-16 14:10:00 +0530`

#### Changes & Additions:
- **Operations > Renamed "Ticket Follow-up" to "Open Tickets"**:
  - Renamed menu item in `application/views/manager/elements/header.php` (both desktop menu and mobile sidebar) from `Ticket Follow-up` to **Open Tickets** (`manager/tickets/followups`).
  - Updated page heading and title to **Open Tickets** in `Ci_admin_tickets::followups()`.
  - Added route aliases `manager/tickets/open` and `manager/team/tickets`.
  - Maintained filtering to display exclusively open tickets with the identical tickets table UI.
- **Tickets Section & Team > Tickets (Created By Header & Select Dropdown Parity)**:
  - Added **Created By** select dropdown to the search filter toolbar in both `index.php` and `followups.php` allowing user selection.
  - Added **Created By** column to table header (`<th>Date</th><th>Created By</th>`), displaying the ticket creator/raiser's name (`creator_name`).
  - Added DataTables column footer select dropdown for column 9 (**Created By**) alongside Status (column 10), Department (column 4), Client Type (column 2), and Priority (column 5), matching the footer select behavior in `Team > Leads` (`team/leads.php`).
  - Linked `Team > Tickets` to `manager/team/tickets` in both desktop and mobile navigation.

---

### Operations, KAM Role Governance, Sticky Navbar, Leads Lost Reason & Dashboard Enhancements
- **Date & Time**: `2026-10-16 13:50:00 +0530`

#### Changes & Additions:
- **Operations > Raise Ticket**:
  - Replaced visible form fields for **Raised By** and **Created Date** with hidden inputs in `application/views/manager/tickets/add.php`.
  - Preserved backend auto-population of `created_by` and `created_date`/`created_at` in `Ci_admin_tickets::add()`.
  - Maintained clean layout alignment for Deadline Date, Expected Closure Date, Follow-up Date, and Time.
- **Operations > Ticket Follow-up**:
  - Filtered records to show only open tickets (`status = 'Open'`) in `application/controllers/ci_admin_tickets.php`.
  - Replaced follow-up interface with the exact Ticket View listing layout (`application/views/manager/tickets/followups.php`), including customer/company, client type, department, priority, assignees, overdue deadlines, and action dropdowns (Preview, Edit).
- **Operations > Ticket View**:
  - Preserved complete ticket listing displaying all tickets regardless of status in `Ci_admin_tickets::index()`.
- **Sticky Top Navigation Bar**:
  - Updated `.wrapper` in `assets/dist/css/crm-dashboard.css` with `overflow: visible !important; min-height: 100vh !important;`.
  - Configured `.main-header` with `position: sticky !important; top: 0 !important; z-index: 1040 !important;`.
  - Kept navigation visible across scrolling without breaking desktop dropdowns, user profile menu, dark mode toggle, or mobile drawer.
- **Team Navigation**:
  - Renamed `Ticket` submenu to **Tickets** in both desktop navigation and mobile offcanvas drawer in `application/views/manager/elements/header.php`.
- **System Role Management**:
  - Added **Sales** user role (`id = 8`, `title = 'Sales'`) in `ci_roles` alongside existing roles.
- **KAM Role Access Control & Governance**:
  - Admin (Role 1) and Support (Role 4): Granted full access to all KAM options (**My Client**, **Onboard Client**, **Create Schedule**, **Report**).
  - Back Office (Role 2): Granted access to KAM with **My Client** as the only visible and accessible option.
  - Sales (Role 8) and ordinary Users (Role 3): Completely hidden from navigation, with direct URL access blocked via HTTP 403 Forbidden in `application/controllers/ci_admin_kam.php`.
  - Implemented `my_client()` view and controller method with routes `manager/kam/my-client` and `manager/kam/my_client`.
- **Leads Add & Edit (Lost Reason)**:
  - Added **Lost Reason** field beside Status in `application/views/manager/leads/addleads.php` and `editleads.php`.
  - Conditioned field visibility and requirement strictly to `Lost` status (Status ID 43) via dynamic JavaScript toggling.
  - Enabled selection of predefined reasons or entry of custom reasons.
  - Persisted `lost_reason` in `ci_leads` database table in `Ci_admin_leads::add()` and `edit()`.
  - Displayed `lost_reason` in `application/views/manager/leads/single_view.php` and `leadsview.php`.
  - Included **Lost Reason** column in CSV export in `Ci_admin_leads::export()`.
- **Dashboard Projections & Operational Breakdown Status Exclusions**:
  - Excluded leads with `Activated` (42), `Lost` (43), or `Invalid Entry` (44) from monthly sum subqueries and count queries in `dashboard_MyPro()` and `dashboard_TeamPro()` in `application/models/ci_admin_model.php`.
  - Excluded statuses 42, 43, 44 from `dashboard_closures()` and `dashboard_vertical_fu()`.
  - Excluded statuses 42, 43, 44 when filtering by `closure` and `vertical_fu` in `get_Myleads()` and `getTeamLeads()`.
  - Corrected date range order in `getLeadsMeetings()` for last 7 days (`between '$last_date' AND '$today'`).
- **Dashboard Operational Activity and Breakdown Cards Restored**:
  - Restored **My Tasks** and **My Team Tasks** cards to Operational Activity and Breakdown in `application/views/manager/dashboard.php`.
  - Implemented `dashboard_tasks($type)` in `ci_admin_model.php` querying `ci_tasks` for missed, last 7 days, today, next 7 days, future, and total counts.
  - Updated `ci_admin_tasks.php` `followups()` to support `type` (1 to 5) and `team` parameters.
  - Organized Operational Activity into 5-card responsive rows using `.crm-row-5cols` and `.crm-col-5` (5 cards in My section: My Tasks, My Followups, My Meetings, My Closures, My Vertical FU; 5 cards in Team section: My Team Tasks, Team Followups, Team Meetings, Team Closures, Team Vertical FU).
- **Code Standards**:
  - Strict compliance with zero inline comment policy across all code files (PHP, JS, CSS).

---

### [Commit 4fe6960] — Consolidate Task and Ticket into Operations and Add KAM Navigation Section with Blank Pages
- **Commit Hash**: `4fe6960` (`4fe69606819fe22ab0f9a12ea73c9ceca148da2c`)
- **Tree Code**: `a2f800a72f4fd0a615d6d0d8657bd9c7039a441d`
- **Author**: Reinhart-py
- **Date & Time**: `2026-10-15 18:44:57 +0530`

#### Changes & Additions:
- **Consolidated Operations Navigation Section**:
  - Combined separate Task and Ticket menu entries into a unified **Operations** navigation section in both desktop navbar (`.munk_menu`) and mobile offcanvas drawer (`.sidebar-menu`) in `application/views/manager/elements/header.php`.
  - Arranged dropdown options in the exact order requested:
    1. **Add Task** (`manager/tasks/add`)
    2. **Task View** (`manager/tasks`)
    3. **Task Follow-up** (`manager/tasks/followups`)
    4. **Raise Ticket** (`manager/tickets/add`)
    5. **Ticket View** (`manager/tickets`)
    6. **Ticket Follow-up** (`manager/tickets/followups`)
- **New KAM Navigation Section**:
  - Added new top-level **KAM** (uppercase) navigation section in both desktop navbar and mobile drawer.
  - Arranged dropdown options in the exact order requested:
    1. **Onboard Client** (`manager/kam/onboard`)
    2. **Create Schedule** (`manager/kam/schedule`)
    3. **Report** (`manager/kam/report`)
- **KAM Controller & Blank Views**:
  - Implemented `Ci_admin_kam` controller (`application/controllers/ci_admin_kam.php`) handling `onboard()`, `schedule()`, and `report()` methods with session verification and clean structure.
  - Configured matching routes in `application/config/routes.php` (`manager/kam/onboard`, `manager/kam/schedule`, `manager/kam/report`).
  - Created `application/views/manager/kam/onboard.php` adopting the exact layout and table design of the Leads section (date pickers, MRR range, search filter, table headers, and action menu) without connecting to or modifying the leads database.
  - Created `application/views/manager/kam/schedule.php` and `report.php` as clean blank AdminLTE views.
  - Preserved strict zero-schema-change policy with no new database tables or backend modifications.
  - Maintained zero inline code comments across all PHP files.

---

### [Commit 5d21809] — Remove Header Banner Clutter from Admin Logs and Integrate Online/Offline into Status Filter and Column
- **Commit Hash**: `5d21809` (`5d2180949d2146e49226d9c6ca1087cf23709b8e`)
- **Tree Code**: `d5c5f7ec763317d87631311d97043dc6cb9fb510`
- **Author**: Reinhart-py
- **Date & Time**: `2026-10-15 18:07:42 +0530`

#### Changes & Additions:
- **Admin Logs Header Clutter Removed**:
  - Removed the custom summary card banner (`System User Sessions: 1 Online Now | 131 Total Users`) from `application/views/manager/system/admin_logs.php` to maintain a clean AdminLTE interface matching `Team > Leads`.
- **Status Filter & Column Integration**:
  - Integrated `Online` and `Offline` user state directly into the native **Status** dropdown filter (`Online`, `Offline`, `SUCCESS`, `FAILED`) instead of displaying intrusive session cards in the header.
  - Updated `application/controllers/ci_admin_system.php` to cleanly filter logs by user online/offline status when selected from the Status dropdown.
  - Positioned the `Online` / `Offline` status badge inside the table's **Status** column alongside the action outcome badge (`SUCCESS`, `FAILED`, `TIMEOUT`), keeping the **User** column clean with username only.

---

### [Commit 6de47b2] — Normalize Company Dropdown Items for String and Array Models
- **Commit Hash**: `6de47b2` (`6de47b209d84639b7d5bead45c110996841072aa`)
- **Tree Code**: `afcd71d34729b80bc8d21172db535b72edf7b069`
- **Author**: Reinhart-py
- **Date & Time**: `2026-10-15 18:01:15 +0530`

#### Changes & Additions:
- **Company Dropdown Rendering Normalization**:
  - Fixed company list iteration in `application/views/manager/tickets/add.php`, `edit.php`, and `index.php` to seamlessly support both flat string arrays returned by `ci_admin_model->get_all_companies_dropdown()` and associative arrays.
  - Eliminated PHP 8+ string offset type errors, ensuring the entire Raise Ticket form, Edit Ticket form, and View Tickets filter toolbar render without interruption.

---

### [Commit fc4f813] — Complete Field Suite for Tickets with Add Lead UI Styling and Clean Codebase
- **Commit Hash**: `fc4f813` (`fc4f813a1e473c1ea71bab40e53272e18b1ba822`)
- **Tree Code**: `4d6e3754f5da7d4ae30ab1a74c648ed1e58bfb8b`
- **Author**: Reinhart-py
- **Date & Time**: `2026-10-15 17:52:34 +0530`

#### Changes & Additions:
- **Complete Ticket Field Suite Restored in Raise Ticket & Edit Ticket Views**:
  - Re-integrated all required customer contact, classification, assignment, and scheduling fields into `application/views/manager/tickets/add.php` and `edit.php`:
    - **Client & Company Information**: Client Type (`Onboarded` / `Non-Onboarded`), Company Name (searchable DB dropdown from all companies), POC Name, POC Contact Number, Alternative Contact Number, Email, City / Location, and Ticket Source (`Internal Request`, `Phone Call`, `Email`, `WhatsApp`, `Client Portal`, `Direct / Meeting`).
    - **Ticket Classification**: Department (`Sales`, `Back Office`, `Operations`, `IT`, `HR`, `Support`, `Accounts`), Ticket Type (`Bug / Technical Issue`, `Feature Request`, `System Access / Permission`, `Billing / Invoice Inquiry`, `Client Query / Onboarding`, `Operational Support`, `Hardware / Equipment`, `General Inquiry`, `Escalation`), Category (`Software`, `Hardware`, `Network`, `Lead Management`, `Calling System`, `CRM Access`, `Data Correction`, `Attendance / HR`, `Security / Breach`, `Other`), Priority (`Low`, `Medium`, `High`, `Urgent`), Status (`Open`, `Assigned`, `In Progress`, `Pending`, `On Hold`, `Resolved`, `Closed`), and Ticket Title.
    - **Assignment & Deadlines**: Multi-user assignment (`assigned_users[]`), Notify / Tag Users (`tagged_users[]`), Raised By, Created Date, Last Updated By (in Edit), Last Updated Date (in Edit), Deadline Date, Expected Closure Date, Initial Follow-up Date, and Initial Follow-up Time.
    - **Details & Attachments**: Detailed Description, Internal Staff Notes, and File Attachment.
  - Native AdminLTE box styling matching `views/manager/leads/addleads.php` without custom SVG icons, colorful borders, or clutter.
- **Codebase Cleanliness & Comment Removal**:
  - Removed all inline comments, explanatory developer notes, guide comments, and AI-like annotations across all PHP controller and view files (`ci_admin_tickets.php`, `add.php`, `edit.php`, `index.php`, `view.php`, `followups.php`).
- **Ticket Preview & Audit History Enhancement**:
  - Enhanced `application/views/manager/tickets/view.php` to display all client POC contacts, location, ticket source, assignment, and deadline details in the 2-column key-value tables matching `views/manager/leads/single_view.php`.

---

### [Commit 212f501] — Align Raise Ticket to Add Lead UI, View Tickets to Team Leads UI, and Preview Ticket to Lead Preview UI
- **Commit Hash**: `212f501` (`212f50125142d08d26634f72c3551f0301550d1e`)
- **Tree Code**: `f5c9a62fe079f2842f017be714c4028e3c39ad8e`
- **Author**: Reinhart-py
- **Date & Time**: `2026-10-15 17:36:05 +0530`

#### Changes & Additions:
- **Raise Ticket & Edit Ticket UI Matched to Add Lead (`addleads.php`)**:
  - Re-architected `application/views/manager/tickets/add.php` and `edit.php` to replicate the clean native AdminLTE form structure of `views/manager/leads/addleads.php`.
  - Removed all custom SVG icons, colorful border highlights, and decorative design clutter.
  - Form organized cleanly into standard `.box.box-default` panels:
    - *Ticket Information*: Client Type, Company Name (searchable DB dropdown), Department, Ticket Type, Category, Priority, Status, Deadline Date, Expected Closure Date, and Ticket Title.
    - *Assignment & Notifications*: Multi-user assignment (`assigned_users[]`) and user notification tagging (`tagged_users[]`).
    - *Details & Attachments*: Description textarea, optional internal notes, and file attachment.
  - Centered box footer with standard CRM buttons: Submit (`btn-info`) and Cancel (`btn-default`).
- **View Tickets Table & Filter Toolbar Matched to `Team > Leads` (`team/leads.php`)**:
  - Replaced custom dashboard stat cards with the clean standard `team/leads.php` layout.
  - Filter toolbar structured inside `div.box.p-4` with `x_row`/`x_col` and `form.form-inline.head_filter`:
    - Date range (`Create From` to `Create To`) using `.input-daterange.input-group` and `.datepicker`.
    - Standard `.select2` dropdowns for Client Type, Company, Department, Ticket Type, Priority, Status, and Assigned User.
    - Text search input for ticket ID/title/company keyword search.
    - Standard `Search` (`btn-success`) and `Reset` (`btn-danger`) buttons, with `Raise Ticket` (`btn-success`) button.
  - Standard DataTables table (`#example_lead`) with columns: `Ticket #`, `Customer / Company`, `Client Type`, `Title`, `Department`, `Priority`, `Assigned To`, `Deadline`, `Created`, `Status`, and `Action`.
  - Implemented dynamic column footer dropdown filters (`initComplete`) matching `team/leads.php`.
- **Ticket Preview Page Matched to `Team > Leads > Action > Preview` (`single_view.php`)**:
  - Restructured `application/views/manager/tickets/view.php` to follow the exact profile card and table structure of `views/manager/leads/single_view.php`.
  - Top action header with Back button (`btn-info pull-left`) and Edit Ticket button (`btn-danger pull-right`).
  - `.my-profile` container with standard 2-column key-value tables for all ticket details, followed by full-width rows for Description, Internal Notes, Attachment download, and Resolution Remarks.
  - Clean AdminLTE `.box.box-default` panels for Update Status & Progress (assigned users), Support Review & Ticket Closure (Support/Admin only with review confirmation), Multiple Follow-up History table with Add Follow-up form, and Complete Ticket History audit log.
- **Action Menu Streamlining**:
  - Standardized the table Action dropdown in `View Tickets` to mirror `team/leads.php`:
    - `Preview` (direct link to ticket preview view)
    - `Edit` (direct link to edit ticket view)

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
