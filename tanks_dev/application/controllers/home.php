<?php


class Home_Controller extends Template_Controller {
	public $name = 'home';

	public $template = 'tpl_internal';

	public function __construct() {
		parent::__construct();
		$this->template->nav_id = $this->name;
	}

	public function index() {
		$view = new View('home_menu');
		$this->template->content = $view;
	}
}
