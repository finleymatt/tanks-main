<?php


Class Owner_waivers_Model extends Model
{
	public $table_name = 'USTX.OWNER_WAIVERS';
	public $pks = array('OWNER_ID', 'FISCAL_YEAR', 'WAIVER_CODE');
	public $parent_pks = array('Owners_mvw' => array('OWNER_ID'));


	public function insert($parent_ids, $data) {
		$this->db->set('owner_id', $parent_ids[0]);
		$this->db->set('user_created', Session::instance()->get('UserID'));
		$this->db->set('date_created', 'sysdate', FALSE);

		return(parent::insert($parent_ids, $data));
	}

	public function update($ids, $data) {
		$this->db->set('user_modified', Session::instance()->get('UserID'));
		$this->db->set('date_modified', 'sysdate', FALSE);

		return(parent::update($ids, $data));
	}

	/**
 	 * Check for uniqueness of multi-keyed ID
 	 */
	public function is_valid_id($val, $field) {
		if ($this->get_row(array($val['owner_id'], $val['fiscal_year'], $val['waiver_code'])))
			$val->add_error($field, 'A waiver already exists for the selected owner, FY, and waiver code');
	}

	protected function _validate_rules($vdata) {
		if (isset($vdata['owner_id'])) {  // rules for inserts only
			$vdata->add_rules('waiver_code', 'required');
			$vdata->add_rules('fiscal_year', 'required');
			$vdata->add_callbacks('waiver_code', array($this, 'is_valid_id'));
		}

		$vdata->add_callbacks('facility_id', array(Model::instance('Facilities_mvw'), 'is_valid_id'));
		return($vdata);
	}
}
