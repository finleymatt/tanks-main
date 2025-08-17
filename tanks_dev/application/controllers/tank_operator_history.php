<?php

class Tank_operator_history_Controller extends Template_Controller {

	public $name =  'tank_operator_history';
	public $prev_name = 'tank';
	public $model_name = 'Tank_operator_history';
	public $template = 'tpl_internal';  // default template for all reports


	public function __construct() {
		parent::__construct();
		$this->template->nav_id = $this->name;
	}

}
