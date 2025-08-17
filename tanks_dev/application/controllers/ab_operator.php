<?php

class Ab_operator_Controller extends Template_Controller {

	public $name = 'ab_operator';
	public $prev_name = 'facility';

	public $label = 'A/B/C Operator';

	public $model_name = 'Ab_operator';

	public $template = 'tpl_internal';  // default template for all reports


	public function __construct() {
		parent::__construct();
		$this->template->nav_id = $this->name;
	}

	/**
 	 * A/B Operator Letter print page
 	 */
	public function index() {
		$view = new View('ab_operator_menu');
		$view->model = new Ab_operator_Model();

		// auto-populate previous entered fields --------------
		$entered = Session::instance()->get('abop_single');
		$view->owner_id = (isset($entered['owner_id']) ? $entered['owner_id'] : '');
		$view->single_letter_date = (isset($entered['letter_date']) ? $entered['letter_date'] : date('m/d/Y'));

		$entered = Session::instance()->get('abop_batch');
		$view->batch_letter_date = (isset($entered['letter_date']) ? $entered['letter_date'] : date('m/d/Y'));

		$this->template->skip_bread_crumbs = TRUE; // is outside of path
		$this->template->content = $view;
	}

	public function view($id=NULL) {
		// if arrived here by search
		if (($id == NULL) && ($post_id = $this->input->post('id'))) {
			//Session::instance()->set('facility_search', $this->input->post());
			url::redirect("/ab_operator/view/{$post_id}");
		}

		$view = new View('ab_operator_view');

		$view->model = new Ab_operator_Model();
		$view->row = $view->model->get_row($id);
		if (! $view->row)
                	$this->_go_message(Controller::_instance('Facility')->_index_url(), 'No AB Operator with selected ID exists.');

		$view->ab_cert = new Ab_cert_Model();
		$view->ab_cert_rows = $view->ab_cert->get_list('AB_OPERATOR_ID = :AB_OPERATOR_ID', NULL, array(':AB_OPERATOR_ID' => $id));

		$this->template->content = $view;
	}

	public function retraining_edit($id=NULL) {
		$view = new View('retraining_view');
		$view->model = new Ab_operator_Model();
		$view->row = $view->model->get_row($id);
		$view->action = "post";
		$view->is_add = true;
		$this->template->content = $view;
	}

	/**
	 * Add method overridden to redirect to Cert Add form
	 */
	public function add_action($parent_id) {
		$parent_ids = func_get_args();

		$model = $this->_model_instance();
		if ($new_id = $model->insert($parent_ids, $this->input->post())) {
			$this->_go_message(Controller::_instance('Ab_cert')->_add_url($new_id), "Successfully created {$this->name}. Next step: create certificate.", 'info_message');
		}
		else
			$this->_go_message($this->_parent_url($parent_ids), "Error occurred while creating {$this->name}.");
	}

	/**
	 * Create C Operator
	 * Uses predefined operator name to only track its existence.
	 */
	public function add_c($parent_id) {
		$parent_ids = func_get_args();
		$data = array('first_name' => 'C Operator', 'last_name' => 'Compliant');

		$model = $this->_model_instance();
		if ($model->insert($parent_ids, $data)) {
			$this->_go_message($this->_parent_url($parent_ids), "Successfully created C Operator.", 'info_message');
		}
		else
			$this->_go_message($this->_parent_url($parent_ids), "Error occurred while creating C Operator.");

	}

	/**
	 * Display add new C Operator button
	 * Only if this facility doesn't already have one
	 */
	public function add_c_button($id, $label='add new C Operator') {
		$model = $this->_model_instance();
		if ($model->has_c_operator($id[0]))
			return('');

		return(Sam::instance()->if_priv($model->table_name, 'INSERT',
			"<a href='{$this->_crud_url($id, 'add_c', TRUE)}'  onclick='return confirm(\"Create C Operator?\")'><div class='action_button ui-state-default ui-corner-all' title='{$label}'><span class='ui-icon ui-icon-plus' style='float:left;'></span>{$label}</div></a>"));
	}

	public function print_file() {
		$owner_id = $this->input->post('owner_id');

		require_once Kohana::find_file('vendor', 'reports/Report', TRUE);
		require_once Kohana::find_file('vendor', 'reports/tcpdf_old/tcpdf', TRUE);
		require_once Kohana::find_file('vendor', 'onestop_batch/abop_letter_print', TRUE);

		$owner_rows = select_owners(NULL, $owner_id);
		if (count($owner_rows) && print_letter($owner_rows[0]))
			$this->template->content = '';  // if success, no output
		else
			$this->_go_message($this->_index_url(), 'Selected owner does not have any active tanks.');
	}

	public function print_all_action() {
		if (Model::instance('Ust_log')->is_batch_running())
			$this->_go_message($this->_index_url(), 'Request not accepted due to another batch process already running. Please wait for the previous request to finish or contact the helpdesk.', 'error_message');

		$this->_model_instance()->print_all_batch();

		$this->_go_message($this->_index_url(), "All A/B Op Letter batch started", 'info_message');
	}

	public function reset_status() {
		$ust_log = new Ust_log_Model();
		$ust_log->reset_permit();

		$this->_go_message($this->_index_url(), 'Certificate Batch Status has been reset.', 'info_message');
	}

	protected function _delete_pre_render() {
		$this->view->note = 'Deleting an A/B Operator also deletes all its A/B operator certificates.';
	}
}
