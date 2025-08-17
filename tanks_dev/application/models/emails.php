<?php
/**
 * Email Model
 *
 * <p>Email model is more involved than other models due to the fact
 * that one Email table is being used for owner, facility, and possibly
 * operator.
 * For parent_url to work, $this->parent_pks must be set directly or by __construct.
 * For listing view, this is not necessary, and get_list_by_entity() can be used.
 * </p>
 *
 * @subpackage models
 *
 */

Class Emails_Model extends Model
{
	public static $ENTITY_TYPES = array('owner', 'facility');

	public $table_name = 'USTX.EMAILS';
	public $pks = array('ID');
	public $parent_pks = NULL;  // will be set in constuctor


	public function __construct($entity_type=NULL) {
		parent::__construct();
		switch($entity_type) {
			case 'owner':
				$this->parent_pks = array('Owners_mvw' => array('ENTITY_ID'));
				break;
			case 'facility':
				$this->parent_pks = array('Facilities_mvw' => array('ENTITY_ID'));
				break;
		}
	}

	public function get_list_by_entity($entity_type=NULL, $where=NULL, $orderby=NULL, $bound_vars=array()) {
		if ($entity_type)
			$this->db->where('ENTITY_TYPE', $entity_type);

		return(parent::get_list($where, $orderby, $bound_vars));
	}

	public function update($ids, $data) {
		$this->db->set('user_modified', Session::instance()->get('UserID'));
		// date_modified handled by trigger

		return(parent::update($ids, $data));
	}

	public function insert($parent_ids, $data) {
		$this->db->set('entity_id', $parent_ids[0]);
		$this->db->set('entity_type', $parent_ids[1]);
		$this->db->set('user_created', Session::instance()->get('UserID'));
		// id and date_created handled by trigger

		return(parent::insert($parent_ids, $data));
	}

	protected function _validate_rules($vdata) {
		return($vdata);
	}
}
