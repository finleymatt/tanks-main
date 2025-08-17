<?php


Class Ab_operator_Model extends Model
{
	public $table_name = 'USTX.AB_OPERATOR';
	public $pks = array('ID');
	public $parent_pks = array('Facilities_mvw' => array('FACILITY_ID'));

	public $more_select = array("(FIRST_NAME || ' ' || LAST_NAME) FULL_NAME");
	public $lookup_code = 'ID';
	public $lookup_desc = "FIRST_NAME || ' ' || LAST_NAME";

	/**
	 * Overridden to include row metadata and to return inserted ID
	 */
	public function insert($parent_ids, $data) {
		$new_id = $this->db->query_field('select ustx.ab_operator_seq.NEXTVAL from dual');
		$this->db->set('id', $new_id);
		$this->db->set('facility_id', $parent_ids[0]);
		$this->db->set('user_created', Session::instance()->get('UserID'));
		$this->db->set('date_created', 'sysdate', FALSE);

		if (parent::insert($parent_ids, $data))
			return($new_id);
		else
			return(FALSE);
	}

	public function update($ids, $data) {
		$this->db->set('user_modified', Session::instance()->get('UserID'));
		$this->db->set('date_modified', 'sysdate', FALSE);

		return(parent::update($ids, $data));
	}

	/**
	 * Overridden to delete its children certificates as well
	 */
	public function delete($ids) {
		if (! $this->check_priv('DELETE')) return(FALSE);

		Model::instance('Ab_cert')->delete_op($ids);

		return(parent::delete($ids));
	}

	public function print_all_batch() {
		$this->batch('abop_letter', array()); // no args
	}

	/**
 	 * Returns current effective cert level for the requested $ab_operator_id
 	 * Note: Oracle aggregate version of this function could not be created
 	 */
	public function get_effective_cert_level($ab_operator_id) {
		$rows = $this->db->query('
			SELECT DISTINCT cert_level FROM ustx.ab_cert
			WHERE ab_operator_id = :AB_OPERATOR_ID
				AND cert_date >= add_months(sysdate, -:MONTHS)'
		, array(':AB_OPERATOR_ID' => $ab_operator_id, ':MONTHS' => Ab_cert_Model::CERT_MONTHS))->as_array();

		$certs = array();
		foreach($rows as $row) {
			$certs[] = $row['CERT_LEVEL'];
		}

		if (in_array('A/B', $certs) ||
			(in_array('A', $certs) && in_array('B', $certs)))
			return('A/B');
		elseif (count($certs))
			return($certs[0]);
		else
			return(NULL);
	}

	/**
	 * Returns cert level for the requested $ab_operator_id, even if it's expired
	 */
	public function get_cert_level($ab_operator_id) {
		$rows = $this->db->query('
			SELECT DISTINCT cert_level FROM ustx.ab_cert
			WHERE ab_operator_id = :AB_OPERATOR_ID'
		, array(':AB_OPERATOR_ID' => $ab_operator_id))->as_array();
		
		$certs = array();
		foreach($rows as $row) {
			$certs[] = $row['CERT_LEVEL'];
		}

		if (in_array('A/B', $certs) ||
			(in_array('A', $certs) && in_array('B', $certs)))
			return('A/B');
		elseif (count($certs))
			return($certs[0]);
		else
			return(NULL);
	}

	/**
 	 * Returns expiration date of certificate (5 years)
 	 * Note: good candidate for turning it into oracle function
 	 */
	public function get_cert_expdate($ab_operator_id) {
		return($this->get_value('
			SELECT add_months(max(cert_date), :MONTHS) VAL
			FROM ustx.ab_cert
			WHERE ab_operator_id = :AB_OPERATOR_ID'
		, array(':MONTHS' => Ab_cert_Model::CERT_MONTHS, ':AB_OPERATOR_ID' => $ab_operator_id)));

	}

	public function has_c_operator($facility_id) {
		return(count($this->get_list("FACILITY_ID = :FACILITY_ID AND FIRST_NAME = 'C Operator'", NULL, array(':FACILITY_ID' => $facility_id))) > 0);
	}

	public function get_dropdown_ab_operators_by_facility($dropdown_id=NULL, $dropdown_desc=NULL, $where=array(), $bound_vars=array()) {
		return($this->get_dropdown($dropdown_id, $dropdown_desc, $where, $bound_vars));
	}

	protected function _validate_rules($vdata) {
		$vdata->add_rules('first_name', 'required');
		$vdata->add_rules('last_name', 'required');

		return($vdata);
	}
}
