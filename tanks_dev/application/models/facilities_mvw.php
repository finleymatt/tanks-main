<?php


Class Facilities_mvw_Model extends Model
{
	public $table_name = 'USTX.FACILITIES_MVW';
	public $pks = array('ID');
	public $lookup_code = 'ID';
	public $lookup_desc = 'FACILITY_NAME';

	public $more_select = array("(select distinct 'Y' from lust.lust_releases_mvw where facility_id = USTX.FACILITIES_MVW.ID) IS_LUST"); // is this facility a LUST site?

	public function search($fields)
	{
		$this->db->from($this->table_name);
		$bound_vars = array();
		if ($fields['FACILITY_NAME']) {
			$this->db->where($this->_search_syntax('FACILITY_NAME'));
			$bound_vars['FACILITY_NAME'] = $fields['FACILITY_NAME'];
		}
		if ($fields['STREET']) {
			$this->db->where("({$this->_search_syntax('ADDRESS1')} or {$this->_search_syntax('ADDRESS2')})");
			$bound_vars['ADDRESS1'] = $fields['STREET'];
			$bound_vars['ADDRESS2'] = $fields['STREET'];
		}
		if ($fields['CITY']) {
			$this->db->where($this->_search_syntax('CITY'));
			$bound_vars['CITY'] = $fields['CITY'];
		}
		if ($fields['ZIP']) {
			$this->db->where($this->_search_syntax('ZIP'));
			$bound_vars['ZIP'] = $fields['ZIP'];
		}

		return($this->db->query(NULL, $bound_vars)->as_array());  //auto-generate sql
	}


	public function get_active_penalties($facility_id) {
		return($this->db->query('select P.inspection_id, P.penalty_code
			from ustx.inspections I
				inner join ustx.penalties P on I.id = P.inspection_id
			where I.facility_id = :facility_id
				and P.date_corrected is null', array(':facility_id' => $facility_id))->as_array());
	}

	public function is_valid_id($validation, $field) {
		if ($validation[$field]) {
			if (! $this->get_row($validation[$field]))
				$validation->add_error($field, 'Entered Facility ID is incorrect');
		}
	}

}
