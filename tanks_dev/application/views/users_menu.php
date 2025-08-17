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

<h1>Users</h1>

<table id="users_tabular" class="display ui-widget ui-widget-content ui-corner-all">
	<thead class="ui-widget-header">
		<tr>
			<th>CODE</th><th>LAST NAME</th><th>FIRST NAME</th><th>SEP LOGIN ID</th><th>STAFF TYPE</th><th>IS ACTIVE?</th>
		</tr>
	</thead>
	<tbody class="ui-widget-content">
		<?= array_reduce($rows, 'display_staff_row'); ?>
	</tbody>
</table>

<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		users_tabular_obj = $('#users_tabular').dataTable({
			"bJQueryUI": true,
			"sPaginationType": "full_numbers"
		});
	} );
</script>

<?php
function display_staff_row($result, $row) {
	if ($result == NULL) $result = '';

	$staff_type = Model::instance('Cg_ref_codes')->get_row(array('STAFF_TYPE', $row['STAFF_TYPE']));
	//$staff_type = Model::instance('Cg_Ref_Codes')->get_lookup_desc('STAFF_TYPE', $row['STAFF_TYPE']);

	$result .= "<tr>
		<td>{$row['CODE']}</td>
		<td>{$row['LAST_NAME']}</td>
		<td>{$row['FIRST_NAME']}</td>
		<td>{$row['SEP_LOGIN_ID']}</td>
		<td>{$staff_type['RV_MEANING']}</td>
		<td>{$row['RESTRICTED']}</td>
		</tr>";
	return($result);
}
?>
