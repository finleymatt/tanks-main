<?php
/**
 * file docblock; short description
 *
 * <p>long description</p>
 *
 * <b>IMPORTANT NOTE</b>
 *
 * @package ### file docblock
 * @subpackage views
 * @uses ###
 * @see ###
 *
*/
?>
<h1>Owner</h1>

<form action='<?= url::fullpath('/owner/view/') ?>' class='validate_form' method='post' style='float:left; clear:left; margin-top:20px; margin-bottom:20px'>
	<fieldset class='ui-widget ui-widget-content ui-corner-all'>
	<legend class='ui-widget ui-widget-header ui-corner-all'>Find by ID</legend>
	<label>Owner ID:</label>
	<input name='owner_id' type='text' id='owner_id' value='<?= $owner_id ?>' size='5' class='validate[required,custom[integer]]' /><br clear='all' />
	<input value="Submit" type="submit" class="ui-button ui-state-default ui-corner-all" />
	</fieldset>
</form>

<form action='<?= url::fullpath('/owner/search/') ?>' class='validate_form' method='post' style='float:left; clear:left; margin-top:20px; margin-bottom:20px'>
	<fieldset class='ui-widget ui-widget-content ui-corner-all'>
	<legend class='ui-widget ui-widget-header ui-corner-all'>Search</legend>
	<label>Name:</label>
	<input type='text' name='owner_name' id='owner_name' value='<?= html::h($owner_name) ?>' size='25' /><br clear='all' />
	<label>Street:</label>
	<input type='text' name='street' id='street' value='<?= html::h($street) ?>' size='25' /><br clear='all' />
	<label>City:</label>
	<input type='text' name='city' id='city' value='<?= html::h($city) ?>' size='25' /><br clear='all' />
	<label>Zip:</label>
	<input type='text' name='zip' id='zip' value='<?= html::h($zip) ?>' size='10' maxlength='15' /><br clear='all' />
	<label>State:</label>
	<input type='text' name='state' id='state' value='<?= html::h($state) ?>' size='2' maxlength='2' /><br clear='all' />
	<input value="Submit" type="submit" class="ui-button ui-state-default ui-corner-all" />
	</fieldset>
</form>
