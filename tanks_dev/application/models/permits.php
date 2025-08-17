<?php


Class Permits_Model extends Model
{
	public $table_name = 'USTX.PERMITS';
	public $pks = array('FACILITY_ID', 'OWNER_ID', 'FISCAL_YEAR');
	public $parent_pks = array('Facilities_mvw' => array('FACILITY_ID'),
		'Owners_mvw' => array('OWNER_ID'));


	public function update($ids, $data) {
		if (isset($data['date_permitted']))
			$this->db->set('date_permitted', Model::sql_date_db($data['date_permitted']), FALSE);
		if (isset($data['date_printed']))
			$this->db->set('date_printed', Model::sql_date_db($data['date_printed']), FALSE);

		return(parent::update($ids, $data));
	}

	public function insert($parent_ids, $data) {
		$this->db->set('id', 'ustx.fin_resp_seq.NEXTVAL', FALSE);
		$this->db->set('facility_id', $parent_ids[0]);
		$this->db->set('owner_id', $parent_ids[1]);
		if (isset($data['begin_date']))
			$this->db->set('begin_date', Model::sql_date_db($data['begin_date']), FALSE);
		if (isset($data['end_date']))
			$this->db->set('end_date', Model::sql_date_db($data['end_date']), FALSE);

		return(parent::insert($parent_ids, $data));
	}

	public function print_batch($owner_id, $facility_id, $date_permitted, $fy) {
		$user_id = Session::instance()->get('UserID');
		$this->batch('certificate', array($owner_id, $facility_id, $date_permitted, $fy, $user_id));
	}

	public function print_all_batch($fy) {
		$this->batch('certificate', array(0, 0, 0, $fy)); // 0 for owner_id, facility_id, date_permitted
	}

	protected function _validate_rules($vdata) {
		$vdata->add_rules('fiscal_year', 'required', 'numeric');
		return($vdata);
	}
}
