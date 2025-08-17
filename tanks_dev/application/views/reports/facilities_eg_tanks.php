<?php
/**
 * Facilities with Emergency Generator Tanks
 *
 * @package Onestop
 * @subpackage views
 * @uses Report.php
 *
*/

$db = Database::instance();

$report_sql = "SELECT F.*, O.owner_name,
		(select count(*) from ustx.tanks T where (T.facility_id = F.id) and (T.tank_type = 'A') and (T.tank_status_code in (1, 2))) ast_count,
		(select count(*) from ustx.tanks T where (T.facility_id = F.id) and (T.tank_type = 'U') and (T.tank_status_code in (1, 2))) ust_count
	FROM ustx.facilities_mvw F
		INNER JOIN ustx.owners_mvw O ON F.owner_id = O.id
	WHERE F.id in (
		select T.facility_id from ustx.tanks T
			inner join ustx.tank_details TD ON T.id = TD.tank_id
		where T.facility_id = F.id
			and TD.tank_detail_code = 'U01'
			and T.tank_status_code in (1, 11) -- CURRENTLY IN USE, NO DATA
		group by T.facility_id)
	ORDER BY F.facility_name";

$rs_arr = $db->query($report_sql)->as_array();
if (count($rs_arr)) {
	$report = new Report($output_format, 'Facilities with Emergency Generator Tanks');
	$report->setRow( array(
		array('value' => 'Facilities with EG tanks in status: CURRENTLY IN USE, NO DATA', 'colspan' => 4, 'style' => Report::$STYLE_NOTE),
	), FALSE );

	// labels -----------------------------------------------
	$report->setLabelRow( array('FID', 'Facility', 'Street', 'Street 2', 'City', 'State', 'Zip', 'OID', 'Owner Name', 'AST', 'UST'),
		array('style' => array('alignment' => array('wrap' => TRUE))) );
	$report->getActiveSheet()->getRowDimension($report->row_num - 1)->setRowHeight(25);
	$report->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($report->row_num-1, $report->row_num-1);
	
	// main body --------------------------------------------
	$group_row_start = $report->row_num;
	foreach ($rs_arr as $row) {
		$report->setRow(array(
			array('value' => $row['ID'], 'style' => Report::$STYLE_CENTER),
			array('value' => $row['FACILITY_NAME'], 'style' => array('alignment' => array('wrap' => TRUE))),
			array('value' => $row['ADDRESS1']),
			array('value' => $row['ADDRESS2']),
			array('value' => $row['CITY']),
			array('value' => $row['STATE']),
			array('value' => $row['ZIP']),
			array('value' => $row['OWNER_ID'], 'style' => Report::$STYLE_CENTER),
			array('value' => $row['OWNER_NAME'], 'style' => array('alignment' => array('wrap' => TRUE))),
			array('value' => $row['AST_COUNT']),
			array('value' => $row['UST_COUNT'])
		));
		$report->getActiveSheet()->getRowDimension($report->row_num - 1)->setRowHeight(25);
	}
	
	$report->setColumnSize(array(8, 35, 30, 20, 15, 8, 8, 8, 35, 5, 5));
	$flag = $report->output('facilities_eg_tanks');
}
?>
