<?php
/**
 * Payment form for entering invoice payments from owners
 *
 * @package ### file docblock
 * @subpackage views
 * @uses ###
 * @see ###
 *
*/
?>
<h1>Tank Owner Payment</h1>

<h2>Step 1: Select Owner</h2>
<form action="<?= url::fullpath('/transaction/payment_add/') ?>" class="validate_form" method="post" style="float:left; clear:left; margin-top:20px; margin-bottom:20px">
	<fieldset class="ui-widget ui-widget-content ui-corner-all">
	<legend class="ui-widget ui-widget-header ui-corner-all">Owner</legend>

	<label>Owner:</label>
	<input name="owner_id" id="owner_id" value="<?= $owner_id ?>" class="ui-autocomplete validate[required,custom[integer]]" /><br clear="all" />
	<? if ($owner_id): ?>
		<div style="margin:10px">Selected owner: <?= html::owner_link($owner_id) ?></div>
	<? endif ?>
	
	<input type="submit" name="submit" value="Select" class="ui-button ui-state-default ui-corner-all" />
	</fieldset>
</form>

<? if ($owner_id && !$invoice_rows): ?>
	<div style="margin:60px; font-size:large">No outstanding invoices were found for this owner</div>
<? elseif ($invoice_rows): ?>
<form id="payment_form" action="<?= url::fullpath('/transaction/payment_add_action/', $owner_id) ?>" class="validate_form" method="post" style="float:left; clear:left; margin-top:20px; margin-bottom:20px">
	<h2>Step 2: Enter Payment Info</h2>
	<fieldset class="ui-widget ui-widget-content ui-corner-all">
	<legend class='ui-widget ui-widget-header ui-corner-all'>Payment</legend>

	<label>Payment Date:</label>
	<input type="text" name="transaction_date" id="transaction_date" size="15" class="datepicker validate[required,custom[date2]]"> mm/dd/yyyy<br clear="all" />
	<label>Deposit Date:</label>
	<input type="text" name="deposit_date" id="deposit_date" size="15" class="datepicker validate[required,custom[date2]]"> mm/dd/yyyy<br clear="all" />
	<label>Check or Transaction Number:</label>
	<input name="check_number" type="text" id="check_number" size="10" maxlength="25" class="validate[required]" /><br clear="all" />
	<label>Name on Check / Payor:</label>
	<input name="name_on_check" type="text" id="name_on_chck" size="30" maxlength="50" class="validate[required] capitalize" /><br clear="all" />
	<label>Payment Type: </label>
	<select name="payment_type_code" id="payment_type_code" class="validate[required]">
	<option value="" selected="">Select...</option>
	<?php
		foreach($payment_types as $payment_type) {
			echo '<option value="' . $payment_type['PAYMENT_TYPE_CODE'] . '">' . $payment_type['PAYMENT_TYPE_DESC'] . '</option>';
		}
	?>
	</select>
	<br clear="all" />	
	<label>Payment Amount:</label>
	$<input name="amount" type="text" id="amount" size="10" class="validate[required,custom[currency]]" />
	Remaining: $<span id="remaining"></span>
	<br clear="all" />

	<label>Paid by Operator</label>
	<input name="operator_id" id="operator_id" class="ui-autocomplete" /> if by operator, not owner<br clear='all' />
	<label>Comments</label>
	<textarea name="comments" id="comments" rows="3" cols="45" maxlength="240"></textarea>max 240 chars

	</fieldset>

	<h2>Step 3: Apply Payments to Invoice(s)</h2>
	<fieldset class='ui-widget ui-widget-content ui-corner-all'>
	<legend class='ui-widget ui-widget-header ui-corner-all'>Owner's Invoices</legend>
	<table class="simple_table">
		<thead>
			<tr>
				<th>FY</th><th>TYPE</th><th>INV#</th><th>NOV#</th><th>AMOUNT</th><th>PAYM. CODE</th><th>PAID</th>
			</tr>
		</thead>
		<tbody>
			<?= array_reduce($invoice_rows, 'display_invoice_row'); ?>
		</tbody>
	</table>
	<input type="button" id="enter-button" name="apply_payments" value="Apply Payment" class="ui-button ui-state-default ui-corner-all" />
	</fieldset>
</form>
<? endif ?>


<div id="enter-confirm" title="Enter Payment?">
	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>This action will create new payment for the selected owner. Are you sure?</p>
</div>
<script>
	$(function() {
		$("#owner_id").autocomplete({
			source: "<?= url::site() ?>index.php?owner/autocomplete&",
			minLength: 3
		});
		$("#operator_id").autocomplete({
			source: "<?= url::site() ?>index.php?operator/autocomplete&",
			minLength: 3
		});
	});

	$(function() {
		$("#enter-confirm").dialog({
			autoOpen:false, resizable:false, height:190, width:330, modal:true,
			buttons: {
				"Enter": function() {
					$( this ).dialog( "close" );
					$("#payment_form").submit();
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});

		$('#enter-button').click(function() {
			if (float_val($("#remaining").text()) < 0) {
				alert('You have assigned more money to "paid" than is available in "payment amount." Please correct before saving.');
				return(false);
			}

			$("#enter-confirm").dialog('open');
			return false;
		});
	});

	// onchange, calculate remaining amount with payments -------------------------
	$("#inv_paid, #amount").change(function() {
		total_paid = 0;  // will be a closure var
		$('input[id=inv_paid]').each(function( index ) {
			total_paid += float_val($(this).val())
		});

		amount = float_val($("#amount").val());

		remaining = amount - total_paid;
		$("#remaining").text(remaining.toFixed(2));
	});

	function float_val(val) {
		if (isNaN(val) || (jQuery.trim(val).length > 0))
			return(parseFloat(jQuery.trim(val)));
		else
			return(0);
	}
</script>

<?php

function display_invoice_row($result, $row) {
	if ($result == NULL) $result = '';

	$hidden_vals = form::hidden('inv_fiscal_year[]', $row['FISCAL_YEAR']) .
		form::hidden('inv_inspection_id[]', $row['INSPECTION_ID']) .
		form::hidden('inv_invoice_id[]', $row['INVOICE_ID']) .
		form::hidden('inv_amount[]', $row['AMOUNT']) .
		form::hidden('inv_payment_code[]', $row['PAYMENT_CODE']);

	return($result . "<tr>
		<td>{$row['FISCAL_YEAR']}</td>
		<td>{$row['TRANSACTION_TYPE']}</td>
		<td>{$row['INVOICE_ID']}</td>
		<td>{$row['NOV_NUMBER']}</td>
		<td>{$row['AMOUNT']}</td>
		<td>{$row['PAYMENT_CODE']}</td>
		<td>$<input type='text' name='inv_paid[]' id='inv_paid' size='10' class='validate[custom[currency]]'  /> {$hidden_vals}</td>
		</tr>");
}

?>
