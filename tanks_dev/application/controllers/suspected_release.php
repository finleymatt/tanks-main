<?php

class Suspected_release_Controller extends Template_Controller {

	public $name =  'suspected_release';
	public $model_name = 'Suspected_releases';
	public $prev_name = 'facility';

	public $template = 'tpl_internal';  // default template for all reports


	public function __construct() {
		parent::__construct();
		$this->template->nav_id = $this->name;
	}

	public function view($id=NULL) {
		$view = new View('suspected_release_view');

		$view->model = new Suspected_releases_Model();
		$rows = $view->model->get_suspected_release($id);
		$suspected_release = $rows[0];
		$suspected_release_ids = array();
		foreach($rows as $row) {
			array_push($suspected_release_ids, $row['TANK_ID']);
		}
		$suspected_release['TANK_ID_STRING'] = implode(",", $suspected_release_ids);
		$view->row = $suspected_release;
		if (! $view->row)
                	$this->_go_message(Controller::_instance('Facility')->_index_url(), 'No Tank with selected ID exists.');

		$this->template->content = $view;
	}

	// override add function in MY_Controller.php of libraries
	public function add($facility_id) {
		$parent_ids = func_get_args();

		$this->view = new View("{$this->name}_edit");
		$this->view->is_add = TRUE;
		$this->view->model = $this->_model_instance();
		$this->view->action = $this->_add_action_url($parent_ids);
		$this->view->parent_ids = $parent_ids;
		$this->view->row = NULL;

		$this->template->content = $this->view;
	}

	public function edit($facility_id) {
		$ids = func_get_args();

		$this->view = new View("{$this->name}_edit");
		$this->view->is_add = FALSE;
		$this->view->model = $this->_model_instance();
		$this->view->action = $this->_edit_action_url($ids);
		$this->view->row = $this->view->model->get_row($ids);
		$suspected_release_id = $this->view->row['ID'];
		$tank_ids = array();
		$tanks = $this->view->model->get_suspected_release_tank($suspected_release_id);
		foreach($tanks as $tank) {
			array_push($tank_ids, $tank['TANK_ID']);
		}
		$this->view->release_tank_ids = $tank_ids;
		if (! $this->view->row)
			$this->_go_message($this->_error_url(), "No {$this->name} with selected ID exists.");
		$this->template->content = $this->view;
	}

	public function add_action($facility_id) {
		$input_arr = $this->input->post();

		$model = new Suspected_Releases_Model();
		$add_suspected_releases = $model->add_suspected_releases($facility_id, $input_arr);
		$result = json_decode($add_suspected_releases, true);

		$parent_url = $this->_parent_url($facility_id);
		if($result['flag_outvar'] == 'Y') {
			$this->_go_message($parent_url, "Successfully created {$this->name}.", 'info_message');
		} else {
			$this->_go_message($parent_url, "Error occurred while creating suspected releases.");
		}
	}

	public function edit_action($suspected_release_id) {
		$input_arr = $this->input->post();
		$model = new Suspected_releases_Model();
		$facility_id = $model->get_suspected_release($suspected_release_id)[0]['FACILITY_ID'];
		$update_suspected_release = $model->update_suspected_release($facility_id, $suspected_release_id, $input_arr);
		//$result = json_decode($update_suspected_release, true);
		$parent_url = $this->_parent_url($facility_id);
		if($update_suspected_release['flag_outvar'] == 'Y') {
			$this->_go_message($parent_url, "Successfully updated {$this->name}.", 'info_message');
		} else {
			$this->_go_message($parent_url, "Error occurred while updating suspected release.");
		}
	}

	public function delete_action($suspected_release_id) {
		$ids = func_get_args();
		$parent_url = $this->_parent_url(NULL, $ids);
		$model = new Suspected_releases_Model();
		$delete_suspected_release = $model->delete_suspected_release($suspected_release_id);

		$result = json_decode($delete_suspected_release, true);
		if($result['flag_outvar'] == 'Y') {
			$this->_go_message($parent_url, "Successfully deleted {$this->name}.", 'info_message');
		} else {
			$this->_go_message($parent_url, "Error occurred while deleting suspected release.");
		}
	}
}
