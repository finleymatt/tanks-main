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

$facility_id = ($is_add ? $parent_ids[0] : $row['FACILITY_ID']);
$owner_id = ($is_add ? $parent_ids[1] : $row['OWNER_ID']);

?>
<h1>Permit - <?= ($is_add ? 'Add New' : 'Edit') ?></h1>

<form action="<?= $action ?>" method="post" class="validate_form edit_form">
<fieldset class='ui-widget ui-widget-content ui-corner-all'>
<legend class='ui-widget ui-widget-header ui-corner-all'>Permit</legend>
<table class="horiz_table ui-widget ui-corner-all">
	<caption><div class="right_float"><?= ($is_add ? '' : Controller::_instance('Permit')->_view_button(array($row['FACILITY_ID'], $row['OWNER_ID'], $row['FISCAL_YEAR']))) ?> <?= ($is_add ? '' : Controller::_instance('Permit')->_delete_button(array($row['FACILITY_ID'], $row['OWNER_ID'], $row['FISCAL_YEAR']))) ?></div></caption>
	<?= html::horiz_table_tr('Owner', html::owner_link($owner_id), FALSE) ?>
	<?= html::horiz_table_tr('Facility', html::facility_link($facility_id), FALSE) ?>
	<?= html::horiz_table_tr_form('FY', form::dropdown('fiscal_year', Model::instance('Fiscal_years')->get_dropdown(), $row['FISCAL_YEAR'], 'class="validate[required]"'), TRUE) ?>
	<?= html::horiz_table_tr_form('Date Permitted', form::input('date_permitted', $row['DATE_PERMITTED'], 'class="datepicker validate[custom[date2]]"') . 'mm/dd/yyyy') ?>
	<?= html::horiz_table_tr_form('Date Printed', form::input('date_printed', $row['DATE_PRINTED'], 'class="datepicker validate[custom[date2]]"') . 'mm/dd/yyyy') ?>
	<?= html::horiz_table_tr_form('Certificate (Permit#)', form::input('permit_number', $row['PERMIT_NUMBER'], 'class="validate[custom[integer]]"')) ?>
	<?= html::horiz_table_tr_form('Tank Count', form::input('tanks', $row['TANKS'], 'class="validate[custom[integer]]"')) ?>
	<?= html::horiz_table_tr_form('AST Count', form::input('ast_count', $row['AST_COUNT'], 'class="validate[custom[integer]]"')) ?>
	<?= html::horiz_table_tr_form('UST Count', form::input('ust_count', $row['UST_COUNT'], 'class="validate[custom[integer]]"')) ?>
</table>
<?= form::submit('submit', 'Submit', 'class="ui-button ui-state-default ui-corner-all"') ?>
</fieldset>
</form>
