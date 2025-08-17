<?php

class Invoice_codes_Controller extends Template_Controller {

	public $name = 'invoice_codes';
	public $model_name = 'Invoice_codes';
	public $prev_name = NULL;

	public $template = 'tpl_internal';  // default template

	public function __construct() {
		parent::__construct();
		$this->template->nav_id = $this->name;
	}

	public function index($id=NULL) {
		$view = new View('invoice_codes_listing');

		$view->invoice_codes = new Invoice_codes_Model();
		$view->rows = $view->invoice_codes->get_list();

		$this->template->content = $view;
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
