<?php
/**
 * Tank Detail Edit and Add form
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
<h1>Tank Detail Add/Edit</h1>

<div style='float:left;'>
<form action="<?= $action ?>" method="post" class="validate_form edit_form">
<fieldset class='ui-widget ui-widget-content ui-corner-all' style="width:630px">
<legend class='ui-widget ui-widget-header ui-corner-all'>Tank Detail</legend>
<table class="horiz_table ui-widget ui-corner-all" style="width:625px">
	<?= html::horiz_table_tr('TANK ID', $tank_id) ?>

<?php foreach ($tank_info_code_rows as $j => $tank_info_code_row) {
	$dropdown_rows = $tank_detail_codes->get_dropdown(NULL, "'(' || CODE || ') ' || DESCRIPTION", array('TANK_INFO_CODE'=>$tank_info_code_row['CODE']));
	if (count($dropdown_rows)) { // some have none
		$html = '';
		foreach($dropdown_rows as $id => $desc) {
			if ($tank_info_code_row['CODE'] == 'U') // USAGE not required
				$html .= form::checkbox(array('name' => "tank_detail_code_{$j}[]", 'id' => "tdc_{$id}", 'class' => 'tdc_checkbox'), html::h($id), in_array($id, $tank_detail_vals));
			else
				$html .= form::checkbox(array('name' => "tank_detail_code_{$j}[]", 'id' => "tdc_{$id}", 'class' => 'tdc_checkbox validate[required]'), html::h($id), in_array($id, $tank_detail_vals));
			$html .= html::h($desc) . "<br />\n";
		}
		echo(html::horiz_table_tr_form($tank_info_code_row['DESCRIPTION'], $html, TRUE));
	}
} ?>

</table>
<?= form::submit('submit', 'Submit', 'class="ui-button ui-state-default ui-corner-all"') ?>
</fieldset>
</form>
</div>

<!-- GoNM tank detail code rules:
	-code longer than it could be due to using jquery's dialog pop-up
	-rules come from tank detail model and are translated to json here -->
<div style='float:left; margin-left:190px'>
	Following GoNM rules apply to tank detail codes:<div style="margin-left:50px; margin-top:10px">
	<? foreach($gonm_rules['triggers'] as $rule): ?>
		<li><?= $rule['message'] ?></li>
	<? endforeach; ?>

	<? foreach($gonm_rules['exclusions'] as $rule): ?>
		<li>Invalid combination: <?= implode(', ', $rule) ?></li>
	<? endforeach; ?></div>
	</div>
</div>

<div id="rule-dialog" title="GoNM Rule Applied"></div>

<script type="text/javascript"> 
	var rules = <?= json_encode($gonm_rules) ?>;

	$("#rule-dialog").dialog({
		autoOpen:false, resizable:false, height:220, width:420, modal:true,
		modal: true,
		buttons: {
			Ok: function() { $(this).dialog('close'); }
		}
	});

	// GoNM tank detail codes rules checks --------------------------------
	$(".tdc_checkbox").change( function () {
		var clicked_val = $(this).val();

		if (! $(this).attr('checked'))
			return;

		// GoNM exclusion rules for disallowing certain combinations ----------
		for (var rule_idx=0; rule_idx<rules['exclusions'].length; rule_idx++) {
			var tdc_set = rules['exclusions'][rule_idx];

			if (! in_array(clicked_val, tdc_set))
				continue;

			// loop and check if TDC set is all checked by user
			var violated = true;
			for (var tdc_idx=0; tdc_idx<tdc_set.length; tdc_idx++) {
				if (! $("#tdc_" + tdc_set[tdc_idx]).attr('checked'))
					violated = false;
			}

			if (violated) {
				$(this).prop('checked', false); // uncheck box
				rule_alert('Selected combination of tank detail codes is not allowed: ' + tdc_set);
				return;  // do not continue rule checking
			}
		}

		// trigger rules for auto-setting tank detail codes -----
		var triggers = rules['triggers'];
		for (var index in triggers) {
			to_set_id = '#tdc_' + triggers[index]['set'];
			if ((clicked_val == index) && (! $(to_set_id).attr('checked'))) {
				$(to_set_id).prop('checked', true);
				rule_alert('<b>' + triggers[index]['set'] + '</b> has been auto-selected due to rule:<br /><br />' + triggers[index]['message']);

			}
		}
	});
	

	function rule_alert(message) {
		$("#rule-dialog").html('<p>' + message + '</p>');
		$("#rule-dialog").dialog('open');
	}

	function in_array(value, arr) {
		return(arr.indexOf(value) !== -1 ? true : false);
	}
</script>
