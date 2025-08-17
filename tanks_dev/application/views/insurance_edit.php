<?php
/**
 * Insurance Add/Edit Form
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

$owner_id = ($is_add ? $parent_ids[0] : $row['OWNER_ID']);

$financial_methods = new Financial_methods_Model();
$financial_method_list = $financial_methods->get_dropdown();
$financial_method = $is_add ? '' : Model::instance('Financial_methods')->get_financial_method($row['FIN_METH_CODE']);
$financial_provider = $is_add ? '' : Model::instance('Financial_providers')->get_financial_provider_name($row['FIN_PROV_CODE']);

// remove unwanted financial methods when adding insurance, but keep them when editing for old records
if($is_add) {
	$remove = range(0,10);
	$financial_method_list = array_diff_key($financial_method_list, array_flip($remove));
}
?>
<h1>Insurance <?= ($is_add ? 'Add New' : 'Edit') ?></h1>

<form action="<?= $action ?>" method="post" class="validate_form edit_form">
<fieldset class='ui-widget ui-widget-content ui-corner-all'>
<legend class='ui-widget ui-widget-header ui-corner-all'>Insurance</legend>
<table class="horiz_table ui-widget ui-corner-all">
	<?= html::horiz_table_tr('Facility ID', $owner_id) ?>
	<?= html::horiz_table_tr('Financial Provider', Controller::_instance('Financial_provider')->add_financial_provider_button($owner_id) . Controller::_instance('Financial_provider')->delete_financial_provider_button($owner_id), FALSE) ?>	
</table>
<div style="margin-top:50px">
<input type="checkbox" id="reminder_date_checkbox" class="checkbox_align" onclick="insurance_toggle()"> Submit only Non-Compliance Reminder Letter Send Date
</div>
<div class = "left_float">
<table id="insurance_add" class="horiz_table ui-widget ui-corner-all" style="margin: 0 50px 30px 0">
	<caption><div class="right_float"><?= ($is_add ? '' : Controller::_instance('Insurance')->_view_button($row['ID'])) ?></div></caption>
	<?= html::horiz_table_tr('ID', $row['ID']) ?>
	<?= html::horiz_table_tr('Owner', html::owner_link($owner_id), FALSE) ?>
	<tr>
		<th>Method:</th>
			<td class='ui-widget-content'>
			<input type='text' list='financial_methods' id='fin_meth_code' name='fin_meth_code' style='width:350px;' value='<?= $financial_method ?>'  class='validate[required]' autocomplete="off" />
			<datalist id='financial_methods'>
				<option value="" selected >Select...</option>
				<?php
				foreach(Model::instance('Financial_methods')->get_list('', 'DESCRIPTION') as $key => $value) {
					echo "<option value='" . $value['DESCRIPTION'] . "'>" . $value['DESCRIPTION'] . "</option>";
				}
				?>
			</datalist>
			</td>
		</th>
	</tr>
	<tr>
		<th>Provider:</th>
		<td class='ui-widget-content'>
			<input type='text' list='financial_providers' id='fin_prov_code' name='fin_prov_code' style='width:350px;' value='<?= $financial_provider ?>'  class='validate[required]' autocomplete="off" />
			<datalist id='financial_providers'>
				<option value="" selected >Select...</option>
				<?php
				foreach(Model::instance('Financial_providers')->get_list('', 'DESCRIPTION') as $key => $value) {
					echo "<option value='" . $value['DESCRIPTION'] . "'>" . $value['DESCRIPTION'] . "</option>";
				}
				?>
			</datalist>
		</td>
	</tr>
			
	<?= html::horiz_table_tr_form('Policy Number', form::input('policy_number', $row['POLICY_NUMBER'])) ?>
	<?= html::horiz_table_tr_form('Per Occurrence Amount', form::input('per_occurrence_amount', $row['PER_OCCURRENCE_AMOUNT'], 'class="validate[custom[currency]]"')) ?>
	<?= html::horiz_table_tr_form('Annual Aggregate Amount', form::input('annual_aggregate_amount', $row['ANNUAL_AGGREGATE_AMOUNT'], 'class="validate[custom[currency]]"')) ?>
	<?= html::horiz_table_tr_form('Amount', form::input('amount', $row['AMOUNT'], 'class="validate[custom[currency]]"')) ?>
	<?= html::horiz_table_tr_form('Effecitive Date', form::input('begin_date', $row['BEGIN_DATE_FMT'], 'class="datepicker validate[custom[date2]]"') . 'mm/dd/yyyy') ?>
	<?= html::horiz_table_tr_form('Expiration Date', form::input('end_date', $row['END_DATE_FMT'], 'class="datepicker validate[custom[date2]]"') . 'mm/dd/yyyy') ?>
	<?= html::horiz_table_tr_form('Number of Tanks Covered', form::input('covered_tanks_count', $row['COVERED_TANKS_COUNT'], 'class="validate[custom[integer]]"')) ?>
	<?= html::horiz_table_tr_form('Non-Compliance Reminder Letter Send Date', form::input('non_compl_reminder_letter_date', $row['REMINDER_DATE_FMT'], 'class="datepicker validate[custom[date2]]"') . 'mm/dd/yyyy') ?>
	<?= html::horiz_table_tr_form('Owner Response?', form::checkbox('owner_response_flag', 'Y', ($row['OWNER_RESPONSE_FLAG'] == 'Y'))) ?>
</table>
<?= form::submit('submit', 'Submit', 'class="ui-button ui-state-default ui-corner-all"') ?>
</div>
<div id="policy_checkboxes">
<input class="policy_checkbox" type="checkbox" value="check" required /> Copy of the entire signed insurance policy <br><br>
<input class="policy_checkbox" type="checkbox" value="check" required /> Does this insurance policy NOT include Voluntary Exclusion/Limitation Language? <br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(Exclusions/Limitations for payments for voluntary tank removals and/or site investigation.) <br><br>
<input class="policy_checkbox" type="checkbox" value="check" required /> Does this insurance policy NOT include Self-Insured Retention Language? <br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(The dollar amount an owner or operator must pay before the insurance policy starts paying. <br> 
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;This is not the same as a deductible and may only be recognized as a partial FR mechanism.) <br><br>
<input class="policy_checkbox" type="checkbox" value="check" required /> Does this insurance policy NOT include Loading and Unloading Exclusion/Limitation Language? <br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(Property that the owner is loading or unloading onto is deemed to be property in your <br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;“care, custody, or control.” Damage to such property is excluded under liability insurance.) <br><br>
<input class="policy_checkbox" type="checkbox" value="check"  required/> Does this policy include coverage for both sudden and non-sudden accidental releases for taking corrective action and/or compensating third parties for bodily in jury and property damage? (i.e., sudden vs gradual event) <br><br>
<input class="policy_checkbox" type="checkbox" value="check" required /> Based on the answers to the previous questions, does this policy met the requirements as specified under 20.5.117 NMAC? <br><br>
</div>
</fieldset>
</form>
<script>
function insurance_toggle() {
if($('#reminder_date_checkbox').prop('checked')) {
	$('#insurance_add').find("tr:nth-child(3)").hide();
	$('#insurance_add').find("tr:nth-child(4)").hide();
	$('#insurance_add').find("tr:nth-child(5)").hide();
	$('#insurance_add').find("tr:nth-child(6)").hide();
	$('#insurance_add').find("tr:nth-child(7)").hide();
	$('#insurance_add').find("tr:nth-child(8)").hide();
	$('#insurance_add').find("tr:nth-child(9)").hide();
	$('#insurance_add').find("tr:nth-child(10)").hide();
	$('#insurance_add').find("tr:nth-child(11)").hide();
	$('#policy_checkboxes').hide();
	$('.policy_checkbox').prop('required', false);
} else {
	$('#insurance_add').find("tr:nth-child(3)").show();
	$('#insurance_add').find("tr:nth-child(4)").show();
	$('#insurance_add').find("tr:nth-child(5)").show();
	$('#insurance_add').find("tr:nth-child(6)").show();
	$('#insurance_add').find("tr:nth-child(7)").show();
	$('#insurance_add').find("tr:nth-child(8)").show();
	$('#insurance_add').find("tr:nth-child(9)").show();
	$('#insurance_add').find("tr:nth-child(10)").show();
	$('#insurance_add').find("tr:nth-child(11)").show();
	$('#policy_checkboxes').show();
	$('.policy_checkbox').prop('required', true);
}
}
</script>
