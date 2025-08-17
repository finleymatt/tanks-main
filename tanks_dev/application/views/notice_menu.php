<?php
/**
 * Notice Menu
 *
 * @package ### file docblock
 * @subpackage views
 * @uses ###
 * @see ###
 *
*/
?>
<style>
	.form_desc { float:left; margin:50px; width:500px }
</style>

<h1>Tank Operator Notice</h1>

<!-- Find By ID form -->
<form action='<?= url::fullpath('/notice/view/') ?>' class='validate_form' method='post' style='float:left; clear:left; margin-top:20px; margin-bottom:20px'>
	<fieldset class='ui-widget ui-widget-content ui-corner-all'>
	<legend class='ui-widget ui-widget-header ui-corner-all'>Find by ID</legend>
	<label>Notice ID:</label>
	<input name='notice_id' id='notice_id' type='text' value='<?= $notice_id ?>' size='8' class='validate[required,custom[integer]]' /><br clear='all' />
	<input value="View" type="submit" class="ui-button ui-state-default ui-corner-all" />
	</fieldset>
</form>

<!-- Create and Print form -->
<? if ($model->check_priv('INSERT')) : ?>
<form id='add_notice_form' action='<?= url::fullpath('/notice/add_action/') ?>' class='validate_form' method='post' style='float:left; clear:left; margin-top:20px; margin-bottom:20px'>
	<fieldset class='ui-widget ui-widget-content ui-corner-all'>
	<legend class='ui-widget ui-widget-header ui-corner-all'>Create and Print</legend>

	<label>Operator ID:</label>
	<input name="operator_id" id="operator_id" value="<?= $operator_id ?>" class="ui-autocomplete validate[required]" /><br clear='all' />

	<label>Notice Code:</label>
	<?= form::dropdown('notice_code', Model::instance('Invoice_codes')->get_dropdown(), $notice_code, 'class="validate[required]"') ?><br clear='all' />

	<label>Fiscal Year:</label>
	<?= form::dropdown('fy', Model::instance('Fiscal_years')->get_dropdown(), $fy, 'class="validate[required]"') ?><br clear='all' />

	<label>Notice Date:</label>
	<input type='text' name='notice_date' id='notice_date' value="<?= $notice_date ?>" size='15' class='datepicker validate[required,custom[date2]]'> mm/dd/yyyy<br clear='all' />

	<input type="button" id="add-button" name="add-button" value="Add and Print" class="ui-button ui-state-default ui-corner-all" />
	</fieldset>
</form>
<div class="form_desc">
	<p style="margin-bottom:10px">This operation will create a new Operator Notice record in database and then create a PDF for print.<br /><br />  If you only need to print an existing Notice, <b>do not</b> use this feature.  Instead, locate the notice first, and then use 'print' feature there.</p>
</div>

<div id="add-confirm" title="Create New Notice?">
	<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>This action will generate new notice for the Operator selected.<br /><br />Are you sure?
</div>
<script>
	$(function() {
		$("#operator_id").autocomplete({
			source: "<?= url::site() ?>index.php?operator/autocomplete&",
			minLength: 3
		});

		// confirmation dialog actions -----------------------------------
		$("#add-confirm").dialog({
			autoOpen:false, resizable:false, height:210, width:330, modal:true,
			buttons: {
				"Add": function() {
					$( this ).dialog( "close" );
					 $("#add_notice_form").submit();
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});

		$('#add-button').click(function() {
			$("#add-confirm").dialog('open');
			return false;
		});
	});
</script>
<!-- end of add Notice form ====================================== -->

<? endif ?>
