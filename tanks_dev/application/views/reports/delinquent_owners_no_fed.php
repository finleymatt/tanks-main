<?php
/**
 * Delinquent Owners Excluding Federal
 * imported from Oracle Report, "Delinquent Owners Excluding Federal"
 *
 * @package Onestop
 * @subpackage views
 * @uses Report.php
 *
*/

$db = Database::instance();

$report_sql = "SELECT DISTINCT 'MAIN' MAIN, e.id owner_id, e.owner_name, e.address1||', '||e.city||', '||e.state||' '||e.zip owner_address, 
	fees.sb assessed, fees.sc paid, fees.ow owed, fac.fi facility_id, fac.fn facility_name, 
	fac.fa||', '||fac.fc facility_address, staff.staff_name, fac.ft UST, fac.ft2 AST
FROM ustx.owners_mvw e, 
	(
		SELECT owner_id,
			sum((principal_assessment - principal_waiver) +
				(late_fee_assessment - late_fee_waiver) +
				(interest_assessment - interest_waiver)) sb,
			sum(principal_payment + late_fee_payment + interest_payment - refund) sc,
			sum((principal_assessment - principal_waiver - principal_payment) +
				(late_fee_assessment - late_fee_waiver - late_fee_payment) +
				(interest_assessment - interest_waiver - interest_payment) + refund) ow
		FROM ustx.owner_transactions_view
		WHERE fiscal_year > 1978
		GROUP BY owner_id
	) fees,
	(
		SELECT DISTINCT t.owner_id, t.facility_id fi, f.facility_name fn,
			f.address1 fa, f.city fc, ust_count.tc ft, ast_count.tc ft2
		FROM ustx.tanks t, ustx.facilities_mvw f, 
			(SELECT g.facility_id fac_id, COUNT(g.id) tc
				FROM    ustx.tanks g
				WHERE g.tank_status_code IN (1,2)
				AND g.tank_type = 'U'
				GROUP BY g.facility_id) ust_count,
			(SELECT h.facility_id fac_id, COUNT(h.id) tc
				FROM    ustx.tanks h
				WHERE h.tank_status_code IN (1,2)
				AND h.tank_type = 'A'
				GROUP BY h.facility_id) ast_count
		WHERE t.facility_id = f.id
			AND f.id = ust_count.fac_id (+)
			AND f.id = ast_count.fac_id (+)
			AND (ust_count.tc <> 0
			OR ast_count.tc <> 0)
			AND t.tank_status_code IN (1,2)
	) fac,
	(
		SELECT DISTINCT i.facility_id, staff.first_name||' '||staff.last_name staff_name
		FROM ustx.inspections i, ustx.staff
		WHERE staff.code = i.staff_code
			and date_inspected = (select max(date_inspected)
                      from ustx.inspections
                      where facility_id = i.facility_id)
	) staff
WHERE
	-- exclude fed owners
	e.id not in (805,10261,14136,15340,42676,75546,76305,76012,75546,14138,14296,14315,14323,14584,15126,15431,16055,16057,16337,16686,17208,44756,75546,14095,15247,15251,47522,13941,323,2223,14085,14771,16163,16562)
	and e.id in
	(
		select owner_id
		from ustx.owner_transactions_view
		where fiscal_year > 1978
		group by owner_id
		having sum((principal_assessment - principal_waiver) +
			(late_fee_assessment - late_fee_waiver) +
			(interest_assessment - interest_waiver) -
			(principal_payment + late_fee_payment + interest_payment - refund))>0
	)
	and e.id = fees.owner_id
	and e.id = fac.owner_id
	and staff.facility_id (+) = fac.fi

UNION

SELECT DISTINCT 'MAIN' MAIN, e.id, e.owner_name, e.address1||', '||e.city||', '||e.state||' '||e.zip, 
	fees.sb, fees.sc, fees.ow, to_number(null), null, null, null, to_number(null), to_number(null)
FROM ustx.owners_mvw e, 
	(
		SELECT owner_id,
			sum((principal_assessment - principal_waiver) +
				(late_fee_assessment - late_fee_waiver) +
				(interest_assessment - interest_waiver)) sb,
			sum(principal_payment + late_fee_payment + interest_payment - refund) sc,
			sum((principal_assessment - principal_waiver - principal_payment) +
				(late_fee_assessment - late_fee_waiver - late_fee_payment) +
				(interest_assessment - interest_waiver - interest_payment) + refund) ow
		FROM ustx.owner_transactions_view
		WHERE fiscal_year > 1978
		GROUP BY owner_id
	) fees
WHERE e.id not in (805,10261,14136,15340,42676,75546)
	AND e.id in (
		select owner_id
		from ustx.owner_transactions_view
		where fiscal_year > 1978
		group by owner_id
		having sum((principal_assessment - principal_waiver) +
			(late_fee_assessment - late_fee_waiver) +
			(interest_assessment - interest_waiver) -
			(principal_payment + late_fee_payment + interest_payment - refund))>0
	)
	AND not exists (
		SELECT t.owner_id
		FROM ustx.tanks t, ustx.facilities_mvw f, 
			(SELECT g.facility_id fac_id, COUNT(g.id) tc
				FROM    ustx.tanks g
				WHERE g.tank_status_code IN (1,2)
				AND g.tank_type = 'U'
				GROUP BY g.facility_id) ust_count,
			(SELECT h.facility_id fac_id, COUNT(h.id) tc
				FROM    ustx.tanks h
				WHERE h.tank_status_code IN (1,2)
				AND h.tank_type = 'A'
				GROUP BY h.facility_id) ast_count
		WHERE t.facility_id = f.id
			AND e.id = t.owner_id
			AND f.id = ust_count.fac_id (+)
			AND f.id = ast_count.fac_id (+)
			AND (ust_count.tc <> 0
			OR ast_count.tc <> 0)
			AND t.tank_status_code IN (1,2)
	)
AND e.id = fees.owner_id
ORDER BY owner_id, facility_id";

$rs_arr = $db->query($report_sql)->as_array();

$report = new Report($output_format, 'All Delinquent Owners Excluding Federal Owners');

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

$report->setColumnSize(array(8, 30, 30, 13, 13, 13, 8, 30, 30, 20, 6, 6));
$flag = $report->output('delinquent_owners_no_fed');


function main_header(&$report, $row) {
	$report->setLabelRow(array('Own#', 'Owner Name', 'Owner Address', 'Assessed', 'Paid', 'Owes', 'Fac#', 'Facility Name', 'Facility Address', 'Staff', 'USTs', 'ASTs'));
	$report->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($report->row_num-1, $report->row_num-1);
}

function main_footer(&$report, $row, $params) {
	$report->setRow(array(
		array(
			'value' => "Grand Totals:",
			'colspan' => 3,
			'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT))
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
		)
	), FALSE, Report::$STYLE_TOTAL);
}

function facility_row(&$report, $row, $params) {
	if ($params['is_first_row']) // if first row, show owner info
		$cols = array( array('value' =>$row['OWNER_ID'], 'style' => Report::$STYLE_CENTER),
			$row['OWNER_NAME'],
			$row['OWNER_ADDRESS'],
			array('value' => $row['ASSESSED'], 'style' => Report::$STYLE_MONEY),
			array('value' => $row['PAID'], 'style' => Report::$STYLE_MONEY),
			array('value' => $row['OWED'], 'style' => Report::$STYLE_MONEY));
	else
		$cols = array('', '', '', '', '', '');

	array_push($cols, array('value' => $row['FACILITY_ID'], 'style' => Report::$STYLE_CENTER),
		$row['FACILITY_NAME'],
		$row['FACILITY_ADDRESS'],
		$row['STAFF_NAME'],
		array('value' => $row['UST'], 'style' => Report::$STYLE_CENTER),
		array('value' => $row['AST'], 'style' => Report::$STYLE_CENTER));

	$report->setRow($cols, FALSE);
}

?>
