<?php
/**
 * Invoice Text edit form
 *
 * Allows edit of invoice text data stored in ustx.invoice_codes table
 *
 * @package ### file docblock
 * @subpackage views
 * @uses ###
 *
*/
?>
<h1>Invoice Text - <?= ($is_add ? 'Add New' : 'Edit') ?></h1>

<form action="<?= $action ?>" method="post" class="validate_form edit_form">
<fieldset class='ui-widget ui-widget-content ui-corner-all'>
<legend class='ui-widget ui-widget-header ui-corner-all'>Tank</legend>
<table class="horiz_table ui-widget ui-corner-all">
	<caption><div class="right_float"></div></caption>
	<?= html::horiz_table_tr('Code', $row['CODE']) ?>
	<?= html::horiz_table_tr_form('Description', form::input('description', $row['DESCRIPTION'], 'class="validate[required]"'), TRUE) ?>
	<?= html::horiz_table_tr_form('Coupon Text', form::textarea(array('name'=>'cupon_format', 'rows'=>10, 'cols'=>100, 'maxlength'=>2000), $row['CUPON_FORMAT']). 'max 2000 chars') ?>
	<?= html::horiz_table_tr_form('Invoice Text', form::textarea(array('name'=>'invoice_text', 'rows'=>20, 'cols'=>100, 'maxlength'=>5000), $row['INVOICE_TEXT']). 'max 5000 chars') ?>
</table>
<?= form::submit('submit', 'Submit', 'class="ui-button ui-state-default ui-corner-all"') ?>
</fieldset>
</form>
