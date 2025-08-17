<?php

class Transaction_Controller extends Template_Controller {

	public $name = 'transaction';
	public $model_name = 'Transactions';
	public $prev_name = 'owner';

	public $template = 'tpl_internal';  // default template


	public function __construct() {
		parent::__construct();
		$this->template->nav_id = $this->name;
	}

	public function view($id) {
		$view = new View('transaction_view');

		$view->model = new Transactions_Model();
		$view->row = $view->model->get_row($id);
		if (! $view->row)
                	$this->_go_message($this->_index_url(), 'No Transaction with selected ID exists.');

		$view->inspections = new Inspections_Model();
		$view->inspection_row = $view->inspections->get_row($view->row['INSPECTION_ID']);

		$this->template->content = $view;
	}

	/**
	 * Form for entering in payments made for invoices
	 **/
	public function payment_add($owner_id=NULL) {
		if (($owner_id == NULL) && ($post_id = $this->input->post('owner_id')))
			url::redirect("/transaction/payment_add/{$post_id}");
			//var_dump(url::redirect("/transaction/payment_add/"));

		$view = new View('transaction_payment_add');

		$view->model = new Ref_transaction_Payment_Types_Model();
		$view->payment_types = $view->model->get_payment_types();

		$view->model = new Transactions_Model();
		if (! $view->model->check_priv('INSERT')) $this->_handle_error('Insufficient Privs.');

		if ($owner_id) {
			$view->owner_id = $owner_id;
			$view->invoice_rows = $view->model->get_payable_invoices($owner_id);
		}
		else {
			$view->owner_id = NULL;
			$view->invoice_rows = [];
		}

		$this->template->skip_bread_crumbs = TRUE; // is outside of owner path
		$this->template->content = $view;
	}

	public function payment_add_action($owner_id) {
		if ($this->_model_instance()->payment_insert($owner_id, $this->input->post()))
			$this->_go_message(url::fullpath('/transaction/payment_add/'), "Successfully created payment(s).", 'info_message');
		else
			$this->_go_message(url::fullpath('/transaction/payment_add/'), "Error occurred while creating payment(s).");
	}
}
