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

$owner_id = ($is_add ? $parent_ids[0] : $row['OWNER_ID']);
$owner_url = html::owner_link($owner_id);
$invoice_url = url::fullpath("invoice/view/{$row['INVOICE_ID']}");
?>
<h1>Transaction <?= ($is_add ? 'Add New' : 'Edit') ?></h1>

<form action="<?= $action ?>" method="post" class="validate_form edit_form">
<fieldset class='ui-widget ui-widget-content ui-corner-all'>
<legend class='ui-widget ui-widget-header ui-corner-all'>Transaction</legend>
<table class="horiz_table ui-widget ui-corner-all">
	<caption><div class="right_float"><?= ($is_add ? '' : Controller::_instance('Transaction')->_view_button($row['ID'])) ?></div></caption>
	<?= html::horiz_table_tr('ID', $row['ID']) ?>
	<?= html::horiz_table_tr('Owner', html::owner_link($owner_id), FALSE) ?>
	<?= html::horiz_table_tr('Inspection ID', $row['INSPECTION_ID']) ?>
	<!-- invoice id not enterable directly <?= html::horiz_table_tr_form('Invoice ID', "<a href='{$invoice_url}'>{$row['INVOICE_ID']}</a>") ?>-->
	<?= html::horiz_table_tr_form('Trx Type', form::dropdown('transaction_code', Model::instance('Transaction_codes')->get_dropdown(), $row['TRANSACTION_CODE'], 'class="validate[required]"'), TRUE) ?>
	<?= html::horiz_table_tr_form('Trx/Payment Date', form::input('transaction_date', $row['TRANSACTION_DATE_FMT'], 'class="datepicker validate[required,custom[date2]]"') . 'mm/dd/yyyy', TRUE) ?>
	<?= html::horiz_table_tr_form('FY', form::dropdown('fiscal_year', Model::instance('Fiscal_years')->get_dropdown(), $row['FISCAL_YEAR'], 'class="validate[required]"'), TRUE) ?>
	<?= html::horiz_table_tr_form('Payment Type', form::dropdown('payment_type_code', Model::instance('Ref_transaction_payment_types')->get_dropdown(), $row['PAYMENT_TYPE_CODE'], 'class="validate[required]"'), TRUE) ?>
	<?= html::horiz_table_tr_form('Amount', form::input('amount', $row['AMOUNT'], 'class="validate[required,custom[currency]]"'), TRUE) ?>
	<?= html::horiz_table_tr_form('Check Number', form::input('check_number', $row['CHECK_NUMBER'])) ?>
	<?= html::horiz_table_tr_form('Name on Check', form::input('name_on_check', $row['NAME_ON_CHECK'], 'size=45 maxlength=50')) ?>
	<?= html::horiz_table_tr_form('Is Operator Payment?', form::checkbox('operator_payment', 'Y', ($row['OPERATOR_PAYMENT'] == 'Y'))) ?>
	<?= html::horiz_table_tr_form('Operator ID', form::input('operator_id', $row['OPERATOR_ID'], 'class="ui-autocomplete"')) ?>
	<?= html::horiz_table_tr_form('Deposit Date', form::input('deposit_date', $row['DEPOSIT_DATE_FMT'], 'class="datepicker validate[custom[date2]]"') . 'mm/dd/yyyy') ?>
	<?= html::horiz_table_tr_form('Comments', form::textarea(array('name'=>'comments', 'rows'=>3, 'cols'=>45, 'maxlength'=>240), $row['COMMENTS']) . 'max 240 chars') ?>
</table>
<?= form::submit('submit', 'Submit', 'class="ui-button ui-state-default ui-corner-all"') ?>
</fieldset>
</form>
<script>
	$(function() {
		$( "#operator_id" ).autocomplete({
			source: "<?= url::site() ?>index.php?operator/autocomplete&",
			minLength: 3
		});
	});
</script>
