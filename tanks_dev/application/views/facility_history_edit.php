<?php
/**
 * Facility History Add/Edit Form
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
<h1>Facility History - <?= ($is_add ? 'Add New' : 'Edit') ?></h1>

<form action="<?= $action ?>" method="post" class="validate_form edit_form">
<fieldset class='ui-widget ui-widget-content ui-corner-all'>
<legend class='ui-widget ui-widget-header ui-corner-all'>Facility History</legend>
<table class="horiz_table ui-widget ui-corner-all">
	<?= html::horiz_table_tr('Facility', html::facility_link($facility_id), FALSE) ?>
	<?= html::horiz_table_tr_form('Owner', form::input('owner_id', $row['OWNER_ID'], 'class="ui-autocomplete validate[required,custom[integer]]"'), TRUE) ?>
	<?= html::horiz_table_tr_form('History Code', form::dropdown('facility_history_code', Model::instance('Facility_history_codes')->get_dropdown(), $row['FACILITY_HISTORY_CODE'], 'class="validate[required]"'), TRUE) ?>
	<?= html::horiz_table_tr_form('History Date', form::input('facility_history_date', $row['FACILITY_HISTORY_DATE_FMT'], 'class="datepicker validate[required,custom[date2]]"') . 'mm/dd/yyyy', TRUE) ?>
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
