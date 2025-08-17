<?php
/**
 * Facility and Tank detail report
 *
 * @package Onestop
 * @subpackage views
 * @uses Report.php
 *
*/
if (!$tank_detail_codes) {
	exit('Required fields not entered.');
} else {
	$code_count = count($tank_detail_codes);
}
$tank_detail_codes_str = "";
// convert array to a string separated by comma
foreach($tank_detail_codes as $key => $tank_detail_code) {
	if($key > 0) {
		$tank_detail_codes_str = $tank_detail_codes_str . ', ';
	}
	$tank_detail_codes_str = $tank_detail_codes_str . "'" . $tank_detail_code . "'";
}

ini_set('max_execution_time', 240); // 4 minutes
ini_set('memory_limit', '1024M');
$db = Database::instance();

$report_sql = "SELECT T.ID TANK_ID, T.FACILITY_ID, TSC.DESCRIPTION TANK_STATUS, T.TANK_TYPE, F.FACILITY_NAME, F.CITY CITY, C.COUNTY,
		TO_CHAR(TI.HISTORY_DATE, 'MM/DD/YYYY') DATE_INSTALLED, TO_CHAR(TR.HISTORY_DATE, 'MM/DD/YYYY') DATE_REMOVED, T.OWNER_ID, O.OWNER_NAME
		FROM USTX.TANKS T
		LEFT JOIN USTX.FACILITIES_MVW F ON F.ID = T.FACILITY_ID
		LEFT JOIN USTX.OWNERS_MVW O ON O.ID = T.OWNER_ID
		LEFT JOIN USTX.CITIES C ON C.CITY = F.CITY
		JOIN USTX.TANK_STATUS_CODES TSC ON T.TANK_STATUS_CODE = TSC.CODE
		JOIN 
			-- find the tank IDs with all the tank detail codes selected by user
			(SELECT tank_id
			FROM ustx.tank_details
			WHERE tank_detail_code IN ({$tank_detail_codes_str})
			GROUP BY tank_id
			HAVING COUNT(DISTINCT tank_detail_code) = {$code_count}) TD1 
			ON T.ID = TD1.TANK_ID
		LEFT JOIN (SELECT TANK_ID, HISTORY_DATE, HISTORY_CODE FROM USTX.TANK_HISTORY WHERE HISTORY_CODE = 'I') TI ON TI.TANK_ID = T.ID
		LEFT JOIN (SELECT TANK_ID, HISTORY_DATE, HISTORY_CODE FROM USTX.TANK_HISTORY WHERE HISTORY_CODE = 'R') TR ON TR.TANK_ID = T.ID
		WHERE T.TANK_STATUS_CODE IN (1, 2)
		ORDER BY FACILITY_ID, TANK_ID";

$rs_arr = $db->query($report_sql)->as_array();

if(count($rs_arr)) {
	$report = new Report($output_format, 'Facility and Tank Detail');
	$report->getActiveSheet()->getPageSetup()->setFitToWidth(1);
	$report->getActiveSheet()->getPageSetup()->setFitToHeight(0);

	$label_arr = array('Tank ID', 'Tank Detail Code', 'Facility ID', 'Facility Name', 'City', 'County', 'Tank Status', 'Tank Type', 'Date Installed', 'Date Removed', 'Owner ID', 'Owner Name');
	foreach($tank_detail_codes as $key => $tank_detail_code) {
		if($key > 0) {
			$tank_detail_code_count = $key + 1;
			// add column labels based on the number of tank detail codes input
			array_splice($label_arr, $tank_detail_code_count, 0, 'Tank Detail Code ' . $tank_detail_code_count); 
		}
	}

	$report->setLabelRow($label_arr, array('style'=> array('alignment' => array('wrap' => TRUE))) );

	$report->getActiveSheet()->getRowDimension($report->row_num - 1)->setRowHeight(25);
	$report->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($report->row_num-1, $report->row_num-1);

	foreach ($rs_arr as $row) {
		$cols = array(
			array('value' => $row['TANK_ID']),
			array('value' => $tank_detail_codes[0]),
			array('value' => $row['FACILITY_ID']),
			array('value' => $row['FACILITY_NAME']),
			array('value' => $row['CITY']),
			array('value' => $row['COUNTY']),
			array('value' => $row['TANK_STATUS']),
			array('value' => $row['TANK_TYPE']),
			array('value' => $row['DATE_INSTALLED']),
			array('value' => $row['DATE_REMOVED']),
			array('value' => $row['OWNER_ID']),
			array('value' => $row['OWNER_NAME'])
		);

		foreach($tank_detail_codes as $key => $tank_detail_code) {
			if($key > 0) {
				$tank_detail_code_count = $key + 1;
				// insert tank detail codes if user select multiple
				array_splice($cols, $tank_detail_code_count, 0, $tank_detail_code);
			}
		}
		$report->setRow($cols);
	}
	$column_size = array(12, 18, 12, 45, 15, 15, 25, 12, 15, 15, 12, 45);
	foreach($tank_detail_codes as $key => $tank_detail_code) {
		if($key > 0) {
			$tank_detail_code_count = $key + 1;
			// set the width of tank detail code column to 18
			array_splice($column_size, $tank_detail_code_count, 0, 18);
		}
	}
	$report->setColumnSize($column_size);
	$flag = $report->output('facility_and_tank_detail');
}
