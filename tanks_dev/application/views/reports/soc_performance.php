<?php
/**
 * SOC Performance Report
 *
 * @package Onestop
 * @subpackage views
 * @uses Report.php
 *
*/

$db = Database::instance();

if (!strtotime($start_date) || !strtotime($end_date))
	exit('Dates are not in valid format.');

$report_sql = "SELECT '1' main_group, F.id facility_id, F.facility_name, O.id owner_id, O.owner_name, I.id inspection_id, I.date_inspected, IC.description inspection_type, P.penalty_code, PC.description penalty_description, PC.soc_category, (S.first_name || ' ' || S.last_name) inspector
	FROM ustx.penalties P, ustx.penalty_codes PC, ustx.inspections I, ustx.facilities_mvw F, ustx.owners_mvw O, ustx.inspection_codes IC, ustx.staff S
	WHERE
		P.penalty_code = PC.code
		AND P.inspection_id = I.id
		AND I.facility_id = F.id
		AND F.owner_id = O.id
		AND I.inspection_code = IC.code
		AND I.staff_code = S.code
		AND I.inspection_code = 1
		AND PC.tank_type = 'U'
		AND PC.end_date is null
		AND PC.soc_category IN ('RP', 'RD')
		AND PC.is_soc = 'T'
		AND date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
		-- somewhat redundant check:
		AND (select count(*) from ustx.tanks T where T.facility_id = I.facility_id and T.tank_status_code in (1, 11, 12) and T.tank_type = 'U') > 0
	ORDER BY main_group, F.id, I.id";

$rs_arr = $db->query($report_sql, array(':start_date' => $start_date, ':end_date' => $end_date))->as_array();

$caf_report = new Report($output_format, 'SOC Performance', "Dates: {$start_date} to {$end_date}");

if (count($rs_arr)) {
	global $total_inspection; // workaround to make this value accessible from functions
	$total_inspection = all_inspection_count($start_date, $end_date);

	$caf_report->setRow( array(
		array('value' => '(RD: Release Detection Compliance measures)', 'colspan' => 5, 'style' => Report::$STYLE_NOTE)
	), FALSE );
	$caf_report->setRow( array(
		array('value' => '(RP: Release Prevention Compliance measures)', 'colspan' => 5, 'style' => Report::$STYLE_NOTE)
	), FALSE );

	$caf_report->setGroup($rs_arr, array(
		array('name' => 'MAIN_GROUP', 'footer_func' => 'main_footer'),
		array('name' => 'FACILITY_ID', 'header_func' => 'facility_header', 'footer_func' => 'facility_footer'),
		array('name' => 'INSPECTION_ID', 'header_func' => 'inspection_header', 'row_func' => 'inspection_row')
	));
}

$caf_report->setColumnSize(array(10, 25, 25, 12, 12, 10, 30, 10, 15, 6, 6));
$flag = $caf_report->output('soc_performance');


function main_footer(&$report, $row, $params) {
	global $total_inspection;

	$report->setLabelRow( array('Total Inspected', 'RD Compliance Only', 'RP Compliance Only', 'Both Compliance') );
	$report->getActiveSheet()->getRowDimension($report->row_num - 1)->setRowHeight(25);

	$report->setRow( array(
		$total_inspection,
		"=A{$report->row_num} - COUNTIF(J{$params['group_row_start']}:J{$params['group_row_end']}, \"=RD\")",
		"=A{$report->row_num} - COUNTIF(K{$params['group_row_start']}:K{$params['group_row_end']}, \"=RP\")",
		"=A{$report->row_num} - COUNTIF(A{$params['group_row_start']}:A{$params['group_row_end']}, \"=Facility ID\")",
	), FALSE);

	$prev_row = $report->row_num - 1;
	$report->setRow( array(
		1 => array('value' => "=B{$prev_row} / A{$prev_row}",
			'style' => Report::$STYLE_PERCENT),
		array('value' => "=C{$prev_row} / A{$prev_row}",
			'style' => Report::$STYLE_PERCENT),
		array('value' => "=D{$prev_row} / A{$prev_row}",
			'style' => Report::$STYLE_PERCENT)
	), FALSE);
}

function facility_header(&$report, $row) {
	$report->setLabelRow( array('Facility ID', 'Facility Name', 'Owner', array('value'=>'', 'colspan'=>8)) );
	$report->setRow(array(
		$row['FACILITY_ID'],
		$row['FACILITY_NAME'],
		$row['OWNER_NAME']
	), FALSE);
}

function facility_footer(&$report, $row, $params) {
	$report->setRow( array(
		array('value' => 'Failed Inspections:', 'colspan' => 9, 
			'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT))),
		9 => "=IF(COUNTIF(H{$params['group_row_start']}:H{$params['group_row_end']}, \"RD\"), \"RD\", \"\")",
		"=IF(COUNTIF(H{$params['group_row_start']}:H{$params['group_row_end']}, \"RP\"), \"RP\", \"\")"
		// display BOTH, RD, or RP - using nested IF - created it for nothing
		//9 => "=IF(AND(COUNTIF(H{$params['group_row_start']}:H{$params['group_row_end']},\"=RD\"),COUNTIF(H{$params['group_row_start']}:H{$params['group_row_end']},\"=RP\")),\"BOTH\",IF(COUNTIF(H{$params['group_row_start']}:H{$params['group_row_end']},\"=RD\"),\"RD\",IF(COUNTIF(H{$params['group_row_start']}:H{$params['group_row_end']},\"=RP\"),\"RP\",\"\")))"
	), FALSE, Report::$STYLE_TOTAL );

	$report->setBlankRow();
}

function inspection_header(&$report, $row) {
	$report->setLabelRow( array(3 => 'Inspection Date', 'Inspection Type', 'NOV Code', 'NOV Description', 'SOC Category', 'Inspector'), array('fill' => array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array('argb' => 'FF3355AA'))
		) );
}

function inspection_row(&$report, $row) {
	$report->setRow(array(
		3 => array('value' => Report::TO_DATE($row['DATE_INSPECTED']), 'style' => Report::$STYLE_DATE),
		array('value' => $row['INSPECTION_TYPE']),
		array('value' => $row['PENALTY_CODE']),
		array('value' => $row['PENALTY_DESCRIPTION']),
		array('value' => $row['SOC_CATEGORY']),
		array('value' => $row['INSPECTOR'])
	), FALSE);
}

function all_inspection_count($start_date, $end_date) {
	$db = Database::instance();
	
	return($db->query_field("
		SELECT Count(*)
		FROM USTX.INSPECTIONS I
		WHERE
			I.inspection_code = 1
			AND ( I.DATE_INSPECTED Between TO_DATE(:start_date, 'mm/dd/yyyy')
				and TO_DATE(:end_date, 'mm/dd/yyyy') )
			AND (select count(*) from USTX.TANKS T where T.facility_id = I.facility_id and T.tank_status_code in (1, 11) and T.tank_type = 'U') > 0
		"
		, array(':start_date' => $start_date, ':end_date' => $end_date)));
}
?>
