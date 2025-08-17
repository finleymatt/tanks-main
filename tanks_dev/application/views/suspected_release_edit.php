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

$facility_id = ($is_add ? $parent_ids[0] : $row['FACILITY_ID']);
$tanks = Model::instance('tanks')->get_list('FACILITY_ID=:FACILITY_ID', 'ID', array(':FACILITY_ID' => $facility_id));
$facility_tank_ids = array();
foreach($tanks as $tank) {
	array_push($facility_tank_ids, $tank['ID']);
}

?>
<h1>Suspected Release - <?= ($is_add ? 'Add New' : 'Edit') ?></h1>

<form action="<?= $action ?>" method="post" class="validate_form edit_form">
<fieldset class='ui-widget ui-widget-content ui-corner-all'>
<legend class='ui-widget ui-widget-header ui-corner-all'>Suspected Release</legend>
<table class="horiz_table ui-widget ui-corner-all">
	<caption><div class="right_float"><?= ($is_add ? '' : Controller::_instance('Suspected_Release')->_view_button($row['ID'])) ?></div></caption>
	<?= html::horiz_table_tr('SCSR ID', $row['ID']) ?>
	<?= html::horiz_table_tr('Facility', html::facility_link($facility_id), FALSE) ?>
	<?php
	$html = '';
	foreach($facility_tank_ids as $tank_id) {
		if($is_add) {
			$checked = false;
		} else {
			$checked = in_array($tank_id, $release_tank_ids);
		}
		$html .= form::checkbox(array('name' => "tanks[]", 'id' => "tank_{$tank_id}", 'class' => 'tdc_checkbox validate[required]'), html::h($tank_id), $checked);
		$html .= html::h($tank_id) . "\n";
	}
	echo(html::horiz_table_tr_form('Tanks', $html, TRUE));
	?>
	<?= html::horiz_table_tr_form('Source', form::dropdown('source', Model::instance('Suspected_Releases')->get_suspected_release_source_list(), $row['SR_SOURCE_ID'], 'class="validate[required]"'), TRUE) ?>
	<tr>
		<th class='required'>Cause:</th>
		<td class='ui-widget-content'>
			<input type='text' list='release_cause' id='cause' name='cause' style='width:350px;' value='<?= $row['CAUSE_DESC'] ?>'  class='validate[required]' autocomplete="off" />
			<datalist id='release_cause'>
			<?php
			foreach(Model::instance('Suspected_Releases')->get_suspected_release_cause_list() as $key => $value) {
				echo "<option value='" . $value . "'>" . $value . "</option>";
			}
			?>
		</td>
	</tr>
	<?= html::horiz_table_tr_form('Date Discovered', form::input('date_discovered',$row['DATE_DISCOVERED_FMT'],'class="datepicker validate[required,custom[date2]]"') . ' mm/dd/yyyy',TRUE)?>
	<?= html::horiz_table_tr_form('Date Reported', form::input('date_reported', $row['DATE_REPORTED_FMT'], 'class="datepicker validate[required,custom[date2]]"') . ' mm/dd/yyyy', TRUE) ?>
	<?= html::horiz_table_tr_form('Date Letter Mailed', form::input('date_mailed', $row['SCSR_LETTER_MAILED_DATE_FMT'], 'class="datepicker validate[custom[date2]]"') . ' mm/dd/yyyy') ?>
	<?= html::horiz_table_tr_form('7-Day Rpt Submit Date', form::input('date_seven_day_report_submit', $row['REPORT_SUBMIT_DATE_FMT'], 'class="datepicker validate[custom[date2]]"') . ' mm/dd/yyyy') ?>
	<?= html::horiz_table_tr_form('System Test Date', form::input('date_system_test', $row['SYSTEM_TEST_DATE_FMT'], 'class="datepicker validate[custom[date2]]"') . ' mm/dd/yyyy') ?>
	<?= html::horiz_table_tr_form('Date Closed', form::input('date_closed', $row['CLOSED_DATE_FMT'], 'class="datepicker validate[custom[date2]]"') . ' mm/dd/yyyy') ?>
	<?= html::horiz_table_tr_form('NFA Letter Date ', form::input('date_nfa_letter', $row['NFA_LETTER_DATE_FMT'], 'class="datepicker validate[custom[date2]]"') . ' mm/dd/yyyy') ?>
	<?= html::horiz_table_tr_form('Approved Date', form::input('date_approved', $row['APPROVED_ALT_REPORT_DATE_FMT'], 'class="datepicker validate[custom[date2]]"') . ' mm/dd/yyyy') ?>
	<?= html::horiz_table_tr_form('Confirmed Date', form::input('date_confirmed', $row['CONFIRMED_DATE_FMT'], 'class="datepicker validate[custom[date2]]"') . ' mm/dd/yyyy') ?>
	<?= html::horiz_table_tr_form('Referred Date', form::input('date_referred', $row['REFERRED_DATE_FMT'], 'class="datepicker validate[custom[date2]]"') . ' mm/dd/yyyy') ?>
	<?= html::horiz_table_tr_form('Comments', form::textarea(array('name'=>'comments', 'rows'=>5, 'cols'=>80, 'maxlength'=>500), $row['COMMENTS']) . 'max 500 chars') ?>
</table>
<?= form::submit('submit', 'Submit', 'class="ui-button ui-state-default ui-corner-all"') ?>
</fieldset>
</form>
