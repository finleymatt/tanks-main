<?php
/**
 * Inspections Review Report
 *
 * @package Onestop
 * @subpackage views
 * @uses Report.php
 *
*/

$db = Database::instance();
$staff = new Staff_Model;
$selected_inspector = ($inspector_id ? $staff->get_name($inspector_id) : 'All');

if (!strtotime($start_date) || !strtotime($end_date))
	exit('Date is not in valid format.');

$inspector_sql = (!empty($inspector_id) ? ':inspector_id' : 'I.staff_code');
$tank_type_list = implode( ',', array_map(function($val) { return("'{$val}'");}, $tank_types) );
$report_sql = "
	SELECT F.*, I.ID INSPECTION_ID, I.date_inspected, I.staff_code, I.case_id, I.nov_number, IC.description INSPECTION_TYPE,
		S.first_name || ' ' || S.last_name inspector,
		assigned_inspector.full_name assigned_inspector,
		(select count(*) from ustx.tanks T where (T.facility_id = F.id) and (T.TANK_TYPE = 'A') ) AST,
		(select count(*) from ustx.tanks T where (T.facility_id = F.id) and (T.TANK_TYPE = 'U') ) UST
	FROM ustx.facilities_mvw F
		INNER JOIN ustx.inspections I ON F.id = I.facility_id
		INNER JOIN ustx.inspection_codes IC ON I.inspection_code = IC.code
		LEFT OUTER JOIN ( SELECT E.entity_id, s2.first_name || ' ' || s2.last_name full_name
			FROM ustx.staff S2,
				ustx.entity_details E
			WHERE E.entity_type = 'facility' and
				E.detail_type = 'assigned_inspector' AND
				E.Detail_Value = S2.sep_login_id ) assigned_inspector ON assigned_inspector.entity_id = F.id
		LEFT OUTER JOIN ustx.staff S on I.staff_code = S.code
	WHERE
		(select count(*) from ustx.tanks T where (T.facility_id = F.id) and (T.TANK_TYPE in ({$tank_type_list})) ) > 0
		and I.date_inspected >= TO_DATE(:start_date, 'mm/dd/yyyy')
		and I.date_inspected <= TO_DATE(:end_date, 'mm/dd/yyyy')
		and I.staff_code = {$inspector_sql}
	ORDER BY I.date_inspected ASC";


$bound_vars = array(':start_date' => $start_date, ':end_date' => $end_date);
if (!empty($inspector_id)) $bound_vars[':inspector_id'] = $inspector_id;
$rs_arr = $db->query($report_sql, $bound_vars)->as_array();

$report = new Report($output_format, 'Inspections Review Report', "Inspections performed during {$start_date} to {$end_date}\nInspector: {$selected_inspector}\nTanks: {$tank_type_list}");

// labels -----------------------------------------------
$report->setLabelRow( array('Facility ID', 'Facility Name', 'Street', 'City', 'State', 'Zip', 'AST Count', 'UST Count', 'Inspection ID', 'Case ID', 'LCC', 'Inspector', 'Assigned Inspector', 'Inspection Type', 'Inspection Date'), array('style' => array('alignment' => array('wrap' => TRUE))) );
$report->getActiveSheet()->getRowDimension($report->row_num - 1)->setRowHeight(25);
$report->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($report->row_num-1, $report->row_num-1);
for($col = 'A'; $col !== 'O'; $col++) {
	$report->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
}
// make column NOV # displayed as long number instead of scientific format// set column
$report->getActiveSheet()->getStyle('J')->getNumberFormat()->setFormatCode('0');
$report->getActiveSheet()->getStyle('K')->getNumberFormat()->setFormatCode('0');
if (count($rs_arr)) {
	// main body --------------------------------------------
	$group_row_start = $report->row_num;
	foreach ($rs_arr as $row) {
		$report->setRow(array(
			array('value' => $row['ID'], 'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER))),
			array('value' => $row['FACILITY_NAME']),
			array('value' => implode(' ', array($row['ADDRESS1'], $row['ADDRESS2']))),
			array('value' => $row['CITY']),
			array('value' => $row['STATE']),
			array('value' => $row['ZIP']),
			array('value' => $row['AST']),
			array('value' => $row['UST']),
			array('value' => $row['INSPECTION_ID']),
			array('value' => $row['CASE_ID']),
			array('value' => $row['NOV_NUMBER']),
			array('value' => $row['INSPECTOR']),
			array('value' => $row['ASSIGNED_INSPECTOR']),
			array('value' => $row['INSPECTION_TYPE']),
			array('value' => Report::TO_DATE($row['DATE_INSPECTED']), 'style' => Report::$STYLE_DATE)
		));
	}
	
	// summary -----------------------------------------------
	$group_row_end = $report->row_num - 1;
	$report->setRow(array(
		array(
			'style' => Report::$STYLE_RIGHT,
			'colspan' => 14,
			'value' => 'Total Inspections Performed:'
		),
		12 => array(
			'value' => "=COUNT(A{$group_row_start}:A{$group_row_end})"
		)
	), FALSE, Report::$STYLE_TOTAL);
}
	
$report->setColumnSize(array(8, 35, 35, 20, 6, 8, 8, 8, 8, 8, 16, 16, 15, 13));
$flag = $report->output('inspections_review');
?>
