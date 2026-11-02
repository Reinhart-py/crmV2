<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_001_core_crm_schema {
    public function up($db) {
        $db->query("CREATE TABLE IF NOT EXISTS `ci_tasks` (
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
        ) ENGINE=InnoDB ROW_FORMAT=DYNAMIC DEFAULT CHARSET=utf8;");

        $db->query("CREATE TABLE IF NOT EXISTS `ci_admin_logs` (
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
            `latitude` DECIMAL(10,8) NULL,
            `longitude` DECIMAL(11,8) NULL,
            `accuracy` DECIMAL(10,2) NULL,
            `location_status` VARCHAR(50) NULL,
            `created_at` DATETIME NOT NULL,
            PRIMARY KEY (`id`),
            KEY `idx_user_id` (`user_id`),
            KEY `idx_created_at` (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $admin_log_cols = array(
            'latitude' => "DECIMAL(10,8) NULL",
            'longitude' => "DECIMAL(11,8) NULL",
            'accuracy' => "DECIMAL(10,2) NULL",
            'location_status' => "VARCHAR(50) NULL",
            'logout_time' => "DATETIME NULL"
        );
        foreach ($admin_log_cols as $col => $def) {
            $chk = $db->query("SHOW COLUMNS FROM `ci_admin_logs` LIKE '$col'");
            $exists = false;
            if ($chk) {
                if (is_object($chk) && method_exists($chk, 'num_rows')) {
                    $exists = ($chk->num_rows() > 0);
                } elseif (isset($chk->num_rows)) {
                    $exists = ($chk->num_rows > 0);
                }
            }
            if (!$exists) {
                $db->query("ALTER TABLE `ci_admin_logs` ADD `$col` $def");
            }
        }

        $cols = array(
            'customer' => "VARCHAR(255) NULL",
            'poc' => "VARCHAR(255) NULL",
            'contact1' => "VARCHAR(255) NULL",
            'alt_mobile' => "VARCHAR(100) NULL",
            'alt_contact' => "VARCHAR(100) NULL",
            'email' => "VARCHAR(255) NULL",
            'a_number' => "VARCHAR(255) NULL",
            'a_department' => "VARCHAR(255) NULL",
            'r_amount' => "VARCHAR(255) NULL",
            'r_date' => "VARCHAR(255) NULL",
            'business_category' => "VARCHAR(150) NULL",
            's_cat' => "VARCHAR(255) NULL",
            'p_cat' => "VARCHAR(255) NULL",
            'p_details' => "TEXT NULL",
            'tl_number' => "VARCHAR(255) NULL",
            'tl_authority' => "VARCHAR(255) NULL",
            'tl_issue_date' => "VARCHAR(255) NULL",
            'tl_expire_date' => "VARCHAR(255) NULL",
            'qty' => "VARCHAR(255) NULL",
            'mrc' => "VARCHAR(255) NULL",
            'revenue' => "VARCHAR(255) NULL",
            'mrr' => "VARCHAR(255) NULL",
            'location' => "VARCHAR(255) NULL",
            'dnd_status' => "VARCHAR(10) NULL DEFAULT 'No'",
            'dnd_check_date' => "VARCHAR(30) NULL",
            'close_date' => "VARCHAR(50) NULL",
            'remark' => "TEXT NULL",
            'address' => "TEXT NULL",
            'country' => "INT(11) NULL DEFAULT 0",
            'state' => "INT(11) NULL DEFAULT 0",
            'city' => "VARCHAR(255) NULL",
            'poc2' => "VARCHAR(255) NULL",
            'contact2' => "VARCHAR(255) NULL",
            'c_partner' => "VARCHAR(255) NULL",
            'otc' => "VARCHAR(255) NULL",
            'lead_cat' => "VARCHAR(255) NULL",
            'assigned_to' => "INT(11) NULL DEFAULT 0",
            'lead_source' => "VARCHAR(255) NULL",
            'followup_date' => "VARCHAR(50) NULL",
            'followup_time' => "VARCHAR(50) NULL",
            'm_date' => "VARCHAR(50) NULL",
            'm_time' => "VARCHAR(50) NULL"
        );

        foreach ($cols as $col => $def) {
            $chk = $db->query("SHOW COLUMNS FROM `ci_tickets` LIKE '$col'");
            $exists = false;
            if ($chk) {
                if (is_object($chk) && method_exists($chk, 'num_rows')) {
                    $exists = ($chk->num_rows() > 0);
                } elseif (isset($chk->num_rows)) {
                    $exists = ($chk->num_rows > 0);
                }
            }
            if (!$exists) {
                $db->query("ALTER TABLE `ci_tickets` ADD `$col` $def");
            }
        }

        $business_cats = array(
            'Trading', 'Retail', 'Corporate Services', 'Information Technology',
            'Real Estate', 'Hospitality & Tourism', 'Healthcare', 'Manufacturing',
            'Finance & Accounting', 'Logistics & Transport', 'Other'
        );
        foreach ($business_cats as $bc) {
            $chk = $db->query("SELECT id FROM ci_terms WHERE cat_type='business_cat' AND title='" . addslashes($bc) . "' LIMIT 1");
            $exists = false;
            if ($chk) {
                if (is_object($chk) && method_exists($chk, 'num_rows')) {
                    $exists = ($chk->num_rows() > 0);
                } elseif (isset($chk->num_rows)) {
                    $exists = ($chk->num_rows > 0);
                }
            }
            if (!$exists) {
                $now = date('Y-m-d H:i:s');
                $db->query("INSERT INTO ci_terms (parent_id, cat_type, title, created, status, delete_status) VALUES (0, 'business_cat', '" . addslashes($bc) . "', '$now', 1, 0)");
            }
        }
        return true;
    }

    public function down($db) {
        $db->query("DROP TABLE IF EXISTS `ci_tasks`");
        $db->query("DROP TABLE IF EXISTS `ci_admin_logs`");
        return true;
    }
}
