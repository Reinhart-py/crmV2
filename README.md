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

## Production Deployment & cPanel Hosting Guide

This guide provides a comprehensive, step-by-step walkthrough for deploying the Seven Strokes CRM onto any standard **cPanel & WHM** hosting environment.

---

### Step 1: PHP Version & Extensions Configuration

1. Log in to your **cPanel Dashboard**.
2. Under the **Software** section, click **MultiPHP Manager** (or **Select PHP Version** on CloudLinux):
   - Set the domain or subdomain PHP version to **PHP 7.4** (recommended for legacy stability) or **PHP 8.1 / 8.2**.
3. Under **Software**, click **Select PHP Extensions** and ensure the following modules are enabled:
   - `mysqli` or `pdo_mysql` (Database driver)
   - `curl` (External API requests & integrations)
   - `mbstring` (Multibyte string handling)
   - `json` (JSON payload decoding & responses)
   - `openssl` (Secure HTTPS encryption)
   - `gd` or `imagick` (Image resizing & avatar processing)
   - `zip` (Document and report archiving)
   - `xml` & `fileinfo` (File uploads & XML parsing)
4. Under **Software**, click **MultiPHP INI Editor**:
   - Select your target domain and configure the following directives:
     ```ini
     max_execution_time = 300
     max_input_time = 300
     memory_limit = 256M
     post_max_size = 64M
     upload_max_filesize = 64M
     max_input_vars = 5000
     ```
   *(Note: `max_input_vars = 5000` is required to save extensive role permission matrices and large data export tables without POST data truncation).*

---

### Step 2: Create MySQL Database & User

1. In cPanel, navigate to **Databases** > **MySQL® Databases**.
2. **Create New Database**:
   - Enter a database name (e.g., `crm`) -> Final name will be `cpaneluser_crm`.
   - Click **Create Database**.
3. **Create Database User**:
   - Under *Add New User*, enter a username (e.g., `crmuser`) -> Final user: `cpaneluser_crmuser`.
   - Use the **Password Generator** to create a strong password. Save this password securely.
   - Click **Create User**.
4. **Add User to Database**:
   - Under *Add User to Database*, select user `cpaneluser_crmuser` and database `cpaneluser_crm`.
   - Click **Add**.
   - Check the **ALL PRIVILEGES** box and click **Make Changes**.

---

### Step 3: Import Database Schema via phpMyAdmin

1. In cPanel, navigate to **Databases** > **phpMyAdmin**.
2. Select your newly created database (`cpaneluser_crm`) from the left sidebar.
3. Click the **Import** tab in the top navigation bar.
4. Click **Choose File** and select the CRM database dump located in the repository:
   ```text
   database/localhost.sql
   ```
5. Leave format as **SQL** and click **Import** (or **Go**).
6. Wait for the green success notice confirming all tables (including `ci_user`, `ci_leads`, `ci_orders`, `ci_roles`, `ci_hr_targets`, etc.) have been imported.

---

### Step 4: Upload Codebase to cPanel

#### Option A: Using cPanel Git Version Control (Recommended)
1. In cPanel, navigate to **Files** > **Git™ Version Control**.
2. Click **Create**:
   - **Clone URL**: `https://github.com/Reinhart-py/crmV2.git`
   - **Repository Path**: `public_html` (for main domain) or `public_html/crm` (for subdomain).
   - **Repository Name**: `crmV2`
3. Click **Create** to clone the repository.
4. Whenever you push updates to GitHub, you can pull them instantly with 1-click in the **Manage** tab > **Pull or Deploy**.

#### Option B: Using cPanel File Manager (Manual Upload)
1. On your local machine, zip all project files (ensure `.htaccess` and all directories are included).
2. In cPanel, open **File Manager**.
3. Ensure **Show Hidden Files (dotfiles)** is enabled:
   - Click **Settings** (top right gear icon) in File Manager.
   - Check **Show Hidden Files (dotfiles)** and click **Save**.
4. Navigate to your target directory (e.g., `public_html` for root domain or `crm.yourdomain.com`).
5. Click **Upload**, choose your `.zip` archive, and wait for the upload to complete (100%).
6. Right-click the `.zip` archive in File Manager and select **Extract**.

---

### Step 5: Configure Application Settings

Open **File Manager** and configure the following two files:

#### 1. Database Connection (`application/config/database.php`)
Locate line 78 and configure your cPanel MySQL credentials:

```php
$db['default']['hostname'] = 'localhost'; // In cPanel, MySQL host is always localhost
$db['default']['username'] = 'cpaneluser_crmuser';
$db['default']['password'] = 'your_strong_generated_password';
$db['default']['database'] = 'cpaneluser_crm';
$db['default']['dbdriver'] = 'mysqli';
$db['default']['dbprefix'] = '';
$db['default']['pconnect'] = FALSE;
$db['default']['db_debug'] = (ENVIRONMENT !== 'production');
$db['default']['cache_on'] = FALSE;
$db['default']['char_set'] = 'utf8';
$db['default']['dbcollat'] = 'utf8_general_ci';
```

#### 2. Site URL & Routing (`application/config/config.php`)
Configure your live production domain and clean URL routing:

```php
// Set your exact domain with trailing slash (must use https://)
$config['base_url'] = 'https://crm.yourdomain.com/';

// Leave index_page blank for clean URL routing
$config['index_page'] = '';

// Security encryption key (32+ random characters)
$config['encryption_key'] = 'your_unique_32_character_security_key';

// Cookie & Session domain (leave empty string for auto-detection)
$config['cookie_domain'] = '';
$config['cookie_path']   = '/';
```

---

### Step 6: Apache URL Rewriting (`.htaccess`)

Verify that the `.htaccess` file exists in the root folder alongside `index.php`. It must contain the clean CodeIgniter URL rewrite rules:

```apache
<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteBase /

  # Force HTTPS
  RewriteCond %{HTTPS} off
  RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

  # Route requests through CodeIgniter front controller
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule ^(.*)$ index.php?/$1 [L]
</IfModule>

<IfModule !mod_rewrite.c>
  ErrorDocument 404 /index.php
</IfModule>
```
*(Note: If hosting in a subfolder like `https://yourdomain.com/crm/`, change `RewriteBase /` to `RewriteBase /crm/`).*

---

### Step 7: Set Directory Permissions

In cPanel File Manager, verify permissions:
- Standard Folders: `0755`
- Standard Files: `0644`
- Writable Directories (must be writable by the web server):
  - `application/cache/` -> `0755` or `0775`
  - `application/logs/` -> `0755` or `0775`
  - `uploads/` (if present) -> `0755` or `0775`

---

### Step 8: SSL / HTTPS Activation

1. In cPanel, navigate to **Security** > **SSL/TLS Status**.
2. Select your CRM domain / subdomain and click **Run AutoSSL**.
3. Once AutoSSL issues the certificate (indicated by a green lock), go to **Domains** > **Domains** and toggle **Force HTTPS Redirect** to **ON**.

---

### Step 9: Configure CRM Background Cron Jobs

The CRM relies on periodic tasks for auto follow-up alerts, reminders, and synchronization.

1. In cPanel, navigate to **Advanced** > **Cron Jobs**.
2. Under **Add New Cron Job**:
   - **Common Settings**: Once Per Minute (`* * * * *`) or Once Every 5 Minutes (`*/5 * * * *`).
   - **Command**:
     ```bash
     /usr/local/bin/php /home/yourcpaneluser/public_html/index.php ci_cron run >/dev/null 2>&1
     ```
     *(Replace `/home/yourcpaneluser/public_html/` with the absolute path of your CRM document root).*
3. Click **Add New Cron Job**.

---

### Step 10: Common cPanel Troubleshooting & Solutions

| Issue | Root Cause | Solution |
|---|---|---|
| **404 Page Not Found on Internal URLs** | Missing or ignored `.htaccess` | In cPanel File Manager settings, check "Show Hidden Files (dotfiles)" to ensure `.htaccess` was uploaded. Verify `RewriteEngine On` is active. |
| **Database Connection Error (`mysqli`)** | Incorrect credentials or missing user privilege | Verify that the cPanel prefix is included (e.g. `cpaneluser_crm`). In *MySQL Databases*, verify the user was added to the database with **ALL PRIVILEGES**. |
| **Broken Styles / Assets Not Loading** | Incorrect `base_url` or Mixed Content | Ensure `$config['base_url']` in `application/config/config.php` starts with `https://` and ends with a trailing slash `/`. |
| **Immediate Logout / Login Loop** | Cookie domain or session permission conflict | Set `$config['cookie_domain'] = '';` in `config.php`. Ensure `application/cache/` has `0755` or `0775` permissions. |
| **500 Internal Server Error** | Missing PHP extension or memory limit | Check cPanel **Metrics** > **Errors** log. Ensure `mysqli`, `curl`, `mbstring`, and `json` are enabled in MultiPHP Manager, and `memory_limit` is at least `256M`. |
| **Permissions / Settings Truncated on Save** | PHP `max_input_vars` limit reached | In cPanel **MultiPHP INI Editor**, increase `max_input_vars` to `5000` so large matrices can submit all POST fields. |

---

### Step 11: Local Development Server (Optional)
For local testing on a development workstation without cPanel:

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
