<?php

class Tank_Controller extends Template_Controller {

	public $name =  'tank';
	public $model_name = 'Tanks';
	public $prev_name = 'facility';

	public $template = 'tpl_internal';  // default template for all reports


	public function __construct() {
		parent::__construct();
		$this->template->nav_id = $this->name;
	}

	public function view($id=NULL) {
		// if arrived here by tank ID search
		if (($id == NULL) && ($post_id = $this->input->post('tank_id'))) {
			Session::instance()->set('facility_search', $this->input->post());
			url::redirect("/tank/view/{$post_id}");
		}

		$view = new View('tank_view');

		$view->model = new Tanks_Model();
		$view->row = $view->model->get_row($id);
		if (! $view->row)
                	$this->_go_message(Controller::_instance('Facility')->_index_url(), 'No Tank with selected ID exists.');

		$view->tank_details = new Tank_details_Model();
		$view->tank_detail_rows = $view->tank_details->get_list('TANK_ID = :TANK_ID', NULL, array(':TANK_ID' => $id));

		$view->tank_history= new Tank_history_Model();
		$view->tank_history_rows = $view->tank_history->get_list('TANK_ID = :TANK_ID', NULL, array(':TANK_ID' => $id));

		$view->tank_operator_history = new Tank_operator_history_Model();
		$view->tank_operator_history_rows = $view->tank_operator_history->get_list('TANK_ID = :TANK_ID', NULL, array(':TANK_ID' => $id));

		$view->tank_equipment_history = new Tank_equipment_history_Model();
		$view->tank_equipment_history_rows = $view->tank_equipment_history->get_list('TANK_ID = :TANK_ID', NULL, array(':TANK_ID' => $id));

		$this->template->content = $view;
	}

}
