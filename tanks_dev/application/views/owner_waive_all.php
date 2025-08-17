<?php
/**
 * Owner Waive All Fees
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
?>
<h1>Waive All Fees for <?= $owner['OWNER_NAME'] ?></h1>

<?php
	$this->_priv_message($this->_view_url($owner_id), 'USTX.TRANSACTIONS', 'INSERT');
?>

<div class='ui-state-highlight ui-corner-all' style='padding:0.7em; margin-top:20px'><span class='ui-icon ui-icon-notice' style='float:left; margin-right:0.3em;'></span>Are you sure you want to waive all fees for this owner?</div>

<p style="padding:10px;">
This action will create waivers for all outstanding fees, and then generate invoices for those fiscal years to recalculate.<br />
Records that will be created are Principal Tank Fee Waiver(s), Invoice(s), and Transactions.
</p>

<form action="<?= $action ?>" method="post" class="validate_form edit_form">
<fieldset class='ui-widget ui-widget-content ui-corner-all'>
<legend class='ui-widget ui-widget-header ui-corner-all'>Waive All Fees</legend>
<table class="horiz_table ui-widget ui-corner-all">
	<?= html::horiz_table_tr_form('Reason for waiving all fees',
		form::textarea(array('name'=>'reason', 'rows'=>4, 'cols'=>45, 'maxlength'=>2000), NULL, 'class="validate[required]"') . 'max 2000 chars') ?>
</table>

<?= form::cancel_button() ?>
<?= form::submit('submit', 'Confirm Waive All', 'class="ui-button ui-state-default ui-corner-all"') ?>
</fieldset>
</form>

