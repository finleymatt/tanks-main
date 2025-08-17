<?php
/**
 * GPA Invoice Print
 *
 * @package Onestop
 * @uses Report.php
 *
 * This report is highly optimized for TCPDF output.
 * It will contain formatting errors for non-TCPDF formats.
*/

$db = Database::instance();

$invoice_sql = "-- query from legacy oracle notice report: ust_gpa_invoice.rdf
SELECT I.id invoice_id, I.invoice_code, to_char(I.invoice_date, 'dd-Mon-yyyy') invoice_date,
	to_char(I.due_date,'dd-Mon-yyyy') due_date, I.nov_gpa_facility_id, I.nov_gpa_amount,
	O.id owner_id, O.owner_name, O.address1 owner_address1,
	O.address2 owner_address2, O.city||', '||O.state||'  '||O.zip owner_address3,
	F.id facility_id, F.facility_name, F.address1 facility_address1,
	F.address2 facility_address2, F.city||', '||F.state||'  '||F.zip facility_address3
FROM ustx.invoices I, ustx.owners_mvw O, ustx.facilities_mvw F
WHERE O.id = I.owner_id  
	AND I.id = :invoice_id
	AND F.id = I.nov_gpa_facility_id
	AND I.invoice_code = 'GPA'";

$invoice_code_sql = "SELECT invoice_text, cupon_format from ustx.invoice_codes
	WHERE code = 'GPA'";

/**
 * Extends the TCPDF class to create custom Header and Footer
 * Can use $this->writeHTML() for simpler formatting, but uses low-level TCPDF methods for
 * more precise formatting.
 **/
class MY_TCPDF extends TCPDF {
	public function __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false) {
		parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache);
		$this->setPrintHeader(TRUE);
		$this->setPrintFooter(FALSE);
		$this->SetMargins(50, 112);  // left, top
	}

	public function Header() {
		$this->Image("{$GLOBALS['GLOBAL_INI']['kohana']['www_path']}/images/nmed_logo_med.gif", 30, 12, 0, 70, 'GIF', '', 'T', false, 300, '', false, false, 0, false, false, false);
		$this->SetFont('helvetica', 'B', 16);
		$this->SetX(110);  // set titles right relative of left edge
		$this->Cell(0, 22, "Storage Tank GPA Invoice", 0, 2, 'L', 0, '', 0, false, 'M', 'M');
		$this->SetFont('helvetica', 'B', 12);
		$this->Cell(0, 12, 'New Mexico Environment Department', 0, 2, 'L', 0, '', 0, false, 'M', 'M');
		$this->Cell(0, 12, 'Petroleum Storage Tank Bureau', 0, 1, 'L', 0, '', 0, false, 'M', 'M');
		$this->SetXY(480, 70);  // page number towards right edge
		$this->SetFont('helvetica', '', 8);
		$this->Cell(0, 8, 'Page ' . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages(), 0, 1, 'L', 0, '', 0, false, 'M', 'M');
		$this->Line(35, 85, 550, 85, array('width'=>0.8, 'color'=>array(0,0,0)));
		$this->SetXY(20, 90);  // location for Invoice Number
		$this->SetFont('helvetica', 'B', 9);
		$this->Cell(0, 9, 'Invoice Number: GPA '. INVOICE_NUMBER .'                                        Invoice Date: '. INVOICE_DATE, 0, 1, 'C', 0, '', 0, false, 'M', 'M');
		$this->Line(35, 105, 550, 105, array('width'=>0.8, 'color'=>array(0,0,0)));
	}
}

$temp_rows = $db->query($invoice_code_sql)->as_array();
$invoice_code_row = $temp_rows[0];

// get invoice/facility/owner info ---------------------------------------------
$temp_rows = $db->query($invoice_sql, array(':invoice_id' => $invoice_id))->as_array();
if (! count($temp_rows)) exit('No GPA invoice found under the requested ID');

$invoice_row = $temp_rows[0];
define('INVOICE_NUMBER', $invoice_row['INVOICE_ID']);         // used in page header
define('INVOICE_DATE', $invoice_row['INVOICE_DATE']); // used in page header

$report = new Report('pdf', 'PSTB Storage GPA Invoice', '', FALSE);
$report->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);


// owner info and adress ------------------------------------------------------
$report->setBlankRow(); $report->setBlankRow(); $report->setBlankRow();
$report->setRow(array(
	label_cell('Owner'),
	array('colspan' => 3, 'rowspan' => 3,
		'value' => implode("\n", array($invoice_row['OWNER_NAME'], $invoice_row['OWNER_ADDRESS1'], $invoice_row['OWNER_ADDRESS2'], $invoice_row['OWNER_ADDRESS3']))),
	4 => label_cell('Owner ID'),
	array('value' => $invoice_row['OWNER_ID'], 'style' => Report::$STYLE_LEFT)
), FALSE, array('font' => array('bold' => true)));

$report->setRow(array(4 => label_cell('Facility ID'),
	array('value' => $invoice_row['FACILITY_ID'], 'style' => Report::$STYLE_LEFT)
), FALSE, array('font' => array('bold' => true)));

$report->setRow(array(4 => label_cell('Due Date'), $invoice_row['DUE_DATE']
), FALSE, array('font' => array('bold' => true)));

$report->setBlankRow(); $report->setBlankRow();


// invoice text - GPA explanation ---------------------------------------------
$report->setLine(6);
$report->setBlankRow();
$report->setRow(array(array('colspan' => 6,
	'value' => $invoice_code_row['INVOICE_TEXT'],
	'style' => array('font' => array('size' => 8.5))
)), FALSE);


// facility info, address, and invoice amount ---------------------------------
$report->setBlankRow();
$report->setLine(6);
$report->setBlankRow(); $report->setBlankRow();
$report->setRow(array(
	label_cell('Facility'),
	array('colspan' => 3, 'rowspan' => 2,
		'value' => implode("\n", array($invoice_row['FACILITY_NAME'], $invoice_row['FACILITY_ADDRESS1'], $invoice_row['FACILITY_ADDRESS2'], $invoice_row['FACILITY_ADDRESS3']))),
	4 => label_cell('Facility ID'),
	array('value' => $invoice_row['FACILITY_ID'], 'style' => Report::$STYLE_LEFT)
), FALSE);

$report->setRow(array(4 => label_cell('Amount'),
	array('value' => $invoice_row['NOV_GPA_AMOUNT'],
		'style' => array_merge(Report::$STYLE_MONEY, Report::$STYLE_LEFT))), FALSE);


// coupon text ----------------------------------------------------------------
$report->setPageBreak();
$report->setDashLine(6);
$report->setRow(array(array('colspan' => 6,
	'value' => $invoice_code_row['CUPON_FORMAT'],
	'style' => array('font' => array('size' => 8.5))
)), FALSE);

// owner and facility info again for coupon -----------------------------------
$report->setBlankRow(); $report->setBlankRow(); $report->setBlankRow();
$report->setRow(array(
	label_cell('Owner'),
	array('colspan' => 3, 'rowspan' => 3,
		'value' => implode("\n", array($invoice_row['OWNER_NAME'], $invoice_row['OWNER_ADDRESS1'], $invoice_row['OWNER_ADDRESS2'], $invoice_row['OWNER_ADDRESS3']))),
	4 => label_cell('Owner ID'),
	array('value' => $invoice_row['OWNER_ID'], 'style' => Report::$STYLE_LEFT)
), FALSE);

$report->setRow(array(4 => label_cell('Facility ID'),
	array('value' => $invoice_row['FACILITY_ID'], 'style' => Report::$STYLE_LEFT)
), FALSE);

$report->setRow(array(4 => label_cell('Due Date'), $invoice_row['DUE_DATE']), FALSE);

$report->setRow(array(4 => label_cell('Inv Total'),
	array('value' => $invoice_row['NOV_GPA_AMOUNT'],
		'style' => array_merge(Report::$STYLE_MONEY, Report::$STYLE_LEFT))
), FALSE);


$report->setColumnSize(array(16, 18, 18, 18, 18, 18));
$flag = $report->output("gpa_invoice_{$invoice_row['INVOICE_ID']}");



// local functions ----------------------------------------------

function label_cell($value) {
	return(array('value' => "{$value}: &nbsp;", 'style' => Report::$STYLE_RIGHT));
}
?>
