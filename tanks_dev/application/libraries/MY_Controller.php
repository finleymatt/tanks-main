<?php defined('SYSPATH') or die('No direct script access.');

class Controller extends Controller_Core {

	public $name = '';              // scaffolding: used for url, view name, and display
	public $label = NULL;		// used as display if set, otherwise $name is used
	public $prev_name = NULL;	// breadcrumbs: name of parent controller in link heirarchy
	public $model_name = '';	// scaffolding: used for loading model
					// don't include '_Model' part of the name
	public $template = '';
	public $auto_render = TRUE;

	public $history_max = 5;
	protected $sam;

	/**
	 * Called with name of a child Controller class name to reuse an existing instance
	 **/
	public static function _instance($class_name) {
		static $instance;

		if (empty($instance[$class_name])) {
			$full_name = "{$class_name}_Controller";
			$instance[$class_name] = new $full_name();
		}

		return $instance[$class_name];
	}

	/**
	 * Overridden to require SEP login for all pages (except login)
	 */
	public function __construct() {
		parent::__construct();

		// redirect to home directly and bypass authentication and login, only used in local VM, remove this line in T, Q & P
		//url::redirect('http://waste.web-ghuang.nmenv.state.nm.us/home');

		if (in_array('login', $this->uri->segment_array()))
			return;

		// if not authenticated, redirect -------------------------------
		$this->sam = Sam::instance();

		// lcal machine or not
		if(strtolower($_SERVER['SERVER_NAME']) !== 'waste.web-ghuang.nmenv.state.nm.us') {
			if (! $this->sam->IsAuthenticated($this->input->get('sessionId'))) {
				if (count($this->uri->segment_array())) // if not visiting home page
					url::redirect('/login/timeout');
				else
					url::redirect('/login');
			}
		} else {
			$this->sam->IsAuthenticated($this->input->get('sessionId'));
		}
	}

	// basic CRUD controller methods =============================================
	// Any of these can be called with more than one argument for multiple keyed-data

	/**
	 * Use when no other data needs to be displayed.  Ex: child records
	 */
	public function view($id) {
		$ids = func_get_args();

		$this->view = new View("{$this->name}_view");
		$this->view->model = $this->_model_instance();
		$this->view->row = $this->view->model->get_row($ids);
		if (! $this->view->row)
                	$this->_go_message($this->_error_url(), "No {$this->name} with selected ID exists.");

		$this->template->content = $this->view;
	}

	public function edit($id) {
		$ids = func_get_args();

		$this->view = new View("{$this->name}_edit");
		$this->view->is_add = FALSE;
		$this->view->model = $this->_model_instance();
		$this->view->action = $this->_edit_action_url($ids);
		$this->view->row = $this->view->model->get_row($ids);
		if (! $this->view->row)
			$this->_go_message($this->_error_url(), "No {$this->name} with selected ID exists.");

		$this->template->content = $this->view;
	}

	public function edit_action($id) {
		$ids = func_get_args();
		$parent_url = $this->_parent_url(NULL, $ids);  // find before data edit

		$label = ($this->label ? $this->label : $this->_friendly_name($this->name));
		$model = $this->_model_instance();
		if ($model->update($ids, $this->input->post()))
			$this->_go_message($parent_url, "Successfully updated {$label}.", 'info_message');
		else
			$this->_go_message($this->_edit_url($ids), "Error occurred while updating {$label}.");
	}

	public function add($parent_id) {
		$parent_ids = func_get_args();

		$this->view = new View("{$this->name}_edit");
		$this->view->is_add = TRUE;
		$this->view->model = $this->_model_instance();
		$this->view->action = $this->_add_action_url($parent_ids);
		$this->view->parent_ids = $parent_ids;
		$this->view->row = NULL;

		$this->template->content = $this->view;
	}

	public function add_action($parent_id) {
		$parent_ids = func_get_args();

		$model = $this->_model_instance();
		if ($model->insert($parent_ids, $this->input->post()))
			$this->_go_message($this->_parent_url($parent_ids), "Successfully created {$this->name}.", 'info_message');
		else
			$this->_go_message($this->_parent_url($parent_ids), "Error occurred while creating {$this->name}.");
	}

	public function delete($id) {
		$ids = func_get_args();

		$this->view = new View('generic_delete');
		$this->view->object_name = ucfirst($this->name);
		$this->view->delete_url = $this->_delete_action_url($ids);

		// get rows that are about to be deleted
		$this->view->model = $this->_model_instance();
		$where = text::where_pk($this->view->model->pks, $ids);
		$this->view->rows = $this->view->model->get_list($where);

		if (method_exists($this, '_delete_pre_render'))
			$this->_delete_pre_render();

		$this->template->content = $this->view;
	}

	/**
	 * If cascade deletes exist, they should be implemented in model->delete($id) or stored proc
	 */
	public function delete_action($id) {
		$ids = func_get_args();
		$parent_url = $this->_parent_url(NULL, $ids);

		$model = $this->_model_instance();
		if ($model->delete($ids))
			$this->_go_message($parent_url, "Successfully deleted {$this->name}.", 'info_message');
		else
			$this->_go_message($parent_url, "Error occurred while deleting {$this->name}.");
	}

	public function _view_button($id, $is_id_only=TRUE, $label='view') {
		$model = $this->_model_instance();
		return(Sam::instance()->if_priv($model->table_name, 'SELECT', form::view_button($this->_view_url($id, $is_id_only), $label)));
	}

	public function _edit_button($id, $is_id_only=TRUE, $label='edit') {
		$model = $this->_model_instance();
		return(Sam::instance()->if_priv($model->table_name, 'UPDATE', form::edit_button($this->_edit_url($id, $is_id_only), $label)));
	}

	public function _add_button($id, $label='add new') { // add_button takes ids only
		$model = $this->_model_instance();
		return(Sam::instance()->if_priv($model->table_name, 'INSERT', form::add_button($this->_add_url($id), $label)));
	}

	public function _delete_button($id, $is_id_only=TRUE, $label='delete') {
		$model = $this->_model_instance();
		return(Sam::instance()->if_priv($model->table_name, 'DELETE', form::delete_button($this->_delete_url($id, $is_id_only), $label)));
	}

	public function _print_button($id, $is_id_only=TRUE, $label='print') {
		$model = $this->_model_instance();
		return(Sam::instance()->if_priv($model->table_name, 'SELECT', form::print_button($this->_print_url($id, $is_id_only), $label)));
	}

	public function _index_url($ids=NULL) {
		return(url::fullpath("/{$this->name}/", $ids));
	}

	public function _view_url($ids, $is_id_only=TRUE) {
		return($this->_crud_url($ids, 'view', $is_id_only));
	}

	public function _edit_url($ids, $is_id_only=TRUE) {
		return($this->_crud_url($ids, 'edit', $is_id_only));
	}

	public function _edit_action_url($ids, $is_id_only=TRUE) {
		return($this->_crud_url($ids, 'edit_action', $is_id_only));
	}

	public function _add_url($ids) {
		return($this->_crud_url($ids, 'add', TRUE)); // add link must specify all ids directly
	}

	public function _add_action_url($ids, $is_id_only=TRUE) {
		return($this->_crud_url($ids, 'add_action', $is_id_only));
	}

	public function _delete_url($ids, $is_id_only=TRUE) {
		return($this->_crud_url($ids, 'delete', $is_id_only));
	}

	public function _delete_action_url($ids, $is_id_only=TRUE) {
		return($this->_crud_url($ids, 'delete_action', $is_id_only));
	}

	public function _print_url($ids, $is_id_only=TRUE) {
		return($this->_crud_url($ids, 'print_file', $is_id_only)); // 'print' is php keyword
	}

	protected function _crud_url($ids, $link_name, $is_id_only=TRUE) {
		if (!$is_id_only) $ids = $this->_model_instance()->ids($ids); // filter for pks
		return(url::fullpath("/{$this->name}/{$link_name}/", $ids));
	}

	public function _parent_url($parent_ids, $child_ids=NULL) {
		if ($child_ids)  // find parent ids from child ids
			$parent_ids = $this->_model_instance()->parent_ids($child_ids);

		if ($parent_ids && $this->prev_name) {
			$parent_controller = Controller::_instance(ucfirst($this->prev_name));
			return($parent_controller->_view_url($parent_ids));
		}
		else {
			return(url::fullpath("/"));
		}
	}

	public function _error_url() {
		return(url::fullpath("/"));
	}

	/**
	 * Returns controller name in a user-presentable format
	 */
	public function _friendly_name() {
		if (isset($this->label))
			return($this->label);
		else
			return(ucfirst(strtr($this->name, array('_'=>' '))));
	}

	/**
	 * Accepts array('type'=>$val, 'id'=>$val, 'name'=$val)
	 */
	public function _save_history($obj_info) {
		$this->session = Session::instance();
		if ($history = $this->session->get('history')) {
			if ($history[0] == $obj_info)  // already at the top
				return;
			if ($matched_id = array_search($obj_info, $history))  // if matched, remove
				array_splice($history, $matched_id, 1);
			if (count($history) >= $this->history_max)  // remove last element
				$deleted = array_pop($history);
		}
		else {
			$history = array();
		}
		array_unshift($history, $obj_info);  // add the new element at beginning
		$this->session->set('history', $history);
	}

	public function _priv_message($url, $table_name, $priv) {
		if (! Sam::instance()->has_priv($table_name, $priv))
			$this->_go_message($url, 'Insufficient Privileges');
	}

	/**
	 * Accepts two types of messages: error_message, info_message
	 */
	public function _go_message($url, $message, $msg_type='error_message') {
		$session = Session::instance();

		$all_errors = $session->get_once('error_message');
		$session->set($msg_type, $message);

		if ($all_errors) {
			if (is_array($all_errors)) {
				$form_message = '';
				foreach($all_errors as $field => $form_error)
					$form_message .= "<li>'{$field}' failed rule: {$form_error}</li>";
				$session->set('error_message', $session->get('error_message') . "<ul>{$form_message}</ul>");
			}
			else
				$session->set('error_message', $session->get('error_message') . "<br />{$all_errors}");
		}

		url::redirect($url);
	}

	public function _handle_error($message='Error has occurred in Onestop.') {
		throw new Kohana_User_Exception('Onestop User Form Error', $message);
	}

	public function _model_instance() {
		return(Model::instance($this->model_name));	
	}

	/**
	 * Shortcut for handling missing required fields.  Only used for reports.
	 */
	protected function _validate_req($fields, $error_page=NULL, $message='Required field(s) not entered.') {
		$validation = new Validation(array_merge($this->input->get(), $this->input->post()));
		foreach ($fields as $key => $value) {
			if (is_numeric($key)) // if rule not provided
				$validation->add_rules($value, 'required');
			else
				$validation->add_rules($key, $value);
		}

		if (! $validation->validate()) {
			if ($error_page)
				$this->_go_message($error_page, $message);
			else
				$this->_handle_error($message);
		}
		
		return(TRUE);
	}

	/**
	 * Given primary key names and row of data, returns where clause
	 * Not used due to string PKs causing trouble -- instead using text::where_pk()
	 */
	protected function _make_where_pk($pks, $values) {
		$result = array();
		if (!is_array($pks)) $pks = array($pks);

		if (count($values) > 1) {
			// due to limitation in query builder, can't use bound vars for multiple keys
			$result['where'] = array();  $result['bound_vars'] = array();
			foreach ($pks as $index => $pk)
				$result['where'][$pk] = $values[$index];
		}
		else {
			$result['where'] = "{$pks[0]} = :ID";
			$result['bound_vars'][':ID'] = $values[0];
		}

		if (count($result['where']) <= 0)
			$this->_go_message($this->_error_url(), 'No WHERE clause defined');
		return($result);
	}
}

?>
