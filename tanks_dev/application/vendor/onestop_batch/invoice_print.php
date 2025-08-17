<?php
/**
 * Invoice Print
 *
 * @package Onestop
 * @subpackage vendor/onestop_batch
 * @uses Report.php
 *
 * This report is highly optimized for TCPDF output.
 * It will contain formatting errors for non-TCPDF formats.
 *
 * This file is used two ways:
 *   1) as part of command line php process -- for async batch pdf creation
 *   2) included in kohana and print_invoice() called -- for single pdf download
 *
 * Due to its highly customized nature and using it in two different ways,
 * this code is an extreme case of using PHPExcel library and is messy.
 * It could possibly be comparmentalized little by making it into a class and
 * having kohana version be subclassed to it with overridden db/output methods.
*/

define('DO_RAW_HTML', TRUE);

$GLOBALS['PRINT_SQL'] = array(
	'all_fys' => '-- All FYs - for All FYs and all owners
		SELECT O.*, D.*, I.*, F.invoice_due_date fy_due_date
		FROM ustx.invoice_detail D
			INNER JOIN ustx.invoices I ON D.invoice_id = I.id
			INNER JOIN ustx.fiscal_years F ON D.fiscal_year = F.fiscal_year
			INNER JOIN ustx.owners_mvw O ON I.owner_id = O.id
		WHERE -- remove all negative balances
			-- (D.tank_fee_balance + D.late_fee_balance + D.interest_balance) > 0
			-- AND (D.invoice_id = :invoice_id)
			-- no longer remove all negative balances, get all balances, even <= $0 amounts
			D.invoice_id = :invoice_id
		ORDER BY D.fiscal_year DESC',
	'single' => '-- for single owner
		SELECT O.*, D.*, I.*, F.invoice_due_date fy_due_date
		FROM ustx.invoice_detail D
			INNER JOIN ustx.invoices I ON D.invoice_id = I.id
			INNER JOIN ustx.fiscal_years F ON D.fiscal_year = F.fiscal_year
			INNER JOIN ustx.owners_mvw O ON I.owner_id = O.id
		WHERE -- unlike all owners option, get amounts, even <= $0 amounts
			D.invoice_id = :invoice_id
		ORDER BY D.fiscal_year DESC',
	/*'all_fys' => '-- All FYs - for All FYs and all owners
		SELECT O.*, D1.*, I.*, F.invoice_due_date fy_due_date 
		FROM ustx.invoice_detail D1
			INNER JOIN ustx.invoices I ON D1.invoice_id = I.id
			INNER JOIN ustx.fiscal_years F ON D1.fiscal_year = F.fiscal_year
			INNER JOIN ustx.owners_mvw O ON I.owner_id = O.id
		WHERE NOT EXISTS ( -- remove fiscal years have balances can cancel each other out($100, -$100)
			SELECT * FROM ustx.invoice_detail D2
			INNER JOIN ustx.invoices I ON D2.invoice_id = I.id
			INNER JOIN ustx.fiscal_years F ON D2.fiscal_year = F.fiscal_year
			INNER JOIN ustx.owners_mvw O ON I.owner_id = O.id
			WHERE D1.sum_balances = -D2.sum_balances
			AND D1.sum_balances <> D2.sum_balances
			AND D2.INVOICE_ID = :invoice_id
		)
		AND D1.invoice_id = :invoice_id
		ORDER BY D1.fiscal_year DESC',
	'single' => '-- for single owner
		SELECT O.*, D1.*, I.*, F.invoice_due_date fy_due_date
		FROM ustx.invoice_detail D1
			INNER JOIN ustx.invoices I ON D1.invoice_id = I.id
			INNER JOIN ustx.fiscal_years F ON D1.fiscal_year = F.fiscal_year
			INNER JOIN ustx.owners_mvw O ON I.owner_id = O.id
		WHERE -- unlike all owners option, get amounts, even <= $0 amounts
			--remove fiscal years have balances can cancel each other out
			NOT EXISTS (
			SELECT * FROM ustx.invoice_detail D2
			INNER JOIN ustx.invoices I ON D2.invoice_id = I.id
			INNER JOIN ustx.fiscal_years F ON D2.fiscal_year = F.fiscal_year
			INNER JOIN ustx.owners_mvw O ON I.owner_id = O.id
			WHERE D1.sum_balances = -D2.sum_balances
			AND D1.sum_balances <> D2.sum_balances
			AND D2.INVOICE_ID = :invoice_id
		)
		AND D1.invoice_id = :invoice_id
		ORDER BY D1.fiscal_year DESC',*/
	'facility' => "
		SELECT 'MAIN' MAIN, IDF.facility_id, tank_count facility_tank_count,
			facility_name, address1, address2, (city || ', ' || state || '  ' || zip) address3,
			TANKS.operator_id, TANKS.operator_name
		FROM ustx.invoice_detail_facilities IDF, ustx.facilities_mvw F,
			(select distinct facility_id, operator_id, operator_name
				from ustx.tanks, ustx.operators_mvw operators
				where tanks.operator_id = operators.id
					and tanks.tank_status_code in (1,2)) TANKS
		WHERE IDF.facility_id = F.id 
			AND F.id = TANKS.facility_id (+)
			AND IDF.invoice_id = :invoice_id
		ORDER BY facility_name",
	'invoice_code' => '-- get dynamic text depending on bankruptcy status
		SELECT IC.* FROM ustx.invoice_codes IC 
		WHERE IC.code = USTX.UST_INVOICE.adjusted_invoice_code(:owner_id)'
);
$GLOBALS['PRINT_SQL']['selected_fy'] = $GLOBALS['PRINT_SQL']['all_fys'];  // same
$GLOBALS['PRINT_SQL']['selected_prior1_fy'] = $GLOBALS['PRINT_SQL']['all_fys'];  // same

/**
 * Extends the TCPDF class to create custom Header and Footer
 * Can use $this->writeHTML() for simpler formatting, but uses low-level TCPDF methods for
 * more precise formatting.
 **/
class MY_TCPDF extends TCPDF {
	public function __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false) {
		parent::__construct($orientation, $unit, 'LETTER', $unicode, $encoding, $diskcache);
		$this->setPrintHeader(TRUE);
		$this->setPrintFooter(FALSE);
		$this->SetMargins(50, 112);  // left, top
	}

	public function Header() {
		$inv_name = ($GLOBALS['IS_BANKRUPT'] ? 'Notice' : 'Invoice');
		$this->Image("{$GLOBALS['GLOBAL_INI']['kohana']['www_path']}/images/nmed_logo_med.gif", 30, 12, 0, 70, 'GIF', '', 'T', false, 300, '', false, false, 0, false, false, false);
		$this->SetFont('helvetica', 'B', 16);
		$this->SetX(110);  // set titles right relative of left edge
		$this->Cell(0, 22, "Storage Tank {$inv_name}", 0, 2, 'L', 0, '', 0, false, 'M', 'M');
		$this->SetFont('helvetica', 'B', 12);
		$this->Cell(0, 12, 'New Mexico Environment Department', 0, 2, 'L', 0, '', 0, false, 'M', 'M');
		$this->Cell(0, 12, 'Petroleum Storage Tank Bureau', 0, 1, 'L', 0, '', 0, false, 'M', 'M');
		$this->SetXY(480, 70);  // page number towards right edge
		$this->SetFont('helvetica', '', 8);
		$this->Cell(0, 8, 'Page ' . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages(), 0, 1, 'L', 0, '', 0, false, 'M', 'M');
		$this->Line(35, 85, 550, 85, array('width'=>0.8, 'color'=>array(0,0,0)));
		$this->SetXY(20, 90);  // location for Notice Number
		$this->SetFont('helvetica', 'B', 9);
		$this->Cell(0, 9, "{$inv_name} Number: {$GLOBALS['INVOICE_NUMBER']}                                         {$inv_name} Date: {$GLOBALS['INVOICE_DATE']}", 0, 1, 'C', 0, '', 0, false, 'M', 'M');
		$this->Line(35, 105, 550, 105, array('width'=>0.8, 'color'=>array(0,0,0)));
	}

	// Page footer not used yet
	public function Footer() {
		// Position at 15 mm from bottom
		// $this->SetY(-15);
	}
}

/**
 * $is_cmdline: boolean value that specifies whether this function is called
 *    under batch command line environment.
 * $conn: DB connection is passed in only when $is_cmdline is TRUE
 * */
function print_invoice($invoice_id, $print_opt, $is_cmdline, $conn=NULL, $seq=NULL) {
	global $GLOBAL_INI, $PRINT_SQL;

	// main invoice fees list -----------------------------------------
	#echo $PRINT_SQL[$print_opt]; exit;
	$invoice_detail_rows = query($is_cmdline, $conn, $PRINT_SQL[$print_opt], array(':invoice_id' => $invoice_id));
	/*
	echo "<pre>";
	print_r($invoice_detail_rows);
	echo "</pre>";
	exit;
	*/

	if (!count($invoice_detail_rows)) return(NULL);

	$GLOBALS['INVOICE_NUMBER'] = $invoice_detail_rows[0]['INVOICE_ID'];  // used in page header
	$GLOBALS['INVOICE_DATE'] = $invoice_detail_rows[0]['INVOICE_DATE'];  // used in page header

	// invoice code / bankruptcy check --------------------------------
	$invoice_code_rows = query($is_cmdline, $conn, $PRINT_SQL['invoice_code'], array(':owner_id' => $invoice_detail_rows[0]['OWNER_ID']));
	$GLOBALS['IS_BANKRUPT'] = (($invoice_code_rows[0]['CODE'] == 'USTBR') ? TRUE : FALSE);
	$GLOBALS['RETURN_ADDRESS'] = $invoice_code_rows[0]['CUPON_FORMAT']; // used in main_header

	$report = new Report('pdf', 'PSTB Storage Tank Notice', '', FALSE);
	$report->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
	$report->getDefaultStyle()->getFont()->setName('helvetica')->setSize(9);

	$owner_id = $invoice_detail_rows[0]['OWNER_ID'];
	$GLOBALS['INVOICE_TOTAL'] = calc_total($invoice_detail_rows);

	$report->setGroup($invoice_detail_rows, array(
		array('name' => 'OWNER_ID',
			'header_func' => 'main_header',
			'footer_func' => 'main_footer'),
		array('name' => 'FISCAL_YEAR',
			'header_func' => 'fy_header',
			'row_func' => 'fy_row',
			'footer_func' => 'fy_footer'),
	));

	// facility/tank list ----------------------------------------------
	$facility_rows = query($is_cmdline, $conn, $PRINT_SQL['facility'], array(':invoice_id' => $invoice_id));

	if (count($facility_rows)) {
		$report->setGroup($facility_rows, array(
			array('name' => 'MAIN',
				'header_func' => 'fac_main_header'),
			array('name' => 'FACILITY_ID',
				'row_func' => 'facility_row')
		));
	}

	// last page info --------------------------------------------------
	$report->setPageBreak();
	$report->setRow(array(
		array('value' => 'PLEASE READ:  IMPORTANT INFORMATION THAT MAY AFFECT YOU',
			'colspan' => 6,
			'style' => array_merge(Report::$STYLE_CENTER, array('font' => array('bold' => true)))
		)
	), FALSE);
	$report->setLine(6);

	$report->setRow(array(array('colspan' => 6,
		'value' => $invoice_code_rows[0]['INVOICE_TEXT']
	)), FALSE);

	$report->setColumnSize(array(19, 22, 22, 22, 22, 22));

	if ($is_cmdline) {
		$filename = 'inv' . (is_null($seq) ? '' : $seq) . "_{$owner_id}_{$GLOBALS['INVOICE_DATE']}";
		$filename = "{$GLOBAL_INI['kohana']['application_path']}/cache/{$filename}.pdf";
		//$filename = "/home/mlee/cache/{$filename}.pdf";

		if (!PHPExcel_Settings::setPdfRenderer(
			PHPExcel_Settings::PDF_RENDERER_TCPDF,
			"{$GLOBAL_INI['kohana']['application_path']}/vendor/reports/tcpdf_old/"
		)) die('Could not set TCPDF');
		$objWriter = PHPExcel_IOFactory::createWriter($report, 'PDF');
		$objWriter->save($filename);

		unset($objWriter); $report->__destruct(); unset($report); // free memory leak

		return($filename);
	}
	else {
		$filename = "inv_{$owner_id}_{$GLOBALS['INVOICE_DATE']}";
		$report->output($filename);
		return($filename);
	}
}

function main_header(&$report, $row, $params) {
	$style_bold = array('font' => array('bold' => true));
	$owner_address = "{$row['OWNER_NAME']}\n{$row['ADDRESS1']}\n"
		. ($row['ADDRESS2'] ? "{$row['ADDRESS2']}\n" : '')
		. "{$row['CITY']}, {$row['STATE']}  {$row['ZIP']}";

	$report->setBlankRow(); $report->setBlankRow(); $report->setBlankRow();
	$report->setRow(array(
		label_cell('Owner'),
		array('colspan' => 3, 'rowspan' => 2,
			'value' => $owner_address),
		4 => label_cell('Owner ID'),
		array('value' => $row['OWNER_ID'], 'style' => Report::$STYLE_LEFT)
	), FALSE, $style_bold);

	$report->setRow(array(4 => label_cell('Due Date'), $row['DUE_DATE']), FALSE, $style_bold);
	$report->setRow(array(3 => array('value' => "Total Amount Due: &nbsp;", 'style' => Report::$STYLE_RIGHT, 'colspan' => 2),
		5 => array('value' => $GLOBALS['INVOICE_TOTAL'],
                        'style' => array_merge(Report::$STYLE_MONEY, Report::$STYLE_LEFT))), FALSE, $style_bold);
	$report->setBlankRow();

	// start of cutoff return mailing label ---------------------------------------
	$report->setDashLine(6);
	$report->setBlankRow(); $report->setBlankRow(); $report->setBlankRow(); $report->setBlankRow();
	$report->setRow(array(array('colspan' => 6,
		'value' => $GLOBALS['RETURN_ADDRESS'])), FALSE);

	$report->setBlankRow(); $report->setBlankRow();
	$report->setRow(array(
		label_cell('Owner'),
		array('colspan' => 3, 'rowspan' => 4,
			'value' => $owner_address),
		4 => label_cell('Owner ID'),
		array('value' => $row['OWNER_ID'], 'style' => Report::$STYLE_LEFT)
	), FALSE);

	$report->setRow(array(4 => label_cell('Total'),
		array('value' => $GLOBALS['INVOICE_TOTAL'],
                        'style' => array_merge(Report::$STYLE_MONEY, Report::$STYLE_LEFT))), FALSE);
	$report->setRow(array(4 => label_cell('Due Date'), $row['DUE_DATE']), FALSE);
	$report->setRow(array(4 => label_cell('Invoice Num'),
		array('value' => $row['INVOICE_ID'], 'style' => Report::$STYLE_LEFT)), FALSE);

	$report->setBlankRow(); $report->setBlankRow();
	$report->setRow(array(array('colspan' => 6,
                'value' => '**When you provide a check as payment, you authorize the State of New Mexico to either use information from your check to make a one-time electronic fund transfer from your account or to process the payment as a check transaction.', 'style' => Report::$STYLE_HIGHLIGHT_YELLOW)), FALSE);
	$report->setBlankRow();

	$report->setPageBreak();

	// header for next page
	$report->setRow(array(
		array('colspan' => 6,
			'style' => array_merge(array('font' => array('underline' => PHPExcel_Style_Font::UNDERLINE_DOUBLE)), Report::$STYLE_CENTER),
			'value' => 'Storage Tank Fees'
		)), FALSE);
}

// main invoice group functions ====================================================

function main_footer(&$report, $row, $params) {
	$group_row_start = $params['group_row_start'] + 1;
	$report->setRow(array(
		array(
			'value' => "Invoice Total Balance:",
			'colspan' => 5,
			'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT))
		),
		5 => array(
			'value' => $GLOBALS['INVOICE_TOTAL'],
			'style' => Report::$STYLE_MONEY
		)
	), FALSE, Report::$STYLE_TOTAL);

	$report->setBlankRow();
}

function fy_header(&$report, $row) {
	$style = array(
		'font' => array(
			'bold' => true,
			'color' => array('argb' => 'FF000000')
		),
		'fill' => array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array('argb' => 'FFDDDDDD') // pink:'FFD07070'
		)
	);

	$report->setLabelRow(array(
		array('value' => "Fiscal Year: {$row['FISCAL_YEAR']}", 'colspan' => 3),
		3 => array('value' => "Due Date: {$row['FY_DUE_DATE']}", 'colspan' => 3)
	), $style);

	$report->setLabelRow(array(
		'', 'New Charges', 'Prev Invoiced', 'Waiver', 'Payment', 'Balance'
	), array_merge($style, Report::$STYLE_RIGHT));
}

function fy_row(&$report, $row) {
	$report->setRow(array(
		'Tank Fee:',
		array('value' => $row['TANK_FEE'], 'style' => Report::$STYLE_MONEY),
		array('value' => $row['TANK_FEE_INVOICED'], 'style' => Report::$STYLE_MONEY),
		array('value' => $row['TANK_FEE_WAIVER'], 'style' => Report::$STYLE_MONEY),
		array('value' => $row['TANK_FEE_PAYMENT'], 'style' => Report::$STYLE_MONEY),
		array('value' => $row['TANK_FEE_BALANCE'], 'style' => Report::$STYLE_MONEY),
	), FALSE);

	$report->setRow(array(
		'Late Fee:',
		array('value' => $row['LATE_FEE'], 'style' => Report::$STYLE_MONEY),
		array('value' => $row['LATE_FEE_INVOICED'], 'style' => Report::$STYLE_MONEY),
		array('value' => $row['LATE_FEE_WAIVER'], 'style' => Report::$STYLE_MONEY),
		array('value' => $row['LATE_FEE_PAYMENT'], 'style' => Report::$STYLE_MONEY),
		array('value' => $row['LATE_FEE_BALANCE'], 'style' => Report::$STYLE_MONEY),
	), FALSE);

	$report->setRow(array(
		'Interest:',
		array('value' => $row['INTEREST'], 'style' => Report::$STYLE_MONEY),
		array('value' => $row['INTEREST_INVOICED'], 'style' => Report::$STYLE_MONEY),
		array('value' => $row['INTEREST_WAIVER'], 'style' => Report::$STYLE_MONEY),
		array('value' => $row['INTEREST_PAYMENT'], 'style' => Report::$STYLE_MONEY),
		array('value' => $row['INTEREST_BALANCE'], 'style' => Report::$STYLE_MONEY),
	), FALSE);
}

function fy_footer(&$report, $row, $params) {
	$group_row_start = $params['group_row_start'] + 1;
	$report->setRow(array(
		array(
			'value' => "Year Total Balance:",
			'colspan' => 5,
			'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT))
		),
		5 => array('value' => $row['SUM_BALANCES'], 'style' => Report::$STYLE_MONEY)
		//"=SUM(F{$group_row_start}:F{$params['group_row_end']})"
	), FALSE, Report::$STYLE_TOTAL);

	$report->setBlankRow();
}

// facility and tank group functions ===============================================

function fac_main_header(&$report, $row, $params) {
	$report->setPageBreak();
	$report->setRow(array(
		array('colspan' => 5,
			'style' => array('font' => array('underline' => PHPExcel_Style_Font::UNDERLINE_DOUBLE)),
			'value' => 'You are the Currently Registered Owner of Tanks at the Following Facilities'
		)), FALSE);
}

function facility_row(&$report, $row, $params) {
	$address = "{$row['FACILITY_NAME']}\n{$row['ADDRESS1']}"
		. ($row['ADDRESS2'] ? "\n{$row['ADDRESS2']}" : '')
		. "\n{$row['ADDRESS3']}";

	$report->setRow(array(
		"Fac ID: {$row['FACILITY_ID']}",
		array('colspan' => 2, 'value' => $address),
		3 => "Operator ID: {$row['OPERATOR_ID']}",
		array('colspan' => 2, 'value' => $row['OPERATOR_NAME'])
	), FALSE, array('font' => array('size' => 7)));

	$report->setRow(array("Tank Count: {$row['FACILITY_TANK_COUNT']}")
		, FALSE, array('font' => array('size' => 7)));

	$report->setBlankRow();
}

// local functions ----------------------------------------------

function label_cell($value) {
	return(array('value' => "{$value}: &nbsp;", 'style' => Report::$STYLE_RIGHT));
}

function calc_total($rows) {
	$total = 0;
	foreach ($rows as $row)
		$total += $row['SUM_BALANCES'];

	return($total);
}

function query($is_cmdline, $conn, $query, $bound_vars=NULL, $ret_name=NULL) {
	if ($is_cmdline)
		return(db_query($conn, $query, $bound_vars, $ret_name));
	else
		return(Database::instance()->query($query, $bound_vars)->as_array());
}
?>
