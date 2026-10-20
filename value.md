# CRM Master Value Reference & Complete Data Dictionary

This document serves as the single source of truth for **all values, dropdown options, master statuses, database columns, formulas, permissions, and workflows across every page** in the CRM system.

---

## 1. System Navigation & Page Architecture

| Module / Page | Route URL | Controller | Primary View | Key Functions & Values |
|---|---|---|---|---|
| **Dashboard** | `/manager/dashboard` | `ci_admin.php` | `manager/dashboard.php` | Summary tiles, pipeline counts, MRR metrics, Team vs My toggle |
| **All Leads** | `/manager/leads` | `ci_admin_leads.php` | `manager/leads/index.php` | Master lead table, search, filters, batch actions, status badges |
| **Add Lead** | `/manager/leads/add` | `ci_admin_leads.php` | `manager/leads/addleads.php` | Full customer creation form, trade license, tax, assignment |
| **Edit Lead** | `/manager/leads/edit/{id}` | `ci_admin_leads.php` | `manager/leads/editleads.php` | Lead modification, status updates, disposition remarks |
| **Single Lead View** | `/manager/leads/view/{id}` | `ci_admin_leads.php` | `manager/leads/single_view.php` | Lead profile, timeline, DNCR badge, closed badge, meeting history |
| **Follow-ups** | `/manager/leads/followups` | `ci_admin_leads.php` | `manager/leads/followups.php` | Follow-up call logs, scheduled dates, reminder status |
| **Meetings** | `/manager/meetings` | `ci_admin_meetings.php` | `manager/meetings/index.php` | Meeting schedules, location, POC, client confirmation |
| **Calling Dashboard** | `/manager/calls` | `ci_admin_calls.php` | `manager/calls/dashboard.php` | Queue-based outbound dialer, 60s wrap timer, call dispositions |
| **Download Reports** | `/manager/download-report` | `ci_admin_report.php` | `manager/report/admin/view.php` | 7 report types, summary pivot export, date range filters |
| **Report Drill-down** | `/manager/download-report/drilldown` | `ci_admin_report.php` | `manager/report/admin/drilldown.php` | Record-level audit, filtered CSV export (Admin/Support only) |
| **Verticals** | `/manager/verticals` | `ci_admin_verticals.php` | `manager/verticals/index.php` | Industry databases, vertical categorization, lead conversion |
| **Tasks** | `/manager/tasks` | `ci_admin_tasks.php` | `manager/tasks/index.php` | Operational tasks, deadlines, assigned personnel |
| **Tickets / Support** | `/manager/tickets` | `ci_admin_tickets.php` | `manager/tickets/index.php` | Customer issues, ticket threads, resolution workflow |
| **Service Delivery** | `/manager/service_delivery` | `ci_admin_service_delivery.php` | `manager/service_delivery/index.php` | Fulfillment orders, technician assignment, delivery status |
| **Payments** | `/manager/payments` | `ci_admin_payments.php` | `manager/payments/index.php` | Receipts, invoices, payment modes, financial tracking |
| **Team Management** | `/manager/team` | `ci_admin_team.php` | `manager/team/index.php` | User accounts, hierarchy, department assignments |
| **Roles & Permissions** | `/manager/roles` | `ci_admin_roles.php` | `manager/roles/index.php` | Role levels, module authorization, access rights |
| **Custom Fields & Terms**| `/manager/terms` | `ci_admin_terms.php` | `manager/terms/index.php` | Dropdown master lists, categories, data sources |
| **System Settings** | `/manager/setting` | `ci_admin_setting.php` | `manager/setting/index.php` | Global company info, email templates, system configuration |

---

## 2. User Roles & Access Hierarchy

### Roles Table (`ci_roles`)
| Role ID (`m_role`) | Role Title | Hierarchy Level | Capabilities & Permissions |
|---|---|---|---|
| **1** | **Admin** | Level 1 (Full) | Complete unrestricted access: view all leads, view all team reports, full CSV/drill-down exports, delete records, system settings. |
| **2** | **Back Office / Manager** | Level 2 | View subordinate leads (`ci_user.manager = session.manager_id`), team dashboard, pivot summary exports only. Record-level exports blocked. |
| **3** | **User / Employee** | Level 3 | Individual access: view own assigned leads (`lead_assign = session.manager_id`), individual performance, pivot summary exports only. Record-level exports blocked. |
| **4** | **Support** | Level 1 (Auditor) | Unrestricted read access: view all company leads, system maintenance, full CSV/drill-down exports, ticket escalations. |

### Departments (`ci_user.department`)
- `Sales`
- `Accounts`
- `Operations`
- `IT`
- `HR`
- `Management`

---

## 3. Lead Master Statuses & Categories

### Lead Status Table (`ci_status`)
| Status ID | Status Name | Category Type | Meaning & Workflow Position |
|---|---|---|---|
| **36** | **Database** | Pipeline (Lead) | Raw or initial imported lead waiting for first contact. |
| **37** | **Expected** | Pipeline (Active) | Positive response received; quote or proposal discussed. |
| **38** | **In Process** | Pipeline (Active) | Active negotiations, KYC collection, documentation in progress. |
| **39** | **Closed** | Deal (Won) | Deal successfully completed and revenue booked. |
| **40** | **Submitted** | Pipeline (Active) | Application or contract submitted to authority/service provider. |
| **41** | **Pending Activation** | Pipeline (Active) | Awaiting service activation, payment confirmation, or card issuance. |
| **42** | **Activated** | Deal (Won) | Service live and fully functioning. |
| **43** | **Lost** | Closed (Lost) | Deal dropped or cancelled by customer. Requires `lost_reason`. |
| **44** | **Invalid Entry** | Disqualified | Wrong number, duplicate entry, out of business, or junk lead. |

### Company Status (`ci_leads.company_status`)
- **`Active`** (Default): Included in all regular operations, reports, and dashboards.
- **`Inactive`**: Account paused or dormant.
- **`Closed`**: Permanently shut down. **Excluded from all reports, pivot summaries, and dashboard performance counters.**

### Lead Categories (`ci_leads.category`)
- `New`: First-time customer enquiry or new acquisition.
- `Existing`: Prior customer with new requirements or renewal.

### Lead Temperature (`ci_terms.cat_type = 'lead_cat'`)
- `76` | **Database**
- `77` | **Cold**
- `78` | **Warm**
- `79` | **Hot**
- `80` | **Instant**

### Lead Sources (`ci_sources`)
- `1` | **New Database**
- `2` | **Online**
- `3` | **Reference**
- `4` | **Ported Out**
- `5` | **Competitor DB**
- `6` | **Contract Agent**

### Data Sources (`ci_terms.cat_type = 'data_source'`)
- `Google Maps`
- `GPS`
- `Referral`
- `Website`
- `Social Media`
- `Other`

### Database Categories (`ci_terms.cat_type = 'database_cat'`)
- `New Database`
- `Online`
- `Reference`
- `Ported Out`
- `Competitor DB`
- `Contract Agent`
- `Raw / Unverified`
- `Cleansed`
- `High Value`
- `Re-engaged`
- `Other`

### Business Categories (`ci_terms.cat_type = 'business_cat'`)
- `Trading`
- `Retail`
- `Corporate Services`
- `Information Technology`
- `Real Estate`
- `Hospitality & Tourism`
- `Healthcare`
- `Manufacturing`
- `Finance & Accounting`
- `Logistics & Transport`
- `Other`

### Product & Service Categories (`ci_terms.cat_type = 'p_cat'`)
- **Telecom**: Fixed, Mobile, Managed, All, Other
- **Accounting**: Registration, Bookkeeping, Audit, Corporate Tax, All, Other
- **IT**: Server, Networking, Cloud Solutions, Hardware, Other
- **Other**

---

## 4. Trade License Authorities & Dependent Filtering

### Emirates (`tl_emirate`)
- `Abu Dhabi`
- `Dubai`
- `Sharjah`
- `Ajman`
- `Ras Al Khaimah`
- `Fujairah`
- `Umm Al Quwain`

### Zones (`tl_zone`)
- `Mainland`
- `Freezone`

### Licensing Authorities Directory (`tl_authority`) with Mappings

| Licensing Authority | Emirate (`data-emirate`) | Zone (`data-zone`) |
|---|---|---|
| **Abu Dhabi DED** | Abu Dhabi | Mainland |
| **ADGM (Abu Dhabi Global Market)** | Abu Dhabi | Freezone |
| **Masdar City Free Zone** | Abu Dhabi | Freezone |
| **KIZAD / KEZAD** | Abu Dhabi | Freezone |
| **twofour54** | Abu Dhabi | Freezone |
| **Dubai Economy (DED / DET)** | Dubai | Mainland |
| **DMCC (Dubai Multi Commodities Centre)** | Dubai | Freezone |
| **IFZA (International Free Zone Authority)** | Dubai | Freezone |
| **DIFC (Dubai International Financial Centre)** | Dubai | Freezone |
| **DAFZA (Dubai Airport Freezone)** | Dubai | Freezone |
| **JAFZA (Jebel Ali Free Zone)** | Dubai | Freezone |
| **DSOA / Dubai Silicon Oasis** | Dubai | Freezone |
| **DWTC (Dubai World Trade Centre)** | Dubai | Freezone |
| **Meydan Free Zone** | Dubai | Freezone |
| **Dubai South (DWC)** | Dubai | Freezone |
| **Dubai Internet City (TECOM)** | Dubai | Freezone |
| **Dubai Media City (TECOM)** | Dubai | Freezone |
| **Dubai Knowledge Park (TECOM)** | Dubai | Freezone |
| **Dubai Production City (TECOM)** | Dubai | Freezone |
| **Dubai Studio City (TECOM)** | Dubai | Freezone |
| **Dubai Outsource City (TECOM)** | Dubai | Freezone |
| **Dubai Science Park (TECOM)** | Dubai | Freezone |
| **Dubai Design District (d3)** | Dubai | Freezone |
| **Dubai Maritime City** | Dubai | Freezone |
| **Dubai Healthcare City (DHCC)** | Dubai | Freezone |
| **Dubai CommerCity** | Dubai | Freezone |
| **Sharjah SEDD** | Sharjah | Mainland |
| **SHAMS (Sharjah Media City)** | Sharjah | Freezone |
| **SAIF Zone (Sharjah Airport International Free Zone)** | Sharjah | Freezone |
| **HFZA (Hamriyah Free Zone)** | Sharjah | Freezone |
| **SPCFZ (Sharjah Publishing City Free Zone)** | Sharjah | Freezone |
| **SRTI Park (Sharjah Research Tech & Innovation)** | Sharjah | Freezone |
| **Ajman DED** | Ajman | Mainland |
| **AFZA (Ajman Free Zone)** | Ajman | Freezone |
| **Ajman Media City Free Zone (AMCFZ)** | Ajman | Freezone |
| **RAK DED** | Ras Al Khaimah | Mainland |
| **RAKEZ (Ras Al Khaimah Economic Zone)** | Ras Al Khaimah | Freezone |
| **RAK Maritime City** | Ras Al Khaimah | Freezone |
| **Fujairah Municipality** | Fujairah | Mainland |
| **Fujairah Free Zone (FFZ)** | Fujairah | Freezone |
| **Creative City Fujairah** | Fujairah | Freezone |
| **UAQ DED** | Umm Al Quwain | Mainland |
| **UAQ FTZ (Umm Al Quwain Free Trade Zone)** | Umm Al Quwain | Freezone |

---

## 5. Operations & Calling Dashboard (`/manager/calls`)

### Calling Workflow & Fields
- **Contact Details**: Company / Customer, Primary POC, Primary Mobile (`contact1`), Alternative Contact, Email.
- **Trade License Information**: Trade License Number, Authority, Emirate, Zone indicator badge (`Mainland` vs `Freezone`).
- **Revenue Overview**: Quoted Amount, Monthly Recurring Revenue (`mrr`), Required Amount (`r_amount`).
- **Schedule Info**: Last Follow-up Date, Next Follow-up Date, Current Status badge.

### Call Dispositions (`call_disposition`)
- `Connected - Interested`
- `Connected - Follow-up Required`
- `Connected - Meeting Fixed`
- `Connected - Quote Requested`
- `Connected - Not Interested`
- `Busy / Call Waiting`
- `No Answer / Ringing`
- `Left Voicemail`
- `Wrong Number / Invalid`
- `DND Requested (DNCR)`

### Call Controls & Timer
- **Countdown Timer**: 60 seconds wrap-up / disposition timer triggered upon call completion.
- **Auto-Call Mode**: Toggle switch between Automatic next call (after timer expiry) and Manual call selection.
- **Queue Actions**: Next Call, Skip Call, Mark as DNCR, Instant Schedule Follow-up / Meeting.

---

## 6. Reports Master Definitions & Formulas

### 6.1 Performance Report
- **My Report**: `ci_user.id = session.manager_id`
- **Team Report**: `ci_user.manager = session.manager_id`
- **Company Report**: All users (`manager_role IN (1, 4)`)

| Metric Name | SQL Formula / Calculation | Condition |
|---|---|---|
| **Total Leads** | `COUNT(ci_leads.id)` | `(lead_assign = user_id OR created_by = user_id)` |
| **Active Deals** | `COUNT(ci_leads.id)` | `status IN (36, 37, 38, 40, 41)` |
| **Closed Deals** | `COUNT(ci_leads.id)` | `status IN (39, 42)` |
| **Lost Deals** | `COUNT(ci_leads.id)` | `status IN (43, 44) OR LOWER(status_category) LIKE '%lost%' OR LOWER(lost_reason) LIKE '%lost%'` |
| **Follow-ups Attended**| `COUNT(ci_followup.id)` | `user_id = user_id AND followup_remark != ''` |
| **Total Quoted Amount**| `SUM(ci_leads.quote_amount)` | All assigned leads within date range |
| **Total MRR** | `SUM(ci_leads.mrr)` | All assigned leads within date range |
| **Total Lost MRR** | `SUM(ci_leads.mrr)` | Lost deals condition |
| **Total Closed MRR** | `SUM(ci_leads.mrr)` | Closed deals condition (`status IN (39, 42)`) |

### 6.2 Trade License Report
Grouped by Licensing Authority (`tl_authority`). Excludes closed companies (`company_status != 'Closed'`).

| Expiry Category | Date Range Criteria |
|---|---|
| **Expired** | `tl_expire_date < CURDATE() OR tl_expire_date IS NULL OR tl_expire_date = '' OR tl_expire_date = '0000-00-00'` |
| **0–30 Days** | `tl_expire_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)` |
| **30–60 Days** | `tl_expire_date BETWEEN DATE_ADD(CURDATE(), INTERVAL 31 DAY) AND DATE_ADD(CURDATE(), INTERVAL 60 DAY)` |
| **60–90 Days** | `tl_expire_date BETWEEN DATE_ADD(CURDATE(), INTERVAL 61 DAY) AND DATE_ADD(CURDATE(), INTERVAL 90 DAY)` |
| **All Future** | `tl_expire_date > DATE_ADD(CURDATE(), INTERVAL 90 DAY)` |

### 6.3 General Reports
- **Database Report**: Grouped by `lead_source` and `database_category`.
- **Quote Report**: Grouped by `quote_status` and `quote_mode` with sum of `quote_amount`.
- **Lost Report**: Grouped by `lost_reason` and `category` with sum of lost `mrr`.
- **Lead Report**: Grouped by `lead_source`, `lead_assign`, and `status`.
- **Tax Report**: Grouped by `vat_status` and `corporate_tax_status`.

---

## 7. Payments & Billing Values (`ci_payments`)

| Field | Type | Allowed Values & Formats |
|---|---|---|
| **payment_mode** | `VARCHAR(50)` | `Cash`, `Bank Transfer`, `Cheque`, `Credit Card`, `Debit Card`, `Online Gateway` |
| **amount** | `DECIMAL(12,2)` | Numeric currency amount in AED |
| **receipt_no** | `VARCHAR(50)` | Sequential receipt format (e.g., `RCP-2026-0001`) |
| **invoice_no** | `VARCHAR(50)` | Referenced billing invoice (e.g., `INV-2026-0001`) |

---

## 8. Service Delivery & Fulfillment Values (`ci_service_delivery`)

| Field | Type | Allowed Values |
|---|---|---|
| **status** | `VARCHAR(50)` | `Pending`, `Assigned`, `In Progress`, `Under Review`, `Completed`, `Cancelled` |
| **vertical** | `VARCHAR(100)` | `Telecom`, `Accounting`, `Taxation`, `IT Infrastructure`, `PRO Services` |

---

## 9. Visual Indicators & Badges

| Badge | Color Code | Placement | Trigger Condition |
|---|---|---|---|
| **DNCR (DND)** | Red (`#ef4444`) | Top Header Box / Lead Tables | `dnd_status = 'Yes'` |
| **Closed Company**| Blue (`#2563eb`) | Top Header Box / Lead Tables | `company_status = 'Closed'` |
| **Mainland** | Gray / Dark (`#475569`) | Header & Form Selector | `tl_zone = 'Mainland'` |
| **Freezone** | Teal / Cyan (`#0d9488`) | Header & Form Selector | `tl_zone = 'Freezone'` |
| **Active Status** | Emerald (`#10b981`) | Status Badges | `status IN (39, 42)` |
| **Pending Status**| Amber (`#f59e0b`) | Status Badges | `status IN (36, 37, 38, 40, 41)` |
| **Lost Status** | Crimson (`#dc2626`)| Status Badges | `status IN (43, 44)` |

---

## 10. Global UI Color Schemes

Configured via `localStorage.crm_global_palette` or `localStorage.crm_palette`:
- **`red`** (Default): Primary `#dd4b39`, Dark `#c23321`, Accent `#f39c12`
- **`blue`**: Primary `#3c8dbc`, Dark `#286090`, Accent `#00c0ef`
- **`green`**: Primary `#00a65a`, Dark `#00733e`, Accent `#001f3f`
- **`black`**: Primary `#222d32`, Dark `#1a2226`, Accent `#605ca8`
