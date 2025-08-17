<?php


Class Tank_operator_history_Model extends Model
{
	public $table_name = 'USTX.TANK_OPERATOR_HISTORY';
	public $pks = array('TANK_ID', 'OPERATOR_ID', 'START_DATE'); // END_DATE is nullable
	public $parent_pks = array('Tanks' => array('TANK_ID'));

	public $more_select = array("to_char(START_DATE, 'MM/DD/YYYY') START_DATE_FMT",
		"to_char(END_DATE, 'MM/DD/YYYY') END_DATE_FMT",
		"to_char(START_DATE, 'DD-MON-YYYY') START_DATE_KEY"); // used in pk


	public function update($ids, $data) {
		$this->db->set('start_date', Model::sql_date_db($data['start_date']), FALSE);
		$this->db->set('end_date', Model::sql_date_db($data['end_date']), FALSE);
		$this->db->set('user_modified', Session::instance()->get('UserID'));
		$this->db->set('date_modified', 'sysdate', FALSE);

		return(parent::update($ids, $data));
	}

	public function insert($parent_ids, $data) {
		$this->db->set('tank_id', $parent_ids[0]);
		$this->db->set('start_date', Model::sql_date_db($data['start_date']));
		$this->db->set('end_date', Model::sql_date_db($data['end_date']));
		$this->db->set('user_created', Session::instance()->get('UserID'));
		$this->db->set('date_created', 'sysdate', FALSE);

		return(parent::insert($parent_ids, $data));
	}

	protected function _validate_rules($vdata) {
		$vdata->add_rules('operator_id', 'required');
		$vdata->add_rules('start_date', 'required');
		$vdata->add_callbacks('operator_id', array(Model::instance('Operators_mvw'), 'is_valid_id'));

		return($vdata);
	}

}
