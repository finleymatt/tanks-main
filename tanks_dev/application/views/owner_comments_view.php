<?php
/**
 * Owner Comments Detail View
 *
 * <p>Not being used</p>
 *
 * <b>IMPORTANT NOTE</b>
 *
 * @package ### file docblock
 * @subpackage views
 * @uses ###
 * @see ###
 *
*/

$owner_url = url::fullpath("owner/view/{$row[0]['OWNER_ID']}");
?>
<h1>Owner - Comment View</h1>
<table class="horiz_table ui-widget ui-corner-all" style="margin-bottom:30px">
	<caption><div class="left_float">Owner - Comment</div><div class="right_float"> <?= Controller::_instance('Owner_comments')->_edit_button(array($row[0]['OWNER_ID'], $row[0]['ID'])) ?> <?= Controller::_instance('Owner_comments')->_delete_button(array($row[0]['OWNER_ID'], $row[0]['ID'])) ?></div></caption>
	<?= html::horiz_table_tr('Owner', html::owner_link($row[0]['OWNER_ID']), FALSE) ?>
	<?= html::horiz_table_tr('Comment Date', $row[0]['COMMENT_DATE']) ?>
	<?= html::horiz_table_tr('Comments', $row[0]['COMMENTS']) ?>
	<?= html::table_foot_info($row) ?>
</table>

