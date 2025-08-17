<?php
/**
 * Tank Fee Billing Exceptions Report
 * Migrated from orignal Oracle report, ust_billing_exceptions.rdf.
 *
 * @package Onestop
 * @subpackage views
 * @uses Report.php
 *
*/

$db = Database::instance();
$GLOBALS['REPORT_HEADER_STYLE'] = array('font' => array('bold' => true, 'size' => 12),
	'fill' => array(
		'type' => PHPExcel_Style_Fill::FILL_SOLID,
		'color' => array('argb' => 'FFD07070')));

if (!$invoice_date)
	exit('Required fields not entered.');

$midyear_sql = "
-- Facilities with Tanks Installed Midyear
SELECT DISTINCT 'MAIN' MAIN_GROUP, O.id owner_id, O.owner_name,
	F.id facility_id, F.facility_name, P.fiscal_year, T.id tank_id, T_H.history_date
FROM ustx.owners_mvw O, ustx.facilities_mvw F,
	ustx.permits P, ustx.tanks T, ustx.tank_history T_H,
	ustx.fiscal_years FY, ustx.invoices I, ustx.invoice_detail INV_D
WHERE O.id = P.owner_id
	AND F.id = P.facility_id
	AND T.facility_id = F.id
	AND T_H.tank_id = T.id
	AND T_H.history_code = 'I'
	AND O.id = I.owner_id
	AND I.invoice_date = to_date(:invoice_date, 'mm/dd/yyyy')
	AND I.id = INV_D.invoice_id
	AND INV_D.sum_balances > 0
	AND INV_D.fiscal_year = FY.fiscal_year
	AND P.fiscal_year = FY.fiscal_year
	AND INV_D.tank_fee_balance > 0
	AND FY.start_date < T_H.history_date
	AND FY.end_date > T_H.history_date
ORDER BY O.id, F.id, P.fiscal_year, T.id";

$partial_payment_sql = "
-- Owners Making Partial Payments
SELECT DISTINCT 'MAIN' MAIN_GROUP, O.id owner_id, O.owner_name,
	T.transaction_date, T.fiscal_year, T.amount payment, INV_D.tank_fee_waiver,
	INV_D.tank_fee_invoiced, INV_D.tank_fee_balance
FROM ustx.owners_mvw O, ustx.transactions T,
	ustx.invoices I, ustx.invoice_detail INV_D
WHERE O.id = T.owner_id
	AND O.id = I.owner_id
	AND I.id = INV_D.invoice_id
	AND I.invoice_date = to_date(:invoice_date, 'mm/dd/yyyy')
	AND nvl(INV_D.tank_fee_payment,0) > 0
	AND nvl(INV_D.tank_fee_balance,0) > 0
	AND T.transaction_code = 'PP'
	AND T.fiscal_year = INV_D.fiscal_year
ORDER BY O.id, INV_D.tank_fee_invoiced, T.transaction_date";

$report = new Report($output_format, 'Tank Fee Billing Exceptions', "Invoice Date: {$invoice_date}");


// Facilities with Tanks Installed Midyear section ============================
$rs_arr = $db->query($midyear_sql, array(':invoice_date' => $invoice_date))->as_array();

if (count($rs_arr)) {
	$report->setGroup($rs_arr, array(
		array('name' => 'MAIN_GROUP',
			'header_func' => 'main_header'),
		array('name' => 'OWNER_NAME',
			'header_func' => 'owner_header',
			'footer_func' => 'owner_footer'),
		array('name' => 'FISCAL_YEAR',
			'header_func' => 'fy_header'),
		array('name' => 'TANK_ID',
			'row_func' => 'tank_row')
	));
}

// Owners Making Partial Payments =============================================
$rs_arr = $db->query($partial_payment_sql, array(':invoice_date' => $invoice_date))->as_array();

if (count($rs_arr)) {
	$report->setGroup($rs_arr, array(
		array('name' => 'MAIN_GROUP',
			'header_func' => 'pp_main_header'),
		array('name' => 'OWNER_NAME',
			'header_func' => 'pp_owner_header',
			'footer_func' => 'pp_owner_footer'),
		array('name' => 'TANK_FEE_INVOICED',
			'header_func' => 'pp_invoiced_header'),
		array('name' => 'TRANSACTION_DATE',
			'row_func' => 'pp_date_row')
	));
}

$report->setColumnSize(array(15, 30, 20, 15, 15, 15));
$flag = $report->output('tank_fee_billing_exceptions');


// Facilities with Tanks Installed Midyear funcs ==============================

function main_header(&$report, $row, $params) {
	global $REPORT_HEADER_STYLE;

	$report->setLabelRow( array(
		array('value'=>'Facilities with Tanks Installed Midyear', 'colspan'=>6)
	),$REPORT_HEADER_STYLE );
        $report->setBlankRow();
}

function owner_header(&$report, $row) {
	$report->setLabelRow( array( array('value'=>"Owner: {$row['OWNER_NAME']} ({$row['OWNER_ID']})", 'colspan'=>5)) );
	$report->setLabelRow( array('F ID', 'Facility Name', 'FY', 'Tank ID', 'Install Date') );
}

function owner_footer(&$report, $row, $params) {
	$report->setBlankRow();
}

function fy_header(&$report, $row) {
	$report->setRow(array(
		array('value' => $row['FACILITY_ID']),
		array('value' => $row['FACILITY_NAME']),
		array('value' => $row['FISCAL_YEAR'])
	), FALSE);
}

function tank_row(&$report, $row) {
	$report->setRow(array(
		'',
		'',
		'',
		array('value' => $row['TANK_ID']),
		array('value' => Report::TO_DATE($row['HISTORY_DATE']), 'style' => Report::$STYLE_DATE)
	), FALSE);
}

// Owners Making Partial Payments funcs =======================================

function pp_main_header(&$report, $row, $params) {
	global $REPORT_HEADER_STYLE;

	$report->setLabelRow( array(
		array('value'=>'Report: Owners Making Partial Payments', 'colspan'=>6)
	),$REPORT_HEADER_STYLE );
	$report->setBlankRow();
}

function pp_owner_header(&$report, $row) {
	$report->setLabelRow( array( array('value'=>"Owner: {$row['OWNER_NAME']} ({$row['OWNER_ID']})", 'colspan'=>6)) );
	$report->setLabelRow( array('Inv Waiver', 'Total Inv Assessed', 'Inv Balance', 'Pay Date', 'FY', 'Payment Amt') );
}

function pp_owner_footer(&$report, $row, $params) {
	$report->setBlankRow();
}

function pp_invoiced_header(&$report, $row) {
	$report->setRow(array(
		array('value' => $row['TANK_FEE_WAIVER'], 'style' => Report::$STYLE_MONEY),
		array('value' => $row['TANK_FEE_INVOICED'], 'style' => Report::$STYLE_MONEY),
		array('value' => $row['TANK_FEE_BALANCE'], 'style' => Report::$STYLE_MONEY)
	), FALSE);
}

function pp_date_row(&$report, $row) {
	$report->setRow(array(
		'',
		'',
		'',
		array('value' => Report::TO_DATE($row['TRANSACTION_DATE']), 'style' => Report::$STYLE_DATE),
		array('value' => $row['FISCAL_YEAR']),
		array('value' => $row['PAYMENT'], 'style' => Report::$STYLE_MONEY)
	), FALSE);
}

?>
