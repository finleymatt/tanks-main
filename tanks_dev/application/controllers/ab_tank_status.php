<?php

class Ab_tank_status_Controller extends Template_Controller {

	public $name = 'ab_tank_status';
	public $prev_name = 'facility';

	public $label = 'A/B/C Tank Status';

	public $model_name = 'Ab_tank_status';

	public $template = 'tpl_internal';  // default template for all reports


	public function __construct() {
		parent::__construct();
		$this->template->nav_id = $this->name;
	}

}
