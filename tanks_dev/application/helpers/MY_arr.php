<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @package Onestop
 * @subpackage helpers
 */


class arr extends arr_Core {

	public static function subset($data_arr, $keys) {
		$result = array();
		foreach ($keys as $key) {
			if (isset($data_arr[$key]))
				$result[] = $data_arr[$key];
		}
		return($result);
	}

	/**
	 * Returns array(id=val) pairs for use in form::dropdown()
	 * $rs_arr must be an array with keys, 'ID' and 'DESCRIPTION'
	 * 'CATEGORY' is optional and will add another dimension to the array returned,
	 * resulting in optgroup select dropdown menu
	 **/
	public static function make_dropdown($rs_arr, $show_message=TRUE) {
		if ($show_message)
			$result = array('' => 'Select...');
		else
			$result = array();

		foreach($rs_arr as $row) {
			//$key = array_keys($row); // use 1st field as id, 2nd as label
			if (isset($row['CATEGORY']))
				$result[$row['CATEGORY']][$row['ID']] = $row['DESCRIPTION'];
			else
				$result[$row['ID']] = $row['DESCRIPTION'];
		}

		return($result);
	}
}
