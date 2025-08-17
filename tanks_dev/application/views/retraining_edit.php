<?php
/**
 * A/B Operator Add/Edit form
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

$facility_id = ($is_add ? $parent_ids[0] : $facility_id);
$retraining_item_ids = ($is_add ? array() : $retraining_item_ids);

?>

<h1>A/B Operator Retraining - <?= ($is_add ? 'Add New' : 'Edit') ?></h1>

<form action="<?= $action ?>" method="post" class="validate_form edit_form">
<fieldset class='ui-widget ui-widget-content ui-corner-all'>
<legend class='ui-widget ui-widget-header ui-corner-all'>A/B Operator Retraining</legend>
<table class="horiz_table ui-widget ui-corner-all">

	<caption><div class="left_float">A/B Operator Retraining</div><div class="right_float"><?= ($is_add ? '' : Controller::_instance('Retraining')->_view_button($row['RETRAINING_ID'])) ?></div></caption>
	<?= html::horiz_table_tr('ID', $row['RETRAINING_ID']) ?>
	<?= html::horiz_table_tr_form('Date of Inspection', form::input('date_inspected', date::new_format_date($row['INSEPECTION_DATE']), 'class="datepicker validate[custom[date2]] validate[required]"') . 'mm/dd/yyyy', TRUE) ?>
	<?= html::horiz_table_tr_form('Designated A/B operator', form::dropdown('ab_operator', Model::instance('Ab_operator')->get_dropdown_ab_operators_by_facility('ID', "FIRST_NAME || ' ' || LAST_NAME", array('facility_id' => $facility_id)), $row['AB_OPERATOR_ID'],  'class="validate[required]"'), TRUE) ?>
	
	<?= html::horiz_table_tr_form('Certification #', form::input('cert_number', $row['CERT_NUMBER'], 'class="validate[required]"'), TRUE) ?>
	<?= html::horiz_table_tr_form('Certification Expiration Date', form::input('expire_date', date::new_format_date($row['CERT_EXPIRE_DATE']), 'class="datepicker validate[custom[date2]] validate[required]"') . 'mm/dd/yyyy', TRUE) ?>
	<?= html::horiz_table_tr_form('Date notified of retraining requirement', form::input('notified_date', date::new_format_date($row['RETRAIN_NOTIFY_DATE']), 'class="datepicker validate[custom[date2]] validate[required]"') . 'mm/dd/yyyy', TRUE) ?>
	<?= html::horiz_table_tr_form('On-site retraining', form::checkbox('onsite', '1', ($row['RETRAIN_ONSITE_FL'] == '1'))) ?>
	<?= html::horiz_table_tr_form('Date on-site retraining completed', form::input('complete_date', date::new_format_date($row['RETRAIN_COMPLETE_DATE']), 'class="datepicker validate[custom[date2]] validate[required]"') . 'mm/dd/yyyy', TRUE) ?>
	<?= html::horiz_table_tr_form('Inspector conducting retraining', form::dropdown('staff_code', Model::instance('Staff')->get_dropdown(), $row['INSPECTOR_TRAIN_STAFF_CODE'], 'class="validate[required]"'), TRUE) ?>
	<?php 
		$dropdown_rows = Model::instance('Ref_training_item')->get_dropdown();
		// remove select row
		if(array_key_exists('', $dropdown_rows)) unset($dropdown_rows['']);	
		$html = '';
		foreach($dropdown_rows as $id => $desc) {
			$html .= form::checkbox(array('name' => "items_trained[]", 'id' => "items_trained_{$id}", 'class' => 'tdc_checkbox validate[required]'), html::h($id), in_array($id, $retraining_item_ids));
			$html .= html::h($desc) . "<br />\n";
		}
		echo(html::horiz_table_tr_form('Items trained', $html, TRUE));
	?>
</table>
<?= form::submit('submit', 'Submit', 'class="ui-button ui-state-default ui-corner-all"') ?>
</fieldset>
</form>
