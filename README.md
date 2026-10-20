# crmV2

[![PHP Version](https://img.shields.io/badge/PHP-7.4%20--%208.2-777bb4?logo=php)](https://www.php.net/)
[![CodeIgniter](https://img.shields.io/badge/CodeIgniter-3.x-ee4326?logo=codeigniter)](https://codeigniter.com/)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%20--%208.0-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![License](https://img.shields.io/badge/License-Proprietary-b91c1c.svg)](#license)

`crmV2` is a fast, responsive PHP web application for lead allocation, sales pipeline tracking, team performance reporting, and customer support ticket workflows.

It is designed to give sales teams and management real-time visibility into leads, quotas, and operations without heavy dependencies or bloated page loads.

# Contents

- [Why?](#why)
- [Features](#features)
- [Installation](#installation)
  - [Method 1: Local Development Server (Fastest)](#method-1-local-development-server-fastest)
  - [Method 2: Apache / XAMPP / WAMP](#method-2-apache--xampp--wamp)
  - [Method 3: cPanel Deployment](#method-3-cpanel-deployment)
- [Dependencies](#dependencies)
- [Configuration](#configuration)
  - [Database Setup](#database-setup)
  - [Base URL](#base-url)
- [Core Workflows](#core-workflows)
  - [Allotment vs. Assignment vs. Transfer](#allotment-vs-assignment-vs-transfer)
  - [Interactive Reports & Drilldowns](#interactive-reports--drilldowns)
  - [Mobile Navigation Drawer](#mobile-navigation-drawer)
- [Key Routes](#key-routes)
- [User Roles & Permissions](#user-roles--permissions)
- [License](#license)

### Why?

Managing enterprise sales teams requires clean boundaries between ownership and collaboration:

- **Strict Ownership**: One lead has one allotted owner (`created_by`), preventing duplicate work or lost credit.
- **Clear Collaboration**: Owners can assign collaborators without losing their ownership of the lead.
- **Simple Handover**: Leads can be transferred cleanly with an audit log of who made the change.
- **Fast, Server-Rendered UI**: Built on PHP and CodeIgniter so pages load fast even with thousands of records.
- **Interactive Reports**: Clicking any count in a report takes you directly to the exact list of matching records.
- **Mobile First**: Works smoothly on Android and iOS browsers with a slide-out navigation drawer.

### Features

- **Lead Allocation**:
  - Filter unallotted and allotted database records.
  - Bulk allocate leads to users with one click.
  - Transfer leads between team members with automatic logging.
- **Sales Dashboard**:
  - Monthly projections (`OCT 26`, `NOV 26`, etc.) with aligned bar charts.
  - Operational breakdown cards for follow-ups, meetings, and closures.
- **Interactive Pivot Reports**:
  - Sales performance by department, manager, and vertical.
  - Only numeric counts and monetary amounts are clickable drilldowns.
  - Export reports to CSV and Excel.
- **Operations & Help Desk**:
  - Ticket queues, priority flags, deadlines, and open ticket follow-ups.
- **Calling Center Dashboard**:
  - Track calls, talk time, and dispositions by agent.
- **KAM (Key Account Management)**:
  - Role-gated client management and onboarding views.
- **Audit Logging**:
  - Full audit trail of admin actions, login attempts, and lead transfers.

### Installation

---

#### Method 1: Local Development Server (Fastest)

1. Clone the repository:
   ```bash
   git clone https://github.com/Reinhart-py/crmV2.git
   cd crmV2
   ```

2. Make sure MySQL is running on port `3306`.

3. Import the database schema:
   ```bash
   mysql -u root -p jaipuuwy_crmrolexto < database/localhost.sql
   ```

4. Start the PHP development server:
   ```bash
   php -S localhost:8080 index.php
   ```

5. Open your browser and go to:
   ```
   http://localhost:8080/manager/login
   ```

#### Method 2: Apache / XAMPP / WAMP

1. Move the repository folder into your web root:
   - **XAMPP**: `C:/xampp/htdocs/crm/`
   - **WAMP**: `C:/wamp64/www/crm/`
   - **Linux**: `/var/www/html/crm/`

2. Import `database/localhost.sql` using phpMyAdmin or command line.

3. Update the base URL in `application/config/config.php`:
   ```php
   $config['base_url'] = 'http://localhost/crm/';
   ```

4. Verify Apache has `mod_rewrite` enabled for `.htaccess` routing.

#### Method 3: cPanel Deployment

1. Compress the project files (excluding `.git`) and upload to cPanel **File Manager** inside `public_html/`.
2. Extract the archive.
3. In cPanel **MySQL Databases**, create a database and user, then assign full permissions.
4. Import `database/localhost.sql` via **phpMyAdmin**.
5. Update `application/config/database.php` and `application/config/config.php` with your domain and database credentials.

### Dependencies

---

- **PHP**: `7.4` to `8.2`
- **Required PHP Extensions**:
  - `mysqli` (database connection)
  - `mbstring` (multibyte string handling)
  - `curl` (API and external requests)
  - `gd` (image processing and captcha)
  - `session` (user session state)
  - `json` (data serialization)
- **Database**: MySQL 5.7+ or MariaDB 10.3+
- **Frontend Libraries** (bundled locally):
  - jQuery 2.x & 3.x
  - Bootstrap 3.x (AdminLTE theme)
  - DataTables with Buttons (Export / Print)
  - FontAwesome 4.x
  - Chart.js / Morris.js

### Configuration

---

#### Database Setup

Edit `application/config/database.php`:

```php
$db['default'] = array(
    'dsn'      => '',
    'hostname' => '127.0.0.1',
    'username' => 'root',
    'password' => '',
    'database' => 'jaipuuwy_crmrolexto',
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => FALSE,
    'db_debug' => (ENVIRONMENT !== 'production'),
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
    'swap_pre' => '',
    'encrypt'  => FALSE,
    'compress' => FALSE,
    'stricton' => FALSE,
    'failover' => array(),
    'save_queries' => TRUE
);
```

#### Base URL

Edit `application/config/config.php`:

```php
$config['base_url'] = 'http://localhost:8080/';
```

### Core Workflows

---

#### Allotment vs. Assignment vs. Transfer

These three operations have distinct rules in the CRM:

| Action | Who Can Do It | Field Updated | Meaning |
|---|---|---|---|
| **Allotment** | Admin Only | `created_by` | Grants primary ownership of an unallocated lead (`created_by = 0`) to a user. |
| **Assignment** | Lead Owner / Admin | `lead_assign` / `agents` | Shares access with collaborators so they can work the lead without changing the owner. |
| **Transfer** | Lead Owner / Admin | `created_by` & `lead_assign` | Full handover of lead ownership to another team member. Recorded in admin audit logs. |

#### Interactive Reports & Drilldowns

- Located under **Reports > Sales Performance**.
- Only numeric counts and amounts are clickable links. Clicking a number takes you to `/manager/reports/drilldown` showing the exact matching records.
- Names, departments, category titles, and total labels are displayed as plain text.

#### Mobile Navigation Drawer

- On mobile screens (under `768px`), tap the 3-line hamburger button (`#crmMobileHamburger`) in the top navigation bar to open the offcanvas menu.
- Tap outside or tap the `X` button (`#crmSidebarClose`) to close the drawer.
- Submenu accordions expand and collapse smoothly.

### Key Routes

---

| Route | Purpose |
|---|---|
| `manager/login` | User authentication |
| `manager/dashboard` | Main sales & operational performance overview |
| `manager/assigned-leads` | Allotted database records (mirrors team leads layout) |
| `manager/database-transfer` | Lead ownership transfer interface |
| `manager/team/leads` | Team lead pipeline |
| `manager/verticals/followups` | Vertical follow-up queue |
| `manager/kam/my-client` | Key account client view |
| `manager/calls/dashboard` | Calling center metrics & records |
| `manager/tickets/open` | Open support tickets |
| `manager/reports/view/sales_performance` | Sales performance pivot report |
| `manager/system/admin-logs` | Administrative audit trail |

### User Roles & Permissions

---

- **Admin (Role 1)**: Full access to all modules, allotment, user management, and system logs.
- **Support (Role 4)**: Full operational access, ticketing, and reporting.
- **Back Office (Role 2)**: Operational processing and KAM client viewing.
- **Sales (Role 8)**: Personal pipeline, vertical follow-ups, and assigned leads.
- **User (Role 3)**: Standard sales agent interface.

### License

---

Proprietary enterprise software. Unauthorized reproduction, modification, or distribution is strictly prohibited. All rights reserved.
