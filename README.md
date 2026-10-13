# Rolexto CRM v2

[![GitHub repo](https://img.shields.io/badge/GitHub-Reinhart--py%2FcrmV2-181717?logo=github)](https://github.com/Reinhart-py/crmV2)
[![PHP Version](https://img.shields.io/badge/PHP-5.6%20--%208.2-777bb4?logo=php)](https://www.php.net/)
[![CodeIgniter](https://img.shields.io/badge/CodeIgniter-3.x-ee4326?logo=codeigniter)](https://codeigniter.com/)
[![License: Proprietary](https://img.shields.io/badge/License-Rolexto%20Enterprise-b91c1c.svg)](#license)
[![Netlify Status](https://img.shields.io/badge/Netlify-Ready-00C7B7?logo=netlify)](https://www.netlify.com/)
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg)](#want-to-contribute)

`Rolexto CRM v2` is an executive-grade modernization of the original CodeIgniter CRM suite. It delivers a high-performance **full-screen dashboard**, a signature **floating dark pill navigation bar**, real-time **operational pipeline metrics** across 5 date ranges, and interactive **drill-down views**—all while preserving 100% of the original CRM's backend logic, permissions, models, and workflows.

---

# Contents

- [Why?](#why)
- [How to See the UI Now](#how-to-see-the-ui-now)
  - [Method 1: Instant Local Preview Server (Zero Install)](#method-1-instant-local-preview-server-zero-install)
  - [Method 2: Standalone Browser Launch (Double Click)](#method-2-standalone-browser-launch-double-click)
  - [Method 3: 1-Click Cloud Hosting (Netlify / Vercel / GitHub Pages)](#method-3-1-click-cloud-hosting-netlify--vercel--github-pages)
  - [Method 4: Full CodeIgniter PHP + MySQL Stack (XAMPP / Laragon / Apache)](#method-4-full-codeigniter-php--mysql-stack-xampp--laragon--apache)
  - [Method 5: Production Linux / VPS / cPanel Deployment](#method-5-production-linux--vps--cpanel-deployment)
- [Key Features & UI Architecture](#key-features--ui-architecture)
  - [1. Floating Dark Pill Navigation Bar](#1-floating-dark-pill-navigation-bar)
  - [2. Executive KPI Header Cards](#2-executive-kpi-header-cards)
  - [3. Projections Analytics Row](#3-projections-analytics-row)
  - [4. Operational Activity & Closures Matrix](#4-operational-activity--closures-matrix)
  - [5. Interactive Metric Drill-Down Views](#5-interactive-metric-drill-down-views)
- [Configuration](#configuration)
  - [Database Configuration](#database-configuration)
  - [Base URL Configuration](#base-url-configuration)
- [Project Directory Structure](#project-directory-structure)
- [Git Integration & Tracking](#git-integration--tracking)
- [Troubleshooting & FAQ](#troubleshooting--faq)
- [Want to Contribute?](#want-to-contribute)
- [License](#license)

---

### Why?

We needed an enterprise-grade interface update that gives the executive team modern visual clarity without risking stability or introducing collateral refactors into the production CodeIgniter backend:

- **100% Core Logic Preservation**: Not a single original field, table, controller route, permission, or workflow outside the dashboard was altered.
- **Full-Screen Responsive Canvas**: Expands beyond legacy fixed containers to utilize 100% of modern high-resolution displays.
- **Reference Pill Navigation**: Implements the modern floating dark pill bar with circular icon controls and high-contrast active state (`• Dashboard`), retaining all existing dropdowns and submenu routes.
- **Real-Time Operational Activity Grid**: 8 structured stat cards covering Followups, Meetings, Closures, and Vertical Followups (both personal and team-wide).
- **5-Range Date Filtering**: Instant segmentation across *All Missed*, *Last 7 Days*, *Today*, *Next 7 Days*, and *All Future*.
- **Integrated Drill-Down View Panel**: Lets executives review filtered leads, contacts, and deadlines inline on the dashboard before opening full modules.
- **Instant Previews**: Standalone static SPA preview (`preview.html` / `server.py`) allows instant evaluation on Netlify or locally without configuring PHP or MySQL upfront.

---

### How to See the UI Now

You have multiple ways to view and interact with the UI, ranging from instant zero-setup local previews to full production PHP deployment.

#### Method 1: Instant Local Preview Server (Zero Install)

The repository includes a dedicated, zero-dependency Python runner (`server.py`). It launches an HTTP server and opens your browser automatically:

```bash
# Clone the repository
git clone https://github.com/Reinhart-py/crmV2.git
cd crmV2

# Run the preview server (Python 3.6+ built-in)
python server.py
```

- **URL**: `http://localhost:8000/preview.html`
- **Features Active**: Full navigation, KPI cards, Jan–Dec bar charts, interactive segment bars, drill-down panels, and Dark/Light mode switcher.
- **Stop Server**: Press `Ctrl + C` in your terminal.

---

#### Method 2: Standalone Browser Launch (Double Click)

You do not even need a web server to view the interface. Simply navigate to the project directory in your file explorer and double-click:

```
preview.html
```

Your default browser (Chrome, Edge, Safari, Firefox) will load the complete executive dashboard UI with all fonts, icons, styling, and charts directly.

---

#### Method 3: 1-Click Cloud Hosting (Netlify / Vercel / GitHub Pages)

The repository includes `index.html` and `_redirects` preconfigured for static SPA deployment.

##### Option A: Netlify Drop (Fastest - 30 Seconds)
1. Go to [app.netlify.com/drop](https://app.netlify.com/drop).
2. Drag and drop the `crmV2` project folder directly into the browser window.
3. Netlify will publish your site instantly and generate a live HTTPS URL (e.g., `https://rolexto-crm.netlify.app`).

##### Option B: Netlify CLI
```bash
# Install Netlify CLI globally
npm install -g netlify-cli

# Deploy directly from repository root
netlify deploy --prod --dir=.
```

##### Option C: Vercel CLI
```bash
# Install Vercel CLI
npm install -g vercel

# Deploy
vercel --prod
```

##### Option D: GitHub Pages
1. Push to your repository: `https://github.com/Reinhart-py/crmV2.git`.
2. Go to **Settings** &rarr; **Pages**.
3. Under **Build and deployment**, select branch `main` and root `/`.
4. Click **Save**. Your site will be published at `https://<username>.github.io/crmV2/`.

---

#### Method 4: Full CodeIgniter PHP + MySQL Stack (XAMPP / Laragon / Apache)

To run the full dynamic PHP application with live database persistence:

##### Step 1: Copy to Webroot
- **XAMPP**: Copy folder to `C:\xampp\htdocs\crm`
- **Laragon**: Copy folder to `C:\laragon\www\crm`
- **Linux Apache**: Copy folder to `/var/www/html/crm`

##### Step 2: Configure `base_url`
Edit `application/config/config.php`:
```php
$config['base_url'] = 'http://localhost/crm/';
```

##### Step 3: Configure Database Connection
Edit `application/config/database.php`:
```php
$db['default'] = array(
    'dsn'   => '',
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'rolexto_crm',
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => FALSE,
    'db_debug' => (ENVIRONMENT !== 'production'),
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
    ...
);
```

##### Step 4: Import Database Schema
Import your CRM database dump (e.g. `rolexto_crm.sql`) via phpMyAdmin or MySQL CLI:
```bash
mysql -u root -p rolexto_crm < rolexto_crm.sql
```

##### Step 5: Access in Browser
Navigate to:
```
http://localhost/crm/manager/dashboard
```

---

#### Method 5: Production Linux / VPS / cPanel Deployment

1. **cPanel / Shared Hosting**:
   - Upload the ZIP archive to `public_html` (or subdomain document root).
   - Extract files.
   - Update `application/config/database.php` with your cPanel MySQL credentials.
   - Update `application/config/config.php` with your live domain:
     `$config['base_url'] = 'https://crm.yourdomain.com/';`
   - Ensure Apache `mod_rewrite` is enabled.

2. **Ubuntu / Nginx / Apache Server**:
   ```bash
   sudo chown -R www-data:www-data /var/www/html/crm
   sudo chmod -R 755 /var/www/html/crm
   sudo chmod -R 777 /var/www/html/crm/application/cache
   sudo a2enmod rewrite
   sudo systemctl restart apache2
   ```

---

### Key Features & UI Architecture

```
+---------------------------------------------------------------------------------------+
|  ROLEXTO CRM       hr@rolextogroup.com | +971 58 563 7737              Rolexto Admin  |
|  [O] ( • Dashboard ) ( Users v ) ( Leads v ) ( Verticals v ) ( Team v ) ( System v )  |
+---------------------------------------------------------------------------------------+
|                                                                                       |
|  DASHBOARD                                                     [ (O) Light/Dark Mode ]|
|                                                                                       |
|  +------------------+  +------------------+  +------------------+  +------------------+  |
|  | 18      MY LEADS |  | 7   MY VERTICALS |  | 14   TEAM USERS  |  | 56   TEAM LEADS  |  |
|  |  (View Details>) |  |  (View Details>) |  |  (View Details>) |  |  (View Details>) |  |
|  +------------------+  +------------------+  +------------------+  +------------------+  |
|                                                                                       |
|  +------------------------------------+  +------------------------------------+       |
|  | [!] My Projection                  |  | [!] Team Projection                |       |
|  | || || || || || || || || || || ||   |  | || || || || || || || || || || ||   |       |
|  | Jan Feb Mar Apr May Jun Jul Aug... |  | Jan Feb Mar Apr May Jun Jul Aug... |       |
|  +------------------------------------+  +------------------------------------+       |
|                                                                                       |
|  [||] OPERATIONAL ACTIVITY & CLOSURES BREAKDOWN          Real-time Pipeline Overview  |
|                                                                                       |
|  +---------------+  +---------------+  +---------------+  +---------------+           |
|  | My Followups  |  | My Meetings   |  | My Closures   |  | My Vertical PU|           |
|  | [=========]   |  | [=========]   |  | [=========]   |  | [=========]   |           |
|  | - Missed:   4 |  | - Missed:   3 |  | - Missed:   1 |  | - Missed:   1 |           |
|  | - Last 7d:  8 |  | - Last 7d:  4 |  | - Last 7d:  1 |  | - Last 7d:  2 |           |
|  | - Today:    9 |  | - Today:    7 |  | - Today:    1 |  | - Today:    2 |           |
|  | - Next 7d: 12 |  | - Next 7d:  9 |  | - Next 7d:  1 |  | - Next 7d:  4 |           |
|  | - Future:  15 |  | - Future:  11 |  | - Future:   2 |  | - Future:   2 |           |
|  +---------------+  +---------------+  +---------------+  +---------------+           |
|  +---------------+  +---------------+  +---------------+  +---------------+           |
|  | Team Followups|  | Team Meetings |  | Team Closures |  | Team Vert. PU |           |
|  | [=========]   |  | [=========]   |  | [=========]   |  | [=========]   |           |
|  | - Missed:  12 |  | - Missed:   5 |  | - Missed:   2 |  | - Missed:   5 |           |
|  | - Last 7d: 22 |  | - Last 7d: 14 |  | - Last 7d:  1 |  | - Last 7d:  9 |           |
|  | - Today:   19 |  | - Today:    9 |  | - Today:    1 |  | - Today:    6 |           |
|  | - Next 7d: 34 |  | - Next 7d: 18 |  | - Next 7d:  2 |  | - Next 7d:  9 |           |
|  | - Future:  53 |  | - Future:  24 |  | - Future:   7 |  | - Future:  15 |           |
|  +---------------+  +---------------+  +---------------+  +---------------+           |
|                                                                                       |
|  +---------------------------------------------------------------------------------+  |
|  | [*] Operational Metric Drill-Down Table (Active Filter)       [Open Full Module] |  |
|  +---------------------------------------------------------------------------------+  |
+---------------------------------------------------------------------------------------+
```

#### 1. Floating Dark Pill Navigation Bar
- **Pill Container**: Styled with `#181b20`, `border-radius: 9999px`, and subtle box shadow.
- **Toggle Icon**: Circular soft-light button with icon on the left.
- **Active Capsule**: Bright neon lime pill (`#d4f938`) with black typography and dot indicator (`• Dashboard`).
- **Submenus**: Floating dark cards (`border-radius: 14px`) with smooth hover transitions, preserving every link and dropdown.

#### 2. Executive KPI Header Cards
Four prominent status cards with icon watermarks:
- **Navy (`#182232`)**: `18 MY LEADS` &rarr; Quick access to `manager/leads`
- **Forest Green (`#0f3d2e`)**: `7 MY VERTICALS` &rarr; Quick access to `manager/verticals`
- **Warm Amber (`#4a2411`)**: `14 TEAM USERS` &rarr; Quick access to `manager/team/members`
- **Dark Crimson (`#4c111a`)**: `56 TEAM LEADS` &rarr; Quick access to `manager/team/leads`

#### 3. Projections Analytics Row
- **My Projection & Team Projection**: Month-by-month performance bar charts (Jan through Dec).
- **Styling**: Rendered in deep Rolex crimson (`#991b1b`) with `#7f1d1d` borders.

#### 4. Operational Activity & Closures Matrix
Eight stat cards divided into personal and team operations:
- **Row 1 (Personal)**: *My Followups*, *My Meetings*, *My Closures*, *My Vertical PU*
- **Row 2 (Team)**: *Team Followups*, *Team Meetings*, *Team Closures*, *Team Vertical PU*
- **Segmented Bar**: Visual proportional breakdown across the 5 date ranges.
- **5 Filter Chips**: *All Missed*, *Last 7 Days*, *Today*, *Next 7 Days*, *All Future*.

#### 5. Interactive Metric Drill-Down Views
- Clicking any of the 40 filter chips smoothly expands an inline drill-down drawer.
- Displays reference code (`#RLX-XXXX`), client organization, POC, phone number, pipeline stage, and assigned specialist.
- Features direct links to the full module table (`manager/leads/followups?type=X`, `manager/leads/meetings?type=X`, etc.).

---

### Configuration

#### Database Configuration
`application/config/database.php`
```php
$active_group = 'default';
$query_builder = TRUE;

$db['default'] = array(
    'hostname' => 'localhost',
    'username' => 'your_db_user',
    'password' => 'your_db_password',
    'database' => 'your_db_name',
    'dbdriver' => 'mysqli',
    'db_debug' => FALSE,
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci'
);
```

#### Base URL Configuration
`application/config/config.php`
```php
$config['base_url'] = 'http://localhost/crm/';
$config['index_page'] = '';
```

---

### Project Directory Structure

```
crmV2/
├── application/
│   ├── config/
│   │   ├── config.php                  # CodeIgniter core config & base_url
│   │   ├── database.php                # MySQL database connection settings
│   │   └── routes.php                  # Application routing rules
│   ├── controllers/
│   │   ├── ci_admin.php                # Main manager controller & dashboard logic
│   │   ├── ci_admin_leads.php          # Leads, followups & meetings controller
│   │   ├── ci_admin_team.php           # Team management controller
│   │   ├── ci_admin_user.php           # User accounts & directory controller
│   │   └── ci_admin_verticals.php     # Verticals management controller
│   ├── models/
│   │   └── ci_admin_model.php          # Database queries & metrics calculation
│   └── views/
│       └── manager/
│           ├── dashboard.php           # Modernized full-screen dashboard view
│           └── elements/
│               ├── header.php          # Pill navigation header bar
│               └── footer.php          # Footer scripts & Chart.js initialization
├── assets/
│   └── dist/
│       └── css/
│           └── crm-dashboard.css       # Complete dashboard & pill navigation styles
├── preview.html                        # Standalone interactive executive dashboard SPA
├── index.html                          # Redirects root to preview.html for static hosts
├── _redirects                          # Netlify SPA redirect rules
├── server.py                           # Zero-dependency local preview runner (Python 3)
└── README.md                           # Documentation & setup guide
```

---

### Git Integration & Tracking

The project is hosted and tracked on GitHub:

```bash
# Repository URL
https://github.com/Reinhart-py/crmV2.git

# Clone
git clone https://github.com/Reinhart-py/crmV2.git

# Check status
git status

# Create a feature branch
git checkout -b feature/my-update

# Commit changes
git add .
git commit -m "feat: your feature description"

# Push
git push origin feature/my-update
```

---

### Troubleshooting & FAQ

#### Q: How do I quickly verify the UI on Windows without PHP?
**A**: Run `python server.py` in your PowerShell or Command Prompt. It starts a lightweight HTTP server on port 8000 and automatically opens `preview.html` in your browser. Alternatively, double-click `preview.html`.

#### Q: How do I deploy to Netlify?
**A**: Simply drag and drop this entire directory into [app.netlify.com/drop](https://app.netlify.com/drop). The included `_redirects` and `index.html` handle all routing automatically.

#### Q: Where is the navigation bar styled?
**A**: In `assets/dist/css/crm-dashboard.css` under the `1. REFERENCE IMAGE PILL NAVIGATION BAR` section. The HTML structure and dropdowns in `application/views/manager/elements/header.php` remain untouched.

#### Q: Are any other CRM pages altered?
**A**: No. Per strict requirements, all forms, tables, modals, user management, permissions, and business logic outside the dashboard remain 100% identical to the original CRM.

---

### Want to Contribute?

1. Fork the project on [GitHub](https://github.com/Reinhart-py/crmV2).
2. Create your feature branch (`git checkout -b feature/AmazingFeature`).
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`).
4. Push to the branch (`git push origin feature/AmazingFeature`).
5. Open a Pull Request.

---

### License

Distributed under the **Rolexto Group Enterprise License**.  
&copy; 2024–2026 Rolex to Group. All rights reserved.
