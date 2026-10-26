<?php
define('BASEPATH', 'dummy');
require_once __DIR__ . '/application/config/database.php';
$cfg = $db['default'];
$mysqli = new mysqli($cfg['hostname'], $cfg['username'], $cfg['password'], $cfg['database']);
if ($mysqli->connect_error) die("DB Error: " . $mysqli->connect_error . "\n");

$mysqli->query("SET SESSION innodb_strict_mode = 0");

function add_col_if_missing($mysqli, $table, $col, $def) {
    $res = $mysqli->query("SHOW COLUMNS FROM `$table` LIKE '$col'");
    if ($res && $res->num_rows == 0) {
        $q = "ALTER TABLE `$table` ADD `$col` $def";
        if ($mysqli->query($q)) {
            echo "Added `$col` to `$table`\n";
        } else {
            echo "Error adding `$col` to `$table`: " . $mysqli->error . "\n";
        }
    } else {
        echo "Column `$col` already in `$table`\n";
    }
}

// 1. ci_tasks table
$q_tasks = "CREATE TABLE IF NOT EXISTS `ci_tasks` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `task_id` VARCHAR(50) NULL,
    `customer` VARCHAR(255) NULL,
    `poc` VARCHAR(255) NULL,
    `contact1` VARCHAR(255) NULL,
    `alt_mobile` VARCHAR(100) NULL,
    `alt_contact` VARCHAR(100) NULL,
    `email` VARCHAR(255) NULL,
    `a_number` VARCHAR(255) NULL,
    `a_department` VARCHAR(255) NULL,
    `r_amount` VARCHAR(255) NULL,
    `r_date` VARCHAR(255) NULL,
    `category` VARCHAR(255) NULL,
    `business_category` VARCHAR(150) NULL,
    `s_cat` VARCHAR(255) NULL,
    `p_cat` VARCHAR(255) NULL,
    `p_details` TEXT NULL,
    `tl_number` VARCHAR(255) NULL,
    `tl_authority` VARCHAR(255) NULL,
    `tl_issue_date` VARCHAR(255) NULL,
    `tl_expire_date` VARCHAR(255) NULL,
    `qty` VARCHAR(255) NULL,
    `mrc` VARCHAR(255) NULL,
    `revenue` VARCHAR(255) NULL,
    `mrr` VARCHAR(255) NULL,
    `location` VARCHAR(255) NULL,
    `status` VARCHAR(50) NULL DEFAULT 'Open',
    `dnd_status` VARCHAR(10) NULL DEFAULT 'No',
    `dnd_check_date` VARCHAR(30) NULL,
    `close_date` VARCHAR(50) NULL,
    `remark` TEXT NULL,
    `address` TEXT NULL,
    `country` INT(11) NULL DEFAULT 0,
    `state` INT(11) NULL DEFAULT 0,
    `city` VARCHAR(255) NULL,
    `poc2` VARCHAR(255) NULL,
    `contact2` VARCHAR(255) NULL,
    `c_partner` VARCHAR(255) NULL,
    `otc` VARCHAR(255) NULL,
    `lead_cat` VARCHAR(255) NULL,
    `assigned_to` INT(11) NULL DEFAULT 0,
    `lead_source` VARCHAR(255) NULL,
    `followup_date` VARCHAR(50) NULL,
    `followup_time` VARCHAR(50) NULL,
    `m_date` VARCHAR(50) NULL,
    `m_time` VARCHAR(50) NULL,
    `created_by` INT(11) NULL DEFAULT 0,
    `created` VARCHAR(50) NULL,
    `modified` VARCHAR(50) NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB ROW_FORMAT=DYNAMIC DEFAULT CHARSET=utf8;";
if ($mysqli->query($q_tasks)) {
    echo "Table `ci_tasks` created / verified\n";
} else {
    echo "Error creating `ci_tasks`: " . $mysqli->error . "\n";
}

// 2. ci_tickets columns
add_col_if_missing($mysqli, 'ci_tickets', 'customer', "VARCHAR(255) NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'poc', "VARCHAR(255) NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'contact1', "VARCHAR(255) NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'alt_mobile', "VARCHAR(100) NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'alt_contact', "VARCHAR(100) NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'email', "VARCHAR(255) NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'a_number', "VARCHAR(255) NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'a_department', "VARCHAR(255) NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'r_amount', "VARCHAR(255) NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'r_date', "VARCHAR(255) NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'business_category', "VARCHAR(150) NULL");
add_col_if_missing($mysqli, 'ci_tickets', 's_cat', "VARCHAR(255) NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'p_cat', "VARCHAR(255) NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'p_details', "TEXT NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'tl_number', "VARCHAR(255) NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'tl_authority', "VARCHAR(255) NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'tl_issue_date', "VARCHAR(255) NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'tl_expire_date', "VARCHAR(255) NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'qty', "VARCHAR(255) NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'mrc', "VARCHAR(255) NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'revenue', "VARCHAR(255) NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'mrr', "VARCHAR(255) NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'location', "VARCHAR(255) NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'dnd_status', "VARCHAR(10) NULL DEFAULT 'No'");
add_col_if_missing($mysqli, 'ci_tickets', 'dnd_check_date', "VARCHAR(30) NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'close_date', "VARCHAR(50) NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'remark', "TEXT NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'address', "TEXT NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'country', "INT(11) NULL DEFAULT 0");
add_col_if_missing($mysqli, 'ci_tickets', 'state', "INT(11) NULL DEFAULT 0");
add_col_if_missing($mysqli, 'ci_tickets', 'city', "VARCHAR(255) NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'poc2', "VARCHAR(255) NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'contact2', "VARCHAR(255) NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'c_partner', "VARCHAR(255) NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'otc', "VARCHAR(255) NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'lead_cat', "VARCHAR(255) NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'assigned_to', "INT(11) NULL DEFAULT 0");
add_col_if_missing($mysqli, 'ci_tickets', 'lead_source', "VARCHAR(255) NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'followup_date', "VARCHAR(50) NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'followup_time', "VARCHAR(50) NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'm_date', "VARCHAR(50) NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'm_time', "VARCHAR(50) NULL");

// 3. ci_admin_logs table
$q_logs = "CREATE TABLE IF NOT EXISTS `ci_admin_logs` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NULL,
    `username` VARCHAR(255) NOT NULL,
    `ip_address` VARCHAR(100) NULL,
    `user_agent` VARCHAR(255) NULL,
    `activity` VARCHAR(100) NOT NULL,
    `status` VARCHAR(50) NOT NULL,
    `login_time` DATETIME NULL,
    `logout_time` DATETIME NULL,
    `details` TEXT NULL,
    `created_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
if ($mysqli->query($q_logs)) {
    echo "Table `ci_admin_logs` created / verified\n";
} else {
    echo "Error creating `ci_admin_logs`: " . $mysqli->error . "\n";
}

// 4. ci_terms default business categories
$business_cats = array(
    'Trading', 'Retail', 'Corporate Services', 'Information Technology',
    'Real Estate', 'Hospitality & Tourism', 'Healthcare', 'Manufacturing',
    'Finance & Accounting', 'Logistics & Transport', 'Other'
);
foreach ($business_cats as $bc) {
    $esc = $mysqli->real_escape_string($bc);
    $check = $mysqli->query("SELECT id FROM ci_terms WHERE cat_type='business_cat' AND title='$esc'");
    if ($check && $check->num_rows == 0) {
        $now = date('Y-m-d H:i:s');
        $mysqli->query("INSERT INTO ci_terms (parent_id, cat_type, title, created, status, delete_status) VALUES (0, 'business_cat', '$esc', '$now', 1, 0)");
        echo "Inserted business_cat: $bc\n";
    }
}

// 5. ci_tickets extended columns
add_col_if_missing($mysqli, 'ci_tickets', 'client_type', "VARCHAR(50) NULL DEFAULT 'Onboarded'");
add_col_if_missing($mysqli, 'ci_tickets', 'company_name', "VARCHAR(255) NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'company_id', "INT(11) NULL DEFAULT 0");
add_col_if_missing($mysqli, 'ci_tickets', 'ticket_type', "VARCHAR(100) NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'department', "VARCHAR(100) NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'ticket_title', "VARCHAR(255) NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'assigned_users', "TEXT NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'tagged_users', "TEXT NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'raised_by', "INT(11) NULL DEFAULT 0");
add_col_if_missing($mysqli, 'ci_tickets', 'created_date', "DATETIME NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'deadline_date', "DATE NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'expected_close_date', "DATE NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'last_updated_date', "DATETIME NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'last_updated_by', "INT(11) NULL DEFAULT 0");
add_col_if_missing($mysqli, 'ci_tickets', 'internal_notes', "TEXT NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'resolution_remarks', "TEXT NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'resolution_reviewed_by', "INT(11) NULL DEFAULT 0");
add_col_if_missing($mysqli, 'ci_tickets', 'resolution_confirmed_at', "DATETIME NULL");
add_col_if_missing($mysqli, 'ci_tickets', 'attachment', "VARCHAR(255) NULL");

// 6. ci_ticket_history table
$q_thistory = "CREATE TABLE IF NOT EXISTS `ci_ticket_history` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `ticket_id` INT(11) NOT NULL,
    `action` VARCHAR(100) NOT NULL,
    `old_status` VARCHAR(50) NULL,
    `new_status` VARCHAR(50) NULL,
    `old_assigned` TEXT NULL,
    `new_assigned` TEXT NULL,
    `remarks` TEXT NULL,
    `user_id` INT(11) NOT NULL,
    `user_name` VARCHAR(255) NOT NULL,
    `user_role` VARCHAR(100) NULL,
    `created_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_ticket_id` (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
if ($mysqli->query($q_thistory)) {
    echo "Table `ci_ticket_history` created / verified\n";
} else {
    echo "Error creating `ci_ticket_history`: " . $mysqli->error . "\n";
}

// 7. ci_ticket_followups table
$q_tfollow = "CREATE TABLE IF NOT EXISTS `ci_ticket_followups` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `ticket_id` INT(11) NOT NULL,
    `followup_date` DATE NOT NULL,
    `next_followup_date` DATE NULL,
    `assigned_user_id` INT(11) NULL,
    `remarks` TEXT NULL,
    `status` VARCHAR(50) NULL,
    `next_action` VARCHAR(255) NULL,
    `created_by` INT(11) NOT NULL,
    `created_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_ticket_id` (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
if ($mysqli->query($q_tfollow)) {
    echo "Table `ci_ticket_followups` created / verified\n";
} else {
    echo "Error creating `ci_ticket_followups`: " . $mysqli->error . "\n";
}

// 8. ci_user session and online tracking
add_col_if_missing($mysqli, 'ci_user', 'current_session_token', "VARCHAR(64) NULL");
add_col_if_missing($mysqli, 'ci_user', 'last_ip', "VARCHAR(100) NULL");
add_col_if_missing($mysqli, 'ci_user', 'last_active_at', "DATETIME NULL");
add_col_if_missing($mysqli, 'ci_user', 'is_online', "TINYINT(1) NULL DEFAULT 0");

// 9. Standard roles in ci_roles
$roles = array(
    1 => 'Admin',
    2 => 'Back Office',
    3 => 'Sales',
    4 => 'Support',
    5 => 'Operations',
    6 => 'IT',
    7 => 'HR'
);
foreach ($roles as $r_id => $r_title) {
    $r_esc = $mysqli->real_escape_string($r_title);
    $check_r = $mysqli->query("SELECT id FROM ci_roles WHERE id=$r_id");
    if ($check_r && $check_r->num_rows == 0) {
        $mysqli->query("INSERT INTO ci_roles (id, title, status, created) VALUES ($r_id, '$r_esc', 1, NOW())");
        echo "Inserted role: $r_title\n";
    }
}

add_col_if_missing($mysqli, 'ci_leads', 'quote_status', "VARCHAR(100) NULL");
add_col_if_missing($mysqli, 'ci_leads', 'quote_mode', "VARCHAR(100) NULL");
add_col_if_missing($mysqli, 'ci_leads', 'quote_amount', "DECIMAL(15,2) NULL");
add_col_if_missing($mysqli, 'ci_leads', 'industry_category', "VARCHAR(150) NULL");
add_col_if_missing($mysqli, 'ci_leads', 'corporate_tax_status', "VARCHAR(50) NULL");
add_col_if_missing($mysqli, 'ci_leads', 'vat_status', "VARCHAR(50) NULL");
add_col_if_missing($mysqli, 'ci_leads', 'vat_trn', "VARCHAR(100) NULL");
add_col_if_missing($mysqli, 'ci_leads', 'ct_trn', "VARCHAR(100) NULL");
add_col_if_missing($mysqli, 'ci_leads', 'data_source', "VARCHAR(150) NULL");
add_col_if_missing($mysqli, 'ci_leads', 'data_batch_referral_no', "VARCHAR(100) NULL");
add_col_if_missing($mysqli, 'ci_leads', 'database_category', "VARCHAR(100) NULL");
add_col_if_missing($mysqli, 'ci_leads', 'status_category', "VARCHAR(255) NULL");
add_col_if_missing($mysqli, 'ci_leads', 'status_remarks', "TEXT NULL");

$default_sources = array('Google Maps', 'GPS', 'Referral', 'Website', 'Social Media', 'Other');
foreach ($default_sources as $ds) {
    $ds_esc = $mysqli->real_escape_string($ds);
    $chk = $mysqli->query("SELECT id FROM ci_terms WHERE cat_type='data_source' AND title='$ds_esc'");
    if ($chk && $chk->num_rows == 0) {
        $mysqli->query("INSERT INTO ci_terms (title, cat_type, status, parent_id) VALUES ('$ds_esc', 'data_source', 1, 0)");
    }
}

$default_db_cats = array('New Database', 'Online', 'Reference', 'Ported Out', 'Competitor DB', 'Contract Agent', 'Raw / Unverified', 'Cleansed', 'High Value', 'Re-engaged', 'Other');
foreach ($default_db_cats as $dc) {
    $dc_esc = $mysqli->real_escape_string($dc);
    $chk = $mysqli->query("SELECT id FROM ci_terms WHERE cat_type='database_cat' AND title='$dc_esc'");
    if ($chk && $chk->num_rows == 0) {
        $mysqli->query("INSERT INTO ci_terms (title, cat_type, status, parent_id) VALUES ('$dc_esc', 'database_cat', 1, 0)");
    }
}

$mysqli->query("CREATE TABLE IF NOT EXISTS `ci_invoices` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `invoice_no` VARCHAR(50) NOT NULL,
    `invoice_date` DATE NOT NULL,
    `due_date` DATE NOT NULL,
    `customer_name` VARCHAR(255) NOT NULL,
    `customer_id` INT(11) NULL,
    `service_desc` TEXT NULL,
    `subtotal` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `vat_rate` DECIMAL(5,2) NOT NULL DEFAULT 5.00,
    `vat_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `total_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `amount_paid` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `balance_due` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `status` VARCHAR(50) NOT NULL DEFAULT 'Unpaid',
    `generated_by` INT(11) NULL,
    `created_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    KEY `idx_inv_status` (`status`),
    KEY `idx_inv_date` (`invoice_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$mysqli->query("CREATE TABLE IF NOT EXISTS `ci_payments` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `receipt_no` VARCHAR(50) NOT NULL,
    `invoice_no` VARCHAR(50) NULL,
    `customer_name` VARCHAR(255) NOT NULL,
    `payment_date` DATE NOT NULL,
    `payment_mode` VARCHAR(50) NOT NULL DEFAULT 'Bank Transfer',
    `amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `reference_no` VARCHAR(100) NULL,
    `recorded_by` INT(11) NULL,
    `remarks` TEXT NULL,
    `created_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    KEY `idx_pmt_date` (`payment_date`),
    KEY `idx_pmt_mode` (`payment_mode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$mysqli->query("CREATE TABLE IF NOT EXISTS `ci_expenses` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `voucher_no` VARCHAR(50) NOT NULL,
    `expense_date` DATE NOT NULL,
    `category` VARCHAR(100) NOT NULL,
    `amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `paid_to` VARCHAR(255) NOT NULL,
    `payment_mode` VARCHAR(50) NOT NULL DEFAULT 'Bank Transfer',
    `approved_by` INT(11) NULL,
    `recorded_by` INT(11) NULL,
    `description` TEXT NULL,
    `created_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    KEY `idx_exp_date` (`expense_date`),
    KEY `idx_exp_cat` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$mysqli->query("CREATE TABLE IF NOT EXISTS `ci_service_delivery` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `delivery_id` VARCHAR(50) NOT NULL,
    `customer_name` VARCHAR(255) NOT NULL,
    `lead_id` INT(11) NULL,
    `service_title` VARCHAR(255) NOT NULL,
    `vertical` VARCHAR(100) NULL,
    `order_date` DATE NOT NULL,
    `target_date` DATE NOT NULL,
    `completion_date` DATE NULL,
    `status` VARCHAR(50) NOT NULL DEFAULT 'Pending',
    `technician_id` INT(11) NULL,
    `remarks` TEXT NULL,
    `created_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    KEY `idx_sd_status` (`status`),
    KEY `idx_sd_date` (`order_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$mysqli->query("CREATE TABLE IF NOT EXISTS `ci_attendance` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL,
    `attendance_date` DATE NOT NULL,
    `in_time` TIME NULL,
    `out_time` TIME NULL,
    `total_hours` DECIMAL(4,2) NOT NULL DEFAULT 0.00,
    `status` VARCHAR(50) NOT NULL DEFAULT 'Present',
    `notes` TEXT NULL,
    `created_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_user_att_date` (`user_id`, `attendance_date`),
    KEY `idx_att_date` (`attendance_date`),
    KEY `idx_att_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$mysqli->query("CREATE TABLE IF NOT EXISTS `ci_leaves` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL,
    `leave_type` VARCHAR(50) NOT NULL,
    `from_date` DATE NOT NULL,
    `to_date` DATE NOT NULL,
    `total_days` INT(11) NOT NULL DEFAULT 1,
    `reason` TEXT NULL,
    `status` VARCHAR(50) NOT NULL DEFAULT 'Approved',
    `approved_by` INT(11) NULL,
    `created_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    KEY `idx_leave_user` (`user_id`),
    KEY `idx_leave_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$mysqli->query("CREATE TABLE IF NOT EXISTS `ci_system_maintenance` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `category` VARCHAR(100) NOT NULL,
    `scheduled_date` DATE NOT NULL,
    `completed_date` DATE NULL,
    `duration_mins` INT(11) NOT NULL DEFAULT 0,
    `status` VARCHAR(50) NOT NULL DEFAULT 'Completed',
    `executed_by` INT(11) NULL,
    `notes` TEXT NULL,
    `created_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    KEY `idx_maint_date` (`scheduled_date`),
    KEY `idx_maint_cat` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$check_inv = $mysqli->query("SELECT id FROM ci_invoices LIMIT 1");
if ($check_inv && $check_inv->num_rows == 0) {
    $mysqli->query("INSERT INTO `ci_invoices` (`invoice_no`, `invoice_date`, `due_date`, `customer_name`, `customer_id`, `service_desc`, `subtotal`, `vat_rate`, `vat_amount`, `total_amount`, `amount_paid`, `balance_due`, `status`, `generated_by`, `created_at`) VALUES
    ('INV-2026-001', '2026-10-01', '2026-10-15', 'Microexcel Technologies LLC', 1, 'Corporate Broadband 500Mbps - Annual Contract', 4500.00, 5.00, 225.00, 4725.00, 4725.00, 0.00, 'Paid', 1, NOW()),
    ('INV-2026-002', '2026-10-03', '2026-10-17', 'CMT Technologies LLC', 2, 'Dedicated Cloud PBX & 20 SIP Trunks', 2800.00, 5.00, 140.00, 2940.00, 2940.00, 0.00, 'Paid', 1, NOW()),
    ('INV-2026-003', '2026-10-05', '2026-10-20', 'Mogul Gems & Jewellery DMCC', 3, 'Managed Firewall & Multi-line Voice Package', 3500.00, 5.00, 175.00, 3675.00, 1800.00, 1875.00, 'Partially Paid', 1, NOW()),
    ('INV-2026-004', '2026-10-08', '2026-10-22', 'Swiss Gold DMCC', 4, 'Enterprise VPN & Security Gateway Service', 6200.00, 5.00, 310.00, 6510.00, 0.00, 6510.00, 'Unpaid', 1, NOW()),
    ('INV-2026-005', '2026-09-20', '2026-10-05', 'MarketVision Research Dubai', 5, 'Structured Cabling & Rack Setup', 1850.00, 5.00, 92.50, 1942.50, 0.00, 1942.50, 'Overdue', 1, NOW())");
}

$check_pmt = $mysqli->query("SELECT id FROM ci_payments LIMIT 1");
if ($check_pmt && $check_pmt->num_rows == 0) {
    $mysqli->query("INSERT INTO `ci_payments` (`receipt_no`, `invoice_no`, `customer_name`, `payment_date`, `payment_mode`, `amount`, `reference_no`, `recorded_by`, `remarks`, `created_at`) VALUES
    ('REC-2026-001', 'INV-2026-001', 'Microexcel Technologies LLC', '2026-10-08', 'Bank Transfer', 4725.00, 'ENBD-TXN-884920', 1, 'Full payment received via ENBD', NOW()),
    ('REC-2026-002', 'INV-2026-002', 'CMT Technologies LLC', '2026-10-10', 'Cheque', 2940.00, 'CHQ-554411-FAB', 1, 'Cheque cleared FAB branch', NOW()),
    ('REC-2026-003', 'INV-2026-003', 'Mogul Gems & Jewellery DMCC', '2026-10-12', 'Credit Card', 1800.00, 'STRIPE-CHG-9922', 1, 'Partial advance payment received', NOW())");
}

$check_exp = $mysqli->query("SELECT id FROM ci_expenses LIMIT 1");
if ($check_exp && $check_exp->num_rows == 0) {
    $mysqli->query("INSERT INTO `ci_expenses` (`voucher_no`, `expense_date`, `category`, `amount`, `paid_to`, `payment_mode`, `approved_by`, `recorded_by`, `description`, `created_at`) VALUES
    ('EXP-2026-001', '2026-10-01', 'Office Rent', 12500.00, 'Opal Tower Management', 'Cheque', 1, 1, 'Q4 Commercial Office Rent installment', NOW()),
    ('EXP-2026-002', '2026-10-04', 'Utilities', 1420.50, 'DEWA Dubai', 'Online', 1, 1, 'Electricity & water bill for Opal Tower office', NOW()),
    ('EXP-2026-003', '2026-10-06', 'Telecom & Internet', 850.00, 'Etisalat by e&', 'Bank Transfer', 1, 1, 'High-speed fiber office internet connection', NOW()),
    ('EXP-2026-004', '2026-10-09', 'Field & Travel', 450.00, 'Salik & Fuel Reimbursement', 'Cash', 1, 1, 'Client site visit transport allowance for sales team', NOW()),
    ('EXP-2026-005', '2026-10-12', 'Software & IT Subscriptions', 620.00, 'Google Workspace & Cloudflare', 'Credit Card', 1, 1, 'Monthly enterprise email & CDN security subscription', NOW())");
}

$check_sd = $mysqli->query("SELECT id FROM ci_service_delivery LIMIT 1");
if ($check_sd && $check_sd->num_rows == 0) {
    $mysqli->query("INSERT INTO `ci_service_delivery` (`delivery_id`, `customer_name`, `lead_id`, `service_title`, `vertical`, `order_date`, `target_date`, `completion_date`, `status`, `technician_id`, `remarks`, `created_at`) VALUES
    ('DEL-2026-001', 'Microexcel Technologies LLC', 1, 'Dedicated Leased Line 500M', 'Telecom', '2026-10-02', '2026-10-06', '2026-10-06', 'Accepted', 28, 'Fiber termination completed and tested. Client signed delivery order.', NOW()),
    ('DEL-2026-002', 'CMT Technologies LLC', 2, 'Cloud PBX Migration', 'IT', '2026-10-04', '2026-10-11', '2026-10-10', 'Delivered', 28, 'Extensions migrated and operational. Awaiting sign-off.', NOW()),
    ('DEL-2026-003', 'Mogul Gems & Jewellery DMCC', 3, 'UTM Firewall Installation', 'Security', '2026-10-08', '2026-10-18', NULL, 'In Progress', 28, 'Hardware dispatched, onsite configuration scheduled for tomorrow.', NOW()),
    ('DEL-2026-004', 'Swiss Gold DMCC', 4, 'Managed SD-WAN Router', 'Networking', '2026-10-10', '2026-10-24', NULL, 'Pending', 28, 'Procurement order placed with distributor.', NOW())");
}

$check_att = $mysqli->query("SELECT id FROM ci_attendance LIMIT 1");
if ($check_att && $check_att->num_rows == 0) {
    $emp_res = $mysqli->query("SELECT id FROM ci_user WHERE id IN (1, 26, 27, 28, 29, 30, 31, 32, 33, 34)");
    $dates = array('2026-10-12', '2026-10-13', '2026-10-14', '2026-10-15', '2026-10-16');
    while ($emp = $emp_res->fetch_assoc()) {
        $u_id = (int)$emp['id'];
        foreach ($dates as $d) {
            $in_time = '09:00:00';
            $out_time = '18:00:00';
            $hrs = 9.00;
            $st = 'Present';
            if ($u_id % 3 == 0 && $d == '2026-10-14') {
                $in_time = '09:35:00';
                $st = 'Late';
                $hrs = 8.42;
            } elseif ($u_id == 32 && $d == '2026-10-15') {
                $st = 'On Leave';
                $in_time = NULL;
                $out_time = NULL;
                $hrs = 0.00;
            }
            $in_val = $in_time ? "'$in_time'" : "NULL";
            $out_val = $out_time ? "'$out_time'" : "NULL";
            $mysqli->query("INSERT IGNORE INTO `ci_attendance` (`user_id`, `attendance_date`, `in_time`, `out_time`, `total_hours`, `status`, `notes`, `created_at`) VALUES ($u_id, '$d', $in_val, $out_val, $hrs, '$st', 'Standard shift', NOW())");
        }
    }
}

$check_lv = $mysqli->query("SELECT id FROM ci_leaves LIMIT 1");
if ($check_lv && $check_lv->num_rows == 0) {
    $mysqli->query("INSERT INTO `ci_leaves` (`user_id`, `leave_type`, `from_date`, `to_date`, `total_days`, `reason`, `status`, `approved_by`, `created_at`) VALUES
    (31, 'Annual Leave', '2026-10-20', '2026-10-25', 5, 'Planned annual vacation', 'Approved', 1, NOW()),
    (32, 'Sick Leave', '2026-10-15', '2026-10-15', 1, 'Medical consultation and recovery', 'Approved', 1, NOW()),
    (33, 'Emergency Leave', '2026-10-05', '2026-10-06', 2, 'Family emergency travel', 'Approved', 1, NOW()),
    (34, 'Casual Leave', '2026-10-28', '2026-10-29', 2, 'Personal paperwork and legal appointment', 'Pending', 1, NOW())");
}

$check_maint = $mysqli->query("SELECT id FROM ci_system_maintenance LIMIT 1");
if ($check_maint && $check_maint->num_rows == 0) {
    $mysqli->query("INSERT INTO `ci_system_maintenance` (`title`, `category`, `scheduled_date`, `completed_date`, `duration_mins`, `status`, `executed_by`, `notes`, `created_at`) VALUES
    ('Weekly MySQL Automated Full Backup', 'Database Backup', '2026-10-11', '2026-10-11', 25, 'Completed', 1, 'All databases verified, checksum validated successfully.', NOW()),
    ('PHP 8.2 Security Patch Update & OpenSSL Re-link', 'Security Patch', '2026-10-08', '2026-10-08', 40, 'Completed', 1, 'Security hotfixes applied, zero downtime deployment.', NOW()),
    ('InnoDB Buffer Pool & Query Cache Tuning', 'Performance Tuning', '2026-10-04', '2026-10-04', 30, 'Completed', 1, 'Buffer pool size adjusted for 16GB allocation.', NOW()),
    ('Wildcard SSL Certificate Renewal (*.rolextogroup.com)', 'SSL Certificate Renewal', '2026-10-14', '2026-10-14', 15, 'Completed', 1, 'Let\'s Encrypt certificate auto-renewed, HTTP/2 verified.', NOW()),
    ('Web Server Temp Storage & Audit Log Archival', 'Cache & Temp Cleanup', '2026-10-16', '2026-10-16', 20, 'Completed', 1, 'Archived logs older than 90 days to cold storage.', NOW())");
}

echo "Database migrations complete.\n";
