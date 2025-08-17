<?php

class Facility_history_Controller extends Template_Controller {

	public $name = 'facility_history';
	public $model_name = 'Facility_history';
	public $prev_name = 'facility';
	public $template = 'tpl_internal';  // default template


	public function __construct() {
		parent::__construct();
		$this->template->nav_id = $this->name;
	}

}
