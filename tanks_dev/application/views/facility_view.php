<?php
/**
 * Facility View
 *
 * This view displays the information of a facility, it includes many tabs of related data.
 *
 * @package ### file docblock
 * @subpackage views
 * @uses ###
 * @see ###
 *
*/

?>

<h1>Facility View</h1>
<table class="horiz_table ui-widget ui-corner-all" style="margin-bottom:30px">
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
	<?= html::horiz_table_tr('Owner', html::owner_link($row['OWNER_ID'], ''), FALSE) ?>
	<?= html::horiz_table_tr('Assigned Inspector', $assigned_inspector['FULL_NAME']
		. Controller::_instance('Entity_details')->_add_edit_button($assigned_inspector['ID'], $row['ID'], 'facility', 'assigned_inspector')
		, FALSE) ?>
	<?= html::table_foot_info($row) ?>
</table>

<?php
if (count($active_penalties))
	echo('This Facility has outstanding Violation. Please review and notify A/B Operator(s).');

echo '<div>';
if(isset($_SESSION['UploadSuccess'])){
	if($_SESSION['UploadSuccess'] == 1){
		echo "<div class=message><<< "."Document has been successfully uploaded"." >>></div>";
	} else {
		echo "<div class=message><<< "."Document upload failed. Please contact OIT for assistance"." >>></div>";
	}
	unset($_SESSION['UploadSuccess']);
}
if(isset($_SESSION['RemoveSuccess'])){
	if($_SESSION['RemoveSuccess'] == 1){
		echo "<div class=message><<< "."Document has been successfully removed"." >>></div>";
	} else {
		echo "<div class=message><<< "."Document remove failed. Please contact OIT for assistance"." >>></div>";
	}
	unset($_SESSION['RemoveSuccess']);
}
echo '</div>';
?>

<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all fade-in">
	<ul>
		<li class="block"><a href="#tabs-email">Contacts</a></li>
		<li class="block"><a href="#tabs-history">Ownership History</a></li>
		<li class="block"><a href="#tabs-inspection">Inspections</a></li>
		<li class="block"><a href="#tabs-permit">Permits</a></li>
		<li class="block"><a href="#tabs-tank">Tanks</a></li>
		<li class="block"><a href="#tabs-ab_op">A/B/C Operator</a></li>
		<li class="inline-block"><a href="#tabs-test">Tester Reports</a></li>
		<li class="block"><a href="#tabs-release">Reported Releases</a></li>
	</ul>

	<!-- -------------- Email ------------------ -->
	<div id="tabs-email">
	<?= Controller::_instance('Email')->_add_button(array($facility_id, 'facility'), 'add new Conact') ?>
	<table id="email_tabular" class="display">
		<thead>
			<tr><th>EMAIL</th><th>PHONE</th><th>TITLE</th><th>FULL NAME</th><th>Contact Type</th><th>CREATED BY</th><th>CREATED ON</th><th>UPDATED BY</th><th>UPDATED ON</th><th>COMMENTS</th><th>ACTION</th></tr>
		</thead>
		<tbody>
			<?= array_reduce($email_rows, 'display_email_row'); ?>
		</tbody>
	</table>
	</div>

	<!-- -------------- Ownership History ------------------ -->
	<div id="tabs-history">
	<?= Controller::_instance('Facility_history')->_add_button($row['ID'], 'add new History') ?>
	<table id="history_tabular" class="display">
		<thead>
			<tr><th>DATE</th><th>OWNER</th><th>HISTORY</th><th>ACTION</th></tr>
		</thead>
		<tbody>
			<?= array_reduce($fac_history_rows, 'display_history_row'); ?>
		</tbody>
	</table>
	</div>

	<!-- -------------- Inspection ------------------ -->
	<div id="tabs-inspection">
	<?= Controller::_instance('Inspection')->_add_button($row['ID'], 'add new Inspection') ?>
	<table id="inspection_tabular" class="display">
		<thead>

			<tr><th>DATE INSPECTED</th><th>COMPLIANCE DATE</th><th>INSPECTION</th><th>LCC NUMBER</th><th>STAFF</th><th>Certified Installer</th><th>User Created</th><th>Date Created</th><th>User Updated</th><th>Date Updated</th><th width="140px">ACTION</th></tr>

		</thead>
		<tbody>
			<?= array_reduce($inspection_rows, 'display_inspection_row'); ?>
		</tbody>
	</table>
	</div>

	<!-- -------------- Permit ------------------ -->
	<div id="tabs-permit">
	<!-- permit creation is done in separate Fees area -->
	<?= Sam::instance()->if_priv($permits->table_name, 'INSERT', "<a href='" . url::fullpath('/permit/') . "'><div class='action_button ui-state-default ui-corner-all' title='add new Permit'><span class='ui-icon ui-icon-plus' style='float:left;'></span>add new Permit</div></a>") ?>
	<table id="permit_tabular" class="display">
		<thead>
			<tr><th>FY</th><th>DATE PERMITTED</th><th>DATE PRINTED</th><th>CERTIFICATE</th><th>TANKS</th><th>CREATED BY</th><th>CREATED ON</th><th>UPDATED BY</th><th>UPDATED ON</th><th>ACTION</th></tr>
		</thead>
		<tbody>
			<?= array_reduce($permit_rows, 'display_permit_row'); ?>
		</tbody>
	</table>
	</div>

	<!-- -------------- Tank ------------------ -->
	<div id="tabs-tank">
	<?= Controller::_instance('Tank')->_add_button(array($row['ID'], $row['OWNER_ID']), 'add new Tank') ?>
	<table id="tank_tabular" class="display">
		<thead>
			<tr><th>ID</th><th width="140px">OWNER</th><th>TYPE</th><th>STATUS</th><th>STATUS DATE</th><th width="140px">COMMENTS</th><th>CREATED BY</th><th>CREATED ON</th><th>UPDATED BY</th><th>UPDATED ON</th><th width="90px">ACTION</th></tr>
		</thead>
		<tbody>
			<?= array_reduce($tank_rows, 'display_tank_row'); ?>
		</tbody>
	</table>
	</div>

	<!-- -------------- A/B/C Ops ------------------ -->
	<div id="tabs-ab_op">

	<h3>Tank Operators</h3>
	<?= Controller::_instance('Ab_operator')->_add_button(array($row['ID']), 'add new A/B Operator') ?>
	<div style="float:left; padding-left:70px;"><?= Controller::_instance('Ab_operator')->add_c_button(array($row['ID'])) ?></div>
	<table id="ab_op_tabular" class="display">
		<thead>
			<tr><th>ID</th><th>FIRST NAME</th><th>LAST NAME</th><th>CERT LEVEL</th><th>CERT EXPIRES ON</th><th>CREATED BY</th><th>CREATED ON</th><th>UPDATED BY</th><th>UPDATED ON</th><th width="120px">ACTION</th></tr>
		</thead>
		<tbody>
			<?= array_reduce($ab_op_rows, 'display_ab_op_row'); ?>
		</tbody>
	</table>

	<h3>Retraining</h3>
	<?= Controller::_instance('Retraining')->_add_button(array($row['ID']), 'add new retraining') ?>
	<table id="retraining_tabular" class="display">
		<thead>
			<tr><th>ID</th><th>FIRST NAME</th><th>LAST NAME</th><th>CERT #</th><th>CERT EXPIRES ON</th><th>UPDATED BY</th><th>UPDATED ON</th><th width="120px">ACTION</th></tr>
		</thead>
		<tbody>
			<?= array_reduce($retraining_rows, 'display_retraining_row'); ?>
		</tbody>
	</table>

	<h3>Reported Tank Status</h3>
	<?= Controller::_instance('Ab_tank_status')->_add_button(array($row['ID']), 'add new Tank Status') ?>
	<table id="ab_tank_status_tabular" class="display">
		<thead>
			<tr><th>ID</th><th>TANK STATUS CODE</th><th>TANK LAST USED</th><th>TANK STATUS NOTE</th><th>CREATED BY</th><th>CREATED ON</th><th>UPDATED BY</th><th>UPDATED ON</th><th width="120px">ACTION</th></tr>
		</thead>
		<tbody>
			<?= array_reduce($ab_tank_status_rows, 'display_ab_tank_status_row'); ?>
		</tbody>
	</table>

	</div>


	<!-- -------------- Test ------------------ -->
	<div id="tabs-test">
	<?php echo Controller::_instance('Test_result')->_add_button(array($row['ID']), 'add new Tester Report') ?>
	<table id="test_tabular" class="display">
		<thead>
			<tr><th>Test ID</th><th>Tank ID</th><th>UST/AST</th><th>Tester</th><th>Testing Company</th><th>Test Date</th><th>ALLD Functionality</th><th>Sensor Functionality</th><th>Line Tightness</th><th>Tank Tightness</th><th>ATG Test</th><th>Next 1 Year Test Date</th><th>Overfill Functionality</th><th>Corrosion Protection</th><th>Spill Containment</th><th>Sump Cointainment</th><th>Next 3 Year Test Date</th><th>Violation(s) Issued (NOV/LCC Number)</th><th>Original Failed Test ID if a Retest</th><th>Attachment</th><th>Upload</th><th style="min-width:120px;">ACTION</th></tr>
		</thead>
		<tbody>
			<?= array_reduce($test_results_rows, 'display_test_results_row'); ?>
		</tbody>
	</table>
	</div>

	<!-- -------------- Reported Release ------------------ -->
	<div id="tabs-release">
	<?php echo Controller::_instance('Suspected_release')->_add_button(array($row['ID']), 'add new Reported Release') ?>
	<table id="release_tabular" class="display">
		<thead>
			<tr><th>SCSR ID</th><th>Tank ID</th><th>Status</th><th>Cause</th><th>Source</th><th>Date Discovered</th><th>Date Reported</th><th>SRSC Letter Mailed</th><th>Date Closed</th><th>NFA Letter Date</th><th>Referred Date</th><th>7-Day Rpt Due</th><th>30-Day Confirmed Date</th><th>User Last Updated</th><th>Date Last Updated</th><th>Attachment</th><th style="max-width:200px;">Upload</th><th style="min-width:120px;">ACTION</th></tr>
		</thead>
		<tbody>
			<?= array_reduce($suspected_release_rows, 'display_suspected_release_row'); ?>
		</tbody>
	</table>
	</div>
</div> <!-- end of tabs -->

<script type="text/javascript" charset="utf-8">
$(document).ready(function() {
	email_tabular_obj = $('#email_tabular').dataTable({
		"bJQueryUI":true, "sPaginationType":"full_numbers", "bProcessing":true,
		"aoColumnDefs": [
			{ "aTargets":[7], "bSearchable": false, "bSortable":false }
		]
	});

	history_tabular_obj = $('#history_tabular').dataTable({
		"bJQueryUI": true, "sPaginationType": "full_numbers",
		"aoColumnDefs": [
			{ "aTargets":[3], "bSearchable":false, "bSortable":false },
			{ "aTargets":[0], "sType":"oracle_date" }
		]
	});

	inspection_tabular_obj = $('#inspection_tabular').dataTable({
		"bJQueryUI": true, "sPaginationType": "full_numbers",
		"aoColumnDefs": [
			{ "aTargets":[5], "bSearchable":false, "bSortable":false },
			{ "aTargets":[0,1], "sType":"oracle_date" }
		 ],
		"aaSorting": [[ 0, "desc" ]]
	});

	permit_tabular_obj = $('#permit_tabular').dataTable({
		"bJQueryUI": true, "sPaginationType": "full_numbers",
		"aoColumnDefs": [
			{ "aTargets":[5], "bSortable":false, "bSearchable":false },
			{ "aTargets":[1,2], "sType":"oracle_date" }
		],
		"aaSorting": [[ 0, "desc" ]]
	});

	tank_tabular_obj = $('#tank_tabular').dataTable({
		"bJQueryUI": true, "sPaginationType": "full_numbers",
		"aoColumnDefs": [
			{ "aTargets":[4], "bSortable":false, "bSearchable":false }
		]
	});

	ab_op_tabular_obj = $('#ab_op_tabular').dataTable({
		"bJQueryUI": true, "sPaginationType": "full_numbers",
		"aoColumnDefs": [
			{ "aTargets":[4], "bSortable":false, "bSearchable":false }
		]
	});
	
	retraining_tabular_obj = $('#retraining_tabular').dataTable({
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

	test_tabular_obj = $('#test_tabular').dataTable({
		"bJQueryUI": true, "sPaginationType": "full_numbers",
		"aoColumnDefs": [
	
		]
	});
	release_tabular_obj = $('#release_tabular').dataTable({
		"bJQueryUI": true, "sPaginationType": "full_numbers",
		"aoColumnDefs": [
		]
	});
});

// switch between inline block(tester report tab) and blcck(other tabs) due to the width of tester report tab
$('.inline-block').click(function(){
	$('#tabs').css('display', 'inline-block');
});
$('.block').click(function(){
	$('#tabs').css('display', 'block');
});

// switch upload file name for suspected release files, including NFA and SCSR Letter
function changeUploadFile(value) {
	var pos = value.lastIndexOf('_');
	var file = value.slice(0, pos);
	var id = value.slice(pos+1);

	if(file == 'upload_scsr_letter') {
		$('#upload_form_name_' + id).val("SCSR_Letter");
//		console.log($('#upload_name').val());
		$('#upload_name_' + id).val("SCSRLetterAttachment");
	} else if (file == 'upload_nfa_letter') {
		$('#upload_form_name_' + id).val("NFA_Letter");
//		console.log($('#upload_name').val());
		$('#upload_name_' + id).val("NFALetterAttachment");
	}
}

// switch download file name for suspected release files, including NFA and SCSR Letter
function changeDownloadFile(value) {
	var pos = value.lastIndexOf('_');
	var file = value.slice(0, pos);
	var id = value.slice(pos+1);
	var nfa_letter = $('.download_nfa_letter_' + id);
	var scsr_letter = $('.download_scsr_letter_' + id);
	if(file == 'download_scsr_letter') {
		nfa_letter.hide();
		scsr_letter.show();
	} else if (file == 'download_nfa_letter') { 
		nfa_letter.show();
		scsr_letter.hide();
	}
}
</script>

<?php
/**
 * Displays the facility contact information row.
 *
 * @return string The contact information of the facility.
 */
function display_email_row($result, $row) {
	$edit_button = Controller::_instance('Email')->_edit_button($row['ID']);
	$delete_button = Controller::_instance('Email')->_delete_button($row['ID']);
	$contact_type = Model::instance('Ref_contact_type')->get_lookup_desc($row['CONTACT_TYPE_ID'], FALSE);
	$email = html::mailto($row['EMAIL']);

	$result .= "<tr>
		<td>{$email}</td>
		<td>{$row['PHONE']}</td>
		<td>{$row['TITLE']}</td>
		<td>{$row['FULLNAME']}</td>
		<td>{$contact_type}</td>	
		<td>{$row['USER_CREATED']}</td>
		<td>{$row['DATE_CREATED']}</td>
		<td>{$row['USER_MODIFIED']}</td>
		<td>{$row['DATE_MODIFIED']}</td>
		<td>{$row['COMMENTS']}</td>
		<td>{$edit_button} {$delete_button}</td>
		</tr>";
	return($result);
}

/**
 * Displays the facility ownership history row.
 *
 * @return string The ownership history of the facility.
 */
function display_history_row($result, $row) {
	// complete info shown -- detailed view not needed
	$edit_button =  Controller::_instance('Facility_history')->_edit_button(array($row['FACILITY_ID'], $row['OWNER_ID'], $row['FACILITY_HISTORY_CODE'], $row['FACILITY_HISTORY_DATE']));
	$delete_button =  Controller::_instance('Facility_history')->_delete_button(array($row['FACILITY_ID'], $row['OWNER_ID'], $row['FACILITY_HISTORY_CODE'], $row['FACILITY_HISTORY_DATE']));
	$history = Model::instance('Facility_history_codes')->get_lookup_desc($row['FACILITY_HISTORY_CODE']);
	$owner_link = html::owner_link($row['OWNER_ID']);
	$result .= "<tr>
		<td>{$row['FACILITY_HISTORY_DATE']}</td>
		<td>{$owner_link}</td>
		<td>{$history}</td>
		<td>{$edit_button} {$delete_button}</td>
		</tr>";
	return($result);
}

/**
 * Displays the facility inspection row.
 *
 * @return string The inspection of the facility.
 */
function display_inspection_row($result, $row) {
	$view_button = Controller::_instance('Inspection')->_view_button($row['ID']);
	$edit_button = Controller::_instance('Inspection')->_edit_button($row['ID']);
	$delete_button = Controller::_instance('Inspection')->_delete_button($row['ID']);
	$inspection = Model::instance('Inspection_codes')->get_lookup_desc($row['INSPECTION_CODE']);
	$certified_installer = Model::instance('Certified_installers')->get_certified_installer_by_id($row['CERTIFIED_INSTALLER_ID']);
	$result .= "<tr>
		<td>{$row['DATE_INSPECTED']}</td>
		<td>{$row['CASE_ID']}</td>
		<td>{$inspection}</td>
		<td>{$row['NOV_NUMBER']}</td>
		<td>{$row['STAFF_CODE']}</td>
		<td>{$certified_installer}</td>
		<td>{$row['USER_CREATED']}</td>
		<td>{$row['DATE_CREATED']}</td>
		<td>{$row['USER_MODIFIED']}</td>
		<td>{$row['DATE_MODIFIED']}</td>
		<td>{$view_button} {$edit_button} {$delete_button}</td>
		</tr>";
	return($result);
}

/**
 * Displays the facility permit row.
 *
 * @return string The permit of the facility.
 */
function display_permit_row($result, $row) {
	$view_button = Controller::_instance('Permit')->_view_button(array($row['FACILITY_ID'], $row['OWNER_ID'], $row['FISCAL_YEAR']));
	//$edit_button = Controller::_instance('Permit')->_edit_button(array($row['FACILITY_ID'], $row['OWNER_ID'], $row['FISCAL_YEAR']));
	//$delete_button = Controller::_instance('Permit')->_delete_button(array($row['FACILITY_ID'], $row['OWNER_ID'], $row['FISCAL_YEAR']));
	$result .= "<tr>
		<td>{$row['FISCAL_YEAR']}</td>
		<td>{$row['DATE_PERMITTED']}</td>
		<td>{$row['DATE_PRINTED']}</td>
		<td>{$row['PERMIT_NUMBER']}</td>
		<td>{$row['TANKS']}</td>
		<td>{$row['USER_CREATED']}</td>
		<td>{$row['DATE_CREATED']}</td>
		<td>{$row['USER_MODIFIED']}</td>
		<td>{$row['DATE_MODIFIED']}</td>
		<td>{$view_button}</td>
		</tr>";
	return($result);
}

/**
 * Displays the facility tank row.
 *
 * @return string The tank of the facility.
 */
function display_tank_row($result, $row) {
	$view_button = Controller::_instance('Tank')->_view_button($row['ID']);
	$edit_button = Controller::_instance('Tank')->_edit_button($row['ID']);
	$delete_button = Controller::_instance('Tank')->_delete_button($row['ID']);
	$status = Model::instance('Tank_status_codes')->get_lookup_desc($row['TANK_STATUS_CODE'], FALSE);
	$owner_link = html::owner_link($row['OWNER_ID']);
	$result .= "<tr>
		<td>{$row['ID']}</td>
		<td>{$owner_link}</td>
		<td>{$row['TANK_TYPE']}</td>
		<td>{$status}</td>
		<td>{$row['STATUS_DATE']}</td>
		<td>{$row['COMMENTS']}</td>
		<td>{$row['USER_CREATED']}</td>
		<td>{$row['DATE_CREATED']}</td>
		<td>{$row['USER_MODIFIED']}</td>
		<td>{$row['DATE_MODIFIED']}</td>
		<td>{$view_button} {$edit_button} {$delete_button}</td>
		</tr>";
	return($result);
}

/**
 * Displays the facility A/B/C operator row.
 *
 * @return string The A/B/C operator of the facility.
 */
function display_ab_op_row($result, $row) {
	$view_button = Controller::_instance('Ab_operator')->_view_button($row['ID']);
	$edit_button = Controller::_instance('Ab_operator')->_edit_button($row['ID']);
	$delete_button = Controller::_instance('Ab_operator')->_delete_button($row['ID']);
	$cert_level = Model::instance('Ab_operator')->get_cert_level($row['ID']);
	$cert_expdate = Model::instance('Ab_operator')->get_cert_expdate($row['ID']);

	$result .= "<tr>
		<td>{$row['ID']}</td>
		<td>{$row['FIRST_NAME']}</td>
		<td>{$row['LAST_NAME']}</td>
		<td>{$cert_level}</td>
		<td>{$cert_expdate}</td>
		<td>{$row['USER_CREATED']}</td>
		<td>{$row['DATE_CREATED']}</td>
		<td>{$row['USER_MODIFIED']}</td>
		<td>{$row['DATE_MODIFIED']}</td>
		<td>{$view_button} {$edit_button} {$delete_button}</td>
		</tr>";
	return($result);
}

/**
 * Displays the facility retraining row.
 *
 * @return string The retraining of the facility.
 */
function display_retraining_row($result, $row) {
	$view_button = Controller::_instance('Retraining')->_view_button($row['RETRAINING_ID']);
	$edit_button = Controller::_instance('Retraining')->_edit_button($row['RETRAINING_ID']);
	$delete_button = Controller::_instance('Retraining')->_delete_button($row['RETRAINING_ID']);

	$result .= "<tr>
		<td>{$row['RETRAINING_ID']}</td>
		<td>{$row['AB_OPERATOR_FIRST_NAME']}</td>
		<td>{$row['AB_OPERATOR_LAST_NAME']}</td>
		<td>{$row['CERT_NUMBER']}</td>
		<td>{$row['CERT_EXPIRE_DATE']}</td>
		<td>{$row['USER_LAST_UPDT']}</td>
		<td>{$row['TMSP_LAST_UPDT']}</td>
		<td>{$view_button} {$edit_button} {$delete_button}</td>
		</tr>";
	return($result);
}

/**
 * Displays the facility A/B tank status row.
 *
 * @return string The A/B tank status of the facility.
 */
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
		<td>{$row['USER_CREATED']}</td>
		<td>{$row['DATE_CREATED']}</td>
		<td>{$row['USER_MODIFIED']}</td>
		<td>{$row['DATE_MODIFIED']}</td>
		<td>{$edit_button} {$delete_button}</td>
		</tr>";
	return($result);
}

/**
 * Displays the facility test result row.
 *
 * @return string The test result of the facility.
 */
function display_test_results_row($result, $row) {
	$view_button = Controller::_instance('Test_result')->_view_button($row['TEST_RESULTS_ID']);
	$edit_button = Controller::_instance('Test_result')->_edit_button($row['TEST_RESULTS_ID']);
	$delete_button = Controller::_instance('Test_result')->_delete_button($row['TEST_RESULTS_ID']);
	$test_id = $row['TEST_RESULTS_ID'];
	$test_upload_id = $row['test_upload_id'];
	$test_date = date("Y-m-d",  strtotime($row['TEST_DATE']));
	$one_year_test_date = date("Y-m-d", strtotime('+1 year', strtotime($row['TEST_DATE'])));
	$three_year_test_date = date("Y-m-d", strtotime('+3 years', strtotime($row['TEST_DATE'])));
	$one_year_due_color = '';
	$three_year_due_color = '';
	$one_year_due_bold = '';
	$three_year_due_bold = '';
	if( strtotime($one_year_test_date) < strtotime('now') ) {
		$one_year_due_color = 'red';
		$one_year_due_bold = 'bold';
	}
	if( strtotime($three_year_test_date) < strtotime('now') ) {
		$three_year_due_color = 'red';
		$three_year_due_bold = 'bold';
	}

	$result .= "<tr>
		<td>{$test_id}</td>
		<td>{$row['TANK_ID']}</td>
		<td class='align-center'>{$row['TANK_TYPE']}</td>
		<td>{$row['TESTER_NAME']}</td>
		<td>{$row['TESTING_COMPANY_NAME']}</td>
		<td>{$test_date}</td>
		<td class='align-center'>{$row['ANNUAL_ALLD_FUNC_PASSFAIL_FL']}</td>
		<td class='align-center'>{$row['ANNUAL_SENS_FUNC_PASSFAIL_FL']}</td>
		<td class='align-center'>{$row['ANNUAL_LINE_TIGHT_PASSFAIL_FL']}</td>
		<td class='align-center'>{$row['ANNUAL_TANK_TIGHT_PASSFAIL_FL']}</td>
		<td class='align-center'>{$row['ANNUAL_AGT_TEST_PASSFAIL_FL']}</td>
		<td style='color:{$one_year_due_color}; font-weight:{$one_year_due_bold}'>{$one_year_test_date}</td>
		<td class='align-center'>{$row['YEAR3_OVERFILL_PASSFAIL_FL']}</td>
		<td class='align-center'>{$row['YEAR3_CORRPROT_PASSFAIL_FL']}</td>
		<td class='align-center'>{$row['YEAR3_SPILLCONT_PASSFAIL_FL']}</td>
		<td class='align-center'>{$row['YEAR3_SUMPCONT_PASSFAIL_FL']}</td>
		<td style='color:{$three_year_due_color}; font-weight:{$three_year_due_bold}'>{$three_year_test_date}</td>
		<td><a style='color:#007bff;' href='{$row['INSPECTION_VIEW']}'>{$row['NOV_LCC']}</a></td>
		<td>{$row['RETEST_OF_TEST_RESULT_ID']}</td>
		<td class='align-center'>";
	if($test_upload_id !== '') {
		$result .= '<a href="' . $row['file_path'] . '"><i class="far fa-file-pdf fa-2x pdf_icon"></i></a>';
		$result .= '<form action="' . $row['delete_action'] . '" method="post" onsubmit="return confirm(\'Are you sure you want to remove this document?\');">';
		$result .= '<input type="submit" value="Remove" name="remove">';
		$result .= '<input type="hidden" name="form_id" value="' . $test_id . '">';
		$result .= '<input type="hidden" name="form_code" value="Test_Result">';
		$result .= '<input type="hidden" name="upload_id" value="' . $test_upload_id. '">';
		$result .= '</form>';
	}
	$result .= "</td>
		<td>
			<form action='" . $row['upload_action'] . "' method='post' enctype='multipart/form-data'>
				<input type='file' name='fileToUpload' id='fileToUpload' multiple required>
				<input type='submit' value='Upload' name='submit'>
				<input type='hidden' name='app_topic' value='TEST_RESULT'>
				<input type='hidden' id='upload_form_name_" . $test_id . "' name='form_name' value='Test_Result'>
				<input type='hidden' name='id' value='" . $test_id . "'>
				<input type='hidden' name='upload_id' value='" . $test_upload_id . "'>
			</form>
		</td>
		<td>{$view_button}{$edit_button} {$delete_button}</td>
		</tr>";
	return($result);
}

/**
 * Displays the facility suspected release row.
 *
 * @return string The suspected release of the facility.
 */
function display_suspected_release_row($result, $row) {
	$suspected_release_id = $row['SUSPECTED_RELEASE_ID'];
	$nfa_letter_upload_id = $row['nfa_letter_upload_id'];
	$scsr_letter_upload_id = $row['scsr_letter_upload_id'];
	if(is_array($nfa_letter_upload_id)) {
		$nfa_letter_upload_id_string = implode(",", $nfa_letter_upload_id);
	} else {
		$nfa_letter_upload_id_string = $nfa_letter_upload_id;
	}
	if(is_array($scsr_letter_upload_id)) {
		$scsr_letter_upload_id_string = implode(",", $scsr_letter_upload_id);
	} else {
		$scsr_letter_upload_id_string = $scsr_letter_upload_id;
	}
	$view_button = Controller::_instance('Suspected_release')->_view_button($row['SUSPECTED_RELEASE_ID']);
	$edit_button = Controller::_instance('Suspected_release')->_edit_button($row['SUSPECTED_RELEASE_ID']);
	$delete_button = Controller::_instance('Suspected_release')->_delete_button($row['SUSPECTED_RELEASE_ID']);
	$tank_ids_wrap = str_replace(",", "<br>", $row['TANK_ID']);
	$seven_day_report_due_date = date("m/d/y", strtotime('+7 days', strtotime($row['DATE_DISCOVERED'])));
	$thirty_day_confirmed_date = date("m/d/y", strtotime('+30 days', strtotime($row['DATE_REPORTED'])));
	if(!is_null($row['CLOSED_DATE']) && !empty($row['CLOSED_DATE'])) {
		$status = "Closed";
	} else if(!is_null($row['REFERRED_DATE']) && !empty($row['REFERRED_DATE'])) {
		$status = "Referred";
	} else if(!is_null($row['CONFIRMED_DATE']) && !empty($row['CONFIRMED_DATE'])) {
		$status = "Confirmed";
	} else {
		$status = "Open";
	}
	$date_discovered = date("m/d/y", strtotime($row['DATE_DISCOVERED']));
	$date_reported = date("m/d/y", strtotime($row['DATE_REPORTED']));
	$nfa_letter_date = is_null($row['NFA_LETTER_DATE']) ? '' : date("m/d/y", strtotime($row['NFA_LETTER_DATE']));
	$referred_date = is_null($row['REFERRED_DATE']) ? '' : date("m/d/y", strtotime($row['REFERRED_DATE']));
	$scsr_letter_mailed_date = is_null($row['SCSR_LETTER_MAILED_DATE']) ? '' : date("m/d/y", strtotime($row['SCSR_LETTER_MAILED_DATE']));
	$date_closed = is_null($row['CLOSED_DATE']) ? '' :date("m/d/y", strtotime($row['CLOSED_DATE']));
	$date_confirmed = is_null($row['CONFIRMED_DATE']) ? '' : date("m/d/y", strtotime($row['CONFIRMED_DATE']));
	$date_last_updated = date("m/d/y", strtotime($row['DATE_MODIFIED']));

	$result .= "<tr>
		<td>{$suspected_release_id}</td>
		<td>{$tank_ids_wrap}</td>
		<td>{$status}</td>
		<td>{$row['CAUSE_DESC']}</td>
		<td>{$row['SOURCE_DESC']}</td>
		<td>{$date_discovered}</td>
		<td>{$date_reported}</td>
		<td>{$scsr_letter_mailed_date}</td>
		<td>{$date_closed}</td>
		<td>{$nfa_letter_date}</td>
		<td>{$referred_date}</td>
		<td>{$seven_day_report_due_date}</td>
		<td>{$thirty_day_confirmed_date}</td>
	

		<td>{$row['USER_MODIFIED']}</td>
		<td>{$date_last_updated}</td>
		<td class='align-center' style='vertical-align:top'>
			<select class='select_file' onchange='changeDownloadFile(this.value)' style='margin-top:7px'>
				<option value='download_nfa_letter_" . $suspected_release_id . "'>NFA Letter</option>
				<option value='download_scsr_letter_" . $suspected_release_id . "'>SCSR Letter</option>
			</select><br><br>";
		if($nfa_letter_upload_id !== '') {
			$result .= '<div class="download_nfa_letter_' . $suspected_release_id . '">';
			foreach($nfa_letter_upload_id as $key => $value) {
				//$result .= '<a href="' . $row['nfa_letter_file_path'] . '"><i class="far fa-file-pdf fa-2x pdf_icon"></i></a>';
				$result .= '<a href="' . $row['nfa_letter_file_path'][$key] . '"><i class="far fa-file-pdf fa-2x pdf_icon"></i></a>&nbsp';
			}
			$result .= '<form action="' . $row['delete_action'] . '" method="post" onsubmit="return confirm(\'Are you sure you want to remove this NFA Letter?\');">';
			$result .= '<input type="submit" value="Remove" name="remove">';
			$result .= '<input type="hidden" name="form_id" value="' . $suspected_release_id . '">';
			$result .= '<input type="hidden" name="form_code" value="NFA_Letter">';
			$result .= '<input type="hidden" name="upload_id" value="' . $nfa_letter_upload_id_string . '">';
			$result .= '</form>';
			$result .= '</div>';	
		}
		if($scsr_letter_upload_id !== '') {
			$result .= '<div class="download_scsr_letter_' . $suspected_release_id . '" style="display: none;">';
			foreach($scsr_letter_upload_id as $key => $value) {
				//$result .= '<a href="' . $row['scsr_letter_file_path'] . '"><i class="far fa-file-pdf fa-2x pdf_icon"></i></a>';
				$result .= '<a href="' . $row['scsr_letter_file_path'][$key] . '"><i class="far fa-file-pdf fa-2x pdf_icon"></i></a>&nbsp';
			}
			$result .= '<form action="' . $row['delete_action'] . '" method="post" onsubmit="return confirm(\'Are you sure you want to remove this SCSR Letter?\');">';
			$result .= '<input type="submit" value="Remove" name="remove">';
			$result .= '<input type="hidden" name="form_id" value="' . $suspected_release_id . '">';
			$result .= '<input type="hidden" name="form_code" value="SCSR_Letter">';
			$result .= '<input type="hidden" name="upload_id" value="' . $scsr_letter_upload_id_string . '">';
			$result .= '</form>';
			$result .= '</div>';
		}
		$result .= "</td>
		<td style='vertical-align:top'>
			<select class='select_file' onchange='changeUploadFile(this.value)' style='margin-top:7px'>
				<option value='upload_nfa_letter_" . $suspected_release_id . "'>NFA Letter</option>
				<option value='upload_scsr_letter_" . $suspected_release_id . "'>SCSR Letter</option>
			</select><br><br>
			<form action='" . $row['upload_action'] . "' method='post' enctype='multipart/form-data'>
				<input type='file' name='fileToUpload[]' id='fileToUpload' multiple required>
				<input type='submit' value='Upload' name='submit'>
				<input type='hidden' name='app_topic' value='NFA_Letter'>
				<input type='hidden' id='upload_form_name_" . $suspected_release_id . "' name='form_name' value='NFA_Letter'>
				<input type='hidden' id='upload_name_" . $suspected_release_id . "' name='upload_name' value='NFALetterAttachment'>
				<input type='hidden' name='id' value='" . $suspected_release_id . "'>
				<input type='hidden' name='upload_id' value=''>
			</form>
		</td>
		<td>{$view_button}{$edit_button} {$delete_button}</td>
		</tr>";
	return($result);
}
?>
