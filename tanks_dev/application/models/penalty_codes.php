<?php


Class Penalty_codes_Model extends Model
{
	public $table_name = 'USTX.PENALTY_CODES';
	public $lookup_code = 'CODE';
	public $lookup_desc = "CODE || ': ' || DESCRIPTION";

	/**
	 *  method to exclude some penalty codes not in use any more (first decimal before 4th character or begins with 'A' or 'B')
	 */
	public function get_active_codes_dropdown($dropdown_id=NULL, $dropdown_desc=NULL, $where=NULL, $bound_vars=array()) {
		$arr = parent::get_dropdown($dropdown_id, $dropdown_desc, $where, $bound_vars);
	
		$active_penalty_codes = array_filter($arr, function($var){
			if(substr($var, 0, 1) == 'A' || substr($var, 0, 1) == 'B' || substr($var, 1, 1) == '.' || substr($var, 2, 1) == '.') {
				return FALSE;
			} else {
				return TRUE;
			}
		});

		return $active_penalty_codes;
	}

	/**
	 *  method that get penalty codes not in use any more (first decimal before 4th character or begins with 'A' or 'B')
	 */
	public function get_inactive_codes($dropdown_id=NULL, $dropdown_desc=NULL, $where=NULL, $bound_vars=array()) {
		$arr = parent::get_dropdown($dropdown_id, $dropdown_desc, $where, $bound_vars);

		$inactive_penalty_codes = array_filter($arr, function($var){
			if(substr($var, 0, 1) == 'A' || substr($var, 0, 1) == 'B' || substr($var, 1, 1) == '.' || substr($var, 2, 1) == '.') {
				 return TRUE;
			} else {
				return FALSE;
			}
		});

		//convert inactive penalty code from array to string with keys
		$temp = array();
		foreach ($inactive_penalty_codes as $key => $value) {
			$temp[] = $key . '|' . $value;
		}
		$inactive_penalty_codes_str = implode(';', $temp);

		return $inactive_penalty_codes_str;
	}
}
