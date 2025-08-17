<?php

class Notice_Controller extends Template_Controller {

	public $name = 'notice';
	public $model_name = 'Notices';
	public $prev_name = 'operator';

	public $template = 'tpl_internal';  // default template for all reports


	public function __construct() {
		parent::__construct();
		$this->template->nav_id = $this->name;
	}

	public function index() {
		global $GLOBAL_INI;
		$view = new View('notice_menu');

		// auto-populate previous entered fields --------------
		$searched = Session::instance()->get('notice_search');
		$view->notice_id = (isset($searched['notice_id']) ? $searched['notice_id'] : '');

		$entered = Session::instance()->get('notice_batch');
		$view->operator_id = (isset($entered['operator_id']) ? $entered['operator_id'] : '');
		$view->notice_code = (isset($entered['notice_code']) ? $entered['notice_code'] : '');
		$view->fy = (isset($entered['fy']) ? $entered['fy'] : '');
		$view->notice_date = (isset($entered['notice_date']) ? $entered['notice_date'] : date('m/d/Y'));
		$view->model = new Notices_Model();

		$this->template->skip_bread_crumbs = TRUE; // is outside of owner path
		$this->template->content = $view;
	}

	public function view($notice_id=NULL) {
		if (($notice_id == NULL) && ($post_id = $this->input->post('notice_id'))) {
			Session::instance()->set('notice_search', $this->input->post());
			url::redirect("/notice/view/{$post_id}");
		}

		$view = new View('notice_view');
		$view->notice_id = $notice_id;

		$view->model = new Notices_Model();
		$view->row = $view->model->get_row($view->notice_id);
		if (! $view->row)
                	$this->_go_message($this->_index_url(), 'No Notice with selected ID exists.');

		$this->template->content = $view;
	}

	public function add_action() {
		$model = $this->_model_instance();
		if ($notice_id = $model->insert($this->input->post()))
			$this->print_file($notice_id);
		else
			$this->_go_message($this->_parent_url(), "Error occurred while creating {$this->name}.");
	}

	public function print_file($notice_id) {
		require_once Kohana::find_file('vendor', 'reports/Report', TRUE);
		require_once Kohana::find_file('vendor', 'reports/tcpdf_old/tcpdf', TRUE);
		$this->template = new View('tpl_blank');
		$view = new View('reports/notice_print');
		$view->notice_row = $this->_model_instance()->get_row($notice_id);
		$view->notice_code_row = Model::instance('Invoice_codes')->get_row($view->notice_row['NOTICE_CODE']);
		$view->operator_row = Model::instance('Operators_mvw')->get_row($view->notice_row['OPERATOR_ID']);
		$this->template->content = $view;
	}
}
