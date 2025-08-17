<?php

class Financial_provider_Controller extends Template_Controller {

	public $name =  'financial_provider';
	public $model_name = 'Financial_providers';
	public $prev_name = 'owner';

	public $template = 'tpl_internal';  // default template for all reports


	public function __construct() {
		parent::__construct();
		$this->template->nav_id = $this->name;
	}

	// add financial providers, override add function in MY_Controller.php of libraries
	public function add($owner_id) {
		$this->view = new View("financial_provider_add");
		$model = new Financial_providers_Model();
		$this->view->code = $model->get_next_code();
		$this->view->action = url::fullpath('/financial_provider/add_action/' . $owner_id);
		$this->template->content = $this->view;
	}

	// go to delete financial provider view
	public function delete($owner_id) {
		$this->view = new View('financial_provider_delete');
		$this->view->model = $this->_model_instance();
		
		$this->view->financial_providers = Model::instance('Financial_providers');
		$this->view->financial_provider_rows = $this->view->financial_providers->get_list(NULL, 'CODE');
		$this->view->action = url::fullpath('/financial_provider/delete_action/' . $owner_id);

		$this->template->content = $this->view;
	}

	// delete selected financial providers
	public function delete_action($owner_id) {
		$parent_ids = func_get_args();
		$model = $this->_model_instance();
		$inputs = $this->input->post()['financial_provider'];

		// delete all selected financial providers
		if ($model->delete($inputs))
			$this->_go_message($this->_parent_url($parent_ids), "Successfully created {$this->name}.", 'info_message');
		else
			$this->_go_message($this->_parent_url($parent_ids), "Error occurred while creating {$this->name}.");
	}

	public function add_financial_provider_button($owner_id) {
		$model = new Ref_test_results_testers_Model();
		$url = url::fullpath('financial_provider/add/' . $owner_id);
		return form::add_button($url, 'Add');
	}

	public function delete_financial_provider_button($owner_id) {
		$model = new Ref_test_results_testers_Model();
		$url = url::fullpath('financial_provider/delete/' . $owner_id);
		return form::delete_button($url, 'Delete');
	}
}
