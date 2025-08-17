<?php
/**
 * Operator Notice Report Print
 *
 * @package Onestop
 * @uses Report.php
 *
 * This report is highly optimized for TCPDF output.
 * It will contain formatting errors for non-TCPDF formats.
 * Unlike other reports, this report's sql is simple enough for rows to come from controller
*/

$db = Database::instance();

$facility_sql = "-- query from legacy oracle notice report: ust_noti.rdf
SELECT 'MAIN' MAIN, F.facility_name, F.address1, F.address2, (F.city || ', ' || F.state || '  ' || F.zip) address3,
	NDF.*, O.owner_name
FROM ustx.notice_detail_facilities NDF, ustx.facilities_mvw F,
        ustx.owners_mvw O
WHERE NDF.facility_id = F.id
        and NDF.owner_id = O.id
		and NDF.notice_id = :notice_id
ORDER BY facility_name";

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
		$this->Cell(0, 22, "Storage Tank Operator Notice", 0, 2, 'L', 0, '', 0, false, 'M', 'M');
		$this->SetFont('helvetica', 'B', 12);
		$this->Cell(0, 12, 'New Mexico Environment Department', 0, 2, 'L', 0, '', 0, false, 'M', 'M');
		$this->Cell(0, 12, 'Petroleum Storage Tank Bureau', 0, 1, 'L', 0, '', 0, false, 'M', 'M');
		$this->SetXY(480, 70);  // page number towards right edge
		$this->SetFont('helvetica', '', 8);
		$this->Cell(0, 8, 'Page ' . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages(), 0, 1, 'L', 0, '', 0, false, 'M', 'M');
		$this->Line(35, 85, 550, 85, array('width'=>0.8, 'color'=>array(0,0,0)));
		$this->SetXY(20, 90);  // location for Notice Number
		$this->SetFont('helvetica', 'B', 9);
		$this->Cell(0, 9, 'Notice Number: '. NOTICE_NUMBER .'                                        Notice Date: '. NOTICE_DATE, 0, 1, 'C', 0, '', 0, false, 'M', 'M');
		$this->Line(35, 105, 550, 105, array('width'=>0.8, 'color'=>array(0,0,0)));
	}

	// Page footer not used
	public function Footer() {
	}
}

define('NOTICE_NUMBER', $notice_row['ID']);        // used in page header
define('NOTICE_DATE', $notice_row['NOTICE_DATE']); // used in page header

$report = new Report('pdf', 'PSTB Storage Operator Notice', '', FALSE);
$report->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);

// start of address ---------------------------------------------------
$report->setBlankRow(); $report->setBlankRow(); $report->setBlankRow();
$report->setRow(array(
	label_cell('Operator'),
	array('colspan' => 3,
		'value' => "{$operator_row['OPERATOR_NAME']}\n{$operator_row['ADDRESS1']}\n{$operator_row['ADDRESS2']}\n{$operator_row['CITY']}, {$operator_row['STATE']}  {$operator_row['ZIP']}"),
	4 => label_cell('Operator ID'),
	array('value' => $operator_row['ID'], 'style' => Report::$STYLE_LEFT)
), FALSE, array('font' => array('bold' => true)));
$report->setBlankRow();  $report->setBlankRow();
$report->setDashLine(6);
$report->setBlankRow();

// facility/tank list -------------------------------------------------
$facility_rows = $db->query($facility_sql, array(':notice_id' => $notice_row['ID']))->as_array();
if (count($facility_rows)) {
	$report->setGroup($facility_rows, array(
		array('name' => 'MAIN',
			'header_func' => 'fac_main_header'),
		array('name' => 'FACILITY_ID',
			'row_func' => 'facility_row')
	));
}

// actual notice message ----------------------------------------------
$report->setRow(array(
	array('value' => 'PLEASE READ THIS IMPORTANT INFORMATION THAT MAY AFFECT YOU',
		'colspan' => 6,
		'style' => array_merge(Report::$STYLE_CENTER, array('font' => array('bold' => true)))
	)
), FALSE);
$report->setLine(6);

$report->setRow(array(array('colspan' => 6,
	'value' => $notice_code_row['INVOICE_TEXT'],
	'style' => array('font' => array('size' => 8.5))
)), FALSE);

$report->setColumnSize(array(16, 18, 18, 18, 18, 18));
$flag = $report->output("notice_{$notice_row['OPERATOR_ID']}");



// facility and tank group functions ===============================================

function fac_main_header(&$report, $row, $params) {
	$report->setRow(array(
		array('colspan' => 6,
			'style' => array('font' => array('underline' => PHPExcel_Style_Font::UNDERLINE_DOUBLE)),
			'value' => 'You are Currently the Operator of Record of Registered Tanks at the Following Facilities'
		)), FALSE);
	$report->setBlankRow();
}

function facility_row(&$report, $row, $params) {
	$address = "{$row['FACILITY_NAME']}\n{$row['ADDRESS1']}"
		. ($row['ADDRESS2'] ? "\n{$row['ADDRESS2']}" : '')
		. "\n{$row['ADDRESS3']}";

	$report->setRow(array(
		"Fac ID: {$row['FACILITY_ID']}",
		array('colspan' => 2, 'value' => $address),
		3 => "Owner ID: {$row['OWNER_ID']}",
		array('colspan' => 2, 'value' => $row['OWNER_NAME'])
	), FALSE, array('font' => array('size' => 7)));

	$report->setRow(array("Tank Count: {$row['TANK_COUNT']}")
		, FALSE, array('font' => array('size' => 7)));

	$report->setBlankRow();
}

// local functions ----------------------------------------------

function label_cell($value) {
	return(array('value' => "{$value}: &nbsp;", 'style' => Report::$STYLE_RIGHT));
}
?>
