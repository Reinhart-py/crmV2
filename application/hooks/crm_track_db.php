<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Record tracking (System > Activity > Record Tracking).
 * Runs before the database is loaded and supplies the query builder class (CI_DB) with
 * update / insert / delete wrapped, so every change to a tracked table is stored in
 * ci_record_changes: which record, which field, old and new value, who, from which page, when.
 * It works for every page that saves through the query builder, so no page needs its own code.
 * The rows are written once the page has finished (so insert ids seen by the page never change).
 */
function crm_track_db()
{
	if (class_exists('CI_DB', false)) return;
	require_once(BASEPATH . 'database/DB_driver.php');
	require_once(BASEPATH . 'database/DB_active_rec.php');
	require_once(APPPATH . 'hooks/crm_track_db_class.php');
}
