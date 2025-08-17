<?php


Class Ab_tank_status_Model extends Model
{
	public $table_name = 'USTX.AB_TANK_STATUS';
	public $pks = array('ID');
	public $parent_pks = array('Facilities_mvw' => array('FACILITY_ID'));

	public $more_select = array("to_char(TANK_LAST_USED, 'MM/DD/YYYY') TANK_LAST_USED_FMT");


	public function insert($parent_ids, $data) {
		$this->db->set('id', 'ustx.ab_operator_seq.NEXTVAL', FALSE);
		$this->db->set('facility_id', $parent_ids[0]);
		$this->db->set('tank_last_used', Model::sql_date_db($data['tank_last_used']), FALSE);
		$this->db->set('user_created', Session::instance()->get('UserID'));
		$this->db->set('date_created', 'sysdate', FALSE);

		return(parent::insert($parent_ids, $data));
	}

	public function update($ids, $data) {
		$this->db->set('tank_last_used', Model::sql_date_db($data['tank_last_used']), FALSE);
		$this->db->set('user_modified', Session::instance()->get('UserID'));
		$this->db->set('date_modified', 'sysdate', FALSE);

		return(parent::update($ids, $data));
	}

}
