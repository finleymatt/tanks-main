<?php

class Operator_Controller extends Template_Controller {

	public $name = 'operator';
	public $model_name = 'Operators_mvw';
	public $prev_name = NULL;

	public $template = 'tpl_internal';  // default template

	public function __construct() {
		parent::__construct();
		$this->template->nav_id = $this->name;
	}

	public function index($id=NULL) {
		$view = new View('operator_menu');

		$searched = Session::instance()->get('operator_search');

		$view->operator_id = (isset($searched['operator_id']) ? $searched['operator_id'] : '');
		$view->operator_name = (isset($searched['operator_name']) ? $searched['operator_name'] : '');
		$view->street = (isset($searched['street']) ? $searched['street'] : '');
		$view->city = (isset($searched['city']) ? $searched['city'] : '');
		$view->zip = (isset($searched['zip']) ? $searched['zip'] : '');

		$this->template->content = $view;
	}

	public function search() {
		$view = new View('operator_listing');
		$view->operator_name = $this->input->post('operator_name');
		$view->street = $this->input->post('street');
		$view->city = $this->input->post('city');
		$view->zip = $this->input->post('zip');
		if (!$view->operator_name && !$view->street && !$view->city && !$view->zip)
			$this->_go_message($this->_index_url(), 'Search requires at least one field.');

		$view->operators_mvw = new Operators_mvw_Model();
		$view->rows = $view->operators_mvw->search(array(
			'OPERATOR_NAME' => $view->operator_name,
			'STREET' => $view->street,
			'CITY' => $view->city,
			'ZIP' => $view->zip));

		$this->template->content = $view;

		// save search query to session
		Session::instance()->set('operator_search', $this->input->post());
	}

	public function view($operator_id=NULL) {
		if (($operator_id == NULL) && ($post_id = $this->input->post('operator_id'))) {
			Session::instance()->set('operator_search', $this->input->post());
			url::redirect("/operator/view/{$post_id}");
		}

		$view = new View('operator_view');
		$view->operator_id = $operator_id;
		$view->operators_mvw = new Operators_mvw_Model();
		$view->row = $view->operators_mvw->get_row($view->operator_id);
		if (! $view->row)
                	$this->_go_message($this->_index_url(), 'No Operator with selected ID exists.');

		$view->facility_rows = $view->operators_mvw->get_facilities($view->operator_id);

		$view->notices = new Notices_Model();
		$view->notice_rows = $view->notices->get_list("OPERATOR_ID = '{$view->operator_id}'");

		$this->template->content = $view;

		// save into recently viewed history
		$this->_save_history(array('type' => 'operator', 'id' => $view->operator_id, 'name' => $view->row['OPERATOR_NAME']));
	}

	public function autocomplete() {
		$this->template = new View('tpl_blank');
		$view = new View('autocomplete');

		$bound_vars = array(':term' => strtoupper("%{$this->input->get('term')}%"));
		$operators_mvw = new Operators_mvw_Model();
		$view->dropdown_rows = $operators_mvw->get_dropdown(NULL, NULL, 'Upper(id) like :term or Upper(operator_name) like :term', $bound_vars);
		$this->template->content = $view;
	}
}
