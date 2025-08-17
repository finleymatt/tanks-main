<?php
/**
 * Assigned Inspector Edit View
 *
 * @package ###
 * @subpackage views
 * @uses ###
 * @see ###
 *
*/

$entity_id = ($is_add ? $parent_ids[0] : $row['ENTITY_ID']);
$entity_name = ucwords($is_add ? $parent_ids[1] : $row['ENTITY_TYPE']);

?>
<h1><?= $entity_name ?> Assigned Inspector - <?= ($is_add ? 'Add New' : 'Edit') ?></h1>

<form action="<?= $action ?>" method="post" class="validate_form edit_form" target="main">
<fieldset class='ui-widget ui-widget-content ui-corner-all'>
<legend class='ui-widget ui-widget-header ui-corner-all'>Assigned Inspector</legend>
<table class="horiz_table ui-widget ui-corner-all">
	<?= html::horiz_table_tr_form('Assigned Inspector', form::dropdown('DETAIL_VALUE', Model::instance('Staff')->get_dropdown_inspector('SEP_LOGIN_ID'), $row['DETAIL_VALUE'], 'class="validate[required]"')) ?>
</table>
<?= form::submit('submit', 'Submit', 'class="ui-button ui-state-default ui-corner-all"') ?>
</fieldset>
</form>
