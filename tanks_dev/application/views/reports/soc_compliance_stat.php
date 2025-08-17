<?php
/**
 * SOC Compliance Statistics
 * Created to fulfill EPA's Semiannual Measure Record Detail request
 * fields: UST-4, UST-5, and UST-6.
 *
 * @package Onestop
 * @subpackage views
 * @uses Report.php
 *
*/

$db = Database::instance();

if (!strtotime($start_date) || !strtotime($end_date))
	exit('Dates are not in valid format.');

$report_sql = "
SELECT * FROM

-- facilities with AST tanks ==================================================
-- if facility has both AST and UST, then UST should be used instead

-- RP violation count
(select Count(Count(facility_id)) ast_rp_violation_count FROM ustx.inspections I
		inner join ustx.penalties P on I.id = P.inspection_id
		inner join ustx.penalty_codes PC on P.penalty_code = PC.code
where
		I.date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
		and PC.is_soc = 'T'
		and PC.soc_category = 'RP'
		and I.inspection_code = 1
		and I.facility_id in (select T.facility_id from ustx.tanks T where T.tank_type = 'A')
		and I.facility_id not in (select T.facility_id from ustx.tanks T where T.tank_type = 'U')
group by facility_id
having count(*) > 0) ast_rp_violation_count,

-- RD violation count
(select Count(Count(facility_id)) ast_rd_violation_count FROM ustx.inspections I
		inner join ustx.penalties P on I.id = P.inspection_id
		inner join ustx.penalty_codes PC on P.penalty_code = PC.code
where
		I.date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
		and PC.is_soc = 'T'
		and PC.soc_category = 'RD'
		and I.inspection_code = 1
		and I.facility_id in (select T.facility_id from ustx.tanks T where T.tank_type = 'A')
		and I.facility_id not in (select T.facility_id from ustx.tanks T where T.tank_type = 'U')
group by facility_id
having count(*) > 0) ast_rd_violation_count,

-- RP or RD violation count
(select Count(Count(facility_id)) ast_rp_rd_violation_count FROM ustx.inspections I
		inner join ustx.penalties P on I.id = P.inspection_id
		inner join ustx.penalty_codes PC on P.penalty_code = PC.code
where
		I.date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
		and PC.is_soc = 'T'
		and (PC.soc_category = 'RP' or PC.soc_category = 'RD')
		and I.inspection_code = 1
		and I.facility_id in (select T.facility_id from ustx.tanks T where T.tank_type = 'A')
		and I.facility_id not in (select T.facility_id from ustx.tanks T where T.tank_type = 'U')
group by facility_id
having count(*) > 0) ast_rp_rd_violation_count,

-- Total Inspections count
(select Count(Count(*)) ast_inspection_count from ustx.inspections
where date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
	and inspection_code = 1
	and facility_id in (select T.facility_id from ustx.tanks T where T.tank_type = 'A' and T.tank_status_code = 1)
	and facility_id not in (select T.facility_id from ustx.tanks T where T.tank_type = 'U')
group by facility_id) ast_inspection_count,

-- facilities with UST tanks ==================================================

-- RP violation count
(select Count(Count(facility_id)) ust_rp_violation_count FROM ustx.inspections I
		inner join ustx.penalties P on I.id = P.inspection_id
		inner join ustx.penalty_codes PC on P.penalty_code = PC.code
where
		I.date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
		and PC.is_soc = 'T'
		and PC.soc_category = 'RP'
		and I.inspection_code = 1
		and I.facility_id in (select T.facility_id from ustx.tanks T where T.tank_type = 'U')
group by facility_id
having count(*) > 0) ust_rp_violation_count,

-- RD violation count
(select Count(Count(facility_id)) ust_rd_violation_count FROM ustx.inspections I
		inner join ustx.penalties P on I.id = P.inspection_id
		inner join ustx.penalty_codes PC on P.penalty_code = PC.code
where
		I.date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
		and PC.is_soc = 'T'
		and PC.soc_category = 'RD'
		and I.inspection_code = 1
		and I.facility_id in (select T.facility_id from ustx.tanks T where T.tank_type = 'U')
group by facility_id
having count(*) > 0) ust_rd_violation_count,

-- RP or RD violation count
(select Count(Count(facility_id)) ust_rp_rd_violation_count FROM ustx.inspections I
		inner join ustx.penalties P on I.id = P.inspection_id
		inner join ustx.penalty_codes PC on P.penalty_code = PC.code
where
		I.date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
		and PC.is_soc = 'T'
		and (PC.soc_category = 'RP' or PC.soc_category = 'RD')
		and I.inspection_code = 1
		and I.facility_id in (select T.facility_id from ustx.tanks T where T.tank_type = 'U')
group by facility_id
having count(*) > 0) ust_rp_rd_violation_count,

-- Total Inspections count
(select Count(Count(*)) ust_inspection_count from ustx.inspections
where date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
	and inspection_code = 1
	and facility_id in (select T.facility_id from ustx.tanks T where T.tank_type = 'U' and T.tank_status_code = 1)
group by facility_id) ust_inspection_count,

-- facilities with ANY tank type ==============================================

-- RP violation count
(select Count(Count(facility_id)) rp_violation_count FROM ustx.inspections I
		inner join ustx.penalties P on I.id = P.inspection_id
		inner join ustx.penalty_codes PC on P.penalty_code = PC.code
where
		I.date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
		and PC.is_soc = 'T'
		and PC.soc_category = 'RP'
		and I.inspection_code = 1
group by facility_id
having count(*) > 0) rp_violation_count,

-- RD violation count
(select Count(Count(facility_id)) rd_violation_count FROM ustx.inspections I
		inner join ustx.penalties P on I.id = P.inspection_id
		inner join ustx.penalty_codes PC on P.penalty_code = PC.code
where
		I.date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
		and PC.is_soc = 'T'
		and PC.soc_category = 'RD'
		and I.inspection_code = 1
group by facility_id
having count(*) > 0) rd_violation_count,

-- RP or RD violation count
(select Count(Count(facility_id)) rp_rd_violation_count FROM ustx.inspections I
		inner join ustx.penalties P on I.id = P.inspection_id
		inner join ustx.penalty_codes PC on P.penalty_code = PC.code
where
		I.date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
		and PC.is_soc = 'T'
		and (PC.soc_category = 'RP' or PC.soc_category = 'RD')
		and I.inspection_code = 1
group by facility_id
having count(*) > 0) rp_rd_violation_count,

-- Total Inspections count
(select Count(Count(*)) inspection_count from ustx.inspections
where date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
	and inspection_code = 1
	and facility_id in (select T.facility_id from ustx.tanks T where T.tank_status_code = 1)
group by facility_id) inspection_count";


$rs_arr = $db->query($report_sql, array(':start_date' => $start_date, ':end_date' => $end_date))->as_array();
if (count($rs_arr)) {
	$report = new Report($output_format, 'SOC Compliance Statistics', "inspections performed: {$start_date} - {$end_date}");
	$row = $rs_arr[0];  // only one row is returned by the aggregate functions
	
	display_stats($report, $row['AST_RP_VIOLATION_COUNT'], $row['AST_RD_VIOLATION_COUNT'], $row['AST_RP_RD_VIOLATION_COUNT'], $row['AST_INSPECTION_COUNT'], 'AST Tanks');
	display_stats($report, $row['UST_RP_VIOLATION_COUNT'], $row['UST_RD_VIOLATION_COUNT'], $row['UST_RP_RD_VIOLATION_COUNT'], $row['UST_INSPECTION_COUNT'], 'UST Tanks');
	display_stats($report, $row['RP_VIOLATION_COUNT'], $row['RD_VIOLATION_COUNT'], $row['RP_RD_VIOLATION_COUNT'], $row['INSPECTION_COUNT'], 'All Tank Types');

	$report->setColumnSize(array(32, 13, 13, 13, 13));
	$flag = $report->output('soc_compliance_stat');
}


function display_stats($report, $rp_violation_count, $rd_violation_count, $rp_rd_violation_count, $inspection_count, $title) {
	$report->setLabelRow(array(array('colspan' => 7, 'value' => $title, 'style' => array('fill' => array(
		'type' => PHPExcel_Style_Fill::FILL_SOLID,
		'color' => array('argb' => 'FFD07070')
	)))));
	$report->setBlankRow();

	$report->setRow(array(
		array('value' => 'Compliance Inspections (count)', 'style' => Report::$STYLE_LABEL),
		array('value' => $inspection_count)
	));
	$report->setBlankRow();

	$report->setLabelRow(array(
		1 => 'RP Violation', 'RD Violation', 'RP or RD'
	));

	$rp_not_compliance_pct = $rp_violation_count / $inspection_count;
	$rd_not_compliance_pct = $rd_violation_count / $inspection_count;
	$rp_rd_not_compliance_pct = $rp_rd_violation_count / $inspection_count;
	$rp_compliance_pct = 1 - $rp_not_compliance_pct;
	$rd_compliance_pct = 1 - $rd_not_compliance_pct;
	$rp_rd_compliance_pct = 1 - $rp_rd_not_compliance_pct;

	$report->setRow(array(
		array('value' => 'Violations (count)', 'style' => Report::$STYLE_LABEL),
		array('value' => $rp_violation_count),
		array('value' => $rd_violation_count),
		array('value' => $rp_rd_violation_count)
	));
	$report->setRow(array(
		array('value' => 'Facilities not in Compliance (%)', 'style' => Report::$STYLE_LABEL),
		array('value' => $rp_not_compliance_pct, 'style' => Report::$STYLE_PERCENT),
		array('value' => $rd_not_compliance_pct, 'style' => Report::$STYLE_PERCENT),
		array('value' => $rp_rd_not_compliance_pct, 'style' => Report::$STYLE_PERCENT)
	));
	$report->setRow(array(
		array('value' => 'Facilities in Compliance (%)', 'style' => Report::$STYLE_LABEL),
		array('value' => $rp_compliance_pct, 'style' => Report::$STYLE_PERCENT),
		array('value' => $rd_compliance_pct, 'style' => Report::$STYLE_PERCENT),
		array('value' => $rp_rd_compliance_pct, 'style' => Report::$STYLE_PERCENT),
		array('value' => '<-- Both RP and RD Compliance', 'style' => array_merge(Report::$STYLE_NOTE, array('borders' => array(), 'fill' => array()))) // remove border and background fill
	));
	$report->setBlankRow();
}

?>
