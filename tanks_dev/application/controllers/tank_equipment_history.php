<?php

class Tank_equipment_history_Controller extends Template_Controller {

	public $name =  'tank_equipment_history';
	public $prev_name = 'tank';
	public $model_name = 'Tank_equipment_history';
	public $template = 'tpl_internal';  // default template for all reports


	public function __construct() {
		parent::__construct();
		$this->template->nav_id = $this->name;
	}

}
