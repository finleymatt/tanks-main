<?php
/**
 * View template for A/B Operator
 *
 * <p>Display A/B Operator and associated Certificates</p>
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
<h1>A/B/C Operator View</h1>
<table class="horiz_table ui-widget ui-corner-all" style="margin-bottom:30px">
	<caption><div class="left_float">A/B/C Operator</div><div class="right_float"><?= Controller::_instance('Ab_operator')->_edit_button($row['ID']) ?></div></caption>
	<?= html::horiz_table_tr('ID', $row['ID']) ?>
	<!--<?= html::horiz_table_tr('Facility', html::facility_link($row['FACILITY_ID']), FALSE) ?>-->
	<?= html::horiz_table_tr('First Name', $row['FIRST_NAME']) ?>
	<?= html::horiz_table_tr('Last Name', $row['LAST_NAME']) ?>
	<?= html::table_foot_info($row) ?>
</table>

<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
	<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
		<li class="ui-state-default ui-corner-top"><a href="#tabs-ab_cert">A/B/C Certificates</a></li>
	</ul>

	<!-- ------------- A/B Certificates --------------------------------- -->
	<div id="tabs-ab_cert" class="ui-tabs-panel ui-widget-content ui-corner-bottom">
	<?= Controller::_instance('Ab_cert')->_add_button($row['ID'], 'add A/B/C Certificate') ?>
	<table id="ab_cert_tabular" class="display">
		<thead>
			<tr><th>CERT DATE</th><th>CERT LEVEL</th><th>CERT &#35;</th><th>CREATED BY</th><th>CREATED ON</th><th>UPDATED BY</th><th>UPDATED ON</th><th>ACTION</th></tr>
		</thead>
		<tbody>
			<?= array_reduce($ab_cert_rows, 'display_ab_cert_row'); ?>
		</tbody>
	</table>
	</div>
</div> <!-- end of tabs -->

<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		ab_cert_tabular_obj = $('#ab_cert_tabular').dataTable({
			"bJQueryUI": true, "sPaginationType": "full_numbers",
			"aoColumnDefs": [
				// due to nulls { "aTargets":[0], "sType":"oracle_date" },
				{ "aTargets":[3], "bSortable":false, "bSearchable":false }
			],
			"aaSorting": [[ 0, "desc" ]]
		});
	});
</script>


<?php

function display_ab_cert_row($result, $row) {
	if ($result == NULL) $result = '';

	$view_button = Controller::_instance('Ab_cert')->_view_button(array($row['ID']));
	$edit_button = Controller::_instance('Ab_cert')->_edit_button(array($row['ID']));
	$delete_button = Controller::_instance('Ab_cert')->_delete_button(array($row['ID']));
	$result .= "<tr>
		<td>{$row['CERT_DATE']}</td>
		<td>{$row['CERT_LEVEL']}</td>
		<td>{$row['CERT_NUM']}</td>
		<td>{$row['USER_CREATED']}</td>
		<td>{$row['DATE_CREATED']}</td>
		<td>{$row['USER_MODIFIED']}</td>
		<td>{$row['DATE_MODIFIED']}</td>
		<td>{$view_button} {$edit_button} {$delete_button}</td>
		</tr>";
	return($result);
}

?>
