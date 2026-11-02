<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_003_user_security_and_currency {
    public function up($db) {
        $cols = array(
            'current_session_token' => "VARCHAR(64) NULL",
            'last_ip' => "VARCHAR(100) NULL",
            'last_active_at' => "DATETIME NULL",
            'is_online' => "TINYINT(1) NULL DEFAULT 0",
            'currency' => "VARCHAR(10) NULL DEFAULT 'AED'"
        );

        foreach ($cols as $col => $def) {
            $chk = $db->query("SHOW COLUMNS FROM `ci_user` LIKE '$col'");
            $exists = false;
            if ($chk) {
                if (is_object($chk) && method_exists($chk, 'num_rows')) {
                    $exists = ($chk->num_rows() > 0);
                } elseif (isset($chk->num_rows)) {
                    $exists = ($chk->num_rows > 0);
                }
            }
            if (!$exists) {
                $db->query("ALTER TABLE `ci_user` ADD `$col` $def");
            }
        }

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
            $chk = $db->query("SELECT id FROM ci_roles WHERE id=" . (int)$r_id . " LIMIT 1");
            $exists = false;
            if ($chk) {
                if (is_object($chk) && method_exists($chk, 'num_rows')) {
                    $exists = ($chk->num_rows() > 0);
                } elseif (isset($chk->num_rows)) {
                    $exists = ($chk->num_rows > 0);
                }
            }
            if (!$exists) {
                $db->query("INSERT INTO ci_roles (id, title, status, created) VALUES (" . (int)$r_id . ", '" . addslashes($r_title) . "', 1, NOW())");
            }
        }
        return true;
    }

    public function down($db) {
        return true;
    }
}
