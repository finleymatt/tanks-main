<?php
/**
 * Last Two Compliance Inspections
 *
 * @package Onestop
 * @subpackage views
 * @uses Report.php
 *
*/

$db = Database::instance();
$Staff = new Staff_Model;

$tank_type_list = implode( ',', array_map(function($val) { return("'{$val}'");}, $tank_types) );
$report_sql = "
	SELECT F.*, I.last_inspected_date,
		(
			select max(I3.date_inspected) last_inspected_date_2
			from ustx.inspections I3
			where (I3.inspection_code = 1) and I3.facility_id = F.id
			and (I3.date_inspected <> I.last_inspected_date)
		) last_inspected_date_2,
		(select count(*) from ustx.tanks T where (T.facility_id = F.id) and (T.TANK_TYPE = 'A') and (t.tank_status_code in (1, 2))) AST,
		(select count(*) from ustx.tanks T where (T.facility_id = F.id) and (T.TANK_TYPE = 'U') and (t.tank_status_code in (1, 2))) UST,
		( select max(I2.staff_code) from ustx.inspections I2 where (I2.facility_id=I.facility_id) and (I2.inspection_code=1) and (I2.date_inspected=I.last_inspected_date) ) STAFF_CODE
	FROM ustx.facilities_mvw F,
	(
		select facility_id, max(date_inspected) last_inspected_date
		from ustx.inspections
		where (inspection_code = 1)
		group by facility_id
	) I
	WHERE (F.id = I.facility_id)
		and (select count(*) from ustx.tanks T where (T.facility_id = F.id) and (T.TANK_TYPE in ({$tank_type_list})) and (t.tank_status_code in (1, 2))) > 0
		and I.last_inspected_date >= TO_DATE(:start_date, 'mm/dd/yyyy') and I.last_inspected_date <= TO_DATE(:end_date, 'mm/dd/yyyy')
	ORDER BY I.last_inspected_date ASC";

$rs_arr = $db->query($report_sql, array(':start_date' => $start_date, ':end_date' => $end_date))->as_array();

if (count($rs_arr)) {
	$report = new Report($output_format, 'Last Two Compliance Inspections Report', "Inspection Dates: {$start_date} to {$end_date}\nTank Types: {$tank_type_list}");
	
	// labels -----------------------------------------------
	$report->setLabelRow( array('Facility ID', 'Facility Name', 'Address', 'City', 'AST Count', 'UST Count', 'Last Inspected By', 'Last Inspected Date 1', 'Last Inspected Date 2'), array('style' => array('alignment' => array('wrap' => TRUE))) );
	$report->getActiveSheet()->getRowDimension($report->row_num - 1)->setRowHeight(25);
	$report->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($report->row_num-1, $report->row_num-1);
	
	// main body --------------------------------------------
	$group_row_start = $report->row_num;
	foreach ($rs_arr as $row) {
		$staff_name = $Staff->get_name($row['STAFF_CODE']);
		$report->setRow(array(
			array('value' => $row['ID'], 'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER))),
			array('value' => $row['FACILITY_NAME']),
			array('value' => $row['ADDRESS1']),
			array('value' => $row['CITY']),
			array('value' => $row['AST']),
			array('value' => $row['UST']),
			array('value' => $staff_name),
			array('value' => Report::TO_DATE($row['LAST_INSPECTED_DATE_2']), 'style' => Report::$STYLE_DATE),
			array('value' => Report::TO_DATE($row['LAST_INSPECTED_DATE']), 'style' => Report::$STYLE_DATE)
		));
	}
	
	// summary -----------------------------------------------
	$group_row_end = $report->row_num - 1;
	$report->setRow(array(
		array(
			'style' => Report::$STYLE_TOTAL,
			'colspan' => 7
		),
		7 => array('value' => 'Total Count:', 'style' => REPORT::$STYLE_TOTAL),
		array(
			'value' => "=COUNT(A{$group_row_start}:A{$group_row_end})",
			'style' => Report::$STYLE_TOTAL
		)
	), FALSE);
	
	$report->setColumnSize(array(8, 35, 35, 20, 8, 8, 20, 15, 15));
	$flag = $report->output('tank_inspection_2_dates');
}
?>
