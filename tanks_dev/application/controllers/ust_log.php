<?php

class Ust_log_Controller extends Template_Controller {

	public $name =  'ust_log';
	public $prev_name = NULL;
	public $model_name = 'Ust_log';

	public $template = 'tpl_internal';  // default template for all reports


	public function __construct() {
		parent::__construct();
		$this->template->nav_id = $this->name;
	}

	public function invoice_status() {
		$this->template = new View('tpl_blank');
		$view = new View('invoice_status_ajax');

		$view->ust_log = new Ust_log_Model();
		$view->latest_status = $view->ust_log->latest_invoice();

		$this->template->content = $view;
	}

	public function permit_status() {
		$this->template = new View('tpl_blank');
		$view = new View('permit_status_ajax');

		$view->ust_log = new Ust_log_Model();
		$view->latest_status = $view->ust_log->permit_print_status();

		$this->template->content = $view;
	}


	public function ab_operator_status() {
		$this->template = new View('tpl_blank');
		$view = new View('ab_operator_status_ajax');

		$view->ust_log = new Ust_log_Model();
		$view->latest_status = $view->ust_log->ab_operator_print_status();

		$this->template->content = $view;
	}
}
