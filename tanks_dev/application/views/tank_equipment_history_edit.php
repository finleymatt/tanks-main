<?php
/**
 * Tank Equipment History Edit and Add form
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

$tank_id = ($is_add ? $parent_ids[0] : $row['TANK_ID']);
$history = array("Installed" => "Installed", "Removed" => "Removed");


?>
<h1>Tank Equipment History - <?= ($is_add ? 'Add New' : 'Edit') ?></h1>

<form action="<?= $action ?>" method="post" class="validate_form edit_form">
<fieldset class='ui-widget ui-widget-content ui-corner-all'>
<legend class='ui-widget ui-widget-header ui-corner-all'>Tank Equipment History</legend>
<table class="horiz_table ui-widget ui-corner-all">
	<caption><div class="right_float"><?= ($is_add ? '' : Controller::_instance('Tank_history')->_delete_button($row, FALSE)) ?></div></caption>
	<?= html::horiz_table_tr('Tank ID', $tank_id) ?>
	<?= html::horiz_table_tr('Tank Detail Code', form::dropdown('tank_detail_code', Model::instance('Tank_detail_codes')->get_dropdown_tank_equipment(), $row['TANK_DETAIL_CODE'], 'class="validate[required]"'), FALSE) ?>
	<?= html::horiz_table_tr_form('History', form::dropdown('history', $history, $row['HISTORY'], 'class="validate[required]"'), FALSE) ?>
	<?= html::horiz_table_tr_form('History Date', form::input('history_date', $row['HISTORY_DATE_FMT'], 'class="datepicker validate[custom[date2]]"') . 'mm/dd/yyyy') ?>
</table>
<?= form::submit('submit', 'Submit', 'class="ui-button ui-state-default ui-corner-all"') ?>
</fieldset>
</form>
<script>
	$(function() {
		$( "#owner_id" ).autocomplete({
			source: "<?= url::site() ?>index.php?owner/autocomplete&",
			minLength: 3
		});
	});
</script>
