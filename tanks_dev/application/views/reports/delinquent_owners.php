<?php
/**
 * Delinquent Owners
 * imported from Oracle Report, "Delinquent Owners"
 *
 * @package Onestop
 * @subpackage views
 * @uses Report.php
 *
*/

$db = Database::instance();

if (!$fy)
	exit('Required fields not entered.');

$report_sql = "SELECT 'MAIN' MAIN, O.id owner_id, O.owner_name, AMTS.assessed, AMTS.paid, AMTS.owe,
	FACS.facility_id, FACS.facility_name, FACS.address1, FACS.city, FACS.ust_count, FACS.ast_count
FROM ustx.owners_mvw O,

	(select owner_id, sum((principal_assessment - principal_waiver) +
			(late_fee_assessment - late_fee_waiver) +
			(interest_assessment - interest_waiver)) assessed,
		sum(principal_payment + late_fee_payment + interest_payment - refund) paid,
		sum((principal_assessment - principal_waiver - principal_payment) +
			(late_fee_assessment - late_fee_waiver - late_fee_payment) +
			(interest_assessment - interest_waiver - interest_payment) + refund) owe
	from ustx.owner_transactions_view
	where fiscal_year between :fy_start and :fy_end /* must be > 1978 */
	group by owner_id) AMTS,
	
	(select DISTINCT t.owner_id, t.facility_id, f.facility_name, f.address1,
		f.city, usts.ust_count, asts.ast_count
	from ustx.tanks t, ustx.facilities_mvw f,
		(select g.facility_id fac_id, count(g.id) ust_count
			from ustx.tanks g
			where g.tank_status_code in (1,2) and g.tank_type = 'U'
			group by g.facility_id) usts,
		(select h.facility_id fac_id, count(h.id) ast_count
			from ustx.tanks h
			where h.tank_status_code in (1,2) and h.tank_type = 'A'
			group by h.facility_id) asts
	where t.facility_id = f.id
		and f.id = usts.fac_id (+)
		and f.id = asts.fac_id (+)
		and (usts.ust_count <> 0 or asts.ast_count <> 0)
		and t.tank_status_code in (1,2)) FACS
WHERE 
	O.id = AMTS.owner_id
	AND O.id = FACS.owner_id (+)
	AND AMTS.owe > 0
	/****AND O.id in (
		select distinct owner_id
		from ustx.owner_transactions_view
		where ((principal_assessment - principal_waiver) + (late_fee_assessment - late_fee_waiver) + (interest_assessment - interest_waiver)
			> (principal_payment + late_fee_payment + interest_payment - refund))
			and fiscal_year > 1978)*****/
ORDER BY owe desc";

$rs_arr = $db->query($report_sql, array(':fy_start' => ($include_prior_years ? 1979 : $fy), ':fy_end' => $fy))->as_array();

$report = new Report($output_format, 'Delinquent Owners with Registered Tanks Who Owe Fees', "FY: {$fy}\n" . ($include_prior_years ? 'Includes Prior Years' : ''));

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

$report->setColumnSize(array(8, 30, 13, 13, 13, 8, 30, 30, 25, 6, 6));
$flag = $report->output('delinquent_owners');


function main_header(&$report, $row) {
	$report->setLabelRow(array('Own#', 'Owner Name', 'Assessed', 'Paid', 'Owes', 'Fac#', 'Facility Name', 'Facility Address', 'City', 'USTs', 'ASTs'));
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
		)
	), FALSE, Report::$STYLE_TOTAL);
}

function facility_row(&$report, $row, $params) {
	if ($params['is_first_row']) // if first row, show owner info
		$cols = array( array('value' =>$row['OWNER_ID'], 'style' => Report::$STYLE_CENTER),
			$row['OWNER_NAME'],
			array('value' => $row['ASSESSED'], 'style' => Report::$STYLE_MONEY),
			array('value' => $row['PAID'], 'style' => Report::$STYLE_MONEY),
			array('value' => $row['OWE'], 'style' => Report::$STYLE_MONEY));
	else
		$cols = array('', '', '', '', '');

	array_push($cols, array('value' => $row['FACILITY_ID'], 'style' => Report::$STYLE_CENTER),
		$row['FACILITY_NAME'],
		$row['ADDRESS1'],
		$row['CITY'],
		array('value' => $row['UST_COUNT'], 'style' => Report::$STYLE_CENTER),
		array('value' => $row['AST_COUNT'], 'style' => Report::$STYLE_CENTER));

	$report->setRow($cols, FALSE);
}

?>
