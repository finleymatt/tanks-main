<?php

/**
 * Certified Installer Model
 *
 * This class handles certified installer related database operations.
 */
Class Certified_installers_Model extends Model
{
	public $table_name = 'USTX.CERTIFIED_INSTALLERS';
	public $pks = array('ID');
	public $lookup_code = 'ID';
	public $lookup_desc = "FIRST_NAME || ' ' || LAST_NAME";

	/**
	 *
	 * Get certified installer full name by id
	 *
	 */
	public function get_certified_installer_by_id($certified_installer_id) {
		$certified_installer = $this->db->query("
			SELECT (FIRST_NAME || ' ' || LAST_NAME) FULL_NAME
			FROM USTX.CERTIFIED_INSTALLERS
			WHERE ID = :ID"
			, array(':ID' => $certified_installer_id));
		return ($certified_installer ? $certified_installer[0]['FULL_NAME'] : NULL);
	}

	/*
	 * Det the drop down list of certified installers
	 *
	 * @return array
	 */
	public function get_dropdown_certified_installer() {
		$certified_installer_list = $this->db->query("
		SELECT ID, FIRST_NAME || ' ' || LAST_NAME DESCRIPTION
		FROM USTX.CERTIFIED_INSTALLERS
		ORDER BY FIRST_NAME")->as_array();
	
		return(arr::make_dropdown($certified_installer_list));
	}
}