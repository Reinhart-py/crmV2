<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_006_orders_and_sales_approval {
    public function up($db) {
        $cols_orders = array(
            'approval_status' => "VARCHAR(50) NOT NULL DEFAULT 'Pending'",
            'approved_by' => "INT(11) NULL DEFAULT NULL",
            'approved_at' => "DATETIME NULL DEFAULT NULL",
            'approval_remarks' => "TEXT NULL DEFAULT NULL",
            'approval_history' => "LONGTEXT NULL DEFAULT NULL",
            'sales_converted_at' => "DATETIME NULL DEFAULT NULL",
            'sales_converted_by' => "INT(11) NULL DEFAULT NULL"
        );

        foreach ($cols_orders as $col => $def) {
            $chk = $db->query("SHOW COLUMNS FROM `ci_orders` LIKE '$col'");
            $exists = false;
            if ($chk) {
                if (is_object($chk) && method_exists($chk, 'num_rows')) {
                    $exists = ($chk->num_rows() > 0);
                } elseif (isset($chk->num_rows)) {
                    $exists = ($chk->num_rows > 0);
                }
            }
            if (!$exists) {
                $db->query("ALTER TABLE `ci_orders` ADD `$col` $def");
            }
        }

        $cols_pipeline = array(
            'order_request_status' => "VARCHAR(50) DEFAULT 'none'",
            'order_requested_by' => "INT(11) DEFAULT NULL",
            'order_requested_at' => "DATETIME DEFAULT NULL",
            'order_request_remarks' => "TEXT DEFAULT NULL",
            'is_locked' => "TINYINT(1) DEFAULT 0",
            'locked_reason' => "VARCHAR(255) DEFAULT NULL",
            'locked_at' => "DATETIME DEFAULT NULL"
        );

        foreach ($cols_pipeline as $col => $def) {
            $chk = $db->query("SHOW COLUMNS FROM `ci_pipeline` LIKE '$col'");
            $exists = false;
            if ($chk) {
                if (is_object($chk) && method_exists($chk, 'num_rows')) {
                    $exists = ($chk->num_rows() > 0);
                } elseif (isset($chk->num_rows)) {
                    $exists = ($chk->num_rows > 0);
                }
            }
            if (!$exists) {
                $db->query("ALTER TABLE `ci_pipeline` ADD `$col` $def");
            }
        }
        return true;
    }

    public function down($db) {
        return true;
    }
}
