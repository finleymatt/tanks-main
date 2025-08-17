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
?>
<h1>Inspection - <?= ($is_add ? 'Add New' : 'Edit') ?></h1>

<form action="<?= $action ?>" method="post" class="validate_form edit_form">
<fieldset class='ui-widget ui-widget-content ui-corner-all'>
<legend class='ui-widget ui-widget-header ui-corner-all'>Inspection</legend>
<table class="horiz_table ui-widget ui-corner-all">
	<caption><div class="right_float"><?= ($is_add ? '' : Controller::_instance('Inspection')->_view_button($row['ID'])) ?></div></caption>
	<?= html::horiz_table_tr('ID', $row['ID']) ?>
	<?= html::horiz_table_tr('Facility', html::facility_link($facility_id), FALSE) ?>
	<?= html::horiz_table_tr_form('Date Inspected', form::input('date_inspected', $row['DATE_INSPECTED_FMT'], 'class="datepicker validate[custom[date2]]"') . 'mm/dd/yyyy') ?>
	<?= html::horiz_table_tr_form('Inspection', form::dropdown('inspection_code', Model::instance('Inspection_codes')->get_dropdown(), $row['INSPECTION_CODE'], 'class="validate[required]"'), TRUE) ?>
	<?= html::horiz_table_tr_form('LCC Number', form::input('nov_number', $row['NOV_NUMBER'], 'class="validate[custom[integer]]"')) ?>
	<?= html::horiz_table_tr_form('Staff', form::dropdown('staff_code', Model::instance('Staff')->get_dropdown(), $row['STAFF_CODE'], 'class="validate[required]"'), TRUE) ?>
	<?= html::horiz_table_tr_form('Certified Installer', form::dropdown('certified_installer_id', Model::instance('Certified_installers')->get_dropdown_certified_installer(), $row['CERTIFIED_INSTALLER_ID'])) ?>
	<?= html::horiz_table_tr_form('Case ID', form::input('case_id', $row['CASE_ID'], 'class="validate[custom[integer]]"')) ?>
	<?= html::horiz_table_tr_form('Compliance Order Issue Date', form::input('compliance_order_issue_date', $row['COMPLIANCE_ISSUE_DATE_FMT'], 'class="datepicker validate[custom[date2]]"') . 'mm/dd/yyyy') ?>
	<?= html::horiz_table_tr_form('Compliance Date', form::input('compliance_date', $row['COMPLIANCE_DATE_FMT'], 'class="datepicker validate[custom[date2]]"') . 'mm/dd/yyyy') ?>
	<?= html::horiz_table_tr_form('Compliance Submit Date', form::input('compliance_submit_date', $row['COMPLIANCE_SUBMIT_DATE_FMT'], 'class="datepicker validate[custom[date2]]"') . 'mm/dd/yyyy') ?>
	<?= html::horiz_table_tr_form('Conference?', form::checkbox('conference', 'Y', ($row['CONFERENCE'] == 'Y'))) ?>
	<?= html::horiz_table_tr_form('Conference Comments', form::textarea(array('name'=>'conference_comments', 'rows'=>3, 'cols'=>45, 'maxlength'=>200), $row['CONFERENCE_COMMENTS']) . 'max 200 chars') ?>
	<!-- inspection changes in the future -->
	<!-- <caption><div class="right_float"><?= ($is_add ? '' : Controller::_instance('Inspection')->_view_button($row['ID'])) ?></div></caption>
	<?= html::horiz_table_tr('ID', $row['ID']) ?>
	<?= html::horiz_table_tr('Facility', html::facility_link($facility_id), FALSE) ?>
	<?= html::horiz_table_tr_form('Start Date', form::input('date_inspected', $row['DATE_INSPECTED_FMT'], 'class="datepicker validate[custom[date2]]"') . 'mm/dd/yyyy') ?>
	<?= html::horiz_table_tr_form('End Date', form::input('date_inspected', $row['DATE_INSPECTED_FMT'], 'class="datepicker validate[custom[date2]]"') . 'mm/dd/yyyy') ?>
	<?= html::horiz_table_tr_form('Inspection', form::dropdown('inspection_code', Model::instance('Inspection_codes')->get_dropdown(), $row['INSPECTION_CODE'], 'class="validate[required]"'), TRUE) ?>
	<?= html::horiz_table_tr_form('LCC Number', form::input('nov_number', $row['NOV_NUMBER'], 'class="validate[custom[integer]]"')) ?>
	<?= html::horiz_table_tr_form('Staff', form::dropdown('staff_code', Model::instance('Staff')->get_dropdown(), $row['STAFF_CODE'], 'class="validate[required]"'), TRUE) ?>
	<?= html::horiz_table_tr_form('Case ID', form::input('case_id', $row['CASE_ID'], 'class="validate[custom[integer]]"')) ?>
	<?= html::horiz_table_tr_form('LCAV', form::checkbox('conference', 'Y', ($row['CONFERENCE'] == 'Y'))) ?>
	<?= html::horiz_table_tr_form('NOV', form::checkbox('conference', 'Y', ($row['CONFERENCE'] == 'Y'))) ?>
	<?= html::horiz_table_tr_form('FCO', form::checkbox('conference', 'Y', ($row['CONFERENCE'] == 'Y'))) ?>
	<?= html::horiz_table_tr_form('Comments', form::textarea(array('name'=>'conference_comments', 'rows'=>3, 'cols'=>45, 'maxlength'=>200), $row['CONFERENCE_COMMENTS']) . 'max 200 chars') ?> -->
</table>
<?= form::submit('submit', 'Submit', 'class="ui-button ui-state-default ui-corner-all"') ?>
</fieldset>
</form>

<script>
// Automatically set compliance date to 30 days from issue date
// Feature requested by Bertha on Nov 2013
// Use onClose, since date can be edited by text change as well
$('#compliance_order_issue_date').datepicker({
	onClose: function(dateStr) {
		if (dateStr === '') return;

		var days = 30;
		var compliance_date = $.datepicker.parseDate('mm/dd/yy', dateStr);
		compliance_date.setDate(compliance_date.getDate() + days);
		$('#compliance_date').datepicker('setDate', compliance_date);

		alert('Compliance Date has been automatically set to be 30 days from the Issue Date.');
	}
});
</script>
