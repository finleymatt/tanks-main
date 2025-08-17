<?php
/**
 * Non-Fuel Tanks
 *
 * @package Onestop
 * @subpackage views
 * @uses Report.php
 *
*/

$db = Database::instance();

$report_sql = "-- retrieves records per tank
SELECT 'MAIN' MAIN, T.owner_id, O.owner_name, T.facility_id, F.facility_name, F.address1, F.address2, F.city, F.state, F.zip,
	T.id tank_id, T.tank_type,
	(select max(tank_detail_code) from ustx.tank_details TD -- if the tank has many codes in the list
		where TD.tank_id = T.id
			and TD.tank_detail_code in ('B05','B06','B07','B08','B11','B31','B79','B83')) tank_detail_code
FROM ustx.tanks T, ustx.owners_mvw O, ustx.facilities_mvw F
WHERE
	T.owner_id = O.id
	AND T.facility_id = F.id
	AND T.tank_status_code = 1 -- only active tanks
	AND ( -- tank has at least one nonfuel detail code, or has no content code B
		(select count(*) from ustx.tank_details TD 
		where TD.tank_id = T.id
			and TD.tank_detail_code in ('B05','B06','B07','B08','B11','B31','B79','B83')) > 0
		
		or 
		
		(select count(*) from ustx.tank_details TD2, ustx.tank_detail_codes TDC
			where TD2.tank_id = T.id
				and TD2.tank_detail_code = TDC.code 
				and TDC.tank_info_code = 'B') = 0
	)
ORDER BY OWNER_ID, FACILITY_ID, TANK_ID";

$rs_arr = $db->query($report_sql)->as_array();

$report = new Report($output_format, 'Non-Fuel Tanks', 'Active Tanks Only');

if (count($rs_arr)) {
	$report->setGroup($rs_arr, array(
		array('name' => 'MAIN',
			'footer_func' => 'main_footer'),
		array('name' => 'OWNER_ID',
			'header_func' => 'owner_header',
			'footer_func' => 'owner_footer'),
		array('name' => 'FACILITY_ID',
			'header_func' => 'facility_header'),
		array('name' => 'TANK_ID',
			'row_func' => 'tank_row')
	));
}

$report->setColumnSize(array(8, 35, 8, 35, 25, 25, 18, 7, 8, 8, 8, 12));
$flag = $report->output('nonfuel_tanks');


function main_footer(&$report, $row, $params) {
	$grand_total_start = $report->row_num;
	$report->setRow(array(
		array(
			'value' => "AST Total:",
			'colspan' => 11,
			'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT))
		),
		11 => "=SUMIF(A{$params['group_row_start']}:A{$params['group_row_end']}, \"=AST Count:\", H{$params['group_row_start']}:H{$params['group_row_end']})"
	), FALSE, Report::$STYLE_TOTAL);

	$report->setRow(array(
		array(
			'value' => "UST Total:",
			'colspan' => 11,
			'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT))
		),
		11 => "=SUMIF(I{$params['group_row_start']}:I{$params['group_row_end']}, \"=UST Count:\", L{$params['group_row_start']}:L{$params['group_row_end']})"
	), FALSE, Report::$STYLE_TOTAL);
	$grand_total_end = $report->row_num - 1;

	$report->setRow(array(
		array(
			'value' => "Grand Total:",
			'colspan' => 11,
			'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT))
		),
		11 => array(
			'value' => "=SUM(L{$grand_total_start}:L{$grand_total_end})"
		)
	), FALSE, Report::$STYLE_TOTAL);
}

function owner_header(&$report, $row) {
	$report->setLabelRow( array('Owner ID', 'Owner Name', 'F ID', 'F Name', 'F Address1', 'F Address2', 'F City', 'F State', 'F Zip', 'Tank ID', 'Tank Type', 'Tank Content'), array('style' => array('alignment' => array('wrap' => TRUE))) );

	$report->setRow(array(
		$row['OWNER_ID'],
		$row['OWNER_NAME']
	), FALSE);
}

function owner_footer(&$report, $row, $params) {
	$group_row_start = $params['group_row_start'] + 1;
	$report->setRow(array(
		array(
			'value' => "AST Count:",
			'colspan' => 7,
			'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT))
		),
		7 => "=COUNTIF(K{$group_row_start}:K{$params['group_row_end']}, \"A\")",
		array(
			'value' => "UST Count:",
			'colspan' => 3,
			'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT))
		),
		11 => "=COUNTIF(K{$group_row_start}:K{$params['group_row_end']}, \"U\")"
	), FALSE, Report::$STYLE_TOTAL);

	$report->setBlankRow();
}

function facility_header(&$report, $row) {
	$report->setRow(array(
		2 => $row['FACILITY_ID'],
		$row['FACILITY_NAME'],
		$row['ADDRESS1'],
		$row['ADDRESS2'],
		array('value' => $row['CITY'], 'style' => Report::$STYLE_TEXT),
		$row['STATE'],
		$row['ZIP']
	), FALSE);
}

function tank_row(&$report, $row) {
	$report->setRow(array(
		9 => $row['TANK_ID'],
		array('value' => $row['TANK_TYPE'], 'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER))),
		($row['TANK_DETAIL_CODE'] ? $row['TANK_DETAIL_CODE'] : 'not specified')
	), FALSE);
}

?>
