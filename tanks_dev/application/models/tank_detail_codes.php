<?php


Class Tank_detail_codes_Model extends Model
{
	public $table_name = 'USTX.TANK_DETAIL_CODES';
	public $lookup_code = 'CODE';
	public $lookup_desc = 'DESCRIPTION';


	/**
	* Overridden to add category from ustx.tank_info_codes
	*/
	/**** this method works, but turns out I didn't need the category feature
	public function get_dropdown($dropdown_id=NULL, $dropdown_desc=NULL, $where=NULL, $bound_vars=array()) {
		$dropdown_id = $this->lookup_code;
		$dropdown_desc = $this->lookup_desc;

		$this->db->select("TDC.{$dropdown_id} as ID");
		$this->db->select("'(' || TDC.{$dropdown_id} || ') ' || TDC.{$dropdown_desc} as DESCRIPTION");
		$this->db->select("'(' || TIC.CODE || ') ' || TIC.DESCRIPTION as CATEGORY");
		$this->db->from("{$this->table_name} TDC");
		$this->db->join('USTX.TANK_INFO_CODES TIC', 'TIC.CODE', 'TDC.TANK_INFO_CODE');
		if ($where)
			$this->db->where($where);
		$this->db->orderby(array('TIC.CODE' => 'ASC',
			"TDC.{$this->lookup_code}" => 'ASC'));

		return(arr::make_dropdown($this->db->query(NULL, $bound_vars)->as_array()));
	}
	****************************/

	/**
 	 * Cheap method to remove the 'Select...' option
 	 */
	public function get_dropdown($dropdown_id=NULL, $dropdown_desc=NULL, $where=NULL, $bound_vars=array()) {
		$arr = parent::get_dropdown($dropdown_id, $dropdown_desc, $where, $bound_vars);
		array_shift($arr);
		return($arr);
	}

	/**
	 * Get the drop down list of tank equipment
	 *
	 */
	public function get_dropdown_tank_equipment() {
		$tank_equipment_codes = "('F01', 'F02','F03','F04','F06','F08','F10','F11','F12','F88','I01','I02','I03','I08','I09','I10','I11','I12', 'S03','S04','S05','S06','S07','S08','S09','S10','S11','S12','S13')";
		$tank_equipment_list = $this->db->query("
                SELECT CODE ID, ('(' || CODE || ') ' || DESCRIPTION) DESCRIPTION
                FROM USTX.TANK_DETAIL_CODES
		WHERE CODE IN " . $tank_equipment_codes
                . " ORDER BY CODE")->as_array();

                return(arr::make_dropdown($tank_equipment_list));
	}

}
