<?php
/**
 * All Storage Tanks Status
 *
 * @package Onestop
 * @subpackage views
 * @uses Report.php
 *
*/

$db = Database::instance();

if (!strtotime($start_date) || !strtotime($end_date))
	exit('Dates are not in valid format.');

$tank_types[] = 'B';  // add Both
$tank_type_list = implode( ',', array_map(function($val) { return("'{$val}'");}, $tank_types) );

$report_sql = "
	SELECT F.id facility_id, F.facility_name, O.id owner_id, O.owner_name, I.date_inspected, IC.description inspection_type, P.penalty_code, PC.description penalty_description, PC.soc_category
	FROM ustx.penalties P, ustx.penalty_codes PC, ustx.inspections I, ustx.facilities_mvw F, ustx.owners_mvw O, ustx.inspection_codes IC
	WHERE
		P.penalty_code = PC.code
		AND P.inspection_id = I.ID
		AND I.facility_id = F.ID
		AND F.owner_id = O.id
		AND I.inspection_code = IC.code
		AND PC.tank_type IN ($tank_type_list)
		AND date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
		-- Date Corrected is inserted as '01-JAN-68' instead of NULL when entering empty input due to UDAPI issue add additional condition here 
		AND (P.date_corrected is null OR P.date_corrected = '01-JAN-68')
	ORDER BY O.owner_name, F.facility_name";
$rs_arr = $db->query($report_sql, array(':start_date' => $start_date, ':end_date' => $end_date))->as_array();

if (in_array('A', $tank_types)) $tank_type_names[] = 'AST';
if (in_array('U', $tank_types)) $tank_type_names[] = 'UST';
$caf_report = new Report($output_format, 'NOV Report - All Violations', "Dates: {$start_date} to {$end_date}\nTank Types: " . implode(', ', $tank_type_names));
$caf_report->setRow( array( array('value' => 'AST/UST count is total number of tanks that exists for that facility whether in violation or not.', 'colspan' => 6, 'style' => Report::$STYLE_NOTE) ), FALSE );

// labels -----------------------------------------------
$caf_report->setLabelRow( array('NOV Code', 'NOV Description', 'SOC Category', 'Date Inspected', 'Inspection Type', 'Owner ID', 'Owner Name', 'Facility ID', 'Facility Name', 'AST', 'UST'), array('style' => array('alignment' => array('wrap' => TRUE))) );
$caf_report->getActiveSheet()->getRowDimension($caf_report->row_num - 1)->setRowHeight(25);
$caf_report->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($caf_report->row_num-1, $caf_report->row_num-1);

if (count($rs_arr)) {
	// main body --------------------------------------------
	$group_row_start = $caf_report->row_num;
	foreach ($rs_arr as $row) {
		$ast_count = get_tank_count($row['FACILITY_ID'], 'A');
		$ust_count = get_tank_count($row['FACILITY_ID'], 'U');
		$caf_report->setRow(array(
			array('value' => $row['PENALTY_CODE'], 'style' => Report::$STYLE_TEXT),
			array('value' => $row['PENALTY_DESCRIPTION']),
			array('value' => $row['SOC_CATEGORY']),
			array('value' => Report::TO_DATE($row['DATE_INSPECTED']), 'style' => Report::$STYLE_DATE),
			array('value' => $row['INSPECTION_TYPE']),
			array('value' => $row['OWNER_ID'], 'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER))),
			array('value' => $row['OWNER_NAME']),
			array('value' => $row['FACILITY_ID'], 'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER))),
			array('value' => $row['FACILITY_NAME']),
			array('value' => $ast_count),
			array('value' => $ust_count)
		));
	}

	// summary -----------------------------------------------
	$group_row_end = ($caf_report->row_num > $group_row_start) ? ($caf_report->row_num - 1) : $group_row_start;
	$caf_report->setBlankRow();
	$caf_report->setRow(array(
		array('value' => 'NOV Count:'),
		array( 'value' => "=COUNT(H{$group_row_start}:H{$group_row_end})" ), //A doesnt work
		array( 'colspan' => 6 ),
		8 => array('value' => 'Tanks Total:'),
		array( 'value' => "=SUM(J{$group_row_start}:J{$group_row_end})" ),
		array( 'value' => "=SUM(K{$group_row_start}:K{$group_row_end})" )
	), FALSE, Report::$STYLE_TOTAL);
}

$caf_report->setColumnSize(array(10, 35, 8, 12, 12, 10, 30, 10, 30, 8, 8));
$flag = $caf_report->output('nov_report_all');


function get_tank_count($facility_id, $tank_type) {
	$db = Database::instance();
	
	return($db->query_field("
		SELECT Count(TANK_TYPE)
		FROM USTX.TANKS
		WHERE (FACILITY_ID=:FACILITY_ID)
			AND (TANK_TYPE=:TANK_TYPE)
			AND (TANK_STATUS_CODE in (1, 2, 11, 12))"
		, array(':FACILITY_ID' => $facility_id, ':TANK_TYPE' => $tank_type)));
}
?>
