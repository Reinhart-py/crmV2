# Seven Strokes CRM: Current Work (`current_work.md`)

> Active state tracking. Updated before switching tasks or ending a turn.

---

## Current Active Task
  23. Dedicated Dialer Page Modular Architecture & Clean Theme (`application/views/manager/calls/dashboard.php`, `application/controllers/ci_admin_calls.php`).
  24. Dialer Click Responsiveness & Modern Navigation Pills (`dashboard.php`, `ci_admin_calls.php`).
  25. Role Permission Fix, White Sleek 3CX Softphone UI, Team Colleague Dialer & Status Reports (`dashboard.php`, `ci_admin_calls.php`, `ci_admin_model.php`, `header.php`).
  26. Roles & Permissions Switcher (`manager/roles`): Segmented toggle between Role-Wise and User-Wise permissions, live user selector, Role Default / Full Admin / Custom modes, role preset loader, and bulk action toggles (`ci_admin_roles.php`, `roles/index.php`, `ci_admin_model.php`).
  27. Sales Assignment from KAM to Operations (`manager/orders`): Assign to Operations workflow with instructions, enabling assigned Operations staff to view, edit, update status, and add notes (`ci_admin_orders.php`, `orders/view.php`).
  28. Task Follow-Up Live Connectivity (`manager/kam/task_followup`): Connected to live assigned tasks in `ci_tasks` with dual-box layout (`ci_admin_kam.php`, `kam/task_followup.php`).
  29. Admin Logs Default Sorting (`manager/system/admin_logs`): Sorted by latest descending by default (`order: [[0, "desc"]]`) without announcement banners (`admin_logs.php`).
  9. User Profile Photo uploading, storage in `assets/uploads/avatars/`, SVG fallback, and navbar avatar sync (`ci_admin.php`, `adminprofile.php`, `profile.php`, `header.php`).
  10. Auto-Dialer sequential calling with 60-second wrap-up gap, pause/resume/extend/stop controls, and session state persistence (`ci_admin_calls.php`, `dashboard.php`).
  11. 3CX CDR & Recording webhooks with Groq Whisper post-call speech transcription (`whisper-large-v3-turbo`) and structured conversation analysis (`openai/gpt-oss-20b`, fallback `openai/gpt-oss-120b`, temperature=0) (`Groq_service.php`, `ci_admin_calls.php`).
  12. Lead Timeline Call History & Post-Call AI Intelligence view with audio player, collapsible transcript, sentiment, next action, and Regenerate AI Summary button (`single_view.php`).
  13. Third-Party API Integrations unified settings management with genuine connection testing for Groq, 3CX, Email SMTP, WhatsApp, Cron, and Helpdesk (`ci_admin_integrations.php`, `integrations.php`, `header.php`, `routes.php`).
  14. Audit bug fixes (duplicate jQuery scripts removed, password types confirmed).
  15. Verified zero comments in all added/modified PHP files and zero syntax errors with `php -l`.
  16. Standardized Orders and Sales UI to View Database (`leadsview.php`) dual-box layout with clean search filters and top headers.
  17. Scoped My Zone Orders (`manager/orders/my`) and Team Orders (`manager/team/orders`) to View-only action, while Back Office retains View, Make Changes, and Accept.
  18. Restored Vertical Follow-Up in My Zone navigation below View Vertical.
  19. Full Dashboard red-to-blue theme migration (`#0072f0` / `#0084ff`), updating cards, icons, borders, progress bars, dark mode elements, and adding css cache buster query string.
  20. Restored complete Team navigation dropdown options in both top navbar and mobile drawer: Members, Chart, Database, Leads, Orders, Verticals, Follow up, Meetings, Assigned Meetings, Assign Database, Task, Tickets (`header.php`).
  21. Rebuilt Pending Order Process (`manager/orders/pending_process`) to match View Database UI with dual-box layout, inline search filters, and DataTables table (`pending_process.php`, `ci_admin_orders.php`).
  22. Unified Dashboard and Phone Dialer UI layout optimization (`dashboard.php`, `calls/dashboard.php`, `crm-dashboard.css`, `header.php`):
      - Database State & Team Database State displayed side-by-side in Row 1.
      - Lead State & Team Lead State positioned directly below them in Row 2.
      - All four state sections permanently visible at once with no arrow buttons or extra pages.
      - My Projection & Team Projection displayed side-by-side in Row 1; My Achievement & Team Achievement placed directly below them in Row 2. All four graphs permanently visible simultaneously without arrow buttons, sliding, or swap animations.
      - Operational Activity & Breakdown display all 14 cards together in 2 rows (7 cards in Row 1, 7 cards in Row 2) with proportionate sizing, clean typography, zero arrow buttons, zero sliding animations, and full backend data connectivity.
      - Phone Dialer UI sized compactly, positioned flush to the left screen edge with no dead space, removed redundant top-left phone icon from sidebar dock, and preserved all 3CX calling functions, auto-dialer, and CRM Royal Blue styling.
  23. Dedicated Dialer Page Modular Architecture & Clean Theme (`application/views/manager/calls/dashboard.php`, `application/controllers/ci_admin_calls.php`):
      - Clean solid dark CRM theme (`#0b0f19`) replacing background image wallpaper completely.
      - Fixed extreme-left menu dock with Follow-up, Database, Leads, Logs, and Exit.
      - Fixed extreme-right dialer panel with phone input, DTMF keypad, call toggle, skip button, and active contact chip.
      - Middle content area hidden by default on initial page load; opens dynamically only when an option/contact is selected.
      - Follow-up menu with submenus (Missed, Today, Last 7 Days, Next 7 Days, All) connected to dashboard follow-up query logic.
      - Database menu with Manual Dialer and Auto Dialer (60s countdown gap, Pause/Resume/Stop/Extend/Skip controls, and status filtering).
      - Leads menu with status filtering and stage separation.
      - Full editable database record fields: Company Name, POC, Primary Phone, Alt Phone, Email, Authority, Emirate, Address, Location, DNC/DNCR status (Yes/No) with conditional DNCR date input, Service Category, Product Category, Product Details, Trade License Number, Industry Category, Quantity, MRC, Revenue, MRR (auto-computed), Quote Status, Quote Mode, Quote Amount, Status, Status Category, Status Remarks, Exp. Close Date, Remarks, and Follow-up Schedule Date & Time.
      - Logs menu with agent internal phone/extension info, 30-day daily summary metrics (total, connected, missed, talk time), date-grouped call activity, and individual call records with 1-click Call and Copy number actions.
      - Strictly modified ONLY the dialer page files and endpoints. Zero comments in PHP files and zero syntax errors.
  24. Dialer Click Responsiveness & Modern Navigation Pills (`dashboard.php`, `ci_admin_calls.php`):
      - Fixed Follow-up and Database dock button clickability: added direct `onclick` handlers on `<button>` elements (`dockBtnFollowup` and `dockBtnDatabase`) so clicking either button immediately loads the queue and opens the middle panel.
      - Removed dock overflow clipping (`overflow: visible !important`) so hover flyout menus are never truncated by the left dock container.
      - Adopted modern subnav pill navigation (inspired by `DialerJazz` and `matthewdonsemail-lab/dialer` UI patterns) directly inside the middle content header:
        - Follow-up filter pills: `[Today (badge)]`, `[Missed (badge)]`, `[Last 7d]`, `[Next 7d]`, `[All]`.
        - Database dialer mode pills: `[Manual]`, `[Auto-Dial (60s)]`.
      - Connected pill clicks to `openMenuQueue()` with instant visual `.active` state switching.
      - Auto-updated badge counts for Today and Missed on both dock flyouts and header subnav pills.
      - Verified 0 PHP comments and 0 syntax errors across all files.
  25. Role Permission Fix, White Sleek 3CX Softphone UI, Team Colleague Dialer & Status Reports (`dashboard.php`, `ci_admin_calls.php`, `ci_admin_model.php`, `header.php`):
      - Resolved dialer visibility for User role: updated `ci_admin_model.php`'s `check_permission` so section `'calls'` is always accessible to all authenticated users, updated `ci_roles` to ensure Role 3 (User) has `"calls": "write"`, and added Phone Dialer link to mobile drawer in `header.php` so it is available across all pages and viewports.
      - Sleek White 3CX Softphone Theme: Replaced dark/black styling with crisp, modern pure white (`#ffffff` / `#f8fafc`) aesthetic, high contrast bold numerals (`#0f172a`, font-weight: 800), and green call action button (`#16a34a`) matching the uploaded screenshot.
      - Team Colleague Dialer (Top Button): Placed Team button at the very top of the left dock (above Follow-up) with S5 avatar pill and online status dot. Connects to `ajax_get_team_colleagues` querying active colleagues from `ci_user` with extension/phone info and 1-click Call action.
  26. Zero-Clutter Rules & Apple-Style Softphone Refactor (`.agents/rules/crm_rules.md`, `calls/dashboard.php`, `roles/add.php`, `roles/edit.php`, `ci_roles`):
      - Updated `crm_rules.md` permanently banning informational/tutorial alert banners (no captions, no helper callouts) and decorative pill badges/counters in headers and toolbars.
      - Purged blue explanatory alert banner (`Internal Colleague Calls...`) and all pill counter badges (`131 colleagues`, `0 contacts`, etc.) from dialer.
      - Rebuilt softphone and dialer workspace to Apple-grade sleek standards: SF Pro typography, light gray Apple canvas (`#f2f2f7`), pure white cards with hairline dividers (`#e5e5ea`), native Apple Segmented Controls (`Day | Week | Month | Year`, `Today | Missed | Last 7d | Next 7d | All`), circular iOS phone keypad buttons (`64px x 64px`, digit with sub-letters), and circular Apple green call button (`#34c759`).
      - Consolidated User and Sales roles in `ci_roles`: migrated all users assigned to role 3 to "Sales" (`UPDATE ci_roles SET title = 'Sales' WHERE id = 3; DELETE FROM ci_roles WHERE id = 8`), so there is a single unified "Sales" role with full dialing, CRM, and team access.
      - Added "Phone Dialer & Calling" (`calls`) to permissions matrix across `roles/add.php` and `roles/edit.php` so administrators can configure dialer permissions.
      - Verified strictly ZERO comments across all PHP files and zero syntax errors with `php -l`.
      - Internal Calls Exclusion: Colleague calls are saved with `is_internal = 1` and `call_category = 'internal'` and are strictly excluded from sales reports.
      - Simplified calling modes: Defaulted to manual dialing; auto-dialer simplified for future AI agentic calls.
      - Comprehensive Calling Report Sheet: Range filters (Day, Week, Month, Year), key summary cards (Total Outbound, Connected, New Client Calls, Follow-ups, Talk Time), compact Status Breakdown table (Status name, New Calls, Follow-ups, Total Calls, % Share), and recent outbound call history with 1-click Call & Copy.
      - Verified 0 PHP comments and 0 syntax errors across all modified files.
  27. Achievement Graph Header Simplification (`application/views/manager/dashboard.php`):
      - Removed repetitive text/legend suffixes (`vs Target`, `■ Target ■ Met ■ Below`) from both Achievement chart headers as requested.
      - Simplified headers to clean, Apple-minimalist titles: `My Achievement` and `Team Achievement`.
  28. Add Database New Company Input (`application/views/manager/leads/add-database.php`):
      - Added toggleable inline input allowing users to select an existing company from Select2 or click "Add New Company" to type in a brand new company name if it does not yet exist.
  29. Full Sales Workflow Testing & Route Bug Fixes (`ci_admin_orders.php`, `ci_admin_model.php`):
      - Fixed SQL query syntax bug in `ci_admin_orders.php` line 75 (`1 = 0` escaping issue in CodeIgniter query builder).
      - Fixed column mismatch in `ci_admin_model.php` `get_user_notifications` (`conversion_requested_by` -> `action_by` and `ci_orders_logs` -> `ci_order_history`).
      - Verified all 28 Sales routes return HTTP 200 OK.
  30. Real-time Multi-Department Cross-Device Notification System (`header.php`, `ci_admin_model.php`):
      - Added `notify_role` and `notify_users` backend helpers for cross-department updates between Back Office, KAM, and Sales.
      - Implemented native HTML5 Web Notifications API (`new Notification(...)`) that surfaces updates on laptops/desktops and Android mobile browsers.
      - Added Web Audio API synthesizer chime (smooth sine-wave chime at 587.33Hz -> 880Hz) on new unread notifications.
      - Reduced background notification polling interval to 20 seconds for near real-time updates.
  31. Comprehensive HR Management Suite (`ci_admin_hr.php`, `views/manager/hr/index.php`, `header.php`, `routes.php`, `ci_roles`):
      - Built hybrid Frappe HRMS + Horilla HR system with Apple-style sleek UI, clean tabbed navigation, and zero extra explanatory banners.
      - Role-gated navigation: HR menu item visible only to HR (Role 7), Admin (Role 1), and users with granular `hr` permissions.
      - Gated backend access via `_check_hr_access()` in `ci_admin_hr.php`. Non-HR users are redirected to dashboard.
      - Schema migrations executed: Geolocation columns added to `ci_attendance` (`latitude`, `longitude`, `accuracy`, `location_address`, `clock_in_ip`, `clock_out_ip`), and created `ci_hr_targets`, `ci_hr_shifts`, `ci_hr_shift_assignments`, `ci_hr_payroll`, `ci_hr_taxation`, `ci_hr_appraisals`, `ci_hr_lifecycle`, and `ci_hr_documents`.
      - Feature Suite:
        1. **Assign Targets**: Monthly/Quarterly targets for Revenue, Calls, Leads, and Meetings with live progress bars and real achievement calculation from `ci_orders` (Sales status) and `ci_call_logs`.
        2. **Employee Lifecycle**: Directory with lifecycle stages (Onboarding, Probation, Confirmed, Promotion, Transfer, Notice, Relieved) with quick modal editor.
        3. **Attendance with Geolocation**: Live HTML5 GPS Geolocation Clock-In / Clock-Out (`navigator.geolocation`) storing exact lat/lng/accuracy with Google Maps link, total working hours, and status.
        4. **Shifts Management**: Shift definitions with timings and grace periods, plus employee shift assignment.
        5. **Leave Management**: Leave requests (Annual, Sick, Casual, Maternity, Unpaid) with 1-click Approve / Reject workflows.
        6. **Payroll & Compensation**: Monthly automated payroll calculation (Basic, Allowances, Gross, Deductions, Net Pay), 1-click Mark as Paid, and disbursement tracking.
        7. **Taxation & UAE Gratuity Benefits**: Built-in interactive UAE Labour Law Gratuity calculator for limited and unlimited contracts, plus corporate/personal tax summaries.
        8. **Performance Appraisals**: Quarterly/annual review cycles, 1-5 star ratings, 0-100 KPI scores, and reviewer feedback.
        9. **Document Vault (Horilla feature)**: Secure tracking for Passports, Visas, Emirates ID, and Labour Contracts with automated expiry calculation and status indicators.
      - Verified zero comments in all PHP files and zero syntax errors with `php -l`.
  32. Report Grid Layout, HR Clean Navigation, and Staff Live Location Tracking (`header.php`, `download_report.php`, `hr/index.php`, `ci_admin_hr.php`, `ci_admin_system.php`, `admin_logs.php`, `live_location.php`, `routes.php`):
      - Renamed "Download Reports" to "Report" in desktop navigation and mobile drawer.
      - Transformed Download Reports layout into a 2x2+1 grid (`display: grid; grid-template-columns: repeat(2, minmax(0, 1fr))`) rendering 2 up, 2 below, and next left.
      - Removed duplicate in-page sub-navigation tabs (`.hr-nav-tabs`) from the HR management suite, routing all HR views directly through the top navbar and mobile drawer.
      - Aligned database fields across HR tables and fixed MySQL error 1054 (`U.phone` -> `U.c_mobile`).
      - Added direct Staff Live Location tracking to Admin Logs (`manager/admin-logs` -> Staff Live Location), querying real database records from `ci_user`, `ci_user_live_locations`, and `ci_attendance` with 1-click Google Maps links.
      - Synchronized HR Clock-In / Clock-Out (`ajax_clock`) with `ci_user_live_locations` and `ci_admin_logs`.
      - Verified all 13 HR, Report, and Admin routes return HTTP 200 OK cleanly.
      - Verified strictly ZERO comments across all modified PHP files.
  33. iOS Notification Centre, Universal Gratuity / Severance Calculator & 100% Full Width HR Layout (`header.php`, `hr/index.php`, `ci_admin_model.php`):
      - Redesigned navbar notifications into an Apple iOS-style Notification Centre with 440px width (`max-width: calc(100vw - 24px)`), rounded-square icon badges, and full multi-line wrapped text (`white-space: normal; word-break: break-word`) eliminating all truncation ellipsis.
      - Removed UAE-only hardcoding and converted HR into a Universal Gratuity & Severance Calculator with dynamic preset standards:
        - Universal Standard (Custom Days/Year)
        - India (Payment of Gratuity Act, 1972: 15/26 days per year with statutory cap)
        - UAE Labour Law (Decree Law No. 33: 21 days first 5y, 30 days after)
        - Custom Multiplier
      - Added Currency Selector (`INR ₹`, `USD $`, `AED`, `EUR €`, `GBP £`, `SAR`, `SGD $`, `CAD $`) with live formula breakdown and currency formatting.
      - Converted Document Vault to universal global identification: National ID (Aadhaar / Emirates ID / SSN / PAN), Passport, Work Permit / Visa, Employment Contract, Tax Documents (Form 16 / PAN), Educational Certificates, and Bank/Payroll Docs.
      - Expanded HR management view to 100% viewport width: removed restrictive Bootstrap `.container` (`1170px`) wrapper so all HR tabs render at comfortable 100% screen size matching View Database (`leadsview.php`).
      - Resolved database error 1054 in `get_user_notifications`: corrected `order_request_status` to target `ci_pipeline` and `ci_admin_logs` column to `activity`.
      - Verified 0 comments across all PHP files and zero syntax errors with `php -l`.

---

## Active Environment & Configuration Required
- **Environment Variables**:
  - `GROQ_API_KEY`: Groq API key for speech-to-text and AI analysis.
  - `GROQ_CHAT_MODEL`: (Optional, default `openai/gpt-oss-20b`).
  - `GROQ_TRANSCRIPTION_MODEL`: (Optional, default `whisper-large-v3-turbo`).
- **Database Tables Verified**:
  - `ci_orders`, `ci_order_history`
  - `ci_call_logs` (extended with all AI and recording columns)
  - `ci_dialer_sessions`
  - `ci_events`, `ci_awards`
  - `ci_integration_settings`
  - `ci_user.avatar`
- **Git Remote Constraints**:
  - Strictly `origin` (`https://github.com/Reinhart-py/crmV2.git`). Never push to `upstream`.
