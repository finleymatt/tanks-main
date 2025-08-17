<?php

class Permit_Controller extends Template_Controller {

	public $name = 'permit';
	public $model_name = 'Permits';
	public $prev_name = 'facility';

	public $template = 'tpl_internal';  // default template for all reports

	public function __construct() {
		parent::__construct();
		$this->template->nav_id = $this->name;
	}

	/**
 	 * Certificate generate and print page
 	 */
	public function index() {
		global $GLOBAL_INI;
		$view = new View('permit_menu');
		$view->model = new Permits_Model();

		$this->template->skip_bread_crumbs = TRUE; // is outside of path
		$this->template->content = $view;
	}

	public function print_action() {
		if (Model::instance('Ust_log')->is_batch_running())
			$this->_go_message($this->_index_url(), 'Request not accepted due to another batch process already running. Please wait for the previous request to finish or contact the helpdesk.', 'error_message');

		$owner_id = $this->input->post('owner_id');
		$facility_id = $this->input->post('facility_id');
		$date_permitted = Model::str_date_db($this->input->post('date_permitted'));
		$fy = $this->input->post('fy');

		$this->_model_instance()->print_batch($owner_id, $facility_id, $date_permitted, $fy);

		$this->_go_message($this->_index_url(), "Single certificate batch started", 'info_message');
	}

	public function print_all_action() {
		if (Model::instance('Ust_log')->is_batch_running())
			$this->_go_message($this->_index_url(), 'Request not accepted due to another batch process already running. Please wait for the previous request to finish or contact the helpdesk.', 'error_message');

		$fy = $this->input->post('fy');

		$this->_model_instance()->print_all_batch($fy);

		$this->_go_message($this->_index_url(), "All certificates batch started", 'info_message');
	}

	public function reset_status() {
		$ust_log = new Ust_log_Model();
		$ust_log->reset_permit();

		$this->_go_message($this->_index_url(), 'Certificate Batch Status has been reset.', 'info_message');
	}
}
