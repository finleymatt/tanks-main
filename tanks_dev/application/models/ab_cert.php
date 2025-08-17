<?php


Class Ab_cert_Model extends Model
{
	const CERT_MONTHS = 60;

	// cert level C is maintained at the facility level
	public static $cert_level_types = array('A/B'=>'A/B', 'A'=>'A', 'B'=>'B', 'C'=>'C', 'A/B and C'=>'A/B and C');

	public $table_name = 'USTX.AB_CERT';
	public $pks = array('ID');
	public $parent_pks = array('Ab_operator' => array('AB_OPERATOR_ID'));

	public $more_select = array("to_char(CERT_DATE, 'MM/DD/YYYY') CERT_DATE_FMT");


	public function update($ids, $data) {
		$this->db->set('cert_date', Model::sql_date_db($data['cert_date']), FALSE);
		$this->db->set('user_modified', Session::instance()->get('UserID'));
		$this->db->set('date_modified', 'sysdate', FALSE);

		return(parent::update($ids, $data));
	}

	public function insert($parent_ids, $data) {
		$this->db->set('id', 'ustx.ab_cert_seq.NEXTVAL', FALSE);
		$this->db->set('ab_operator_id', $parent_ids[0]); // other ids will come from $data
		$this->db->set('cert_date', Model::sql_date_db($data['cert_date']), FALSE);

		$this->db->set('user_created', Session::instance()->get('UserID'));
		$this->db->set('date_created', 'sysdate', FALSE);

		return(parent::insert($parent_ids, $data));
	}

	public function delete_op($parent_ids) {
		if (! $this->check_priv('DELETE')) return(FALSE);

		assert(isset($parent_ids[0]));
		$this->db->set('user_deleted', Session::instance()->get('UserID'));
		$this->db->set('date_deleted', 'sysdate', FALSE);

		$this->db->where(text::where_pk('AB_OPERATOR_ID', $parent_ids));
		return($this->db->delete($this->table_name));
	}
}
