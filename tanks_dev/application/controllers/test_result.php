<?php

class Test_result_Controller extends Template_Controller {

	public $name =  'test_result';
	public $model_name = 'Test_results';
	public $prev_name = 'facility';

	public $template = 'tpl_internal';  // default template for all reports


	public function __construct() {
		parent::__construct();
		$this->template->nav_id = $this->name;
	}

	public function view($id=NULL) {
		$view = new View('test_result_view');

		$view->model = new Test_results_Model();
		$view->row = $view->model->get_test_result($id)[0];
		if (! $view->row)
                	$this->_go_message(Controller::_instance('Facility')->_index_url(), 'No Tank with selected ID exists.');

		$this->template->content = $view;
	}

	// override add function in MY_Controller.php of libraries
	public function add($facility_id) {
		$parent_ids = func_get_args();

		$this->view = new View("test_result_add");	
		$this->view->model = $this->_model_instance();
		$this->view->action = $this->_add_action_url($parent_ids);
		$this->view->parent_ids = $parent_ids;
		$this->view->row = NULL;

		$this->template->content = $this->view;
	}

	public function add_action($facility_id) {
		$input_arr = $this->input->post();

		$tank_id_arr = [];
		$test_results = [];
		$test_company = new Ref_test_results_test_company_Model();
		$tester = new Ref_test_results_testers_Model();

		// get tester and test company id if they exist, add test company id and tester id to input array if not
		foreach($input_arr as $key => $value) {
			if(substr($key, 0, strrpos($key, "_")) == 'testing_company') {
				$test_company_id = $test_company->get_test_company_id(urlencode($value));
				$input_arr[$key] = $test_company_id;
			} else if (substr($key, 0, strrpos($key, "_")) == 'tester') {
				$tester_id = $tester->get_tester_id($value);
				$input_arr[$key] = $tester_id;
			}
		}

		// add all tank ids into an array
		foreach($input_arr as $key => $value) {
			if(substr($key, 0, 8) == 'checkbox') {
				$tank_id = substr($key, strrpos($key, "_")+1);
				array_push($tank_id_arr,  $tank_id);
			}
		}
		// create multidimensional array for UDAPI
		foreach($tank_id_arr as $tank_id) {

			foreach($input_arr as $key => $value) {
				$tank = substr($key, strrpos($key, "_")+1);
				if($tank == $tank_id) {
					$test_results[$tank_id][$key] = $value;
				}
			}
		}

		$test_result = new Test_results_Model();
		$add_test_results = $test_result->add_test_results($facility_id, $test_results);
		$result = json_decode($add_test_results, true);

		$parent_url = $this->_parent_url($facility_id);
		if($result['success_flag_outvar'] == 'Y') {
			$this->_go_message($parent_url, "Successfully created {$this->name}.", 'info_message');
		} else {
			$this->_go_message($parent_url, "Error occurred while creating test results.");
		}
	}

	public function edit_action($test_result_id) {
		$input_arr = $this->input->post();
		$model = new Test_results_Model();
		$facility_id = $model->get_test_result($test_result_id)[0]['FACILITY_ID'];
		$update_test_results = $model->update_test_results($test_result_id, $input_arr);
		$result = json_decode($update_test_results, true);
		$parent_url = $this->_parent_url($facility_id);
		if($result['success_flag_outvar'] == 'Y') {
			$this->_go_message($parent_url, "Successfully updated {$this->name}.", 'info_message');
		} else {
			$this->_go_message($parent_url, "Error occurred while updating test results.");
		}
	}

	public function delete_action($test_result_id) {
		$ids = func_get_args();
		$parent_url = $this->_parent_url(NULL, $ids);
		$model = new Test_results_Model();
		$delete_test_result = $model->delete_test_result($test_result_id);

		$result = json_decode($delete_test_result, true);
		if($result['success_flag_outvar'] == 'Y') {
			$this->_go_message($parent_url, "Successfully deleted {$this->name}.", 'info_message');
		} else {
			$this->_go_message($parent_url, "Error occurred while deleting test result.");
		}
	}

	public function add_tester_button($facility_id) {
		$model = new Ref_test_results_testers_Model();
		$url = url::fullpath('test_result_tester/add_tester/' . $facility_id);
		return (Sam::instance()->if_priv($model->table_name, 'INSERT', form::add_button($url, 'Add')));
	}

	public function delete_tester_button($facility_id) {
		$model = new Ref_test_results_testers_Model();
		$url = url::fullpath('test_result_tester/delete_tester/' . $facility_id);
		return (Sam::instance()->if_priv($model->table_name, 'DELETE', form::delete_button($url, 'Delete')));
	}

	public function add_test_company_button($facility_id) {
		$model = new Ref_test_results_test_company_Model();
		$url = url::fullpath('test_result_test_company/add_test_company/' . $facility_id);
		return (Sam::instance()->if_priv($model->table_name, 'INSERT', form::add_button($url, 'Add')));

	}

	public function delete_test_company_button($facility_id) {
		$model = new Ref_test_results_test_company_Model();
		$url = url::fullpath('test_result_test_company/delete_test_company/' . $facility_id);
		return (Sam::instance()->if_priv($model->table_name, 'DELETE', form::delete_button($url, 'Delete')));
	}
}
