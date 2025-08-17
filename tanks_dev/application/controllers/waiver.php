<?php

class Waiver_Controller extends Template_Controller {

	public $name = 'waiver';
	public $model_name = 'Owner_waivers';
	public $prev_name = 'owner';

	public $template = 'tpl_internal';


	public function __construct() {
		parent::__construct();
		$this->template->nav_id = $this->name;
	}

	/**
 	 * Workaround: Set owner_id to field so validation on uniqueness can work
 	 */
	public function add_action($parent_id) {
		$parent_ids = func_get_args();
		$_POST['owner_id'] = $parent_ids[0];

		return(parent::add_action($parent_id));
	}
}
