<?php
/**
 * Operator view
 *
 * @package ### file docblock
 * @subpackage views
 * @uses ###
 * @see ###
 *
*/
?>
<h1>Operator View</h1>
<table class="horiz_table ui-widget ui-corner-all" style="margin-bottom:30px">
	<caption><div class="left_float">Operator</div></caption>
	<?= html::horiz_table_tr('Operator ID', $row['ID']) ?>
	<?= html::horiz_table_tr('Name', $row['OPERATOR_NAME']) ?>
	<?= html::horiz_table_tr('Address Line 1', $row['ADDRESS1']) ?>
	<?= html::horiz_table_tr('Address Line 2', $row['ADDRESS2']) ?>
	<?= html::horiz_table_tr('City', $row['CITY']) ?>
	<?= html::horiz_table_tr('State', $row['STATE']) ?>
	<?= html::horiz_table_tr('Zip', $row['ZIP']) ?>
	<?= html::horiz_table_tr('Phone', $row['PHONE_NUMBER']) ?>
	<?= html::table_foot_info($row) ?>
</table>

<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
	<ul>
		<li><a href="#tabs-facility">Facilities</a></li>
		<li><a href="#tabs-notice">Notices</a></li>
	</ul>

	<!-- -------------- Facilities ------------------ -->
	<div id="tabs-facility">
	<table id="facility_tabular" class="display">
		<thead>
			<tr><th>ID</th><th>FACILITY</th><th>TANK COUNT</th><th>ACTION</th></tr>
		</thead>
		<tbody>
			<?= array_reduce($facility_rows, 'display_facility_row'); ?>
		</tbody>
	</table>
	</div>

	<!-- -------------- Notices ------------------ -->
	<div id="tabs-notice">
	<table id="notice_tabular" class="display">
		<thead>
			<tr><th>NOTICE CODE</th><th>NOTICE DATE</th><th>LETTER DATE</th><th>ACTION</th></tr>
		</thead>
		<tbody>
			<?= array_reduce($notice_rows, 'display_notice_row'); ?>
		</tbody>
	</table>
	<!-- not used often - will be created later <?= Controller::_instance('Notice')->_add_button($row['ID'], 'add new Notice') ?> -->
	</div>

</div> <!-- end of tabs -->

<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		facility_tabular_obj = $('#facility_tabular').dataTable({
			"bJQueryUI": true, "sPaginationType": "full_numbers",
			"aoColumnDefs": [
				{ "aTargets":[3], "bSearchable":false, "bSortable":false }
			]
		});

		notice_tabular_obj = $('#notice_tabular').dataTable({
			"bJQueryUI": true, "sPaginationType": "full_numbers",
			"aoColumnDefs": [
				{ "aTargets":[3], "bSearchable":false, "bSortable":false },
				{ "aTargets":[1,2], "sType":"oracle_date" }
			 ]
		});
	});
</script>

<?php

function display_facility_row($result, $row) {
	$view_button =  Controller::_instance('Facility')->_view_button($row, FALSE);
	$result .= "<tr>
		<td>{$row['ID']}</td>
		<td>{$row['FACILITY_NAME']}</td>
		<td>{$row['TANK_COUNT']}</td>
		<td>{$view_button}</td>
		</tr>";
	return($result);
}

function display_notice_row($result, $row) {
	$view_button = Controller::_instance('Notice')->_view_button($row['ID']);
	$edit_button = Controller::_instance('Notice')->_edit_button($row['ID']);
	$delete_button = Controller::_instance('Notice')->_delete_button($row['ID']);
	$print_button = Controller::_instance('Notice')->_print_button($row['ID']);
	$result .= "<tr>
		<td>{$row['NOTICE_CODE']}</td>
		<td>{$row['NOTICE_DATE']}</td>
		<td>{$row['LETTER_DATE']}</td>
		<td>{$view_button} {$delete_button} {$print_button}</td>
		</tr>";
	return($result);
}

?>
