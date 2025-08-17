<?php
/**
 * Email Edit View
 *
 * <p>This view is used for owner, facility, and operator for maintaining email addresses.</p>
 * Redirects after "add" will be determined by $this->_parent_url($entity_id),
 * where "edit" and "delete" will redirect to $this->_parent_url(NULL, $row['ID'])
 *
 * @package ###
 * @subpackage views
 * @uses ###
 * @see ###
 *
*/

$entity_id = ($is_add ? $parent_ids[0] : $row['ENTITY_ID']);
$entity_name = ucwords($is_add ? $parent_ids[1] : $row['ENTITY_TYPE']);
$entity_link = (($entity_name == 'Owner') ?
	html::owner_link($entity_id) : html::facility_link($entity_id));
?>
<h1><?= $entity_name ?> Contact - <?= ($is_add ? 'Add New' : 'Edit') ?></h1>

<form action="<?= $action ?>" method="post" class="validate_form edit_form">
<fieldset class='ui-widget ui-widget-content ui-corner-all'>
<legend class='ui-widget ui-widget-header ui-corner-all'>Email</legend>
<table class="horiz_table ui-widget ui-corner-all">
	<?= html::horiz_table_tr($entity_name, $entity_link, FALSE) ?>
	<?= html::horiz_table_tr_form('Title', form::input('title', $row['TITLE'], 'size=30 maxlength=100')) ?>
	<?= html::horiz_table_tr_form('Full Name', form::input('fullname', $row['FULLNAME'], 'size=30 maxlength=100')) ?>
	<?= html::horiz_table_tr_form('Contact Type', form::dropdown('contact_type_id', Model::instance('Ref_contact_type')->get_sorted_dropdown(), $row['CONTACT_TYPE_ID'], 'class="validate[required]"'), TRUE) ?>
	<?= html::horiz_table_tr_form('Email', form::input('email', $row['EMAIL'], 'class="validate[custom[email]]" size=45 maxlength=100')) ?>
	<?= html::horiz_table_tr_form('Phone', form::input('phone', $row['PHONE'], 'class="validate[custom[phone]]" size=25 maxlength=25')) ?>
	<?= html::horiz_table_tr_form('Comments', form::textarea(array('name'=>'comments', 'rows'=>5, 'cols'=>80, 'maxlength'=>500), $row['COMMENTS']) . 'max 500 chars') ?>
</table>
<?= form::cancel_button() ?>
<?= form::submit('submit', 'Submit', 'class="ui-button ui-state-default ui-corner-all"') ?>
</fieldset>
</form>
