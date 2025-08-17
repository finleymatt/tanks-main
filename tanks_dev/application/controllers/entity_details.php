<?php
/**
 * Entity Details Controller
 *
 * <p>Entity Details model is is similar to Emails model in that it's used
 * for owner, facility, and operator.
 * Entity type is first determined by each of the CRUD methods.
 * It is then stored in $this->prev_name, and also forwarded on to the
 * Entity_details_Model.</p>
 *
 * @package ### file docblock
 * @subpackage controllers
 *
 */

class Entity_details_Controller extends Template_Controller {

	public $name = 'entity_details';
	public $label = NULL;
	public $model_name = 'Entity_details';
	public $prev_name = NULL;  // set in each function as: owner, facility
				   // same as entity_type
	public $detail_type = NULL;

	public $template = 'tpl_popup';


	public function __construct()
	{
		parent::__construct();
		$this->template->nav_id = $this->name;
	}

	/**
	 * Sets $this->prev_name to one of the possible entity types.
	 */
	public function set_prev_name($prev_name) {
		if (in_array($prev_name, Entity_details_Model::$ENTITY_TYPES))
			$this->prev_name = $prev_name;
		else
			$this->_go_message($this->_error_url(), "{$prev_name} is unknown.");
	}

	/**
 	 * Calls set_prev_name after finding the entity_type from the DB record
 	 */
	public function set_prev_name_id($id) {
		$row = $this->_model_instance()->get_row($id);
		$this->set_prev_name($row['ENTITY_TYPE']);
		$this->label = ucfirst(strtr(($this->detail_type = $row['DETAIL_TYPE']), array('_'=>' ')));
	}

	public function add($id) {
		$parent_ids = func_get_args();
		$this->detail_type = end($parent_ids);

		$this->view = new View("{$this->detail_type}_edit");
		$this->view->is_add = TRUE;
		$this->view->model = $this->_model_instance();
		$this->view->action = $this->_add_action_url($parent_ids);
		$this->view->parent_ids = $parent_ids;
		$this->view->row = NULL;

		$this->template->content = $this->view;
	}

	public function edit($id) {
		$this->set_prev_name_id($id);

		$this->view = new View("{$this->detail_type}_edit");
		$this->view->is_add = FALSE;
		$this->view->model = $this->_model_instance();
		$this->view->action = $this->_edit_action_url($id);
		$this->view->row = $this->view->model->get_row($id);
		if (! $this->view->row)
			exit("No {$this->name} with selected ID exists.");

		$this->template->content = $this->view;
	}

	public function edit_action($id) {
		$this->set_prev_name_id($id);
		return(parent::edit_action($id));
	}

	public function add_with_detail($parent_id, $entity_type, $detail_type) {
		$this->set_prev_name($entity_type);
		$this->detail_type = $detail_type;

		$this->view = new View("{$this->detail_type}_edit");
		$this->view->is_add = TRUE;
		$this->view->model = $this->_model_instance();
		$this->view->parent_ids = func_get_args();
		$this->view->action = $this->_add_action_url($this->view->parent_ids);
		$this->view->row = NULL;

		$this->template->content = $this->view;
	}

	public function add_action_detail($parent_id, $entity_type, $detail_type) {
		$this->set_prev_name($entity_type);
		return(parent::add_action($parent_id, $entity_type, $detail_type));
	}

	public function delete($id) {
		$this->set_prev_name_id($id);
		return(parent::delete($id));
	}

	public function delete_action($id) {
		$this->set_prev_name_id($id);
		return(parent::delete_action($id));
	}

	/**
	 * overwritten to specify the entity type of model: owner, facility
	 */
	public function _model_instance() {
		static $instance;

		if ($this->prev_name == NULL)
			return(parent::_model_instance());

		if (empty($instance[$this->prev_name])) {
			$instance[$this->prev_name] = new Entity_details_Model($this->prev_name);
		}

		return $instance[$this->prev_name];
	}

	public function _add_edit_button($detail_id, $entity_id, $entity_type, $detail_type) {
		return("<div class='popup' style='float:right'>"
			. ($detail_id
				? Controller::_instance('Entity_details')->_edit_button($detail_id)
				: Controller::_instance('Entity_details')->_add_button(array($entity_id, $entity_type, $detail_type), 'add'))
			. "</div>");
	}
}
