<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_008_pipeline_sales_ref_no {
    private function column_exists($db, $table, $column) {
        $res = $db->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
        if (!$res) {
            return false;
        }
        if (is_object($res) && method_exists($res, 'num_rows')) {
            return $res->num_rows() > 0;
        }
        return isset($res->num_rows) && $res->num_rows > 0;
    }

    public function up($db) {
        if (!$this->column_exists($db, 'ci_pipeline', 'sales_ref_no')) {
            $ok = $db->query("ALTER TABLE `ci_pipeline` ADD `sales_ref_no` VARCHAR(100) NULL DEFAULT NULL AFTER `lead_ref_no`");
            if (!$ok) {
                return false;
            }
        }

        $db->query("UPDATE `ci_pipeline` P
            INNER JOIN `ci_orders` O ON O.lead_id = P.lead_id
            SET P.sales_ref_no = O.sales_ref_no
            WHERE O.sales_ref_no IS NOT NULL AND O.sales_ref_no != ''
              AND (P.sales_ref_no IS NULL OR P.sales_ref_no = '')");

        $db->query("UPDATE `ci_pipeline` P
            INNER JOIN `ci_orders` O ON O.lead_id = P.lead_id
            SET P.order_request_status = 'approved'
            WHERE O.status = 'Sales' AND O.approval_status = 'approved'
              AND O.sales_ref_no IS NOT NULL AND O.sales_ref_no != ''
              AND (P.order_request_status IS NULL OR P.order_request_status NOT IN ('approved', 'ordered', 'processed'))");

        return true;
    }

    public function down($db) {
        if ($this->column_exists($db, 'ci_pipeline', 'sales_ref_no')) {
            $db->query("ALTER TABLE `ci_pipeline` DROP COLUMN `sales_ref_no`");
        }
        return true;
    }
}
