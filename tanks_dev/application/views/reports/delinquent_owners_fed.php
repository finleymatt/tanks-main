<?php
/**
 * Delinquent Owners - Federal
 * Created for Antonette's one-time report request
 *
 * @package Onestop
 * @subpackage views
 * @uses Report.php
 *
*/

$db = Database::instance();

$report_sql = "
SELECT 'MAIN' MAIN, O.id owner_id, O.owner_name, AMTS.p_assessed, AMTS.l_assessed, AMTS.i_assessed,
	AMTS.P_paid, AMTS.l_paid, AMTS.i_paid, AMTS.refund, AMTS.waivers, AMTS.owed,
	FACS.facility_id, FACS.facility_name, FACS.address1 facility_address1, FACS.city facility_city,
	FACS.ust_count, FACS.ast_count
FROM ustx.owners_mvw O,
	(select owner_id,
		sum(principal_assessment) p_assessed,
		sum(late_fee_assessment) l_assessed,
		sum(interest_assessment) i_assessed,

		sum(principal_payment) p_paid,
		sum(late_fee_payment) l_paid,
		sum(interest_payment) i_paid,

		(sum(principal_waiver) + sum(late_fee_waiver) + sum(interest_waiver)) waivers,
		sum(refund) refund,

		sum((principal_assessment - principal_waiver - principal_payment) +
			(late_fee_assessment - late_fee_waiver - late_fee_payment) +
			(interest_assessment - interest_waiver - interest_payment) + refund) owed
	from ustx.owner_transactions_view
	where fiscal_year between 1979 and 2016 /* must be > 1978 */
	group by owner_id) AMTS,
	
	(select DISTINCT f.owner_id, f.id facility_id, f.facility_name, f.address1,
		f.city, usts.ust_count, asts.ast_count
	from ustx.facilities_mvw f,
		(select g.facility_id fac_id, count(g.id) ust_count
			from ustx.tanks g
			where g.tank_status_code in (1,2) and g.tank_type = 'U'
			group by g.facility_id) usts,
		(select h.facility_id fac_id, count(h.id) ast_count
			from ustx.tanks h
			where h.tank_status_code in (1,2) and h.tank_type = 'A'
			group by h.facility_id) asts
	where f.id = usts.fac_id (+)
		and f.id = asts.fac_id (+)
		and (usts.ust_count <> 0 or asts.ast_count <> 0)) FACS

WHERE
	O.id = AMTS.owner_id (+)
	AND O.id = FACS.owner_id (+)
	--AND AMTS.owed > 0
	AND O.id in (805,10261,14136,15340,42676,75546,76305,76012,75546,14138,14296,14315,14323,14584,15126,15431,16055,16057,16337,16686,17208,44756,75546,14095,15247,15251,47522,13941,323,2223,14085,14771,16163,16562)
ORDER BY o.id, FACS.facility_id";

$rs_arr = $db->query($report_sql)->as_array();

$report = new Report('excel', 'All Federal Owners');

if (count($rs_arr)) {
	$report->setGroup($rs_arr, array(
		array('name' => 'MAIN',
			'header_func' => 'main_header',
			'footer_func' => 'main_footer'),
		array('name' => 'OWNER_ID'),
		array('name' => 'FACILITY_ID',
			'row_func' => 'facility_row')
	));
}

$report->setColumnSize(array(8, 30, 13, 13, 13, 13, 13, 13, 13, 13, 13, 8, 30, 30, 25, 6, 6));
$flag = $report->output('all_fed_owners');


function main_header(&$report, $row) {
	$report->setLabelRow(array('Own#', 'Owner Name', 'P Assessed', 'LF Assessed', 'Int Assessed', 'P Paid', 'LF Paid', 'Int Paid', 'Waivers', 'Refund', 'Owes', 'Fac#', 'Facility Name', 'Facility Address', 'Facility City', 'USTs', 'ASTs'));
	$report->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($report->row_num-1, $report->row_num-1);
}

function main_footer(&$report, $row, $params) {
	$report->setRow(array(
		array(
			'value' => "Grand Totals:",
			'colspan' => 2,
			'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT))
		),
		array(
			'value' => "=SUM(C{$params['group_row_start']}:C{$params['group_row_end']})",
			'style' => Report::$STYLE_MONEY
		),
		array(
			'value' => "=SUM(D{$params['group_row_start']}:D{$params['group_row_end']})",
			'style' => Report::$STYLE_MONEY
		),
		array(
			'value' => "=SUM(E{$params['group_row_start']}:E{$params['group_row_end']})",
			'style' => Report::$STYLE_MONEY
		),
		array(
			'value' => "=SUM(F{$params['group_row_start']}:F{$params['group_row_end']})",
			'style' => Report::$STYLE_MONEY
		),
		array(
			'value' => "=SUM(G{$params['group_row_start']}:G{$params['group_row_end']})",
			'style' => Report::$STYLE_MONEY
		),
		array(
			'value' => "=SUM(H{$params['group_row_start']}:H{$params['group_row_end']})",
			'style' => Report::$STYLE_MONEY
		),
		array(
			'value' => "=SUM(I{$params['group_row_start']}:I{$params['group_row_end']})",
			'style' => Report::$STYLE_MONEY
		),
		array(
			'value' => "=SUM(J{$params['group_row_start']}:J{$params['group_row_end']})",
			'style' => Report::$STYLE_MONEY
		),
		array(
			'value' => "=SUM(K{$params['group_row_start']}:K{$params['group_row_end']})",
			'style' => Report::$STYLE_MONEY
		)
	), FALSE, Report::$STYLE_TOTAL);
}

function facility_row(&$report, $row, $params) {
	if ($params['is_first_row']) // if first row, show owner info
		$cols = array( array('value' =>$row['OWNER_ID'], 'style' => Report::$STYLE_CENTER),
			$row['OWNER_NAME'],
			array('value' => $row['P_ASSESSED'], 'style' => Report::$STYLE_MONEY),
			array('value' => $row['L_ASSESSED'], 'style' => Report::$STYLE_MONEY),
			array('value' => $row['I_ASSESSED'], 'style' => Report::$STYLE_MONEY),
			array('value' => $row['P_PAID'], 'style' => Report::$STYLE_MONEY),
			array('value' => $row['L_PAID'], 'style' => Report::$STYLE_MONEY),
			array('value' => $row['I_PAID'], 'style' => Report::$STYLE_MONEY),
			array('value' => $row['WAIVERS'], 'style' => Report::$STYLE_MONEY),
			array('value' => $row['REFUND'], 'style' => Report::$STYLE_MONEY),
			array('value' => $row['OWED'], 'style' => Report::$STYLE_MONEY));
	else
		$cols = array('', '', '', '', '', '', '', '', '', '', '');

	array_push($cols, array('value' => $row['FACILITY_ID'], 'style' => Report::$STYLE_CENTER),
		$row['FACILITY_NAME'],
		$row['FACILITY_ADDRESS1'],
		$row['FACILITY_CITY'],
		array('value' => $row['UST_COUNT'], 'style' => Report::$STYLE_CENTER),
		array('value' => $row['AST_COUNT'], 'style' => Report::$STYLE_CENTER));

	$report->setRow($cols, FALSE);
}

?>
