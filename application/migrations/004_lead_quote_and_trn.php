<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_004_lead_quote_and_trn {
    public function up($db) {
        $cols = array(
            'quote_status' => "VARCHAR(100) NULL",
            'quote_mode' => "VARCHAR(100) NULL",
            'quote_amount' => "DECIMAL(15,2) NULL",
            'industry_category' => "VARCHAR(150) NULL",
            'corporate_tax_status' => "VARCHAR(50) NULL",
            'vat_status' => "VARCHAR(50) NULL",
            'vat_trn' => "VARCHAR(100) NULL",
            'ct_trn' => "VARCHAR(100) NULL",
            'data_source' => "VARCHAR(150) NULL",
            'data_batch_referral_no' => "VARCHAR(100) NULL",
            'database_category' => "VARCHAR(100) NULL",
            'status_category' => "VARCHAR(255) NULL",
            'status_remarks' => "TEXT NULL"
        );

        foreach ($cols as $col => $def) {
            $chk = $db->query("SHOW COLUMNS FROM `ci_leads` LIKE '$col'");
            $exists = false;
            if ($chk) {
                if (is_object($chk) && method_exists($chk, 'num_rows')) {
                    $exists = ($chk->num_rows() > 0);
                } elseif (isset($chk->num_rows)) {
                    $exists = ($chk->num_rows > 0);
                }
            }
            if (!$exists) {
                $db->query("ALTER TABLE `ci_leads` ADD `$col` $def");
            }
        }

        $default_sources = array('Google Maps', 'GPS', 'Referral', 'Website', 'Social Media', 'Other');
        foreach ($default_sources as $ds) {
            $chk = $db->query("SELECT id FROM ci_terms WHERE cat_type='data_source' AND title='" . addslashes($ds) . "' LIMIT 1");
            $exists = false;
            if ($chk) {
                if (is_object($chk) && method_exists($chk, 'num_rows')) {
                    $exists = ($chk->num_rows() > 0);
                } elseif (isset($chk->num_rows)) {
                    $exists = ($chk->num_rows > 0);
                }
            }
            if (!$exists) {
                $db->query("INSERT INTO ci_terms (title, cat_type, status, parent_id) VALUES ('" . addslashes($ds) . "', 'data_source', 1, 0)");
            }
        }

        $default_db_cats = array('New Database', 'Online', 'Reference', 'Ported Out', 'Competitor DB', 'Contract Agent', 'Raw / Unverified', 'Cleansed', 'High Value', 'Re-engaged', 'Other');
        foreach ($default_db_cats as $dc) {
            $chk = $db->query("SELECT id FROM ci_terms WHERE cat_type='database_cat' AND title='" . addslashes($dc) . "' LIMIT 1");
            $exists = false;
            if ($chk) {
                if (is_object($chk) && method_exists($chk, 'num_rows')) {
                    $exists = ($chk->num_rows() > 0);
                } elseif (isset($chk->num_rows)) {
                    $exists = ($chk->num_rows > 0);
                }
            }
            if (!$exists) {
                $db->query("INSERT INTO ci_terms (title, cat_type, status, parent_id) VALUES ('" . addslashes($dc) . "', 'database_cat', 1, 0)");
            }
        }
        return true;
    }

    public function down($db) {
        return true;
    }
}
