<?php
/**
 * Notice Edit form
 *
 * @package ### file docblock
 * @subpackage views
 * @uses ###
 * @see ###
 *
*/

$operator_id = ($is_add ? $parent_ids[0] : $row['OPERATOR_ID']);

?>
<h1>Notice <?= ($is_add ? 'Add New' : 'Edit') ?></h1>

<form action="<?= $action ?>" method="post" class="validate_form edit_form">
<fieldset class='ui-widget ui-widget-content ui-corner-all'>
<legend class='ui-widget ui-widget-header ui-corner-all'>Notice</legend>
<table class="horiz_table ui-widget ui-corner-all">
	<caption><div class="right_float"><?= ($is_add ? '' : Controller::_instance('Notice')->_view_button($row, FALSE)) ?></div></caption>
	<?= html::horiz_table_tr('ID', $row['ID']) ?>
	<?= html::horiz_table_tr('Operator', html::operator_link($operator_id), FALSE) ?>
	<?= html::horiz_table_tr_form('Notice Code', form::dropdown('notice_code', Model::instance('Invoice_codes')->get_dropdown(), $row['NOTICE_CODE'], 'class="validate[required]"'), TRUE) ?>
	<?= html::horiz_table_tr_form('Notice Date', form::input('notice_date', $row['NOTICE_DATE_FMT'], 'class="datepicker validate[required,custom[date2]]"') . 'mm/dd/yyyy', TRUE) ?>
	<?= html::horiz_table_tr_form('Letter Date', form::input('letter_date', $row['LETTER_DATE_FMT'], 'class="datepicker validate[required,custom[date2]]"') . 'mm/dd/yyyy', TRUE) ?>
</table>
<?= form::submit('submit', 'Submit', 'class="ui-button ui-state-default ui-corner-all"') ?>
</fieldset>
</form>
