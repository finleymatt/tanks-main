<?php
/**
 * A/B Operator Letter Menu
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

<h1>Letters for Requesting A/B Operator Info</h1>

<!-- batch status - refreshes every 120 seconds -->
<div id="batch_status"></div>
<script type="text/javascript">
function refresh_status() {
	var $batch_status = $("#batch_status");
	$batch_status.load("<?= url::fullpath('/ust_log/ab_operator_status') ?>");
}

$(document).ready(function() {
	refresh_status(); // initial load of status

	setInterval(function () {
		refresh_status();
	}, 120000);
});
</script>


<form id='single_form' action='<?= url::fullpath('/ab_operator/print_file/') ?>' class='validate_form' method='post' style='float:left; clear:left; margin-top:20px; margin-bottom:20px'>
	<fieldset class='ui-widget ui-widget-content ui-corner-all'>
	<legend class='ui-widget ui-widget-header ui-corner-all'>Single Letter</legend>

	<label>Owner ID:</label>
	<input name="owner_id" id="owner_id" value="<?= $owner_id ?>" class="ui-autocomplete validate[required]" />
	<br clear='all' />

	<input type="submit" value="Print Single" class="ui-button ui-state-default ui-corner-all" />
	</fieldset>
</form>


<div class="form_desc">
	<p style="margin-bottom:10px">This operation will create a &quot;request for A/B Operator info&quot; letter for the selected owner.<br /><br />
		The letter will be downloaded to your browser immediately.
	</p>
</div>

<!-- batch form =====================================================-->
<form id='batch_form' action='<?= url::fullpath('/ab_operator/print_all_action/') ?>' class='validate_form' method='post' style='float:left; clear:left; margin-top:20px; margin-bottom:20px'>
	<fieldset class='ui-widget ui-widget-content ui-corner-all'>
	<legend class='ui-widget ui-widget-header ui-corner-all'>All Letters</legend>

	<input type="button" id="print-button" name="batch" value="Print All Available" class="ui-button ui-state-default ui-corner-all" />
	</fieldset>
</form>
<div class="form_desc">
	<p style="margin-bottom:10px">This operation will create letters for any owners that have at least one active tank.<br /><br />
		This is a batch operation and will take about 20 minutes to complete.</p>
	<a href="<?= url::fullpath('/download/') ?>" target="download">Letters download folder</a>
</div>

<div id="print-confirm" title="Print All Letters?">
	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>This action will print all letters. Print may take a long time to complete.<br /><br />Are you sure?</p>
</div>

<script>
	$(function() {
		$("#owner_id").autocomplete({
			source: "<?= url::site() ?>index.php?owner/autocomplete&",
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

