<?php
/**
 * file docblock; short description
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
$owner_id = ($is_add ? $parent_ids[1] : $row['OWNER_ID']);

?>
<h1>Tank - <?= ($is_add ? 'Add New' : 'Edit') ?></h1>

<form action="<?= $action ?>" method="post" class="validate_form edit_form">
<fieldset class='ui-widget ui-widget-content ui-corner-all'>
<legend class='ui-widget ui-widget-header ui-corner-all'>Tank</legend>
<table class="horiz_table ui-widget ui-corner-all">
	<caption><div class="right_float"><?= ($is_add ? '' : Controller::_instance('Tank')->_view_button($row['ID'])) ?></div></caption>
	<?= html::horiz_table_tr('ID', $row['ID']) ?>
	<?= html::horiz_table_tr_form('Owner ID', form::input('owner_id', $owner_id, 'class="ui-autocomplete validate[required]"'), TRUE) ?>
	<?= html::horiz_table_tr('Facility', html::facility_link($facility_id), FALSE) ?>
	<?= html::horiz_table_tr_form('Operator ID', form::input('operator_id', $row['OPERATOR_ID'], 'class="ui-autocomplete"')) ?>
	<?= html::horiz_table_tr_form('Tank Type', form::dropdown('tank_type', Tanks_Model::$tank_types, $row['TANK_TYPE'], 'class="validate[required]"'), TRUE) ?>
	<?= html::horiz_table_tr_form('Registration &#35;', form::input('registration_number', $row['REGISTRATION_NUMBER'])) ?>
	<?= html::horiz_table_tr_form('Status', form::dropdown('tank_status_code', Model::instance('Tank_status_codes')->get_dropdown(), $row['TANK_STATUS_CODE'], 'class="validate[required]"'), TRUE) ?>
	<?= html::horiz_table_tr_form('Status Date', form::input('status_date', $row['STATUS_DATE_FMT'], 'class="datepicker validate[custom[date2]]"') . 'mm/dd/yyyy') ?>
	<?= html::horiz_table_tr_form('Capacity', form::input('capacity', $row['CAPACITY'], 'class="validate[custom[integer]]"')) ?>
	<?= html::horiz_table_tr_form('Meets 1988 Req?', form::checkbox('meets_1988_req', 'Y', ($row['MEETS_1988_REQ'] == 'Y'))) ?>
	<?= html::horiz_table_tr_form('Meets 2011 Req?', form::checkbox('meets_2011_req', 'Y', ($row['MEETS_2011_REQ'] == 'Y'))) ?>
	<?= html::horiz_table_tr_form('Move to Duplicate Facility?', form::checkbox('move_2_dup', 'Y', ($row['MOVE_2_DUP'] == 'Y'))) ?>
	<?= html::horiz_table_tr_form('Quantity Remaining', form::input('quantity_remaining', $row['QUANTITY_REMAINING'], 'class="validate[custom[integer]]"')) ?>
	<?= html::horiz_table_tr_form('Fill Material', form::dropdown('fill_material', Model::instance('Tank_fill_material_codes')->get_dropdown(), $row['FILL_MATERIAL'])) ?>
	<?= html::horiz_table_tr_form('Hazardous Substance Mixture?', form::checkbox('hs_mixture', 'Y', ($row['HS_MIXTURE'] == 'Y'))) ?>
	<?= html::horiz_table_tr_form('Hazardous Substance Number', form::input('hs_number', $row['HS_NUMBER'], 'class="validate[custom[integer]]"')) ?>
	<?= html::horiz_table_tr_form('Hazardous Substance Name', form::input('hs_name', $row['HS_NAME'])) ?>
	<?= html::horiz_table_tr_form('Comments', form::textarea(array('name'=>'comments', 'rows'=>3, 'cols'=>45, 'maxlength'=>200), $row['COMMENTS']). 'max 200 chars') ?>
</table>
<?= form::submit('submit', 'Submit', 'class="ui-button ui-state-default ui-corner-all"') ?>
</fieldset>
</form>
<script>
	$(function() {
		$( "#owner_id" ).autocomplete({
			source: "<?= url::site() ?>index.php?owner/autocomplete&",
			minLength: 3
		});

		$( "#operator_id" ).autocomplete({
			source: "<?= url::site() ?>index.php?operator/autocomplete&",
			minLength: 3
		});
	});
</script>
