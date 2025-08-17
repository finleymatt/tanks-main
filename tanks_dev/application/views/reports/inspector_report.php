<?php
/**
 *
 * Delivery Prohibition inspectors Report
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

$report_sql = "
	SELECT F.ID FACILITY_ID, F.FACILITY_NAME, I.DATE_INSPECTED, I.STAFF_CODE, S.first_name || ' ' || S.last_name inspector, I.NOV_NUMBER, P.DATE_CORRECTED,
		P.NOV_DATE, P.NOD_DATE,	P.NOIRT_DATE, P.PENALTY_CODE, P.PENALTY_OCCURANCE, P.TANK_ID, P.REDTAG_PLACED_DATE		
	FROM ustx.facilities_mvw F
		INNER JOIN ustx.inspections I on F.ID = I.FACILITY_ID
		INNER JOIN ustx.penalties P on I.ID = P.INSPECTION_ID
		INNER JOIN ustx.staff S on I.staff_code = S.code
	WHERE
		I.DATE_INSPECTED >= TO_DATE(:start_date, 'mm/dd/yyyy')
		AND I.DATE_INSPECTED <= TO_DATE(:end_date, 'mm/dd/yyyy')
		and I.staff_code = {$inspector_sql}
	ORDER BY I.date_inspected ASC";

$bound_vars = array(':start_date' => $start_date, ':end_date' => $end_date);
if (!empty($inspector_id)) $bound_vars[':inspector_id'] = $inspector_id;
$rs_arr = $db->query($report_sql, $bound_vars)->as_array();

$report = new Report($output_format, 'Delivery Prohibition Report for inspectors', "Inspections performed during {$start_date} to {$end_date}\nInspector: {$selected_inspector}");

// labels -----------------------------------------------
$report->setLabelRow( array('Facility ID', 'Facility Name', 'Inspection Date', 'NOV #', 'TANK ID', 'PENATY CODE', 'OCCURRENCE #', 'Date Corrected', 'NOV Date', 'NOD DATE', 'NOIRT Date', 'Red Tag Placed Date', 'Red Tag Removal Date'), array('style' => array('alignment' => array('wrap' => TRUE))) );
$report->getActiveSheet()->getRowDimension($report->row_num - 1)->setRowHeight(25);
$report->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($report->row_num-1, $report->row_num-1);
for($col = 'A'; $col !== 'N'; $col++) {
    $report->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
}
// make column NOV # displayed as long number instead of scientific format
$report->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode('0');
if (count($rs_arr)) {
	// main body --------------------------------------------
	$group_row_start = $report->row_num;
	foreach ($rs_arr as $row) {
		$report->setRow(array(
			array('value' => $row['FACILITY_ID'], 'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER))),
			array('value' => $row['FACILITY_NAME']),
			array('value' => Report::TO_DATE($row['DATE_INSPECTED']), 'style' => Report::$STYLE_DATE),
			array('value' => $row['NOV_NUMBER']),
			array('value' => $row['TANK_ID']),
			array('value' => $row['PENALTY_CODE'], 'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT))),
			array('value' => $row['PENALTY_OCCURANCE']),
			array('value' => Report::TO_DATE($row['DATE_CORRECTED']), 'style' => Report::$STYLE_DATE),
			array('value' => Report::TO_DATE($row['NOV_DATE']), 'style' => Report::$STYLE_DATE),
			array('value' => Report::TO_DATE($row['NOD_DATE']), 'style' => Report::$STYLE_DATE),
			array('value' => Report::TO_DATE($row['NOIRT_DATE']), 'style' => Report::$STYLE_DATE),
			array('value' => Report::TO_DATE($row['REDTAG_PLACED_DATE']), 'style' => Report::$STYLE_DATE),
			array('value' => ''),
		));
	}
}

$flag = $report->output('violations_per_inspector');
?>
