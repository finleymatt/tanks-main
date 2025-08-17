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
<h1>Inspection View</h1>
<table class="horiz_table ui-widget ui-corner-all" style="margin-bottom:30px">
	<caption><div class="left_float">Inspection</div><div class="right_float"><?= Controller::_instance('Inspection')->_edit_button($row['ID']) ?> <?= Controller::_instance('Inspection')->_delete_button($row['ID']) ?></div></caption>
	<?= html::horiz_table_tr('ID', $row['ID']) ?>
	<?= html::horiz_table_tr('Facility', html::facility_link($row['FACILITY_ID']), FALSE) ?>
	<?= html::horiz_table_tr('Date Inspected', $row['DATE_INSPECTED']) ?>
	<?= html::horiz_table_tr('Inspection', Model::instance('Inspection_codes')->get_lookup_desc($row['INSPECTION_CODE'])) ?>
	<?= html::horiz_table_tr('LCC/NOV Number', $row['NOV_NUMBER']) ?>
	<?= html::horiz_table_tr('Staff', $row['STAFF_CODE']) ?>
	<?= html::horiz_table_Tr('Certified Installer', Model::instance('Certified_installers')->get_certified_installer_by_id($row['CERTIFIED_INSTALLER_ID'])) ?>
	<?= html::horiz_table_tr('Case ID', $row['CASE_ID']) ?>
	<?= html::horiz_table_tr('Compliance Date', $row['COMPLIANCE_DATE']) ?>
	<?= html::horiz_table_tr('Compliance Order Issue Date', $row['COMPLIANCE_ORDER_ISSUE_DATE']) ?>
	<?= html::horiz_table_tr('Compliance Submit Date', $row['COMPLIANCE_SUBMIT_DATE']) ?>
	<?= html::horiz_table_tr('Conference?', text::yesno($row['CONFERENCE'])) ?>
	<?= html::horiz_table_tr('Conference Comments', $row['CONFERENCE_COMMENTS']) ?>
</table>

<h2>Penalties</h2>
<?= Controller::_instance('Penalty')->_add_button($row['ID'], 'add new Penalty') ?>
<table id="penalty_tabular" class="display" width="100%">
	<thead>
		<tr><th>PENALTY</th><th>NO OF OCCURRANCE</th><th>TANK ID</th><th>DATE CORRECTED</th><th>NOV DATE</th><th>NOD DATE</th><th>NOIRT B DATE</th><th>NOIRT A DATE</th><th>ACTION</th></tr>
	</thead>
	<tbody>
		<?= array_reduce($penalty_rows, 'display_penalty_row'); ?>
	</tbody>
</table>


<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		penalty_tabular_obj = $('#penalty_tabular').dataTable({
			"bJQueryUI": true, "sPaginationType": "full_numbers",
		});
	});
</script>


<?php

function display_penalty_row($result, $row) {
	if ($result == NULL) $result = '';

	$view_button =  Controller::_instance('Penalty')->_view_button(array($row['INSPECTION_ID'], $row['PENALTY_CODE'], $row['TANK_ID']));
	$edit_button =  Controller::_instance('Penalty')->_edit_button(array($row['INSPECTION_ID'], $row['PENALTY_CODE'], $row['TANK_ID']));
	$delete_button =  Controller::_instance('Penalty')->_delete_button(array($row['INSPECTION_ID'], $row['PENALTY_CODE'], $row['TANK_ID']));
	$penalty = Model::instance('Penalty_codes')->get_lookup_desc($row['PENALTY_CODE'], FALSE);
	$tank = Model::instance('Tanks')->get_lookup_desc($row['TANK_ID'], FALSE);
	$thirty_days_ago = date("Y-m-d", strtotime('-30 days'));
	$fifteen_days_ago = date("Y-m-d", strtotime('-15 days'));
	$nov_date = date("Y-m-d", strtotime($row['NOV_DATE']));
	$nod_date = date("Y-m-d", strtotime($row['NOD_DATE']));
	$noirt_b_date = $row['PENALTY_LEVEL'] == 'B' ? $row['NOIRT_DATE'] : "";
	$noirt_a_date = $row['PENALTY_LEVEL'] == 'A' ? $row['NOIRT_DATE'] : "";
	$nod_bg_color = '';
	$noirt_bg_color = '';
	$nod_text_color = '';
	$noirt_text_color = '';

	if(is_null($row['NOD_DATE'])){
		$nod_bg_color = '';
		$nod_text_color = '';
	}else if($nov_date <= $thirty_days_ago){ // 30 days after NOV date
		$nod_bg_color = '#ffc7ce';
		$nod_text_color = '#9c0006';
	}else if($nov_date > $thirty_days_ago && $nov_date <= $fifteen_days_ago){ // 15 days after & 30 days before NOV date
		$nod_bg_color = '#ffeb9c';
		$nod_text_color = '#9c5700';
	}

	if(is_null($row['NOIRT_DATE'])){
		$noirt_bg_color = '';
		$noirt_text_color = '';
	}else if($nod_date <= $thirty_days_ago){ // 30 days after NOD date
		$noirt_bg_color = '#ffc7ce';
		$noirt_text_color = '#9c0006';
	}else if($nod_date > $thirty_days_ago && $nod_date <= $fifteen_days_ago){ // 15 days after & 30 days before NOD date
		$noirt_bg_color = '#ffeb9c';
		$noirt_text_color = '#9c5700';
	}

	// convert '99999' and '01-JAN-68' to emapty and display
	if($row['PENALTY_OCCURANCE'] == '99999') $row['PENALTY_OCCURANCE'] = '';
	if($row['DATE_CORRECTED'] == '01-JAN-68') $row['DATE_CORRECTED'] = '';
	if($row['NOV_DATE'] == '01-JAN-68') $row['NOV_DATE'] = '';
	if($row['NOD_DATE'] == '01-JAN-68') $row['NOD_DATE'] = '';
	if($noirt_a_date == '01-JAN-68') $noirt_a_date = '';
	if($noirt_b_date == '01-JAN-68') $noirt_b_date = '';

	$result .= "<tr>
		<td>{$penalty}</td>
		<td>{$row['PENALTY_OCCURANCE']}</td>
		<td>{$tank}</td>
		<td>{$row['DATE_CORRECTED']}</td>
		<td>{$row['NOV_DATE']}</td>
		<td style='background-color:{$nod_bg_color};color:{$nod_text_color}'>{$row['NOD_DATE']}</td>"
		. "<td>{$noirt_b_date}</td>"
		. "<td>{$noirt_a_date}</td>"
		. "<td>{$view_button} {$edit_button} {$delete_button}</td>
		</tr>";
	return($result);
}

?>
