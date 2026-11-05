<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Self Training: a company library of pitch scripts, step-by-step guides, documents,
 * videos and links. Admin / HR (or Full on "Manage Self Training") upload the material;
 * users with the "Self Training" right (Roles > Core Modules) open it and mark it done.
 * Text material is stored as plain text (ci_training_modules.body) so an AI assistant
 * can be added on top of the same library later.
 */
class Ci_admin_training extends CI_Controller {

	// content_type => label, icon, colour
	public static $kinds = array(
		'pitch'    => array('Pitch Script', 'fa-bullhorn', '#7c3aed'),
		'guide'    => array('Step-by-step Guide', 'fa-list-ol', '#0891b2'),
		'document' => array('Document', 'fa-file-text-o', '#2563eb'),
		'video'    => array('Video', 'fa-play-circle', '#dc2626'),
		'link'     => array('Link', 'fa-external-link', '#059669'),
	);

	private static $file_types = array(
		'pdf' => 'application/pdf', 'doc' => 'application/msword',
		'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'ppt' => 'application/vnd.ms-powerpoint',
		'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
		'xls' => 'application/vnd.ms-excel',
		'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		'txt' => 'text/plain', 'mp4' => 'video/mp4', 'webm' => 'video/webm', 'mp3' => 'audio/mpeg',
		'png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
	);
	private static $inline_types = array('pdf', 'txt', 'mp4', 'webm', 'mp3', 'png', 'jpg', 'jpeg');
	const MAX_FILE_MB = 40;
	const UPLOAD_DIR = 'assets/uploads/training/';

	private $uid = 0;
	private $role = 0;
	private $can_manage = false;

	public function __construct()
	{
		parent::__construct();
		$this->load->library(array('Session'));
		$this->load->helper(array('form', 'url', 'text', 'date'));
		$this->load->model(array('comman_model', 'ci_admin_model', 'hr_selfservice_model'));
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Pragma: no-cache');

		$this->uid = (int)$this->session->userdata('manager_id');
		if ($this->uid === 0) {
			redirect('/manager/login');
			exit;
		}
		$this->role = (int)$this->session->userdata('manager_role');
		$is_admin = ($this->role === 1 || $this->uid === 1);
		$this->can_manage = ($is_admin || crm_can('hr_training', 'write'));

		$method = $this->router->fetch_method();
		if (in_array($method, array('index', 'view', 'done', 'file'), true)) {
			if (!$this->can_manage && !crm_can('self_training', 'read')) {
				$this->session->set_flashdata('error_message', 'You do not have access to Self Training. Ask your administrator if you need it.');
				redirect(crm_landing_url());
				exit;
			}
		} elseif (!$this->can_manage) {
			$this->session->set_flashdata('error_message', 'Only HR and administrators can manage Self Training.');
			redirect('manager/self-training');
			exit;
		}
	}

	/** A module this user may open (managers may also open archived ones). */
	private function _module($id)
	{
		if ($this->can_manage) {
			return $this->db->where('id', (int)$id)->get('ci_training_modules')->row_array();
		}
		return $this->hr_selfservice_model->module_visible_to($id, $this->role);
	}

	private function _categories()
	{
		$rows = $this->db->select('DISTINCT category', false)->where('category !=', '')->order_by('category', 'ASC')->get('ci_training_modules')->result_array();
		return array_map(function ($r) { return $r['category']; }, $rows);
	}

	/* ---------- Library (everyone with the right) ---------- */

	public function index()
	{
		$data = array();
		$data['info'] = array('title' => 'Self Training', 'page_heading' => 'Self Training');
		$data['kinds'] = self::$kinds;
		$data['modules'] = $this->hr_selfservice_model->my_training($this->uid, $this->role);
		$data['summary'] = $this->hr_selfservice_model->training_summary($this->uid, $this->role);
		$data['can_manage'] = $this->can_manage;
		$this->load->view('manager/training/index', $data);
	}

	public function view($id = 0)
	{
		$mod = $this->_module($id);
		if (empty($mod)) {
			$this->session->set_flashdata('error_message', 'This training material is not available to you.');
			redirect('manager/self-training');
			return;
		}
		$progress = $this->db->where('module_id', (int)$mod['id'])->where('user_id', $this->uid)->get('ci_training_progress')->row_array();
		if (empty($progress) && $mod['status'] === 'active') {
			// Opening it counts as started.
			$this->hr_selfservice_model->set_progress($mod['id'], $this->uid, 'In Progress');
			$progress = array('status' => 'In Progress', 'completed_at' => null);
		}
		$data = array();
		$data['info'] = array('title' => $mod['title'] . ' - Self Training', 'page_heading' => 'Self Training');
		$data['kinds'] = self::$kinds;
		$data['mod'] = $mod;
		$data['progress'] = $progress;
		$data['can_manage'] = $this->can_manage;
		$data['file_inline'] = !empty($mod['file_path']) && in_array(strtolower(pathinfo($mod['file_path'], PATHINFO_EXTENSION)), self::$inline_types, true);
		$this->load->view('manager/training/view', $data);
	}

	public function done($id = 0, $state = '')
	{
		$mod = $this->hr_selfservice_model->module_visible_to($id, $this->role);
		if ($this->input->server('REQUEST_METHOD') !== 'POST' || empty($mod)) {
			redirect('manager/self-training');
			return;
		}
		$undo = ($state === 'undo');
		$this->hr_selfservice_model->set_progress($mod['id'], $this->uid, $undo ? 'In Progress' : 'Completed');
		$this->session->set_flashdata('message', $undo ? 'Marked as not done yet.' : 'Well done! Marked as completed.');
		redirect('manager/self-training/view/' . (int)$mod['id']);
	}

	/** Uploaded files are only served through here, after the access check. */
	public function file($id = 0)
	{
		$mod = $this->_module($id);
		$path = !empty($mod['file_path']) ? FCPATH . self::UPLOAD_DIR . basename($mod['file_path']) : '';
		if ($path === '' || !is_file($path)) {
			show_404();
			return;
		}
		$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
		$type = isset(self::$file_types[$ext]) ? self::$file_types[$ext] : 'application/octet-stream';
		$name = preg_replace('/[^A-Za-z0-9 ._()-]/', '_', (string)$mod['file_name']);
		if ($name === '') $name = 'training.' . $ext;
		$inline = in_array($ext, self::$inline_types, true) && !$this->input->get('download');
		$size = filesize($path);
		$start = 0;
		$end = $size - 1;
		// Simple byte ranges so videos can be skipped forward / back.
		$range = $this->input->server('HTTP_RANGE');
		if ($range && preg_match('/bytes=(\d*)-(\d*)/', $range, $m)) {
			if ($m[1] !== '') $start = (int)$m[1];
			if ($m[2] !== '') $end = min((int)$m[2], $size - 1);
			if ($m[1] === '' && $m[2] !== '') { $start = max(0, $size - (int)$m[2]); $end = $size - 1; }
			if ($start > $end || $start >= $size) {
				header('HTTP/1.1 416 Range Not Satisfiable');
				header('Content-Range: bytes */' . $size);
				return;
			}
			header('HTTP/1.1 206 Partial Content');
			header('Content-Range: bytes ' . $start . '-' . $end . '/' . $size);
		}
		while (ob_get_level()) ob_end_clean();
		header('Content-Type: ' . $type);
		header('Accept-Ranges: bytes');
		header('Content-Length: ' . ($end - $start + 1));
		header('X-Content-Type-Options: nosniff');
		header('Content-Disposition: ' . ($inline ? 'inline' : 'attachment') . '; filename="' . $name . '"');
		$fh = fopen($path, 'rb');
		fseek($fh, $start);
		$left = $end - $start + 1;
		while ($left > 0 && !feof($fh)) {
			$chunk = fread($fh, min(8192, $left));
			echo $chunk;
			$left -= strlen($chunk);
			flush();
		}
		fclose($fh);
		exit;
	}

	/* ---------- Manage (Admin / HR) ---------- */

	public function manage()
	{
		$data = array();
		$data['info'] = array('title' => 'Manage Self Training', 'page_heading' => 'Manage Self Training');
		$data['kinds'] = self::$kinds;
		$data['modules'] = $this->hr_selfservice_model->all_training();
		$data['roles'] = $this->db->select('id, title')->where('status', 1)->order_by('title', 'ASC')->get('ci_roles')->result_array();
		$data['categories'] = $this->_categories();
		$data['max_mb'] = self::MAX_FILE_MB;
		$data['file_exts'] = array_keys(self::$file_types);
		$edit_id = (int)$this->input->get('edit');
		$data['edit'] = $edit_id > 0 ? $this->db->where('id', $edit_id)->get('ci_training_modules')->row_array() : array();
		$data['people'] = $this->hr_selfservice_model->people_progress();
		$prog_id = (int)$this->input->get('progress');
		$data['progress_mod'] = $prog_id > 0 ? $this->db->where('id', $prog_id)->get('ci_training_modules')->row_array() : array();
		$data['progress_people'] = !empty($data['progress_mod']) ? $this->hr_selfservice_model->module_people($data['progress_mod']) : array();
		$this->load->view('manager/training/manage', $data);
	}

	private function _save_upload(&$error)
	{
		if (empty($_FILES['file']) || $_FILES['file']['error'] === UPLOAD_ERR_NO_FILE) return null;
		$f = $_FILES['file'];
		if ($f['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($f['tmp_name'])) {
			$error = 'The file could not be uploaded (it may be larger than ' . self::MAX_FILE_MB . ' MB).';
			return false;
		}
		$ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
		if (!isset(self::$file_types[$ext])) {
			$error = 'This file type is not allowed. Use: ' . implode(', ', array_keys(self::$file_types)) . '.';
			return false;
		}
		if ($f['size'] > self::MAX_FILE_MB * 1024 * 1024) {
			$error = 'The file is larger than ' . self::MAX_FILE_MB . ' MB.';
			return false;
		}
		$dir = FCPATH . self::UPLOAD_DIR;
		if (!is_dir($dir)) @mkdir($dir, 0755, true);
		if (!is_file($dir . '.htaccess')) {
			// Files are served only through the controller (access check first).
			@file_put_contents($dir . '.htaccess', "<IfModule mod_authz_core.c>\n  Require all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\n  Deny from all\n</IfModule>\n");
		}
		$stored = 'tr_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
		if (!move_uploaded_file($f['tmp_name'], $dir . $stored)) {
			$error = 'The file could not be saved.';
			return false;
		}
		return array('file_path' => $stored, 'file_name' => substr(basename($f['name']), 0, 255));
	}

	private function _remove_file($stored)
	{
		$p = FCPATH . self::UPLOAD_DIR . basename((string)$stored);
		if ($stored !== '' && $stored !== null && is_file($p)) @unlink($p);
	}

	public function save()
	{
		if ($this->input->server('REQUEST_METHOD') !== 'POST') {
			redirect('manager/self-training/manage');
			return;
		}
		$id = (int)$this->input->post('id');
		$old = $id > 0 ? $this->db->where('id', $id)->get('ci_training_modules')->row_array() : array();
		if ($id > 0 && empty($old)) {
			redirect('manager/self-training/manage');
			return;
		}
		$back = 'manager/self-training/manage' . ($id ? '?edit=' . $id : '');
		$title = trim((string)$this->input->post('title'));
		$type = (string)$this->input->post('content_type');
		if ($title === '' || !isset(self::$kinds[$type])) {
			$this->session->set_flashdata('error_message', 'Please enter a title and choose the type.');
			redirect($back);
			return;
		}
		$url = trim((string)$this->input->post('content_url'));
		if ($url !== '' && !preg_match('#^https?://#i', $url)) $url = 'https://' . $url;
		$due = trim((string)$this->input->post('due_date'));
		$due = (preg_match('/^\d{4}-\d{2}-\d{2}$/', $due) && strtotime($due)) ? $due : null;
		$row = array(
			'title' => $title,
			'content_type' => $type,
			'category' => trim((string)$this->input->post('category')) ?: 'General',
			'description' => trim((string)$this->input->post('description')),
			'body' => trim((string)$this->input->post('body')),
			'content_url' => $url,
			'duration_minutes' => max(0, (int)$this->input->post('duration_minutes')),
			'is_mandatory' => $this->input->post('is_mandatory') ? 1 : 0,
			'audience_role_id' => max(0, (int)$this->input->post('audience_role_id')),
			'due_date' => $due,
			'updated_at' => date('Y-m-d H:i:s'),
		);
		$error = '';
		$up = $this->_save_upload($error);
		if ($up === false) {
			$this->session->set_flashdata('error_message', $error);
			redirect($back);
			return;
		}
		if (is_array($up)) {
			$row = array_merge($row, $up);
			if (!empty($old['file_path'])) $this->_remove_file($old['file_path']);
		} elseif ($this->input->post('remove_file') && !empty($old['file_path'])) {
			$this->_remove_file($old['file_path']);
			$row['file_path'] = null;
			$row['file_name'] = null;
		}
		$has_file = !empty($row['file_path']) || (!isset($row['file_path']) && !empty($old['file_path']));
		if ($row['body'] === '' && $url === '' && !$has_file) {
			if (is_array($up)) $this->_remove_file($up['file_path']);
			$this->session->set_flashdata('error_message', 'Add the content: write the text, give a link or upload a file.');
			redirect($back);
			return;
		}
		if ($id > 0) {
			$this->db->where('id', $id)->update('ci_training_modules', $row);
			$this->session->set_flashdata('message', 'Training material updated.');
			redirect('manager/self-training/manage');
			return;
		}
		$row['status'] = 'active';
		$row['created_by'] = $this->uid;
		$row['created_at'] = date('Y-m-d H:i:s');
		$this->db->insert('ci_training_modules', $row);
		$new_id = (int)$this->db->insert_id();
		$roles = ($row['audience_role_id'] > 0)
			? array($row['audience_role_id'])
			: array_map(function ($r) { return (int)$r['id']; }, $this->db->select('id')->get('ci_roles')->result_array());
		$this->ci_admin_model->notify_role(
			$roles,
			'training_assigned',
			'New in Self Training',
			($row['is_mandatory'] ? 'Mandatory: ' : '') . self::$kinds[$type][0] . ' - ' . $title,
			base_url('manager/self-training/view/' . $new_id),
			'fa fa-graduation-cap',
			'#1e3a8a'
		);
		$this->session->set_flashdata('message', 'Training material published.');
		redirect('manager/self-training/manage');
	}

	public function status($id = 0, $status = '')
	{
		$id = (int)$id;
		if ($this->input->server('REQUEST_METHOD') === 'POST' && $id > 0 && in_array($status, array('active', 'archived'), true)) {
			$this->db->where('id', $id)->update('ci_training_modules', array('status' => $status, 'updated_at' => date('Y-m-d H:i:s')));
			$this->session->set_flashdata('message', $status === 'archived' ? 'Archived: users no longer see it. Their progress is kept.' : 'Restored for users.');
		}
		redirect('manager/self-training/manage');
	}
}
