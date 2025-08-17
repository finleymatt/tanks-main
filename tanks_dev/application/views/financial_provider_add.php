<?php
/**
 * Financial Add form
 *
 * @package ### file docblock
 * @subpackage views
 * @uses ###
 * @see ###
 *
*/
?>

<h1>Financial Provider - Add</h1>
<form action="<?= $action ?>" method="post" class="validate_form edit_form">
<fieldset class='ui-widget ui-widget-content ui-corner-all'>
<legend class='ui-widget ui-widget-header ui-corner-all'>Financial Provider</legend>
<table class="horiz_table ui-widget ui-corner-all">
	<?= html::horiz_table_tr_form('Provider Name', form::input('description', '', 'class="validate[required]"'), TRUE) ?>
	<input type="hidden" id="financial_provider_code" name="code" value="<?php echo $code?>">
</table>
<?= form::submit('submit', 'Submit', 'class="ui-button ui-state-default ui-corner-all"') ?>
</fieldset>
</form>
