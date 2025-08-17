<?php

class Facility_Controller extends Template_Controller {

	public $name = 'facility';
	public $model_name = 'Facilities_mvw';
	public $prev_name = NULL;

	public $template = 'tpl_internal';  // default template for all reports


	public function __construct() {
		parent::__construct();
		$this->template->nav_id = $this->name;
	}

	public function index() {
		$view = new View('facility_menu');

		$searched = Session::instance()->get('facility_search');

		$view->facility_id = (isset($searched['facility_id']) ? trim($searched['facility_id']) : '');

		$view->facility_name = (isset($searched['facility_name']) ? $searched['facility_name'] : '');
		$view->street = (isset($searched['street']) ? $searched['street'] : '');
		$view->city = (isset($searched['city']) ? $searched['city'] : '');
		$view->zip = (isset($searched['zip']) ? $searched['zip'] : '');

		$view->tank_id = (isset($searched['tank_id']) ? $searched['tank_id'] : '');

		$this->template->content = $view;
	}

	public function search() {
		$view = new View('facility_listing');
		$view->facility_name = $this->input->post('facility_name');
		$view->street = $this->input->post('street');
		$view->city = $this->input->post('city');
		$view->zip = $this->input->post('zip');
		if (!$view->facility_name && !$view->street && !$view->city && !$view->zip)
			$this->_go_message($this->_index_url(), 'Search requires at least one field.');

		$view->facilities_mvw = new Facilities_mvw_Model();
		$view->rows = $view->facilities_mvw->search(array(
			'FACILITY_NAME' => $view->facility_name,
			'STREET' => $view->street,
			'CITY' => $view->city,
			'ZIP' => $view->zip));

		$this->template->content = $view;

		// save search query to session
		Session::instance()->set('facility_search', $this->input->post());
	}

	public function view($facility_id=NULL) {
		if (($facility_id == NULL) && ($post_id = $this->input->post('facility_id'))) {
			Session::instance()->set('facility_search', $this->input->post());
			url::redirect("/facility/view/{$post_id}");
		}

		$view = new View('facility_view');
		$view->facility_id = $facility_id;
		$view->facilities_mvw = new Facilities_mvw_Model();
		$view->row = $view->facilities_mvw->get_row($view->facility_id);
		if (! $view->row)
			$this->_go_message($this->_index_url(), 'No Facility with selected ID exists.');

		$view->entity_details = new Entity_details_Model();
		$view->assigned_inspector = $view->entity_details->get_assigned_inspector($view->facility_id);

		$view->emails = new Emails_Model();
		$view->email_rows = $view->emails->get_list_by_entity('facility', 'ENTITY_ID = :ENTITY_ID', NULL, array(':ENTITY_ID' => $view->facility_id));

		$view->facility_history = new Facility_history_Model();
		$view->fac_history_rows = $view->facility_history->get_list('FACILITY_ID = :FACILITY_ID', NULL, array(':FACILITY_ID' => $view->facility_id));

		$view->inspections = new Inspections_Model();
		$view->inspection_rows = $view->inspections->get_list('FACILITY_ID = :FACILITY_ID', NULL, array(':FACILITY_ID' => $view->facility_id));

		$view->permits = new Permits_Model();
		$view->permit_rows = $view->permits->get_list('FACILITY_ID = :FACILITY_ID', NULL, array(':FACILITY_ID' => $view->facility_id));

		$view->tanks = new Tanks_Model();
		$view->tank_rows = $view->tanks->get_list('FACILITY_ID = :FACILITY_ID', NULL, array(':FACILITY_ID' => $view->facility_id));

		$view->ab_operator = new Ab_operator_Model();
		$view->ab_op_rows = $view->ab_operator->get_list('FACILITY_ID = :FACILITY_ID', NULL, array(':FACILITY_ID' => $view->facility_id));
	
		$view->retraining = new Retraining_Model();
		$view->retraining_rows = $view->retraining->get_retrainings_by_facility($view->facility_id);

		$view->ab_tank_status = new Ab_tank_status_Model();
		$view->ab_tank_status_rows = $view->ab_tank_status->get_list('FACILITY_ID = :FACILITY_ID', NULL, array(':FACILITY_ID' => $view->facility_id));

		$view->suspected_release = new Suspected_releases_Model();
		$suspected_releases = $view->suspected_release->get_suspected_release_list($view->facility_id);
	
		// combine same suspected releases with different tanks	
		$suspected_releases_combined = array();
		foreach($suspected_releases as $release) {
			// push release ids in an array
			$suspected_release_ids = array();
			foreach($suspected_releases_combined as $release_combine) {
				array_push($suspected_release_ids, $release_combine['SUSPECTED_RELEASE_ID']);
			}
			// if release id is not in the array, add new release
			if(!in_array($release['SUSPECTED_RELEASE_ID'], $suspected_release_ids)) {
				array_push($suspected_releases_combined, $release);
			} else { // if release id is in array, concatenate comma and tank id to the existing release
				$key = array_search($release['SUSPECTED_RELEASE_ID'], $suspected_release_ids);
				$suspected_releases_combined[$key]['TANK_ID'] .= ',' . $release['TANK_ID'];
			}
		}
		
		// add upload url and upload information to suspected release row
		$suspected_release_rows = array();
		foreach($suspected_releases_combined as $suspected_release) {
			// add upload url
			$suspected_release['upload_action'] = url::fullpath('') . 'upload';
			$suspected_release['delete_action'] = url::fullpath('') . 'file_remove';
			// add upload information
			$nfa_letter_form_code = 'NFA_Letter';
			$scsr_letter_form_code ='SCSR_Letter';
			$form_id = $suspected_release['SUSPECTED_RELEASE_ID'];
			$nfa_letter_exist = json_decode($this->file_exist($nfa_letter_form_code, $form_id), true);
			$nfa_letters = $nfa_letter_exist['result']['out_cur'];
			$scsr_letter_exist = json_decode($this->file_exist($scsr_letter_form_code, $form_id), true);
			$scsr_letters = $scsr_letter_exist['result']['out_cur'];
			if($nfa_letter_exist['flag_outvar'] == 'Y') {
				 if(is_null($nfa_letter_exist['msg_outvar'])) {
				 	$nfa_letter_id_arr = array();
					$nfa_letter_url_arr = array();
				 	foreach($nfa_letters as $nfa_letter) {
						array_push($nfa_letter_id_arr, $nfa_letter['ID']);
						array_push($nfa_letter_url_arr, $nfa_letter['UPLOAD_FILEPATH']);
				 		//$suspected_release['nfa_letter_upload_id'] = $nfa_letter_exist['result']['out_cur'][0]['ID'];
						//$suspected_release['nfa_letter_file_path'] = $nfa_letter_exist['result']['out_cur'][0]['UPLOAD_FILEPATH'];
					}
					$suspected_release['nfa_letter_upload_id'] = $nfa_letter_id_arr;
					$suspected_release['nfa_letter_file_path'] = $nfa_letter_url_arr;
				} else {
					$suspected_release['nfa_letter_upload_id'] = '';
					$suspected_release['nfa_letter_file_path'] = '';
				}
			} else {
				echo $nfa_letter_exist['msg_outvar'];
			}
			if($scsr_letter_exist['flag_outvar'] == 'Y') {
				if(is_null($scsr_letter_exist['msg_outvar'])) {
					$scsr_letter_id_arr = array();
					$scsr_letter_url_arr = array();
					foreach($scsr_letters as $scsr_letter) {
						array_push($scsr_letter_id_arr, $scsr_letter['ID']);
						array_push($scsr_letter_url_arr, $scsr_letter['UPLOAD_FILEPATH']);
					}
					$suspected_release['scsr_letter_upload_id'] = $scsr_letter_id_arr;
					$suspected_release['scsr_letter_file_path'] = $scsr_letter_url_arr;
				} else {
					$suspected_release['scsr_letter_upload_id'] = '';
					$suspected_release['scsr_letter_file_path'] = '';
				}
			} else {
				echo $scsr_letter_exist['msg_outvar'];
			}
			array_push($suspected_release_rows, $suspected_release);
		}
		$view->suspected_release_rows = $suspected_release_rows;

		$view->active_penalties = $view->facilities_mvw->get_active_penalties($view->facility_id);

		$view->test_results = new Test_results_Model();
		$test_results = $view->test_results->get_test_result_list($view->facility_id);

		// add upload url and upload information to test results rows
		$test_results_rows = array();

		foreach($test_results as $test_result) {
			// add upload url
			$test_result['upload_action'] = url::fullpath('') . 'upload';
			$test_result['delete_action'] = url::fullpath('') . 'file_remove';
			$inspection_id = $test_result['VIOLATION_INSPECTION_ID'];
			$test_result['INSPECTION_VIEW'] = url::fullpath('') . 'inspection/view/' . $inspection_id;
			$test_result['NOV_LCC'] = is_null($inspection_id) ? '' : $view->test_results->get_nov_lcc_number($inspection_id); 
		
			// add upload information
			$form_code = 'Test_Result';
			$form_id = $test_result['TEST_RESULTS_ID'];
			$file_exist = json_decode($this->file_exist($form_code, $form_id), true);

			if($file_exist['flag_outvar'] == 'Y') {
				if(is_null($file_exist['msg_outvar'])) {
					$test_result['test_upload_id'] = $file_exist['result']['out_cur'][0]['ID'];
					$test_result['file_path'] = $file_exist['result']['out_cur'][0]['UPLOAD_FILEPATH'];
				} else {
					$test_result['test_upload_id'] = '';
					$test_result['file_path'] ='';
				}
			} else {
				echo $file_exist['msg_outvar'];
			}
			array_push($test_results_rows, $test_result);
		}
		$view->test_results_rows = $test_results_rows;	

		$view->upload_action = url::fullpath('') . 'upload';

		$this->template->content = $view;

		// save into recently viewed history
		$this->_save_history(array('type' => 'facility', 'id' => $view->facility_id, 'name' => $view->row['FACILITY_NAME']));
	}

	public function autocomplete() {
		$this->template = new View('tpl_blank');
		$view = new View('autocomplete');

		$facilities_mvw = new Facilities_mvw_Model();
		$bound_vars = array(':term' => strtoupper("%{$this->input->get('term')}%"));
		$view->dropdown_rows = $facilities_mvw->get_dropdown(NULL, NULL, 'id like :term or Upper(facility_name) like :term', $bound_vars);
		$this->template->content = $view;
	}

	public function file_exist($form_code, $form_id, $form_topic='') {
		$url = str_replace('tanks.', '', url::fullpath('')) . 'data/tank/getfileupload';
		$dataArray = array(
			'n_inparm_upload_id' => '',
			'vc_inparm_form_code' => $form_code,
			'n_inparm_form_id' => $form_id,
			'flag_outvar' => '[length=1,type=chr,value=]',
			'msg_outvar' => '[length=500,type=chr,value=]'
		);
		$data = urldecode(http_build_query($dataArray));
		$getUrl = $url . "?" . $data . "&out_cur" ;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $getUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$result = curl_exec($ch);
		curl_close($ch);

		return $result;
	}
}
