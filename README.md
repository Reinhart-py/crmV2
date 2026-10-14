# Rolexto CRM

[![PHP Version](https://img.shields.io/badge/PHP-7.4%20--%208.2-777bb4?logo=php)](https://www.php.net/)
[![CodeIgniter](https://img.shields.io/badge/CodeIgniter-3.x-ee4326?logo=codeigniter)](https://codeigniter.com/)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%20--%208.0-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![License: Proprietary](https://img.shields.io/badge/License-Rolexto%20Enterprise-b91c1c.svg)](#license)

Enterprise CRM suite built for high-performance sales teams, lead pipeline distribution, operational tracking, team performance analytics, and integrated customer help desk ticketing.

---

## Executive Summary of Recent Updates (October 13–14, 2026)

> On October 13 and 14, 2026, we completed the following updates: we gave each customer support ticket its own dedicated page with the full message history and added action dropdown buttons to quickly reply, resolve, close, or re-open tickets; we moved the Help Desk to a floating corner button so it doesn't clutter the top menu; we fixed the Android mobile menu so the 3-line button now opens the sidebar and lets staff expand all sub-menus on phones; we created an 'Unassigned Leads' page so managers can view new leads and assign them to team members; we allowed the Support role to reassign leads and upload lead spreadsheets without needing full admin access; and we added a one-click dark/light mode button at the top of the screen.

---

## Quick Start (Run Locally)

### 1. Prerequisites
- **PHP**: Version 7.4 to 8.2 (with `mysqli`, `mbstring`, `curl`, `gd` extensions enabled)
- **MySQL / MariaDB**: Running on `127.0.0.1:3306`

### 2. Configure Database & Base URL

1. **Database Configuration** (`application/config/database.php`):
   ```php
   $db['default'] = array(
       'hostname' => '127.0.0.1',
       'username' => 'root',
       'password' => '',
       'database' => 'jaipuuwy_crmrolexto', // or your local database name
       'dbdriver' => 'mysqli',
       'db_debug' => FALSE,
       'char_set' => 'utf8',
       'dbcollat' => 'utf8_general_ci'
   );
   ```

2. **Base URL Configuration** (`application/config/config.php`):
   ```php
   $config['base_url'] = 'http://127.0.0.1:8080/';
   ```

### 3. Launch Development Server

Run the built-in PHP development server from the project root:

```bash
# Start local PHP server on port 8080
php -S 127.0.0.1:8080
```

Open your browser and navigate to:
```
http://127.0.0.1:8080/manager/dashboard
```

---

## Production & Web Server Deployment

### Running with Apache (XAMPP / Laragon / WampServer)
1. Copy or clone the project directory into your web root:
   - **XAMPP**: `C:\xampp\htdocs\crm`
   - **Laragon**: `C:\laragon\www\crm`
   - **Linux Apache**: `/var/www/html/crm`
2. Update `application/config/config.php`:
   ```php
   $config['base_url'] = 'http://localhost/crm/';
   ```
3. Ensure Apache `mod_rewrite` is enabled and `.htaccess` allows URL rewriting.

### Linux / VPS / Ubuntu Server Deployment
```bash
# Set folder permissions for web server
sudo chown -R www-data:www-data /var/www/html/crm
sudo chmod -R 755 /var/www/html/crm
sudo chmod -R 777 /var/www/html/crm/application/cache

# Enable Apache rewrite module and restart
sudo a2enmod rewrite
sudo systemctl restart apache2
```

---

## Core System Features & Modules

### 1. Modernized Executive Dashboard
- **Floating Pill Navigation Bar**: High-contrast dark pill layout with smooth dropdowns for Desktop and Tablet screens.
- **Dark/Light Mode**: One-click theme switcher in top bar with instant persistence across all pages.
- **Executive KPI Cards**: Real-time summary cards for Personal Leads, Verticals, Team Users, and Team Leads.
- **Operational Activity Grid**: Multi-range pipeline tracking (*Missed*, *Last 7 Days*, *Today*, *Next 7 Days*, *All Future*).
- **Projections Chart**: Month-by-month sales trajectory and comparative team analytics.

### 2. Lead Management & Assignment
- **Unassigned Leads Queue**: Filter and view all freshly imported or unassigned inquiries in one dedicated list.
- **Lead Reassignment for Admin & Support**: Support role (Role ID: 4) and Administrators can batch reassign leads to sales specialists.
- **Secure Import & Export**: Role-based access control protecting client data downloads.

### 3. Team Collaboration & Reports
- **Team Hierarchy & Visual Chart**: Organizational tree view of managers, team leads, and field agents.
- **Performance Reports**: Detailed individual and company-wide KPI reports with exportable PDF options.

### 4. Help Desk & Ticket Management
- **Dedicated Conversation Threads**: Each support ticket has its own dedicated view page (`manager/tickets/view/{id}`) with the full message history and response form.
- **Standardized Action Menu**: Quick dropdown button with actions:
  - **View Ticket**: Open dedicated discussion thread.
  - **Reply**: Jump straight to the reply text area.
  - **Mark In Progress**: Update ticket status for active handling.
  - **Clear / Mark Resolved**: Flag inquiry as resolved.
  - **Close Ticket**: Archive completed tickets.
  - **Re-Open Ticket**: Restore tickets that require additional attention.
  - **Delete Ticket**: Restricted strictly to Administrators.
- **Floating Help Button**: Persistent floating help desk button on the bottom left corner for quick 1-click access without cluttering the top navigation bar.

### 5. Android & Mobile Navigation
- **3-Line Hamburger Menu**: Touch-friendly hamburger button on Android and mobile screens.
- **Off-Canvas Sidebar Drawer**: Slides in smoothly with the 7 Strokes logo, category icons, and a dedicated `×` close button.
- **Submenu Accordions**: Seamlessly expand and collapse submenus (Leads, Verticals, Team, System, Profile) on any touch device.
- **Instant Dismissal**: Tap the close button or touch anywhere outside the drawer to dismiss.

---

## Project Directory Structure

```
crm/
├── application/
│   ├── config/
│   │   ├── config.php                  # Base URL, session, & system settings
│   │   ├── database.php                # MySQL database connection credentials
│   │   └── routes.php                  # Application routing rules & clean URLs
│   ├── controllers/
│   │   ├── ci_admin.php                # Dashboard, authentication, and core manager views
│   │   ├── ci_admin_leads.php          # Leads, followups, meetings, and import/export
│   │   ├── ci_admin_team.php           # Team members, hierarchy chart, and lead assignment
│   │   ├── ci_admin_tickets.php        # Help desk tickets, status updates, and replies
│   │   ├── ci_admin_user.php           # User account creation, editing, and roles
│   │   └── ci_admin_verticals.php     # Verticals management
│   ├── models/
│   │   └── ci_admin_model.php          # Database queries & pipeline metrics calculations
│   └── views/
│       └── manager/
│           ├── dashboard.php           # Executive full-screen dashboard
│           ├── tickets/                # Tickets index and individual conversation view
│           ├── team/                   # Team members, chart, and assign leads views
│           └── elements/
│               ├── header.php          # Navigation bar, mobile hamburger & sidebar drawer
│               └── footer.php          # Authoritative touch handlers & floating help desk
├── assets/
│   ├── dist/
│   │   └── css/
│   │       └── crm-dashboard.css       # Complete design system, theme tokens, & mobile CSS
│   └── plugins/                        # DataTables, Select2, Datepicker, and Chart scripts
└── README.md                           # Documentation, setup guide, & changelog
```

---

## License

Distributed under the **Rolexto Group Enterprise License**.  
&copy; 2024–2026 Rolex to Group. All rights reserved.
