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
?>
<h1>Permit View</h1>
<table class="horiz_table ui-widget ui-corner-all" style="margin-bottom:30px">
	<caption><div class="left_float">Permit</div><div class="right_float"><!--<?= Controller::_instance('Permit')->_edit_button(array($row['FACILITY_ID'], $row['OWNER_ID'], $row['FISCAL_YEAR'])) ?> <?= Controller::_instance('Permit')->_delete_button(array($row['FACILITY_ID'], $row['OWNER_ID'], $row['FISCAL_YEAR'])) ?>--></div></caption>
	<?= html::horiz_table_tr('Owner', html::owner_link($row['OWNER_ID']), FALSE) ?>
	<?= html::horiz_table_tr('Facility', html::facility_link($row['FACILITY_ID']), FALSE) ?>
	<?= html::horiz_table_tr('FY', $row['FISCAL_YEAR']) ?>
	<?= html::horiz_table_tr('Date Permitted', $row['DATE_PERMITTED']) ?>
	<?= html::horiz_table_tr('Date Printed', $row['DATE_PRINTED']) ?>
	<?= html::horiz_table_tr('Certificate (Permit#)', $row['PERMIT_NUMBER']) ?>
	<?= html::horiz_table_tr('Tank Count', $row['TANKS']) ?>
	<?= html::horiz_table_tr('AST Count', $row['AST_COUNT']) ?>
	<?= html::horiz_table_tr('UST Count', $row['UST_COUNT']) ?>
</table>


