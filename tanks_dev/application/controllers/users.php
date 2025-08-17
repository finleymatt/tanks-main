<?php

class Users_Controller extends Template_Controller {
	public $name = 'users';

	public $template = 'tpl_internal';  // default template for all reports


	public function __construct() {
		parent::__construct();
		$this->template->nav_id = $this->name;
	}

	public function index() {
		$view = new View('users_menu');

		$view->model = new Staff_Model();
		$view->rows = $view->model->get_list(NULL, 'LAST_NAME, FIRST_NAME');

		$this->template->content = $view;
	}

}
