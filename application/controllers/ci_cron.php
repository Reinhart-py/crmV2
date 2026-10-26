<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ci_cron extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('ci_admin_model');
	}

	public function daily_schedule_reminders()
	{
		$processed_recurring = $this->_run_task_recurrence();
		$reminders_count = $this->_process_daily_reminders();

		header('Content-Type: application/json');
		echo json_encode(array(
			'status' => true,
			'message' => 'Daily schedule reminders and recurrence executed successfully',
			'processed_recurring' => $processed_recurring,
			'reminders_count' => $reminders_count,
			'timestamp' => date('Y-m-d H:i:s')
		));
		exit();
	}

	public function recurrence_runner()
	{
		$processed_recurring = $this->_run_task_recurrence();
		header('Content-Type: application/json');
		echo json_encode(array(
			'status' => true,
			'message' => 'Task recurrence processor completed',
			'spawned_tasks' => $processed_recurring,
			'timestamp' => date('Y-m-d H:i:s')
		));
		exit();
	}

	private function _run_task_recurrence()
	{
		$today = date('Y-m-d');
		$this->db->from('ci_tasks');
		$this->db->where('is_recurring', 1);
		$this->db->where('next_occurrence IS NOT NULL');
		$this->db->where('next_occurrence <=', $today);
		$due_tasks = $this->db->get()->result_array();

		$spawned = 0;
		if (!empty($due_tasks)) {
			foreach ($due_tasks as $task) {
				$next_date = $this->_calculate_next_occurrence($task['next_occurrence'], $task['recurrence_freq']);

				$new_task = $task;
				unset($new_task['id']);
				$new_task['task_id'] = 'T' . date('ymd') . rand(1000, 9999);
				$new_task['status'] = 'Open';
				$new_task['is_recurring'] = 0;
				$new_task['next_occurrence'] = null;
				$new_task['created'] = date('Y-m-d H:i:s');
				$new_task['modified'] = date('Y-m-d H:i:s');
				$new_task['remark'] = "[Auto-Generated Recurring Instance of Task #" . $task['task_id'] . "] " . $task['remark'];

				$this->db->insert('ci_tasks', $new_task);
				$new_id = $this->db->insert_id();

				$this->db->where('id', $task['id'])->update('ci_tasks', array(
					'next_occurrence' => $next_date,
					'modified' => date('Y-m-d H:i:s')
				));

				$this->ci_admin_model->log_admin_action(
					'task_recurrence_spawn',
					"Spawned recurring task ID #$new_id from parent task #" . $task['id'],
					'SUCCESS',
					$task['created_by'],
					'System Cron'
				);
				$spawned++;
			}
		}
		return $spawned;
	}

	private function _calculate_next_occurrence($current_date, $freq)
	{
		$base_time = strtotime($current_date);
		if (!$base_time) {
			$base_time = time();
		}

		$freq = strtolower(trim($freq));
		if ($freq === 'daily') {
			return date('Y-m-d', strtotime('+1 day', $base_time));
		} elseif ($freq === 'weekly') {
			return date('Y-m-d', strtotime('+1 week', $base_time));
		} elseif ($freq === 'monthly') {
			return date('Y-m-d', strtotime('+1 month', $base_time));
		} elseif ($freq === 'yearly') {
			return date('Y-m-d', strtotime('+1 year', $base_time));
		}
		return date('Y-m-d', strtotime('+1 month', $base_time));
	}

	private function _process_daily_reminders()
	{
		$today = date('Y-m-d');
		$count = 0;

		$this->db->select('id, task_id, customer, assigned_to, close_date');
		$this->db->from('ci_tasks');
		$this->db->where('close_date', $today);
		$this->db->where('status !=', 'Closed');
		$this->db->where('status !=', 'Completed');
		$tasks_due = $this->db->get()->result_array();
		$count += count($tasks_due);

		$this->db->select('F.*, U.name as agent_name');
		$this->db->from('ci_followup F');
		$this->db->join('ci_user U', 'U.id = F.user_id', 'left');
		$this->db->where('F.followup_date', $today);
		$followups_due = $this->db->get()->result_array();
		$count += count($followups_due);

		return $count;
	}
}
