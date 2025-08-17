<?php
/**
 * A/B Operator Tank Status Add/Edit form
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

?>
<h1>Reported Tank Status - <?= ($is_add ? 'Add New' : 'Edit') ?></h1>

<form action="<?= $action ?>" method="post" class="validate_form edit_form">
<fieldset class='ui-widget ui-widget-content ui-corner-all'>
<legend class='ui-widget ui-widget-header ui-corner-all'>A/B Operator</legend>
<table class="horiz_table ui-widget ui-corner-all">
	<!--<caption><div class="right_float"><?= ($is_add ? '' : Controller::_instance('Ab_tank_status')->_view_button($row['ID'])) ?></div></caption>-->
	<?= html::horiz_table_tr_form('Tank Status', form::dropdown('tank_status_code', Model::instance('Tank_status_codes')->get_dropdown(), $row['TANK_STATUS_CODE'])) ?>
	<?= html::horiz_table_tr_form('Tank Last Used', form::input('tank_last_used', $row['TANK_LAST_USED_FMT'], 'class="datepicker validate[custom[date2]]"') . ' mm/dd/yyyy') ?>
	<?= html::horiz_table_tr_form('Tank Status Note', form::input('tank_status_note', $row['TANK_STATUS_NOTE']) . ' 200 chars max') ?>
</table>
<?= form::submit('submit', 'Submit', 'class="ui-button ui-state-default ui-corner-all"') ?>
</fieldset>
</form>
