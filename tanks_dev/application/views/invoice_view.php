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
<h1>Invoice View</h1>
<table class="horiz_table ui-widget ui-corner-all" style="margin-bottom:30px">
	<caption><div class="left_float">Invoice</div>
		<div class="right_float">
			<?= Controller::_instance('Invoice')->_delete_button($row['ID']) ?>
			<?= Controller::_instance('Invoice')->_print_button($row['ID']) ?>
		</div>
	</caption>
	<?= html::horiz_table_tr('Invoice ID', $row['ID']) ?>
	<?= html::horiz_table_tr('Owner', html::owner_link($row['OWNER_ID']), FALSE) ?>
	<?= html::horiz_table_tr('Inspection ID', $row['INSPECTION_ID']) ?>
	<?= html::horiz_table_tr('Invoice Code', $row['INVOICE_CODE']) ?>
	<?= html::horiz_table_tr('Invoice Status', $row['INVOICE_STATUS']) ?>
	<?= html::horiz_table_tr('Invoice Date', $row['INVOICE_DATE']) ?>
	<?= html::horiz_table_tr('Due Date', $row['DUE_DATE']) ?>
	<?= html::horiz_table_tr('Letter Date', $row['LETTER_DATE']) ?>
	<?= html::horiz_table_tr('NOV Number', $row['NOV_NUMBER']) ?>
	<?= html::horiz_table_tr('NOV GPA Facility', html::facility_link($row['NOV_GPA_FACILITY_ID']), FALSE) ?>
	<?= html::horiz_table_tr('NOV GPA Amount', $row['NOV_GPA_AMOUNT']) ?>
	<?= html::horiz_table_tr('NOV GPA FY', $row['NOV_GPA_FISCAL_YEAR']) ?>
	<?= html::table_foot_info($row) ?>
</table>

<h2>Fees</h2>
<table id="invoice_detail_tabular" class="display" width="100%">
	<thead>
		<tr><th rowspan="2">FY</th><th colspan="6" bgcolor="#5577CC">TANK</th><th colspan="5" bgcolor="#CC5577">LATE FEE</th><th colspan="5" bgcolor="#55CC77">INTEREST</th><th rowspan="2">YEAR TOTAL</th></tr>
		<tr><th>COUNT</th><th>FEE</th><th>PREV INVOICED</th><th>WAIVER</th><th>PAYMENT</th><th>BAL</th><th>FEE</th><th>PREV INVOICED</th><th>WAIVER</th><th>PAYMENT</th><th>BAL</th><th>FEE</th><th>PREV INVOICED</th><th>WAIVER</th><th>PAYMENT</th><th>BAL</th></tr>
	</thead>
	<tbody>
		<?= array_reduce($invoice_detail_rows, 'display_invoice_detail_row'); ?>
	</tbody>
</table>

<h2>Facilities and Tanks <?= (count($invoice_detail_rows) ? "for FY {$invoice_detail_rows[0]['FISCAL_YEAR']}" : '') ?></h2>
<table id="facility_tabular" class="display" width="100%">
	<thead>
		<tr><th>FACILITY ID</th><th>FACILITY</th><th>TANK COUNT</th></tr>
	</thead>
	<tbody>
		<?= array_reduce($facility_rows, 'display_facility_row'); ?>
	</tbody>
</table>


<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		invoice_detail_tabular_obj = $('#invoice_detail_tabular').dataTable({
			"bJQueryUI": true, "sPaginationType": "full_numbers",
			"aoColumnDefs": [
				{ "aTargets":[2,3,4,5,6, 7,8,9,10,11, 12,13,14,15,16, 17], "fnRender":currency_format, "bUseRendered":false }
			 ],
			"aaSorting": [[ 0, "desc" ]]
		});

		facility_tabular_obj = $('#facility_tabular').dataTable({
			"bJQueryUI": true, "sPaginationType": "full_numbers"
		});
	});
</script>

<?php
function display_invoice_detail_row($result, $row) {
	if ($result == NULL) $result = '';

	$result .= "<tr>
		<td>{$row['FISCAL_YEAR']}</td>
		<td>{$row['TANK_COUNT']}</td>
		<td>{$row['TANK_FEE']}</td>
		<td>{$row['TANK_FEE_INVOICED']}</td>
		<td>{$row['TANK_FEE_WAIVER']}</td>
		<td>{$row['TANK_FEE_PAYMENT']}</td>
		<td>{$row['TANK_FEE_BALANCE']}</td>

		<td>{$row['LATE_FEE']}</td>
		<td>{$row['LATE_FEE_INVOICED']}</td>
		<td>{$row['LATE_FEE_WAIVER']}</td>
		<td>{$row['LATE_FEE_PAYMENT']}</td>
		<td>{$row['LATE_FEE_BALANCE']}</td>

		<td>{$row['INTEREST']}</td>
		<td>{$row['INTEREST_INVOICED']}</td>
		<td>{$row['INTEREST_WAIVER']}</td>
		<td>{$row['INTEREST_PAYMENT']}</td>
		<td>{$row['INTEREST_BALANCE']}</td>

		<td>{$row['SUM_BALANCES']}</td>
		</tr>";
	return($result);
}

function display_facility_row($result, $row) {
	if ($result == NULL) $result = '';

	$result .= "<tr>
		<td>{$row['FACILITY_ID']}</td>
		<td>" . html::facility_link($row['FACILITY_ID']) . "</td>
		<td>{$row['TANK_COUNT']}</td>
		</tr>";
	return($result);
}
?>
