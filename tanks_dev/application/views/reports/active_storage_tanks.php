<?php
/**
 * Facilities with Active Tanks by County
 * imported from Oracle Report, "Facilities with Active Tanks by County"
 *
 * @package Onestop
 * @subpackage views
 * @uses Report.php
 *
*/

$db = Database::instance();

$report_sql = "
SELECT 
	O.id owner_id, O.owner_name, F.id FID, F.facility_name, F.address1, F.address2, F.city, F.zip, nvl(CT.county, 'UNKNOWN COUNTY') county,
	(select count(*) from ustx.tanks T where (T.facility_id = F.id) and (T.tank_type = 'A') and (T.tank_status_code in (1, 2))) ast_count,
	(select count(*) from ustx.tanks T where (T.facility_id = F.id) and (T.tank_type = 'U') and (T.tank_status_code in (1, 2))) ust_count
FROM USTX.facilities_mvw F
	INNER JOIN USTX.owners_mvw O ON F.owner_id = O.id
	LEFT OUTER JOIN USTX.cities CT ON F.city = CT.city
WHERE
	-- in use, temp out of use
	(select count(*) from ustx.tanks T where (T.facility_id = F.id) and (T.tank_type in ('A', 'U')) and (T.tank_status_code in (1, 2))) > 0
ORDER BY F.ID";

$rs_arr = $db->query($report_sql)->as_array();
if (count($rs_arr)) {
	$caf_report = new Report($output_format, 'Facilities with Active Tanks by County');
	$caf_report->setRow( array(
		array('value' => 'Facilities with tanks in status: CURRENTLY IN USE, TEMPORARILY OUT OF USE', 'colspan' => 4, 'style' => Report::$STYLE_NOTE),
	), FALSE );
	// labels -----------------------------------------------
	$caf_report->setLabelRow( array('FID', 'Facility', 'Street', 'Street 2', 'City', 'County', 'Zip', 'AST', 'UST', 'Owner ID', 'Owner Name'), array('style' => array('alignment' => array('wrap' => TRUE))) );
	$caf_report->getActiveSheet()->getRowDimension($caf_report->row_num - 1)->setRowHeight(25);
	$caf_report->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($caf_report->row_num-1, $caf_report->row_num-1);
	
	// main body --------------------------------------------
	$group_row_start = $caf_report->row_num;
	foreach ($rs_arr as $row) {
		$caf_report->setRow(array(
			array('value' => $row['FID']),
			array('value' => $row['FACILITY_NAME']),
			array('value' => $row['ADDRESS1']),
			array('value' => $row['ADDRESS2']),
			array('value' => $row['CITY']),
			array('value' => $row['COUNTY']),
			array('value' => $row['ZIP']),
			array('value' => $row['AST_COUNT']),
			array('value' => $row['UST_COUNT']),
			array('value' => $row['OWNER_ID']),
			array('value' => $row['OWNER_NAME'], 'style' => array('alignment' => array('wrap' => TRUE)))
		));
	}
	
	$caf_report->setColumnSize(array(8, 35, 25, 25, 15, 15, 8, 6, 6, 8, 40));
	$flag = $caf_report->output('facility_active_tanks_county');
}
?>
