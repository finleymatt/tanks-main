<?php
/**
 * Operator search menu
 *
 * @package ### file docblock
 * @subpackage views
 * @uses ###
 * @see ###
 *
*/
?>
<h1>Operator</h1>

<form action='<?= url::fullpath('/operator/view/') ?>' class='validate_form' method='post' style='float:left; clear:left; margin-top:20px; margin-bottom:20px'>
	<fieldset class='ui-widget ui-widget-content ui-corner-all'>
	<legend class='ui-widget ui-widget-header ui-corner-all'>Find by ID</legend>
	<label>Operator ID:</label>
	<input name='operator_id' type='text' id='operator_id' value='<?= $operator_id ?>' size='5' class='validate[required]' /><br clear='all' />
	<input value="Submit" type="submit" class="ui-button ui-state-default ui-corner-all" />
	</fieldset>
</form>

<form action='<?= url::fullpath('/operator/search/') ?>' class='validate_form' method='post' style='float:left; clear:left; margin-top:20px; margin-bottom:20px'>
	<fieldset class='ui-widget ui-widget-content ui-corner-all'>
	<legend class='ui-widget ui-widget-header ui-corner-all'>Search</legend>
	<label>Name:</label>
	<input name='operator_name' type='text' id='operator_name' value='<?= $operator_name ?>' size='25' /><br clear='all' />
	<label>Street:</label>
	<input type='text' name='street' id='street' value='<?= html::h($street) ?>' size='25' /><br clear='all' />
	<label>City:</label>
	<input type='text' name='city' id='city' value='<?= $city ?>' size='25' /><br clear='all' />
	<label>Zip:</label>
	<input type='text' name='zip' id='zip' value='<?= $zip ?>' size='10' maxlength='15' /><br clear='all' />
	<input value="Submit" type="submit" class="ui-button ui-state-default ui-corner-all" />
	</fieldset>
</form>
