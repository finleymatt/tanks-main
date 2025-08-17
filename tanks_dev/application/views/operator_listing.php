<?php
/**
 * Operating listing page -- search results
 *
 * @package ### file docblock
 * @subpackage views
 * @uses ###
 * @see ###
 *
*/
?>

<h1>Operator Search Results</h1>

<table id="operator_tabular" class="display">
	<thead>
		<tr>
			<th>ID</th><th>NAME</th><th>MAILING ADDRESS 1</th><th>CITY</th><th>ZIP</th><th>ACTION</th>
		</tr>
	</thead>
	<tbody>
		<?= array_reduce($rows, 'display_operator_row'); ?>
	</tbody>
</table>

<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		operator_tabular_obj = $('#operator_tabular').dataTable({
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"aoColumnDefs": [
				{ "aTargets":[5], "bSortable":false, "bSearchable":false }
			]
		});
	});
</script>

<?php
function display_operator_row($result, $row) {
	$view_button =  Controller::_instance('Operator')->_view_button($row['ID']);
	$result .= "<tr>
		<td>{$row['ID']}</td>
		<td>{$row['OPERATOR_NAME']}</td>
		<td>{$row['ADDRESS1']}</td>
		<td>{$row['CITY']}</td>
		<td>{$row['ZIP']}</td>
		<td>{$view_button}</td>
		</tr>";
	return($result);
}
?>
