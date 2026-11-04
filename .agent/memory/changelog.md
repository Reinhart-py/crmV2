# Changelog

## 2026-11-04

### group_start() fatal error — fixed across all files
- Injected `group_start()`/`group_end()` shim into `system/database/DB_active_rec.php`
- Removed/replaced `group_start()` calls in: `ci_admin_orders.php`, `ci_admin_kam.php`, `ci_admin_model.php`, `ci_admin_leads.php`, `ci_admin_report.php`

### Login page redesign
- `application/views/manager/loginpage.php` rewritten
- Split-screen layout: form left, illustration right
- Removed Sign Up link, Google/Facebook social buttons
- Purple theme matching reference screenshot

### Navigation: Sales Approval workflow
- `header.php`: Back Office "Pending Sales Approval" → "View Sales Approvals"
- `header.php`: KAM submenu: added "Pending Sales Approval"
- `ci_admin_orders.php`: added `view_sales_approvals()` method (Back Office read-only)
- `routes.php`: added `manager/orders/view_sales_approvals`

### Add Database form simplification
- `views/manager/leads/add-database.php`: fields after Email wrapped in collapsible "Additional Details" toggle section
- Basic always-visible: Company Name, POC Name, POC Contact, Email + toggle button
- Everything else (Account Number, Trade License, etc.) hidden until user clicks toggle
