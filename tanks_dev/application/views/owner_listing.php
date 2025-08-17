<?php
/**
 * Users menu
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

<h1>Owners Search Results</h1>

<table id="owners_tabular" class="display">
	<thead>
		<tr>
			<th>ID</th><th>NAME</th><th>MAILING ADDRESS 1</th><th>CITY</th><th>ZIP</th><th>ACTION</th>
		</tr>
	</thead>
	<tbody>
		<?= array_reduce($rows, 'display_owner_row'); ?>
	</tbody>
</table>

<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		owners_tabular_obj = $('#owners_tabular').dataTable({
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"aoColumnDefs": [
				{ "aTargets":[5], "bSortable":false, "bSearchable":false }
			]
		});

		//$('#owners_tabular tbody tr').live('click', function () {
		//	var row_id = owners_tabular_obj.fnGetPosition(this);
		//	$(location).attr('href', owners_tabular_obj.fnGetData(row_id)[url_col]);
		//});
	});
</script>

<?php
function display_owner_row($result, $row) {
	if ($result == NULL) $result = '';

	$view_button =  Controller::_instance('Owner')->_view_button($row['ID']);
	$result .= "<tr>
		<td>{$row['ID']}</td>
		<td>{$row['OWNER_NAME']}</td>
		<td>{$row['ADDRESS1']}</td>
		<td>{$row['CITY']}</td>
		<td>{$row['ZIP']}</td>
		<td>{$view_button}</td>
		</tr>";
	return($result);
}
?>
