<?php
/**
 * Test result tester Controller
 *
 * @package ### file docblock
 * @subpackage controllers
 *
 */

class Test_result_tester_Controller extends Template_Controller {

	public $name = 'test_result_tester';
	public $model_name = 'Ref_test_results_testers';
	public $template = 'tpl_internal';

	public function __construct() {
		parent::__construct();
		$this->template->nav_id = $this->name;
	}

	public function add_tester($facility_id) {
		$this->view = new View("tester_add");
		//$this->view->model = $this->model_name;
		$this->view->action = url::fullpath('/test_result_tester/add_tester_action/' . $facility_id);

		$this->template->content = $this->view;
	}

	public function delete_tester($facility_id) {
		$this->view = new View("tester_delete");
		$this->view->action = url::fullpath('/test_result_tester/delete_tester_action/' . $facility_id);
		

		$this->template->content = $this->view;
	}

	public function add_tester_action($facility_id) {
		$tester_name = $this->input->post();

		$model = $this->_model_instance();
		$add_tester = $model->add_tester($tester_name);
		$result = json_decode($add_tester, true);
		
		$parent_url = url::fullpath('/test_result/add/' . $facility_id);
		if($result['success_flag_outvar'] == 'Y') {
			$this->_go_message($parent_url, "Successfully created {$this->name}.", 'info_message');
		} else {
			$this->_go_message($parent_url, "Error occurred while creating tester.");
		}
	}

	public function delete_tester_action($facility_id) {
		$input_arr = $this->input->post();
		$all_ids = array();

		// consolidate all tester ids into one array -----------
		foreach($input_arr as $input_name => $input) {
			if($input_name == 'test_result_tester') {
				$all_ids = array_merge($all_ids, $input);
			}
		}

		$model = $this->_model_instance();
		$delete_testers = $model->delete_testers($all_ids);
		$result = json_decode($delete_testers, true);

		$parent_url = url::fullpath('/test_result/add/' . $facility_id);
		if($result['success_flag_outvar'] == 'Y') {
			$this->_go_message($parent_url, "Successfully deleted {$this->name}.", 'info_message');
		} else {
			$this->_go_message($parent_url, "Error occurred while deleting testers.");
		}
	}
}
