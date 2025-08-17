<?php

class Owner_Controller extends Template_Controller {

	public $name = 'owner';
	public $model_name = 'Owners_mvw';
	public $prev_name = NULL;

	public $template = 'tpl_internal';  // default template for all reports


	public function __construct() {
		parent::__construct();
		$this->template->nav_id = $this->name;
	}

	public function index() {
		$view = new View('owner_menu');

		$searched = Session::instance()->get('owner_search');
		$view->owner_id = (isset($searched['owner_id']) ? trim($searched['owner_id']) : '');
		$view->owner_name = (isset($searched['owner_name']) ? $searched['owner_name'] : '');
		$view->street = (isset($searched['street']) ? $searched['street'] : '');
		$view->city = (isset($searched['city']) ? $searched['city'] : '');
		$view->zip = (isset($searched['zip']) ? $searched['zip'] : '');
		$view->state = (isset($searched['state']) ? $searched['state'] : '');

		$this->template->content = $view;
	}
        

	public function search() {
		$view = new View('owner_listing');
		$view->owner_name = $this->input->post('owner_name');
		$view->street = $this->input->post('street');
		$view->city = $this->input->post('city');
		$view->zip = $this->input->post('zip');
		$view->state = $this->input->post('state');
		if (!$view->owner_name && !$view->street && !$view->city && !$view->zip)
			$this->_go_message($this->_index_url(), 'Search requires at least one field. (not State)');

		$view->owners_mvw = new Owners_mvw_Model();
		$view->rows = $view->owners_mvw->search(array(
			'OWNER_NAME' => $view->owner_name,
			'STREET' => $view->street,
			'CITY' => $view->city,
			'ZIP' => $view->zip,
			'STATE' => $view->state));

		$this->template->content = $view;

		// save search query to session
		Session::instance()->set('owner_search', $this->input->post());
	}

	public function view($owner_id=NULL) {
		if (($owner_id == NULL) && ($post_id = $this->input->post('owner_id'))) {
	                Session::instance()->set('owner_search', $this->input->post());
			url::redirect("/owner/view/{$post_id}");
		}

		$view = new View('owner_view');
		$view->owner_id = $owner_id;

		$view->owners_mvw = new Owners_mvw_Model();
		$view->row = $view->owners_mvw->get_row($view->owner_id);
		if (! $view->row)
			$this->_go_message($this->_index_url(), 'No Owner with selected ID exists.');

		$view->emails = new Emails_Model();
		$view->email_rows = $view->emails->get_list_by_entity('owner', 'ENTITY_ID = :ENTITY_ID', NULL, array(':ENTITY_ID' => $view->owner_id));

		$view->financial_responsibilities = new Financial_responsibilities_Model();
		//$view->insurance_rows = $view->financial_responsibilities->get_list('OWNER_ID = :OWNER_ID', NULL, array(':OWNER_ID' => $view->owner_id));
		$insurances = $view->financial_responsibilities->get_list('OWNER_ID = :OWNER_ID', NULL, array(':OWNER_ID' => $view->owner_id));

		// add upload url and upload information to insurances
		$insurance_rows = array();

		foreach($insurances as $insurance) {
			// add upload url
			$insurance['upload_action'] = url::fullpath('') . 'upload';
			$insurance['delete_action'] = url::fullpath('') . 'file_remove';
			// add upload information
			$form_code = 'Insurance';
			$form_id = $insurance['ID'];
			$file_exist = json_decode($this->file_exist($form_code, $form_id), true);
			if($file_exist['flag_outvar'] == 'Y') {
				if(is_null($file_exist['msg_outvar'])) {
					$insurance['upload_id'] = $file_exist['result']['out_cur'][0]['ID'];
					$insurance['file_path'] = $file_exist['result']['out_cur'][0]['UPLOAD_FILEPATH'];
				} else {
					$insurance['upload_id'] = '';
					$insurance['file_path'] = '';
				}
			} else {
				echo $file_exist['msg_outvar'];
			}
			array_push($insurance_rows, $insurance);
		}
		$view->insurance_rows = $insurance_rows;
		$view->upload_action = url::fullpath('') . 'upload';

		$view->owner_waivers = new Owner_waivers_Model();
		$view->waiver_rows = $view->owner_waivers->get_list('OWNER_ID = :OWNER_ID', NULL, array(':OWNER_ID' => $view->owner_id));

		$view->transactions = new Transactions_Model();
		$view->transaction_rows = $view->transactions->get_list('OWNER_ID = :OWNER_ID', NULL, array(':OWNER_ID' => $view->owner_id));

		$view->owner_comments = new Owner_comments_Model();
		$view->comment_rows = $view->owner_comments->get_list('OWNER_ID = :OWNER_ID', NULL,  array(':OWNER_ID' => $view->owner_id));

		$view->facilities_mvw = new Facilities_mvw_Model();
		$view->facility_rows = $view->facilities_mvw->get_list('OWNER_ID = :OWNER_ID', NULL, array(':OWNER_ID' => $view->owner_id));

		$view->invoices = new Invoices_Model();
		$view->invoice_rows = $view->invoices->get_list('OWNER_ID = :OWNER_ID', NULL, array(':OWNER_ID' => $view->owner_id));

		$view->tank_rows = $view->owners_mvw->get_owner_tanks($view->owner_id);
		//$ll = $view->invoices->get_list('OWNER_ID = :OWNER_ID', NULL, array(':OWNER_ID' => $view->owner_id));
		//$view->invoice_rows = $ll; 
		//print_r($ll);
		//$view->invoice_rows = array(); 

		$this->template->content = $view;

		// stats
		$view->stats_active_facs = Model::instance('Facilities_mvw')->get_value("SELECT COUNT(*) AS VAL FROM ustx.facilities_mvw F WHERE F.owner_id = :OWNER_ID AND (select count(*) from ustx.tanks T where T.facility_id = F.id and T.tank_status_code = 1) > 0", array(':OWNER_ID' => $view->owner_id));
		$view->stats_inactive_facs = Model::instance('Facilities_mvw')->get_value("SELECT COUNT(*) AS VAL FROM ustx.facilities_mvw F WHERE F.owner_id = :OWNER_ID AND (select count(*) from ustx.tanks T where T.facility_id = F.id and T.tank_status_code = 1) = 0", array(':OWNER_ID' => $view->owner_id));
		$view->stats_ast_facs = Model::instance('Facilities_mvw')->get_value("SELECT COUNT(*) AS VAL FROM USTX.FACILITIES_MVW F WHERE F.ID IN (select facility_id from ustx.tanks where tank_type='A' and owner_id=:OWNER_ID) AND F.OWNER_ID = :OWNER_ID", array(':OWNER_ID' => $view->owner_id));
		$view->stats_ust_facs = Model::instance('Facilities_mvw')->get_value("SELECT COUNT(*) AS VAL FROM USTX.FACILITIES_MVW F WHERE F.ID IN (select facility_id from ustx.tanks where tank_type='U' and owner_id=:OWNER_ID) AND F.OWNER_ID = :OWNER_ID", array(':OWNER_ID' => $view->owner_id));
		$view->stats_total_tanks = Model::instance('Tanks')->get_value('SELECT COUNT(*) AS VAL FROM USTX.TANKS WHERE OWNER_ID = :OWNER_ID', array(':OWNER_ID' => $view->owner_id));
		$view->stats_tos_tanks = Model::instance('Tanks')->get_value('SELECT COUNT(*) AS VAL FROM USTX.TANKS WHERE OWNER_ID = :OWNER_ID AND TANK_STATUS_CODE = 2', array(':OWNER_ID' => $view->owner_id));
		$view->stats_active_tanks = Model::instance('Tanks')->get_value('SELECT COUNT(*) AS VAL FROM USTX.TANKS WHERE OWNER_ID = :OWNER_ID AND TANK_STATUS_CODE = 1', array(':OWNER_ID' => $view->owner_id));
		$view->balance_summary = $view->owners_mvw->get_balance_summary($view->owner_id);

		// save into recently viewed history
		$this->_save_history(array('type' => 'owner', 'id' => $view->owner_id, 'name' => $view->row['OWNER_NAME']));
	}

	public function waive_all($owner_id) {
		$view = new View('owner_waive_all');
		$view->owner_id = $owner_id;
		$view->action = $this->_crud_url($owner_id, 'waive_all_action');

		$view->model = new Owners_mvw_Model();
		$view->owner = $view->model->get_row($owner_id);
		if (! $view->owner)
                	$this->_go_message($this->_index_url(), 'No Owner with selected ID exists.');

		$this->template->content = $view;
	}

	public function waive_all_action($owner_id) {
		$view = new View('owner_waive_all_action');
		$view->owner_id = $owner_id;
		$view->reason = $this->input->post('reason');

		$view->model = new Owners_mvw_Model();
		$view->owner = $view->model->get_row($owner_id);
		$view->waivers = $view->model->waive_all($owner_id, $view->reason);

		$this->template->content = $view;
	}

	public function autocomplete() {
		$this->template = new View('tpl_blank');
		$view = new View('autocomplete');

		$owners_mvw = new Owners_mvw_Model();
		$bound_vars = array(':term' => strtoupper("%{$this->input->get('term')}%"));
		$view->dropdown_rows = $owners_mvw->get_dropdown(NULL, NULL, 'id like :term or Upper(owner_name) like :term', $bound_vars);
		$this->template->content = $view;
	}

	public function file_exist($form_code, $form_id) {
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
