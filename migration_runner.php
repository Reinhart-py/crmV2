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

echo "Database migrations complete.\n";
