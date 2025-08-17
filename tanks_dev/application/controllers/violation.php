<?php

/**
 * Violation controller
 *
 * @package ### file docblock
 * @subpackage controllers
 * @author george.huang
 *
*/

class Violation_Controller extends Template_Controller {

	public $name = 'violation';
	public $model_name = 'Facilities_mvw';
	public $prev_name = NULL;

	public $template = 'tpl_internal';  // default template for all reports

	public function __construct() {
		parent::__construct();
		$this->template->nav_id = $this->name;
	}

	public function index() {
		$view = new View('violation_menu');

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
		$view = new View('violation_listing');
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
			url::redirect("/violation/view/{$post_id}");
		}
		
		$view = new View('violation_view');
		$view->facility_id = $facility_id;
		$view->facilities_mvw = new Facilities_mvw_Model();
		$view->owners_mvw = new Owners_mvw_Model();
		$view->row = $view->facilities_mvw->get_row($view->facility_id);
		$view->owner_row = $view->owners_mvw->get_row($view->row["OWNER_ID"]);
		
		if (! $view->row)
			$this->_go_message($this->_index_url(), 'No Facility with selected ID exists.');

		$view->entity_details = new Entity_details_Model();
		$view->assigned_inspector = $view->entity_details->get_assigned_inspector($view->facility_id);

		$view->emails = new Emails_Model();
		$view->email_rows = $view->emails->get_list_by_entity('facility', 'ENTITY_ID = :ENTITY_ID', NULL, array(':ENTITY_ID' => $view->facility_id));

		$view->facility_history = new Facility_history_Model();
		$view->fac_history_rows = $view->facility_history->get_list('FACILITY_ID = :FACILITY_ID', NULL, array(':FACILITY_ID' => $view->facility_id));

		$view->inspections = new Inspections_Model();
		$inspections = $view->inspections->get_list('FACILITY_ID = :FACILITY_ID', NULL, array(':FACILITY_ID' => $view->facility_id));

		$inspection_ids = array();
		foreach($inspections as $row) {
			array_push($inspection_ids, $row['ID']);
		}

		$inspection_rows = array();
		foreach($inspection_ids as $inspection_id) {
			$inspection_row = $view->inspections->get_inspection_with_penalty_dates($inspection_id);

			if(isset($inspection_row[0])) {
				array_push($inspection_rows, $inspection_row[0]);
			} else {
				$inspection = $view->inspections->get_inspection($inspection_id)[0];
				$inspection['NOD_DATE'] = '';
				$inspection['NOIRT_DATE'] = '';
				$inspection['PENALTY_LEVEL'] = '';
				array_push($inspection_rows, $inspection);	
			}
		}

		$view->inspection_rows = $inspection_rows;
		
		$view->penalties = new Penalties_Model();
		$view->penalty_rows = $view->penalties->get_facility_penalties($inspection_ids);
		$view->penalty_json = json_encode($view->penalty_rows);

		$view->permits = new Permits_Model();
		$view->permit_rows = $view->permits->get_list('FACILITY_ID = :FACILITY_ID', NULL, array(':FACILITY_ID' => $view->facility_id));

		$view->tanks = new Tanks_Model();
		$tanks = $view->tanks->get_list('FACILITY_ID = :FACILITY_ID', NULL, array(':FACILITY_ID' => $view->facility_id));

		$tank_ids = array();
		foreach($tanks as $row) {
			array_push($tank_ids, $row['ID']);
		}

		$tank_rows = array();
		foreach($tank_ids as $tank_id) {
			$tank_row = $view->tanks->get_tank_with_penalty_dates($tank_id);

			if(isset($tank_row[0])) {
				array_push($tank_rows, $tank_row[0]);
			} else {
				$tank = $view->tanks->get_tank($tank_id)[0];
				$tank['NOD_DATE'] = '';
				$tank['NOV_DATE'] = '';
				$tank['REDTAG_PLACED_DATE'] = '';
				$tank['REDTAG_REMOVED_DATE'] = '';
				array_push($tank_rows, $tank);
			}
		}
		
		$view->tank_rows = $tank_rows;	
		$active_tank_rows = array();
		foreach($view->tank_rows as $tank_row) {
			if($tank_row['TANK_STATUS_CODE'] == 1 || $tank_row['TANK_STATUS_CODE'] == 2) {
				array_push($active_tank_rows, $tank_row);
			}
		}
	
		$view->operators_mvw = new Operators_mvw_Model();
		$view->operator_id = $active_tank_rows[0]['OPERATOR_ID'];
		$view->operator_row = $view->operators_mvw->get_row($view->operator_id);

		$view->ab_operator = new Ab_operator_Model();
		$view->ab_op_rows = $view->ab_operator->get_list('FACILITY_ID = :FACILITY_ID', NULL, array(':FACILITY_ID' => $view->facility_id));

		$view->ab_tank_status = new Ab_tank_status_Model();
		$view->ab_tank_status_rows = $view->ab_tank_status->get_list('FACILITY_ID = :FACILITY_ID', NULL, array(':FACILITY_ID' => $view->facility_id));

		$view->active_penalties = $view->facilities_mvw->get_active_penalties($view->facility_id);

		$this->template->content = $view;

		// save into recently viewed history
		$this->_save_history(array('type' => 'facility', 'id' => $view->facility_id, 'name' => $view->row['FACILITY_NAME']));
	}

}
