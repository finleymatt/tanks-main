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
<h1>Facility</h1>

<!-- find Facility by ID form -->
<form action='<?= url::fullpath('/facility/view/') ?>' class='validate_form' method='post' style='float:left; clear:left; margin-top:20px; margin-bottom:20px'>
	<fieldset class='ui-widget ui-widget-content ui-corner-all'>
	<legend class='ui-widget ui-widget-header ui-corner-all'>Find by ID</legend>
	<label>Facility ID:</label>
	<input name='facility_id' type='text' id='facility_id' value='<?= $facility_id ?>' size='5' class='validate[required,custom[integer]]' /><br clear='all' />
	<input value="Submit" type="submit" class="ui-button ui-state-default ui-corner-all" />
	</fieldset>
</form>

<!-- find Facility by name or address form -->
<form action='<?= url::fullpath('/facility/search/') ?>' class='validate_form' method='post' style='float:left; clear:left; margin-top:20px; margin-bottom:20px'>
	<fieldset class='ui-widget ui-widget-content ui-corner-all'>
	<legend class='ui-widget ui-widget-header ui-corner-all'>Search</legend>
	<label>Name:</label>
	<input name='facility_name' type='text' id='facility_name' value='<?= $facility_name ?>' size='25' /><br clear='all' />
	<label>Street:</label>
	<input name='street' type='text' id='steet' value='<?= $street ?>' size='25' /><br clear='all' />
	<label>City:</label>
	<input type='text' name='city' id='city' value='<?= $city ?>' size='25' /><br clear='all' />
	<label>Zip:</label>
	<input type='text' name='zip' id='zip' value='<?= $zip ?>' size='10' maxlength='15' /><br clear='all' />
	<input value="Submit" type="submit" class="ui-button ui-state-default ui-corner-all" />
	</fieldset>
</form>

<!-- find Tank by ID form -->
<form action='<?= url::fullpath('/tank/view/') ?>' class='validate_form' method='post' style='float:left; clear:left; margin-top:20px; margin-bottom:20px'>
	<fieldset class='ui-widget ui-widget-content ui-corner-all'>
	<legend class='ui-widget ui-widget-header ui-corner-all'>Find Tank by ID</legend>
	<label>Tank ID:</label>
	<input name='tank_id' type='text' id='tank_id' value='<?= $tank_id ?>' size='5' class='validate[required,custom[integer]]' /><br clear='all' />
	<input value="Submit" type="submit" class="ui-button ui-state-default ui-corner-all" />
	</fieldset>
</form>
