<?php
/**
 * View template for A/B Operator Certificate
 *
 * <p>Display A/B Operator Certificate</p>
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
<h1>A/B/C Operator Certificate View</h1>
<table class="horiz_table ui-widget ui-corner-all" style="margin-bottom:30px">
	<caption><div class="left_float">A/B/C Certificate</div><div class="right_float"><?= Controller::_instance('Ab_cert')->_edit_button($row['ID']) ?></div></caption>
	<?= html::horiz_table_tr('ID', $row['ID']) ?>
	<?= html::horiz_table_tr('Certificate Date', $row['CERT_DATE_FMT']) ?>
	<?= html::horiz_table_tr('Training Level', $row['CERT_LEVEL']) ?>
	<?= html::horiz_table_tr('Certificate#', $row['CERT_NUM']) ?>
	<?= html::table_foot_info($row) ?>
</table>

