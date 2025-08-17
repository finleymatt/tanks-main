<?php
/**
 * Owner Waiver View
 *
 * Due to lack of unique identifier(s) in Owner_waivers table, the edit and delete
 * features are not displayed.
 *
 * <b>IMPORTANT NOTE</b>
 *
 * @package ### file docblock
 * @subpackage views
 * @uses ###
 * @see ###
 *
*/

?>
<h1>Owner Waiver View</h1>
<table class="horiz_table ui-widget ui-corner-all" style="margin-bottom:30px">
	<caption><div class="left_float">Waiver</div>
		<div class="right_float"></div>
		<div class="right_float"></div>
	</caption>
	<?= html::horiz_table_tr('Owner', html::owner_link($row['OWNER_ID']), FALSE) ?>
	<?= html::horiz_table_tr('Facility', html::facility_link($row['FACILITY_ID']), FALSE) ?>
	<?= html::horiz_table_tr('Tank ID', $row['TANK_ID']) ?>
	<?= html::horiz_table_tr('Waiver Code', Model::instance('Ust_ref_codes')->get_lookup_desc('OWNER_WAIVERS.WAIVER_CODE', $row['WAIVER_CODE'])) ?>
	<?= html::horiz_table_tr('FY', $row['FISCAL_YEAR']) ?>
	<?= html::horiz_table_tr('Amount', format::currency($row['AMOUNT'])) ?>
	<?= html::horiz_table_tr('Comments', $row['WAIVER_COMMENT']) ?>
	<?= html::table_foot_info($row) ?>
</table>

