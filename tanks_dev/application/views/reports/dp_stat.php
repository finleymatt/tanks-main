<?php
/**
 * DP Statistics Report
 *
 * @package Onestop
 * @subpackage views
 * @uses Report.php
 *
*/

$db = Database::instance();

if (!strtotime($start_date) || !strtotime($end_date))
	exit('Dates are not in valid format.');

$tank_types[] = 'B';  // add Both
$tank_type_list = implode( ',', array_map(function($val) { return("'{$val}'");}, $tank_types) );

$report_sql = "SELECT * FROM

-- NOV count
(select count(*) nov_count
from ustx.penalties P, ustx.tanks T, ustx.penalty_codes PC
where P.tank_id = T.id
and T.tank_type in ($tank_type_list)
and P.PENALTY_CODE = PC.CODE
and PC.PENALTY_LEVEL IN ('A','B')
and P.nov_date between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')) nov_count,

-- NOD count
(select count(*) nod_count
from ustx.penalties P, ustx.tanks T, ustx.penalty_codes PC
where P.tank_id = T.id
and t.tank_type in ($tank_type_list)
and P.PENALTY_CODE = PC.CODE
and PC.PENALTY_LEVEL IN ('A','B')
and P.nod_date between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')) nod_count,

-- NOIRT count
(select count(*) noirt_count
from ustx.penalties P, ustx.tanks T, ustx.penalty_codes PC
where P.tank_id = T.id
and t.tank_type in ($tank_type_list)
and P.PENALTY_CODE = PC.CODE
and PC.PENALTY_LEVEL IN ('A','B')
and P.noirt_date between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')) noirt_count,

-- NRTPID count
(select count(*) nrtpid_count
from ustx.penalties P, ustx.tanks T, ustx.penalty_codes PC
where P.tank_id = T.id
and t.tank_type in ($tank_type_list)
and P.PENALTY_CODE = PC.CODE
and PC.PENALTY_LEVEL IN ('A','B')
and P.redtag_placed_date between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')) nrtpid_count,

-- COC count
(select count(*) coc_count
from ustx.penalties P, ustx.tanks T, ustx.penalty_codes PC
where P.tank_id = T.id
and t.tank_type in ($tank_type_list)
and P.PENALTY_CODE = PC.CODE
and PC.PENALTY_LEVEL IN ('A','B')
and P.date_corrected between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')) coc_count,

-- LCAV count
(select count(*) lcav_count
from ustx.penalties P, ustx.tanks T, ustx.penalty_codes PC
where P.tank_id = T.id
and t.tank_type in ($tank_type_list)
and P.PENALTY_CODE = PC.CODE
and PC.PENALTY_LEVEL IN ('A','B')
and P.nov_date between TO_DATE(:start_date, 'mm/dd/yyyy') and TO_DATE(:end_date, 'mm/dd/yyyy')) lcav_count
";

$rs_arr = $db->query($report_sql, array(':start_date' => $start_date, ':end_date' => $end_date))->as_array();
if (in_array('A', $tank_types)) $tank_type_names[] = 'AST';
if (in_array('U', $tank_types)) $tank_type_names[] = 'UST';

if (count($rs_arr)) {
	$caf_report = new Report($output_format, 'Delivery Prohibition Statistics', "Dates: {$start_date} - {$end_date}\nTank Types: " . implode(', ', $tank_type_names));

	// main body --------------------------------------------
	foreach ($rs_arr as $row) {
		$caf_report->setRow(array(
			array('value' => 'Number of NOVs'),
			array('value' => $row['NOV_COUNT'])
		));
		$caf_report->setRow(array(
			array('value' => 'Number of NODs'),
			array('value' => $row['NOD_COUNT'])
		));
		$caf_report->setRow(array(
			array('value' => 'Number of NOIRTs'),
			array('value' => $row['NOIRT_COUNT'])
		));
		$caf_report->setRow(array(
			array('value' => 'Number of NRTPIDs'),
			array('value' => $row['NRTPID_COUNT'])
		));
		$caf_report->setRow(array(
			array('value' => 'Number of COCs'),
			array('value' => $row['COC_COUNT'])
		));
		$caf_report->setRow(array(
			array('value' => 'Number of LCAVs'),
			array('value' => $row['LCAV_COUNT'])
		));
	}

	$caf_report->setColumnSize(array(40, 20));
	$flag = $caf_report->output('delivery_prohibition_stat');
}

?>
