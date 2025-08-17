<?php
/**
 * Notice View
 *
 * @package ### file docblock
 * @subpackage views
 * @uses ###
 * @see ###
 *
*/
?>
<h1>Notice View</h1>
<table class="horiz_table ui-widget ui-corner-all" style="margin-bottom:30px">
	<caption><div class="left_float">Notice</div><div class="right_float"> <?= Controller::_instance('Notice')->_delete_button($row['ID']) ?> <?= Controller::_instance('Notice')->_print_button($row['ID']) ?></div></caption>
	<?= html::horiz_table_tr('ID', $row['ID']) ?>
	<?= html::horiz_table_tr('Operator', html::operator_link($row['OPERATOR_ID']), FALSE) ?>
	<?= html::horiz_table_tr('Notice Code', $row['NOTICE_CODE']) ?>
	<?= html::horiz_table_tr('Notice Date', $row['NOTICE_DATE']) ?>
	<!-- <?= html::horiz_table_tr('Due Date', $row['DUE_DATE']) ?> -->
	<?= html::horiz_table_tr('Letter Date', $row['LETTER_DATE']) ?>
</table>


