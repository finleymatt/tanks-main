<?php
/**
 * Test Result View
 * 
 * <p>long description</p>
 *
 * @package ### file docblock
 * @subpackage views
 *
 */

?>

<h1>Test Result View</h1>
<table class="horiz_table ui-widget ui-corner-all" style="margin-bottom:30px">
	<caption><div class="left_float">Test Result</div><div class="right_float"><?= Controller::_instance('Test_result')->_edit_button($row['TEST_RESULTS_ID']) ?> 
	<?= Controller::_instance('Test_result')->_delete_button($row['TEST_RESULTS_ID']) ?></div></caption>
	<?= html::horiz_table_tr('Test ID', $row['TEST_RESULTS_ID']) ?>
	<?= html::horiz_table_tr('Facility ID', $row['FACILITY_ID']) ?>
	<?= html::horiz_table_tr('Tank ID', $row['TANK_ID']) ?>
	<?= html::horiz_table_tr('Tester', $row['TESTER_NAME']) ?>
	<?= html::horiz_table_tr('Test Company', $row['TESTING_COMPANY_NAME']) ?>
	<?= html::horiz_table_tr('Test Date', $row['TEST_DATE']) ?>
	<?= html::horiz_table_tr('Test Submitted Date', $row['TEST_SUBMITTED_DATE']) ?>
	<?= html::horiz_table_tr('Inspector', $row['INSPECTOR_NAME']) ?>
	<?= html::horiz_table_tr('ALLD Functionality', $row['ANNUAL_ALLD_FUNC_PASSFAIL_FL']) ?>
	<?= html::horiz_table_tr('Sensor Functioality', $row['ANNUAL_SENS_FUNC_PASSFAIL_FL']) ?>
	<?= html::horiz_table_tr('Line Tightness', $row['ANNUAL_LINE_TIGHT_PASSFAIL_FL']) ?>
	<?= html::horiz_table_tr('Tank Tightness', $row['ANNUAL_TANK_TIGHT_PASSFAIL_FL']) ?>
	<?= html::horiz_table_tr('ATG Test', $row['ANNUAL_AGT_TEST_PASSFAIL_FL']) ?>
	<?= html::horiz_table_tr('Overfill Functionality', $row['YEAR3_OVERFILL_PASSFAIL_FL']) ?>
	<?= html::horiz_table_tr('Corrosion Protection', $row['YEAR3_CORRPROT_PASSFAIL_FL']) ?>
	<?= html::horiz_table_tr('Spill Containment', $row['YEAR3_SPILLCONT_PASSFAIL_FL']) ?>
	<?= html::horiz_table_tr('Sump Containment', $row['YEAR3_SUMPCONT_PASSFAIL_FL']) ?>
	<?= html::horiz_table_tr('Violation(s) Issued', $row['VIOLATION_NOV']) ?>
	<?= html::horiz_table_tr('Original Failed Test ID if a Retest', $row['RETEST_OF_TEST_RESULT_ID']) ?>
	<?= html::horiz_table_tr('Comments', $row['COMMENTS']) ?>
	<?= html::table_foot_info($row) ?>
</table>
