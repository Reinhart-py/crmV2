<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_005_finance_and_operations {
    public function up($db) {
        $db->query("CREATE TABLE IF NOT EXISTS `ci_invoices` (
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

        $db->query("CREATE TABLE IF NOT EXISTS `ci_payments` (
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

        $db->query("CREATE TABLE IF NOT EXISTS `ci_expenses` (
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

        $db->query("CREATE TABLE IF NOT EXISTS `ci_service_delivery` (
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

        $db->query("CREATE TABLE IF NOT EXISTS `ci_attendance` (
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

        $db->query("CREATE TABLE IF NOT EXISTS `ci_leaves` (
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

        $db->query("CREATE TABLE IF NOT EXISTS `ci_system_maintenance` (
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

        return true;
    }

    public function down($db) {
        $db->query("DROP TABLE IF EXISTS `ci_invoices`");
        $db->query("DROP TABLE IF EXISTS `ci_payments`");
        $db->query("DROP TABLE IF EXISTS `ci_expenses`");
        $db->query("DROP TABLE IF EXISTS `ci_service_delivery`");
        $db->query("DROP TABLE IF EXISTS `ci_attendance`");
        $db->query("DROP TABLE IF EXISTS `ci_leaves`");
        $db->query("DROP TABLE IF EXISTS `ci_system_maintenance`");
        return true;
    }
}
