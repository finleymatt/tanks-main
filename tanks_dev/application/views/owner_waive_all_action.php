<?php
/**
 * Owner Waive All Fees - Display results
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
<h1>Results Report for Waive All Fees for <?= $owner['OWNER_NAME'] ?></h1>

<table class="horiz_table ui-widget ui-corner-all" style="margin-bottom:50px">
	<caption>Waivers created</caption>
	<tbody>
	<tr><th>FY</th><th>Waiver Type</th><th>Waiver Amount</th></tr>
	<?php foreach($waivers as $waiver)
		echo("<tr>
			<td>{$waiver['FISCAL_YEAR']}</td>
			<td>{$waiver['WAIVER_CODE']}</td>
			<td>{$waiver['AMOUNT']}</td></tr>");
	?>
	</tbody>
</table>

<?= $this->_view_button($owner_id, TRUE, 'Go Back to Owner View') ?>

