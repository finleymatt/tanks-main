<?php
/**
 * Invoice Codes listing page -- where invoice text can be managed
 *
 * @package ### file docblock
 * @subpackage views
 * @uses ###
 * @see ###
 *
*/
?>

<h1>Manage Invoice Text</h1>

<table id="invoice_codes_tabular" class="display">
	<thead>
		<tr>
			<th>CODE</th><th>DESCRIPTION</th><th>COUPON TEXT</th><th>INVOICE TEXT</th><th>ACTION</th>
		</tr>
	</thead>
	<tbody>
		<?= array_reduce($rows, 'display_invoice_codes_row'); ?>
	</tbody>
</table>

<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		invoice_codes_tabular_obj = $('#invoice_codes_tabular').dataTable({
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"aoColumnDefs": [
				{ "aTargets":[4], "bSortable":false, "bSearchable":false }
			]
		});
	});
</script>

<?php
function display_invoice_codes_row($result, $row) {
	$edit_button =  Controller::_instance('Invoice_codes')->_edit_button($row['CODE']);
	$result .= "<tr>
		<td>{$row['CODE']}</td>
		<td>{$row['DESCRIPTION']}</td>
		<td>" . text::limit_chars($row['CUPON_FORMAT'], 300) . "</td>
		<td>" . text::limit_chars($row['INVOICE_TEXT'], 300) . "</td>
		<td>{$edit_button}</td>
		</tr>";
	return($result);
}
?>
