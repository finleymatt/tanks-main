<?php

class Owner_comments_Controller extends Template_Controller {

	public $name = 'owner_comments';
	public $model_name = 'Owner_comments';
	public $prev_name = 'owner';

	public $template = 'tpl_internal';  // default template

	public function __construct() {
		parent::__construct();
		$this->template->nav_id = $this->name;
	}

	public function view($owner_id, $comment_id=null) {

		$view = new View('owner_comments_view');

		$view->model = new Owner_comments_Model();
		$view->row = $view->model->get_owner_comment($owner_id, $comment_id);
		if (! $view->row)
			$this->_go_message(Controller::_instance('Facility')->_index_url(), 'No Tank with selected ID exists.');
		$this->template->content = $view;
	}

	/*public function edit($owner_id, $comment_id=null) {
		$view = new View('owner_comments_edit');
		$this->view->model = $this->_model_instance();
		$this->view->action = $this->_edit_action_url($ids);
	}*/

	public function delete_action($owner_id) {
		$ids = func_get_args();
		$parent_url = $this->_parent_url(NULL, $ids[0]);

		$model = new Owner_comments_Model();
		$delete_owner_comment = $model->delete($ids);

		$result = json_decode($delete_owner_comment, true);
		if($result['flag_outvar'] == 'Y') {
			$this->_go_message($parent_url, "Successfully deleted {$this->name}.", 'info_message');
		} else {
			$this->_go_message($parent_url, "Error occurred while deleting test result.");
		}	
	}
}
