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

$invoice_url = url::fullpath("invoice/view/{$row['INVOICE_ID']}");
?>
<h1>Transaction View</h1>
<table class="horiz_table ui-widget ui-corner-all" style="margin-bottom:30px">
	<caption><div class="left_float">Transaction</div>
		<div class="right_float"><?= Controller::_instance('Transaction')->_edit_button($row['ID']) ?> <?= Controller::_instance('Transaction')->_delete_button($row['ID']) ?></div>
	</caption>
	<?= html::horiz_table_tr('ID', $row['ID']) ?>
	<?= html::horiz_table_tr('Owner', html::owner_link($row['OWNER_ID']), FALSE) ?>
	<?= html::horiz_table_tr('Invoice ID', "<a href='{$invoice_url}'>{$row['INVOICE_ID']}</a>", FALSE) ?>
	<?= html::horiz_table_tr('Trx Type', Model::instance('Transaction_codes')->get_lookup_desc($row['TRANSACTION_CODE'])) ?>
	<?= html::horiz_table_tr('Trx Status', $row['TRANSACTION_STATUS']) ?>
	<?= html::horiz_table_tr('Trx/Payment Date', $row['TRANSACTION_DATE_FMT']) ?>
	<?= html::horiz_table_tr('FY', $row['FISCAL_YEAR']) ?>
	<?= html::horiz_table_tr('Amount', format::currency($row['AMOUNT'])) ?>

	<?= html::horiz_table_tr('Check Number', $row['CHECK_NUMBER'], TRUE, array('th'=>'row_group_1')) ?>
	<?= html::horiz_table_tr('Name on Check', $row['NAME_ON_CHECK'], TRUE, array('th'=>'row_group_1')) ?>
	<?= html::horiz_table_tr('Deposit Date', $row['DEPOSIT_DATE_FMT'], TRUE, array('th'=>'row_group_1')) ?>
	<?= html::horiz_table_tr('Is Operator Payment?', $row['OPERATOR_PAYMENT'], TRUE, array('th'=>'row_group_1')) ?>
	<?= html::horiz_table_tr('Operator ID', $row['OPERATOR_ID'], TRUE, array('th'=>'row_group_1')) ?>
	<?= html::horiz_table_tr('Comments', $row['COMMENTS'], TRUE, array('th'=>'row_group_1')) ?>
	<?= html::table_foot_info($row) ?>
</table>

<table class="horiz_table ui-widget ui-corner-all" style="margin-bottom:30px">
	<caption><div class="left_float">Associated Penalty/Inspection</div></caption>
	<?= html::horiz_table_tr('Inspection ID', text::default_str($row['INSPECTION_ID'], 'none')) ?>
	<?= html::horiz_table_tr('NOV Number', text::default_str($inspection_row['NOV_NUMBER'], 'none')) ?>
	<?= html::horiz_table_tr('Facility',text::default_str(html::facility_link($inspection_row['FACILITY_ID']), 'none'), FALSE) ?>
</table>

