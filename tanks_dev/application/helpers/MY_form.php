<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Form helper Class Overload
 *
 * This is a customized extension/overload of the form helper class.
 *
 * @package onestop
 * @subpackage helpers
 * @author Min Lee
 */

class form extends form_Core {


	///////////////////////////////////////////////////////////////////
	// Onestop specific functions
	///////////////////////////////////////////////////////////////////

	public static function view_button($url, $label='view') {
		return("<a href='{$url}'><div class='action_button ui-state-default ui-corner-all' title='{$label}'><span class='ui-icon ui-icon-arrowreturnthick-1-e' style='float:left;'></span>{$label}</div></a>");
	}

	public static function edit_button($url, $label='edit') {
		return("<a href='{$url}'><div class='action_button ui-state-default ui-corner-all' title='{$label}'><span class='ui-icon ui-icon-pencil' style='float:left;'></span>{$label}</div></a>");
	}

	public static function add_button($url, $label='add new') {
		return("<a href='{$url}'><div class='action_button ui-state-default ui-corner-all' title='{$label}'><span class='ui-icon ui-icon-plus' style='float:left;'></span>{$label}</div></a>");
	}

	public static function delete_button($url, $label='delete') {
		return("<a href='{$url}'><div class='action_button ui-state-default ui-corner-all' title='{$label}'><span class='ui-icon ui-icon-trash' style='float:left;'></span>{$label}</div></a>");
	}

	public static function cancel_button($url=NULL, $label='Cancel') {
		if ($url == NULL) $url = 'history.back();';
		return("<input type='button' class='ui-button ui-state-default ui-corner-all' value='{$label}' onClick='{$url}' /> ");
		//return("<a href='{$url}'><div class='action_button ui-state-default ui-corner-all' title='{$label}'><span class='ui-icon ui-icon-arrowreturnthick-1-w' style='float:left;'></span>{$label}</div></a>");
	}

	public static function confirm_button($url, $label='Confirm') {
		return("<input type='button' class='ui-button ui-state-highlight ui-state-default ui-corner-all' value='{$label}' onClick='location.href=\"{$url}\"' /> ");
		//return("<a href='{$url}'><div class='action_button ui-state-highlight ui-state-default ui-corner-all' title='{$label}'><span class='ui-icon ui-icon-check' style='float:left;'></span>{$label}</div></a>");
	}

	public static function print_button($url, $label='print') {
		return("<a href='{$url}'><div class='action_button ui-state-default ui-corner-all' title='{$label}'><span class='ui-icon ui-icon-print' style='float:left;'></span>{$label}</div></a>");
	}

	/**
	* Copied from kohana's url::dropdown and modified to work with chained selects
	* Not used yet -- using optgroup feature instead for tank detail edit form
	*
	* @param   string|array  input name or an array of HTML attributes
	* @param   array         select options, when using a name
	* @param   string        option key that should be selected by default
	* @param   string        a string to be attached to the end of the attributes
	* @return  string
	*/
	public static function dropdown_chained($data, $options = NULL, $selected = NULL, $extra = '')
	{
		if (isset($data['options']))
		{
			// Use data options
			$options = $data['options'];
		}

		if (isset($data['selected']))
		{
			// Use data selected
			$selected = $data['selected'];
		}

		// Selected value should always be a string
		$selected = (string) $selected;

		$input = '<select'.form::attributes($data, 'select').' '.$extra.'>'."\n";
		foreach ((array) $options as $option)
		{
			// Key should always be a string
			$key = (string) $key;

			//$sel = ($selected === $key) ? ' selected="selected"' : '';
			// changed by KK 01/09/2009 - see W3C HTML 4.01 spec.: http://www.w3.org/TR/1999/REC-html401-19991224/interact/forms.html#adef-selected
			$sel = ($selected === $key) ? ' selected ' : '';
			$input .= '<option value="'.$option['ID'].'"'.$sel. " class='{$option['CATEGORY']}'>{$option['DESCRIPTION']}</option>\n";
		}
		$input .= '</select>';

		return $input;
	}
}

