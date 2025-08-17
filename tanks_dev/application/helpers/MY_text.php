<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @package Onestop
 * @subpackage helpers
 */


class text extends text_Core {
	public static function default_str($str, $default)
	{
		return(strlen(trim($str)) ? $str : $default);
	}

	/**
	 * Returns string joined by a delimeter, and skips over empty strings
	 */
	public static function join($str_arr, $delim=', ')
	{
		$result = '';
		foreach($str_arr as $index => $str) {
			if ($index == 0)
				$result = $str;
			elseif (!empty($str))
				$result .= "{$delim}{$str}";
		}

		return($result);
	}

	/**
	 * returns Yes or No given either Y or N
	 */
	public static function yesno($value, $default='N')
	{
		return(text::lookup(array('Y'=>'Yes', 'N'=>'No'), $value, $default));
	}

	/**
	 * returns string value of matched key
	 */
	public static function lookup($values_arr, $needle, $default_key=NULL)
	{
		if (isset($values_arr[$needle]))
			return($values_arr[$needle]);
		elseif ($default_key)
			return($values_arr[$default_key]);
		else
			return('');
	}

	public static function where_pk($pks, $values) {
		if (!is_array($pks)) $pks = array($pks);
		if (!is_array($values)) $values = array($values);

		// due to limitation in query builder, not using bound vars here
		// always quotes pk values in case str, but also works with numbers
		$conditions = array();
		foreach ($pks as $index => $pk) {
			$escaped = str_replace("'", "''", $values[$index]);
			$conditions[] = "{$pk} = '{$escaped}'";
		}

		if (count($conditions) <= 0) {
			throw new Kohana_User_Exception('text::where_pk error', 'Blank where clause encountered');
			exit;
		}

		return(implode(' AND ', $conditions));
	}

	public static function capitalize_first_letters($str) {
		$exception = array('LLC', 'DBA'); // Words you dont want to convert
		$temp = explode(' ', $str);
		foreach($temp as $key => $value) {
			$temp[$key] = in_array($value, $exception) ? ucwords($value) : ucwords(strtolower($value));
		}
		$result = implode(' ', $temp);
		return $result;
	}

	///////////////////////////////////////////////////////////////////
	// Onestop specific functions
	///////////////////////////////////////////////////////////////////
	
}
