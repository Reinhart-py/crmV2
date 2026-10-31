# Seven Strokes CRM Platform (crmV2)

[![PHP Version](https://img.shields.io/badge/PHP-7.4%20--%208.2-777bb4?logo=php)](https://www.php.net/)
[![CodeIgniter](https://img.shields.io/badge/CodeIgniter-3.x-ee4326?logo=codeigniter)](https://codeigniter.com/)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%20--%208.0-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![License](https://img.shields.io/badge/License-Proprietary-0072f0.svg)](#license)

**Seven Strokes CRM** is an enterprise-grade commercial operations platform engineered for sales teams, corporate service providers, key account managers, HR administrators, and executive leadership. Built with an Apple-style minimalist aesthetic, the CRM delivers fast response times, zero visual clutter, real-time telephony integration, and unified workflow management across desktop and mobile devices.

---

## CRM Workflow

The CRM workflow moves smoothly from initial prospect outreach to long-term account management in a single connected pipeline: sales agents discover prospects or dial them through the auto and manual phone dialers, record notes, and schedule follow-ups; when a prospect agrees to move forward, the Back Office verifies and approves the qualified lead, prompting the sales rep to create an order which locks the prospect record to prevent accidental changes; once the order is finalized and approved as a completed sale, the client is onboarded to Key Account Management (KAM) where dedicated account managers monitor trade license expiries, corporate tax and VAT deadlines, and ongoing service schedules while leadership tracks real-time revenue and live operational stats across the dashboard.

---

## Core Modules & Capabilities

### 1. Dashboard
- **Executive Analytics**: Real-time KPI summary cards displaying counts and financial values for Database, Leads, Orders, and Closed Sales.
- **Royal Blue Theme**: Consistent corporate styling (`#0072f0` / `#0084ff`) across cards, indicators, and charts.
- **Projections & Achievements**: Dynamic visual projections showing month-to-date and quarter-to-date progress against quotas.
- **Operational Activity**: Breakdown section detailing calling volume, follow-up execution, and stage conversions.

### 2. My Zone (Personal Commercial Operations)
- **Database & Leads**: Add Database, View Database, View Leads with structured dual-box filtering.
- **Orders & Sales**: View Orders (view-only personal deal pipeline) and View Sales.
- **Calendars & Meetings**: My Follow-Up (today, missed, 7-day windows) and My Meetings.
- **Verticals & Tasks**: Add/View Verticals, Vertical Follow-up, Internal Tasks, External Tasks, and Database Transfer.

### 3. Team Management
- **Interactive Hierarchy Chart**: Sleek tree diagram supporting upward navigation to the direct manager and downward expansion to team reportees with responsive zoom controls.
- **Team Activity Monitoring**: Real-time visibility into team databases, leads, orders, verticals, follow-ups, meetings, and tickets.
- **Database Assignment**: Bulk database distribution to sales team members with capacity balancing.

### 4. Back Office
- **Pending Leads Approval**: Verification gate for converting raw database records into qualified leads.
- **Pending Order Process**: Review submitted orders, audit pricing, verify documents, and accept or reject.
- **Pending Sales Approval**: Verification of completed orders before finalizing into revenue-generating sales records.
- **Client Onboarding**: Register client legal profiles, trade license numbers, Emirates, and jurisdiction details.
- **Comprehensive Order Pipelines**: Separate queues for Open, Follow-up, Completed, Closed, and Rejected/Lost orders.

### 5. Phone Dialer & Calling Engine
- **Cross-Device Responsiveness**: Fully responsive layout adapting across Android smartphones, tablets, laptops, and wide monitors.
- **Layout Architecture**: Horizontal dock on mobile / vertical sidebar on desktop, center record configuration work area, and right-side keypad softphone.
- **Calling Queues**: Streamlined 4-stage follow-up queues in exact sequence:
  1. Last Seven Days
  2. Today
  3. Next Seven Days
  4. All Future
- **Telephony Features**: WebRTC / 3CX integration, live duration tracking, dual contact dialing, instant call notes, and Groq AI call analysis.

### 6. Live Operations Section
- **Pivot Sheet Table Layout**: Clean tabular layout displaying My Stats, Team Stats, and Performance Recognition.
- **Distinct Color Coding**:
  - **Today**: Emerald Green tint (`#ecfdf5` header, `#f0fdf4` cells)
  - **Last 7 Days**: Warm Amber tint (`#fffbeb` header, `#fefce8` cells)
  - **This Month**: Royal Blue tint (`#eff6ff` header, `#f0f9ff` cells)
  - **This Quarter**: Soft Purple tint (`#f5f3ff` header, `#faf5ff` cells)
- **Silent Real-Time Sync**: Background polling every 10 seconds updates values without page reload.
- **Presentation TV Mode**: Full-screen presentation view for commercial display boards.

### 7. Key Account Management (KAM)
- **Client Portfolio**: My Client directory, corporate schedules, task follow-ups, reminders, and performance reports.
- **Service Schedules**: Automated tracking of trade license renewals, corporate tax filings, and VAT submission deadlines.
- **Client Assignee Management**: Reassign corporate accounts across account managers with complete history tracking.

### 8. HR Operations
- **People Operations**: Target assignment, employee lifecycle tracking, daily attendance, GPS geofenced check-ins, and shift management.
- **Leave & Payroll**: Leave approvals, holiday calendars, automated payroll generation, and taxation calculation.
- **Appraisals & Document Vault**: Performance reviews and secure employee document storage.

### 9. Quality Check Panel (QC Panel)
- **Direct Navigation**: Every page and route is clickable as a direct hyperlink that opens in a new tab.
- **7-Point Checklist**:
  1. UI Responsiveness
  2. UX
  3. Functionality
  4. Navigation
  5. Data Accuracy
  6. Permissions
  7. Error Handling
- **Select All Controls**: One-click row Select All and section-level Select All for rapid quality sign-offs.
- **Remarks Drawer**: Inline remarks modal to record observations, bug reports, and UX notes saved via AJAX.
- **Section SVG Icons**: Tailored Feather-style SVG icons for every module.

### 10. Roles & Access Control
- **Granular Page Matrix**: Every module and page in the QC panel is mirrored in Roles (`add.php`, `edit.php`).
- **Access Tiers**: No Access, View Only, or Full Access permissions per page.
- **Quick Bulk Toggles**: Bulk "None", "View", or "Full" controls per section and globally.

---

## Entity Lifecycle & Reference Numbering

The system enforces strict entity boundaries with auto-generated reference identifiers:

| Entity | Identifier Format | Table | Description |
|---|---|---|---|
| **Database Record** | `D%010d` (e.g. `D0000000042`) | `ci_leads` | Raw outreach prospect. Never referred to as a Lead. |
| **Qualified Lead** | `L%010d` (e.g. `L0000000042`) | `ci_leads` | Verified prospect approved by Back Office or Admin. |
| **Order Record** | `O%010d` (e.g. `O0000000015`) | `ci_orders` | Commercial order. Once submitted, the source lead is locked. |
| **Sales Record** | `S%010d` (e.g. `S0000000008`) | `ci_orders` | Finalized, won sale approved by Back Office. |

---

## System Requirements

- **PHP**: 7.4 to 8.2 with `mysqli`, `curl`, `mbstring`, `json`, and `openssl` extensions.
- **Database**: MySQL 5.7+ or MariaDB 10.3+.
- **Web Server**: Apache 2.4+ (with `mod_rewrite` enabled) or Nginx with PHP-FPM.
- **Browser**: Modern desktop or mobile browser (Chrome, Safari, Edge, Firefox).

---

## Installation & Setup

### 1. Database Configuration
Edit `application/config/database.php` to configure database credentials:

```php
$db['default']['hostname'] = '127.0.0.1';
$db['default']['username'] = 'your_database_user';
$db['default']['password'] = 'your_database_password';
$db['default']['database'] = 'your_database_name';
$db['default']['dbdriver'] = 'mysqli';
```

### 2. Base URL Configuration
Edit `application/config/config.php` to set the site URL:

```php
$config['base_url'] = 'https://your-crm-domain.com/';
```

### 3. Local Development Server
To launch a rapid local development server:

```bash
php -S 127.0.0.1:8000
```

---

## Key Navigation Routes

| Module | Route | Access |
|---|---|---|
| Dashboard | `/manager/dashboard` | All authenticated users |
| Phone Dialer | `/manager/calls` | Telephony permitted users |
| Live Operations | `/manager/live` | Operations & sales staff |
| View Database | `/manager/leads` | Sales & management |
| View Leads | `/manager/leads/view_leads` | Sales & management |
| View Orders | `/manager/orders/my` | Personal orders |
| Pending Leads Review | `/manager/leads/review_leads` | Back Office / Admin |
| Pending Order Process | `/manager/orders/pending_process` | Back Office / Admin |
| Team Chart | `/manager/team/chart` | Team leaders & managers |
| Key Accounts (KAM) | `/manager/kam/my-client` | KAM staff |
| HR Operations | `/manager/hr` | HR staff & Admin |
| Roles & Permissions | `/manager/roles` | Admin |
| Quality Check Panel | `/manager/system/qc` | Admin & QA |

---

## License & Copyright

© 2026 Seven Strokes CRM Platform. All rights reserved.
Proprietary commercial software. Unauthorized copying, modification, distribution, or decompilation is strictly prohibited.
