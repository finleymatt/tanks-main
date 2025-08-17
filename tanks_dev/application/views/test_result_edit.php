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

$facility_id = $row['FACILITY_ID'];
$nov_lcc_arr = array('' => 'No Violation');
foreach(Model::instance('Inspections')->get_inspections_by_facility($facility_id) as $inspection) {
	if(!is_null($inspection['NOV_NUMBER'])) {
		$nov_lcc_arr[$inspection['INSPECTION_ID']] = $inspection['NOV_NUMBER'];
	}
}
//var_dump($nov_lcc_arr);exit;
function radio_html($test_name, $test_result) {
	if($test_result == 'P') {
		$pass = ' checked';
		$fail = '';
	} else if ($test_result == 'F') {
		$pass = '';
		$fail = ' checked';
	} else {
		$pass = '';
		$fail = '';
	}
	$radio_html = '<label for="' . $test_name . '_pass" class="align-left"><input id="' . $test_name . '_pass" type="radio" name="' . $test_name . '" value="P"'
	. $pass . '>Pass</label>'
	. '<label for="' . $test_name . '_fail" class="align-left"><input id="' . $test_name . '_fail" type="radio" name="' . $test_name . '" value="F"' . $fail . '>Fail</label>';
	return $radio_html;
}
?>
<h1>Test Result - Edit</h1>

<form action="<?= $action ?>" method="post" class="validate_form edit_form">
<fieldset class='ui-widget ui-widget-content ui-corner-all'>
<legend class='ui-widget ui-widget-header ui-corner-all'>Test Result</legend>
<table class="horiz_table ui-widget ui-corner-all">
	<caption><div class="right_float"><?= ($is_add ? '' : Controller::_instance('Test_result')->_view_button($row['ID'])) ?></div></caption>
	<?= html::horiz_table_tr('ID', $row['ID']) ?>
	<?= html::horiz_table_tr('Facility', html::facility_link($facility_id), FALSE) ?>
	<?= html::horiz_table_tr('Tank ID', $row['TANK_ID']) ?>
	<?= html::horiz_table_tr_form('Tester', form::dropdown('tester_id', Model::instance('Ref_test_results_testers')->get_tester_list(), $row['TESTER_ID'], 'class="validate[required]"'), TRUE) ?>
	<?= html::horiz_table_tr_form('Test Company', form::dropdown('test_company_id', Model::instance('Ref_test_results_test_company')->get_test_company_list(), $row['TESTING_COMPANY_ID'], 'class="validate[required]"'), TRUE) ?>
	<?= html::horiz_table_tr_form('Violation(s) Issued (NOV/LCC Number)', form::dropdown('violation_issued', $nov_lcc_arr, $row['VIOLATION_INSPECTION_ID'], 'class="validate[custom[integer]]"')) ?>
	<?= html::horiz_table_tr_form('Test Date', form::input('test_date', $row['TEST_DATE_FMT'], 'class="datepicker validate[custom[date2]] validate[required]"') . ' mm/dd/yyyy', TRUE) ?>
	<?= html::horiz_table_tr_form('Test Submitted Date', form::input('test_submitted_date', $row['TEST_SUBMITTED_DATE_FMT'], 'class="datepicker validate[custom[date2]]"') . ' mm/dd/yyyy') ?>

	<tr>
		<th>Inspector:</th>
		<td class='ui-widget-content'>
			<input type='text' list='inspector_edit' id='inspector' name='inspector' value='<?= $row['INSPECTOR_NAME'] ?>' autocomplete="off" />
			<datalist id='inspector_edit'>
			<?php
				foreach(Model::instance('Test_results')->get_inspector_list() as $inspector) {
					echo "<option value='" . $inspector . "'>" . $inspector . "</option>";
				}
			?>
		</td>
	</tr>
	<?= html::horiz_table_tr_form('Original Failed Test ID if a Retest', form::input('original_test_id', $row['RETEST_OF_TEST_RESULT_ID'], 'class="validate[custom[integer]]"')) ?>
	<?= html::horiz_table_tr_form('ALLD Functionalify',  radio_html('alld', $row['ANNUAL_ALLD_FUNC_PASSFAIL_FL'])) ?>
	<?= html::horiz_table_tr_form('Sensor Functionalify', radio_html('sensor', $row['ANNUAL_SENS_FUNC_PASSFAIL_FL'])) ?>
	<?= html::horiz_table_tr_form('Line Tightness', radio_html('line_tightness', $row['ANNUAL_LINE_TIGHT_PASSFAIL_FL'])) ?>
	<?= html::horiz_table_tr_form('Tank Tightness', radio_html('tank_tightness', $row['ANNUAL_TANK_TIGHT_PASSFAIL_FL'])) ?>
	<?= html::horiz_table_tr_form('ATG Test', radio_html('atg', $row['ANNUAL_AGT_TEST_PASSFAIL_FL'])) ?>
	<?= html::horiz_table_tr_form('Overfill Functionality', radio_html('overfill', $row['YEAR3_OVERFILL_PASSFAIL_FL'])) ?>
	<?= html::horiz_table_tr_form('Corrosion Protection', radio_html('corrosion', $row['YEAR3_CORRPROT_PASSFAIL_FL'])) ?>
	<?= html::horiz_table_tr_form('Spill Containment', radio_html('spill', $row['YEAR3_SPILLCONT_PASSFAIL_FL'])) ?>
	<?= html::horiz_table_tr_form('Sump Containment',  radio_html('sump', $row['YEAR3_SUMPCONT_PASSFAIL_FL'])) ?>
	<?= html::horiz_table_tr_form('Comments', form::textarea(array('name'=>'comments', 'rows'=>5, 'cols'=>80, 'maxlength'=>500), $row['COMMENTS']) . 'max 500 chars') ?>
</table>
<?= form::submit('submit', 'Submit', 'class="ui-button ui-state-default ui-corner-all"') ?>
</fieldset>
</form>
