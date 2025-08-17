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

?>
<h1>A/B Operator Retraining View</h1>
<table class="horiz_table ui-widget ui-corner-all" style="margin-bottom:30px">
	<caption><div class="left_float">A/B Operator</div><div class="right_float"><?= Controller::_instance('Retraining')->_edit_button($row['RETRAINING_ID']) ?></div></caption>
	<?= html::horiz_table_tr('ID', $row['RETRAINING_ID']) ?>
	<?= html::horiz_table_tr('Date of Inspection', date::new_format_date($row['INSEPECTION_DATE'])) ?>
	<?= html::horiz_table_tr('Designated A/B operator First Name', $row['AB_OPERATOR_FIRST_NAME']) ?>
	<?= html::horiz_table_tr('Designated A/B operator Last Name', $row['AB_OPERATOR_LAST_NAME']) ?>
	<?= html::horiz_table_tr('Certification #', $row['CERT_NUMBER']) ?>
	<?= html::horiz_table_tr('Certification date', date::new_format_date($row['CERT_EXPIRE_DATE'])) ?>
	<?= html::horiz_table_tr('Date notified of retraining requirement', date::new_format_date($row['RETRAIN_NOTIFY_DATE'])) ?>
	<?= html::horiz_table_tr('On-site retraining?', $row['RETRAIN_ONSITE_FL'] ? 'Yes' : 'No') ?>
	<?= html::horiz_table_tr('Date on-site retraining completed', date::new_format_date($row['RETRAIN_COMPLETE_DATE'])) ?>
	<?= html::horiz_table_tr('Inspector condcuting retraining', Model::instance('Staff')->get_name($row['INSPECTOR_TRAIN_STAFF_CODE'])) ?>
	<?php
		$html = '<ul style="margin-left:15px;">';
		foreach($retraining_items as $item) {
			$html .= '<li>' . $item . '</li>';
		}
		$html .= '</ul>';
		echo(html::horiz_table_tr_form('Items trained', $html));
	?>
	<?php //html::horiz_table_tr('Items tranined', $html) ?>
</table>
