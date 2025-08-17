<?php
/**
 * Accounts Aging report
 * imported from Oracle Report, "Accounts Aging"
 *
 * @package Onestop
 * @subpackage views
 * @uses Report.php
 *
*/

$db = Database::instance();

if (!$days)
	exit('Required fields not entered.');

$report_sql = "SELECT 'MAIN' MAIN, O.id owner_id, O.owner_name, LAST_PAY.last_pay_date, I.id invoice_id, I.invoice_date, INV_D.fiscal_year,
	(INV_D.tank_fee_balance + INV_D.late_fee_balance + INV_D.interest_balance) owe,
	INV_D.tank_fee_balance, INV_D.late_fee_balance, INV_D.interest_balance
FROM ustx.owners_mvw O,
	(select t.owner_id, max(t.transaction_date) last_pay_date
		from ustx.transactions t
		where t.transaction_code in ('PP','LP','IP')
		group by t.owner_id) LAST_PAY,
	(select inv.owner_id, max(inv.invoice_date) last_invoice_date
		from ustx.invoices inv
		group by inv.owner_id) LAST_INV,
	ustx.invoices I, ustx.invoice_detail INV_D
WHERE O.id = LAST_PAY.owner_id
	and O.id = LAST_INV.owner_id
	and LAST_INV.last_invoice_date = I.invoice_date
	and O.id = I.owner_id
	and I.id = INV_D.invoice_id
	and ((INV_D.tank_fee_balance + INV_D.late_fee_balance + INV_D.interest_balance) > 0)
	and (sysdate - last_pay_date) > :aging_days
ORDER BY O.id, I.id, INV_D.fiscal_year";

$rs_arr = $db->query($report_sql, array(':aging_days' => $days))->as_array();

$report = new Report($output_format, 'Accounts Aging Report', "Tank Registration Fees Unpaid for {$days} Days");

if (count($rs_arr)) {
	$report->setGroup($rs_arr, array(
		array('name' => 'MAIN',
			'footer_func' => 'main_footer'),
		array('name' => 'OWNER_ID',
			'header_func' => 'owner_header',
			'footer_func' => 'owner_footer'),
		array('name' => 'INVOICE_ID',
			'header_func' => 'invoice_header',
			'footer_func' => 'invoice_footer'),
		array('name' => 'FISCAL_YEAR',
			'row_func' => 'fiscal_year_row')
	));
}

$report->setColumnSize(array(15, 13, 15, 10, 15, 15, 15, 15));
$flag = $report->output('accounts_aging');


function main_footer(&$report, $row, $params) {
	$report->setRow(array(
		array(
			'value' => "Grand Totals:",
			'colspan' => 4,
			'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT))
		),
		array(
			'value' => "=SUMIF(A{$params['group_row_start']}:A{$params['group_row_end']}, \"=Invoice Totals:\", E{$params['group_row_start']}:E{$params['group_row_end']})",
			'style' => Report::$STYLE_MONEY
		),
		array(
			'value' => "=SUMIF(A{$params['group_row_start']}:A{$params['group_row_end']}, \"=Invoice Totals:\", F{$params['group_row_start']}:F{$params['group_row_end']})",
			'style' => Report::$STYLE_MONEY
		),
		array(
			'value' => "=SUMIF(A{$params['group_row_start']}:A{$params['group_row_end']}, \"=Invoice Totals:\", G{$params['group_row_start']}:G{$params['group_row_end']})",
			'style' => Report::$STYLE_MONEY
		),
		array(
			'value' => "=SUMIF(A{$params['group_row_start']}:A{$params['group_row_end']}, \"=Invoice Totals:\", H{$params['group_row_start']}:H{$params['group_row_end']})",
			'style' => Report::$STYLE_MONEY
		)
	), FALSE, Report::$STYLE_TOTAL);
}

function owner_header(&$report, $row) {
	$report->setLabelRow(array( array('colspan'=>8, 'value'=>"({$row['OWNER_ID']}) {$row['OWNER_NAME']}") ));
	$report->setLabelRow(array('Date Last Pd', 'Invoice ID', 'Invoice Date', 'FY', 'Owes', 'Principal', 'Late Fee', 'Interest'), array('style' => array('alignment' => array('wrap' => TRUE))));
}

function owner_footer(&$report, $row, $params) {
	$report->setBlankRow(); $report->setBlankRow();
}

function invoice_header(&$report, $row) {
	$report->setRow(array(
		array('value' => Report::TO_DATE($row['LAST_PAY_DATE']), 'style' => Report::$STYLE_DATE),
		array('value' => $row['INVOICE_ID']),
		array('value' => Report::TO_DATE($row['INVOICE_DATE']), 'style' => Report::$STYLE_DATE)
	), FALSE);
}

function invoice_footer(&$report, $row, $params) {
	$report->setRow(array(
		array(
			'value' => "Invoice Totals:",
			'colspan' => 4,
			'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT))
		),
		array(
			'value' => "=SUM(E{$params['group_row_start']}:E{$params['group_row_end']})",
			'style' => Report::$STYLE_MONEY
		),
		array(
			'value' => "=SUM(F{$params['group_row_start']}:F{$params['group_row_end']})",
			'style' => Report::$STYLE_MONEY
		),
		array(
			'value' => "=SUM(G{$params['group_row_start']}:G{$params['group_row_end']})",
			'style' => Report::$STYLE_MONEY
		),
		array(
			'value' => "=SUM(H{$params['group_row_start']}:H{$params['group_row_end']})",
			'style' => Report::$STYLE_MONEY
		)
	), FALSE, array_merge(Report::$STYLE_TOTAL,
		array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array('argb' => 'FFCCCCCC')
		))));
}

function fiscal_year_row(&$report, $row) {
	$report->setRow(array(
		3 => $row['FISCAL_YEAR'],
		array('value' => $row['OWE'], 'style' => Report::$STYLE_MONEY),
		array('value' => $row['TANK_FEE_BALANCE'], 'style' => Report::$STYLE_MONEY),
		array('value' => $row['LATE_FEE_BALANCE'], 'style' => Report::$STYLE_MONEY),
		array('value' => $row['INTEREST_BALANCE'], 'style' => Report::$STYLE_MONEY)
	), FALSE);
}

?>
