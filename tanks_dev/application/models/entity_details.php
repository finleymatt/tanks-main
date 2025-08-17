<?php
/**
 * Entity Details Model
 *
 * <p>Entity Details model is is similar to Emails model in that it's used
 * for owner, facility, and operator.
 * For parent_url to work, $this->parent_pks must be set directly or by __construct.
 * For listing view, this is not necessary, and get_list_by_entity() can be used.
 * </p>
 *
 * @subpackage models
 *
 */

Class Entity_details_Model extends Model
{
	public static $ENTITY_TYPES = array('owner', 'facility', 'operator');

	public $table_name = 'USTX.ENTITY_DETAILS';
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
			case 'operator':
				$this->parent_pks = array('Operators_mvw' => array('ENTITY_ID'));
				break;
		}
	}

	public function get_list_by_entity($entity_type=NULL, $where=NULL, $orderby=NULL, $bound_vars=array()) {
		if ($entity_type)
			$this->db->where('ENTITY_TYPE', $entity_type);

		return(parent::get_list($where, $orderby, $bound_vars));
	}

	public function get_assigned_inspector($entity_id) {
		$rows = $this->db->query("
			SELECT E.*, (S.FIRST_NAME || ' ' || S.LAST_NAME) FULL_NAME
			FROM ustx.entity_details E
				inner join ustx.staff S on E.DETAIL_VALUE = S.SEP_LOGIN_ID
			WHERE E.entity_type = 'facility' and E.detail_type = 'assigned_inspector'
				and E.ENTITY_ID = :entity_id"
			, array(':entity_id' => $entity_id))->as_array();

		return($rows ? $rows[0] : NULL);
	}

	public function update($ids, $data) {
		$this->db->set('user_modified', Session::instance()->get('UserID'));
		// date_modified handled by trigger

		return(parent::update($ids, $data));
	}

	public function insert($parent_ids, $data) {
		$this->db->set('entity_id', $parent_ids[0]);
		$this->db->set('entity_type', $parent_ids[1]);
		$this->db->set('detail_type', $parent_ids[2]);
		$this->db->set('user_created', Session::instance()->get('UserID'));
		// id and date_created handled by trigger

		return(parent::insert($parent_ids, $data));
	}

	protected function _validate_rules($vdata) {
		return($vdata);
	}
}
