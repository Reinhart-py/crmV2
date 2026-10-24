# Seven Strokes CRM: Current Work (`current_work.md`)

> Active state tracking. Updated before switching tasks or ending a turn.

---

## Current Active Task
- **Completed**:
  1. Dashboard Task/Ticket Personal vs Team Separation (`ci_admin_model.php`).
  2. Dashboard Projections restricted to qualified Leads (`ci_admin_model.php`).
  3. Review Lead Queue & Conversion Permissions workflow (`ci_admin_leads.php`, `single_view.php`, `leadsview.php`).
  4. Orders Module with client-centric grouping and Back Office navigation (`ci_admin_orders.php`, `views/manager/orders/`).
  5. KAM Schedule decoupled from hardcoded telecom presets and renamed to Client Schedule Matrix (`ci_admin_kam.php`, `schedule.php`).
  6. Revenue List & Company Projections outside KAM navigation (`ci_admin_report.php`, `revenue_list.php`, `company_projections.php`).
  7. Events Calendar & Employee Awards Leaderboard (`ci_admin_events.php`, `views/manager/events/`).
  8. Granular Role Permissions matrix expansion across core CRM sections (`ci_admin_roles.php`, `ci_admin_model.php`, `add.php`, `edit.php`).
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
