<?php
/**
 * Violation view
 *
 * @package ### file docblock
 * @subpackage views
 * @author george.huang
 *
*/
?>
<h1>Violation View</h1>
<table class="horiz_table ui-widget ui-corner-all" style="margin-bottom:30px;float:left;margin-right:100px">
	<caption><div class="left_float">Facility</div></caption>
	<?= html::horiz_table_tr('Facility ID', $row['ID']) ?>
	<?= html::horiz_table_tr('Agency Interest ID', $row['AI_ID']) ?>
	<?= html::horiz_table_tr('Name', $row['FACILITY_NAME'], FALSE) ?>
	<?= html::horiz_table_tr('Address Line 1', $row['ADDRESS1']) ?>
	<?= html::horiz_table_tr('Address Line 2', $row['ADDRESS2']) ?>
	<?= html::horiz_table_tr('City', $row['CITY']) ?>
	<?= html::horiz_table_tr('State', $row['STATE']) ?>
	<?= html::horiz_table_tr('Zip', $row['ZIP']) ?>
	<?= html::horiz_table_tr('Is LUST Site?', text::yesno($row['IS_LUST'])) ?>
	<?= html::horiz_table_tr('On Native Am. Land?', text::yesno($row['INDIAN'])) ?>
	<?= html::horiz_table_tr('Assigned Inspector', $assigned_inspector['FULL_NAME']
		. Controller::_instance('Entity_details')->_add_edit_button($assigned_inspector['ID'], $row['ID'], 'facility', 'assigned_inspector')
		, FALSE) ?>
	<?= html::table_foot_info($row) ?>
</table>

<table class="horiz_table ui-widget ui-corner-all" style="margin-bottom:30px;float:left;margin-right:100px">
	<caption><div class="left_float">Owner</div></caption>
	<?= html::horiz_table_tr('Owner ID', $owner_row['ID']) ?>
	<?= html::horiz_table_tr('Person ID', $owner_row['PER_ID']) ?>
	<?= html::horiz_table_tr('Organization ID', $owner_row['ORG_ID']) ?>
	<?= html::horiz_table_tr('Name', html::owner_link($row['OWNER_ID'], ''), FALSE) ?>
	<?= html::horiz_table_tr('Address Line 1', $owner_row['ADDRESS1']) ?>
	<?= html::horiz_table_tr('Address Line 2', $owner_row['ADDRESS2']) ?>
	<?= html::horiz_table_tr('City', $owner_row['CITY']) ?>
	<?= html::horiz_table_tr('State', $owner_row['STATE']) ?>
	<?= html::horiz_table_tr('Zip', $owner_row['ZIP']) ?>
	<?= html::horiz_table_tr('Phone', $owner_row['PHONE_NUMBER']) ?>
	<?= html::table_foot_info($owner_row) ?>
</table>

<table class="horiz_table ui-widget ui-corner-all" style="margin-bottom:30px">
	<caption><div class="left_float">Operator</div></caption>
	<?= html::horiz_table_tr('Operator ID', $operator_row['ID']) ?>
	<?= html::horiz_table_tr('Name', html::operator_link($operator_row['ID'], ''), FALSE) ?>
	<?= html::horiz_table_tr('Address Line 1', $operator_row['ADDRESS1']) ?>
	<?= html::horiz_table_tr('Address Line 2', $operator_row['ADDRESS2']) ?>
	<?= html::horiz_table_tr('City', $operator_row['CITY']) ?>
	<?= html::horiz_table_tr('State', $operator_row['STATE']) ?>
	<?= html::horiz_table_tr('Zip', $operator_row['ZIP']) ?>
	<?= html::horiz_table_tr('Phone', $operator_row['PHONE_NUMBER']) ?>
	<?= html::table_foot_info($operator_row) ?>
</table>

<div style="clear:both">
<?php
if (count($active_penalties))
	echo('This Facility has outstanding Violation. Please review and notify A/B Operator(s).');
?>
</div>

<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all fade-in">
	<ul>
		<li><a href="#tabs-inspection">Inspections</a></li>
		<li><a href="#tabs-tank">Tanks</a></li>
		<li><a href="#tabs-ab_op">A/B/C Operator</a></li>
	</ul>
	
	<!-- -------------- Inspection ------------------ -->
	<div id="tabs-inspection">
	<?= Controller::_instance('Inspection')->_add_button($row['ID'], 'add new Inspection') ?>
	<table id="inspection_tabular" class="display">
		<thead>
			<tr id="first_row"><th>DATE INSPECTED</th><th>INSPECTION</th><th>NOV/LCC NUMBER</th><th>STAFF</th><th>NOD DATE</th><th>NOIRT B DATE</th><th>NOIRT A DATE</th><th>Penalties</th><th width="140px">ACTION</th><th>Downloads</th></tr>
		</thead>
		<tbody>
			<?= array_reduce($inspection_rows, 'display_inspection_row'); ?>
		</tbody>
		<tfoot id="inspection_footer">
			<tr><th>DATE INSPECTED</th><th>INSPECTION</th><th>NOV/LCC NUMBER</th><th>STAFF</th><th>NOIRT B DATE</th><th>NOIRT A DATE</th><th>NOD DATE</th></tr>
		</tfoot>
	</table>
	</div>

	<!-- -------------- Tank ------------------ -->
	<div id="tabs-tank">
	<?= Controller::_instance('Tank')->_add_button(array($row['ID'], $row['OWNER_ID']), 'add new Tank') ?>
	<table id="tank_tabular" class="display">
		<thead>
			<tr><th>ID</th><th width="170px">OWNER</th><th>TYPE</th><th>STATUS</th><th>STATUS DATE</th><th>NOV DATE</th><th>NOD DATE</th><th>RED TAG DATE</th><th>RED TAG REMOVED DATE</th><th width="160px">COMMENTS</th><th>Inspections</th><th>ACTION</th></tr>
		</thead>
		<tbody>
			<?= array_reduce($tank_rows, 'display_tank_row'); ?>
		</tbody>
		<tfoot id="tank_footer">
			<tr><th>ID</th><th width="170px">OWNER</th><th>TYPE</th><th>STATUS</th><th>STATUS DATE</th><th>NOV DATE</th><th>NOD DATE</th><th>RED TAG DATE</th><th>RED TAG REMOVED DATE</th><th width="160px">COMMENTS</th>
		</tfoot>
	</table>
	</div>

	<!-- -------------- A/B/C Ops ------------------ -->
	<div id="tabs-ab_op">

	<h3>Tank Operators</h3>
	<?= Controller::_instance('Ab_operator')->_add_button(array($row['ID']), 'add new A/B Operator') ?>
	<div style="float:left; padding-left:70px;"><?= Controller::_instance('Ab_operator')->add_c_button(array($row['ID'])) ?></div>
	<table id="ab_op_tabular" class="display">
		<thead>
			<tr><th>ID</th><th>FIRST NAME</th><th>LAST NAME</th><th>CERT LEVEL</th><th>CERT EXPIRES ON</th><th width="120px">ACTION</th></tr>
		</thead>
		<tbody>
			<?= array_reduce($ab_op_rows, 'display_ab_op_row'); ?>
		</tbody>
	</table>

	<h3>Reported Tank Status</h3>
	<?= Controller::_instance('Ab_tank_status')->_add_button(array($row['ID']), 'add new Tank Status') ?>
	<table id="ab_tank_status_tabular" class="display">
		<thead>
			<tr><th>ID</th><th>TANK STATUS CODE</th><th>TANK LAST USED</th><th>TANK STATUS NOTE</th><th>DATE CREATED</th><th width="120px">ACTION</th></tr>
		</thead>
		<tbody>
			<?= array_reduce($ab_tank_status_rows, 'display_ab_tank_status_row'); ?>
		</tbody>
	</table>
	</div>
</div> <!-- end of tabs -->

<!-- -------------- Penalties popup window (only show penalties with Penalty Level equals to 'A' or 'B')------------------ -->
<div id="penalty_modal" title="Penalties (only level A & B)">
	<table id="penalty_tabular_modal" class="display" style="width:1050px">
		<thead>
			<!-- <tr><th>Inspection ID</th><th>Penalty Code</th><th>SOC Category</th><th>DP Category</th><th>NOV Date</th><th>NOD Date</th><th>NOIRT Date</th></tr> -->
			<tr><th>NOV/LCC Number</th><th>Tank ID</th><th>Penalty Code</th><th>Penalty Level</th><th>Date Corrected</th><th>NOV Date</th><th>LCAV Date</th><th>NOD Date</th><th>NOIRT B Date</th><th>NOIRT A Date</th><th>NRTPID Date</th></tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>

<!-- -------------- Inspections popup window ------------------ -->
<div id="inspection_modal" title="Inspections">
	<table id="inspection_tabular_modal" class="display" style="width:900px">
		<thead>
			<tr><th>Inspection ID</th><th>NOV Number</th><th>NOIRT B Date</th><th>NOIRT A Date</th></tr>
		</thead>
		<tbody>
			<?= array_reduce($inspection_rows, 'display_inspection_modal'); ?>
		</tbody>
	</table>
</div>

<div id="hidden_items">
	<input type="hidden" name="penalty_json" id="penalty_json" value='<?php echo $penalty_json; ?>'>
</div>

<script>
	$( function() {
		// inspection modal
		$( "#inspection_modal" ).dialog({
			autoOpen: false,
			height: 'auto',
			width: 'auto',
			resizable: false,
			modal: true,
			open: function(){
				$('.ui-widget-overlay').bind('click',function(){
					$('#inspection_modal').dialog('close');
				});
			}
		});

		// penalty modal
		$( "#penalty_modal" ).dialog({
			autoOpen: false,
			height: 'auto',
			width: 'auto',
			resizable: false,
			modal: true,
			open: function(){
				$('.ui-widget-overlay').bind('click',function(){
					$('#penalty_modal').dialog('close');
				});
			}
		});

		$( ".open_inspection" ).bind( "click", function() {
			$( "#inspection_modal" ).dialog( "open" );
		});

		$( ".open_penalty" ).bind( "click", function() {
			var open_penalty_id = this.id;
			var inspection_id = open_penalty_id.substring(open_penalty_id.lastIndexOf('_')+1);
			var penalty_array = JSON.parse($('#penalty_json').val());

			// datatables 1.8 equivalent of penalty_tabular_obj.clear().destroy()/datatables 1.10
			penalty_tabular_obj.fnClearTable();
			penalty_tabular_obj.fnDestroy();
			$('#penalty_tabular_modal tbody').empty();

			$.each(penalty_array, function(key, value) {
				if(inspection_id == value.INSPECTION_ID){console.log(value);
					var ins_id = (value.INSPECTION_ID == null) ? "" : value.INSPECTION_ID;
					var nov_lcc_number = (value.NOV_NUMBER == null) ? "" : value.NOV_NUMBER;
					var penalty_code = (value.PENALTY_CODE == null) ? "" : value.PENALTY_CODE;
					var penalty_level = value.PENALTY_LEVEL;
					var tank_id = (value.TANK_ID == null) ? "" : value.TANK_ID;
					var date_corrected = (value.DATE_CORRECTED == null || value.DATE_CORRECTED == '01-JAN-68') ? "" : value.DATE_CORRECTED;
					var nov_date = (value.NOV_DATE == null || value.NOV_DATE == '01-JAN-68') ? "" : value.NOV_DATE;
					var nod_date = (value.NOD_DATE == null || value.NOD_DATE == '01-JAN-68') ? "" : value.NOD_DATE;
					var noirt_date = (value.NOIRT_DATE == null || value.NOIRT_DATE == '01-JAN-68') ? "" : value.NOIRT_DATE;
					var dp_catgory = (value.DP_CATEGORY == null) ? "" : value.DP_CATEGORY;
					var soc_category = (value.SOC_CATEGORY == null) ? "" : value.SOC_CATEGORY;
					var penalty_html = '<tr>'
					penalty_html += '<td>' + nov_lcc_number  + '</td>';
					penalty_html += '<td>' + tank_id + '</td>';
					penalty_html += '<td>' + penalty_code + '</td>';
					penalty_html += '<td>' + penalty_level  + '</td>';
					penalty_html += '<td>' + date_corrected + '</td>';
					penalty_html += '<td>' + nov_date + '</td>';
					penalty_html += '<td></td>';
					penalty_html += '<td>' + nod_date + '</td>';
					penalty_html += '<td></td>';
					penalty_html += '<td></td>';
					penalty_html += '<td></td>';
					penalty_html += '</tr>'	
				
					$('#penalty_tabular_modal tbody').append(penalty_html);
				}
			});

			$('#penalty_tabular_modal').dataTable({
				"bJQueryUI": true, "sPaginationType": "full_numbers",
				"aoColumnDefs": [
					{ "aTargets":[2,3,4], "sType":"oracle_date" }
				],
				"aaSorting": [[ 0, "desc" ]]
			});

			$( "#penalty_modal" ).dialog( "open" );
		});

		// change downloads drop down options, only show the selected download button
		$('.select_letter').change(function() {
			var inspection_id = this.id.substring(this.id.lastIndexOf('_')+1);
			letter_id = $(this).val() + '_' + inspection_id;
console.log(letter_id);
			$(".NOD_" + inspection_id).hide();
			$(".NOIRTA_" + inspection_id).hide();
			$(".NOIRTB_" + inspection_id).hide();
			$(".RTPIDA_" + inspection_id).hide();
			$(".RTPIDB_" + inspection_id).hide();
			$(".STANDARD_NOV_" + inspection_id).hide();
			$(".ELEVATED_NOV_" + inspection_id).hide();
			$(".NOMV_" + inspection_id).hide();
			$("." + letter_id).show();
		});
	});

	$(document).ready(function() {
		// Setup - add a text input to each footer cell in inspection table
		$('#inspection_tabular tfoot th').each( function () {
			$(this).html( '<input type="text" placeholder="Filter" style="width:80%" />' );
		} );

		penalty_tabular_obj = $('#penalty_tabular_modal').dataTable({
			"bJQueryUI": true, "sPaginationType": "full_numbers",
			"aoColumnDefs": [
				{ "aTargets":[3,4,5], "sType":"oracle_date" }
			],
			"aaSorting": [[ 1, "desc" ]]
		});
		
		inspection_modal_tabular_obj = $('#inspection_tabular_modal').dataTable({
			"bJQueryUI": true, "sPaginationType": "full_numbers",
			"aoColumnDefs": [
				{ "aTargets":[2,3], "sType":"oracle_date" }
			],
			"aaSorting": [[ 0, "desc" ]]
		});

		inspection_tabular_obj = $('#inspection_tabular').dataTable({
			"bJQueryUI": true, "sPaginationType": "full_numbers",
			"aoColumnDefs": [
				{ "aTargets":[8,9], "bSearchable":false, "bSortable":false },
				{ "aTargets":[0,1,5,6,7], "sType":"oracle_date" }
			],
			"aaSorting": [[ 0, "desc" ]]
		});

		$("#inspection_footer input").keyup( function () {
			inspection_tabular_obj.fnFilter( this.value, $("#inspection_footer input").index(this) );
		} );

		// Setup - add a text input to each footer cell in tanks table
		$('#tank_tabular tfoot th').each( function () {
			$(this).html( '<input type="text" placeholder="Filter" style="width:80%" />' );
		} );

		tank_tabular_obj = $('#tank_tabular').dataTable({
			"bJQueryUI": true, "sPaginationType": "full_numbers",
			"aoColumnDefs": [
				{ "aTargets":[4], "bSortable":false, "bSearchable":false }
			]
		});
		
		$("#tank_footer input").keyup( function () {
			tank_tabular_obj.fnFilter( this.value, $("#tank_footer input").index(this) );
		} );

		ab_op_tabular_obj = $('#ab_op_tabular').dataTable({
			"bJQueryUI": true, "sPaginationType": "full_numbers",
			"aoColumnDefs": [
				{ "aTargets":[4], "bSortable":false, "bSearchable":false }
			]
		});
		
		ab_tank_status_obj = $('#ab_tank_status_tabular').dataTable({
			"bJQueryUI": true, "sPaginationType": "full_numbers",
			"aoColumnDefs": [
				{ "aTargets":[5], "bSortable":false, "bSearchable":false }
			]
		});
	});
</script>

<?php

function display_inspection_modal($result, $row) {
	$result .= "<tr>
		<td>{$row['ID']}</td>
		<td>{$row['NOV_NUMBER']}</td>
		<td></td>
		<td></td>
		</tr>";
	return $result;
}

function display_inspection_row($result, $row) {
	$view_button = Controller::_instance('Inspection')->_view_button($row['ID']);
	$edit_button = Controller::_instance('Inspection')->_edit_button($row['ID']);
	$delete_button = Controller::_instance('Inspection')->_delete_button($row['ID']);
	$inspection = Model::instance('Inspection_codes')->get_lookup_desc($row['INSPECTION_CODE']);
	
	// temporarily change '01-JAN/68' to empty string, will fix after UDAPI can accept empty array
	$nod_date = $row['NOD_DATE'] == '01-JAN-68' ? '' : $row['NOD_DATE'];
	$noirt_date = $row['NOIRT_DATE'] == '01-JAN-68' ? '' : $row['NOIRT_DATE'];
	$noirta_date = $row['PENALTY_LEVEL'] == 'A' ? $noirt_date : '';
	$noirtb_date = $row['PENALTY_LEVEL'] == 'B' ? $noirt_date : '';

	$result .= "<tr>
		<td>{$row['DATE_INSPECTED']}</td>
		<td>{$inspection}</td>
		<td>{$row['NOV_NUMBER']}</td>
		<td>{$row['STAFF_CODE']}</td>
		<td>{$nod_date}</td>
		<td>{$noirtb_date}</td>
		<td>{$noirta_date}</td>
		<td><button id='open_penalty_" . $row['ID']  .  "' class='open_penalty' style='background-color:#D1E6F6;border-radius:5px;display:inline-block;cursor:pointer'>Penalties</button></td>
		<td>{$view_button} {$edit_button} {$delete_button}</td>
		<td>"
		. "<select id='select_letter_" . $row['ID'] . "' class='select_letter' style='margin-bottom:10px;margin-top:10px'>"
		. "<option value='NOD'>NOD</option>"
		. "<option value='NOIRTA'>NOIRTA</option>"
		. "<option value='NOIRTB'>NOIRTB</option>"
		. "<option value='RTPIDA'>RTPIDA</option>"
		. "<option value='RTPIDB'>RTPIDB</option>"
		. "<option value='STANDARD_NOV'>Standard NOV</option>"
		. "<option value='ELEVATED_NOV'>Elevated NOV</option>"
		. "<option value='NOMV'>NOMV</option>"
		. "</select><br>"	
		. "<a href=" . URL::base() . "inspection/download/" . $row['ID']  . "/NOD/PDF class='NOD_" . $row['ID'] . "' ><i class='far fa-file-pdf fa-2x' style='color:#333333;margin-left:5px'></i></a>"
		. "<a href=" . URL::base() . "inspection/download/" . $row['ID']  . "/NOIRTA/PDF class='NOIRTA_" . $row['ID'] . "' style='display:none' ><i class='far fa-file-pdf fa-2x' style='color:#333333;margin-left:5px'></i></a>"
		. "<a href=" . URL::base() . "inspection/download/" . $row['ID']  . "/NOIRTB/PDF class='NOIRTB_" . $row['ID'] . "' style='display:none' ><i class='far fa-file-pdf fa-2x' style='color:#333333;margin-left:5px'></i></a>"
		. "<a href=" . URL::base() . "inspection/download/" . $row['ID']  . "/RTPIDA/PDF class='RTPIDA_" . $row['ID'] . "' style='display:none' ><i class='far fa-file-pdf fa-2x' style='color:#333333;margin-left:5px'></i></a>"
		. "<a href=" . URL::base() . "inspection/download/" . $row['ID']  . "/RTPIDB/PDF class='RTPIDB_" . $row['ID'] . "' style='display:none' ><i class='far fa-file-pdf fa-2x' style='color:#333333;margin-left:5px'></i></a>"
		. "<a href=" . URL::base() . "inspection/download/" . $row['ID']  . "/NOD/WORD class='NOD_" . $row['ID'] . "' ><i class='far fa-file-word fa-2x' style='color:#333333;margin-left:25px'></i></a>"
		. "<a href=" . URL::base() . "inspection/download/" . $row['ID']  . "/NOIRTA/WORD class='NOIRTA_" . $row['ID'] . "' style='display:none' ><i class='far fa-file-word fa-2x' style='color:#333333;margin-left:25px'></i></a>"
		. "<a href=" . URL::base() . "inspection/download/" . $row['ID']  . "/NOIRTB/WORD class='NOIRTB_" . $row['ID'] . "' style='display:none' ><i class='far fa-file-word fa-2x' style='color:#333333;margin-left:25px'></i></a>"
		. "<a href=" . URL::base() . "inspection/download/" . $row['ID']  . "/RTPIDA/WORD class='RTPIDA_" . $row['ID'] . "' style='display:none' ><i class='far fa-file-word fa-2x' style='color:#333333;margin-left:25px'></i></a>"
		. "<a href=" . URL::base() . "inspection/download/" . $row['ID']  . "/RTPIDB/WORD class='RTPIDB_" . $row['ID'] . "' style='display:none' ><i class='far fa-file-word fa-2x' style='color:#333333;margin-left:25px'></i></a>"
		. "<a href=" . URL::base() . "inspection/download/" . $row['ID']  . "/STANDARD_NOV/WORD class='STANDARD_NOV_" . $row['ID'] . "' style='display:none' ><i class='far fa-file-word fa-2x' style='color:#333333;margin-left:25px'></i></a>"
		. "<a href=" . URL::base() . "inspection/download/" . $row['ID']  . "/ELEVATED_NOV/WORD class='ELEVATED_NOV_" . $row['ID'] . "' style='display:none' ><i class='far fa-file-word fa-2x' style='color:#333333;margin-left:25px'></i></a>"
		. "<a href=" . URL::base() . "inspection/download/" . $row['ID']  . "/NOMV/WORD class='NOMV_" . $row['ID'] . "' style='display:none' ><i class='far fa-file-word fa-2x' style='color:#333333;margin-left:25px'></i></a>"
		. "</td></tr>";
	return($result);
}

function display_tank_row($result, $row) {
	$view_button = Controller::_instance('Tank')->_view_button($row['ID']);
	$edit_button = Controller::_instance('Tank')->_edit_button($row['ID']);
	$status = Model::instance('Tank_status_codes')->get_lookup_desc($row['TANK_STATUS_CODE'], FALSE);
	$owner_link = html::owner_link($row['OWNER_ID']);

	// temporarily change '01-JAN/68' to empty string, will fix after UDAPI can accept empty array
	$nov_date = $row['NOV_DATE'] == '01-JAN-68' ? '' : $row['NOV_DATE'];
	$nod_date = $row['NOD_DATE'] == '01-JAN-68' ? '' : $row['NOD_DATE'];
	$redtag_placed_date = $row['REDTAG_PLACED_DATE'] == '01-JAN-68' ? '' : $row['REDTAG_PLACED_DATE'];
	$redtag_removed_date = $row['REDTAG_REMOVED_DATE'] == '01-JAN-68' ? '' : $row['REDTAG_REMOVED_DATE'];

	$result .= "<tr>
		<td>{$row['ID']}</td>
		<td>{$owner_link}</td>
		<td>{$row['TANK_TYPE']}</td>
		<td>{$status}</td>
		<td>{$row['STATUS_DATE']}</td>
		<td>{$nov_date}</td>
		<td>{$nod_date}</td>
		<td>{$redtag_placed_date}</td>
		<td>{$redtag_removed_date}</td>
		<td>{$row['COMMENTS']}</td>
		<td><button class='open_inspection' style='background-color:#D1E6F6;border-radius:5px;display:inline-block;cursor:pointer'>Inspections</button></td>
		<td>{$view_button} {$edit_button}</td>
		</tr>";
	return($result);
}

function display_ab_op_row($result, $row) {
	$view_button = Controller::_instance('Ab_operator')->_view_button($row['ID']);
	$edit_button = Controller::_instance('Ab_operator')->_edit_button($row['ID']);
	$delete_button = Controller::_instance('Ab_operator')->_delete_button($row['ID']);
	$cert_level = Model::instance('Ab_operator')->get_cert_level($row['ID']);
	$cert_expdate = Model::instance('Ab_operator')->get_cert_expdate($row['ID']);
	//$retraining_edit = Controller::_instance('Ab_operator')->retraining_edit_button($row['ID']);

	$result .= "<tr>
		<td>{$row['ID']}</td>
		<td>{$row['FIRST_NAME']}</td>
		<td>{$row['LAST_NAME']}</td>
		<td>{$cert_level}</td>
		<td>{$cert_expdate}</td>
		<td>{$view_button} {$edit_button} {$delete_button}</td>
		</tr>";
	return($result);
}

function display_ab_tank_status_row($result, $row) {
	$view_button = Controller::_instance('Ab_tank_status')->_view_button($row['ID']);
	$edit_button = Controller::_instance('Ab_tank_status')->_edit_button($row['ID']);
	$delete_button = Controller::_instance('Ab_tank_status')->_delete_button($row['ID']);
	$tank_status = Model::instance('Tank_status_codes')->get_lookup_desc($row['TANK_STATUS_CODE'], FALSE);

	$result .= "<tr>
		<td>{$row['ID']}</td>
		<td>{$tank_status}</td>
		<td>{$row['TANK_LAST_USED']}</td>
		<td>{$row['TANK_STATUS_NOTE']}</td>
		<td>{$row['DATE_CREATED']}</td>
		<td>{$edit_button} {$delete_button}</td>
		</tr>";
	return($result);
}

?>
