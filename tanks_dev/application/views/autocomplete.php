<?php
/**
 * General Autocomplete View for use by any Object in JSON format
 *
 * <p>Used for Jquery's autocomplete input feature</p>
 *
 * <b>When using this view, template must be set to tpl_blank</b>
 *
 * @package ### file docblock
 * @subpackage views
 * @uses ###
 * @see ###
 *
*/

header('Content-Type: application/json');

$result = array();
foreach($dropdown_rows as $code => $desc) {
	$result[] = array('value' => $code, 'label' => "{$code}: {$desc}");
}

echo(json_encode($result));
?>

