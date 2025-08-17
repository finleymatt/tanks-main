<?php
/**
 * Current FY Tank Fees
 *
 * @package Onestop
 * @subpackage views
 * @uses Report.php
 *
*/

$db = Database::instance();

if (!$start_date || !$fy)
	exit('Required fields not entered.');

// query from invoice report ust_inv_bat_prior_no.rdf -maybe this is a starting point?
$report_sql = "
SELECT 'MAIN' main, invoices.id invoice_id, invoice_code, 
	ustx.ust_invoice.adjusted_invoice_code( invoices.owner_id ) adjusted_invoice_code, 
	invoice_date, owners.id owner_id, 
	owners.owner_name name, owners.address1,owners.address2, owners.city, 
	owners.state,owners.zip, due_date
FROM ustx.invoices, ustx.owners_mvw owners
WHERE trunc(invoices.invoice_date) = TO_DATE(:invoice_date, 'mm/dd/yyyy')
	and invoices.owner_id = owners.id
	and invoices.owner_id in (select owner_id
		from ustx.transactions
		where instr(transaction_code, 'H')=0
			and instr(transaction_code, 'G')=0
			and fiscal_year between 1979 and :invoice_fy-1
		group by owner_id
		having sum(decode(transaction_code,'PP',amount*-1,'LP',amount*-1,
			'WP',amount*-1,'IP',amount*-1,'PW',amount*-1,
			'LW', amount*-1, 'IW', amount*-1, amount)) = 0)
	and invoices.owner_id in (select owner_id
		from ustx.transactions
		where instr(transaction_code, 'H')=0
			and instr(transaction_code, 'G')=0
			and fiscal_year = :invoice_fy
		group by owner_id
		having sum(decode(transaction_code,'PP',amount*-1,'LP',amount*-1,
			'WP',amount*-1,'IP',amount*-1,'PW',amount*-1,
			'LW', amount*-1, 'IW', amount*-1, amount)) > 0)
ORDER BY owners.owner_name";

$rs_arr = $db->query($report_sql, array(':invoice_date' => $start_date, ':invoice_fy' => $fy))->as_array();
exit;
$report = new Report($output_format, 'Current FY Tank Fees', "Invoice Date: {$start_date} FY: {$fy}");

if (count($rs_arr)) {
	$report->setGroup($rs_arr, array(
		array('name' => 'MAIN',
			'header_func' => 'main_header',
			'footer_func' => 'main_footer'),
		array('name' => 'OWNER_ID',
			'row_func' => 'owner_row')
	));
}

$report->setColumnSize(array(10, 30, 10, 10, 10, 10, 12, 12, 12, 12));
$flag = $report->output('current_fy_tank_fees');


function main_header(&$report, $row) {
	$report->setLabelRow( array('Owner ID', 'Owner Name', 'Sept Inv Amounts', 'Date Paid', 'Amount Paid', 'Balance', 'Error', 'Insp', 'Demand Letter', 'Comments'), array('style' => array('alignment' => array('wrap' => TRUE))) );
}

function main_footer(&$report, $row, $params) {
	$report->setRow(array(
		array(
			'value' => "Grand Totals:",
			'colspan' => 6,
			'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT))
		),
		6 => array(
			'value' => "=SUM(G{$params['group_row_start']}:G{$params['group_row_end']})",
			'style' => Report::$STYLE_MONEY
		),
		array(
			'value' => "=SUM(H{$params['group_row_start']}:H{$params['group_row_end']})",
			'style' => Report::$STYLE_MONEY
		),
		array(
			'value' => "=SUM(I{$params['group_row_start']}:I{$params['group_row_end']})",
			'style' => Report::$STYLE_MONEY
		),
		array(
			'value' => "=SUM(J{$params['group_row_start']}:J{$params['group_row_end']})",
			'style' => Report::$STYLE_MONEY
		)
	), FALSE, Report::$STYLE_TOTAL);

	$report->setBlankRow();
}

function owner_row(&$report, $row) {
	$report->setRow(array(
		5 => $row['FISCAL_YEAR'],
		array('value' => $row['OWNER_ID'], 'style' => Report::$STYLE_MONEY),
		array('value' => $row['OWNER_NAME'], 'style' => Report::$STYLE_MONEY),
		array('value' => $row['INVOICE_AMOUNT'], 'style' => Report::$STYLE_MONEY),
		array('value' => Report::TO_DATE($row['PAY_DATE']), 'style' => Report::$STYLE_DATE),
		array('value' => "=C{$report->row_num} - E{$report->row_num}", 'style' => Report::$STYLE_MONEY)
	), FALSE);
}

?>
