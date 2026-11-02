<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_002_ticket_history_and_followups {
    public function up($db) {
        $db->query("CREATE TABLE IF NOT EXISTS `ci_ticket_history` (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $db->query("CREATE TABLE IF NOT EXISTS `ci_ticket_followups` (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $cols = array(
            'client_type' => "VARCHAR(50) NULL DEFAULT 'Onboarded'",
            'company_name' => "VARCHAR(255) NULL",
            'company_id' => "INT(11) NULL DEFAULT 0",
            'ticket_type' => "VARCHAR(100) NULL",
            'department' => "VARCHAR(100) NULL",
            'ticket_title' => "VARCHAR(255) NULL",
            'assigned_users' => "TEXT NULL",
            'tagged_users' => "TEXT NULL",
            'raised_by' => "INT(11) NULL DEFAULT 0",
            'created_date' => "DATETIME NULL",
            'deadline_date' => "DATE NULL",
            'expected_close_date' => "DATE NULL",
            'last_updated_date' => "DATETIME NULL",
            'last_updated_by' => "INT(11) NULL DEFAULT 0",
            'internal_notes' => "TEXT NULL",
            'resolution_remarks' => "TEXT NULL",
            'resolution_reviewed_by' => "INT(11) NULL DEFAULT 0",
            'resolution_confirmed_at' => "DATETIME NULL",
            'attachment' => "VARCHAR(255) NULL"
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
        return true;
    }

    public function down($db) {
        $db->query("DROP TABLE IF EXISTS `ci_ticket_history`");
        $db->query("DROP TABLE IF EXISTS `ci_ticket_followups`");
        return true;
    }
}
