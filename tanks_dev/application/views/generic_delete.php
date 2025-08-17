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
?>
<h1>Delete <?= $object_name ?> Confirmation</h1>

<div class='ui-state-highlight ui-corner-all' style='padding:0.7em;'><span class='ui-icon ui-icon-notice' style='float:left; margin-right:0.3em;'></span> You are about to delete following record from the Onestop database. To delete, please confirm.</div>

<?php
if (count($rows) > 1)
	echo("<div class='ui-state-error ui-corner-all' style='padding:0.7em;'><span class='ui-icon ui-icon-alert' style='float:left; margin-right:0.3em;'></span> This delete operation will result in deletion of more than one record in the Database! This sometimes happens for certain record types due to the limitations of the original Onestop DB design.  Please review carefully before you confirm deletion. For further info, please contact Onestop developer.</div>");

if (isset($note))  // object specific notes
	echo("<div class='ui-state-highlight ui-corner-all' style='padding:0.7em; margin-top:20px'><span class='ui-icon ui-icon-notice' style='float:left; margin-right:0.3em;'></span> {$note}</div>");

foreach($rows as $row) {
?>
<table class="horiz_table ui-widget ui-corner-all" style="margin:20px 0px 20px 0px">
	<caption><div class="left_float"><?= $object_name ?></div></caption>
	<?php
	foreach ($row as $col_name => $col_val) {
		echo(html::horiz_table_tr($col_name, $col_val));
	}
	?>
</table>
<?php
}

$cancel_url = (isset($cancel_url) ? $cancel_url : NULL);
echo(form::cancel_button($cancel_url));
echo(form::confirm_button($delete_url, 'Confirm Delete'));
?>

