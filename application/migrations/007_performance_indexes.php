<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_007_performance_indexes {
    private function add_index_if_missing($db, $table, $index_name, $column_def) {
        $chk = $db->query("SHOW INDEX FROM `$table` WHERE Key_name = '$index_name'");
        $exists = false;
        if ($chk) {
            if (is_object($chk) && method_exists($chk, 'num_rows')) {
                $exists = ($chk->num_rows() > 0);
            } elseif (isset($chk->num_rows)) {
                $exists = ($chk->num_rows > 0);
            }
        }
        if (!$exists) {
            $db->query("ALTER TABLE `$table` ADD INDEX `$index_name` ($column_def)");
        }
    }

    public function up($db) {
        $this->add_index_if_missing($db, 'ci_leads', 'idx_leads_assign', '`lead_assign`');
        $this->add_index_if_missing($db, 'ci_leads', 'idx_leads_created_by', '`created_by`');
        $this->add_index_if_missing($db, 'ci_leads', 'idx_leads_status', '`status`');
        $this->add_index_if_missing($db, 'ci_leads', 'idx_leads_vertical', '`vertical_id`');
        $this->add_index_if_missing($db, 'ci_leads', 'idx_leads_customer', '`customer`(100)');

        $this->add_index_if_missing($db, 'ci_user', 'idx_user_manager', '`manager`');
        $this->add_index_if_missing($db, 'ci_user', 'idx_user_c_username', '`c_username`');
        $this->add_index_if_missing($db, 'ci_user', 'idx_user_status', '`status`');
        $this->add_index_if_missing($db, 'ci_user', 'idx_user_type', '`user_type`');
        $this->add_index_if_missing($db, 'ci_user', 'idx_user_session_token', '`current_session_token`');

        $this->add_index_if_missing($db, 'ci_pipeline', 'idx_pipeline_stage', '`pipeline_stage`');
        $this->add_index_if_missing($db, 'ci_pipeline', 'idx_pipeline_order_status', '`order_request_status`');
        $this->add_index_if_missing($db, 'ci_pipeline', 'idx_pipeline_locked', '`is_locked`');

        $this->add_index_if_missing($db, 'ci_orders', 'idx_orders_created_by', '`created_by`');
        $this->add_index_if_missing($db, 'ci_orders', 'idx_orders_status', '`status`');
        $this->add_index_if_missing($db, 'ci_orders', 'idx_orders_approval_status', '`approval_status`');
        $this->add_index_if_missing($db, 'ci_orders', 'idx_orders_assigned_to', '`assigned_to`');

        $this->add_index_if_missing($db, 'ci_tasks', 'idx_tasks_assigned_to', '`assigned_to`');
        $this->add_index_if_missing($db, 'ci_tasks', 'idx_tasks_status', '`status`');
        $this->add_index_if_missing($db, 'ci_tasks', 'idx_tasks_created_by', '`created_by`');

        $this->add_index_if_missing($db, 'ci_tickets', 'idx_tickets_assigned_to', '`assigned_to`');
        $this->add_index_if_missing($db, 'ci_tickets', 'idx_tickets_status', '`status`');
        $this->add_index_if_missing($db, 'ci_tickets', 'idx_tickets_raised_by', '`raised_by`');

        $this->add_index_if_missing($db, 'ci_terms', 'idx_terms_cat_type', '`cat_type`');
        $this->add_index_if_missing($db, 'ci_terms', 'idx_terms_parent_id', '`parent_id`');

        $this->add_index_if_missing($db, 'ci_admin_logs', 'idx_logs_activity', '`activity`');
        $this->add_index_if_missing($db, 'ci_admin_logs', 'idx_logs_status', '`status`');

        return true;
    }

    public function down($db) {
        return true;
    }
}
