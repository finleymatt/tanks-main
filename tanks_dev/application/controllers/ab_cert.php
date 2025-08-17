<?php

class Ab_cert_Controller extends Template_Controller {

	public $name = 'ab_cert';
	public $prev_name = 'ab_operator';

	public $label = 'A/B/C Certificate';

	public $model_name = 'Ab_cert';

	public $template = 'tpl_internal';  // default template for all reports


	public function __construct() {
		parent::__construct();
		$this->template->nav_id = $this->name;
	}

	public function view($id=NULL) {
		$view = new View('ab_cert_view');

		$view->model = new Ab_cert_Model();
		$view->row = $view->model->get_row($id);
		if (! $view->row)
                	$this->_go_message(Controller::_instance('Facility')->_index_url(), 'No A/B Certificate with selected ID exists.');

		$this->template->content = $view;
	}

	/**
	 * Add method overridden to redirect to Facility list page
	 */
	public function add_action($parent_id) {
		$parent_ids = func_get_args();

		$model = $this->_model_instance();
		if ($model->insert($parent_ids, $this->input->post())) {
			$ab_operator_row = Model::instance('Ab_operator')->get_row($parent_ids[0]);
			$this->_go_message(Controller::_instance('Facility')->_view_url($ab_operator_row['FACILITY_ID']), "Successfully created {$this->name}.", 'info_message');
		}
		else
			$this->_go_message($this->_parent_url($parent_ids), "Error occurred while creating {$this->name}.");
	}
}
