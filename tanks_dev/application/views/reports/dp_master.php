<?php
/**
 *
 * Delivery Prohibition Master Report
 *
 * @package Onestop
 * @subpackage views
 * @uses Report.php
 *
*/

$db = Database::instance();

if (!strtotime($start_date) || !strtotime($end_date))
	exit('Date is not in valid format.');

$report_sql = "
	SELECT F.ID FACILITY_ID, F.FACILITY_NAME, I.DATE_INSPECTED, I.NOV_NUMBER, P.DATE_CORRECTED, P.NOV_DATE, P.NOD_DATE, P.NOIRT_DATE, P.REDTAG_PLACED_DATE,
		PC.DP_CATEGORY, PC.SOC_CATEGORY, PC.PENALTY_LEVEL VIOLATION_CLASS,
		(select count(*) from ustx.tanks T where (T.id = P.tank_id) and (T.TANK_TYPE = 'A') and (t.tank_status_code in (1, 2))) AST,
		(select count(*) from ustx.tanks T where (T.id = P.tank_id) and (T.TANK_TYPE = 'U') and (t.tank_status_code in (1, 2))) UST
	FROM ustx.facilities_mvw F
		INNER JOIN ustx.inspections I on F.ID = I.FACILITY_ID
		INNER JOIN ustx.penalties P on I.ID = P.INSPECTION_ID
		INNER JOIN ustx.penalty_codes PC on P.PENALTY_CODE = PC.CODE
	WHERE
		PC.PENALTY_LEVEL IN ('A','B')
		AND PC.SOC_CATEGORY IN ('RP', 'RD')
		AND I.DATE_INSPECTED >= TO_DATE(:start_date, 'mm/dd/yyyy')
		AND I.DATE_INSPECTED <= TO_DATE(:end_date, 'mm/dd/yyyy')";

$bound_vars = array(':start_date' => $start_date, ':end_date' => $end_date);
$rs_arr = $db->query($report_sql, $bound_vars)->as_array();

$report = new Report($output_format, 'Delivery Prohibition Master Report', "Inspections performed during {$start_date} to {$end_date}");

// labels -----------------------------------------------
$report->setLabelRow( array('Facility ID', 'Facility Name', 'Inspection Date', 'NOV #', 'AST Count', 'UST Count', 'Class of Violation', 'SOC Category', 'DP Category', 'COC Date', 'NOV Date', 'NOD DATE', 'NOIRT Date', 'Red Tag Placed Date', 'Red Tag Removal Date'), array('style' => array('alignment' => array('wrap' => TRUE))) );
$report->getActiveSheet()->getRowDimension($report->row_num - 1)->setRowHeight(25);
$report->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($report->row_num-1, $report->row_num-1);
// set cell size automatically
for($col = 'A'; $col !== 'P'; $col++) {
	$report->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
}
// make column NOV # displayed as long number instead of scientific format 
$report->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode('0');

if (count($rs_arr)) {
	// main body --------------------------------------------
	$group_row_start = $report->row_num;

	foreach ($rs_arr as $row) {
		// empty value doesn't work with UDAPI, so use '01-JAN-68' instead in DB
		$date_corrected = ($row['DATE_CORRECTED'] == '01-JAN-68') ? Null : $row['DATE_CORRECTED'];
		$nov_date = ($row['NOV_DATE'] == '01-JAN-68') ? Null : $row['NOV_DATE'];
		$nod_date = ($row['NOD_DATE'] == '01-JAN-68') ? Null : $row['NOD_DATE'];
		$noirt_date = ($row['NOIRT_DATE'] == '01-JAN-68') ? Null : $row['NOIRT_DATE'];
		$redtag_placed_date = ($row['REDTAG_PLACED_DATE'] == '01-JAN-68') ? Null : $row['REDTAG_PLACED_DATE'];
		$report->setRow(array(
			array('value' => $row['FACILITY_ID'], 'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER))),
			array('value' => $row['FACILITY_NAME']),
			array('value' => Report::TO_DATE($row['DATE_INSPECTED']), 'style' => Report::$STYLE_DATE),
			array('value' => $row['NOV_NUMBER']),
			array('value' => $row['AST']),
			array('value' => $row['UST']),
			array('value' => $row['VIOLATION_CLASS']),
			array('value' => $row['SOC_CATEGORY']),
			array('value' => $row['DP_CATEGORY']),
			array('value' => Report::TO_DATE($date_corrected), 'style' => Report::$STYLE_DATE),
			array('value' => Report::TO_DATE($nov_date), 'style' => Report::$STYLE_DATE),
			array('value' => Report::TO_DATE($nod_date), 'style' => Report::$STYLE_DATE),
			array('value' => Report::TO_DATE($noirt_date), 'style' => Report::$STYLE_DATE),
			array('value' => Report::TO_DATE($redtag_placed_date), 'style' => Report::$STYLE_DATE),
			array('value' => ''),
		));
	}
}

$flag = $report->output('DP_Master');
?>
