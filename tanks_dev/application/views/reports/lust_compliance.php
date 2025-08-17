<?php
/**
 * Active Facility LUST Compliance
 *
 * @package Onestop
 * @subpackage views
 * @uses Report.php
 *
*/

$db = Database::instance();

$report_sql = "
SELECT 
	O.owner_name, R.responsible_party, R.id RID, F.id FID, F.facility_name, F.city, nvl(CT.county, 'UNKNOWN COUNTY') county, I.last_inspected_date,
	( select max(I2.staff_code) from ustx.inspections I2 where (I2.facility_id=I.facility_id) and (I2.inspection_code=1) and (I2.date_inspected=I.last_inspected_date) group by I2.staff_code ) STAFF_CODE
FROM USTX.facilities_mvw F
	INNER JOIN USTX.owners_mvw O ON F.owner_id = O.id
	INNER JOIN LUST.lust_releases_mvw R ON F.id = R.facility_id
	INNER JOIN (
		select facility_id, max(date_inspected) last_inspected_date
		from ustx.inspections
		where (inspection_code = 1) -- 1 = COMPLIANCE
		group by facility_id
		having max(date_inspected) < TO_DATE(:before_date, 'mm/dd/yyyy')
	) I ON F.id = I.facility_id
	LEFT OUTER JOIN USTX.cities CT ON F.city = CT.city
WHERE
	(select count(*) from ustx.tanks T where (T.facility_id = F.id) and (T.TANK_TYPE in ('A', 'U')) and (t.tank_status_code in (1, 2))) > 0
	-- only include release sites with the following status list
	AND ((select max(status_code) from lust.lust_status_mvw S where (S.rel_id = R.id)
		and S.date_created = (select max(date_created) from lust.lust_status_mvw S2 where (S2.rel_id = R.id)))
		in ('L84', 'L90', 'L93', 'L94', 'L99', 'M00', 'M03', 'M23')
	)
ORDER BY F.ID";


$rs_arr = $db->query($report_sql, array(':before_date' => $before_date))->as_array();

if (count($rs_arr)) {
	$report = new Report($output_format, 'Active Facility LUST Compliance', "Before: {$before_date}");
	
	// labels -----------------------------------------------
	$report->setLabelRow( array('Owner Name', 'Responsible Party', 'RID', 'FID', 'Facility', 'City', 'County', 'Last Compl Insp Date', 'Inspector Initials'), array('style' => array('alignment' => array('wrap' => TRUE))) );
	$report->getActiveSheet()->getRowDimension($report->row_num - 1)->setRowHeight(25);
	$report->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($report->row_num-1, $report->row_num-1);
	
	// main body --------------------------------------------
	$group_row_start = $report->row_num;
	foreach ($rs_arr as $row) {
		$report->setRow(array(
			array('value' => $row['OWNER_NAME']),
			array('value' => $row['RESPONSIBLE_PARTY']),
			array('value' => $row['RID']),
			array('value' => $row['FID']),
			array('value' => $row['FACILITY_NAME']),
			array('value' => $row['CITY']),
			array('value' => $row['COUNTY']),
			array('value' => Report::TO_DATE($row['LAST_INSPECTED_DATE']), 'style' => Report::$STYLE_DATE),
			array('value' => $row['STAFF_CODE'])
		));
	}
	
	$report->setColumnSize(array(35, 35, 8, 8, 20, 20, 15, 10, 8));
	$flag = $report->output('lust_compliance');
}
?>
