<?php
/**
 * Facility Score Report
 * This report displays calculated risk scores gained from the GoNM project (GISST_DATA)
 *
 * @package Onestop
 * @subpackage views
 * @uses Report.php
 *
*/

$db = Database::instance();

// GISST_DATA only stores inspector's last name and not staff code
if ($inspector_lname) {
	$inspector_where = ' WHERE INSPECTOR = :inspector_lname';
	$bound_vars = array(':inspector_lname' => $inspector_lname);
}
else {
	$inspector_where = '';
	$bound_vars = array();
}


$report_sql = "
SELECT 'MAIN' MAIN_GROUP, S.* FROM (
	select facility.*, nvl(inspector, 'N/A') inspector_name,
		round(((score + number_tanks_score + lust_site_score + facility_history_score) / 17), 2) facility_criteria_score
	from gisst_data.facility
	{$inspector_where}
) S
ORDER BY S.inspector, S.facility_criteria_score DESC
";

$rs_arr = $db->query($report_sql, $bound_vars)->as_array();

$report = new Report($output_format, 'Facility Score');

if (count($rs_arr)) {
	$report->setGroup($rs_arr, array(
		array('name' => 'MAIN_GROUP',
			'footer_func' => 'main_footer'),
		array('name' => 'INSPECTOR_NAME',
			'header_func' => 'inspector_header',
			'footer_func' => 'inspector_footer'),
		array('name' => 'FACILITY_ID',
			'row_func' => 'facility_row')
	));
}

$report->setColumnSize(array(10, 15, 10, 25, 20, 20, 15, 10, 10, 10, 10, 15, 15, 10, 10));
$flag = $report->output('facility_score');


function main_footer(&$report, $row, $params) {
	$report->setRow( array(
		array('value' => 'Grand Facility Count:', 'colspan' => 2),
		2 => "=SUMIF(A{$params['group_row_start']}:A{$params['group_row_end']}, \"=Inspector's Facility Count:\", C{$params['group_row_start']}:C{$params['group_row_end']})",
	), FALSE, Report::$STYLE_TOTAL);
}

function inspector_header(&$report, $row) {
	$report->setLabelRow( array( array('value'=>"Inspector: {$row['INSPECTOR_NAME']}", 'colspan'=>15)) );
	$report->setLabelRow( array('Fac Score', 'Last Inspected Date', 'F ID', 'Facility Name', 'F Address', 'F City', 'F County', 'F State', 'F Zip', 'Phone', 'Owner ID', 'Owner', 'Operator', 'Latitude', 'Longitude') );
}

function inspector_footer(&$report, $row, $params) {
	$report->setRow( array(
		array('value' => "Inspector's Facility Count:", 'colspan' => 2),
		2 => "=COUNT(A{$params['group_row_start']}:A{$params['group_row_end']})"
	), FALSE, Report::$STYLE_TOTAL);
	$report->setBlankRow();
}

function facility_row(&$report, $row) {
	$report->setRow(array(
		array('value' => $row['FACILITY_CRITERIA_SCORE']),
		array('value' => Report::TO_DATE($row['LAST_DATE_INSPECTED']), 'style' => Report::$STYLE_DATE),
		array('value' => $row['FACILITY_ID']),
		array('value' => $row['FACILITY_NAME']),
		array('value' => $row['FACILITY_ADDRESS']),
		array('value' => $row['CITY']),
		array('value' => $row['COUNTY']),
		array('value' => $row['STATE']),
		array('value' => $row['ZIP_CODE']),
		array('value' => $row['PHONE_NUMBER']),
		array('value' => $row['OWNER_ID']),
		array('value' => $row['OWNER_NAME']),
		array('value' => $row['OWNER_OPERATOR']),
		array('value' => $row['LATITUDE']),
		array('value' => $row['LONGITUDE'])
	), FALSE);
}

function all_inspection_count($start_date, $end_date) {
	$db = Database::instance();
	
	return($db->query_field("
		SELECT Count(*)
		FROM USTX.INSPECTIONS I
		WHERE
			I.inspection_code = 1
			AND ( I.DATE_INSPECTED Between TO_DATE(:start_date, 'mm/dd/yyyy')
				and TO_DATE(:end_date, 'mm/dd/yyyy') )
			AND (select count(*) from USTX.TANKS T where T.facility_id = I.facility_id and T.tank_status_code in (1, 11) and T.tank_type = 'U') > 0
		"
		, array(':start_date' => $start_date, ':end_date' => $end_date)));
}
?>
