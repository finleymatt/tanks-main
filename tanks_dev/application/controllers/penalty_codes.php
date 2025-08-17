<?php

class Penalty_codes_Controller extends Template_Controller {
	public $name = 'users';
	public $model_name = 'Penalty_codes';
	public $prev_name = NULL;

	public $template = 'tpl_internal';


	public function __construct() {
		parent::__construct();
		$this->template->nav_id = $this->name;
	}

	public function index() {
		$view = new View('penalty_codes_menu');

		$view->model = new Penalty_codes_Model();
		$view->rows = $view->model->get_list(NULL, 'END_DATE, CODE');

		$this->template->content = $view;
	}

}
