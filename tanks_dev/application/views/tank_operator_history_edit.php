<?php
/**
 * Tank Operator History Edit and Add form
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
$operator_id = ($is_add ? '' : $row['OPERATOR_ID']);

?>
<h1>Tank Operator History - <?= ($is_add ? 'Add New' : 'Edit') ?></h1>

<form action="<?= $action ?>" method="post" class="validate_form edit_form">
<fieldset class='ui-widget ui-widget-content ui-corner-all'>
<legend class='ui-widget ui-widget-header ui-corner-all'>Tank Operator History</legend>
<table class="horiz_table ui-widget ui-corner-all">
	<caption><div class="right_float"><?= ($is_add ? '' : Controller::_instance('Tank_operator_history')->_delete_button($row, FALSE)) ?></div></caption>
	<?= html::horiz_table_tr('Tank ID', $tank_id) ?>
	<?= html::horiz_table_tr_form('Start Date', form::input('start_date', $row['START_DATE_FMT'], 'class="datepicker validate[required,custom[date2]]"') . 'mm/dd/yyyy', TRUE) ?>
	<?= html::horiz_table_tr_form('End Date', form::input('end_date', $row['END_DATE_FMT'], 'class="datepicker validate[custom[date2]]"') . 'mm/dd/yyyy') ?>
	<?= html::horiz_table_tr_form('Operator ID', form::input('operator_id', $operator_id, 'class="ui-autocomplete validate[required]"'), TRUE) ?>
</table>
<?= form::submit('submit', 'Submit', 'class="ui-button ui-state-default ui-corner-all"') ?>
</fieldset>
</form>
<script>
	$(function() {
		$( "#operator_id" ).autocomplete({
			source: "<?= url::site() ?>index.php?operator/autocomplete&",
			minLength: 3
		});
	});
</script>
