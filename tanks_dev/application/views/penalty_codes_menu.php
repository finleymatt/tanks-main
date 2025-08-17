<?php
/**
 * Penalty Codes menu
 *
 * <p>long description</p>
 *
 * @package ### file docblock
 * @subpackage views
 * @uses ###
 * @see ###
 *
*/
?>

<h1>Penalty Codes</h1>

<table id="users_tabular" class="display ui-widget ui-widget-content ui-corner-all">
	<thead class="ui-widget-header">
		<tr>
			<th>CODE</th><th>DESCRIPTION</th><th>DP CATEGORY</th><th>SOC CATEGORY</th><th>PENALTY LEVEL</th><th>IS SOC?</th><th>TANK TYPE</th><th>END DATE</th>
		</tr>
	</thead>
	<tbody class="ui-widget-content">
		<?= array_reduce($rows, 'display_code_row'); ?>
	</tbody>
</table>

<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		codes_tabular_obj = $('#users_tabular').dataTable({
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"aaSorting": [[ 7, "asc" ]]
		});
	} );
</script>

<?php
function display_code_row($result, $row) {
	if ($result == NULL) $result = '';

	$result .= "<tr>
		<td>{$row['CODE']}</td>
		<td>{$row['DESCRIPTION']}</td>
		<td>{$row['DP_CATEGORY']}</td>
		<td>{$row['SOC_CATEGORY']}</td>
		<td>{$row['PENALTY_LEVEL']}</td>
		<td>{$row['IS_SOC']}</td>
		<td>{$row['TANK_TYPE']}</td>
		<td>{$row['END_DATE']}</td>
		</tr>";
	return($result);
}
?>
