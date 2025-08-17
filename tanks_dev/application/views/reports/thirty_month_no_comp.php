<?php
/**
 * Tank Inspection Dates
 * imported from Oracle Report, "Facilities not Inspected in Last 12 Months"
 *
 * @package Onestop
 * @subpackage views
 * @uses Report.php
 *
*/

$db = Database::instance();
$Staff = new Staff_Model;

if (!strtotime($before_date))
	exit('Date is not in valid format.');

$county_sql = ($county ? ':county' : 'CT.county');
$tank_type_list = implode( ',', array_map(function($val) { return("'{$val}'");}, $tank_types) );
$report_sql = "
	SELECT F.*, I.last_inspected_date, CT.county,
		assigned_inspector.full_name assigned_inspector,
		(select count(*) from ustx.tanks T where (T.facility_id = F.id) and (T.TANK_TYPE = 'A') and (t.tank_status_code in (1, 2))) AST,
		(select count(*) from ustx.tanks T where (T.facility_id = F.id) and (T.TANK_TYPE = 'U') and (t.tank_status_code in (1, 2))) UST,
		(select max(case_id) from ustx.inspections I2 where I2.facility_id = F.id and I2.date_inspected = I.last_inspected_date) case_id,
		-- (select max(I2.staff_code) from ustx.inspections I2 where (I2.facility_id=I.facility_id) and (I2.inspection_code=1) and (I2.date_inspected=I.last_inspected_date)) STAFF_CODE,
		-- (trunc(to_date(:before_date, 'mm/dd/yyyy')) - I.last_inspected_date) DAYS_OVERDUE
		trunc(sysdate - I.last_inspected_date) DAYS_SINCE
	FROM ustx.facilities_mvw F
		inner join (select facility_id, max(date_inspected) last_inspected_date
			from ustx.inspections
			where (inspection_code = 1)
			group by facility_id
			having max(date_inspected) < TO_DATE(:before_date, 'mm/dd/yyyy')
		) I on F.id = I.facility_id
		left outer join ustx.cities CT on F.city = CT.city
		LEFT OUTER JOIN ( SELECT E.entity_id, s2.first_name || ' ' || s2.last_name full_name
				FROM ustx.staff S2,
					ustx.entity_details E
				WHERE E.entity_type = 'facility' and
					E.detail_type = 'assigned_inspector' AND
					E.Detail_Value = S2.sep_login_id ) assigned_inspector ON assigned_inspector.entity_id = F.id
	WHERE
		(select count(*) from ustx.tanks T where (T.facility_id = F.id) and (T.TANK_TYPE in ({$tank_type_list})) and (t.tank_status_code in (1, 2))) > 0
		AND Upper(CT.county) = Upper({$county_sql})
		AND trunc(sysdate - I.last_inspected_date) > 914
	ORDER BY I.last_inspected_date ASC";


$bound_vars = array(':before_date' => $before_date);
if ($county) $bound_vars[':county'] = $county;
$rs_arr = $db->query($report_sql, $bound_vars)->as_array();

$report = new Report($output_format, 'Thirty Months No Compliance Report', "Tanks with no inspections since {$before_date}\nCounty: ". ($county ? $county : 'All Counties') ."\nTanks: {$tank_type_list}\nReport Generated on: " . date('m/d/Y'));
	
// labels -----------------------------------------------
$report->setLabelRow( array('Facility ID', 'Facility Name', 'Street', 'City', 'State', 'Zip', 'County', 'AST Count', 'UST Count', 'Case ID', 'Assigned Inspector', 'Last Inspected Date', 'Days Since Insp'), array('style' => array('alignment' => array('wrap' => TRUE))) );
$report->getActiveSheet()->getRowDimension($report->row_num - 1)->setRowHeight(25);
$report->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($report->row_num-1, $report->row_num-1);

if (count($rs_arr)) {
	// main body --------------------------------------------
	$group_row_start = $report->row_num;
	foreach ($rs_arr as $row) {
		//$staff_name = $Staff->get_name($row['STAFF_CODE']);
		if (($row['DAYS_SINCE'] >= 1095) && ($row['AST'] > 0))
			$overdue_style =  array('fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('argb' => 'FF5555FF')));
		elseif ($row['DAYS_SINCE'] >= 1095)
			$overdue_style =  array('fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('argb' => 'FFFF5555')));
		else
			$overdue_style = array();

		$report->setRow(array(
			array('value' => $row['ID'], 'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER))),
			array('value' => $row['FACILITY_NAME']),
			array('value' => implode(' ', array($row['ADDRESS1'], $row['ADDRESS2']))),
			array('value' => $row['CITY']),
			array('value' => $row['STATE']),
			array('value' => $row['ZIP']),
			array('value' => $row['COUNTY']),
			array('value' => $row['AST']),
			array('value' => $row['UST']),
			array('value' => $row['CASE_ID']),
			array('value' => $row['ASSIGNED_INSPECTOR']),
			array('value' => Report::TO_DATE($row['LAST_INSPECTED_DATE']), 'style' => Report::$STYLE_DATE),
			array('value' => $row['DAYS_SINCE'], 'style' => $overdue_style)
		));
	}
	
	// summary -----------------------------------------------
	$group_row_end = $report->row_num - 1;
	$report->setRow(array(
		array(
			'style' => Report::$STYLE_TOTAL,
			'colspan' => 11
		),
		11 => array('value' => 'Total Count:', 'style' => REPORT::$STYLE_TOTAL),
		array(
			'value' => "=COUNT(A{$group_row_start}:A{$group_row_end})",
			'style' => Report::$STYLE_TOTAL
		)
	), FALSE);
}
	
$report->setColumnSize(array(8, 35, 35, 20, 6, 8, 18, 8, 8, 8, 16, 16, 13));
$flag = $report->output('30_month_no_comp');
?>
