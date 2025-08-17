<?php
/**
 * Test result test company Controller
 *
 * @package ### file docblock
 * @subpackage controllers
 *
 */

class Test_result_test_company_Controller extends Template_Controller {
	public $name = 'test_result_test_company';
	public $model_name = 'Ref_test_results_test_company';
	public $template = 'tpl_internal';

	public function add_test_company($facility_id) {
		$this->view = new View("test_company_add");
		$this->view->action = url::fullpath('/test_result_test_company/add_test_company_action/' . $facility_id);
		$this->template->content = $this->view;
	}

	public function delete_test_company($facility_id) {
		$this->view = new View("test_company_delete");
		$this->view->action = url::fullpath('/test_result_test_company/delete_test_company_action/' . $facility_id);
		$this->template->content = $this->view;
	}

	public function add_test_company_action($facility_id) {
		$company_name = $this->input->post();
		$model = $this->_model_instance();
		$add_test_company = $model->add_test_company($company_name);
		$result = json_decode($add_test_company, true);

		$parent_url = url::fullpath('/test_result/add/' . $facility_id);
		if($result['success_flag_outvar'] == 'Y') {
			$this->_go_message($parent_url, "Successfully created {$this->name}.", 'info_message');
		} else {
			$this->_go_message($parent_url, "Error occurred while creating test company.");
		}
	}

	public function delete_test_company_action($facility_id) {
		$input_arr = $this->input->post();
		$all_ids = array();
		foreach($input_arr as $input_name => $input) {
			if($input_name == 'test_result_test_company') {
				$all_ids = array_merge($all_ids, $input);
			}
		}

		$model = $this->_model_instance();
		$delete_test_companies = $model->delete_test_companies($all_ids);
		$result = json_decode($delete_test_companies, true);

		$parent_url = url::fullpath('/test_result/add/' . $facility_id);
		if($result['success_flag_outvar'] == 'Y') {
			$this->_go_message($parent_url, "Successfully deleted {$this->name}.", 'info_message');
		} else {
			$this->_go_message($parent_url, "Error occurred while deleting test_companies.");
		}
	}
}
