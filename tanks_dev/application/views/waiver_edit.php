<?php
/**
 * Owner Waiver Add/Edit Form
 *
 * <p>View for Owner waiver add and edit forms.</p>
 *
 * <b>Since owner_waiver has multiple fields in PK, waiver_code and FY fields
 * are disabled and not editable in edit form.</b>
 *
 * @package ### file docblock
 * @subpackage views
 * @uses ###
 * @see ###
 *
*/

$owner_id = ($is_add ? $parent_ids[0] : $row['OWNER_ID']);

?>
<h1>Owner Waiver <?= ($is_add ? 'Add New' : 'Edit') ?></h1>

<form action="<?= $action ?>" method="post" class="validate_form edit_form">
<fieldset class='ui-widget ui-widget-content ui-corner-all'>
<legend class='ui-widget ui-widget-header ui-corner-all'>Waiver</legend>
<table class="horiz_table ui-widget ui-corner-all">
	<caption><div class="right_float"><?= ($is_add ? '' : Controller::_instance('Waiver')->_view_button(array($row['OWNER_ID'], $row['FISCAL_YEAR'], $row['WAIVER_CODE']))) ?></div></caption>
	<?= html::horiz_table_tr('Owner', html::owner_link($owner_id), FALSE) ?>
	<?= html::horiz_table_tr_form('Facility', form::input('facility_id', $row['FACILITY_ID'], 'class="ui-autocomplete validate[custom[integer]]"')) ?>
	<?= html::horiz_table_tr_form('Waiver Code', form::dropdown('waiver_code', Model::instance('Ust_ref_codes')->get_dropdown('OWNER_WAIVERS.WAIVER_CODE'), $row['WAIVER_CODE'], ($is_add ? '' : 'disabled="disabled"') .' class="validate[required]"'), TRUE) ?>
	<?= html::horiz_table_tr_form('FY', form::dropdown('fiscal_year', Model::instance('Fiscal_years')->get_dropdown(), $row['FISCAL_YEAR'], ($is_add ? '' : 'disabled="disabled"') .' class="validate[required]"'), TRUE) ?>
	<?= html::horiz_table_tr_form('Amount', form::input('amount', $row['AMOUNT'], 'class="validate[required,custom[currency]]"'), TRUE) ?>
	<?= html::horiz_table_tr_form('Comment', form::textarea(array('name'=>'waiver_comment', 'rows'=>8, 'cols'=>80, 'maxlength'=>2000), $row['WAIVER_COMMENT']) . 'max 2000 chars') ?>
</table>
<?= form::submit('submit', 'Submit', 'class="ui-button ui-state-default ui-corner-all"') ?>
</fieldset>
</form>
<script>
	$(function() {
		$( "#facility_id" ).autocomplete({
			source: "<?= url::site() ?>index.php?facility/autocomplete&",
			minLength: 3
		});
	});
</script>
