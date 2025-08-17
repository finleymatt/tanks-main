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
$facility_id = $parent_ids[0];

?>

<h1>Test Result - Add New</h1>
<form action="<?= $action ?>" method="post" class="validate_form edit_form">
<fieldset class='ui-widget ui-widget-content ui-corner-all'>
<legend class='ui-widget ui-widget-header ui-corner-all'>Test Result</legend>
<table class="horiz_table ui-widget ui-corner-all">
	<?= html::horiz_table_tr('Facility ID', $facility_id) ?>
	<?= html::horiz_table_tr('Tester', Controller::_instance('Test_result')->add_tester_button($facility_id) . Controller::_instance('Test_result')->delete_tester_button($facility_id), FALSE) ?>
	<?= html::horiz_table_tr('Testing Company',  Controller::_instance('Test_result')->add_test_company_button($facility_id) . Controller::_instance('Test_result')->delete_test_company_button($facility_id), FALSE) ?>

</table>

<button type="button" id="select_all" onclick="selectAll()" style="margin-top:30px">Select All</button>
<button type="button" id="select_none" onclick="selectNone()">Select None</button>
<button type="button" id="select_active" onclick="selectActive()">Select Active</button>
<button type="button" id="show_removed_tanks" onclick="showRemovedTanks()">Show/Hide Removed Tanks</button>
<table class="horiz_table ui-widget ui-corner-all" style="margin-top:5px">
	<tr>
		<td>Tank</td>
		<td>Tester&nbsp<input type="checkbox" id="tester_all" class="checkbox_align">ALL</td>
		<td>Testing Company&nbsp<input type="checkbox" id="testing_company_all" class="checkbox_align">ALL</td>
		<td>Violation(s) Issued (NOV/LCC Number)&nbsp<input type="checkbox" id="violation_issued_all" class="checkbox_align">ALL</td>
		<td>Test Date&nbsp<input type="checkbox" id="test_date_all" class="checkbox_align">ALL</td>
		<td>Date Test Submitted&nbsp<input type="checkbox" id="test_submitted_date_all" class="checkbox_align">ALL</td>
		<td>Inspector&nbsp<input type="checkbox" id="inspector_all" class="checkbox_align">ALL</td>
		<td>Original Failed Test ID if a Retest&nbsp<input type="checkbox" id="original_test_id_all" class="checkbox_align">ALL</td>
		<td>ALLD Functionality</td>
		<td>Sensor Functionality</td>
		<td>Line Tightness</td>
		<td>Tank Tightness</td>
		<td style='white-space: nowrap'>ATG Test</td>
		<td>Overfill Funcationality</td>
		<td>Corrosion Protection</td>
		<td>Spill Containment</td>
		<td>Sump Containment</td>
		<td>Comments</td>
	</tr>

	<?php
		$tanks = Model::instance('tanks')->get_list('FACILITY_ID=:FACILITY_ID', 'ID', array(':FACILITY_ID' => $facility_id));

		foreach($tanks as $tank) {
			if($tank['TANK_STATUS_CODE'] == '1' || $tank['TANK_STATUS_CODE'] == '2'){
				echo "<tr class='active_tanks'>
				<td><input type='checkbox' name='checkbox_tank_{$tank['ID']}' class='tank_id_checkbox' id='tank_{$tank['ID']}'>{$tank['ID']}</td>";
				echo "<td><input type='text' list='tester' id='tester_{$tank['ID']}' name='tester_{$tank['ID']}' class='tester editable validate[required]' pattern='/^[a-zA-Z'- ]+$/'  autocomplete='off' disabled />
				<datalist id='tester'>";
				foreach(Model::instance('Ref_test_results_testers')->get_tester_list() as $key => $value) {
				        echo "<option value='" . $value . "'>" . $value . "</option>";
				}
				echo "</td>
				<td style='width:300px;'><input type='text' list='testing_company' id='testing_company_{$tank['ID']}' name='testing_company_{$tank['ID']}' class='testing_company editable validate[required]' style='width:400px;' autocomplete='off' disabled />
				<datalist id='testing_company'>";
				foreach(Model::instance('Ref_test_results_test_company')->get_test_company_list() as $key => $value) {
					echo "<option value='" . $value . "'>" . $value . "</option>";
				}
				echo "</td>	
				<td><select name='violation_inspection_id_{$tank['ID']}' id='violation_inspection_id_{$tank['ID']}' class='violation_issued editable' disabled>
					<option value='' selected=''>No Violation</option>";
				foreach(Model::instance('Inspections')->get_inspections_by_facility($facility_id) as $inspection) {
					if(!is_null($inspection['NOV_NUMBER'])) {
						echo "<option value='" . $inspection['INSPECTION_ID'] . "'>" . $inspection['NOV_NUMBER'] . "</option>";
					}
				}
				echo "</td>
				<td><input type='text' name='test_date_{$tank['ID']}' id='test_date_{$tank['ID']}' class='test_date datepicker editable validate[required]' disabled></td>
				<td><input type='text' name='test_submitted_date_{$tank['ID']}' id='test_submitted_date_{$tank['ID']}' class='test_submitted_date datepicker editable test_submitted_date' disabled></td>
				<td><input type='text' list='inspector' id='inspector_{$tank['ID']}' name='inspector_{$tank['ID']}' autocomplete='off' class='inspector editable' disabled />
					<datalist id='inspector'>";	
					foreach(Model::instance('Test_results')->get_inspector_list() as $inspector) {
						echo "<option value='" . $inspector . "'>" . $inspector . "</option>";
					}
				echo "</td>
				<td><input type='number' name='original_test_id_{$tank['ID']}' id='original_test_id_{$tank['ID']}' class='original_test_id editable' disabled></td>
				<td>
					<input id='alld_pass_{$tank['ID']}' name='alld_{$tank['ID']}' type='checkbox' value='P' class='checkbox checkbox_align editable' disabled>Pass<br>
					<input id='alld_fail_{$tank['ID']}' name='alld_{$tank['ID']}' type='checkbox' value='F' class='checkbox checkbox_align editable' disabled>Fail
				</td>
				<td>
					<input id='sensor_pass_{$tank['ID']}' name='sensor_{$tank['ID']}' type='checkbox' value='P' class='checkbox checkbox_align editable' disabled>Pass<br>
					<input id='sensor_fail_{$tank['ID']}' name='sensor_{$tank['ID']}' type='checkbox' value='F' class='checkbox checkbox_align editable' disabled>Fail
				</td>
				<td>
					<input id='line_tightness_pass_{$tank['ID']}' name='line_tightness_{$tank['ID']}' type='checkbox' value='P' class='checkbox checkbox_align editable' disabled>Pass<br>
					<input id='line_tightness_fail_{$tank['ID']}' name='line_tightness_{$tank['ID']}' type='checkbox' value='F' class='checkbox checkbox_align editable' disabled>Fail
				</td>
				<td>
					<input id='tank_tightness_pass_{$tank['ID']}' name='tank_tightness_{$tank['ID']}' type='checkbox' value='P' class='checkbox checkbox_align editable' disabled>Pass<br>
					<input id='tank_tightness_fail_{$tank['ID']}' name='tank_tightness_{$tank['ID']}' type='checkbox' value='F' class='checkbox checkbox_align editable' disabled>Fail
				</td>
				<td>
					<input id='atg_pass_{$tank['ID']}' name='atg_{$tank['ID']}' type='checkbox' value='P' class='checkbox checkbox_align editable' disabled>Pass<br>
					<input id='atg_fail_{$tank['ID']}' name='atg_{$tank['ID']}' type='checkbox' value='F' class='checkbox checkbox_align editable' disabled>Fail
				</td>
				<td>
					<input id='overfill_pass_{$tank['ID']}' name='overfill_{$tank['ID']}' type='checkbox' value='P' class='checkbox checkbox_align editable' disabled>Pass<br>
					<input id='overfill_fail_{$tank['ID']}' name='overfill_{$tank['ID']}' type='checkbox' value='F' class='checkbox checkbox_align editable' disabled>Fail
				</td>
				<td>
					<input id='corrosion_pass_{$tank['ID']}' name='corrosion_{$tank['ID']}' type='checkbox' value='P' class='checkbox checkbox_align editable' disabled>Pass<br>
					<input id='corrosion_fail_{$tank['ID']}' name='corrosion_{$tank['ID']}' type='checkbox' value='F' class='checkbox checkbox_align editable' disabled>Fail
				</td>
				<td>
					<input id='spill_pass_{$tank['ID']}' name='spill_{$tank['ID']}' type='checkbox' value='P' class='checkbox checkbox_align editable' disabled>Pass<br>
					<input id='spill_fail_{$tank['ID']}' name='spill_{$tank['ID']}' type='checkbox' value='F' class='checkbox checkbox_align editable' disabled>Fail
				</td>
				<td>
					<input id='sump_pass_{$tank['ID']}' name='sump_{$tank['ID']}' type='checkbox' value='P' class='checkbox checkbox_align editable' disabled>Pass<br>
					<input id='sump_fail_{$tank['ID']}' name='sump_{$tank['ID']}' type='checkbox' value='F' class='checkbox checkbox_align editable' disabled>Fail
				</td>
				<td><input type='text' name='test_comment_{$tank['ID']}' id='test_comment_{$tank['ID']}' class='editable' disabled></td>
				</tr>";
			} else if ($tank['TANK_STATUS_CODE'] == '5'){
				echo "<tr class='removed_tanks' id='removed_tank_{$tank['ID']}'>
					<td><input type='checkbox' name='checkbox_tank_{$tank['ID']}' class='tank_id_checkbox' id='tank_{$tank['ID']}'>{$tank['ID']}</td>";
				echo "<td><input type='text' list='tester' id='tester_{$tank['ID']}' name='tester_{$tank['ID']}' class='tester editable validate[required]' pattern='/^[a-zA-Z'- ]+$/'  autocomplete='off' disabled />
				<datalist id='tester'>";
				foreach(Model::instance('Ref_test_results_testers')->get_tester_list() as $key => $value) {
					echo "<option value='" . $value . "'>" . $value . "</option>";
				}
				echo "</td>
				<td style='width:300px;'><input type='text' list='testing_company' id='testing_company_{$tank['ID']}' name='testing_company_{$tank['ID']}' class='testing_company editable validate[required]' style='width:400px;' autocomplete='off' disabled />
				<datalist id='testing_company'>";
				foreach(Model::instance('Ref_test_results_test_company')->get_test_company_list() as $key => $value) {
					echo "<option value='" . $value . "'>" . $value . "</option>";
				}
				echo "</td>
				<td><select name='violation_inspection_id_{$tank['ID']}' id='violation_inspection_id_{$tank['ID']}' class='violation_issued editable' disabled>
					<option value='' selected=''>No Violation</option>";
				foreach(Model::instance('Inspections')->get_inspections_by_facility($facility_id) as $inspection) {
					if(!is_null($inspection['NOV_NUMBER'])) {
						echo "<option value='" . $inspection['INSPECTION_ID'] . "'>" . $inspection['NOV_NUMBER'] . "</option>";
					}
				}
				echo "</td>
				<td><input type='text' name='test_date_{$tank['ID']}' id='test_date_{$tank['ID']}' class='test_date datepicker editable validate[required]' disabled></td>
				<td><input type='text' name='test_submitted_date_{$tank['ID']}' id='test_submitted_date_{$tank['ID']}' class='test_submitted_date datepicker editable test_submitted_date' disabled></td>
				<td><input type='text' list='inspector' id='inspector_{$tank['ID']}' name='inspector_{$tank['ID']}' autocomplete='off' class='inspector editable' disabled />
					<datalist id='inspector'>";
					foreach(Model::instance('Test_results')->get_inspector_list() as $inspector) {
						echo "<option value='" . $inspector . "'>" . $inspector . "</option>";
					}
				echo "</td>
				<td><input type='number' name='original_test_id_{$tank['ID']}' id='original_test_id_{$tank['ID']}' class='original_test_id editable' disabled></td>
				<td>
					<input id='alld_pass_{$tank['ID']}' name='alld_{$tank['ID']}' type='checkbox' value='P' class='checkbox checkbox_align editable' disabled>Pass<br>
					<input id='alld_fail_{$tank['ID']}' name='alld_{$tank['ID']}' type='checkbox' value='F' class='checkbox checkbox_align editable' disabled>Fail
				</td>
				<td>
					<input id='sensor_pass_{$tank['ID']}' name='sensor_{$tank['ID']}' type='checkbox' value='P' class='checkbox checkbox_align editable' disabled>Pass<br>
					<input id='sensor_fail_{$tank['ID']}' name='sensor_{$tank['ID']}' type='checkbox' value='F' class='checkbox checkbox_align editable' disabled>Fail
				</td>
				<td>
					<input id='line_tightness_pass_{$tank['ID']}' name='line_tightness_{$tank['ID']}' type='checkbox' value='P' class='checkbox checkbox_align editable' disabled>Pass<br>
					<input id='line_tightness_fail_{$tank['ID']}' name='line_tightness_{$tank['ID']}' type='checkbox' value='F' class='checkbox checkbox_align editable' disabled>Fail
				</td>
				<td>
					<input id='tank_tightness_pass_{$tank['ID']}' name='tank_tightness_{$tank['ID']}' type='checkbox' value='P' class='checkbox checkbox_align editable' disabled>Pass<br>
					<input id='tank_tightness_fail_{$tank['ID']}' name='tank_tightness_{$tank['ID']}' type='checkbox' value='F' class='checkbox checkbox_align editable' disabled>Fail
				</td>
				<td>
					<input id='atg_pass_{$tank['ID']}' name='atg_{$tank['ID']}' type='checkbox' value='P' class='checkbox checkbox_align editable' disabled>Pass<br>
					<input id='atg_fail_{$tank['ID']}' name='atg_{$tank['ID']}' type='checkbox' value='F' class='checkbox checkbox_align editable' disabled>Fail
				</td>
				<td>
					<input id='overfill_pass_{$tank['ID']}' name='overfill_{$tank['ID']}' type='checkbox' value='P' class='checkbox checkbox_align editable' disabled>Pass<br>
					<input id='overfill_fail_{$tank['ID']}' name='overfill_{$tank['ID']}' type='checkbox' value='F' class='checkbox checkbox_align editable' disabled>Fail
				</td>
				<td>
					<input id='corrosion_pass_{$tank['ID']}' name='corrosion_{$tank['ID']}' type='checkbox' value='P' class='checkbox checkbox_align editable' disabled>Pass<br>
					<input id='corrosion_fail_{$tank['ID']}' name='corrosion_{$tank['ID']}' type='checkbox' value='F' class='checkbox checkbox_align editable' disabled>Fail
				</td>
				<td>
					<input id='spill_pass_{$tank['ID']}' name='spill_{$tank['ID']}' type='checkbox' value='P' class='checkbox checkbox_align editable' disabled>Pass<br>
					<input id='spill_fail_{$tank['ID']}' name='spill_{$tank['ID']}' type='checkbox' value='F' class='checkbox checkbox_align editable' disabled>Fail
				</td>
				<td>
					<input id='sump_pass_{$tank['ID']}' name='sump_{$tank['ID']}' type='checkbox' value='P' class='checkbox checkbox_align editable' disabled>Pass<br>
					<input id='sump_fail_{$tank['ID']}' name='sump_{$tank['ID']}' type='checkbox' value='F' class='checkbox checkbox_align editable' disabled>Fail
				</td>
				<td><input type='text' name='test_comment_{$tank['ID']}' id='test_comment_{$tank['ID']}' class='editable' disabled></td>
				</tr>";
			}
		}
	?>
</table>
<?= form::submit('submit', 'Submit', 'class="ui-button ui-state-default ui-corner-all"') ?>



</fieldset>
</form>

<script type="module" src="home/env/Kohana_Applications/tanks/script.js"></script>
<script>
// Select all tanks
function selectAll() {
	$('.tank_id_checkbox').prop('checked', true);
	// enable all the fields excpet removed tanks
	$('.editable').prop('disabled', false);
}

// Unselect all tanks
function selectNone() {
	$('.tank_id_checkbox').prop('checked', false);
	$('.editable').prop('disabled', true);
	$('.editable').val('');
}

// Select all active tanks (CURRENTLY IN USE & TEMPORARILY OUT OF USE)
function selectActive() {
	selectNone();
	$('.active_tanks .tank_id_checkbox').prop('checked', true);
	$('.active_tanks .editable').prop('disabled', false);
}

// Show Removed Tanks
function showRemovedTanks() {
	$('.removed_tanks').toggle();
}

// copy the same field content to different tanks
$('.tester').change(function(){ if($('#tester_all').prop('checked')) $('.tester:enabled').val($(this).val()); });
$('.testing_company').change(function(){ if($('#testing_company_all').prop('checked')) $('.testing_company:enabled').val($(this).val()); });
$('.violation_issued').change(function(){ if($('#violation_issued_all').prop('checked')) $('.violation_issued:enabled').val($(this).val()); });
$('.test_date').change(function(){ if($('#test_date_all').prop('checked')) $('.test_date:enabled').val($(this).val()); });
$('.test_submitted_date').change(function(){ if($('#test_submitted_date_all').prop('checked')) $('.test_submitted_date:enabled').val($(this).val()); });
$('.inspector').change(function(){ if($('#inspector_all').prop('checked')) $('.inspector:enabled').val($(this).val()); });
$('.original_test_id').change(function(){ if($('#original_test_id_all').prop('checked')) $('.original_test_id:enabled').val($(this).val()); });

// Make a group of pass/fail checkboxes mutually exclusive
$('td .checkbox').click(function () {
	checkedState = $(this).prop('checked');
	$(this).parent('td').children('.checkbox:checked').each(function () {
		$(this).prop('checked', false);
	});
	$(this).prop('checked', checkedState);
});

// Select tank to enable and disable date pickers
$.each($('.tank_id_checkbox'), function(){
	var id = this.id;
	var tank_id = id.substring(id.lastIndexOf('_')+1);
	var tank_checkbox_id = 'tank_' + tank_id;
	$('#' + tank_checkbox_id).change(function() {
		if (this.checked) {
			$('#tester_' + tank_id).prop('disabled', false);
			$('#testing_company_' + tank_id).prop('disabled', false);
			$('#test_date_' + tank_id).prop('disabled', false);
			$('#test_submitted_date_' + tank_id).prop('disabled', false);
			$('#inspector_' + tank_id).prop('disabled', false);
			$('#violation_inspection_id_' + tank_id).prop('disabled', false);
			$('#original_test_id_' + tank_id).prop('disabled', false);
			$("input[name=alld_" + tank_id + "]").prop('disabled', false);
			$("input[name=sensor_" + tank_id + "]").prop('disabled', false);
			$("input[name=line_tightness_" + tank_id + "]").prop('disabled', false);
			$("input[name=tank_tightness_" + tank_id + "]").prop('disabled', false);
			$("input[name=atg_" + tank_id + "]").prop('disabled', false);
			$("input[name=overfill_" + tank_id + "]").prop('disabled', false);
			$("input[name=corrosion_" + tank_id + "]").prop('disabled', false);
			$("input[name=spill_" + tank_id + "]").prop('disabled', false);
			$("input[name=sump_" + tank_id + "]").prop('disabled', false);
			$('#test_comment_' + tank_id).prop('disabled', false);		

		} else {
			$('#tester_' + tank_id).prop('disabled', true);
			$('#testing_company_' + tank_id).prop('disabled', true);
			$('#test_date_' + tank_id).prop('disabled', true);
			$('#test_submitted_date_' + tank_id).prop('disabled', true);
			$('#inspector_' + tank_id).prop('disabled', true);
			$('#violation_inspection_id_' + tank_id).prop('disabled', true);
			$('#original_test_id_' + tank_id).prop('disabled', true);
			$("input[name=alld_" + tank_id + "]").prop('disabled', true);
			$("input[name=sensor_" + tank_id + "]").prop('disabled', true);
			$("input[name=line_tightness_" + tank_id + "]").prop('disabled', true);
			$("input[name=tank_tightness_" + tank_id + "]").prop('disabled', true);
			$("input[name=atg_" + tank_id + "]").prop('disabled', true);
			$("input[name=overfill_" + tank_id + "]").prop('disabled', true);
			$("input[name=corrosion_" + tank_id + "]").prop('disabled', true);
			$("input[name=spill_" + tank_id + "]").prop('disabled', true);
			$("input[name=sump_" + tank_id + "]").prop('disabled', true);
			$('#test_comment_' + tank_id).prop('disabled', true);
		}

	});
});
</script>
