<?php
/**
 * file docblock; short description
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
<h1>Tank View</h1>
<table class="horiz_table ui-widget ui-corner-all" style="margin-bottom:30px">
	<caption><div class="left_float">Tank</div><div class="right_float"><?= Controller::_instance('Tank')->_edit_button($row['ID']) ?></div></caption>
	<?= html::horiz_table_tr('ID', $row['ID']) ?>
	<?= html::horiz_table_tr('Owner', html::owner_link($row['OWNER_ID']), FALSE) ?>
	<?= html::horiz_table_tr('Facility', html::facility_link($row['FACILITY_ID']), FALSE) ?>
	<?= html::horiz_table_tr('Operator', html::operator_link($row['OPERATOR_ID']), FALSE) ?>
	<?= html::horiz_table_tr('Tank Type', $row['TANK_TYPE']) ?>
	<?= html::horiz_table_tr('Registration &#35;', $row['REGISTRATION_NUMBER']) ?>
	<?= html::horiz_table_tr('Status', Model::instance('Tank_status_codes')->get_lookup_desc($row['TANK_STATUS_CODE'])) ?>
	<?= html::horiz_table_tr('Status Date', $row['STATUS_DATE']) ?>
	<?= html::horiz_table_tr('Capacity', $row['CAPACITY']) ?>
	<?= html::horiz_table_tr('Meets 1988 Req?', text::yesno($row['MEETS_1988_REQ'])) ?>
	<?= html::horiz_table_tr('Meets 2011 Req?', text::yesno($row['MEETS_2011_REQ'])) ?>
	<?= html::horiz_table_tr('Move to Duplicate Facility?', text::yesno($row['MOVE_2_DUP'])) ?>	
	<?= html::horiz_table_tr('Comments', $row['COMMENTS']) ?>
</table>

<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
	<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
		<li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a href="#tabs-detail">Details</a></li>
		<li class="ui-state-default ui-corner-top"><a href="#tabs-histories">Tank Histories</a></li>
	</ul>

	<!-- ------------- Details ---------------------------------------- -->
	<div id="tabs-detail" class="ui-tabs-panel ui-widget-content ui-corner-bottom">
	<?= Controller::_instance('Tank_detail')->_add_button($row['ID'], 'add/edit Tank Details') ?>
	<table id="detail_tabular" class="display">
		<thead>
			<tr><th>TYPE</th><th>DETAIL</th><th>UPDATED BY</th><th>UPDATED ON</th></tr>
		</thead>
		<tbody>
			<?= array_reduce($tank_detail_rows, 'display_detail_row'); ?>
		</tbody>
	</table>
	</div>

	<!-- ------------- Tank Histories (owner and operator) ----------------- -->
	<div id="tabs-histories" class="ui-tabs-panel ui-widget-content ui-corner-bottom">

	<!-- Owner History -->
	<h3>Owner History</h3>
	<?= Controller::_instance('Tank_history')->_add_button(array($row['ID'], $row['OWNER_ID']), 'add Tank Owner History') ?>
	<table id="owner_history_tabular" class="display">
		<thead>
			<tr><th>HISTORY DATE</th><th>HISTORY</th><th>OWNER</th><th>CREATED BY</th><th>CREATED ON</th><th>UPDATED BY</th><th>UPDATED ON</th><th>ACTION</th></tr>
		</thead>
		<tbody>
			<?= array_reduce($tank_history_rows, 'display_owner_history_row'); ?>
		</tbody>
	</table>

	<!-- Operator History -->
	<h3>Operator History</h3>
	<?= Controller::_instance('Tank_operator_history')->_add_button($row['ID'], 'add Tank Operator History') ?>
	<table id="operator_history_tabular" class="display">
		<thead>
			<tr><th>START DATE</th><th>END DATE</th><th>OPERATOR</th><th>CREATED BY</th><th>CREATED ON</th><th>UPDATED BY</th><th>UPDATED ON</th><th>ACTION</th></tr>
		</thead>
		<tbody>
			<?= array_reduce($tank_operator_history_rows, 'display_operator_history_row'); ?>
		</tbody>
	</table>

	<!-- Equipment History -->
	<h3>Equipment History</h3>
	<?= Controller::_instance('Tank_equipment_history')->_add_button($row['ID'], 'add Tank Equipment History') ?>
	<table id="equipment_history_tabular" class="display">
		<thead>
			<tr><th>DETAIL</th><th>HISTORY</th><th>HISTORY DATE</th><th>CREATED BY</th><th>CREATED ON</th><th>UPDATED BY</th><th>UPDATED ON</th><th>ACTION</th></tr>
		</thead>
		<tbody>
			<?= array_reduce($tank_equipment_history_rows, 'display_equipment_history_row'); ?>
		</tbody>
	</table>
	</div>
</div> <!-- end of tabs -->

<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		detail_tabular_obj = $('#detail_tabular').dataTable({
			"bJQueryUI": true, "sPaginationType": "full_numbers"
		});

		owner_history_tabular_obj = $('#owner_history_tabular').dataTable({
			"bJQueryUI": true, "sPaginationType": "full_numbers",
			"aoColumnDefs": [
				//{ "aTargets":[0], "sType":"oracle_date" }
			],
			"aaSorting": [[ 0, "desc" ]]
		});

		operator_history_tabular_obj = $('#operator_history_tabular').dataTable({
			"bJQueryUI": true, "sPaginationType": "full_numbers",
			"aoColumnDefs": [
				//{ "aTargets":[0,1], "sType":"oracle_date" }
			],
			"aaSorting": [[ 0, "desc" ]]
		});

		equipment_history_tabular_obj = $('#equipment_history_tabular').dataTable({
			"bJQueryUI": true, "sPaginationType": "full_numbers",
			"aoColumnDefs": [
			],
			"aaSorting": [[ 0, "desc" ]]
		});
	});
</script>


<?php

function display_detail_row($result, $row) {
	if ($result == NULL) $result = '';

	$edit_button = Controller::_instance('Tank_detail')->_edit_button(array($row['TANK_ID'], $row['TANK_DETAIL_CODE']));
	$delete_button = Controller::_instance('Tank_detail')->_delete_button(array($row['TANK_ID'], $row['TANK_DETAIL_CODE']));
	$tank_detail_type = Model::instance('Tank_info_codes')->get_lookup_desc($row['TANK_INFO_CODE']);
	$tank_detail = Model::instance('Tank_detail_codes')->get_lookup_desc($row['TANK_DETAIL_CODE']);
	$result .= "<tr>
		<td>{$tank_detail_type}</td>
		<td>{$tank_detail}</td>
		<td>{$row['USER_CREATED']}</td>
		<td>{$row['DATE_CREATED']}</td>
		</tr>";
	return($result);
}

function display_owner_history_row($result, $row) {
	if ($result == NULL) $result = '';

	// owner_id is not part of pk, but appended for foreign key info ------
	$edit_button = Controller::_instance('Tank_history')->_edit_button(array($row['TANK_ID'], $row['OWNER_ID'], $row['HISTORY_DATE_KEY'], $row['HISTORY_CODE']));
	$delete_button = Controller::_instance('Tank_history')->_delete_button(array($row['TANK_ID'], $row['OWNER_ID'], $row['HISTORY_DATE_KEY'], $row['HISTORY_CODE']));
	$history = Model::instance('Tank_history_codes')->get_lookup_desc($row['HISTORY_CODE']);
	$owner = html::owner_link($row['OWNER_ID']);
	$result .= "<tr>
		<td>{$row['HISTORY_DATE_FMT']}</td>
		<td>{$history}</td>
		<td>{$owner}</td>
		<td>{$row['USER_CREATED']}</td>
		<td>{$row['DATE_CREATED']}</td>
		<td>{$row['USER_MODIFIED']}</td>
		<td>{$row['DATE_MODIFIED']}</td>
		<td>{$edit_button} {$delete_button}</td>
		</tr>";
	return($result);
}

function display_operator_history_row($result, $row) {
	if ($result == NULL) $result = '';

	$edit_button = Controller::_instance('Tank_operator_history')->_edit_button(array($row['TANK_ID'], $row['OPERATOR_ID'], $row['START_DATE_KEY']));
	$delete_button = Controller::_instance('Tank_operator_history')->_delete_button(array($row['TANK_ID'], $row['OPERATOR_ID'], $row['START_DATE_KEY']));
	$operator = html::operator_link($row['OPERATOR_ID']);
	$result .= "<tr>
		<td>{$row['START_DATE_FMT']}</td>
		<td>{$row['END_DATE_FMT']}</td>
		<td>{$operator}</td>
		<td>{$row['USER_CREATED']}</td>
		<td>{$row['DATE_CREATED']}</td>
		<td>{$row['USER_MODIFIED']}</td>
		<td>{$row['DATE_MODIFIED']}</td>
		<td>{$edit_button} {$delete_button}</td>
		</tr>";
	return($result);
}

function display_equipment_history_row($result, $row) {
	if ($result == NULL) $result = '';
	$edit_button = Controller::_instance('Tank_equipment_history')->_edit_button(array($row['TANK_ID'], $row['HISTORY'], $row['HISTORY_DATE']));
	$delete_button = Controller::_instance('Tank_equipment_history')->_delete_button(array($row['TANK_ID'], $row['HISTORY'], $row['HISTORY_DATE']));
	$equipment = Model::instance('Tank_detail_codes')->get_lookup_desc($row['TANK_DETAIL_CODE']);

	$result .= "<tr>
		<td>{$equipment}</td>
		<td>{$row['HISTORY']}</td>
		<td>{$row['HISTORY_DATE']}</td>
		<td>{$row['USER_CREATED']}</td>
		<td>{$row['DATE_CREATED']}</td>
		<td>{$row['USER_MODIFIED']}</td>
		<td>{$row['DATE_MODIFIED']}</td>
		<td>{$edit_button} {$delete_button}</td>
		</tr>";
	return($result);
}
?>
