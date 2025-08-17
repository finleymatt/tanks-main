<?php
/**
 * GPA Invoice menu
 *
 * @package ### file docblock
 * @subpackage views
 * @uses ###
 * @see ###
 *
*/
?>
<h1>GPA Invoice</h1>

<h2>Step 1: Select Owner</h2>
<form action="<?= url::fullpath('/invoice/gpa_menu/') ?>" class="validate_form" method="post" style="float:left; clear:left; margin-top:20px; margin-bottom:20px">
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

<!-- Step 2 =============================================================== -->
<? if ($owner_id && $is_valid_owner): ?>
	<div style="clear:left; margin-left:100px">

	<div style="float:left">
	<h2>Step 2: Create and Print new GPA Invoice</h2>
	<form id="gpa_invoice_form" action="<?= url::fullpath('/invoice/gpa_add_action/', $owner_id) ?>" class="validate_form" method="post">
	<fieldset class="ui-widget ui-widget-content ui-corner-all">
	<legend class='ui-widget ui-widget-header ui-corner-all'>Payment</legend>

	<label>Fiscal Year:</label>
	<?= form::dropdown('nov_gpa_fiscal_year', Model::instance('Fiscal_years')->get_dropdown(), NULL, 'class="validate[required]"') ?><br clear='all' />
	<label>Invoice Date:</label>
	<input type="text" name="invoice_date" id="invoice_date" size="15" class="datepicker validate[required,custom[date2]]"> mm/dd/yyyy<br clear="all" />
	<label>Due Date:</label>
	<input type="text" name="due_date" id="due_date" size="15" class="datepicker validate[required,custom[date2]]"> mm/dd/yyyy<br clear="all" />
	<label>Facility</label>
	<?= form::dropdown('nov_gpa_facility_id', Model::instance('Facilities_mvw')->get_dropdown(NULL, NULL, array('owner_id'=>$owner_id)), NULL, 'class="validate[required,custom[integer]]"') ?><br clear='all' />
	<label>Invoice Amount:</label>
	$<input name="nov_gpa_amount" type="text" id="nov_gpa_amount" size="10" class="validate[required,custom[integer]]" /><br clear="all" />
	<input type="button" id="create-button" name="" value="Create and Print" class="ui-button ui-state-default ui-corner-all" />
	</fieldset>
	</form>
	</div>

	<div style="float:left; padding:150px 50px; font-size:large">OR</div>

	<div style="float:left;">
	<h2>Step 2: Print previously created GPA Invoice</h2>

	<? if (!$invoice_rows): ?>
		<div style="margin:60px; font-size:large">No GPA invoice exist for this owner</div>
	<? else: ?>
		<fieldset class='ui-widget ui-widget-content ui-corner-all'>
		<legend class='ui-widget ui-widget-header ui-corner-all'>Owner's Existing GPA Invoices</legend>
		<table class="simple_table">
			<thead>
			<tr>
				<th>INVOICE DATE</th><th>INV#</th><th>AMOUNT</th><th></th>
			</tr>
			</thead>
			<tbody>
				<?= array_reduce($invoice_rows, 'display_invoice_row'); ?>
			</tbody>
		</table>
		</fieldset>
	<? endif ?>
	</div>

	</div><!-- end of all of Step 2 -->
<? endif ?>

<div id="create-confirm" title="Create GPA Invoice?">
	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>This action will create new GPA invoice for the selected owner. Are you sure?</p>
</div>
<script>
	$(function() {
		$("#owner_id").autocomplete({
			source: "<?= url::site() ?>index.php?owner/autocomplete&",
			minLength: 3
		});
	});

	$(function() {
		$("#create-confirm").dialog({
			autoOpen:false, resizable:false, height:190, width:330, modal:true,
			buttons: {
				"Create": function() {
					$( this ).dialog( "close" );
					$("#gpa_invoice_form").submit();
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});

		$('#create-button').click(function() {
			$("#create-confirm").dialog('open');
			return false;
		});
	});
</script>

<?php

function display_invoice_row($result, $row) {
	if ($result == NULL) $result = '';

	$print_button = Controller::_instance('Invoice')->_print_button($row['ID']);
	return($result . "<tr>
		<td>{$row['INVOICE_DATE']}</td>
		<td>{$row['ID']}</td>
		<td>{$row['NOV_GPA_AMOUNT']}</td>
		<td>{$print_button}</td>
		</tr>");
}

?>
