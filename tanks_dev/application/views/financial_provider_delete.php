<?php
/**
 * Financial Provider delete form
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
//var_dump($financial_provider_rows);exit;
?>
<h1>Financial Provider Delete</h1>

<div style='float:left;'>
<form action="<?= $action ?>" method="post" class="validate_form edit_form">
<fieldset class='ui-widget ui-widget-content ui-corner-all' style="width:630px">
<legend class='ui-widget ui-widget-header ui-corner-all'>Financial Providers</legend>
<table class="horiz_table ui-widget ui-corner-all" style="width:625px">

<?php 
$html = '';
foreach ($financial_provider_rows as $financial_provider_row) {
	$html .= form::checkbox(array('name' => "financial_provider[]", 'id' => "financial_provider_{$financial_provider_row['CODE']}", 'class' =>'validate[required]'), html::h($financial_provider_row['CODE']));
	$html .= html::h($financial_provider_row['DESCRIPTION'] . ' ') . "<br />\n";
}
echo html::horiz_table_tr_form('Financial Providers', $html, TRUE);
?>

</table>
<?= form::submit('submit', 'Submit', 'class="ui-button ui-state-default ui-corner-all"') ?>
</fieldset>
</form>
</div>
