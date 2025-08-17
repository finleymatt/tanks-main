<?php
/**
 * Tanks Installed report
 * This is a new report requested by Bertha on Oct 2013.
 *
 * @package Onestop
 * @subpackage views
 * @uses Report.php
 *
*/

$db = Database::instance();

$start_date = '30-JUN-' . ($fy - 1);
$end_date = "01-JUL-{$fy}";
$report_sql = "
SELECT 'MAIN' MAIN, O.id owner_id, O.owner_name, F.id facility_id, F.facility_name, T.id tank_id, T.tank_type, H.history_date, '{$fy}' fy
FROM ustx.tank_history H
	LEFT OUTER JOIN ustx.tanks T ON H.tank_id = T.id
	LEFT OUTER JOIN ustx.facilities_mvw F ON T.facility_id = F.id
	LEFT OUTER JOIN ustx.owners_mvw O ON H.owner_id = O.id
WHERE H.history_code = 'I'
	AND H.history_date BETWEEN :start_date AND :end_date
ORDER BY O.id, F.id, T.id";

$rs_arr = $db->query($report_sql, array('start_date'=>$start_date, 'end_date'=>$end_date))->as_array();

$report = new Report($output_format, 'Tanks Installed', "FY: {$fy}");
$report->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
$report->getActiveSheet()->getPageSetup()->setFitToWidth(1);
$report->getActiveSheet()->getPageSetup()->setFitToHeight(0);

if (count($rs_arr)) {
	$report->setGroup($rs_arr, array(
		array('name' => 'MAIN',
			'footer_func' => 'main_footer'),
		array('name' => 'OWNER_ID',
			'header_func' => 'owner_header',
			'footer_func' => 'owner_footer'),
		array('name' => 'TANK_ID',
			'row_func' => 'tank_row')
	));
}

$report->setColumnSize(array(14, 50, 14, 14, 14));
$flag = $report->output('tanks_installed');


function main_footer(&$report, $row, $params) {
	$report->setRow( array(
		array(
			'value' => 'Total Tank Count:',
			'colspan' => 2,
			'style' => Report::$STYLE_RIGHT
		),
		2 => array(
			'value' => "=COUNT(A{$params['group_row_start']}:A{$params['group_row_end']})"
		)
	), FALSE, Report::$STYLE_TOTAL);
}

function owner_header(&$report, $row) {
	$report->setLabelRow(array(
		"FY:{$row['FY']}",
		array('colspan' => 4,
			'value' => "{$row['OWNER_NAME']} ({$row['OWNER_ID']})"
		)
	), Report::$STYLE_LABEL_2);

	$report->setLabelRow(array('Facility ID', 'Facility Name', 'Tank ID', 'Tank Type', 'Date Installed'));
}

function owner_footer(&$report, $row, $params) {
	$report->setRow(array(
		array(
			'value' => 'Owner Tank Count:',
			'colspan' => 2,
			'style' => Report::$STYLE_RIGHT
		),
		2 => array(
			'value' => "=COUNT(A{$params['group_row_start']}:A{$params['group_row_end']})"
		)
	), FALSE, Report::$STYLE_TOTAL);

	$report->setBlankRow();
}


function tank_row(&$report, $row) {
	$report->setRow(array(
		array('value' => $row['FACILITY_ID'], 'style' => Report::$STYLE_CENTER),
		array('value' => $row['FACILITY_NAME']),
		array('value' => $row['TANK_ID']),
		array('value' => $row['TANK_TYPE'], 'style' => Report::$STYLE_CENTER),
		array('value' => Report::TO_DATE($row['HISTORY_DATE']), 'style' => array_merge(Report::$STYLE_DATE, Report::$STYLE_CENTER))
	), FALSE);
}
?>
