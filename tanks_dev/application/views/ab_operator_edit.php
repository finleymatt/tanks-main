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

$facility_id = ($is_add ? $parent_ids[0] : $row['FACILITY_ID']);

?>
<h1>A/B/C Operator - <?= ($is_add ? 'Add New' : 'Edit') ?></h1>

<form action="<?= $action ?>" method="post" class="validate_form edit_form">
<fieldset class='ui-widget ui-widget-content ui-corner-all'>
<legend class='ui-widget ui-widget-header ui-corner-all'>A/B Operator</legend>
<table class="horiz_table ui-widget ui-corner-all">
	<caption><div class="right_float"><?= ($is_add ? '' : Controller::_instance('Ab_operator')->_view_button($row['ID'])) ?></div></caption>
	<?= html::horiz_table_tr_form('Last Name', form::input('last_name', $row['LAST_NAME'], 'class="validate[required]"'), TRUE) ?>
	<?= html::horiz_table_tr_form('First Name', form::input('first_name', $row['FIRST_NAME'], 'class="validate[required]"'), TRUE) ?>
</table>
<?= form::submit('submit', 'Submit', 'class="ui-button ui-state-default ui-corner-all"') ?>
</fieldset>
</form>
