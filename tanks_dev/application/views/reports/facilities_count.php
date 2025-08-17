<?php
/**
 * Facilities Count Report
 * Created on 11/21/2013 from Dana's request.
 *
 * @package Onestop
 * @subpackage views
 * @uses Report.php
 *
*/

$db = Database::instance();

$report_sql = sql($tank_types, $tos_only);

$rs_arr = $db->query($report_sql)->as_array();

$report = new Report($output_format, 'Facilities Count Report',
	"Tank Types: ". implode(', ', $tank_types) ."\nTOS only: ". ($tos_only ? 'Yes' : 'No'));

if (count($rs_arr)) {
	// labels -----------------------------------------------
	$report->setLabelRow( array('F ID', 'Facility Name', 'Adress 1', 'Address 2', 'City', 'State', 'Zip'), array('style' => array('alignment' => array('wrap' => TRUE))) );
	//$report->getActiveSheet()->getRowDimension($report->row_num - 1)->setRowHeight(25);
	$report->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($report->row_num-1, $report->row_num-1);

	// main body --------------------------------------------
	$group_row_start = $report->row_num;
	foreach ($rs_arr as $row) {
		$report->setRow(array(
			$row['ID'],
			$row['FACILITY_NAME'],
			$row['ADDRESS1'],
			$row['ADDRESS2'],
			$row['CITY'],
			$row['STATE'],
			$row['ZIP']
		));
	}
	
	// summary -----------------------------------------------
	$group_row_end = $report->row_num - 1;
	$report->setRow(array(
		array(
			'value' => 'Total Count:',
			'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)),
			'colspan' => 6,
		),
		6 => array(
			'value' => "=COUNT(A{$group_row_start}:A{$group_row_end})"
		)
	), FALSE, Report::$STYLE_TOTAL);
}

$report->setColumnSize(array(6, 35, 30, 20, 27, 6, 10));
$flag = $report->output('facilities_count');


function sql($tank_types, $tos_only) {
	$tank_types_sql = "'" . implode("','", $tank_types) . "'";
	$status_codes_sql = ($tos_only ? '2' : '1, 2, 11, 12');
	return("
		SELECT F.*
		FROM USTX.facilities_mvw F
		WHERE
			F.id in (select distinct T.facility_id from ustx.tanks T where (T.tank_type in ({$tank_types_sql})) and (T.tank_status_code in ({$status_codes_sql})))
	");
}
?>
