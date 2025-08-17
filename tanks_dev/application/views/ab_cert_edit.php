<?php
/**
 * A/B Operator Certificate Add/Edit form
 *
 * @package ### file docblock
 * @subpackage views
 * @uses ###
 * @see ###
 *
*/

$ab_operator_id = ($is_add ? $parent_ids[0] : $row['AB_OPERATOR_ID']);
$ab_operator_row = Model::instance('Ab_operator')->get_row($ab_operator_id);
?>
<h1><?= ($is_add ? 'Add New' : 'Edit') ?> A/B Operator Certificate for <?= $ab_operator_row['FULL_NAME'] ?></h1>

<form action="<?= $action ?>" method="post" class="validate_form edit_form">
<fieldset class='ui-widget ui-widget-content ui-corner-all'>
<legend class='ui-widget ui-widget-header ui-corner-all'>A/B Certificate</legend>
<table class="horiz_table ui-widget ui-corner-all">
	<caption><div class="right_float"><?= ($is_add ? '' : Controller::_instance('Ab_cert')->_view_button($row['ID'])) ?></div></caption>
	<?= html::horiz_table_tr_form('Certificate Date', form::input('cert_date', $row['CERT_DATE_FMT'], 'class="datepicker validate[custom[date2]]"') . 'mm/dd/yyyy') ?>
	<?= html::horiz_table_tr_form('Certificate Level', form::dropdown('cert_level', Ab_cert_Model::$cert_level_types, $row['CERT_LEVEL'], 'class="validate[required]"'), TRUE) ?>
	<?= html::horiz_table_tr_form('Certificate&#35;', form::input('cert_num', $row['CERT_NUM'])) ?>
</table>
<?= form::submit('submit', 'Submit', 'class="ui-button ui-state-default ui-corner-all"') ?>
</fieldset>
</form>
