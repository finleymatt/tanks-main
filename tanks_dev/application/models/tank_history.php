<?php


Class Tank_history_Model extends Model
{
	public $table_name = 'USTX.TANK_HISTORY';
	public $pks = array('TANK_ID', 'OWNER_ID', 'HISTORY_DATE', 'HISTORY_CODE');
	public $parent_pks = array('Tanks' => array('TANK_ID'));

	public $more_select = array("to_char(HISTORY_DATE, 'MM/DD/YYYY') HISTORY_DATE_FMT",
		"to_char(HISTORY_DATE, 'DD-MON-YYYY') HISTORY_DATE_KEY"); // used in pk


	public function update($ids, $data) {
		$this->db->set('history_date', Model::sql_date_db($data['history_date']), FALSE);
		$this->db->set('user_modified', Session::instance()->get('UserID'));
		$this->db->set('date_modified', 'sysdate', FALSE);

		return(parent::update($ids, $data));
	}

	public function insert($parent_ids, $data) {
		$this->db->set('tank_id', $parent_ids[0]);
		$this->db->set('history_date', Model::sql_date_db($data['history_date']));
		$this->db->set('user_created', Session::instance()->get('UserID'));
		$this->db->set('date_created', 'sysdate', FALSE);

		return(parent::insert($parent_ids, $data));
	}

	protected function _validate_rules($vdata) {
		$vdata->add_rules('owner_id', 'required', 'numeric');
		$vdata->add_rules('history_date', 'required');
		$vdata->add_rules('history_code', 'required');
		$vdata->add_callbacks('owner_id', array(Model::instance('Owners_mvw'), 'is_valid_id'));

		return($vdata);
	}
}
