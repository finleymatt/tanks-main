<?php

Class Ref_contact_type_Model extends Model
{
	public $table_name = 'USTX.REF_CONTACT_TYPE';
	public $lookup_code = 'CONTACT_TYPE_ID';
	public $lookup_desc = 'CONTACT_TYPE_DESC';

	// sort array by value before generate dropdown list
	public function get_sorted_dropdown() {
		$dropdown = $this->get_dropdown();
		asort($dropdown);
		$sorted_dropdown = array("" => $dropdown[""]) + $dropdown;
		return $sorted_dropdown;
	}
}
