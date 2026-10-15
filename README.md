<div align="center">
  <h1>Rolexto CRM</h1>
  <p><strong>Enterprise Sales Pipeline, Operations Management & Customer Help Desk</strong></p>
  <p>
    <a href="https://www.php.net/"><img src="https://img.shields.io/badge/PHP-7.4%20--%208.2-777bb4?logo=php" alt="PHP Version"></a>
    <a href="https://codeigniter.com/"><img src="https://img.shields.io/badge/CodeIgniter-3.x-ee4326?logo=codeigniter" alt="CodeIgniter"></a>
    <a href="https://www.mysql.com/"><img src="https://img.shields.io/badge/MySQL-5.7%20--%208.0-4479A1?logo=mysql&logoColor=white" alt="MySQL"></a>
    <a href="#license"><img src="https://img.shields.io/badge/License-Proprietary-b91c1c.svg" alt="License"></a>
  </p>
</div>

---

## Table of Contents

- [Overview](#overview)
- [Prerequisites](#prerequisites)
- [Hosting & Deployment Guide](#hosting--deployment-guide)
  - [1. Local Hosting (PHP Built-in Server & XAMPP)](#1-local-hosting-php-built-in-server--xampp)
  - [2. cPanel Hosting](#2-cpanel-hosting)
  - [3. Linux / VPS Hosting (Ubuntu / Debian)](#3-linux--vps-hosting-ubuntu--debian)
  - [4. Cloudflare Deployment (Cloudflare Tunnel)](#4-cloudflare-deployment-cloudflare-tunnel)
  - [5. Netlify Hosting (Reverse Proxy / Edge Proxy)](#5-netlify-hosting-reverse-proxy--edge-proxy)
- [Data Storage & Persistence Architecture](#data-storage--persistence-architecture)
- [Where to Check Status & Logs on Each Platform](#where-to-check-status--logs-on-each-platform)
- [Core Application Routing](#core-application-routing)
- [License](#license)

---

## Overview

Rolexto CRM is a full-featured PHP/MySQL enterprise relationship management application designed for lead distribution, team performance monitoring, pipeline operational metrics, audit tracking, and multi-role ticket workflows.

---

## Prerequisites

| Component | Minimum Requirement | Recommended |
|---|---|---|
| **PHP** | 7.4 | 8.0 – 8.2 |
| **PHP Extensions** | `mysqli`, `mbstring`, `curl`, `gd`, `session`, `json` | Same |
| **Database** | MySQL 5.7+ / MariaDB 10.3+ | MySQL 8.0+ |
| **Web Server** | Apache (`mod_rewrite` enabled) or Nginx | Apache 2.4+ |

---

## Hosting & Deployment Guide

### 1. Local Hosting (PHP Built-in Server & XAMPP)

#### Option A: PHP CLI Server (Fastest)
1. Ensure MySQL daemon is running on port `3306` (e.g. via XAMPP or native service).
2. Start the built-in server from the project directory:
   ```bash
   php -S localhost:8080 index.php
   ```
3. Open `http://localhost:8080/manager/login` in your web browser.

#### Option B: XAMPP / Laragon / WAMP
1. Move or clone project files into the web root:
   - **XAMPP**: `C:\xampp\htdocs\crm`
   - **Laragon**: `C:\laragon\www\crm`
2. Update base URL in `application/config/config.php`:
   ```php
   $config['base_url'] = 'http://localhost/crm/';
   ```
3. Import database schema into `localhost/phpmyadmin` under database `jaipuuwy_crmrolexto`.
4. Configure `application/config/database.php`:
   ```php
   $db['default']['hostname'] = '127.0.0.1';
   $db['default']['username'] = 'root';
   $db['default']['password'] = '';
   $db['default']['database'] = 'jaipuuwy_crmrolexto';
   ```

---

### 2. cPanel Hosting

1. **Upload Files**:
   - In cPanel **File Manager**, navigate to `public_html/` (or your subdomain directory).
   - Upload the project archive (`.zip`) and extract it.
2. **Setup MySQL Database**:
   - Go to **MySQL Database Wizard** in cPanel.
   - Create database name (e.g., `cpaneluser_crm`) and database user with full privileges.
   - Open **phpMyAdmin**, select the newly created database, and import the SQL file (`database/localhost.sql`).
3. **Configure Connection**:
   - Edit `application/config/database.php`:
     ```php
     $db['default']['hostname'] = 'localhost';
     $db['default']['username'] = 'cpaneluser_crmuser';
     $db['default']['password'] = 'YourStrongPassword';
     $db['default']['database'] = 'cpaneluser_crm';
     ```
   - Edit `application/config/config.php`:
     ```php
     $config['base_url'] = 'https://crm.yourdomain.com/';
     ```
4. **Verify URL Rewriting**:
   - Ensure `.htaccess` exists in root with:
     ```apache
     RewriteEngine On
     RewriteCond %{REQUEST_FILENAME} !-f
     RewriteCond %{REQUEST_FILENAME} !-d
     RewriteRule ^(.*)$ index.php/$1 [L]
     ```

---

### 3. Linux / VPS Hosting (Ubuntu / Debian)

1. **Install Dependencies**:
   ```bash
   sudo apt update
   sudo apt install -y apache2 php php-mysqli php-mbstring php-curl php-gd php-xml mysql-server
   ```
2. **Deploy Codebase**:
   ```bash
   sudo git clone https://github.com/Reinhart-py/crmV2.git /var/www/html/crm
   sudo chown -R www-data:www-data /var/www/html/crm
   sudo chmod -R 755 /var/www/html/crm
   sudo chmod -R 775 /var/www/html/crm/application/cache /var/www/html/crm/application/logs
   ```
3. **Configure Apache VirtualHost**:
   Create `/etc/apache2/sites-available/crm.conf`:
   ```apache
   <VirtualHost *:80>
       ServerName crm.yourdomain.com
       DocumentRoot /var/www/html/crm

       <Directory /var/www/html/crm>
           Options -Indexes +FollowSymLinks
           AllowOverride All
           Require all granted
       </Directory>

       ErrorLog ${APACHE_LOG_DIR}/crm_error.log
       CustomLog ${APACHE_LOG_DIR}/crm_access.log combined
   </VirtualHost>
   ```
4. **Enable Site & Rewrite**:
   ```bash
   sudo a2enmod rewrite
   sudo a2ensite crm.conf
   sudo systemctl restart apache2
   ```

---

### 4. Cloudflare Deployment (Cloudflare Tunnel)

Because Rolexto CRM is a server-side PHP/MySQL dynamic application, fronting it through Cloudflare is done via **Cloudflare Tunnel (`cloudflared`)** or Cloudflare Proxy:

1. **Install Cloudflare Tunnel (`cloudflared`)** on your VPS or local machine:
   ```bash
   # Ubuntu/Debian
   curl -L --output cloudflared.deb https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-amd64.deb
   sudo dpkg -i cloudflared.deb
   ```
2. **Authenticate & Create Tunnel**:
   ```bash
   cloudflared tunnel login
   cloudflared tunnel create crm-tunnel
   ```
3. **Route Traffic**:
   In `~/.cloudflared/config.yml`:
   ```yaml
   tunnel: <TUNNEL_UUID>
   credentials-file: /root/.cloudflared/<TUNNEL_UUID>.json
   ingress:
     - hostname: crm.yourdomain.com
       service: http://localhost:8080
     - service: http_status:404
   ```
4. **Run Tunnel**:
   ```bash
   cloudflared tunnel run crm-tunnel
   ```
   *Benefit*: Complete zero-trust edge protection with free SSL, DDoS mitigation, and no open router ports.

---

### 5. Netlify Hosting (Reverse Proxy / Edge Proxy)

Netlify serves static assets and Serverless Functions. To route traffic through Netlify to your active CRM backend:

1. **Proxy Configuration (`_redirects`)**:
   Add this rule to `_redirects` in the root:
   ```
   /*  https://your-backend-vps.com/:splat  200!
   ```
2. **Deploy via Netlify CLI or Git**:
   ```bash
   netlify deploy --prod --dir=.
   ```
3. All requests to your Netlify custom domain are proxied over HTTPS directly to your PHP backend while leveraging Netlify's global CDN for static assets.

---

## Data Storage & Persistence Architecture

| Data Category | Storage Location | Key Database Tables / Paths | Notes |
|---|---|---|---|
| **Leads & Pipeline** | MySQL Database | `ci_leads`, `ci_leads_followup`, `ci_meetings`, `ci_terms` | Tracks all customer details, MRR, status, and followup dates |
| **System Audit Logs** | MySQL Database | `ci_admin_logs` | Logs user logins, failed attempts, logouts, IP addresses, timestamps |
| **User Directory** | MySQL Database | `ci_user`, `ci_roles` | Staff credentials, roles (Admin=1, Manager=2, Executive=3, Support=4) |
| **Support Tickets** | MySQL Database | `ci_tickets`, `ci_ticket_replies` | Support queries, conversation thread history, status lifecycle |
| **Uploaded Files** | Server Filesystem | `assets/uploads/` | Lead imports, attachments, user profile pictures |
| **User Sessions** | MySQL Database / Cache | `ci_session` table or `application/cache/` | Encrypted session tokens with 5-minute inactivity timeout |

---

## Where to Check Status & Logs on Each Platform

| Platform | Where to Check Logs & Errors | Where Data is Stored & Checked |
|---|---|---|
| **Local (PHP Server)** | Terminal console output where `php -S` is running | phpMyAdmin (`localhost/phpmyadmin`) or MySQL CLI |
| **Local (XAMPP)** | `C:\xampp\apache\logs\error.log` and `php_error_log` | MySQL data in `C:\xampp\mysql\data\` |
| **cPanel** | cPanel &rarr; **Metrics** &rarr; **Errors** log viewer | cPanel &rarr; **phpMyAdmin** &rarr; select database |
| **Linux / VPS** | `/var/log/apache2/error.log` & `journalctl -u mysql` | `/var/lib/mysql/` or via command-line MySQL client |
| **Cloudflare** | Cloudflare Dashboard &rarr; **Zero Trust** &rarr; **Access** &rarr; **Logs** | Edge analytics, request count, and SSL status in Cloudflare UI |
| **Netlify** | Netlify Dashboard &rarr; **Deploys** &rarr; **Function Logs** | Deployed commits and edge redirect analytics |
| **Application Audit** | Web UI: `http://<your-host>/manager/admin-logs` | `ci_admin_logs` table (real-time filterable audit trail) |

---

## Core Application Routing

| Route | Controller / Action | Access Role |
|---|---|---|
| `/manager/dashboard` | `ci_admin/dashboard` | All authenticated staff |
| `/manager/admin-logs` | `ci_admin_system/logs` | Administrator (Role 1) |
| `/manager/team/leads` | `ci_admin_team/leads` | Managers & Admins |
| `/manager/team/assignleads` | `ci_admin_team/assignleads` | Admin & Support |
| `/manager/tickets` | `ci_admin_tickets/index` | All authenticated staff |
| `/manager/tickets/view/(:num)` | `ci_admin_tickets/view/$1` | All authenticated staff |
| `/manager/leads/import` | `ci_admin_leads/import` | Admin & Support |
| `/manager/leads/export` | `ci_admin_leads/export` | Administrator (Role 1) |

---

## License

Distributed under the **Rolexto Group Enterprise License**.  
&copy; 2024–2026 Rolex to Group. All rights reserved.
