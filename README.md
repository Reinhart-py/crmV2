# Seven Strokes CRM Platform (crmV2)

[![PHP Version](https://img.shields.io/badge/PHP-7.4%20--%208.2-777bb4?logo=php)](https://www.php.net/)
[![CodeIgniter](https://img.shields.io/badge/CodeIgniter-3.x-ee4326?logo=codeigniter)](https://codeigniter.com/)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%20--%208.0-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![License](https://img.shields.io/badge/License-Proprietary-2563eb.svg)](#license)

**Seven Strokes CRM** is a fast, modern web application built for sales teams, account managers, and business executives. It gives your team complete control over your leads, phone calling workflows, customer support tickets, sales pipelines, and executive reporting.

Everything is designed to be fast, reliable, clean, and simple to use on both computers and mobile phones.

---

## Table of Contents

- [Overview](#overview)
- [Key Features](#key-features)
  - [1. Calling Center & Smart Dialer](#1-calling-center--smart-dialer)
  - [2. Lead Management & Database Allocation](#2-lead-management--database-allocation)
  - [3. Interactive Pivot Reports & Drilldowns](#3-interactive-pivot-reports--drilldowns)
  - [4. Tasks & Follow-ups](#4-tasks--follow-ups)
  - [5. Help Desk & Support Tickets](#5-help-desk--support-tickets)
  - [6. Key Account Management (KAM)](#6-key-account-management-kam)
  - [7. Standardized Time & Date Inputs](#7-standardized-time--date-inputs)
  - [8. Security & Admin Audit Logs](#8-security--admin-audit-logs)
- [Design & User Experience](#design--user-experience)
- [System Requirements](#system-requirements)
- [Quick Installation Guide](#quick-installation-guide)
  - [Option A: Local Development Server (Fastest)](#option-a-local-development-server-fastest)
  - [Option B: XAMPP / WAMP / Apache](#option-b-xampp--wamp--apache)
  - [Option C: cPanel / Live Server Deployment](#option-c-cpanel--live-server-deployment)
- [Configuration](#configuration)
  - [Database Connection](#database-connection)
  - [Base URL](#base-url)
- [Key URL Routes](#key-url-routes)
- [User Roles & Access Levels](#user-roles--access-levels)
- [License](#license)

---

## Overview

Seven Strokes CRM solves common sales and operational bottlenecks:

- **Clear Ownership**: Every lead has one primary owner (`created_by`), eliminating confusion over who closed the deal.
- **Team Collaboration**: Lead owners can assign secondary collaborators (`agents`) without losing their ownership.
- **Integrated Phone Calling**: Dial contacts directly from the CRM with live call duration tracking, dual-number selection, and quick outcome saving.
- **Real-Time Data Drilldowns**: Click any number in an executive report to instantly see the exact list of matching leads.
- **Zero Clutter**: Clean, functional interface with no unnecessary decorations, fake timers, or slow page transitions.

---

## Key Features

### 1. Calling Center & Smart Dialer
Located at `manager/calls`, this workstation gives agents everything they need to handle calls without leaving the CRM:

- **Apple-Style Dark Glassmorphic Design**: Clean, modern interface with high visual clarity and easy controls.
- **Dual Contact Numbers**: Displays both Primary and Alternate phone numbers side-by-side with 1-click active number switching.
- **Real Call Awareness**: Detects PBX integration. When PBX is not configured, provides a direct 1-click `tel:` device launcher rather than simulating fake calls.
- **Accurate Duration**: Call duration timers only count while an actual call is connected.
- **Slide-Out Call Logs Drawer**: A smartphone-style drawer sliding from the right showing past completed calls with 1-click buttons to:
  - Call the number again
  - Copy the number to the clipboard
  - View or open the full lead details
- **Responsive Post-Call Drawer**: Slide-out panel for immediate notes, disposition updates, and scheduling next follow-ups, with sticky bottom action buttons visible on all screen sizes.

### 2. Lead Management & Database Allocation
- **Allotted vs. Unallotted Leads**: Easily filter leads that need an owner (`manager/assigned-leads?alloc_filter=non_allotted`).
- **Bulk Allocation**: Allocate multiple leads to team members in one click.
- **Database Transfer**: Safely transfer lead ownership with full records in the admin audit logs.
- **Custom Filters**: Filter by country, city, status, category, date ranges, and revenue.

### 3. Interactive Pivot Reports & Drilldowns
Located at `manager/download-report`, the reporting engine provides real-time visibility across all operations:

- **Two-Section Layout**: Every sales report is split into **My Report** (your own leads) and **Team Report** (leads of your team members).
- **8 Comprehensive Report Types**:
  1. Performance Report
  2. Database Report (Source, Batch, Status, Corporate Tax)
  3. Lead Pipeline Report
  4. Quotation & Proposal Report
  5. Tax & VAT Registration Report
  6. Lost Deals Analysis
  7. Trade License Expiry Report
  8. Custom Configurable Report
- **1-Click Drilldowns**: Click any count or monetary amount to open the exact list of matching records.
- **Instant Client-Side Tabs**: Switch between `All Records`, `My Records`, and `Team Records` without refreshing the page.
- **PowerSheet Field Selector**: In custom reports, pick and choose exact columns to export to CSV or Excel.

### 4. Tasks & Follow-ups
Located at `manager/tasks`:

- Schedule client follow-ups with dates, times, and priority flags.
- Filter by pending, overdue, and completed tasks.
- Keep records of trade license issue/expiry dates and DND checks.

### 5. Help Desk & Support Tickets
Located at `manager/tickets`:

- Full customer issue ticketing system with ticket numbers (`#TKT-...`).
- Assign tickets to agents, track priorities (Low, Medium, High, Urgent), and set deadlines.
- Track ticket conversation history, resolution notes, and customer communication.

### 6. Key Account Management (KAM)
Located at `manager/kam`:

- Dedicated view for high-value corporate clients.
- Client onboarding checklist and document verification steps.
- Meeting scheduling with attendee assignment and executive tracking.

### 7. Standardized Time & Date Inputs
- All time inputs across the CRM use an easy-to-use **Timepicker** with a clock icon.
- Click the input or the clock icon to adjust hours, minutes, and AM/PM using simple up/down chevron buttons.
- Available across Leads, Calls, Tasks, Tickets, KAM Schedules, and Meetings.

### 8. Security & Admin Audit Logs
- **Strict Authentication**: Secure password verification with encrypted sessions.
- **Single Active Session**: Prevents multiple people from using the same user credentials at the same time.
- **Inactivity Timeout**: Automatically protects idle workstations after 10 minutes of inactivity.
- **Complete Audit Trail (`manager/system/admin-logs`)**: Records every admin login, password reset, lead transfer, and record modification with user ID, timestamp, and IP address.

---

## Design & User Experience

- **Clean Aesthetic**: Built with a modern slate/gray palette (`#f8fafc` background, clean white cards, subtle `#e2e8f0` borders, and high-contrast text).
- **Zero AI Clutter**: Clean labels and functional forms without confusing helper text or decorative pill badges.
- **Illustrated Vector Avatars**: Professional SVG illustrated avatars for all user profiles.
- **Mobile Responsive**: Fully responsive layout with an offcanvas slide-out menu drawer for phones and tablets.

---

## System Requirements

- **PHP**: Version `7.4` to `8.2`
- **Required Extensions**:
  - `mysqli` (database)
  - `mbstring` (text processing)
  - `curl` (API communication)
  - `gd` (image processing)
  - `session` & `json`
- **Database**: MySQL 5.7+ or MariaDB 10.3+
- **Web Server**: Apache (with `mod_rewrite` enabled) or PHP Built-in Server for local development.

---

## Quick Installation Guide

### Option A: Local Development Server (Fastest)

1. **Clone the repository**:
   ```bash
   git clone https://github.com/Reinhart-py/crmV2.git
   cd crmV2
   ```

2. **Import the database**:
   Make sure MySQL is running on port 3306, then run:
   ```bash
   mysql -u root -p jaipuuwy_crmrolexto < database/localhost.sql
   ```

3. **Start the local server**:
   ```bash
   php -S localhost:8080 index.php
   ```

4. **Open in your browser**:
   Navigate to `http://localhost:8080/manager/login`.

---

### Option B: XAMPP / WAMP / Apache

1. Place the project folder into your web root:
   - **XAMPP**: `C:/xampp/htdocs/crm/`
   - **WAMP**: `C:/wamp64/www/crm/`
   - **Linux**: `/var/www/html/crm/`
2. Import `database/localhost.sql` using phpMyAdmin or command line.
3. Update `$config['base_url']` in `application/config/config.php`:
   ```php
   $config['base_url'] = 'http://localhost/crm/';
   ```
4. Ensure Apache has `mod_rewrite` enabled.

---

### Option C: cPanel / Live Server Deployment

1. Compress the project folder (excluding `.git`) and upload to your cPanel **File Manager** inside `public_html/`.
2. Extract the archive.
3. In cPanel **MySQL Databases**, create a database and database user, and assign all privileges.
4. Import `database/localhost.sql` using **phpMyAdmin**.
5. Update your database settings in `application/config/database.php` and base URL in `application/config/config.php`.

---

## Configuration

### Database Connection
File: `application/config/database.php`

```php
$db['default']['hostname'] = '127.0.0.1';
$db['default']['username'] = 'root';
$db['default']['password'] = '';
$db['default']['database'] = 'jaipuuwy_crmrolexto';
$db['default']['dbdriver'] = 'mysqli';
```

*(The configuration includes automatic detection for local vs. production environments).*

### Base URL
File: `application/config/config.php`

```php
$config['base_url'] = 'http://localhost:8080/';
```

---

## Key URL Routes

| Route | Description |
|---|---|
| `manager/login` | User login and authentication |
| `manager/dashboard` | Main executive and sales dashboard |
| `manager/calls` | Integrated Calling Center, smart dialer, and call logs |
| `manager/assigned-leads` | Allotted database leads (supports `?alloc_filter=non_allotted`) |
| `manager/database-transfer` | Lead ownership transfer interface |
| `manager/leads/add` | Add new lead / database contact |
| `manager/team/members` | Team members directory |
| `manager/team/leads` | Team lead pipeline |
| `manager/team/assignleads` | Assigned team database records |
| `manager/tasks` | Task management and follow-up reminders |
| `manager/tickets` | Customer support ticket tracking |
| `manager/kam/my-client` | Key Account Management client portfolio |
| `manager/meetings` | Meeting scheduling and logs |
| `manager/download-report` | Executive reporting catalog |
| `manager/system/admin-logs` | Administrative audit trail and security logs |

---

## User Roles & Access Levels

- **Super Admin (Role 1)**: Complete administrative control, database allotment, user permissions, and audit logs.
- **Support Manager (Role 4)**: Full operational management, customer tickets, and reporting.
- **Back Office (Role 2)**: Operations processing, KAM client viewing, and record verification.
- **Sales Executive (Role 8)**: Personal pipeline, team follow-ups, calling center, and assigned leads.
- **Standard User (Role 3)**: Direct sales agent interface with lead management and calling tools.

---

## License

© 2026 Seven Strokes CRM Platform. All rights reserved.

Proprietary software. Unauthorized reproduction, modification, copying, or distribution of this software and associated documentation is strictly prohibited.
