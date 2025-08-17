<?php
/**
 * Owner Comment Add View
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

$owner_id = ($is_add ? $parent_ids[0] : $row['OWNER_ID']);
?>
<h1>Owner - Comment Add New</h1>

<form action="<?= $action ?>" method="post" class="validate_form edit_form">
<fieldset class='ui-widget ui-widget-content ui-corner-all'>
<legend class='ui-widget ui-widget-header ui-corner-all'>Owner Comment</legend>
<table class="horiz_table ui-widget ui-corner-all">
	<caption></caption>
	<?= html::horiz_table_tr('Owner', html::owner_link($owner_id, ''), FALSE) ?>
	<?= html::horiz_table_tr_form('Comments', form::textarea(array('name'=>'comments', 'rows'=>7, 'cols'=>80, 'maxlength'=>1000), $row['COMMENTS']) . 'max 1000 chars') ?>
</table>
<?= form::submit('submit', 'Submit', 'class="ui-button ui-state-default ui-corner-all"') ?>
</fieldset>
</form>
