<?php


Class Staff_Model extends Model
{
	const TANK_TYPE_AST = 'A';
	public $table_name = 'USTX.STAFF';
	public $pks = array('CODE');
	public $lookup_code = 'CODE';
	public $lookup_desc = "FIRST_NAME || ' ' || LAST_NAME";


	/**
 	 * Given login_id, returns 2 or 3 letter initals of the staff.
 	 * The $login_id is checked against login_id first, then sep_login_id.
 	 * Reason for this is login_id was originally used throughout Onestop,
 	 * however, after move to SEP, sep_login_id replaced login_id.
 	 */
	public function get_code($login_id) {
		$rows = Model::instance('Staff')->get_list(array('LOGIN_ID' => $login_id));
		if (count($rows) <= 0)
			$rows = Model::instance('Staff')->get_list(array('SEP_LOGIN_ID' => $login_id));

		if (count($rows))
			return($rows[0]['CODE']);
		else
			return('unknown');

	}

	public function get_name($code) {
		$row = $this->get_row($code);
		if ($row)
			return(text::join(array($row['FIRST_NAME'], $row['LAST_NAME']), ' '));
		else
			return('INVALID STAFF');
	}

	public function get_dropdown_inspector($dropdown_id=NULL, $dropdown_desc=NULL, $where=array(), $bound_vars=array(), $active_only=TRUE) {
		$where['staff_type'] = 'S';
		if ($active_only) $where['restricted'] = 'Y';
		return($this->get_dropdown($dropdown_id, $dropdown_desc, $where, $bound_vars));
	}

	public static function staff_type_lu($key) {
		$staff_types = array('B' => 'Budget Analyst',
			'F' => 'Financial Specialist',
			'Y' => 'Secretary',
			'M' => 'Manager',
			'S' => 'Inspector',
			'I' => 'IT suport',
			'P' => 'Project Mgr',
			'A' => 'Auditor');

		if (!$key)
			return('');
		elseif (array_key_exists($key, $staff_types))
			return($staff_types[$key]);
		else
			return('unknown');
	}

	public function get_staff_phone_number($sep_login_id) {
		$row = $this->db->query('
			SELECT TELEPHONE_AREA_CODE, TELEPHONE_EXCHANGE_CODE, TELEPHONE_STATION_CODE			
			FROM SEP.reg_user_profile
			WHERE user_id = :SEP_LOGIN_ID'
		, array(':SEP_LOGIN_ID' => $sep_login_id))->as_array();
		$phone_number = !empty($row) ? $row[0]['TELEPHONE_AREA_CODE'] . '-' . $row[0]['TELEPHONE_EXCHANGE_CODE'] . '-' . $row[0]['TELEPHONE_STATION_CODE'] : '';
	
		return $phone_number;
	}
}
