<?php
/**
 * Test Result Tester Delete Form
 *
 * @package ### file docblock
 * @subpackage views
 * @uses ###
 * @see ###*
*/

?>
<h1>Test Result Tester Delete</h1>

<div style='float:left;'>
<form action="<?= $action ?>" method="post" class="validate_form edit_form">
<fieldset class='ui-widget ui-widget-content ui-corner-all' style="width:630px">
<legend class='ui-widget ui-widget-header ui-corner-all'>Test Result Tester</legend>
<table class="horiz_table ui-widget ui-corner-all" style="width:625px">
<?php
$html = '';
foreach(Model::instance('Ref_test_results_testers')->get_tester_list() as $id => $name) {
	$html .= form::checkbox(array('name' => "test_result_tester[]", 'id' => "tester_{$id}", 'class' =>'validate[required]'), html::h($id));
	$html .= html::h($name . ' ') . "<br />\n";
}
echo html::horiz_table_tr_form('Test Result Testers', $html, TRUE); 
?>
<?php Model::instance('Ref_test_results_testers')->get_tester_list() ?>
</table>
<?= form::submit('submit', 'Submit', 'class="ui-button ui-state-default ui-corner-all"') ?>
</fieldset>
</form>
</div>

<div style='float:left; margin-left:190px'>
Please check all the tester you want to delete and submit the form.
</div>
