<?php
/**
 * Suspected Release View
 *
 * <p>long description</p>
 *
 * @package ### file docblock
 * @subpackage views
 * @uses ###
 * @see ###
 *
*/

?>

<h1>Suspected Release View</h1>
<table class="horiz_table ui-widget ui-corner-all" style="margin-bottom:30px">
	<caption><div class="left_float">Suspected Release</div></caption>
	<?php 
		/*$row['PENALTY_OCCURANCE'] = $row['PENALTY_OCCURANCE'] == '99999' ? '' : $row['PENALTY_OCCURANCE'];
		$row['LCC_DATE_FMT'] = $row['LCC_DATE_FMT'] == '01/01/1968' ? '' : $row['LCC_DATE_FMT'];
		$row['NOV_DATE_FMT'] = $row['NOV_DATE_FMT'] == '01/01/1968' ? '' : $row['NOV_DATE_FMT'];
		$row['NOD_DATE_FMT'] = $row['NOD_DATE_FMT'] == '01/01/1968' ? '' : $row['NOD_DATE_FMT'];
		$row['NOIRT_DATE_FMT'] = $row['NOIRT_DATE_FMT'] == '01/01/1968' ? '' : $row['NOIRT_DATE_FMT'];
		$row['REDTAG_PLACED_DATE_FMT'] = $row['REDTAG_PLACED_DATE_FMT'] == '01/01/1968' ? '' : $row['REDTAG_PLACED_DATE_FMT'];
		$row['DATE_CORRECTED_FMT'] = $row['DATE_CORRECTED_FMT'] == '01/01/1968' ? '' : $row['DATE_CORRECTED_FMT'];*/
	?>
	<?= html::horiz_table_tr('SCSR ID', $row['SUSPECTED_RELEASE_ID']) ?>
	<?= html::horiz_table_tr('Facility ID', $row['FACILITY_ID']) ?>
	<?= html::horiz_table_tr('Tank ID', $row['TANK_ID_STRING']) ?>
	<?= html::horiz_table_tr('Date Discovered', $row['DATE_DISCOVERED']) ?>
	<?= html::horiz_table_tr('Date Reported', $row['DATE_REPORTED']) ?>
	<?= html::horiz_table_tr('Sourcing', $row['SOURCE_DESC']) ?>
	<?= html::horiz_table_tr('Cause', $row['CAUSE_DESC']) ?>
	<?= html::horiz_table_tr('SCSR Letter Mailed', $row['SCSR_LETTER_MAILED_DATE']) ?>
	<?= html::horiz_table_tr('7-Day Rpt Submitted', $row['SEVEN_DAY_REPORT_SUMBIT_DATE']) ?>
	<?= html::horiz_table_tr('Date System Test', $row['SYSTEM_TEST_DATE']) ?>
	<?= html::horiz_table_tr('Date Closed', $row['CLOSED_DATE']) ?>
	<?= html::horiz_table_tr('NFA Letter Date', $row['NFA_LETTER_DATE']) ?>
	<?= html::horiz_table_tr('Approved Alt Report Date', $row['APPROVED_ALT_REPORT_DATE']) ?>
	<?= html::horiz_table_tr('Date Confirmed', $row['CONFIRMED_DATE']) ?>
	<?= html::horiz_table_tr('Date Referred', $row['REFERRED_DATE']) ?>
	<?= html::horiz_table_tr('Comments', $row['COMMENTS']) ?>
	<?php html::table_foot_info($row) ?>
</table>


