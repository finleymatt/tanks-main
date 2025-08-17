<?php defined('SYSPATH') or die('No direct script access.');
/**
  * Date helper Class Overload
  *
  * This is a customized extension/overload of the date helper class.
  *
  * @package onestop
  * @subpackage helpers
  * @author George Huang
  */

class date extends date_Core {

	// convert date to dd-MMM-yy format
	public static function format_date($date) {
		return !empty($date) ? strtoupper(date('d-M-y', strtotime($date))) : '';
	}

	// convert date to M d, Y format
	public static function reverse_format_date($date) {
		return !is_null($date) ? date("F d, Y", strtotime($date)) : NULL;
	}

	// convert date to mm/dd/yyyy format
	public static function new_format_date($date){
		return !is_null($date) ? date("m/d/Y", strtotime($date)) : NULL;
	}
}

