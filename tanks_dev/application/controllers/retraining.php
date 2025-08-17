<?php

class Retraining_Controller extends Template_Controller {

	public $name = 'retraining';
	public $prev_name = 'facility';

	public $label = 'Retraining';

	public $model_name = 'Retraining';

	public $template = 'tpl_internal';  // default template for all reports

	public function __construct() {
		parent::__construct();
		$this->template->nav_id = $this->name;
	}

	public function view($retraining_id) {
		$view = new View('retraining_view');

		$view->retraining = new Retraining_Model();
		$view->facility_id = $view->retraining->get_facility_id($retraining_id);
		$view->row = $view->retraining->get_retraining_detail($view->facility_id, $retraining_id)['out_cur'][0];
		$retraining_items = array();
		foreach($view->retraining->get_retraining_detail($view->facility_id, $retraining_id)['out2_cur'] as $retraining_item) {
			$retraining_items[] = $retraining_item['TRAINING_ITEM_DESC'];
		}
		
		$view->retraining_items = $retraining_items;

		if (! $view->row)
			$this->_go_message(Controller::_instance('Facility')->_index_url(), 'No Retraining with selected ID exists.');

		$this->template->content = $view;
	}

	public function add_action($facility_id) {
		$input_arr = $this->input->post();
		$model = new Retraining_Model();
		$add_retraining = $model->add_retraining($facility_id, $input_arr);
		$result = json_decode($add_retraining, true);

		$parent_url = $this->_parent_url($facility_id);
		if($result['success_flag_outvar'] == 'Y') {
			$this->_go_message($parent_url, "Successfully created {$this->name}.", 'info_message');
		}else {
			$this->_go_message($parent_url, "Error occurred while creating retraining.");
		}
	}

	// override parent method to get retraining items
	public function edit($retraining_id) {
		$view = new View('retraining_edit');

		$view->retraining = new Retraining_Model();
		$view->facility_id = $view->retraining->get_facility_id($retraining_id);
		$retraining_detail = $view->retraining->get_retraining_detail($view->facility_id, $retraining_id);
		$view->row = $retraining_detail['out_cur'][0];
		$retraining_items = $retraining_detail['out2_cur'];
		$retraining_item_ids = array();
		foreach($retraining_items as $item) {
			array_push($retraining_item_ids, $item["TRAINING_ITEM_ID"]);
		}
		$view->retraining_item_ids = $retraining_item_ids;
		$view->is_add = FALSE;
		$view->action = $this->_edit_action_url($retraining_id);

		if (! $view->row) {
			$this->_go_message(Controller::_instance('Facility')->_index_url(), 'No Retraining with selected ID exists.');	
		}
		
		$this->template->content = $view;
	}

	public function edit_action($retraining_id) {
		$input_arr = $this->input->post();
		$model = new Retraining_Model();
		$facility_id = $model->get_facility_id($retraining_id);
		$edit_retraining = $model->edit_retraining($facility_id, $retraining_id, $input_arr);
		$result = json_decode($edit_retraining, true);

		$parent_url = $this->_parent_url($facility_id);
		if($result['success_flag_outvar'] == 'Y') {
			$this->_go_message($parent_url, "Successfully updated {$this->name}.", 'info_message');
		}else {
			$this->_go_message($parent_url, "Error occurred while updating retraining.");
		}
	}
}
