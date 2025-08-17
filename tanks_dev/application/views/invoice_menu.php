<?php
/**
 * Invoice Menu
 *
 * @package ### file docblock
 * @subpackage views
 * @uses ###
 * @see ###
 *
*/
?>
<h1>Tank Owner Invoice</h1>

<style>
	#worksheet { padding:10px; margin:10px; }
	#worksheet caption { font-size:16px; }
	#worksheet th { min-width:90px; }
	.print { display:none } /* will only show when in print */
</style>

<!-- invoice status - refreshes every 120 seconds -->
<div id="invoice_status"></div>
<script type="text/javascript">
function refresh_status() {
	var $invoice_status = $("#invoice_status");
	$invoice_status.load("<?= url::fullpath('/ust_log/invoice_status') ?>");
}

$(document).ready(function() {
	refresh_status(); // initial load of status

	setInterval(function () {
		refresh_status();
	}, 120000);
});
</script>


<!-- Find By ID form -->
<form action='<?= url::fullpath('/invoice/view/') ?>' class='validate_form' method='post' style='float:left; clear:left; margin-top:20px; margin-bottom:20px'>
	<fieldset class='ui-widget ui-widget-content ui-corner-all'>
	<legend class='ui-widget ui-widget-header ui-corner-all'>Find by ID</legend>
	<label>Invoice ID:</label>
	<input name='invoice_id' id='invoice_id' type='text' value='<?= $invoice_id ?>' size='8' class='validate[required,custom[integer]]' /><br clear='all' />
	<input value="View" type="submit" class="ui-button ui-state-default ui-corner-all" />
	</fieldset>
</form>

<!-- Batch Operations form -->
<? if ($model->check_priv('INSERT')) : ?>
<div style="clear:both">Hover over the labels for descriptions</div>
<form id='batch_form' action='<?= url::fullpath('/invoice/batch_action/') ?>' class='validate_form' method='post' style='float:left; clear:left; margin-top:20px; margin-bottom:20px'>
	<fieldset class='ui-widget ui-widget-content ui-corner-all'>
	<legend class='ui-widget ui-widget-header ui-corner-all'>Invoice Operations</legend>

	<label title="Enter ID for a single owner invoice.">Owner ID:</label>
	<input name="owner_id" id="owner_id" value="<?= $owner_id ?>" class="ui-autocomplete" />
        leave blank for all<br clear='all' />

	<label title="FY for which you want the invoice generated/printed.">Fiscal Year:</label>
	<?= form::dropdown('fy', Model::instance('Fiscal_years')->get_dropdown(), $fy, 'class="validate[required]"') ?><br clear='all' />

	<label title="For this invoice, rewinds (or fast forwards) the system's date to this date. Usually can be left to be today's date.">Invoice Date:</label>
	<input type='text' name='invoice_date' id='invoice_date' value="<?= $invoice_date ?>" size='15' class='datepicker validate[required,custom[date2]]'> mm/dd/yyyy<br clear='all' />

	<label title="Late fees will be accessed after this date.">Due Date:</label>
	<input type='text' name='due_date' id='due_date' value="<?= $due_date ?>" size='15' class='datepicker validate[required,custom[date2]]'> mm/dd/yyyy<br clear='all' />

	<fieldset style="margin-top:8px; padding:1px; height:65px"><legend>Multiple Owners Option</legend>
	<label title="">Print Invoies for:</label>
	<select id="print_option" name="print_option">
		<option value="all_fys">All owners</option>
		<option value="selected_fy">Owners owing for selected FY</option>
		<option value="prior_fys">Owners owing for prior FYs</option>
		<option value="selected_prior1_fy">Owners owing for selected FY and prior 1 FY</option>
	</select>
	</fieldset><br />

	<select name="action" id="action">
		<option value="print">Print PDF Only</option>
		<!--<option value="gen">Generate DB Record</option>-->
		<option value="gen-print">Generate and Print</option>
	</select>
	<input type="button" id="invoice-button" name="batch" value="Run" class="ui-button ui-state-default ui-corner-all" />
	</fieldset>
</form>
<div id="print-confirm" title="Print Invoice(s)?">
	<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>This action will print invoices for the Owner(s) selected.<br /><br />If you did not specify Owner, all Owners will be printed.<br /><br />No data will be modified.<br /><br />Continue?
</div>
<div id="gen-print-confirm" title="Generate and Print Invoice(s)?">
	<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>This action will generate invoices and print PDFs for the Owner(s) selected.<br /><br />If you did not specify Owner, all Owners will be generated and printed.<br /><br />Continue?
</div>
<script>
$(function() {
	$("#owner_id").autocomplete({
		source: "<?= url::site() ?>index.php?owner/autocomplete&",
		minLength: 3
	});

	$("#owner_id").bind('change', function(event){
		if ($(this).val())
			$("#print_option").attr("disabled", true);
		else
			$("#print_option").attr("disabled", false);
	});
	$("#owner_id").trigger('change'); // to init on load

	// batch confirmation codes -----------------------------------
	$("#print-confirm").dialog({
		autoOpen:false, resizable:false, height:260, width:400, modal:true,
		buttons: {
			"Print": function() {
				$( this ).dialog( "close" );
				 $("#batch_form").submit();
			},
			Cancel: function() {
				$( this ).dialog( "close" );
			}
		}
	});

	$("#gen-print-confirm").dialog({
		autoOpen:false, resizable:false, height:250, width:380, modal:true,
		buttons: {
			"Generate and Print": function() {
				$( this ).dialog( "close" );
				 $("#batch_form").submit();
			},
			Cancel: function() {
				$( this ).dialog( "close" );
			}
		}
	});

	$('#invoice-button').click(function() {
		if ($("#action").val() == 'print')
			$("#print-confirm").dialog('open');
		else
			$("#gen-print-confirm").dialog('open');
			
		return false;
	});
});
</script>
<!-- end of Batch Operation form ====================================== -->

<div style='float:left; margin:50px; font-size:11pt'><a href="<?= url::fullpath('/download/') ?>" target="download">Invoices download folder</a> - download recent invoices</div>
<? endif ?>
<br clear="all" />

<!-- Invoice Calculation Worksheet ==================================== -->
<div style="left:clear; float:left;">
<table id="worksheet" border=1>
<caption>Invoice Calculation Worksheet</caption>
<thead>
<tr>
	<th>Tank Count</th><th>FY</th><th>Late Fee</th><th>Interest</th><th>Principal</th><th>FY Total</th>
</tr>
</thead>

<tbody>
<!-- no charges for FYs 1981 to 1992 -->
<?php
$j = 0;
foreach($fy_int_rates as $fy => $int) {
	echo("
		<tr>
		<td><input type='text' size='2' id='r{$j}' index='{$j}' fy='{$fy}' is_active='1' class='tank_count no_print' value='' /><span id='hidden{$j}' class='print'>0</span></td>
		<td id='r{$j}_fy'>{$fy}</td>
		<td id='r{$j}_fy_lf' raw='0'>$0.00</td>
		<td id='r{$j}_fy_i' raw='0'>$0.00</td>
		<td id='r{$j}_fy_p' raw='0'>$0.00</td>
		<td id='r{$j}_fy_tot' raw='0'>$0.00</td>
		</tr>");
	$j++;
}
?>
</tbody>

<tfoot>
<tr>
	<td colspan="2">Totals:</td>
	<td id="rtotal_fys_lf">$0.00</td>
	<td id="rtotal_fys_i">$0.00</td>
	<td id="rtotal_fys_p">$0.00</td>
	<td id="rtotal_fys_tot">$0.00</td>
</tr>
</tfoot>
</table>
<input type="button" value="Print Worksheet" onclick="javascript:print_div('worksheet');" />
</div>

<script type="text/javascript">
function money_round(amount) {
	return(Math.round(amount * 100) / 100);
}

$(document).ready(function() {
	// TODO: create money class that uses integer instead of floats
	jQuery.fn.extend({
		// methods for other objects -----------------------------------------
		get_obj: function(cell_id, index) {
			if (index == undefined)
				row_id = $(this).attr('id');
			else
				row_id = 'r' + index;
			
			return($('#' + row_id + '_' + cell_id));
		},
		
		get_sum: function(cell_id, from, to) {
			if (from == undefined) from = 0;
			if (to == undefined) to = <?= count($fy_int_rates) - 1 ?>;
			if (to < 0) to = 0;

			var sum = 0;
			for (j = from; j <= to; j++) {
				obj = $(this).get_obj(cell_id, j);
				if (obj.length)
					sum += obj.get_val();
			}

			return(sum);
		},
		
		// methods for this object -------------------------------------------
		// returns dollar amounts without formatting
		get_val: function() {
			if ($(this).attr('raw') != undefined)
				return(parseFloat($(this).attr('raw')));
				//return(parseFloat($(this).html().replace('$', '')));
			else
				return(0);
		},
		
		set_amt: function(amount) {
			amount = amount.toFixed(2);
			$(this).html('$' + amount.replace(/(\d)(?=(\d{3})+\.)/g, '$1,')); // add commas
			$(this).attr('raw', amount);  // raw value w/o formatting for calculations
		}
	});
	
	$('.tank_count').bind('change keyup', function(event){
		var index = parseInt($(this).attr('index'));
		var tank_count = ((isNaN($(this).val()) || ($(this).val().trim() == ''))
			? 0 : parseInt($(this).val()));
		var fy = parseInt($(this).attr('fy'));

		// hidden tank count for display in print ---------------------
		$("#hidden" + index).html(tank_count);
		
		// principal --------------------------------------------------
		var fy_p = 100 * tank_count;
		$(this).get_obj('fy_p').set_amt(fy_p);

		// late fee ---------------------------------------------------
		if (fy <= 2002)  // 5% of principal
			fy_lf = tank_count * 5;
		else  // 25% of the prev FY balance
			fy_lf = money_round((($(this).get_sum('fy_tot', 0, index-1) + fy_p) * 0.25));
		
		$(this).get_obj('fy_lf').set_amt(fy_lf);
		
		// FY interest -------------------------------------------------
		var fy_i =  money_round(fy_p * parseFloat(fy_int_rates[fy]));
		$(this).get_obj('fy_i').set_amt(fy_i);
		
		// FY total --------------------------------------------------
		$(this).get_obj('fy_tot').set_amt(fy_p + fy_lf + fy_i);
		
		// trigger change event for next FY tank count -----------------------
		//alert('#r' + (parseInt($(this).attr('id').replace('r', '')) +1));
		var next_tc = $('#r' + (index + 1));
		if (next_tc.length) {
			next_tc.trigger('change');
		}
		else {
			// grand totals --------------------------------------------------
			$(this).get_obj('fys_lf', 'total').set_amt($(this).get_sum('fy_lf'));
			$(this).get_obj('fys_i', 'total').set_amt($(this).get_sum('fy_i'));
			$(this).get_obj('fys_p', 'total').set_amt($(this).get_sum('fy_p'));
			$(this).get_obj('fys_tot', 'total').set_amt($(this).get_sum('fy_tot'));
		}
	});

	var fy_int_rates = <?= json_encode($fy_int_rates) ?>;  // fy:int_rate
});
</script>

<!-- Invoice rules ==================================================== -->
<div style='float:left; padding:10px; margin:10px'>
<h3>Invoice Generation Rules</h3>
<h4>Principal Assessment:</h4>
<li>No fees for FYs 1981 to 1992.</li>
<li>$100 is charged for each tank per FY.</li>
<li>Emergency Generator tanks do not get charged for FYs 2002 to 2012.</li>
<li>A tank in a sale transaction gets charged a tank fee for that FY only towards the seller, not the purchaser.</li>
<h4>Late Fee Assessment:</h4>
<li>No fees for FYs 1981 to 1992.</li>
<li>For FYs 1979 to 2002, late fee per tank is $5.</li>
<li>For FYs starting from 2003, late fee per tank is 25% of the remaining balance.</li>
<h4>Interest Assessment:</h4>
<li>No fees for FYs 1981 to 1992.</li>
<li>For FYs 1979 to 2002, interest for late payments are assessed at 1.5% per month.</li>
<li>For FYs starting from 2003, there are no interest fees.</li>
</div>

