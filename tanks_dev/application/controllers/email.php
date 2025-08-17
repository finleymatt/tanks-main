<?php
/**
 * Email Controller
 *
 * <p>Email controller is more involved than other controllers due to the fact
 * that one Email table is being used for owner, facility, and possibly
 * operator.  Entity type is first determined by each of the CRUD methods.
 * It is then stored in $this->prev_name, and also forwarded on to the
 * Emails_Model.</p>
 *
 * @package ### file docblock
 * @subpackage controllers
 *
 */

class Email_Controller extends Template_Controller {

	public $name = 'email';
	public $model_name = 'Emails';
	public $prev_name = NULL;  // set in each function as: owner, facility
				   // this method however results in no breadcrumbs

	public $template = 'tpl_internal';


	public function __construct()
	{
		parent::__construct();
		$this->template->nav_id = $this->name;
	}

	/**
	 * Sets $this->prev_name to one of the possible entity types.
	 */
	public function set_prev_name($prev_name) {
		if (in_array($prev_name, Emails_Model::$ENTITY_TYPES))
			$this->prev_name = $prev_name;
		else
			$this->_go_message($this->_error_url(), "{$prev_name} is unknown.");
	}

	/**
 	 * Calls set_prev_name after finding the entity_type from the DB record
 	 */
	public function set_prev_name_id($id) {
		$email_row = $this->_model_instance()->get_row($id);
		$this->set_prev_name($email_row['ENTITY_TYPE']);
	}

	public function edit($id) {
		$this->set_prev_name_id($id);
		return(parent::edit($id));
	}

	public function edit_action($id) {
		$this->set_prev_name_id($id);
		return(parent::edit_action($id));
	}

	public function add($parent_id, $entity_type) {
		$this->set_prev_name($entity_type);
		return(parent::add($parent_id, $entity_type));
	}

	public function add_action($parent_id, $entity_type) {
		$this->set_prev_name($entity_type);
		return(parent::add_action($parent_id, $entity_type));
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
	 * overwritten to specify the entity type of email model: owner, facility
	 */
	public function _model_instance() {
		static $instance;

		if ($this->prev_name == NULL)
			return(parent::_model_instance());

		if (empty($instance[$this->prev_name])) {
			$instance[$this->prev_name] = new Emails_Model($this->prev_name);
		}

		return $instance[$this->prev_name];
	}
}
