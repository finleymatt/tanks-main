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
$inspection_id = ($is_add ? $parent_ids[0] : $row['INSPECTION_ID']);

$facility = html::get_parents($this->uri->argument_array(), 'facility');

// find entered penalty codes, so they can be excluded from selection
if ($is_add) {
	$entered_penalty_rows = $model->get_entered($inspection_id);
	$entered_penalty_arr = array();
	foreach($entered_penalty_rows as $entered_row)
		$entered_penalty_arr[] = $entered_row['PENALTY_CODE'];

	if (count($entered_penalty_arr))
		$filter_sql = "code not in ('" . implode("','", $entered_penalty_arr) . "')";
	else
		$filter_sql = NULL;
}

// get list of Class A penalties for form validation
$class_a = array_column(Model::instance('Penalty_codes')->get_list("PENALTY_LEVEL = 'A'"), 'CODE');
?>

<h1>Penalty - <?= ($is_add ? 'Add New' : 'Edit') ?></h1>

<form action="<?= $action ?>" method="post" class="validate_form edit_form">
<fieldset class='ui-widget ui-widget-content ui-corner-all'>
<legend class='ui-widget ui-widget-header ui-corner-all'>Penalty</legend>
<table class="horiz_table ui-widget ui-corner-all">
	<caption><div class="right_float"><?= ($is_add ? '' : Controller::_instance('Penalty')->_view_button(array($row['INSPECTION_ID'], $row['PENALTY_CODE'], $row['TANK_ID']))) ?></div></caption>

	<?= html::horiz_table_tr('Inspection ID', $inspection_id) ?>
	<?= ($is_add ?
		html::horiz_table_tr_form('Penalty', form::dropdown('penalty_code', 
			Model::instance('Penalty_codes')->get_active_codes_dropdown(NULL, NULL, $filter_sql), $row['PENALTY_CODE'], 'class="lock_field validate[required]"')
			. '<br /><br />Following penalties already exist for this inspection: ' . implode(', ', $entered_penalty_arr)
			. '<br /><br /><input type="checkbox" id="old_penalties" class="checkbox_align" onclick="penalties_toggle()"> Include penalties no longer in use', TRUE) :
		html::horiz_table_tr('Penalty', $row['PENALTY_CODE']));
	 ?>

	<!--<?= html::horiz_table_tr_form('Tank', form::dropdown('tank_id', Model::instance('tanks')->get_dropdown(NULL, NULL, "facility_id = {$facility['ids'][0]}"), $row['TANK_ID'], 'class="validate[required]"'), TRUE) ?>
	<?= html::horiz_table_tr_form('No. of Occurrence', form::input('penalty_occurance', $row['PENALTY_OCCURANCE'], 'class="validate[custom[integer]]"')) ?>
	<?= html::horiz_table_tr_form('<span title="List of Compliance Concerns">LCC</span> Date', form::input('lcc_date', $row['LCC_DATE_FMT'], 'class="datepicker validate[custom[date2]]"') . 'mm/dd/yyyy') ?>
	<?= html::horiz_table_tr_form('Date <span title="Notice of Violation">NOV</span> Issued', form::input('nov_date', $row['NOV_DATE_FMT'], 'class="datepicker lock_field validate[custom[date2]]"') . 'mm/dd/yyyy') ?>
	<?= html::horiz_table_tr_form('Date <span title="Notice of Deficiency">NOD</span> Issued', form::input('nod_date', $row['NOD_DATE_FMT'], 'class="datepicker lock_field validate[custom[date2]]"') . 'mm/dd/yyyy') ?>
	<?= html::horiz_table_tr_form('Date <span title="Notice of Intent to Red Tag">NOIRT</span> Issued', form::input('noirt_date', $row['NOIRT_DATE_FMT'], 'class="datepicker lock_field validate[custom[date2]]"') . 'mm/dd/yyyy') ?>
	<?= html::horiz_table_tr_form('Date Red Tag Placed', form::input('redtag_placed_date', $row['REDTAG_PLACED_DATE_FMT'], 'class="datepicker lock_field validate[custom[date2]]"') . 'mm/dd/yyyy') ?>
	<?= html::horiz_table_tr_form('Date Corrected', form::input('date_corrected', $row['DATE_CORRECTED_FMT'], 'class="datepicker validate[custom[date2]]"') . 'mm/dd/yyyy') ?> -->
</table>

<?php $inactive_penalty_codes = htmlspecialchars(Model::instance('Penalty_codes')->get_inactive_codes(NULL, NULL)); ?>
<input type="hidden" name="inactive_penalty_codes" id="inactive_penalty_codes" value="<?php echo $inactive_penalty_codes; ?>" >
<button type="button" id="select_all" onclick="selectAll()" style="margin-top:30px">Select All</button>
<button type="button" id="select_none" onclick="selectNone()">Select None</button>
<button type="button" id="select_active" onclick="selectActive()">Select Active</button>
<button type="button" id="show_removed_tanks" onclick="showRemovedTanks()">Show/Hide Removed Tanks</button>
<table class="horiz_table ui-widget ui-corner-all" style="width:2000px;margin-top:5px">
	<tr>
		<td>Tank</td>
		<td>No. of Occurence&nbsp<input type="checkbox" id="occurence_all" class="checkbox_align">ALL</td>
		<td><a href="#" title="Pre 07/24/2018">NOV/LCC Date&nbsp<i class="fa fa-info-circle" style="font-size:16px;color:grey"></i></a>&nbsp<input type="checkbox" id="lcc_date_all" class="checkbox_align">ALL</td>
		<td><a href="#" title="Post 07/24/2018">NOV Date&nbsp<i class="fa fa-info-circle" style="font-size:16px;color:grey"></i></a>&nbsp<input type="checkbox" id="nov_date_all" class="checkbox_align">ALL</td>
		<td>LCAV Date&nbsp<input type="checkbox" id="lcav_date_all" class="checkbox_align">ALL</td>
		<td>NOD Date&nbsp<input type="checkbox" id="nod_date_all" class="checkbox_align">ALL</td>
		<td>NOIRT Date&nbsp<input type="checkbox" id="noirt_date_all" class="checkbox_align">ALL</td>
		<td>Red Tag Placement Date&nbsp<input type="checkbox" id="red_tag_placement_date_all" class="checkbox_align">ALL</td>
		<td>Red Tag Removal Date&nbsp<input type="checkbox" id="red_tag_removal_date_all" class="checkbox_align">ALL</td>
		<td>Date Corrected&nbsp<input type="checkbox" id="date_corrected_all" class="checkbox_align">ALL</td>
		<td>NTRF Date&nbsp<input type="checkbox" id="ntrf_date_all" class="checkbox_align">ALL</td>
	</tr>

	<?php
		$tanks = Model::instance('tanks')->get_list('FACILITY_ID=:FACILITY_ID', 'ID', array(':FACILITY_ID' => $facility['ids'][0]));

		foreach($tanks as $tank) {
			if($tank['TANK_STATUS_CODE'] == '1' || $tank['TANK_STATUS_CODE'] == '2'){
				echo "<tr class='active_tanks'>
				<td><input type='checkbox' name='checkbox_tank_{$tank['ID']}' class='tank_id_checkbox' id='tank_{$tank['ID']}'>{$tank['ID']}</td>
				<td><input type='number' name='occurrence_{$tank['ID']}' id='occurrence_{$tank['ID']}' class='editable penalty_occurence' min='1' max='999999999' disabled></td>
				<td><input type='text' name='lcc_date_{$tank['ID']}' id='lcc_date_{$tank['ID']}' class='datepicker editable penalty_lcc_date' disabled></td>
				<td><input type='text' name='nov_date_{$tank['ID']}' id='nov_date_{$tank['ID']}' class='datepicker editable penalty_nov_date' disabled></td>
				<td><input type='text' name='lcav_date_{$tank['ID']}' id='lcav_date_{$tank['ID']}' class='datepicker editable penalty_lcav_date' disabled></td>
				<td><input type='text' name='nod_date_{$tank['ID']}' id='nod_date_{$tank['ID']}' class='datepicker editable penalty_nod_date' disabled></td>
				<td><input type='text' name='noirt_date_{$tank['ID']}' id='noirt_date_{$tank['ID']}' class='datepicker editable penalty_noirt_date' disabled></td>
				<td><input type='text' name='red_tag_placement_date_{$tank['ID']}' id='red_tag_placement_date_{$tank['ID']}' class='datepicker editable penalty_red_tag_placement_date' style='width:200px' disabled></td>
				<td><input type='text' name='red_tag_removal_date_{$tank['ID']}' id='red_tag_removal_date_{$tank['ID']}' class='datepicker editable penalty_red_tag_removal_date' style='width:200px' disabled></td>
				<td><input type='text' name='date_corrected_{$tank['ID']}' id='date_corrected_{$tank['ID']}' class='datepicker editable penalty_date_corrected' disabled></td>
				<td><input type='text' name='ntrf_date_{$tank['ID']}' id='ntrf_date_{$tank['ID']}' class='datepicker editable penalty_ntrf_date' disabled></td>
				</tr>";
			}else if($tank['TANK_STATUS_CODE'] == '5'){
				echo "<tr class='removed_tanks' id='removed_tank_{$tank['ID']}'>
				<td><input type='checkbox' name='checkbox_tank_{$tank['ID']}' class='tank_id_checkbox removed' id='tank_{$tank['ID']}'>{$tank['ID']}</td>
				<td><input type='number' name='occurrence_{$tank['ID']}' id='occurrence_{$tank['ID']}' min='1' max='999999999' disabled></td>
				<td><input type='text' name='lcc_date_{$tank['ID']}' id='lcc_date_{$tank['ID']}' class='datepicker' disabled></td>
				<td><input type='text' name='nov_date_{$tank['ID']}' id='nov_date_{$tank['ID']}' class='datepicker' disabled></td>
				<td><input type='text' name='lcav_date_{$tank['ID']}' id='lcav_date_{$tank['ID']}' class='datepicker' disabled></td>
				<td><input type='text' name='nod_date_{$tank['ID']}' id='nod_date_{$tank['ID']}' class='datepicker' disabled></td>
				<td><input type='text' name='noirt_date_{$tank['ID']}' id='noirt_date_{$tank['ID']}' class='datepicker' disabled></td>
				<td><input type='text' name='red_tag_placement_date_{$tank['ID']}' id='red_tag_placement_date_{$tank['ID']}' class='datepicker' style='width:200px' disabled></td>
				<td><input type='text' name='red_tag_removal_date_{$tank['ID']}' id='red_tag_removal_date_{$tank['ID']}' class='datepicker' style='width:200px' disabled></td>
				<td><input type='text' name='date_corrected_{$tank['ID']}' id='date_corrected_{$tank['ID']}' class='datepicker editable penalty_date_corrected' disabled></td>
				<td><input type='text' name='ntrf_date_{$tank['ID']}' id='ntrf_date_{$tank['ID']}' class='datepicker' disabled></td>
				</tr>";
			}
		}
	?>
</table>
<?= form::submit('submit', 'Submit', 'class="ui-button ui-state-default ui-corner-all"') ?>
<input type='hidden' id='hidden_inspection_id' value="<?php echo $inspection_id; ?>" disabled>
<input type='hidden' id='hidden_penalty_code' value="<?php echo $row['PENALTY_CODE']; ?>" disabled>
<input type='hidden' id='dynamic_url' value="<?php echo url::fullpath('/'); ?>" disabled>
</fieldset>
</form>

<script type="module" src="home/env/Kohana_Applications/tanks/script.js"></script>
<script>
var domain = $('#dynamic_url').val().replace("tanks.", "");
loadPenaltyDetail();

// include and exclude old penalties in the drop down list
function penalties_toggle() {
	// convert inactive penalty codes from string to an array
	var inactive_penalty_codes_arr = $('#inactive_penalty_codes').val().split(';');

	$.each(inactive_penalty_codes_arr, function(i, result) {
		var inactive_penalty_code = result.split('|');
		if($('#old_penalties').prop('checked')) {
			$('#penalty_code').append("<option value='" + inactive_penalty_code[0] + "'>" + inactive_penalty_code[1] + "</option>");
		} else {

			$("#penalty_code option[value='" + inactive_penalty_code[0] + "']").remove();
		}
	});
}

// retrieve penalty detail
function loadPenaltyDetail() {
	var inspection_id = $('#hidden_inspection_id').val();
	var penalty_code = $('#hidden_penalty_code').val();
	var url = domain + 'data/insp/getpen?n_inparm_inspection_id=' + inspection_id + '&vc_inparm_penalty_code=' + penalty_code + '&vc_inparm_ustr_number=' + 600 + '&flag_outvar=[length=1,type=chr,value=]&msg_outvar=[length=500,type=chr,value=]&out_cur';
	$.ajax({
		url: url,
		method:"GET",
		dataType: 'json',
		success: function(data) {
			var tanks = data.result['out_cur'];
			$.each(tanks, function(i, result) {
				tank_id = result['TANK_ID'];

				// enable all the tank fields
				$('#tank_' + tank_id).prop('checked', true);
				$('#occurrence_' + tank_id).prop('disabled', false);
				$('#lcc_date_' + tank_id).prop('disabled', false);
				$('#nov_date_' + tank_id).prop('disabled', false);
				$('#lcav_date_' + tank_id).prop('disabled', false);
				$('#nod_date_' + tank_id).prop('disabled', false);
				$('#noirt_date_' + tank_id).prop('disabled', false);
				$('#red_tag_placement_date_' + tank_id).prop('disabled', false);
				$('#red_tag_removal_date_' + tank_id).prop('disabled', false);
				$('#date_corrected_' + tank_id).prop('disabled', false);
				$('#ntrf_date_' + tank_id).prop('disabled', false);

				// fill all the fields
				$('#occurrence_' + tank_id).val((result['PENALTY_OCCURANCE'] == '99999') ? '' : result['PENALTY_OCCURANCE']);
				$('#lcc_date_' + tank_id).val((result['LCC_DATE'] == '01-JAN-68') ? '' : format_date(result['LCC_DATE']));
				$('#nov_date_' + tank_id).val((result['NOV_DATE'] == '01-JAN-68') ? '' : format_date(result['NOV_DATE']));
				$('#lcav_date_' + tank_id).val((result['LCAV_DATE'] == '01-JAN-68') ? '' : format_date(result['LCAV_DATE']));
				$('#nod_date_' + tank_id).val((result['NOD_DATE'] == '01-JAN-68') ? '' : format_date(result['NOD_DATE']));
				$('#noirt_date_' + tank_id).val((result['NOIRT_DATE'] == '01-JAN-68') ? '' : format_date(result['NOIRT_DATE']));
				$('#red_tag_placement_date_' + tank_id).val((result['REDTAG_PLACED_DATE'] == '01-JAN-68') ? '' : format_date(result['REDTAG_PLACED_DATE']));
				$('#red_tag_removal_date_' + tank_id).val((result['REDTAG_REMOVED_DATE'] == '01-JAN-68') ? '' : format_date(result['REDTAG_REMOVED_DATE']));
				$('#date_corrected_' + tank_id).val((result['DATE_CORRECTED'] == '01-JAN-68') ? '' : format_date(result['DATE_CORRECTED']));
				$('#ntrf_date_' + tank_id).val((result['NTRF_DATE'] == '01-JAN-68') ? '' : format_date(result['NTRF_DATE']));
			});
		}
	});
}

// convert DD-MMM-YY to YYYY-MM-DD
function format_date(date) {
if (date === null){
		return null;
	}else if (date == ''){
		return date;
	}else{
		var formatted_date = moment(date, "DD-MMM-YY").format("MM/DD/YYYY");
		return formatted_date;
	}
}

// Show Removed Tanks
function showRemovedTanks() {
	$('.removed_tanks').toggle();
}
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

// Select tank to enable and disable date pickers
$.each($('.tank_id_checkbox'), function(){
	var id = this.id;
	var tank_id = id.substring(id.lastIndexOf('_')+1);
	var tank_checkbox_id = 'tank_' + tank_id;
	$('#' + tank_checkbox_id).change(function() {
		// inactived tanks only enable data corrected active tanks enable all fields
		if ($(this).hasClass('removed')) {
			if (this.checked) {
				$('#date_corrected_' + tank_id).prop('disabled', false);
				//$('#date_corrected_' + tank_id).prop('required', true);
			} else {
				$('#date_corrected_' + tank_id).prop('disabled', true);
				$('#date_corrected_' + tank_id).val('');
				//$('#date_corrected_' + tank_id).prop('required', false);
			}
		} else {
			if (this.checked) {
				$('#occurrence_' + tank_id).prop('disabled', false);
				$('#lcc_date_' + tank_id).prop('disabled', false);
				$('#nov_date_' + tank_id).prop('disabled', false);
				$('#lcav_date_' + tank_id).prop('disabled', false);
				$('#nod_date_' + tank_id).prop('disabled', false);
				$('#noirt_date_' + tank_id).prop('disabled', false);
				$('#red_tag_placement_date_' + tank_id).prop('disabled', false);
				$('#red_tag_removal_date_' + tank_id).prop('disabled', false);
				$('#date_corrected_' + tank_id).prop('disabled', false);
				$('#ntrf_date_' + tank_id).prop('disabled', false);
				/*$('#occurrence_' + tank_id).prop('required', true);
				$('#lcc_date_' + tank_id).prop('required', true);
				$('#nov_date_' + tank_id).prop('required', true);
				$('#lcav_date_' + tank_id).prop('required', true);
				$('#nod_date_' + tank_id).prop('required', true);
				$('#noirt_date_' + tank_id).prop('required', true);
				$('#red_tag_placement_date_' + tank_id).prop('required', true);
				$('#red_tag_removal_date_' + tank_id).prop('required', true);
				$('#date_corrected_' + tank_id).prop('required', true);
				$('#ntrf_date_' + tank_id).prop('required', true);*/
			} else {
				$('#occurrence_' + tank_id).prop('disabled', true);
				$('#lcc_date_' + tank_id).prop('disabled', true);
				$('#nov_date_' + tank_id).prop('disabled', true);
				$('#lcav_date_' + tank_id).prop('disabled', true);
				$('#nod_date_' + tank_id).prop('disabled', true);
				$('#noirt_date_' + tank_id).prop('disabled', true);
				$('#red_tag_placement_date_' + tank_id).prop('disabled', true);
				$('#red_tag_removal_date_' + tank_id).prop('disabled', true);
				$('#date_corrected_' + tank_id).prop('disabled', true);
				$('#ntrf_date_' + tank_id).prop('disabled', true);
				$('#occurrence_' + tank_id).val('');
				$('#lcc_date_' + tank_id).val('');
				$('#nov_date_' + tank_id).val('');
				$('#lcav_date_' + tank_id).val('');
				$('#nod_date_' + tank_id).val('');
				$('#noirt_date_' + tank_id).val('');
				$('#red_tag_placement_date_' + tank_id).val('');
				$('#red_tag_removal_date_' + tank_id).val('');
				$('#date_corrected_' + tank_id).val('');
				$('#ntrf_date_' + tank_id).val('');
				/*$('#occurrence_' + tank_id).prop('required', false);
				$('#lcc_date_' + tank_id).prop('required', false);
				$('#nov_date_' + tank_id).prop('required', false);
				$('#lcav_date_' + tank_id).prop('required', false);
				$('#nod_date_' + tank_id).prop('required', false);
				$('#noirt_date_' + tank_id).prop('required', false);
				$('#red_tag_placement_date_' + tank_id).prop('required', false);
				$('#red_tag_removal_date_' + tank_id).prop('required', false);
				$('#date_corrected_' + tank_id).prop('required', false);
				$('#ntrf_date_' + tank_id).prop('required', false);*/
			}
		}
	});
});

// copy the same date to different tanks
$('.penalty_occurence').change(function(){ if($('#occurence_all').prop('checked')) $('.penalty_occurence:enabled').val($(this).val()); });
$('.penalty_lcc_date').change(function(){ if($('#lcc_date_all').prop('checked')) $('.penalty_lcc_date:enabled').val($(this).val()); });
$('.penalty_nov_date').change(function(){ if($('#nov_date_all').prop('checked')) $('.penalty_nov_date:enabled').val($(this).val()); });
$('.penalty_lcav_date').change(function(){ if($('#lcav_date_all').prop('checked')) $('.penalty_lcav_date:enabled').val($(this).val()); });
$('.penalty_nod_date').change(function(){ if($('#nod_date_all').prop('checked')) $('.penalty_nod_date:enabled').val($(this).val()); });
$('.penalty_noirt_date').change(function(){ if($('#noirt_date_all').prop('checked')) $('.penalty_noirt_date:enabled').val($(this).val()); });
$('.penalty_red_tag_placement_date').change(function(){ if($('#red_tag_placement_date_all').prop('checked')) $('.penalty_red_tag_placement_date:enabled').val($(this).val()); });
$('.penalty_red_tag_removal_date').change(function(){ if($('#red_tag_removal_date_all').prop('checked')) $('.penalty_red_tag_removal_date:enabled').val($(this).val()); });
$('.penalty_ntrf_date').change(function(){ if($('#ntrf_date_all').prop('checked')) $('.penalty_ntrf_date:enabled').val($(this).val()); });
$('.penalty_date_corrected').change(function(){ if($('#date_corrected_all').prop('checked')) $('.penalty_date_corrected:enabled').val($(this).val()); });

$(function() {
	var class_a = <?= json_encode($class_a) ?>;

	/**
	 * Enable or disable dependent fields. Class A penalties goes straight to
	 * Red Tag, while B goes through complete process.
	 */
	function check_lock_fields() {
		if ('<?= $row['PENALTY_CODE'] ?>' != '')  // edit form
			penalty_code = '<?= $row['PENALTY_CODE'] ?>';
		else
			penalty_code = $('#penalty_code').val();

		if (penalty_code && (jQuery.inArray(penalty_code, class_a) == -1))
			penalty_class = 'B';
		else if (penalty_code && (jQuery.inArray(penalty_code, class_a) != -1))
			penalty_class = 'A';
		else
			penalty_class = null;
		
		lock_field((penalty_class == 'B'), 'nov_date');
		lock_field($('#nov_date').val(), 'nod_date');
		lock_field($('#nod_date').val(), 'noirt_date');
		lock_field(($('#noirt_date').val() || (penalty_class == 'A')), 'redtag_placed_date');
	}

	function lock_field(cond, field_class) {
		field_class = '#' + field_class;
		if (cond)
			$(field_class).attr("disabled", false);
		else {
			$(field_class).val('');
			$(field_class).attr("disabled", true);
		}

	}

	$(".lock_field").bind('change', function(event){
		check_lock_fields();
	});

	check_lock_fields(); // to init on load
});
</script>
