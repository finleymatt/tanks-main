<?php


Class Operators_mvw_Model extends Model
{
	public $table_name = 'USTX.OPERATORS_MVW';
	public $pks = array('ID');
	public $lookup_code = 'ID';
	public $lookup_desc = 'OPERATOR_NAME';


	public function search($fields)
	{
		$this->db->from($this->table_name);
		$bound_vars = array();
		if ($fields['OPERATOR_NAME']) {
			$this->db->where($this->_search_syntax('OPERATOR_NAME'));
			$bound_vars['OPERATOR_NAME'] = $fields['OPERATOR_NAME'];
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

	/**
	 * Get all facilities that have tanks with the selected operator
	 **/
	public function get_facilities($operator_id) {
		return($this->db->query("SELECT F.*, TANK_INFO.tank_count FROM ustx.facilities_mvw F,
			(select T.facility_id, count(*) tank_count from ustx.tanks T
				where T.operator_id = :operator_id
					and T.tank_status_code not in (4, 5) -- sold or removed
				group by T.facility_id) TANK_INFO
			WHERE F.id = TANK_INFO.facility_id"
			, array(':operator_id' => $operator_id))->as_array());
	}

	public function is_valid_id($validation, $field) {
		if ($validation[$field]) {
			if (! $this->get_row($validation[$field]))
				$validation->add_error($field, 'Entered Operator ID is incorrect');
		}
	}

}
