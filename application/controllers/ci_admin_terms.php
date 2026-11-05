<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * System > Lists: the choices offered in forms (table ci_terms, one cat_type per list).
 *
 * Opening needs View on "Lists"; adding, renaming, disabling needs Full.
 * Entries are never deleted: Disable hides them from new choices while records that already
 * use them keep their value. Renaming an entry of a list stored by name (Business Category,
 * Database Category, Data Source) also renames it on the records that use it.
 */
class Ci_admin_terms extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->library(array('Session'));
		$this->load->helper(array('form', 'url', 'text'));
		$this->load->model(array('ci_admin_model', 'comman_model'));
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");

		if ((int)$this->session->userdata('manager_id') <= 0) {
			redirect('manager/login');
			exit;
		}
		if (!crm_can('terms')) {
			$this->session->set_flashdata('error_message', 'You do not have access to Lists.');
			redirect(crm_landing_url());
			exit;
		}
	}

	public function if_not_login($url = '')
	{
		return array();
	}

	/**
	 * The lists. 'tree' = entries can have sub-entries; 'by' = how records store the choice
	 * (id or name); 'used' = where it is used.
	 */
	public static function lists()
	{
		return array(
			'v_cat' => array('label' => 'Industry', 'desc' => 'Industry and sub-industry of a vertical', 'used' => 'Verticals', 'tree' => true, 'by' => 'id'),
			'v_status' => array('label' => 'Client Status', 'desc' => 'Status of a vertical (Active, Inactive...)', 'used' => 'Verticals', 'tree' => false, 'by' => 'id'),
			'p_cat' => array('label' => 'Products', 'desc' => 'Product and service picked on a lead, order or KAM client', 'used' => 'Leads, Orders, KAM', 'tree' => true, 'by' => 'id'),
			'lead_cat' => array('label' => 'Lead Temperature', 'desc' => 'How warm a lead is (Cold, Warm, Hot...)', 'used' => 'Leads', 'tree' => false, 'by' => 'id'),
			'business_cat' => array('label' => 'Business Category', 'desc' => 'Type of business of a client', 'used' => 'Leads, Orders, KAM', 'tree' => false, 'by' => 'name'),
			'database_cat' => array('label' => 'Database Category', 'desc' => 'Where a database record comes from or its quality', 'used' => 'Database, Calls', 'tree' => false, 'by' => 'name'),
			'data_source' => array('label' => 'Data Source', 'desc' => 'Source of a database record', 'used' => 'Database', 'tree' => false, 'by' => 'name'),
		);
	}

	private function can_write()
	{
		return crm_can('terms', 'write');
	}

	private function back($type = '', $msg = '', $error = false)
	{
		if ($msg !== '') $this->session->set_flashdata($error ? 'error_message' : 'message', $msg);
		redirect('manager/terms' . ($type !== '' ? '?list=' . rawurlencode($type) : ''));
		exit;
	}

	/** Typos and stray tabs in names stored by id (safe to fix: records keep the id). */
	private function _tidy()
	{
		$this->db->query("UPDATE ci_terms SET title = 'Database' WHERE cat_type = 'lead_cat' AND title = 'Datatbase'");
		$this->db->query("UPDATE ci_terms SET title = 'Doctors' WHERE cat_type = 'v_cat' AND title = 'Doctros'");
		$this->db->query("UPDATE ci_terms SET title = TRIM(BOTH ' ' FROM TRIM(BOTH '\t' FROM title))
			WHERE cat_type IN ('v_cat', 'v_status', 'p_cat', 'lead_cat') AND (title LIKE '%\t' OR title LIKE '\t%' OR title LIKE '% ' OR title LIKE ' %')");
	}

	/** Number of records using each entry: id => count. */
	private function _usage($type)
	{
		$q = array();
		switch ($type) {
			case 'v_cat':
				$q[] = "SELECT category AS k, COUNT(*) n FROM ci_verticals GROUP BY category";
				$q[] = "SELECT sub_category AS k, COUNT(*) n FROM ci_verticals GROUP BY sub_category";
				break;
			case 'v_status':
				$q[] = "SELECT status AS k, COUNT(*) n FROM ci_verticals GROUP BY status";
				break;
			case 'p_cat':
				$q[] = "SELECT s_cat AS k, COUNT(*) n FROM ci_leads GROUP BY s_cat";
				$q[] = "SELECT p_cat AS k, COUNT(*) n FROM ci_leads GROUP BY p_cat";
				break;
			case 'lead_cat':
				$q[] = "SELECT lead_cat AS k, COUNT(*) n FROM ci_leads GROUP BY lead_cat";
				break;
			case 'business_cat':
				$q[] = "SELECT business_category AS k, COUNT(*) n FROM ci_leads GROUP BY business_category";
				$q[] = "SELECT industry_category AS k, COUNT(*) n FROM ci_leads WHERE industry_category <> IFNULL(business_category, '') GROUP BY industry_category";
				break;
			case 'database_cat':
				$q[] = "SELECT database_category AS k, COUNT(*) n FROM ci_leads GROUP BY database_category";
				break;
			case 'data_source':
				$q[] = "SELECT data_source AS k, COUNT(*) n FROM ci_leads GROUP BY data_source";
				break;
		}
		$by_key = array();
		foreach ($q as $sql) {
			$res = $this->db->query($sql);
			if (!$res) continue;
			foreach ($res->result_array() as $r) {
				$k = strtolower(trim((string)$r['k']));
				if ($k === '') continue;
				$by_key[$k] = (isset($by_key[$k]) ? $by_key[$k] : 0) + (int)$r['n'];
			}
		}
		$lists = self::lists();
		$out = array();
		foreach ($this->db->select('id, title')->where('cat_type', $type)->get('ci_terms')->result_array() as $t) {
			$k = $lists[$type]['by'] === 'id' ? (string)(int)$t['id'] : strtolower(trim($t['title']));
			$out[(int)$t['id']] = isset($by_key[$k]) ? $by_key[$k] : 0;
		}
		return $out;
	}

	public function index()
	{
		$this->_tidy();
		$lists = self::lists();
		$type = (string)$this->input->get('list');
		if (!isset($lists[$type])) $type = 'v_cat';
		$data = array();
		$data['info'] = array('title' => 'Lists', 'page_heading' => 'Lists');
		$data['lists'] = $lists;
		$data['type'] = $type;
		$counts = array();
		foreach ($this->db->query("SELECT cat_type, COUNT(*) n, SUM(status = 1) a FROM ci_terms GROUP BY cat_type")->result_array() as $r) {
			$counts[$r['cat_type']] = array('all' => (int)$r['n'], 'active' => (int)$r['a']);
		}
		$data['counts'] = $counts;
		// parents first, each followed by its sub-entries
		$rows = $this->db->where('cat_type', $type)->order_by('title', 'ASC')->get('ci_terms')->result_array();
		$kids = array(); $tops = array();
		foreach ($rows as $r) {
			if ((int)$r['parent_id'] > 0) $kids[(int)$r['parent_id']][] = $r; else $tops[] = $r;
		}
		$ordered = array();
		$seen = array();
		foreach ($tops as $t) {
			$t['depth'] = 0; $ordered[] = $t; $seen[(int)$t['id']] = 1;
			if (!empty($kids[(int)$t['id']])) foreach ($kids[(int)$t['id']] as $c) { $c['depth'] = 1; $c['parent_title'] = $t['title']; $ordered[] = $c; $seen[(int)$c['id']] = 1; }
		}
		foreach ($rows as $r) { if (empty($seen[(int)$r['id']])) { $r['depth'] = 1; $ordered[] = $r; } } // sub-entries of a missing parent
		$data['rows'] = $ordered;
		$data['parents'] = $tops;
		$data['usage'] = $this->_usage($type);
		$data['can_write'] = $this->can_write();
		$this->load->view('manager/terms/index', $data);
	}

	/** Older links: the add / edit forms now open on the Lists page. */
	public function add()
	{
		redirect('manager/terms?add=1');
	}

	public function edit($did = '')
	{
		$id = (int)(ctype_digit((string)$did) ? $did : base64_decode((string)$did));
		$t = $id ? $this->db->select('id, cat_type')->where('id', $id)->get('ci_terms')->row_array() : array();
		if (empty($t)) $this->back();
		redirect('manager/terms?list=' . rawurlencode($t['cat_type']) . '&edit=' . (int)$t['id']);
	}

	/** Add or rename an entry (POST). */
	public function save()
	{
		if ($this->input->server('REQUEST_METHOD') !== 'POST') $this->back();
		$lists = self::lists();
		$type = (string)$this->input->post('cat_type');
		if (!isset($lists[$type])) $this->back('', 'Choose a list.', true);
		if (!$this->can_write()) $this->back($type, 'Changing lists needs Full on Lists.', true);
		$id = (int)$this->input->post('id');
		$title = trim(preg_replace('/\s+/', ' ', strip_tags((string)$this->input->post('title'))));
		if ($title === '' || mb_strlen($title) > 120) $this->back($type, 'Name is required (up to 120 characters).', true);
		$parent = $lists[$type]['tree'] ? (int)$this->input->post('parent_id') : 0;
		if ($parent > 0) {
			$p = $this->db->where(array('id' => $parent, 'cat_type' => $type, 'parent_id' => 0))->get('ci_terms')->row_array();
			if (empty($p) || $parent === $id) $parent = 0;
		}
		$old = array();
		if ($id > 0) {
			$old = $this->db->where(array('id' => $id, 'cat_type' => $type))->get('ci_terms')->row_array();
			if (empty($old)) $this->back($type, 'Entry not found.', true);
			// an entry with sub-entries stays at the top
			if ($parent > 0 && $this->db->where('parent_id', $id)->count_all_results('ci_terms') > 0) $parent = 0;
		}
		$dup = $this->db->query("SELECT id FROM ci_terms WHERE cat_type = ? AND parent_id = ? AND LOWER(TRIM(title)) = LOWER(?) AND id <> ? LIMIT 1", array($type, $parent, $title, $id))->row_array();
		if (!empty($dup)) $this->back($type, '"' . $title . '" is already in this list.', true);

		if ($id > 0) {
			$this->db->where('id', $id)->update('ci_terms', array('title' => $title, 'parent_id' => $parent));
			$moved = 0;
			if ($lists[$type]['by'] === 'name' && trim($old['title']) !== $title) {
				$cols = array('business_cat' => array('business_category', 'industry_category'), 'database_cat' => array('database_category'), 'data_source' => array('data_source'));
				foreach ($cols[$type] as $c) {
					$this->db->query("UPDATE ci_leads SET `$c` = ? WHERE TRIM(`$c`) = ?", array($title, trim($old['title'])));
					$moved += $this->db->affected_rows();
				}
			}
			$this->ci_admin_model->log_admin_action('term_edit', 'Lists: renamed "' . $old['title'] . '" to "' . $title . '" in ' . $lists[$type]['label']);
			$this->back($type, '"' . $title . '" saved.' . ($moved ? ' Updated on ' . $moved . ' record' . ($moved === 1 ? '' : 's') . ' that used the old name.' : ''));
		}
		$this->db->insert('ci_terms', array('title' => $title, 'parent_id' => $parent, 'cat_type' => $type, 'status' => 1, 'created' => date('Y-m-d H:i:s')));
		$this->ci_admin_model->log_admin_action('term_add', 'Lists: added "' . $title . '" to ' . $lists[$type]['label']);
		$this->back($type, '"' . $title . '" added to ' . $lists[$type]['label'] . '.');
	}

	/** Disable / enable an entry (POST). Disabled entries are not offered for new records. */
	public function toggle($id = 0)
	{
		$id = (int)$id;
		$t = $this->db->where('id', $id)->get('ci_terms')->row_array();
		$type = !empty($t) ? $t['cat_type'] : '';
		if ($this->input->server('REQUEST_METHOD') !== 'POST' || empty($t)) $this->back($type);
		if (!$this->can_write()) $this->back($type, 'Changing lists needs Full on Lists.', true);
		$to = (int)$t['status'] === 1 ? 0 : 1;
		$this->db->where('id', $id)->update('ci_terms', array('status' => $to));
		$this->ci_admin_model->log_admin_action('term_status', 'Lists: ' . ($to ? 'enabled' : 'disabled') . ' "' . $t['title'] . '" (#' . $id . ')');
		$this->back($type, '"' . $t['title'] . '" ' . ($to ? 'enabled again.' : 'disabled. Records that already use it keep it.'));
	}

	/** Entries are no longer deleted (that removed products too); this now disables. */
	public function delete($id = 0)
	{
		$id = (int)(ctype_digit((string)$id) ? $id : base64_decode((string)$id));
		$t = $this->db->where('id', $id)->get('ci_terms')->row_array();
		$this->back(!empty($t) ? $t['cat_type'] : '', 'Entries are not deleted any more. Use Disable: it hides the entry from new choices and keeps it on old records.', true);
	}

	public function getSubCatAjax()
	{
		$cid = (int)$this->input->post('c_id');
		if ($cid <= 0) { echo 'error'; return; }
		$list = $this->db->where(array('parent_id' => $cid, 'status' => 1))->get('ci_terms')->result_array();
		if (empty($list)) { echo '<option value="">Not Available</option>'; return; }
		$str = '<option value="">Choose Sub Category</option>';
		foreach ($list as $l) $str .= '<option value="' . (int)$l['id'] . '">' . htmlspecialchars($l['title'], ENT_QUOTES, 'UTF-8') . '</option>';
		echo $str;
	}
}
