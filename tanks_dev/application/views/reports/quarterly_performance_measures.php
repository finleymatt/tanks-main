<?php

$db = Database::instance();

if ($quarter == '1st') {
	$start_date = '01/01/' . $year;
	$end_date = '03/31/' . $year;
} else if ($quarter == '2nd') {
	$start_date = '04/01/' . $year;
	$end_date = '06/30/' . $year;
} else if ($quarter == '3rd') {
	$start_date = '07/01/' . $year;
	$end_date = '09/30/' . $year;
} else {
	$start_date = '10/01/' . $year;
	$end_date = '12/31/' . $year;
}

$report_sql = "
SELECT * FROM
-- Number of facilities with active tanks (AST & UST) where the status of the tanks is either “currently in use” or “temporarily out of use” received a compliance inspection 
(SELECT count(distinct I.facility_id) active_facility_count from ustx.inspections I
JOIN USTX.tanks T on I.facility_id = T.facility_id
WHERE I.inspection_code = 1 
AND I.date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') AND TO_DATE(:end_date, 'mm/dd/yyyy')
AND (select count(*) from ustx.tanks T WHERE (T.facility_id = I.facility_id) 
AND (T.tank_type in ('A', 'U')) and (T.tank_status_code in (1, 2))) > 0
) active_facility_count,

-- the total number of active facilities
(SELECT count(*) active_facility_total_count FROM USTX.FACILITIES_MVW F
WHERE
	(select count(*)
	from ustx.tanks T
	where (T.facility_id = F.id) and (T.tank_type in ('A', 'U'))
	and (T.tank_status_code in (1, 2))) > 0) active_facility_total_count,

-- Number of facilities with active tanks (AST & UST) where the status of the tanks is either “currently in use” or “temporarily out of use” received a compliance inspection during the quarter where class A or B violations of 20.5 NMAC were discovered
(SELECT count(distinct I.facility_id) facility_ab_nmac_count from ustx.inspections I
	JOIN USTX.tanks T on I.facility_id = T.facility_id
	JOIN USTX.penalties P on I.ID = P.inspection_id
	JOIN USTX.penalty_codes PC on P.penalty_code = PC.code
WHERE I.inspection_code = 1 
	AND PC.penalty_level in ('A', 'B')
	AND substr(to_char(PC.USTR),1,4) = '20.5'
	AND I.date_inspected BETWEEN TO_DATE(:start_date, 'mm/dd/yyyy') AND TO_DATE(:end_date, 'mm/dd/yyyy')
	AND (select count(*) from ustx.tanks T WHERE (T.facility_id = I.facility_id) and (T.tank_type in ('A', 'U')) 
	and (T.tank_status_code in (1, 2))) > 0) facility_ab_nmac_count,

-- the total number of facilities inspected within the quarter
(SELECT count(distinct I.facility_id) inspected_facility_count from ustx.inspections I
WHERE I.date_inspected BETWEEN TO_DATE(:start_date, 'mm/dd/yyyy') AND TO_DATE(:end_date, 'mm/dd/yyyy')) inspected_facility_count,

-- Number of facilities with active tanks (AST & UST) where the status of the tanks is either “currently in use” or “temporarily out of use” received a compliance inspection during the quarter where no class A or B violations were discovered
(SELECT COUNT(DISTINCT I.facility_id) facility_not_ab_count
	FROM ustx.inspections I
	JOIN ustx.tanks T ON I.facility_id = T.facility_id
WHERE I.inspection_code = 1
	AND I.date_inspected BETWEEN TO_DATE(:start_date, 'mm/dd/yyyy') AND TO_DATE(:end_date, 'mm/dd/yyyy')
	AND (SELECT COUNT(*) 
		FROM ustx.tanks T2 
		WHERE T2.facility_id = I.facility_id 
			AND T2.tank_type IN ('A', 'U') 
			AND T2.tank_status_code IN (1, 2)) > 0
	AND NOT EXISTS (
		SELECT 1
		FROM ustx.penalties P
		JOIN ustx.penalty_codes PC ON P.penalty_code = PC.code
		WHERE P.inspection_id = I.ID
			AND PC.penalty_level IN ('A', 'B')
	) 
) facility_not_ab_count,

-- the total number of active facilities inspected during the quarter
(SELECT count(distinct I.facility_id) inspected_active_fac_count from ustx.inspections I
JOIN USTX.facilities_mvw F ON I.facility_id = F.id
WHERE I.date_inspected BETWEEN TO_DATE(:start_date, 'mm/dd/yyyy') AND TO_DATE(:end_date, 'mm/dd/yyyy')
AND (select count(*) from ustx.tanks T WHERE (T.facility_id = F.id) and (T.tank_type in ('A', 'U')) and (T.tank_status_code in (1, 2))) > 0
) inspected_active_fac_count
";

$rs_arr = $db->query($report_sql, array(':start_date' => $start_date, ':end_date' => $end_date))->as_array();

if (count($rs_arr)) {
	$report = new Report($output_format, 'Quarterly Performance Measures', "{$year} {$quarter} Quarter : {$start_date} - {$end_date}");
	$row = $rs_arr[0];  // only one row is returned by the aggregate functions
	$active_facility_count = $row['ACTIVE_FACILITY_COUNT'];
	$active_facility_total_count = $row['ACTIVE_FACILITY_TOTAL_COUNT'];
	$facility_ab_nmac_count = $row['FACILITY_AB_NMAC_COUNT'];
	$inspected_facility_count = $row['INSPECTED_FACILITY_COUNT'];
	$facility_not_ab_count = $row['FACILITY_NOT_AB_COUNT'];
	$inspected_active_fac_count = $row['INSPECTED_ACTIVE_FAC_COUNT'];

	$report->setBlankRow();

	$report->setRow(array(
		array('value' => 'Number of active facilities with compliance inspection during the quarter', 'style' => Report::$STYLE_LABEL),
		array('value' => $active_facility_count)
	));

	$report->setRow(array(
		array('value' => 'The total number of active facilities', 'style' => Report::$STYLE_LABEL),
		array('value' => $active_facility_total_count)
	));

	$report->setRow(array(
		array('value' => 'Percentage %', 'style' => Report::$STYLE_LABEL),
		array('value' => $active_facility_count/$active_facility_total_count, 'style' => Report::$STYLE_PERCENT)
	));

	$report->setBlankRow();

	$report->setRow(array(
		array('value' => 'Number of active facilities with compliance inspection where class A/B violations of 20.5 NMAC were discovered', 'style' => Report::$STYLE_LABEL),
		array('value' => $facility_ab_nmac_count)
	));

	$report->setRow(array(
		array('value' => 'The total number of facilities inspected within the quarter', 'style' => Report::$STYLE_LABEL),
		array('value' => $inspected_facility_count)
	));

	$report->setRow(array(
		array('value' => 'Percentage %', 'style' => Report::$STYLE_LABEL),
		array('value' => $facility_ab_nmac_count/$inspected_facility_count, 'style' => Report::$STYLE_PERCENT)
	));

	$report->setBlankRow();

	$report->setRow(array(
		array('value' => 'Number of active facilities with ccompliance inspection where no class A/B violations were discovered ', 'style' => Report::$STYLE_LABEL),
		array('value' => $facility_not_ab_count)
	));

	$report->setRow(array(
		array('value' => 'The total number of active facilities inspected during the quarter', 'style' => Report::$STYLE_LABEL),
		array('value' => $inspected_active_fac_count)
	));

	$report->setRow(array(
		array('value' => 'Percentage %', 'style' => Report::$STYLE_LABEL),
		array('value' => $facility_not_ab_count/$inspected_active_fac_count, 'style' => Report::$STYLE_PERCENT)
	));

	$report->setBlankRow();

	$report->setColumnSize(array(60, 13));

	$flag = $report->output('quarterly_performance_measures');
}
