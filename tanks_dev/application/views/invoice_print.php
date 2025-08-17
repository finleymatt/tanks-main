<?php
/**
 * Invoice Print
 *
 * @package Onestop
 * @subpackage views
 * @uses Report.php
 *
*/

//$db = Database::instance();

$invoice_detail = new Invoice_detail_Model();
$invoice_detail_rows = $invoice_detail->get_list('INVOICE_ID = :INVOICE_ID', 'FISCAL_YEAR', array(':INVOICE_ID' => $invoice_id));


$report = new Report($output_format, 'Invoice', 'Owner: FY: ');

if (count($invoice_detail_rows)) {
	$report->setGroup($invoice_detail_rows, array(
		//array('name' => 'MAIN',
		//	'footer_func' => 'main_footer'),
		array('name' => 'FISCAL_YEAR',
			'header_func' => 'fy_header')
			//'footer_func' => 'fy_footer'),
		//array('name' => 'TANK_ID',
		//	'row_func' => 'tank_row')
	));
}

$report->setColumnSize(array(8, 35, 8, 35, 25, 25, 18, 7, 8, 8, 8, 12));
$flag = $report->output('invoice');


function main_footer(&$report, $row, $params) {
	$grand_total_start = $report->row_num;
	$report->setRow(array(
		array(
			'value' => "AST Total:",
			'colspan' => 11,
			'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT))
		),
		11 => "=SUMIF(A{$params['group_row_start']}:A{$params['group_row_end']}, \"=AST Count:\", H{$params['group_row_start']}:H{$params['group_row_end']})"
	), FALSE, Report::$STYLE_TOTAL);

	$report->setRow(array(
		array(
			'value' => "UST Total:",
			'colspan' => 11,
			'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT))
		),
		11 => "=SUMIF(I{$params['group_row_start']}:I{$params['group_row_end']}, \"=UST Count:\", L{$params['group_row_start']}:L{$params['group_row_end']})"
	), FALSE, Report::$STYLE_TOTAL);
	$grand_total_end = $report->row_num - 1;

	$report->setRow(array(
		array(
			'value' => "Grand Total:",
			'colspan' => 11,
			'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT))
		),
		11 => array(
			'value' => "=SUM(L{$grand_total_start}:L{$grand_total_end})"
		)
	), FALSE, Report::$STYLE_TOTAL);
}

function fy_header(&$report, $row) {
	$report->setRow(array(
		$row['FISCAL_YEAR'],
		$row['INVOICE_ID']
	), FALSE);
}

function fy_footer(&$report, $row, $params) {
	$group_row_start = $params['group_row_start'] + 1;
	$report->setRow(array(
		array(
			'value' => "AST Count:",
			'colspan' => 7,
			'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT))
		),
		7 => "=COUNTIF(K{$group_row_start}:K{$params['group_row_end']}, \"A\")",
		array(
			'value' => "UST Count:",
			'colspan' => 3,
			'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT))
		),
		11 => "=COUNTIF(K{$group_row_start}:K{$params['group_row_end']}, \"U\")"
	), FALSE, Report::$STYLE_TOTAL);

	$report->setBlankRow();
}

function facility_header(&$report, $row) {
	$report->setRow(array(
		2 => $row['FACILITY_ID'],
		$row['FACILITY_NAME'],
		$row['ADDRESS1'],
		$row['ADDRESS2'],
		array('value' => $row['CITY'], 'style' => Report::$STYLE_TEXT),
		$row['STATE'],
		$row['ZIP']
	), FALSE);
}

function tank_row(&$report, $row) {
	$report->setRow(array(
		9 => $row['TANK_ID'],
		array('value' => $row['TANK_TYPE'], 'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER))),
		($row['TANK_DETAIL_CODE'] ? $row['TANK_DETAIL_CODE'] : 'not specified')
	), FALSE);
}

?>
