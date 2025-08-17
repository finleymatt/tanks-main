<?php


Class Facility_history_Model extends Model
{
	public $table_name = 'USTX.FACILITY_HISTORY';
	public $pks = array('FACILITY_ID', 'OWNER_ID', 'FACILITY_HISTORY_CODE', 'FACILITY_HISTORY_DATE');
	public $parent_pks = array('Facilities_mvw' => array('FACILITY_ID'));

	public $more_select = array("to_char(FACILITY_HISTORY_DATE, 'MM/DD/YYYY') FACILITY_HISTORY_DATE_FMT");


	public function update($ids, $data) {
		$this->db->set('facility_history_date', Model::sql_date_db($data['facility_history_date']), FALSE);

		return(parent::update($ids, $data));
	}

	public function insert($parent_ids, $data) {
		$this->db->set('facility_id', $parent_ids[0]);
		$this->db->set('facility_history_date', Model::sql_date_db($data['facility_history_date']), FALSE);

		return(parent::insert($parent_ids, $data));
	}

	protected function _validate_rules($vdata) {
		$vdata->add_rules('owner_id', 'required', 'numeric');
		$vdata->add_rules('facility_history_date', 'required');
		$vdata->add_rules('facility_history_code', 'required');
		$vdata->add_callbacks('owner_id', array(Model::instance('Owners_mvw'), 'is_valid_id'));

		return($vdata);
	}
}
