<?php


Class Tanks_Model extends Model
{
	const TANK_TYPE_AST = 'A';
	const TANK_TYPE_UST = 'U';

	public static $tank_types = array('A' => 'AST', 'U' => 'UST');

	public $table_name = 'USTX.TANKS';
	public $pks = array('ID');
	public $parent_pks = array('Facilities_mvw' => array('FACILITY_ID'));

	public $lookup_code = 'ID';
	public $lookup_desc = "'ID:' || ID || ', Type:' || TANK_TYPE || ', Capacity:' || CAPACITY || ', Status:' || (select description from ustx.tank_status_codes where code = tank_status_code)";

	public $more_select = array("to_char(STATUS_DATE, 'MM/DD/YYYY') STATUS_DATE_FMT");


	public function insert($parent_ids, $data) {
		$this->db->set('id', 'ustx.tank_seq.NEXTVAL', FALSE);
		$this->db->set('facility_id', $parent_ids[0]);
		$this->db->set('status_date', Model::sql_date_db($data['status_date']), FALSE);
		$this->db->set('user_created', Session::instance()->get('UserID'));
		$this->db->set('date_created', 'sysdate', FALSE);
		// owner_id will come from $data

		return(parent::insert($parent_ids, $data));
	}

	public function update($ids, $data) {
		$this->db->set('status_date', Model::sql_date_db($data['status_date']), FALSE);
		$this->db->set('user_modified', Session::instance()->get('UserID'));
		$this->db->set('date_modified', 'sysdate', FALSE);

		return (parent::update($ids, $data));
	}

	public function get_tank_with_penalty_dates($tank_id) {
		$tank = $this->db->query('
		SELECT T.*, P.NOV_DATE, P.NOD_DATE, P.REDTAG_PLACED_DATE, P.REDTAG_REMOVED_DATE, PC.PENALTY_LEVEL
		FROM USTX.tanks T
		JOIN (SELECT * FROM USTX.penalties WHERE ROWNUM <= 1 AND tank_id = :TANK_ID) P
		ON T.ID = P.TANK_ID
		JOIN USTX.penalty_codes PC
		ON P.penalty_code = PC.code
		WHERE T.id = :TANK_ID' 
		, array(':TANK_ID' => $tank_id))->as_array();
		return $tank;
	}

	public function get_tank($tank_id) {
		$tank = $this->db->query('
		SELECT * FROM USTX.tanks
		WHERE ID = :TANK_ID'
		, array(':TANK_ID' => $tank_id))->as_array();

		return $tank;
	}

	protected function _validate_rules($vdata) {
		$vdata->add_rules('owner_id', 'required');
		$vdata->add_rules('tank_type', 'required');
		$vdata->add_rules('tank_status_code', 'required');
		$vdata->add_callbacks('owner_id', array(Model::instance('Owners_mvw'), 'is_valid_id'));

		return($vdata);
	}

	public function get_tanks_with_penalty_dates($tank_id) {
		$tanks = $this->db->query('
		SELECT TANK_ID, NOV_DATE, NOD_DATE, REDTAG_PLACED_DATE, REDTAG_REMOVED_DATE FROM USTX.PENALTIES
		WHERE NOD_DATE IS NOT NULL
		AND TANK_ID = :TANK_ID
		', array(':TANK_ID' => $tank_id))->as_array();
		
		return $tanks;
	}
}
