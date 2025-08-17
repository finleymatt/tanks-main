<?php


Class Inspections_Model extends Model
{
	public $table_name = 'USTX.INSPECTIONS';
	public $pks = array('ID');
	public $parent_pks = array('Facilities_mvw' => array('FACILITY_ID'));

	//used short key, COMPLIANCE_ISSUE_DATE_FMT, since column name length limit
	public $more_select = array("to_char(DATE_INSPECTED, 'MM/DD/YYYY') DATE_INSPECTED_FMT",
		"to_char(COMPLIANCE_DATE, 'MM/DD/YYYY') COMPLIANCE_DATE_FMT",
		"to_char(COMPLIANCE_ORDER_ISSUE_DATE, 'MM/DD/YYYY') COMPLIANCE_ISSUE_DATE_FMT",
		"to_char(COMPLIANCE_SUBMIT_DATE, 'MM/DD/YYYY') COMPLIANCE_SUBMIT_DATE_FMT");


	public function update($ids, $data) {
		$this->db->set('date_inspected', Model::sql_date_db($data['date_inspected']), FALSE);
		$this->db->set('compliance_date', Model::sql_date_db($data['compliance_date']), FALSE);
		$this->db->set('compliance_order_issue_date', Model::sql_date_db($data['compliance_order_issue_date']), FALSE);
		$this->db->set('compliance_submit_date', Model::sql_date_db($data['compliance_submit_date']), FALSE);
		$this->db->set('user_modified', Session::instance()->get('UserID'));
		$this->db->set('date_modified', 'sysdate', FALSE);

		return(parent::update($ids, $data));
	}

	public function insert($parent_ids, $data) {
		$this->db->set('id', 'ustx.inspection_seq.NEXTVAL', FALSE);
		$this->db->set('facility_id', $parent_ids[0]);
		$this->db->set('date_inspected', Model::sql_date_db($data['date_inspected']), FALSE);
		$this->db->set('compliance_date', Model::sql_date_db($data['compliance_date']), FALSE);
		$this->db->set('compliance_order_issue_date', Model::sql_date_db($data['compliance_order_issue_date']), FALSE);
		$this->db->set('compliance_submit_date', Model::sql_date_db($data['compliance_submit_date']), FALSE);
		$this->db->set('user_created', Session::instance()->get('UserID'));
		$this->db->set('date_created', 'sysdate', FALSE);

		return(parent::insert($parent_ids, $data));
	}

	public function delete($ids) {
		if (! $this->check_priv('DELETE')) return(FALSE);
		$user_id = Session::instance()->get('UserID');		
		
		return($this->db->procedure_call("BEGIN ustx.d_inspection(:p_inspection_id, :p_user_id); END;", array(':p_inspection_id' => $ids[0], ':p_user_id' => $user_id)));  // procedure handles errors itself
	}

	public function get_inspections_by_facility($facility_id) {
		$domain = str_replace('tanks.', '', url::fullpath(''));
		$url = $domain . 'data/facility/getinspection?n_inparm_inspection_id=0&n_inparm_facility_id=' . $facility_id
		. '&flag_outvar=[length=1,type=chr,value=]&msg_outvar=[length=500,type=chr,value=]&out_cur';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);	
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);  // HOW LONG TO WAIT FOR A RESPONSE
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$results = curl_exec($ch);
		curl_close($ch);

		$result = json_decode($results, true)['result']['out_cur'];
		return $result;
	}

	public function get_inspection_with_penalty_dates($inspection_id) {
		$inspection = $this->db->query('
		SELECT I.*, P.NOD_DATE, P.NOIRT_DATE, PC.penalty_level FROM USTX.inspections I
		JOIN (SELECT * FROM USTX.penalties WHERE ROWNUM <= 1 AND inspection_id = :INSPECTION_ID) P
		on I.id = P.inspection_id
		JOIN USTX.penalty_codes PC
		on P.penalty_code = PC.code
		WHERE I.id = :INSPECTION_ID'
		, array(':INSPECTION_ID' => $inspection_id))->as_array();

		return $inspection;
	}

	public function get_inspection($inspection_id) {
		$inspection = $this->db->query('
		SELECT * FROM USTX.inspections
		WHERE ID = :INSPECTION_ID'
		, array(':INSPECTION_ID' => $inspection_id))->as_array();

		return $inspection;
	}

	protected function _validate_rules($vdata) {
		$vdata->add_rules('inspection_code', 'required', 'numeric');
		$vdata->add_rules('staff_code', 'required');

		return($vdata);
	}
}
