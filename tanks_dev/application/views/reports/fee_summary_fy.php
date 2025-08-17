<?php
/**
 * Fee Summary Report
 * imported from Oracle Report, "Daily/Monthly Fee Summary (ust_fee_summary_fy.rdf)"
 *
 * @package Onestop
 * @subpackage views
 * @uses Report.php
 *
*/

$db = Database::instance();

$ust_code_where = ($transaction_code == 'UST' ? "or T.transaction_code in ('PP', 'IP', 'LP')" : '');

$report_sql = "-- retrieves records per transaction
SELECT 'MAIN' MAIN, T.owner_id, O.owner_name, T.invoice_id, 
	T.fiscal_year, T.transaction_code, T.operator_payment, T.comments,
	T.check_number, T.name_on_check, T.amount,
	T.transaction_date, T.deposit_date, T.id transaction_id, T.payment_type_code
FROM ustx.transactions T, ustx.owners_mvw O
WHERE T.owner_id = O.id
	and (T.transaction_code = :transaction_code
		{$ust_code_where}
	)
	and :transaction_code not in ('ICP','SCP')
	and T.transaction_date >= to_date(:start_date, 'mm/dd/yyyy')
	and T.transaction_date <= to_date(:end_date, 'mm/dd/yyyy')
ORDER BY T.fiscal_year, T.transaction_date";

$rs_arr = $db->query($report_sql, array(':transaction_code' => $transaction_code, ':start_date' => $start_date, ':end_date' => $end_date))->as_array();

$report = new Report($output_format, 'Fee Summary Report by FY', "Transaction dates between: {$start_date} to {$end_date}\nTransaction: {$transaction_code}");

if (count($rs_arr)) {
	$report->setGroup($rs_arr, array(
		array('name' => 'MAIN',
			'footer_func' => 'main_footer'),
		array('name' => 'FISCAL_YEAR',
			'header_func' => 'fy_header',
			'footer_func' => 'fy_footer'),
		array('name' => 'TRANSACTION_ID',
			'row_func' => 'transaction_row')
	));
}

$report->setColumnSize(array(6, 35, 8, 8, 10, 10, 15, 38, 6, 15, 23, 15));
$flag = $report->output('fee_summary_fy');


function main_footer(&$report, $row, $params) {
	$report->setRow(array(
		array(
			'value' => "Grand Total:",
			'colspan' => 11,
			'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT))
		),
		11 => array(
			'value' => "=SUMIF(A{$params['group_row_start']}:A{$params['group_row_end']}, \"=FY Total:\", L{$params['group_row_start']}:L{$params['group_row_end']})",
			'style' => Report::$STYLE_MONEY
		)
	), FALSE, Report::$STYLE_TOTAL);
}

function fy_header(&$report, $row) {
	$report->setLabelRow( array(array('colspan' => 12, 'value' => "FY {$row['FISCAL_YEAR']}")), Report::$STYLE_LABEL_2 );
	$report->setLabelRow( array('O ID', 'Owner Name', 'TRX Code', 'Invoice ID', 'Pay Date', 'Dep Date', 'Check#', 'Name on Check', 'Operator?', 'Payment Type', 'Comments', 'Amount'), array('style' => array('alignment' => array('wrap' => TRUE))) );
	$report->getActiveSheet()->getRowDimension($report->row_num - 1)->setRowHeight(25);
}

function fy_footer(&$report, $row, $params) {
	$report->setRow(array(
		array(
			'value' => 'FY Total:',
			'colspan' => 11,
			'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT))
		),
		11 => array(
			'value' => "=SUM(L{$params['group_row_start']}:L{$params['group_row_end']})",
			'style' => Report::$STYLE_MONEY
		)
	), FALSE, Report::$STYLE_TOTAL);
	$report->setBlankRow();
}

function transaction_row(&$report, $row) {
	$report->setRow(array(
		$row['OWNER_ID'],
		$row['OWNER_NAME'],
		$row['TRANSACTION_CODE'],
		$row['INVOICE_ID'],
		array('value' => Report::TO_DATE($row['TRANSACTION_DATE']), 'style' => Report::$STYLE_DATE),
		array('value' => Report::TO_DATE($row['DEPOSIT_DATE']), 'style' => Report::$STYLE_DATE),
		$row['CHECK_NUMBER'],
		array('value' => $row['NAME_ON_CHECK'], 'style' => array('alignment' => array(
			'wrap' => TRUE))),
		$row['OPERATOR_PAYMENT'],
		$row['PAYMENT_TYPE_CODE'],
		array('value' => $row['COMMENTS']),
		array('value' => $row['AMOUNT'], 'style' => Report::$STYLE_MONEY)
	));
}

?>
