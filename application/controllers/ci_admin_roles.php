<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * System > Roles & Permissions.
 *
 * Role-wise: every role has a permission map (catalogue in
 * application/helpers/crm_permission_helper.php). Users get their role's map.
 * User-wise: a user can follow the role, have a custom map, or be a full admin.
 * Chatbot rights are user-wise only (see chatbot_rights()).
 *
 * Opening this page needs View on "Roles & Permissions"; saving needs Full.
 * Only full admins can edit the Admin role, give "Full admin", or change an admin.
 *
 * Role types (crm_role_types()): the Admin role has everything; every other role is a Department
 * (Sales, Back Office, KAM, ...) directly under Admin, or a System login (TV screen). There is no
 * "Reports to" between roles: who reports to whom is set per person (Manager on the staff profile).
 */
class Ci_admin_roles extends CI_Controller {

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
		if (!crm_can('roles')) {
			$this->session->set_flashdata('error_message', 'You do not have access to Roles & Permissions.');
			redirect(crm_landing_url());
			exit;
		}
	}

	public function if_not_login($url = '')
	{
		return array();
	}

	public function is_user_login($url = '')
	{
		if ($this->session->userdata('manager_id') != '') {
			redirect($url != '' ? $url : 'manager/dashboard');
		}
	}

	private function can_write()
	{
		return crm_can('roles', 'write');
	}

	private function deny($msg, $to = 'manager/roles')
	{
		$this->session->set_flashdata('error_message', $msg);
		redirect($to);
		exit;
	}

	private function json($payload)
	{
		header('Content-Type: application/json');
		echo json_encode($payload);
		exit;
	}

	/** Roles with user counts, parent name and access summary. */
	private function roles_overview()
	{
		$roles = $this->db->order_by('id', 'ASC')->get_where('ci_roles', array('status' => 1))->result_array();
		$counts = array();
		foreach ($this->db->query("SELECT user_type, COUNT(*) n FROM ci_user WHERE status = 1 GROUP BY user_type")->result_array() as $r) {
			$counts[(int)$r['user_type']] = (int)$r['n'];
		}
		$names = array();
		foreach ($roles as $r) $names[(int)$r['id']] = $r['title'];
		$total = count(crm_perm_items());
		foreach ($roles as &$r) {
			$map = json_decode((string)$r['permissions'], true);
			$exp = ((int)$r['id'] === 1) ? array_fill_keys(array_keys(crm_perm_items()), 'write') : crm_perm_expand(is_array($map) ? $map : array());
			$w = 0; $rd = 0;
			foreach ($exp as $v) { if ($v === 'write') $w++; elseif ($v === 'read') $rd++; }
			$r['expanded'] = $exp;
			$r['n_write'] = $w;
			$r['n_read'] = $rd;
			$r['n_total'] = $total;
			$r['users'] = isset($counts[(int)$r['id']]) ? $counts[(int)$r['id']] : 0;
			$r['parent_title'] = isset($names[(int)$r['parent_id']]) ? $names[(int)$r['parent_id']] : '';
			$r['role_type'] = ((int)$r['id'] === 1) ? 'admin' : (isset($r['role_type']) && $r['role_type'] === 'system' ? 'system' : 'department');
		}
		unset($r);
		return $roles;
	}

	public function index()
	{
		$chk_pres = $this->db->get_where('ci_roles', array('title' => 'Presentation'))->row_array();
		if (empty($chk_pres)) {
			$this->db->insert('ci_roles', array(
				'title' => 'Presentation', 'parent_id' => 0, 'level' => 5, 'status' => 1,
				'permissions' => json_encode(array('live' => 'read')),
				'created' => date('Y-m-d')
			));
		}
		chatbot_ensure_column();
		$data = array();
		$data['info'] = array('title' => 'Roles & Permissions', 'page_heading' => 'Roles & Permissions');
		$data['roles'] = $this->roles_overview();
		$data['users'] = $this->db->query("SELECT u.id, u.name, u.email, u.user_type, r.title AS role_title, up.permission_mode, up.chatbot_permissions
			FROM ci_user u LEFT JOIN ci_roles r ON r.id = u.user_type LEFT JOIN ci_user_permissions up ON up.user_id = u.id
			WHERE u.status = 1 ORDER BY u.name ASC")->result_array();
		$data['can_write'] = $this->can_write();
		$data['is_full_admin'] = crm_is_full_admin();
		$data['role_types'] = crm_role_types();
		$this->load->view('manager/roles/index', $data);
	}

	public function add()
	{
		if (!$this->can_write()) $this->deny('You can view roles but not add them (needs Full on Roles & Permissions).');
		$data = array();
		$data['info'] = array('title' => 'Add Role', 'page_heading' => 'Add Role');
		$data['roles'] = $this->roles_overview();

		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$title = trim((string)$this->input->post('title', true));
			$type = $this->input->post('role_type') === 'system' ? 'system' : 'department';
			if ($title === '' || mb_strlen($title) > 60) $this->deny('Role name is required (up to 60 characters).', 'manager/roles/add');
			if ($this->db->where('title', $title)->where('status', 1)->count_all_results('ci_roles') > 0) {
				$this->deny('A role with this name already exists.', 'manager/roles/add');
			}
			$this->db->insert('ci_roles', array(
				'title' => $title, 'role_type' => $type, 'parent_id' => 1, 'level' => 2, 'status' => 1,
				'permissions' => json_encode(crm_perm_normalize($this->input->post('permissions'))),
				'created' => date('Y-m-d')
			));
			$this->ci_admin_model->log_admin_action('role_add', 'Added role "' . $title . '"');
			$this->session->set_flashdata('message', 'Role "' . $title . '" added.');
			redirect('manager/roles');
		}
		$from = (int)$this->input->get('copy');
		$data['role'] = array('id' => 0, 'title' => '', 'parent_id' => 1, 'status' => 1, 'role_type' => 'department');
		$data['role_types'] = crm_role_types();
		$data['values'] = array();
		foreach ($data['roles'] as $r) {
			if ((int)$r['id'] === $from) $data['values'] = $r['expanded'];
		}
		$data['readonly'] = false;
		$data['users_in_role'] = 0;
		$this->load->view('manager/roles/edit', $data);
	}

	public function edit($id = '')
	{
		$id = (int)$id;
		$role = $this->db->get_where('ci_roles', array('id' => $id, 'status' => 1))->row_array();
		if (empty($role)) $this->deny('Role not found.');

		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			if (!$this->can_write()) $this->deny('You can view roles but not change them (needs Full on Roles & Permissions).');
			if ($id === 1) $this->deny('The Admin role always has full access and cannot be changed.');
			if (!crm_is_full_admin() && $id === (int)$this->session->userdata('manager_role')) {
				$this->deny('You cannot change your own role. Ask a full administrator.');
			}
			$title = trim((string)$this->input->post('title', true));
			$type = $this->input->post('role_type') === 'system' ? 'system' : 'department';
			$status = (int)$this->input->post('status') === 0 ? 0 : 1;
			if ($title === '' || mb_strlen($title) > 60) $this->deny('Role name is required (up to 60 characters).', 'manager/roles/edit/' . $id);
			if ($this->db->where('title', $title)->where('id !=', $id)->where('status', 1)->count_all_results('ci_roles') > 0) {
				$this->deny('A role with this name already exists.', 'manager/roles/edit/' . $id);
			}
			if ($status === 0 && $this->db->where('user_type', $id)->where('status', 1)->count_all_results('ci_user') > 0) {
				$this->deny('Move the people in this role to another role before making it inactive.', 'manager/roles/edit/' . $id);
			}
			$this->db->where('id', $id)->update('ci_roles', array(
				'title' => $title, 'role_type' => $type, 'parent_id' => 1, 'level' => 2, 'status' => $status,
				'permissions' => json_encode(crm_perm_normalize($this->input->post('permissions')))
			));
			$this->ci_admin_model->log_admin_action('role_edit', 'Updated role #' . $id . ' "' . $title . '"');
			$this->session->set_flashdata('message', 'Role "' . $title . '" saved. People in this role get the new access on their next page.');
			redirect('manager/roles');
		}

		$data = array();
		$data['info'] = array('title' => 'Edit Role', 'page_heading' => 'Edit Role');
		$data['roles'] = $this->roles_overview();
		$data['role'] = $role;
		$data['values'] = array();
		foreach ($data['roles'] as $r) {
			if ((int)$r['id'] === $id) $data['values'] = $r['expanded'];
		}
		$data['readonly'] = ($id === 1) || !$this->can_write();
		$data['users_in_role'] = $this->db->where('user_type', $id)->where('status', 1)->count_all_results('ci_user');
		$data['role_types'] = crm_role_types();
		$this->load->view('manager/roles/edit', $data);
	}

	/** One role: its type, its people (with Move to another role) and its access, area by area. */
	public function view($id = '')
	{
		$id = (int)$id;
		$roles = $this->roles_overview();
		$role = null;
		foreach ($roles as $r) { if ((int)$r['id'] === $id) $role = $r; }
		if (empty($role)) $this->deny('Role not found.');
		$data = array();
		$data['info'] = array('title' => 'Role: ' . $role['title'], 'page_heading' => 'Role');
		$data['role'] = $role;
		$data['roles'] = $roles;
		$data['role_types'] = crm_role_types();
		$data['people'] = $this->db->query("SELECT u.id, u.name, u.email, u.emp_id, u.admin_status, m.name AS manager_name,
				(SELECT X.left_on FROM ci_user_exit X WHERE X.user_id = u.id LIMIT 1) AS left_on
			FROM ci_user u LEFT JOIN ci_user m ON m.id = u.manager
			WHERE u.status = 1 AND u.user_type = " . $id . " ORDER BY u.admin_status DESC, u.name ASC")->result_array();
		$data['can_write'] = $this->can_write();
		$data['is_full_admin'] = crm_is_full_admin();
		$data['me'] = (int)$this->session->userdata('manager_id');
		$data['profile_base'] = crm_can('users') ? 'manager/users/view/' : (crm_can('hr_staff') ? 'manager/hr/staff/view/' : '');
		$this->load->view('manager/roles/view', $data);
	}

	/** Move the ticked people of one role to another role. Admin role in or out: full admins only; never yourself. */
	public function move()
	{
		if ($this->input->server('REQUEST_METHOD') !== 'POST') redirect('manager/roles');
		$from = (int)$this->input->post('from_role');
		$back = $from > 0 ? 'manager/roles/view/' . $from : 'manager/roles';
		if (!$this->can_write()) $this->deny('Moving people needs Full on Roles & Permissions.', $back);
		$to = (int)$this->input->post('to_role');
		$target = $this->db->get_where('ci_roles', array('id' => $to, 'status' => 1))->row_array();
		if (empty($target)) $this->deny('Choose the role to move them to.', $back);
		$ids = array_values(array_unique(array_filter(array_map('intval', (array)$this->input->post('ids')))));
		if (!$ids) $this->deny('Tick at least one person.', $back);
		$full = crm_is_full_admin();
		$me = (int)$this->session->userdata('manager_id');
		if ($to === 1 && !$full) $this->deny('Only a full administrator can move people into the Admin role.', $back);
		$titles = array();
		foreach ($this->db->select('id, title')->get('ci_roles')->result_array() as $r) $titles[(int)$r['id']] = $r['title'];
		$has_hist = $this->db->table_exists('ci_user_history');
		$done = 0; $skip = array();
		foreach ($this->db->select('id, name, user_type')->where_in('id', $ids)->get('ci_user')->result_array() as $u) {
			$uid = (int)$u['id'];
			if ($uid === $me || $uid === 1) { $skip[] = $u['name'] . ($uid === $me ? ' (yourself)' : ' (main administrator)'); continue; }
			if ((int)$u['user_type'] === 1 && !$full) { $skip[] = $u['name'] . ' (Admin account)'; continue; }
			if ((int)$u['user_type'] === $to) continue;
			$this->db->where('id', $uid)->update('ci_user', array('user_type' => $to, 'modified' => date('Y-m-d')));
			if ($has_hist) {
				$old = isset($titles[(int)$u['user_type']]) ? $titles[(int)$u['user_type']] : '';
				$this->db->insert('ci_user_history', array('user_id' => $uid, 'field' => 'Role', 'old_value' => $old, 'new_value' => $target['title'],
					'changed_by' => $me, 'changed_at' => date('Y-m-d H:i:s')));
			}
			$done++;
		}
		$this->ci_admin_model->log_admin_action('role_move', 'Moved ' . $done . ' user(s) to role #' . $to . ' "' . $target['title'] . '": #' . implode(', #', $ids), 'SUCCESS', $me, $this->session->userdata('manager_name'));
		$msg = $done . ' ' . ($done === 1 ? 'person' : 'people') . ' moved to ' . $target['title'] . '. Their access changes on their next page.';
		if ($skip) $msg .= ' Skipped: ' . implode(', ', $skip) . '.';
		$this->session->set_flashdata($done || !$skip ? 'message' : 'error_message', $msg);
		redirect($back);
	}

	public function delete($id = '')
	{
		$id = (int)$id;
		if ($this->input->server('REQUEST_METHOD') !== 'POST') $this->deny('Use the Delete button to remove a role.');
		if (!$this->can_write()) $this->deny('You can view roles but not delete them.');
		if ($id <= 1) $this->deny('The Admin role cannot be deleted.');
		$n = $this->db->where('user_type', $id)->count_all_results('ci_user');
		if ($n > 0) $this->deny('This role still has ' . $n . ' ' . ($n === 1 ? 'person' : 'people') . '. Move them to another role first.');
		$this->db->where('id', $id)->update('ci_roles', array('status' => 0));
		$this->ci_admin_model->log_admin_action('role_delete', 'Deleted role ID #' . $id, 'SUCCESS', $this->session->userdata('manager_id'), $this->session->userdata('manager_name'));
		$this->session->set_flashdata('message', 'Role removed.');
		redirect('manager/roles');
	}

	public function ajax_get_user_permissions($user_id = 0)
	{
		$user_id = (int)$user_id;
		$user = $this->db->query("SELECT u.id, u.name, u.email, u.user_type, u.department, r.title AS role_title, r.permissions AS role_perms
			FROM ci_user u LEFT JOIN ci_roles r ON u.user_type = r.id WHERE u.id = " . $user_id . " LIMIT 1")->row_array();
		if (empty($user)) $this->json(array('status' => 'error', 'message' => 'User not found.'));

		$row = $this->db->query("SELECT permission_mode, permissions FROM ci_user_permissions WHERE user_id = " . $user_id . " LIMIT 1")->row_array();
		$mode = !empty($row['permission_mode']) && in_array($row['permission_mode'], array('role', 'admin', 'custom'), true) ? $row['permission_mode'] : 'role';
		$role_map = json_decode((string)$user['role_perms'], true);
		$role_exp = ((int)$user['user_type'] === 1) ? array_fill_keys(array_keys(crm_perm_items()), 'write') : crm_perm_expand(is_array($role_map) ? $role_map : array());
		$custom = json_decode((string)(isset($row['permissions']) ? $row['permissions'] : ''), true);
		$custom_exp = (is_array($custom) && !empty($custom)) ? crm_perm_expand($custom) : $role_exp;

		$this->json(array(
			'status' => 'success',
			'user' => array(
				'id' => (int)$user['id'],
				'name' => $user['name'],
				'email' => $user['email'],
				'role_id' => (int)$user['user_type'],
				'role_title' => $user['role_title'] ? $user['role_title'] : 'No role',
				'department' => $user['department'] ? $user['department'] : '',
				'is_role_admin' => ((int)$user['user_type'] === 1 || (int)$user['id'] === 1),
			),
			'mode' => $mode,
			'role_permissions' => $role_exp,
			'custom_permissions' => $custom_exp,
			'chatbot_permissions' => (object)chatbot_user_rights($user_id),
		));
	}

	public function ajax_save_user_permissions()
	{
		if (!$this->can_write()) $this->json(array('status' => 'error', 'message' => 'You can view permissions but not change them (needs Full on Roles & Permissions).'));
		$user_id = (int)$this->input->post('user_id');
		$user = $this->db->select('id, user_type')->where('id', $user_id)->get('ci_user')->row_array();
		if (empty($user)) $this->json(array('status' => 'error', 'message' => 'User not found.'));

		$mode = $this->input->post('permission_mode');
		if (!in_array($mode, array('role', 'admin', 'custom'), true)) $mode = 'role';
		$me_admin = crm_is_full_admin();
		if (!$me_admin) {
			if ($mode === 'admin') $this->json(array('status' => 'error', 'message' => 'Only a full administrator can give Full admin access.'));
			if (crm_is_full_admin($user_id)) $this->json(array('status' => 'error', 'message' => 'Only a full administrator can change an administrator.'));
			if ($user_id === (int)$this->session->userdata('manager_id')) $this->json(array('status' => 'error', 'message' => 'You cannot change your own permissions.'));
		}

		$row = $this->db->query("SELECT id FROM ci_user_permissions WHERE user_id = " . $user_id . " LIMIT 1")->row_array();
		$save = array('permission_mode' => $mode, 'updated_at' => date('Y-m-d H:i:s'));
		if ($mode === 'custom') {
			$save['permissions'] = json_encode(crm_perm_normalize($this->input->post('permissions')));
		}
		if ($row) {
			$this->db->update('ci_user_permissions', $save, array('user_id' => $user_id));
		} else {
			$save['user_id'] = $user_id;
			$save['created_at'] = date('Y-m-d H:i:s');
			if (!isset($save['permissions'])) $save['permissions'] = null;
			$this->db->insert('ci_user_permissions', $save);
		}

		// Chatbot rights (user-wise only). Every right needs "access"; without it all are cleared.
		$chat_in = $this->input->post('chatbot');
		$chat_clean = array();
		if (is_array($chat_in) && !empty($chat_in['access']) && (string)$chat_in['access'] === '1') {
			foreach (array_keys(chatbot_rights()) as $k) {
				if (!empty($chat_in[$k]) && (string)$chat_in[$k] === '1') $chat_clean[$k] = 1;
			}
		}
		chatbot_ensure_column();
		$this->db->where('user_id', $user_id)->update('ci_user_permissions', array('chatbot_permissions' => empty($chat_clean) ? null : json_encode($chat_clean)));
		if (empty($chat_clean) && $this->db->table_exists('ci_support_agents')) {
			$this->db->where('user_id', $user_id)->update('ci_support_agents', array('available' => 0));
		}

		$this->ci_admin_model->log_admin_action('user_perm_update', 'Updated permissions for user ID #' . $user_id . ' (mode: ' . $mode . ', chatbot: ' . (empty($chat_clean) ? 'none' : implode(',', array_keys($chat_clean))) . ')', 'SUCCESS', $this->session->userdata('manager_id'), $this->session->userdata('manager_name'));
		$this->json(array('status' => 'success', 'message' => 'Permissions saved. They apply on the person\'s next page.', 'mode' => $mode, 'chatbot' => !empty($chat_clean)));
	}
}
