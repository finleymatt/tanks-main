<?php

class Tank_detail_Controller extends Template_Controller {

	public $name =  'tank_detail';
	public $prev_name = 'tank';
	public $model_name = 'Tank_details';

	public $template = 'tpl_internal';  // default template for all reports


	public function __construct() {
		parent::__construct();
		$this->template->nav_id = $this->name;
	}

	/**
	 * Adds complete list of tank details in one operation.
	 */
	public function add($tank_id) {
		$this->view = new View('tank_detail_edit');
		$this->view->model = $this->_model_instance();
		$this->view->action = $this->_add_action_url($tank_id);
		$this->view->tank_id = $tank_id;

		$this->view->tank_detail_rows = $this->view->model->get_list(array('TANK_ID'=>$tank_id));
		$vals = array();
		foreach($this->view->tank_detail_rows as $row)
			$vals[] = $row["TANK_DETAIL_CODE"];
		$this->view->tank_detail_vals = $vals;

		$this->view->tank_info_codes = Model::instance('Tank_info_codes');
		$this->view->tank_info_code_rows = $this->view->tank_info_codes->get_list(NULL, 'CODE');

		$this->view->tank_detail_codes = Model::instance('Tank_detail_codes');

		$this->view->gonm_rules = Tank_details_Model::$GONM_RULES;

		$this->template->content = $this->view;
	}

	/**
	 * Adds complete list of tank details in one operation.
	 * Deletes all first, then adds the detail codes
	 */
	public function add_action($tank_id) {
		$parent_ids = func_get_args();
		$model = $this->_model_instance();

		// consolidate all tank detail codes into one array -----------
		$inputs = $this->input->post();  $all_codes = array();
		foreach($inputs as $input_name => $input)
			if (strstr($input_name, 'tank_detail_code_'))
				$all_codes = array_merge($all_codes, $input);

		// clear and insert all selected tank details -----------------
		if ($model->update_all($tank_id, $all_codes))
			$this->_go_message($this->_parent_url($parent_ids), "Successfully created {$this->name}.", 'info_message');
		else
			$this->_go_message($this->_parent_url($parent_ids), "Error occurred while creating {$this->name}.");
	}
}
