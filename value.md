# CRM Value Reference & Data Dictionary

Comprehensive mapping of report metrics, source database tables, fields, formulas, and filter conditions across all reports and lead management modules.

---

## 1. Performance Report (Pivot & Drill-down)

### Hierarchy & Grouping
- **My Report**: Metrics for the currently logged-in user (`ci_user.id = session.manager_id`).
- **Team Report**: Metrics for direct reportees (`ci_user.manager = session.manager_id`).
- **Admin / Support**: Full hierarchy across all departments (`m_role IN (1, 4)`).

### Column Definitions (Exact Order)

| # | Column Name | Source Table(s) | Source Field(s) | Calculation / SQL Formula | Conditions & Filters |
|---|---|---|---|---|---|
| 1 | **Name** | `ci_user` | `name` | `ci_user.name` | Active users (`ci_user.status = 1`) |
| 2 | **Department** | `ci_user` | `department` | `COALESCE(ci_user.department, 'Sales')` | User department assignment |
| 3 | **Total Leads** | `ci_leads` | `id` | `COUNT(ci_leads.id)` | `(lead_assign = user_id OR created_by = user_id)` AND date filter on `created` |
| 4 | **Active Deals** | `ci_leads` | `status` | `COUNT(ci_leads.id)` | `(lead_assign = user_id OR created_by = user_id)` AND `status IN (36, 37, 38, 40, 41)` AND date filter on `created` |
| 5 | **Closed Deals** | `ci_leads` | `status` | `COUNT(ci_leads.id)` | `(lead_assign = user_id OR created_by = user_id)` AND `status IN (39, 42)` AND date filter on `created` |
| 6 | **Lost Deals** | `ci_leads` | `status`, `status_category`, `lost_reason` | `COUNT(ci_leads.id)` | `(lead_assign = user_id OR created_by = user_id)` AND `(status IN (43, 44) OR LOWER(status_category) LIKE '%lost%' OR LOWER(lost_reason) LIKE '%lost%')` AND date filter on `created` |
| 7 | **Followups Attended** | `ci_followup` | `id`, `followup_remark` | `COUNT(ci_followup.id)` | `user_id = user_id AND followup_remark != ''` AND date filter on `followup_date` |
| 8 | **Total Quoted Amount** | `ci_leads` | `quote_amount` | `SUM(ci_leads.quote_amount)` | `(lead_assign = user_id OR created_by = user_id)` AND date filter on `created` |
| 9 | **Total MRR** | `ci_leads` | `mrr` | `SUM(ci_leads.mrr)` | `(lead_assign = user_id OR created_by = user_id)` AND date filter on `created` |
| 10 | **Total Lost MRR** | `ci_leads` | `mrr`, `status`, `status_category`, `lost_reason` | `SUM(ci_leads.mrr)` | `(lead_assign = user_id OR created_by = user_id)` AND `(status IN (43, 44) OR LOWER(status_category) LIKE '%lost%' OR LOWER(lost_reason) LIKE '%lost%')` AND date filter on `created` |
| 11 | **Total Closed MRR** | `ci_leads` | `mrr`, `status` | `SUM(ci_leads.mrr)` | `(lead_assign = user_id OR created_by = user_id)` AND `status IN (39, 42)` AND date filter on `created` |

### Drill-Down Navigation
Clicking any numeric value opens `/manager/download-report/drilldown` with parameters:
- `report=performance`
- `metric`: `leads`, `active`, `closed`, `lost`, `followup`, `quoted`, `mrr`, `lost_mrr`, `closed_mrr`
- `uid`: User ID
- `d1`, `d2`: Filter dates
- Displays individual records with Lead ID, Customer / Company, Mobile, Email, Category, Status badge, Amounts, Created Date, and direct link to Lead view.

---

## 2. Trade License Report

### Grouping
- Grouped by `Licensing Authority` (`ci_leads.tl_authority`), defaulting to `General Licensing` if empty or null.

### Column Definitions (Exact Order)

| # | Column Name | Source Table | Source Field | Calculation / Date Logic | Description |
|---|---|---|---|---|---|
| 1 | **Licensing Authority** | `ci_leads` | `tl_authority` | `COALESCE(tl_authority, 'General Licensing')` | Authority issuing license (e.g., DED, SHAMS, IFZA) |
| 2 | **Expiry** | `ci_leads` | `tl_expire_date` | `tl_expire_date < CURDATE() OR tl_expire_date IS NULL OR tl_expire_date = '' OR tl_expire_date = '0000-00-00'` | Already expired trade licenses |
| 3 | **60–90 Days** | `ci_leads` | `tl_expire_date` | `tl_expire_date BETWEEN DATE_ADD(CURDATE(), INTERVAL 61 DAY) AND DATE_ADD(CURDATE(), INTERVAL 90 DAY)` | Expiring between 61 and 90 days from today |
| 4 | **30–60 Days** | `ci_leads` | `tl_expire_date` | `tl_expire_date BETWEEN DATE_ADD(CURDATE(), INTERVAL 31 DAY) AND DATE_ADD(CURDATE(), INTERVAL 60 DAY)` | Expiring between 31 and 60 days from today |
| 5 | **0–30 Days** | `ci_leads` | `tl_expire_date` | `tl_expire_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)` | Expiring within the next 30 days |

### Drill-Down Navigation
Clicking count opens `/manager/download-report/drilldown` with:
- `report=trade-license`
- `auth`: Licensing authority name
- `window`: `expiry`, `60-90`, `30-60`, `0-30`
- Columns: Company Name (`customer`), License Number (`tl_number`), Licensing Authority (`tl_authority`), Issue Date (`tl_issue_date`), Expiry Date (`tl_expire_date`), and Action button.

---

## 3. General Pivot Reports (Database, Quote, Lost, Lead, Tax)

All legacy "Count of", "COUNTA of", and "SUM of" prefixes have been removed:
- **Clean Column Headers**:
  - `Category`
  - `Subcategory`
  - `Record Count` / `Amount (AED)`
- **Summary Cards**:
  - Rendered in a single continuous horizontal row (`flex-wrap: nowrap; overflow-x: auto;`).
  - Compact padding (`10px 14px`) with bold clean metric titles.

---

## 4. Lead Management & Company Status

### Database Fields
- **Table**: `ci_leads`
- **Field**: `company_status VARCHAR(20) DEFAULT 'Active'`
- **Allowed Values**:
  - `Active` (Default)
  - `Inactive`
  - `Closed`

### Form & UI Locations
- **Add Lead Form** (`application/views/manager/leads/addleads.php`): Company Status dropdown with Active (selected), Inactive, Closed.
- **Edit Lead Form** (`application/views/manager/leads/editleads.php`): Pre-populates stored `company_status`.
- **Single Lead View** (`application/views/manager/leads/single_view.php`): Displays Company Status with colored badge.
- **CSV Import / Export** (`ci_admin_leads.php`): Includes `company_status` column in sample template and batch importer.

### Visual Badges in Header Box
- **DNCR Indicator**: Red badge (`background: #ef4444; color: #fff;`) placed in the top header box next to Category and Subcategory.
- **Closed Indicator**: Blue badge (`background: #2563eb; color: #fff;`) placed directly beside the DNCR badge on the same line when `company_status == 'Closed'`.

---

## 5. Security & Access Control

| Feature | Admin (`m_role = 1`) / Support (`m_role = 4`) | General Employee / Manager |
|---|---|---|
| **Summary Pivot CSV Export** | Allowed (`export=pivot`) | Allowed (`export=pivot`) |
| **Detailed Records CSV Export** | Allowed (`export=csv`) | Blocked (HTTP 403 Forbidden) |
| **Table Data Copying** | Allowed | Blocked via CSS `user-select: none;` and JS clipboard cancel |
| **Reports Landing Page** | Centered title "Reports", no extra description text | Centered title "Reports", no extra description text |
| **Back Navigation** | Dedicated left-aligned Back button on every report view | Dedicated left-aligned Back button on every report view |
