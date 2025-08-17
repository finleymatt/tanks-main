<?php
/**
 * Owner Tank Fee Transaction History report
 * This report migrated from legacy Onestop with same name from file ust_owner_fee_history.rdf
 *
 * @package Onestop
 * @subpackage views
 * @uses Report.php
 *
*/

$db = Database::instance();

if ($fy) {
	$fy_where = 'AND T.fiscal_year = :fy';
	$bound_vars = array(':fy' => $fy, ':owner_id' => $owner_id);
}
else {
	$fy_where = '';
	$bound_vars = array(':owner_id' => $owner_id);
}


$report_sql = "
SELECT T.owner_id, T.fiscal_year, T.id transaction_id,
	T.transaction_date, T.transaction_code, 
	DECODE(T.transaction_code, 
	'PP', T.amount*-1, 'LP', T.amount*-1, 
	'WP', T.amount*-1, 'IP', T.amount*-1, 
	'PW', T.amount*-1, 'LW', T.amount*-1, 
	'IW', T.amount*-1, T.amount) actual_amount, 
	T.invoice_id, T.user_created, 
	T.date_created, T.name_on_check, 
	T.check_number
FROM ustx.transactions T, ustx.owners_mvw O
WHERE T.owner_id = O.id
	AND (T.owner_id = :owner_id
		AND instr(T.transaction_code, 'H') = 0
		AND T.fiscal_year > 1978
		{$fy_where}
		AND instr(T.transaction_code, 'G') = 0
	)
ORDER BY T.fiscal_year desc, T.transaction_date DESC";

$rs_arr = $db->query($report_sql, $bound_vars)->as_array();

$owner_row = Model::instance('Owners_mvw')->get_row($owner_id);
$report = new Report($output_format, 'Owner Tank Fee Transaction History', "Owner: ({$owner_id}) {$owner_row['OWNER_NAME']}");
$report->getActiveSheet()->getPageSetup()->setFitToWidth(1);
$report->getActiveSheet()->getPageSetup()->setFitToHeight(0);

if (count($rs_arr)) {
	$report->setGroup($rs_arr, array(
		array('name' => 'OWNER_ID',
			'footer_func' => 'owner_footer'),
		array('name' => 'FISCAL_YEAR',
			'header_func' => 'fiscal_year_header',
			'footer_func' => 'fiscal_year_footer'),
		array('name' => 'TRANSACTION_ID',
			'row_func' => 'transaction_row')
	));
}

$report->setColumnSize(array(15, 10, 17, 13, 18, 18, 43, 18));
$flag = $report->output('owner_tank_fee_history');


function owner_footer(&$report, $row, $params) {
	$report->setRow( array(
		array(
			'value' => 'Owner Total:',
			'colspan' => 2
		),
		2 => array(
			'value' => "=SUMIF(A{$params['group_row_start']}:A{$params['group_row_end']}, \"=FY Balance:\", C{$params['group_row_start']}:C{$params['group_row_end']})",
			'style' => Report::$STYLE_MONEY
		)
	), FALSE, Report::$STYLE_TOTAL);
}

function fiscal_year_header(&$report, $row) {
	$report->setLabelRow(array( array('colspan' => 8, 'value' => "FY: {$row['FISCAL_YEAR']}") ), Report::$STYLE_LABEL_2);
	$report->setLabelRow(array('Trx Date', 'Trx Code', 'Amount', 'Invoice ID', 'User Created', 'Date Created', 'Name on Check', 'Check Number'));
}

function fiscal_year_footer(&$report, $row, $params) {
	$report->setRow( array(
		array(
			'value' => 'FY Balance:',
			'colspan' => 2
		),
		2 => array(
			'value' => "=SUM(C{$params['group_row_start']}:C{$params['group_row_end']})",
			'style' => Report::$STYLE_MONEY
		)
	), FALSE, Report::$STYLE_TOTAL);

	$report->setBlankRow();
}

function transaction_row(&$report, $row) {
	$report->setRow(array(
		array('value' => Report::TO_DATE($row['TRANSACTION_DATE']), 'style' => Report::$STYLE_DATE),
		array('value' => $row['TRANSACTION_CODE'], 'style' => Report::$STYLE_RIGHT),
		array('value' => $row['ACTUAL_AMOUNT'], 'style' => Report::$STYLE_MONEY),
		array('value' => $row['INVOICE_ID']),
		array('value' => $row['USER_CREATED'], 'style' => Report::$STYLE_CENTER),
		array('value' => Report::TO_DATE($row['DATE_CREATED']), 'style' => array_merge(Report::$STYLE_DATE, Report::$STYLE_CENTER)),
		array('value' => $row['NAME_ON_CHECK']),
		array('value' => $row['CHECK_NUMBER'])
	), FALSE);
}
?>
