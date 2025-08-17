<?php
/**
 * All Storage Tanks Status
 *
 * @package Onestop
 * @subpackage views
 * @uses Report.php
 *
*/
ini_set('max_execution_time', 240); // 4 minutes

$db = Database::instance();

$tank_status_codes = ($get_active_only ? '1, 2, 12' : '1, 2, 4, 5, 11, 12');

$report_sql = "SELECT O.id owner_id, O.owner_name, F.id FID, F.facility_name, F.address1, F.address2, F.city, F.zip, CT.county,
		(select count(*) from ustx.tanks T where (T.facility_id = F.id) and (T.tank_type = 'A') and (T.tank_status_code = 1)) ast_inuse_count,
		(select count(*) from ustx.tanks T where (T.facility_id = F.id) and (T.tank_type = 'A') and (T.tank_status_code = 2)) ast_tempout_count,
		(select count(*) from ustx.tanks T where (T.facility_id = F.id) and (T.tank_type = 'A') and (T.tank_status_code = 4)) ast_sold_count,
		(select count(*) from ustx.tanks T where (T.facility_id = F.id) and (T.tank_type = 'A') and (T.tank_status_code = 5)) ast_removed_count,
		(select count(*) from ustx.tanks T where (T.facility_id = F.id) and (T.tank_type = 'A') and (T.tank_status_code = 11)) ast_nodata_count,
		(select count(*) from ustx.tanks T where (T.facility_id = F.id) and (T.tank_type = 'A') and (T.tank_status_code = 12)) ast_exempt_count,
    
		(select count(*) from ustx.tanks T where (T.facility_id = F.id) and (T.tank_type = 'U') and (T.tank_status_code = 1)) ust_inuse_count,
		(select count(*) from ustx.tanks T where (T.facility_id = F.id) and (T.tank_type = 'U') and (T.tank_status_code = 2)) ust_tempout_count,
		(select count(*) from ustx.tanks T where (T.facility_id = F.id) and (T.tank_type = 'U') and (T.tank_status_code = 4)) ust_sold_count,
		(select count(*) from ustx.tanks T where (T.facility_id = F.id) and (T.tank_type = 'U') and (T.tank_status_code = 5)) ust_removed_count,
		(select count(*) from ustx.tanks T where (T.facility_id = F.id) and (T.tank_type = 'U') and (T.tank_status_code = 11)) ust_nodata_count,
		(select count(*) from ustx.tanks T where (T.facility_id = F.id) and (T.tank_type = 'U') and (T.tank_status_code = 12)) ust_exempt_count
	FROM USTX.facilities_mvw F, USTX.owners_mvw O, USTX.cities CT
	WHERE
		(F.owner_id = O.id)
		AND (F.city = CT.city)
		AND ( (select count(*) from ustx.tanks T where (T.facility_id = F.id) and (T.tank_type in ('A', 'U')) and (T.tank_status_code in ({$tank_status_codes}))) > 0 )
		AND (CT.county = nvl(:county, CT.county))
	ORDER BY F.ID";

$rs_arr = $db->query($report_sql, array(':county' => $county))->as_array();

if (count($rs_arr)) {
	$report = new Report($output_format, 'All Storage Tanks', "County: " . ($county ? $county : 'All') . "\nFacilities with Active Tanks Only?: ". ($get_active_only ? 'Yes' : 'No'));
	// labels -----------------------------------------------
	$status_header_style = array( 'fill' => array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('argb' => 'FF3355AA') ) );
	$report->setLabelRow(array(
		0 => array('colspan' => 9),
		9 => array(
			'value' => 'In Use',
			'colspan' => 2, 'style' => $status_header_style),
		11 => array(
			'value' => 'Temp Out',
			'colspan' => 2, 'style' => $status_header_style),
		13 => array(
			'value' => 'Exempt',
			'colspan' => 2, 'style' => $status_header_style),
		15 => array(
			'value' => 'Sold',
			'colspan' => 2, 'style' => $status_header_style),
		17 => array(
			'value' => 'Removed',
			'colspan' => 2, 'style' => $status_header_style),
		19 => array(
			'value' => 'No Data',
			'colspan' => 2, 'style' => $status_header_style)
	));
	$report->setLabelRow( array('FID', 'Facility', 'Street', 'Street 2', 'City', 'County', 'Zip', 'Owner ID', 'Owner Name', 'AST', 'UST', 'AST', 'UST', 'AST', 'UST', 'AST', 'UST', 'AST', 'UST', 'AST', 'UST'), array('style' => array('alignment' => array('wrap' => TRUE))) );
	$report->getActiveSheet()->getRowDimension($report->row_num - 1)->setRowHeight(25);
	$report->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($report->row_num-2, $report->row_num-1);
	
	// main body --------------------------------------------
	$group_row_start = $report->row_num;
	foreach ($rs_arr as $row) {
		$report->setRow(array(
			array('value' => $row['FID']),
			array('value' => $row['FACILITY_NAME']),
			array('value' => $row['ADDRESS1']),
			array('value' => $row['ADDRESS2']),
			array('value' => $row['CITY']),
			array('value' => $row['COUNTY']),
			array('value' => $row['ZIP']),
			array('value' => $row['OWNER_ID']),
			array('value' => $row['OWNER_NAME'], 'style' => array('alignment' => array('wrap' => TRUE))),
			array('value' => $row['AST_INUSE_COUNT']),
			array('value' => $row['UST_INUSE_COUNT']),
			array('value' => $row['AST_TEMPOUT_COUNT']),
			array('value' => $row['UST_TEMPOUT_COUNT']),
			array('value' => $row['AST_EXEMPT_COUNT']),
			array('value' => $row['UST_EXEMPT_COUNT']),
			array('value' => $row['AST_SOLD_COUNT']),
			array('value' => $row['UST_SOLD_COUNT']),
			array('value' => $row['AST_REMOVED_COUNT']),
			array('value' => $row['UST_REMOVED_COUNT']),
			array('value' => $row['AST_NODATA_COUNT']),
			array('value' => $row['UST_NODATA_COUNT'])
		));
	}

	// tank totals ------------------------------------------------
	$group_row_end = $report->row_num - 1;
	$report->setRow(array(
		array(
			'style' => Report::$STYLE_TOTAL,
			'colspan' => 8
		),
		8 => array('value' => 'Totals:', 'style' => REPORT::$STYLE_TOTAL),
		array(
			'value' => "=SUM(J{$group_row_start}:J{$group_row_end})",
			'style' => Report::$STYLE_TOTAL
		),
		array(
			'value' => "=SUM(K{$group_row_start}:K{$group_row_end})",
			'style' => Report::$STYLE_TOTAL
		),
		array(
			'value' => "=SUM(L{$group_row_start}:L{$group_row_end})",
			'style' => Report::$STYLE_TOTAL
		),
		array(
			'value' => "=SUM(M{$group_row_start}:M{$group_row_end})",
			'style' => Report::$STYLE_TOTAL
		),
		array(
			'value' => "=SUM(N{$group_row_start}:N{$group_row_end})",
			'style' => Report::$STYLE_TOTAL
		),
		array(
			'value' => "=SUM(O{$group_row_start}:O{$group_row_end})",
			'style' => Report::$STYLE_TOTAL
		),
		array(
			'value' => "=SUM(P{$group_row_start}:P{$group_row_end})",
			'style' => Report::$STYLE_TOTAL
		),
		array(
			'value' => "=SUM(Q{$group_row_start}:Q{$group_row_end})",
			'style' => Report::$STYLE_TOTAL
		),
		array(
			'value' => "=SUM(R{$group_row_start}:R{$group_row_end})",
			'style' => Report::$STYLE_TOTAL
		),
		array(
			'value' => "=SUM(S{$group_row_start}:S{$group_row_end})",
			'style' => Report::$STYLE_TOTAL
		),
		array(
			'value' => "=SUM(T{$group_row_start}:T{$group_row_end})",
			'style' => Report::$STYLE_TOTAL
		),
		array(
			'value' => "=SUM(U{$group_row_start}:U{$group_row_end})",
			'style' => Report::$STYLE_TOTAL
		)
	), FALSE);
	
	$report->setColumnSize(array(8, 35, 25, 25, 15, 15, 8, 8, 40, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6));
	$flag = $report->output('all_storage_tanks');
}

