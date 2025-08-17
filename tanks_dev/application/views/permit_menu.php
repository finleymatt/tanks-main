<?php
/**
 * Permit Menu
 *
 * @package ### file docblock
 * @subpackage views
 * @uses ###
 * @see ###
 *
*/
?>
<style>
	.form_desc { float:left; margin:40px; width:500px }
</style>

<h1>Registration Certificate</h1>

<!-- permit status - refreshes every 120 seconds -->
<div id="permit_status"></div>
<script type="text/javascript">
function refresh_status() {
	var $permit_status = $("#permit_status");
	$permit_status.load("<?= url::fullpath('/ust_log/permit_status') ?>");
}

$(document).ready(function() {
	refresh_status(); // initial load of status

	setInterval(function () {
		refresh_status();
	}, 120000);
});
</script>


<? if ($model->check_priv('INSERT')) : ?>
<form id='single_form' action='<?= url::fullpath('/permit/print_action/') ?>' class='validate_form' method='post' style='float:left; clear:left; margin-top:20px; margin-bottom:20px'>
	<fieldset class='ui-widget ui-widget-content ui-corner-all'>
	<legend class='ui-widget ui-widget-header ui-corner-all'>Single Certificate</legend>

	<label>Owner ID:</label>
	<input name="owner_id" id="owner_id" class="ui-autocomplete validate[required]" />
	<br clear='all' />

	<label>Facility ID:</label>
	<input name="facility_id" id="facility_id" class="ui-autocomplete" />
	<br clear='all' />

	<label>Fiscal Year:</label>
	<?= form::dropdown('fy', Model::instance('Fiscal_years')->get_dropdown(), NULL, 'class="validate[required]"') ?><br clear='all' />

	<label>Date Permitted:</label>
	<input type='text' name='date_permitted' id='date_permitted' size='15' class='datepicker validate[required,custom[date2]]'> mm/dd/yyyy<br clear='all' />

	<input type="submit" value="Create Single" class="ui-button ui-state-default ui-corner-all" />
	</fieldset>
</form>


<div class="form_desc">
	<p style="margin-bottom:10px">This operation will update an existing certificate for the facility if it already exists for the selected FY. If not, it will create a new one.<br /><br />
	Either way, the "Date Permitted" record in database will be updated with the date entered.<br /><br />
	This method will always print a certificate if it exists.
	</p>
</div>

<!-- batch form =====================================================-->
<form id='batch_form' action='<?= url::fullpath('/permit/print_all_action/') ?>' class='validate_form' method='post' style='float:left; clear:left; margin-top:20px; margin-bottom:20px'>
	<fieldset class='ui-widget ui-widget-content ui-corner-all'>
	<legend class='ui-widget ui-widget-header ui-corner-all'>Batch Certificates - All Facilities</legend>

	<label>Fiscal Year:</label>
	<?= form::dropdown('fy', Model::instance('Fiscal_years')->get_dropdown(), NULL, 'class="validate[required]"') ?><br clear='all' />

	<input type="button" id="print-button" name="batch" value="Create All Available" class="ui-button ui-state-default ui-corner-all" />
	</fieldset>
</form>
<div class="form_desc">
	<p style="margin-bottom:10px">This operation will affect only the certificates already created -- created either indirectly during invoice generation, or directly by single certificate creation.<br /><br />
	The "Date Permitted" record in database will be updated with the date entered.<br /><br />
	This method only prints the certificates that have not been printed before.
	</p>

	<a href="<?= url::fullpath('/download/') ?>" target="download">Certificate download folder</a>
</div>

<div id="print-confirm" title="Batch Print Certificates?">
	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>This action will print all certificates for the selected FY. Print may take a long time to complete.<br /><br />Are you sure?</p>
</div>

<script>
	$(function() {
		$("#owner_id").autocomplete({
			source: "<?= url::site() ?>index.php?owner/autocomplete&",
			minLength: 3
		});

		$("#facility_id").autocomplete({
			source: "<?= url::site() ?>index.php?facility/autocomplete&",
			minLength: 3
		});

		// batch confirmation codes -----------------------------------
		$("#print-confirm").dialog({
			autoOpen:false, resizable:false, height:230, width:350, modal:true,
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

		$('#print-button').click(function() {
			$("#print-confirm").dialog('open');
			return false;
		});
	});
</script>
<!-- end of Batch Operation form ====================================== -->

<? endif // has permission ?>
