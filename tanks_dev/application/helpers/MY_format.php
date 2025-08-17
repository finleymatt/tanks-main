<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @package Onestop
 * @subpackage helpers
 */


class format extends format_Core {

	public static function currency($money) {
		return(money_format('%.2n', $money));
	}
}
