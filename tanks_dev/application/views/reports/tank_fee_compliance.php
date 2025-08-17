<?php
/**
 * Tank Fee Compliance
 * imported from Oracle Report, "Tank Fee Compliance"
 *
 * @package Onestop
 * @subpackage views
 * @uses Report.php
 *
*/

$db = Database::instance();
$facility = new Facilities_mvw_Model();

$report_sql = "SELECT fiscal_year, O.id owner_id, O.owner_name, T.operator_id, T.operator_name,
                sum(decode(TR.transaction_code,'PA',amount,0)) pa,
                sum(decode(TR.transaction_code, 'GWAA',amount,0)) gwaa,
                sum(decode(TR.transaction_code,'PW',amount,0)) pw,
                sum(decode(TR.transaction_code,'PP',amount,0)) pp,
                sum(decode(TR.transaction_code,'GWAP',amount,0)) gwap,
                sum(decode(TR.transaction_code,'IA',amount,0)) ia,
                sum(decode(TR.transaction_code,'IW',amount,0)) iw,
                sum(decode(TR.transaction_code,'IP',amount,0)) ip,
                sum(decode(TR.transaction_code,'LA',amount,0)) la,
                sum(decode(TR.transaction_code,'LW',amount,0)) lw,
                sum(decode(TR.transaction_code,'LP',amount,0)) lp,
                sum(decode(TR.transaction_code,'R',amount,0)) refund
        FROM ustx.transactions TR, ustx.owners_mvw O,
                (select distinct owner_id, operator_id, operator_name
                from ustx.tanks, ustx.operators_mvw operators
                where tanks.operator_id = operators.id and facility_id = :facility_id) T
        WHERE TR.owner_id (+) = O.id
                and (
			--2014-08-11: added this additional or statement for owners without TH.
			O.id in (select owner_id from ustx.facilities_mvw where id = :facility_id)
			or
			O.id in (select TH.owner_id from ustx.tank_history TH, ustx.tanks T
				where TH.tank_id = T.id and T.facility_id = :facility_id)
		)
                and TR.owner_id = T.owner_id (+)
        GROUP BY O.id, O.owner_name, fiscal_year, T.operator_id, T.operator_name
        ORDER BY owner_id, O.owner_name, fiscal_year desc";

$rs_arr = $db->query($report_sql, array(':facility_id' => $facility_id))->as_array();

$facility = $facility->get_row($facility_id);
$caf_report = new Report($output_format, 'Tank Fee Compliance', "for Facility: ({$facility_id}) {$facility['FACILITY_NAME']}");
$caf_report->getActiveSheet()->getPageSetup()->setFitToWidth(1);
$caf_report->getActiveSheet()->getPageSetup()->setFitToHeight(0);

if (count($rs_arr)) {
	$caf_report->setGroup($rs_arr, array(
		array('name' => 'OWNER_ID', 'header_func' => 'owner_header', 'footer_func' => 'owner_footer'),
		array('name' => 'FISCAL_YEAR', 'row_func' => 'fiscal_year_row')
	));
}
else {
	$caf_report->setRow(array('No Data'), FALSE, Report::$STYLE_HIGHLIGHT_YELLOW);
}

$caf_report->setColumnSize(array(10, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12));
$flag = $caf_report->output('tank_fee_compliance');


function owner_header(&$caf_report, $row) {
	// labels -----------------------------------------------
	$caf_report->setLabelRow(array(
		0 => array('value' => "Owner ID: {$row['OWNER_ID']}", 'colspan' => 3),
		3 => array('value' => "Owner Name: {$row['OWNER_NAME']}", 'colspan' => 3),
		6 => array('value' => "Operator ID: {$row['OPERATOR_ID']}", 'colspan' => 3),
		9 => array('value' => "Operator Name: {$row['OPERATOR_NAME']}", 'colspan' => 3),
		), array(
			'font' => array('bold' => true, 'color' => array('argb' => 'FF000000')),
			'fill' => array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('argb' => 'FFFFFFFF') )
		));
	$caf_report->setLabelRow(array(
		0 => array(),
		1 => array(
			'value' => "Principal Amounts",
			'colspan' => 3,
			'style' => array('fill' => array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('argb' => 'FF3355AA') ))
		),
		4 => array(
			'value' => "Interest Amounts",
			'colspan' => 3,
			'style' => array('fill' => array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('argb' => 'FF33AA55') ))
		),
		7 => array(
			'value' => "Late Fee Amounts",
			'colspan' => 3,
			'style' => array('fill' => array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('argb' => 'FFAA5533') ))
		),
		10 => array(), array() // blank label cells
	));
	$caf_report->setLabelRow( array('Fiscal Year', 'Assessed', 'Waiver', 'Paid', 'Assessed', 'Waiver', 'Paid', 'Assessed', 'Waiver', 'Paid', 'Refund', 'Year Total'), array('style' => array('alignment' => array('wrap' => TRUE))) );
}

function owner_footer(&$caf_report, $row, $params) {
	$caf_report->setRow(array(
		10 => array(
			'value' => "Owner Total:",
			'style' => array_merge(Report::$STYLE_TOTAL, array(
				'alignment' => array('wrap' => TRUE)
			))
		),
		array(
			'value' => "=SUM(L{$params['group_row_start']}:L{$params['group_row_end']})",
			'style' => array_merge(Report::$STYLE_MONEY, Report::$STYLE_TOTAL)
		)
	), FALSE);
	$caf_report->setBlankRow();
}
	
function fiscal_year_row(&$caf_report, $row) {
	$year_total = round(($row['PA'] - $row['PW'] - $row['PP']) + ($row['IA'] - $row['IW'] - $row['IP']) + ($row['LA'] - $row['LW'] - $row['LP']) + $row['REFUND'], 2);
	$caf_report->setRow(array(
		array('value' => $row['FISCAL_YEAR'], 'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER))),
		array('value' => $row['PA'], 'style' => Report::$STYLE_MONEY),
		array('value' => $row['PW'], 'style' => Report::$STYLE_MONEY),
		array('value' => $row['PP'], 'style' => Report::$STYLE_MONEY),
		array('value' => $row['IA'], 'style' => Report::$STYLE_MONEY),
		array('value' => $row['IW'], 'style' => Report::$STYLE_MONEY),
		array('value' => $row['IP'], 'style' => Report::$STYLE_MONEY),
		array('value' => $row['LA'], 'style' => Report::$STYLE_MONEY),
		array('value' => $row['LW'], 'style' => Report::$STYLE_MONEY),
		array('value' => $row['LP'], 'style' => Report::$STYLE_MONEY),
		array('value' => $row['REFUND'], 'style' => Report::$STYLE_MONEY),
		array('value' => $year_total, 'style' => Report::$STYLE_MONEY)
	));
}
?>
