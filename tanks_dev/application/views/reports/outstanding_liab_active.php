<?php
/**
 * Outstanding Liabilities for Active Tanks
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

$no_prior_sql = ($include_prior_years ? '' : 'and fiscal_year = :fy_end');
$report_sql = "SELECT 'MAIN' MAIN, O.id owner_id, O.owner_name, FEES.fiscal_year, FEES.assessed, FEES.paid, FEES.owe
FROM ustx.owners_mvw O,
	(
		select owner_id, fiscal_year,
			sum((principal_assessment - principal_waiver) +
				(late_fee_assessment - late_fee_waiver) +
				(interest_assessment - interest_waiver)) assessed,
			sum(principal_payment + late_fee_payment + interest_payment - refund) paid,
			sum((principal_assessment - principal_waiver - principal_payment) +
				(late_fee_assessment - late_fee_waiver - late_fee_payment) +
				(interest_assessment - interest_waiver - interest_payment) + refund) owe
		from ustx.owner_transactions_view
		where fiscal_year between :fy_start and :fy_end
		group by owner_id, fiscal_year
	) FEES
WHERE
	O.id = FEES.owner_id -- Only looks per FY, Does not combine all FYs.
	AND FEES.owe > 0
	AND (select count(*)
		from ustx.facilities_mvw f, ustx.tanks t
		where (t.facility_id = f.id) and (f.owner_id = O.id)
			and (t.tank_status_code = 1)) > 0
	AND O.id in (  -- look across all FYs, not just the selected, to find if owner owes anything
		select owner_id
		from ustx.owner_transactions_view
		where fiscal_year > 1978
			{$no_prior_sql}
		group by owner_id
		having sum((principal_assessment - principal_waiver) +
			(late_fee_assessment - late_fee_waiver) +
			(interest_assessment - interest_waiver) -
			(principal_payment + late_fee_payment + interest_payment - refund)) >0
	)
ORDER BY owner_name, fiscal_year";

$rs_arr = $db->query($report_sql, array(':fy_start' => ($include_prior_years ? 1978 : $fy), ':fy_end' => $fy))->as_array();

$report = new Report($output_format, 'Outstanding Liabilities for Active Tanks', "FY: {$fy}\n" . ($include_prior_years ? 'Includes Prior Years' : ''));

if (count($rs_arr)) {
	$report->setGroup($rs_arr, array(
		array('name' => 'MAIN',
			'footer_func' => 'main_footer'),
		array('name' => 'OWNER_ID',
			'header_func' => 'owner_header',
			'footer_func' => 'owner_footer'),
		array('name' => 'FISCAL_YEAR',
			'row_func' => 'fiscal_year_row')
	));
}

$report->setColumnSize(array(10, 40, 8, 13, 13, 13));
$flag = $report->output('outstanding_liab_active');


function main_footer(&$report, $row, $params) {
	$report->setRow(array(
		array(
			'value' => "Grand Totals:",
			'colspan' => 3,
			'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT))
		),
		3 => array(
			'value' => "=SUMIF(A{$params['group_row_start']}:A{$params['group_row_end']}, \"=Owner Totals:\", D{$params['group_row_start']}:D{$params['group_row_end']})",
			'style' => Report::$STYLE_MONEY
		),
		array(
			'value' => "=SUMIF(A{$params['group_row_start']}:A{$params['group_row_end']}, \"=Owner Totals:\", E{$params['group_row_start']}:E{$params['group_row_end']})",
			'style' => Report::$STYLE_MONEY
		),
		array(
			'value' => "=SUMIF(A{$params['group_row_start']}:A{$params['group_row_end']}, \"=Owner Totals:\", F{$params['group_row_start']}:F{$params['group_row_end']})",
			'style' => Report::$STYLE_MONEY
		)
	), FALSE, Report::$STYLE_TOTAL);
}

function owner_header(&$report, $row) {
	$report->setLabelRow( array('Owner ID', 'Owner Name', 'FY', 'Assessed', 'Paid', 'Owes'), array('style' => array('alignment' => array('wrap' => TRUE))) );

	$report->setRow(array(
		$row['OWNER_ID'],
		$row['OWNER_NAME']
	), FALSE);
}

function owner_footer(&$report, $row, $params) {
	$report->setRow(array(
		array(
			'value' => "Owner Totals:",
			'colspan' => 3,
			'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT))
		),
		3 => array(
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

	$report->setBlankRow();
}

function fiscal_year_row(&$report, $row) {
	$report->setRow(array(
		2 => $row['FISCAL_YEAR'],
		array('value' => $row['ASSESSED'], 'style' => Report::$STYLE_MONEY),
		array('value' => $row['PAID'], 'style' => Report::$STYLE_MONEY),
		array('value' => $row['OWE'], 'style' => Report::$STYLE_MONEY)
	), FALSE);
}

?>
