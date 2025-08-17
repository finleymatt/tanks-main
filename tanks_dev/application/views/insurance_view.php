<?php
/**
 * Insurance View
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
<h1>Insurance View</h1>
<table class="horiz_table ui-widget ui-corner-all" style="margin-bottom:30px">
	<caption><div class="left_float">Insurance</div>
		<div class="right_float"><?= Controller::_instance('Insurance')->_edit_button($row['ID']) ?>
		<?= Controller::_instance('Insurance')->_delete_button($row['ID']) ?></div>
	</caption>
	<?= html::horiz_table_tr('ID', $row['ID']) ?>
	<?= html::horiz_table_tr('Owner', html::owner_link($row['OWNER_ID']), FALSE) ?>
	<?= html::horiz_table_tr('Facility', html::facility_link($row['FACILITY_ID']), FALSE) ?>
	<?= html::horiz_table_tr('Method',  Model::instance('Financial_methods')->get_financial_method($row['FIN_METH_CODE'])) ?>
	<?= html::horiz_table_tr('Provider', Model::instance('Financial_providers')->get_financial_provider_name($row['FIN_PROV_CODE'])) ?>
	<?= html::horiz_table_tr('Policy Number', $row['POLICY_NUMBER']) ?>
	<?= html::horiz_table_tr('Amount', format::currency($row['AMOUNT'])) ?>
	<?= html::horiz_table_tr('Effective Date', $row['BEGIN_DATE_FMT']) ?>
	<?= html::horiz_table_tr('Expiration Date', $row['END_DATE_FMT']) ?>
	<?= html::horiz_table_tr('# of Tanks Covered', $row['COVERED_TANKS_COUNT']) ?>
	<?= html::table_foot_info($row) ?>
</table>

