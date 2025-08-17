<?php
/**
 * TCR Compliance Statistics
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

-- SP violation count
(select Count(Count(facility_id)) ast_sp_violation_count FROM ustx.inspections I
		inner join ustx.penalties P on I.id = P.inspection_id
		inner join ustx.penalty_codes PC on P.penalty_code = PC.code
where
		I.date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
		and PC.is_tcr = 'T'
		and PC.tcr_category = 'SP'
		and I.inspection_code = 1
		and I.facility_id in (select T.facility_id from ustx.tanks T where T.tank_type = 'A')
		and I.facility_id not in (select T.facility_id from ustx.tanks T where T.tank_type = 'U')
group by facility_id
having count(*) > 0) ast_sp_violation_count,

-- OF violation count
(select Count(Count(facility_id)) ast_of_violation_count FROM ustx.inspections I
		inner join ustx.penalties P on I.id = P.inspection_id
		inner join ustx.penalty_codes PC on P.penalty_code = PC.code
where
		I.date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
		and PC.is_tcr = 'T'
		and PC.tcr_category = 'OF'
		and I.inspection_code = 1
		and I.facility_id in (select T.facility_id from ustx.tanks T where T.tank_type = 'A')
		and I.facility_id not in (select T.facility_id from ustx.tanks T where T.tank_type = 'U')
group by facility_id
having count(*) > 0) ast_of_violation_count,

-- CP violation count
(select Count(Count(facility_id)) ast_cp_violation_count FROM ustx.inspections I
	inner join ustx.penalties P on I.id = P.inspection_id
	inner join ustx.penalty_codes PC on P.penalty_code = PC.code
where
	I.date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
	and PC.is_tcr = 'T'
	and PC.tcr_category = 'CP'
	and I.inspection_code = 1
	and I.facility_id in (select T.facility_id from ustx.tanks T where T.tank_type = 'A')
	and I.facility_id not in (select T.facility_id from ustx.tanks T where T.tank_type = 'U')
group by facility_id
having count(*) > 0) ast_cp_violation_count,

-- RD violation count
(select Count(Count(facility_id)) ast_rd_violation_count FROM ustx.inspections I
	inner join ustx.penalties P on I.id = P.inspection_id
	inner join ustx.penalty_codes PC on P.penalty_code = PC.code
where
	I.date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
	and PC.is_tcr = 'T'
	and PC.tcr_category = 'RD'
	and I.inspection_code = 1
	and I.facility_id in (select T.facility_id from ustx.tanks T where T.tank_type = 'A')
	and I.facility_id not in (select T.facility_id from ustx.tanks T where T.tank_type = 'U')
group by facility_id
having count(*) > 0) ast_rd_violation_count,

-- SP, OF, CP or RD violation count
(select Count(Count(facility_id)) ast_sp_of_cp_rd_vio_count FROM ustx.inspections I
		inner join ustx.penalties P on I.id = P.inspection_id
		inner join ustx.penalty_codes PC on P.penalty_code = PC.code
where
		I.date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
		and PC.is_tcr = 'T'
		and (PC.tcr_category = 'SP' or PC.tcr_category = 'OF' or PC.tcr_category = 'CP' or PC.tcr_category = 'RD')
		and I.inspection_code = 1
		and I.facility_id in (select T.facility_id from ustx.tanks T where T.tank_type = 'A')
		and I.facility_id not in (select T.facility_id from ustx.tanks T where T.tank_type = 'U')
group by facility_id
having count(*) > 0) ast_sp_of_cp_rd_vio_count,

-- OT violation count
(select Count(Count(facility_id)) ast_ot_violation_count FROM ustx.inspections I
	inner join ustx.penalties P on I.id = P.inspection_id
	inner join ustx.penalty_codes PC on P.penalty_code = PC.code
where
	I.date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
	and PC.is_cm = 'T'
	and PC.cm_category = 'OT'
	and I.inspection_code = 1
	and I.facility_id in (select T.facility_id from ustx.tanks T where T.tank_type = 'A')
	and I.facility_id not in (select T.facility_id from ustx.tanks T where T.tank_type = 'U')
group by facility_id
having count(*) > 0) ast_ot_violation_count,

-- FR violation count
(select Count(Count(facility_id)) ast_fr_violation_count FROM ustx.inspections I
	inner join ustx.penalties P on I.id = P.inspection_id
	inner join ustx.penalty_codes PC on P.penalty_code = PC.code
where
	I.date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
	and PC.is_cm = 'T'
	and PC.cm_category = 'FR'
	and I.inspection_code = 1
	and I.facility_id in (select T.facility_id from ustx.tanks T where T.tank_type = 'A')
	and I.facility_id not in (select T.facility_id from ustx.tanks T where T.tank_type = 'U')
group by facility_id
having count(*) > 0) ast_fr_violation_count,

-- WI violation count
(select Count(Count(facility_id)) ast_wi_violation_count FROM ustx.inspections I
	inner join ustx.penalties P on I.id = P.inspection_id
	inner join ustx.penalty_codes PC on P.penalty_code = PC.code
where
	I.date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
	and PC.is_cm = 'T'
	and PC.cm_category = 'WI'
	and I.inspection_code = 1
	and I.facility_id in (select T.facility_id from ustx.tanks T where T.tank_type = 'A')
	and I.facility_id not in (select T.facility_id from ustx.tanks T where T.tank_type = 'U')
group by facility_id
having count(*) > 0) ast_wi_violation_count,


-- Total Inspections count
(select Count(Count(*)) ast_inspection_count from ustx.inspections
where date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
	and inspection_code = 1
	and facility_id in (select T.facility_id from ustx.tanks T where T.tank_type = 'A' and T.tank_status_code = 1)
	and facility_id not in (select T.facility_id from ustx.tanks T where T.tank_type = 'U')
group by facility_id) ast_inspection_count,

-- facilities with UST tanks ==================================================

-- SP violation count
(select Count(Count(facility_id)) ust_sp_violation_count FROM ustx.inspections I
		inner join ustx.penalties P on I.id = P.inspection_id
		inner join ustx.penalty_codes PC on P.penalty_code = PC.code
where
		I.date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
		and PC.is_tcr = 'T'
		and PC.tcr_category = 'SP'
		and I.inspection_code = 1
		and I.facility_id in (select T.facility_id from ustx.tanks T where T.tank_type = 'U')
group by facility_id
having count(*) > 0) ust_sp_violation_count,

-- OF violation count
(select Count(Count(facility_id)) ust_of_violation_count FROM ustx.inspections I
		inner join ustx.penalties P on I.id = P.inspection_id
		inner join ustx.penalty_codes PC on P.penalty_code = PC.code
where
		I.date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
		and PC.is_tcr = 'T'
		and PC.tcr_category = 'OF'
		and I.inspection_code = 1
		and I.facility_id in (select T.facility_id from ustx.tanks T where T.tank_type = 'U')
group by facility_id
having count(*) > 0) ust_of_violation_count,

-- CP violation count
(select Count(Count(facility_id)) ust_cp_violation_count FROM ustx.inspections I
	inner join ustx.penalties P on I.id = P.inspection_id
	inner join ustx.penalty_codes PC on P.penalty_code = PC.code
where
	I.date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
	and PC.is_tcr = 'T'
	and PC.tcr_category = 'CP'
	and I.inspection_code = 1
	and I.facility_id in (select T.facility_id from ustx.tanks T where T.tank_type = 'U')
group by facility_id
having count(*) > 0) ust_cp_violation_count,

-- RD violation count
(select Count(Count(facility_id)) ust_rd_violation_count FROM ustx.inspections I
	inner join ustx.penalties P on I.id = P.inspection_id
	inner join ustx.penalty_codes PC on P.penalty_code = PC.code
where
	I.date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
	and PC.is_tcr = 'T'
	and PC.tcr_category = 'RD'
	and I.inspection_code = 1
	and I.facility_id in (select T.facility_id from ustx.tanks T where T.tank_type = 'U')
group by facility_id
having count(*) > 0) ust_rd_violation_count,

-- SP, OF, CP or RD violation count
(select Count(Count(facility_id)) ust_sp_of_cp_rd_vio_count FROM ustx.inspections I
		inner join ustx.penalties P on I.id = P.inspection_id
		inner join ustx.penalty_codes PC on P.penalty_code = PC.code
where
		I.date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
		and PC.is_tcr = 'T'
		and (PC.tcr_category = 'SP' or PC.tcr_category = 'OF' or PC.tcr_category = 'CP' or PC.tcr_category = 'RD')
		and I.inspection_code = 1
		and I.facility_id in (select T.facility_id from ustx.tanks T where T.tank_type = 'U')
group by facility_id
having count(*) > 0) ust_sp_of_cp_rd_vio_count,

-- OT violation count
(select Count(Count(facility_id)) ust_ot_violation_count FROM ustx.inspections I
	inner join ustx.penalties P on I.id = P.inspection_id
	inner join ustx.penalty_codes PC on P.penalty_code = PC.code
where
	I.date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
	and PC.is_cm = 'T'
	and PC.cm_category = 'OT'
	and I.inspection_code = 1
	and I.facility_id in (select T.facility_id from ustx.tanks T where T.tank_type = 'U')
group by facility_id
having count(*) > 0) ust_ot_violation_count,

-- FR violation count
(select Count(Count(facility_id)) ust_fr_violation_count FROM ustx.inspections I
	inner join ustx.penalties P on I.id = P.inspection_id
	inner join ustx.penalty_codes PC on P.penalty_code = PC.code
where
	I.date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
	and PC.is_cm = 'T'
	and PC.cm_category = 'FR'
	and I.inspection_code = 1
	and I.facility_id in (select T.facility_id from ustx.tanks T where T.tank_type = 'U')
group by facility_id
having count(*) > 0) ust_fr_violation_count,

-- WI violation count
(select Count(Count(facility_id)) ust_wi_violation_count FROM ustx.inspections I
	inner join ustx.penalties P on I.id = P.inspection_id
	inner join ustx.penalty_codes PC on P.penalty_code = PC.code
where
	I.date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
	and PC.is_cm = 'T'
	and PC.cm_category = 'WI'
	and I.inspection_code = 1
	and I.facility_id in (select T.facility_id from ustx.tanks T where T.tank_type = 'U')
group by facility_id
having count(*) > 0) ust_wi_violation_count,

-- Total Inspections count
(select Count(Count(*)) ust_inspection_count from ustx.inspections
where date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
	and inspection_code = 1
	and facility_id in (select T.facility_id from ustx.tanks T where T.tank_type = 'U' and T.tank_status_code = 1)
group by facility_id) ust_inspection_count,

-- facilities with ANY tank type ==============================================

-- SP violation count
(select Count(Count(facility_id)) sp_violation_count FROM ustx.inspections I
		inner join ustx.penalties P on I.id = P.inspection_id
		inner join ustx.penalty_codes PC on P.penalty_code = PC.code
where
		I.date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
		and PC.is_tcr = 'T'
		and PC.tcr_category = 'SP'
		and I.inspection_code = 1
group by facility_id
having count(*) > 0) sp_violation_count,

-- OF violation count
(select Count(Count(facility_id)) of_violation_count FROM ustx.inspections I
	inner join ustx.penalties P on I.id = P.inspection_id
	inner join ustx.penalty_codes PC on P.penalty_code = PC.code
where
	I.date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
	and PC.is_tcr = 'T'
	and PC.tcr_category = 'OF'
	and I.inspection_code = 1
group by facility_id
having count(*) > 0) of_violation_count,

-- CP violation count
(select Count(Count(facility_id)) cp_violation_count FROM ustx.inspections I
	inner join ustx.penalties P on I.id = P.inspection_id
	inner join ustx.penalty_codes PC on P.penalty_code = PC.code
where
	I.date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
	and PC.is_tcr = 'T'
	and PC.tcr_category = 'CP'
	and I.inspection_code = 1
group by facility_id
having count(*) > 0) cp_violation_count,

-- RD violation count
(select Count(Count(facility_id)) rd_violation_count FROM ustx.inspections I
		inner join ustx.penalties P on I.id = P.inspection_id
		inner join ustx.penalty_codes PC on P.penalty_code = PC.code
where
		I.date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
		and PC.is_tcr = 'T'
		and PC.tcr_category = 'RD'
		and I.inspection_code = 1
group by facility_id
having count(*) > 0) rd_violation_count,

-- SP, OF, CP or RD violation count
(select Count(Count(facility_id)) sp_of_cp_rd_vio_count FROM ustx.inspections I
		inner join ustx.penalties P on I.id = P.inspection_id
		inner join ustx.penalty_codes PC on P.penalty_code = PC.code
where
		I.date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
		and PC.is_tcr = 'T'
		and (PC.tcr_category = 'SP' or PC.tcr_category = 'OF' or PC.tcr_category = 'CP' or PC.tcr_category = 'RD')
		and I.inspection_code = 1
group by facility_id
having count(*) > 0) sp_of_cp_rd_vio_count,

-- OT violation count
(select Count(Count(facility_id)) ot_violation_count FROM ustx.inspections I
	inner join ustx.penalties P on I.id = P.inspection_id
	inner join ustx.penalty_codes PC on P.penalty_code = PC.code
where
	I.date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
	and PC.is_cm = 'T'
	and PC.cm_category = 'OT'
	and I.inspection_code = 1
group by facility_id
having count(*) > 0) ot_violation_count,

-- FR violation count
(select Count(Count(facility_id)) fr_violation_count FROM ustx.inspections I
	inner join ustx.penalties P on I.id = P.inspection_id
	inner join ustx.penalty_codes PC on P.penalty_code = PC.code
where
	I.date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
	and PC.is_cm = 'T'
	and PC.cm_category = 'FR'
	and I.inspection_code = 1
group by facility_id
having count(*) > 0) fr_violation_count,

-- WI violation count
(select Count(Count(facility_id)) wi_violation_count FROM ustx.inspections I
	inner join ustx.penalties P on I.id = P.inspection_id
	inner join ustx.penalty_codes PC on P.penalty_code = PC.code
where
	I.date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
	and PC.is_cm = 'T'
	and PC.cm_category = 'WI'
	and I.inspection_code = 1
group by facility_id
having count(*) > 0) wi_violation_count,

-- Total Inspections count
(select Count(Count(*)) inspection_count from ustx.inspections
where date_inspected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')
	and inspection_code = 1
	and facility_id in (select T.facility_id from ustx.tanks T where T.tank_status_code = 1)
group by facility_id) inspection_count";


$rs_arr = $db->query($report_sql, array(':start_date' => $start_date, ':end_date' => $end_date))->as_array();
if (count($rs_arr)) {
	$report = new Report($output_format, 'TCR Compliance Statistics', "inspections performed: {$start_date} - {$end_date}");
	$row = $rs_arr[0];  // only one row is returned by the aggregate functions

	if($row['AST_INSPECTION_COUNT'] == 0 || $row['UST_INSPECTION_COUNT'] == 0) {
		echo 'There are no inspections performed on AST or UST during the selected period of time, please re-enter another start date and end date.';
		exit;
	} else {
		display_stats($report, $row['AST_SP_VIOLATION_COUNT'], $row['AST_OF_VIOLATION_COUNT'], $row['AST_CP_VIOLATION_COUNT'], $row['AST_RD_VIOLATION_COUNT'], $row['AST_SP_OF_CP_RD_VIO_COUNT'], $row['AST_OT_VIOLATION_COUNT'], $row['AST_FR_VIOLATION_COUNT'], $row['AST_WI_VIOLATION_COUNT'], $row['AST_INSPECTION_COUNT'], 'AST Tanks');
		display_stats($report, $row['UST_SP_VIOLATION_COUNT'], $row['UST_OF_VIOLATION_COUNT'], $row['UST_CP_VIOLATION_COUNT'], $row['UST_RD_VIOLATION_COUNT'], $row['UST_SP_OF_CP_RD_VIO_COUNT'], $row['UST_OT_VIOLATION_COUNT'], $row['UST_FR_VIOLATION_COUNT'], $row['UST_WI_VIOLATION_COUNT'], $row['UST_INSPECTION_COUNT'], 'UST Tanks');
		display_stats($report, $row['SP_VIOLATION_COUNT'], $row['OF_VIOLATION_COUNT'], $row['CP_VIOLATION_COUNT'], $row['RD_VIOLATION_COUNT'], $row['SP_OF_CP_RD_VIO_COUNT'], $row['OT_VIOLATION_COUNT'], $row['FR_VIOLATION_COUNT'], $row['WI_VIOLATION_COUNT'], $row['INSPECTION_COUNT'], 'All Tank Types');

		$report->setColumnSize(array(32, 13, 13, 13, 13, 13, 13, 13, 13, 13));
		$flag = $report->output('tcr_compliance_stat');
	}
}


function display_stats($report, $sp_violation_count, $of_violation_count,$cp_violation_count, $rd_violation_count, $sp_of_cp_rd_violation_count, $ot_violation_count, $fr_violation_count, $wi_violation_count, $inspection_count, $title) {
	$report->setLabelRow(array(array('colspan' => 12, 'value' => $title, 'style' => array('fill' => array(
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
		1 => 'Spill TCR', 'Overfill TCR', 'CP TCR', 'RD TCR', 'Overall TCR', 'OT Compliance', 'FR Compliance', 'WI Compliance'
	));

	$sp_not_compliance_pct = $sp_violation_count / $inspection_count;
	$of_not_compliance_pct = $of_violation_count / $inspection_count;
	$cp_not_compliance_pct = $cp_violation_count / $inspection_count;
	$rd_not_compliance_pct = $rd_violation_count / $inspection_count;
	$sp_of_cp_rd_not_compliance_pct = $sp_of_cp_rd_violation_count / $inspection_count;
	$ot_not_compliance_pct = $ot_violation_count / $inspection_count;
	$fr_not_compliance_pct = $fr_violation_count / $inspection_count;
	$wi_not_compliance_pct = $wi_violation_count / $inspection_count;
	$sp_compliance_pct = 1 - $sp_not_compliance_pct;
	$of_compliance_pct = 1 - $of_not_compliance_pct;
	$cp_compliance_pct = 1 - $cp_not_compliance_pct;
	$rd_compliance_pct = 1 - $rd_not_compliance_pct;
	$sp_of_cp_rd_compliance_pct = 1 - $sp_of_cp_rd_not_compliance_pct;
	$ot_compliance_pct = 1 - $ot_not_compliance_pct;
	$fr_compliance_pct = 1 - $fr_not_compliance_pct;
	$wi_compliance_pct = 1 - $wi_not_compliance_pct;

	$report->setRow(array(
		array('value' => 'Violations (count)', 'style' => Report::$STYLE_LABEL),
		array('value' => $sp_violation_count),
		array('value' => $of_violation_count),
		array('value' => $cp_violation_count),
		array('value' => $rd_violation_count),
		array('value' => $sp_of_cp_rd_violation_count),
		array('value' => $ot_violation_count),
		array('value' => $fr_violation_count),
		array('value' => $wi_violation_count)
	));
	$report->setRow(array(
		array('value' => 'Facilities not in Compliance (%)', 'style' => Report::$STYLE_LABEL),
		array('value' => $sp_not_compliance_pct, 'style' => Report::$STYLE_PERCENT),
		array('value' => $of_not_compliance_pct, 'style' => Report::$STYLE_PERCENT),
		array('value' => $cp_not_compliance_pct, 'style' => Report::$STYLE_PERCENT),
		array('value' => $rd_not_compliance_pct, 'style' => Report::$STYLE_PERCENT),
		array('value' => $sp_of_cp_rd_not_compliance_pct, 'style' => Report::$STYLE_PERCENT),
		array('value' => $ot_not_compliance_pct, 'style' => Report::$STYLE_PERCENT),
		array('value' => $fr_not_compliance_pct, 'style' => Report::$STYLE_PERCENT),
		array('value' => $wi_not_compliance_pct, 'style' => Report::$STYLE_PERCENT)
	));
	$report->setRow(array(
		array('value' => 'Facilities in Compliance (%)', 'style' => Report::$STYLE_LABEL),
		array('value' => $sp_compliance_pct, 'style' => Report::$STYLE_PERCENT),
		array('value' => $of_compliance_pct, 'style' => Report::$STYLE_PERCENT),
		array('value' => $cp_compliance_pct, 'style' => Report::$STYLE_PERCENT),
		array('value' => $rd_compliance_pct, 'style' => Report::$STYLE_PERCENT),
		array('value' => $sp_of_cp_rd_compliance_pct, 'style' => Report::$STYLE_PERCENT),
		array('value' => $ot_compliance_pct, 'style' => Report::$STYLE_PERCENT),
		array('value' => $fr_compliance_pct, 'style' => Report::$STYLE_PERCENT),
		array('value' => $wi_compliance_pct, 'style' => Report::$STYLE_PERCENT),
		array('value' => '<-- SP, OF, CP, RD, OT, FR & WI Compliance', 'style' => array_merge(Report::$STYLE_NOTE, array('borders' => array(), 'fill' => array()))) // remove border and background fill
	));
	$report->setBlankRow();
}

?>
