<?php
/**
 * Test result tester Add form
 *
 * @package ### file docblock
 * @subpackage views
 * @uses ###
 * @see ###
 *
*/
?>

<h1>Test Result Tester - Add</h1>
<form action="<?= $action ?>" method="post" class="validate_form edit_form">
<fieldset class='ui-widget ui-widget-content ui-corner-all'>
<legend class='ui-widget ui-widget-header ui-corner-all'>Test Result Tester</legend>
<table class="horiz_table ui-widget ui-corner-all">
	<?= html::horiz_table_tr_form('First Name', form::input('first_name', '', 'class="validate[required]"'), TRUE) ?>
	<?= html::horiz_table_tr_form('Last Name', form::input('last_name', '', 'class="validate[required]"'), TRUE) ?>
</table>
<?= form::submit('submit', 'Submit', 'class="ui-button ui-state-default ui-corner-all"') ?>
</fieldset>
</form>
