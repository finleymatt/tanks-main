<?php

Class Tank_equipment_history_Model extends Model
{
	public $table_name = 'USTX.TANK_EQUIPMENT_HISTORY';
	public $pks = array('TANK_ID', 'HISTORY', 'HISTORY_DATE');
        public $parent_pks = array('Tanks' => array('TANK_ID'));
	public $more_select = array("to_char(HISTORY_DATE, 'MM/DD/YYYY') HISTORY_DATE_FMT");

	/*
	 * Insert tank equipment history
	 */
	public function insert($parent_ids, $data) {
		$this->db->set('tank_id', $parent_ids[0]);
		$this->db->set('tank_detail_code', $data['tank_detail_code']);
		$this->db->set('history', $data['history']);
		$this->db->set('user_created', Session::instance()->get('UserID'));
		$this->db->set('date_created', 'sysdate', FALSE);
		$this->db->set('history_date', Model::sql_date_db($data['history_date']), FALSE);	

		return(parent::insert($parent_ids, $data));
	}

	/*
	 * Update tank equipment history
	 */
	public function update($ids, $data) {
		$this->db->set('tank_detail_code', $data['tank_detail_code']);
		$this->db->set('history', $data['history']);
		$this->db->set('history_date', Model::sql_date_db($data['history_date']), FALSE);
		$this->db->set('user_modified', Session::instance()->get('UserID'));
		$this->db->set('date_modified', 'sysdate', FALSE);

		return(parent::update($ids, $data));	
	}
}
