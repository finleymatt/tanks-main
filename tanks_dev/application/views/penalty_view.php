<?php
/**
 * Penalty View
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

<h1>Penalty View</h1>
<table class="horiz_table ui-widget ui-corner-all" style="margin-bottom:30px">
	<caption><div class="left_float">Penalty</div><div class="right_float"><?= Controller::_instance('Penalty')->_edit_button($row, FALSE) ?> <?= Controller::_instance('Penalty')->_delete_button(array($row['INSPECTION_ID'], $row['PENALTY_CODE'], $row['TANK_ID'])) ?></div></caption>
	<?php 
		$row['PENALTY_OCCURANCE'] = $row['PENALTY_OCCURANCE'] == '99999' ? '' : $row['PENALTY_OCCURANCE'];
		$row['LCC_DATE_FMT'] = $row['LCC_DATE_FMT'] == '01/01/1968' ? '' : $row['LCC_DATE_FMT'];
		$row['NOV_DATE_FMT'] = $row['NOV_DATE_FMT'] == '01/01/1968' ? '' : $row['NOV_DATE_FMT'];
		$row['NOD_DATE_FMT'] = $row['NOD_DATE_FMT'] == '01/01/1968' ? '' : $row['NOD_DATE_FMT'];
		$row['NOIRT_DATE_FMT'] = $row['NOIRT_DATE_FMT'] == '01/01/1968' ? '' : $row['NOIRT_DATE_FMT'];
		$row['REDTAG_PLACED_DATE_FMT'] = $row['REDTAG_PLACED_DATE_FMT'] == '01/01/1968' ? '' : $row['REDTAG_PLACED_DATE_FMT'];
		$row['DATE_CORRECTED_FMT'] = $row['DATE_CORRECTED_FMT'] == '01/01/1968' ? '' : $row['DATE_CORRECTED_FMT'];
	?>
	<?= html::horiz_table_tr('Inspection ID', $row['INSPECTION_ID']) ?>
	<?= html::horiz_table_tr('Penalty', Model::instance('Penalty_codes')->get_lookup_desc($row['PENALTY_CODE'], FALSE), FALSE) ?>
	<?= html::horiz_table_tr('Tank', Model::instance('Tanks')->get_lookup_desc($row['TANK_ID'], FALSE), FALSE) ?>
	<?= html::horiz_table_tr('No. of Occurrence', $row['PENALTY_OCCURANCE']) ?>
	<?= html::horiz_table_tr('USTR Number', $row['USTR_NUMBER']) ?>
	<?= html::horiz_table_tr('LCC Date', $row['LCC_DATE_FMT']) ?>
	<?= html::horiz_table_tr('Date NOV Issued', $row['NOV_DATE_FMT']) ?>
	<?= html::horiz_table_tr('Date NOD Issued', $row['NOD_DATE_FMT']) ?>
	<?= html::horiz_table_tr('Date NOIRT Issued', $row['NOIRT_DATE_FMT']) ?>
	<?= html::horiz_table_tr('Date Red Tag Placed', $row['REDTAG_PLACED_DATE_FMT']) ?>
	<?= html::horiz_table_tr('Date Corrected', $row['DATE_CORRECTED_FMT']) ?>
	<?= html::table_foot_info($row) ?>
</table>


