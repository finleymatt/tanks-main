<?php


Class Notices_Model extends Model
{
	public $table_name = 'USTX.NOTICES';
	public $pks = array('ID');
	public $parent_pks = array('Operators_mvw' => array('OPERATOR_ID'));

	public $more_select = array("to_char(NOTICE_DATE, 'MM/DD/YYYY') NOTICE_DATE_FMT",
		"to_char(DUE_DATE, 'MM/DD/YYYY') DUE_DATE_FMT",
		"to_char(LETTER_DATE, 'MM/DD/YYYY') LETTER_DATE_FMT");


	public function update($ids, $data) {
		$this->db->set('notice_date', Model::sql_date_db($data['notice_date']));
		//$this->db->set('due_date', Model::sql_date_db($data['due_date']));
		$this->db->set('letter_date', Model::sql_date_db($data['letter_date']));
		$this->db->set('user_modified', Session::instance()->get('UserID'));
		$this->db->set('date_modified', 'sysdate', FALSE);

		return(parent::update($ids, $data));
	}

	/**
 	 * Calls ustx.notice.generate_notice function which creates notice and
 	 * related records.
 	 */
	public function insert($data) {
		$rows = $this->db->query("SELECT ustx.notice.generate_notice(:p_operator_id, :p_notice_code, :p_notice_date, :p_fy) notice_id FROM DUAL",
			array(':p_operator_id' => $data['operator_id'],
				':p_notice_code' => $data['notice_code'],
				':p_notice_date' => Model::str_date_db($data['notice_date']),
				':p_fy' => $data['fy']))->as_array();
		return($rows[0]['NOTICE_ID']);  // 0 if error
	}

	/**
 	 * Delete done through DB function, which will cascade delete related records
 	 */
	public function delete($ids) {
		if (! $this->check_priv('DELETE')) return(FALSE);

		$rows = $this->db->query("SELECT ustx.notice.delete_notice(:notice_id) flag FROM DUAL", array(':notice_id' => $ids[0]))->as_array();
		return($rows[0]['FLAG']);
	}

	protected function _validate_rules($vdata) {
		$vdata->add_rules('operator_id', 'required');
		$vdata->add_rules('notice_code', 'required');
		$vdata->add_rules('notice_date', 'required');
		$vdata->add_rules('fy', 'required');
		$vdata->add_callbacks('operator_id', array(Model::instance('Operators_mvw'), 'is_valid_id'));

		return($vdata);
	}
}
