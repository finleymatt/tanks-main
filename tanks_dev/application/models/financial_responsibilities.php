<?php


Class Financial_responsibilities_Model extends Model
{
	public $table_name = 'USTX.FINANCIAL_RESPONSIBILITIES';
	public $pks = array('ID');
	public $parent_pks = array('Owners_mvw' => array('OWNER_ID'));

	public $more_select = array("to_char(BEGIN_DATE, 'MM/DD/YYYY') BEGIN_DATE_FMT",
		"to_char(END_DATE, 'MM/DD/YYYY') END_DATE_FMT",
		"to_char(NON_COMPL_REMINDER_LETTER_DATE, 'MM/DD/YYYY') REMINDER_DATE_FMT",);


	public function update($ids, $data) {
		// exchange financial method/provider description to financial method/provider code
		$fin_prov_code = Model::instance('Financial_providers')->get_financial_provider_code($data['fin_prov_code']);
		$fin_method_code = Model::instance('Financial_methods')->get_financial_method_code($data['fin_meth_code']);
		$data['fin_prov_code'] = $fin_prov_code;
		$data['fin_meth_code'] = $fin_method_code;

		if (isset($data['begin_date']))
			$this->db->set('begin_date', Model::sql_date_db($data['begin_date']), FALSE);
		if (isset($data['end_date']))
			$this->db->set('end_date', Model::sql_date_db($data['end_date']), FALSE);
		if (isset($data['non_compl_reminder_letter_date']))
			$this->db->set('non_compl_reminder_letter_date', Model::sql_date_db($data['non_compl_reminder_letter_date']), FALSE);
		$this->db->set('user_modified', Session::instance()->get('UserID'));
		$this->db->set('date_modified', 'sysdate', FALSE);

		return(parent::update($ids, $data));
	}

	public function insert($parent_ids, $data) {
		$this->db->set('id', 'ustx.fin_resp_seq.NEXTVAL', FALSE);
		$this->db->set('owner_id', $parent_ids[0]);
		// exchange financial provider description to financial provider code
		$fin_prov_code = Model::instance('Financial_providers')->get_financial_provider_code($data['fin_prov_code']);
		$fin_method_code = Model::instance('Financial_methods')->get_financial_method_code($data['fin_meth_code']);
		$data['fin_prov_code'] = $fin_prov_code;
		$data['fin_meth_code'] = $fin_method_code;

		if (isset($data['begin_date']))
			$this->db->set('begin_date', Model::sql_date_db($data['begin_date']), FALSE);
		if (isset($data['end_date']))
			$this->db->set('end_date', Model::sql_date_db($data['end_date']), FALSE);
		if (isset($data['non_compl_reminder_letter_date']))
			$this->db->set('non_compl_reminder_letter_date', Model::sql_date_db($data['non_compl_reminder_letter_date']), FALSE);
		$this->db->set('user_created', Session::instance()->get('UserID'));
		$this->db->set('date_created', 'sysdate', FALSE);

		return(parent::insert($parent_ids, $data));
	}

	protected function _validate_rules($vdata) {
		$vdata->add_rules('amount', 'numeric');
		$vdata->add_callbacks('facility_id', array(Model::instance('Facilities_mvw'), 'is_valid_id'));
		return($vdata);
	}
}
