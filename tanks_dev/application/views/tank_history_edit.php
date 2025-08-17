<?php
/**
 * Tank History Edit and Add form
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
$owner_id = ($is_add ? $parent_ids[1] : $row['OWNER_ID']);  // not pk, but for foreign key

?>
<h1>Tank Owner History - <?= ($is_add ? 'Add New' : 'Edit') ?></h1>

<form action="<?= $action ?>" method="post" class="validate_form edit_form">
<fieldset class='ui-widget ui-widget-content ui-corner-all'>
<legend class='ui-widget ui-widget-header ui-corner-all'>Tank History</legend>
<table class="horiz_table ui-widget ui-corner-all">
	<caption><div class="right_float"><?= ($is_add ? '' : Controller::_instance('Tank_history')->_delete_button($row, FALSE)) ?></div></caption>
	<?= html::horiz_table_tr('Tank ID', $tank_id) ?>
	<?= html::horiz_table_tr_form('History Date', form::input('history_date', $row['HISTORY_DATE_FMT'], 'class="datepicker validate[custom[date2]]"') . 'mm/dd/yyyy') ?>
	<?= html::horiz_table_tr_form('History', form::dropdown(array('name' => 'history_code'), Model::instance('Tank_history_codes')->get_dropdown(), $row['HISTORY_CODE'], 'class="validate[required]"'), TRUE) ?>
	<?= html::horiz_table_tr_form('Owner ID', form::input('owner_id', $owner_id, 'class="ui-autocomplete validate[required]"'), TRUE) ?>
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
