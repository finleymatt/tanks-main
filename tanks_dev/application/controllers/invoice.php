<?php

class Invoice_Controller extends Template_Controller {

	public $name = 'invoice';
	public $model_name = 'Invoices';
	public $prev_name = 'owner';

	public $template = 'tpl_internal';  // default template for all reports


	public function __construct() {
		parent::__construct();
		$this->template->nav_id = $this->name;
	}

	public function index() {
		global $GLOBAL_INI;
		$view = new View('invoice_menu');

		// auto-populate previous entered fields --------------
		$searched = Session::instance()->get('invoice_search');
		$view->invoice_id = (isset($searched['invoice_id']) ? $searched['invoice_id'] : '');

		$entered = Session::instance()->get('invoice_batch');
		$view->owner_id = (isset($entered['owner_id']) ? $entered['owner_id'] : '');
		$view->fy = (isset($entered['fy']) ? $entered['fy'] : '');
		$view->invoice_date = (isset($entered['invoice_date']) ? $entered['invoice_date'] : date('m/d/Y'));
		$view->due_date = (isset($entered['due_date']) ? $entered['due_date'] : '');
		$view->model = new Invoices_Model();

		$view->fy_int_rates = Model::instance('Fiscal_years')->get_int_rates();

		$this->template->skip_bread_crumbs = TRUE; // is outside of owner path
		$this->template->content = $view;
	}

	public function batch_action() {
		// save entered fields to session first
		Session::instance()->set('invoice_batch', $this->input->post());

		// abort if another batch process is already running
		if (Model::instance('Ust_log')->is_batch_running())
			$this->_go_message($this->_index_url(), 'Request not accepted due to another batch process already running. Please wait for the previous request to finish or contact the helpdesk.', 'error_message');

		$owner_id = $this->input->post('owner_id', 0);
		$owner_id = (empty($owner_id) ? 0 : $owner_id); // 0 for all owners
		$fy = $this->input->post('fy');
		$invoice_date = Model::str_date_db($this->input->post('invoice_date'));
		$due_date = Model::str_date_db($this->input->post('due_date'));
		$print_opt = $this->input->post('print_option', 'all_fys');

		$model = new Invoices_Model();
		switch($this->input->post('action')) {
			case ('gen'):
				$model->generate($owner_id, $fy, $invoice_date, $due_date);
				break;
			case ('print'):
				$model->print_report($owner_id, $fy, $invoice_date, $due_date, $print_opt);
				break;
			case ('gen-print'):
				$model->gen_print($owner_id, $fy, $invoice_date, $due_date, $print_opt);
				break;
			default: $this->_go_message($this->_index_url(), "Unknown batch action: {$this->input->post('action')}");
		}
		$this->_go_message($this->_index_url(), "Invoice batch started", 'info_message');
	}

	public function view($invoice_id=NULL) {
		if (($invoice_id == NULL) && ($post_id = $this->input->post('invoice_id'))) {
			Session::instance()->set('invoice_search', $this->input->post());
			url::redirect("/invoice/view/{$post_id}");
		}

		$view = new View('invoice_view');
		$view->invoice_id = $invoice_id;

		$view->model = new Invoices_Model();
		$view->row = $view->model->get_row($view->invoice_id);
		if (! $view->row)
                	$this->_go_message($this->_index_url(), 'No Invoice with selected ID exists.');

		$view->invoice_detail = new Invoice_detail_Model();
		$view->invoice_detail_rows = $view->invoice_detail->get_list('INVOICE_ID = :INVOICE_ID', 'FISCAL_YEAR', array(':INVOICE_ID' => $view->invoice_id));

		$view->idf = new Invoice_detail_facilities_Model();
		$view->facility_rows = $view->idf->get_list('INVOICE_ID = :INVOICE_ID', 'FACILITY_ID', array(':INVOICE_ID' => $view->invoice_id));

		$this->template->content = $view;

		// save into recently viewed history
		$this->_save_history(array('type' => 'invoice', 'id' => $view->invoice_id, 'name' => $view->invoice_row['INVOICE_DATE']));
	}

	protected function _delete_pre_render() {
		$this->view->note = 'Deleting an Invoice also deletes all its associated transactions.';
	}

	public function print_file($invoice_id) {
		if ($this->_model_instance()->is_gpa($invoice_id)) // GPA invoice has diff output
			return($this->print_gpa_file($invoice_id));

		require_once Kohana::find_file('vendor', 'reports/Report', TRUE);
		require_once Kohana::find_file('vendor', 'reports/tcpdf_old/tcpdf', TRUE);
		require_once Kohana::find_file('vendor', 'onestop_batch/invoice_print', TRUE);

		if (print_invoice($invoice_id, 'single', FALSE))
			$this->template->content = '';  // if success, no output--pdf download
		else
			$this->_go_message($this->_view_url($invoice_id), 'Selected invoice does not result in a PDF being generated.');
	}

	public function reset_status() {
		$ust_log = new Ust_log_Model();
		$ust_log->reset_invoice();

               	$this->_go_message($this->_index_url(), 'Invoice Batch Status has been reset.', 'info_message');
	}

	// GPA invoice features ===============================================
	
	public function gpa_menu($owner_id=NULL) {
		if (($owner_id == NULL) && ($post_id = $this->input->post('owner_id')))
			url::redirect("/invoice/gpa_menu/{$post_id}");

		$view = new View('gpa_invoice_menu');
		$view->model = new Invoices_Model();
		if ($owner_id) {
			$view->is_valid_owner = (Model::instance('Owners_mvw')->get_row($owner_id) != NULL);
			$view->owner_id = $owner_id;
			$view->invoice_rows = $view->model->get_gpa_invoices($owner_id);
		}
		else {
			$view->owner_id = NULL;
			$view->invoice_rows = NULL;
		}

		$this->template->skip_bread_crumbs = TRUE; // is outside of owner path
		$this->template->content = $view;
	}

	public function gpa_add_action($owner_id) {
		// insert GPA invoice and transaction
		if ($invoice_id = $this->_model_instance()->gpa_insert($this->input->post()))
			$this->print_file($invoice_id);
		else
			$this->_go_message(url::fullpath('/invoice/gpa_menu/', $owner_id),
				"Error occurred while creating GPA invoice.");
	}

	public function print_gpa_file($invoice_id) {
		require_once Kohana::find_file('vendor', 'reports/Report', TRUE);
		require_once Kohana::find_file('vendor', 'reports/tcpdf_old/tcpdf', TRUE);

		$this->template = new View('tpl_blank');
		$view = new View('reports/gpa_invoice_print');
		$view->invoice_id = $invoice_id;
		$this->template->content = $view;
	}
}

