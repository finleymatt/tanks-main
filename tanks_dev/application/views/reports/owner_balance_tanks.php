<?php
/**
 * Owner Balance and Tanks Report
 * Migrated from orignal Oracle report, ust_owners_balance_tanks.rdf.
 * Original version had 2 more columns: 'abandoned' and 'permanently out'
 *
 * @package Onestop
 * @subpackage views
 * @uses Report.php
 *
*/

$db = Database::instance();

if (!$tank_status_codes || !$balance_type)
	exit('Required fields not entered.');

// get desc of each code and check valid code
$tank_status_desc = array();
foreach($tank_status_codes as $code) {
	if (! is_numeric($code))
		exit('Invalid tank status code submitted.');
	$tank_status_desc[] = Model::instance('Tank_status_codes')->get_lookup_desc($code);
}

$tank_status_codes_str = implode(',', $tank_status_codes);
$tank_status_desc_str = implode(',', $tank_status_desc);

$report_sql = "
-- Report tank counts by tank status and tank fee balance by owner
SELECT owner_bal.owner_name, owner_bal.balance, owner_tanks.*
FROM
	-- Get the tank fee balance due for each owner
	(select t.owner_id,
		(select max(ow.owner_name) from ustx.owners_mvw ow where ow.id = t.owner_id) owner_name,
		sum(DECODE(t.transaction_code, 
			'PP', -t.amount, 
			'LP', -t.amount, 
			'WP', -t.amount,
			'IP', -t.amount, 
			'PW', -t.amount,
			'LW', -t.amount, 
			'IW', -t.amount, 
			t.amount)) balance
	from ustx.transactions t
	where t.transaction_code not in ('GWAA','GWAP','HWEA','HWEP','HW','ICP','ICA','SCP','SCA')
		and t.fiscal_year > 1978
	group by t.owner_id) owner_bal,
	
	-- Get the tank counts by status for each owner
	(select t.owner_id,
		sum(decode(t.tank_status_code,1,1,0)) in_use,
		sum(decode(t.tank_status_code,2,1,0)) temp_out,
		sum(decode(t.tank_status_code,4,1,0)) sold,
		sum(decode(t.tank_status_code,5,1,0)) removed,
		sum(decode(t.tank_status_code,11,1,0)) no_data,
		sum(decode(t.tank_status_code,12,1,0)) exempt
	from ustx.tanks t
	group by t.owner_id) owner_tanks,
	
	-- Get the tank counts with the selected status codes for each owner
	(select t.owner_id, count(*)selected_tanks
	from ustx.tanks t
	where (t.tank_status_code in ({$tank_status_codes_str}))
	group by t.owner_id
	having count(*) > 0) tanks
	
WHERE owner_bal.owner_id = owner_tanks.owner_id
	AND tanks.owner_id = owner_bal.owner_id
	AND tanks.selected_tanks > 0
	AND sign(owner_bal.balance) = decode(:balance_type,
		'all', sign(owner_bal.balance),
		'credit', -1,
		'debit', 1, 
		sign(owner_bal.balance))
	AND sign(owner_bal.balance) <> decode(:balance_type, 'non-zero', 0, 2)
ORDER BY owner_bal.owner_name";

$rs_arr = $db->query($report_sql, array(':balance_type' => $balance_type))->as_array();

$report = new Report($output_format, 'Owner Balance and Tanks', "Tank Status selected: {$tank_status_desc_str} \nBalance Type: {$balance_type}");

$report->setRow( array(
	 array('value' => "Tank Fees and Associated Charges Only\nFY77 and FY78 Fees Not Included", 'colspan' => 5, 'style' => Report::$STYLE_NOTE)
), FALSE);

$report->setLabelRow( array('Owner ID', 'Owner Name', 'Balance', 'In Use', 'Temp Out', 'Sold', 'Removed', 'No Data', 'Exempt'), array('style' => array('alignment' => array('wrap' => TRUE))) );
$report->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($report->row_num-1, $report->row_num-1);

// main body --------------------------------------------
if (count($rs_arr)) {
	$group_row_start = $report->row_num;

	foreach ($rs_arr as $row) {
		$report->setRow(array(
			$row['OWNER_ID'],
			$row['OWNER_NAME'],
			array('value' => $row['BALANCE'], 'style' => Report::$STYLE_MONEY),
			$row['IN_USE'],
			$row['TEMP_OUT'],
			$row['SOLD'],
			$row['REMOVED'],
			$row['NO_DATA'],
			$row['EXEMPT'],
		));
	}

	// totals summary row ---------------------------------------------
	$group_row_end = $report->row_num - 1;
	$report->setRow( array(
		array('colspan' => 2, 'value' => 'Totals:'),
		2 => array('value' => "=SUM(C{$group_row_start}:C{$group_row_end})", 'style' => Report::$STYLE_MONEY),
		"=SUM(D{$group_row_start}:D{$group_row_end})",
		"=SUM(E{$group_row_start}:E{$group_row_end})",
		"=SUM(F{$group_row_start}:F{$group_row_end})",
		"=SUM(G{$group_row_start}:G{$group_row_end})",
		"=SUM(H{$group_row_start}:H{$group_row_end})",
		"=SUM(I{$group_row_start}:I{$group_row_end})"
	), FALSE, Report::$STYLE_TOTAL);
}

$report->setColumnSize(array(10, 35, 15, 10, 10, 10, 10, 10, 10));
$flag = $report->output('owner_balance_tanks');

// local function =========================================================
// returns TRUE only if $pre_total has a nonzero value and none of the pre values match it
function has_alert($pre_total, $permit_tank, $past_2, $past_1) {
	if (!is_numeric($pre_total) || empty($pre_total))
		return(FALSE);
	return(($pre_total != $permit_tank) && ($pre_total != $past_2) && ($pre_total != $past_1));
}
?>
