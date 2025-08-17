<?php
/**
 *
 * Suspected Release Report
 *
 * @package Onestop
 * @subpackage views
 * @uses Report.php
 *
*/

$db = Database::instance();

if (!strtotime($start_date) || !strtotime($end_date))
        exit('Date is not in valid format.');
if($release_status == 'All') {
	$status_filter = " AND SR.DATE_REPORTED BETWEEN TO_DATE(:start_date,'mm/dd/yyyy') AND TO_DATE(:end_date,'mm/dd/yyyy') ";
} else if ($release_status == 'Closed') {
	$status_filter = " AND SR.CLOSED_DATE IS NOT NULL AND SR.CLOSED_DATE BETWEEN TO_DATE(:start_date,'mm/dd/yyyy') AND TO_DATE(:end_date,'mm/dd/yyyy') ";
} else if ($release_status == 'Refered') {
	$status_filter = " AND SR.CLOSED_DATE IS NULL AND SR.REFERRED_DATE IS NOT NULL AND SR.REFERRED_DATE BETWEEN TO_DATE(:start_date,'mm/dd/yyyy') AND TO_DATE(:end_date,'mm/dd/yyyy')";
} else if ($release_status == 'Confirmed') {
	$status_filter = " AND SR.CLOSED_DATE IS NULL AND SR.REFERRED_DATE IS NULL AND SR.CONFIRMED_DATE IS NOT NULL AND SR.CONFIRMED_DATE BETWEEN TO_DATE(:start_date,'mm/dd/yyyy') AND TO_DATE(:end_date,'mm/dd/yyyy') ";
} else {
	$status_filter = " AND SR.CLOSED_DATE IS NULL AND SR.REFERRED_DATE IS NULL AND SR.CONFIRMED_DATE IS NULL ";
}

$report_sql = "
	SELECT SR.FACILITY_ID FACILITY_ID, FAC.FACILITY_NAME FACILITY_NAME,
		( SELECT MIN( s.first_name || ' ' || s.last_name )
			FROM ustx.staff s 
			INNER JOIN ustx.inspections i 
			ON i.staff_code = s.code
			AND i.facility_id = FAC.id
			AND i.date_inspected = ( SELECT MAX( i2.date_inspected )
				FROM ustx.inspections i2
				WHERE i2.facility_id = FAC.id )
		) INSPECTOR,
		FAC.OWNER_ID OWNER_ID, OWN.OWNER_NAME OWNER_NAME, SR.ID SUSPECTED_RELEASE_ID,
		( SELECT LISTAGG( TANK_ID, ', ') WITHIN GROUP (ORDER BY TANK_ID)
			FROM USTX.SUSPECTED_RELEASE_TANK
			WHERE SUSPECTED_RELEASE_ID = SR.ID ) RELATED_TANK_ID,
		SR.DATE_DISCOVERED DATE_DISCOVERED, SR.DATE_REPORTED DATE_REPORTED, REF_SOURCE.SOURCE_DESCRIPTION SOURCE_DESCRIPTION, SR.CAUSE_DESC CAUSE_DESCRIPTION,
		CASE
			WHEN SR.CLOSED_DATE IS NOT NULL THEN 'Closed'
			WHEN SR.REFERRED_DATE IS NOT NULL THEN 'Referred'
			WHEN SR.CONFIRMED_DATE IS NOT NULL THEN 'Confirmed'
			ELSE 'Open'
		END STATUS,
		SR.CLOSED_DATE DATE_CLOSED,
		SR.NFA_LETTER_DATE NFA_LETTER_DATE,
		SR.CONFIRMED_DATE DATE_CONFIRMED,
		SR.REFERRED_DATE DATE_REFERRED,
		SR.COMMENTS COMMENTS
	FROM USTX.SUSPECTED_RELEASE SR
	INNER JOIN USTX.FACILITIES_MVW FAC ON FAC.ID = SR.FACILITY_ID
	INNER JOIN USTX.OWNERS_MVW OWN ON OWN.ID = FAC.OWNER_ID
		LEFT OUTER JOIN ustx.ref_suspected_release_source REF_SOURCE ON REF_SOURCE.ID = SR.SR_SOURCE_ID
	WHERE 1 = 1 {$status_filter} 
	ORDER BY FACILITY_ID, SUSPECTED_RELEASE_ID";

if($release_status == 'Open') {
	$bound_vars = array();
} else {
	$bound_vars = array(':start_date' => $start_date, ':end_date' => $end_date);
}

$rs_arr = $db->query($report_sql, $bound_vars)->as_array();

$report = new Report($output_format, 'Suspected Release Report', "Inspections performed during {$start_date} to {$end_date}\nStatus: {$release_status}");

// labels -----------------------------------------------
$report->setLabelRow( array('Facility ID', 'Facility Name', 'Inspector', 'Owner ID', 'Owner Name', 'Suspected Release ID', 'Related Tank ID', 'Date Discovered', 'Date Reported', 'Source', 'Cause', 'Status', 'Date Closed', 'NFA Letter Date', 'Date Confirmed', 'Date Referred', 'Comments'), array('style' => array('alignment' => array('wrap' => TRUE))) );
$report->getActiveSheet()->getRowDimension($report->row_num - 1)->setRowHeight(25);
$report->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($report->row_num-1, $report->row_num-1);
for($col = 'A'; $col !== 'R'; $col++) {
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
			array('value' => $row['INSPECTOR']), 
			array('value' => $row['OWNER_ID'], 'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER))),
			array('value' => $row['OWNER_NAME']),
			array('value' => $row['SUSPECTED_RELEASE_ID'], 'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER))),
			array('value' => $row['RELATED_TANK_ID'], 'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER))),
			array('value' => Report::TO_DATE($row['DATE_DISCOVERED']), 'style' => Report::$STYLE_DATE),
			array('value' => Report::TO_DATE($row['DATE_REPORTED']), 'style' => Report::$STYLE_DATE),
			array('value' => $row['SOURCE_DESCRIPTION']),
			array('value' => $row['CAUSE_DESCRIPTION']),
			array('value' => $row['STATUS']),
			array('value' => Report::TO_DATE($row['DATE_CLOSED']), 'style' => Report::$STYLE_DATE),
			array('value' => Report::TO_DATE($row['NFA_LETTER_DATE']), 'style' => Report::$STYLE_DATE),
			array('value' => Report::TO_DATE($row['DATE_CONFIRMED']), 'style' => Report::$STYLE_DATE),
			array('value' => Report::TO_DATE($row['DATE_REFERRED']), 'style' => Report::$STYLE_DATE),
			array('value' => $row['COMMENTS'])
		));
	}
}

$flag = $report->output('suspected_release');
?>
