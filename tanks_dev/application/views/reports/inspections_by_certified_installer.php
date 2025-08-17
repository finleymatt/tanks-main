<?php
/**
 * Inspections by Certified Insstaller report
 *
 * @package Onestop
 * @subpackage views
 * @uses Report.php
 *
*/

$db = Database::instance();
if (!strtotime($start_date) || !strtotime($end_date))
	exit('Date is not in valid format.');

$certified_installer = new Certified_installers_Model;
$selected_certified_installer = ($certified_installer_id ? $certified_installer->get_certified_installer_by_id($certified_installer_id) : 'All');
$certified_installer_sql = (!empty($certified_installer_id) ? ':certified_installer_id' : 'I.certified_installer_id');

$report_sql = "SELECT F.*, I.ID INSPECTION_ID, I.date_inspected, I.staff_code, I.case_id, I.nov_number, CI.FIRST_NAME || ' ' || CI.LAST_NAME certified_installer,
		IC.description INSPECTION_TYPE,	S.first_name || ' ' || S.last_name inspector
		FROM ustx.facilities_mvw F
			INNER JOIN ustx.inspections I ON F.id = I.facility_id
			INNER JOIN ustx.certified_installers CI ON I.certified_installer_id = CI.id 
			INNER JOIN ustx.inspection_codes IC ON I.inspection_code = IC.code
			LEFT OUTER JOIN ustx.staff S on I.staff_code = S.code
		WHERE
			I.date_inspected >= TO_DATE(:start_date, 'mm/dd/yyyy')
			and I.date_inspected <= TO_DATE(:end_date, 'mm/dd/yyyy')
			and I.certified_installer_id = {$certified_installer_sql}
		ORDER BY certified_installer";

$bound_vars = array(':start_date' => $start_date, ':end_date' => $end_date);
if (!empty($certified_installer_id)) $bound_vars[':certified_installer_id'] = $certified_installer_id;
$rs_arr = $db->query($report_sql, $bound_vars)->as_array();


$report = new Report($output_format, 'Inspections by Certified Installer', "Inspections performed during {$start_date} to {$end_date}\nCertified Installer: {$selected_certified_installer}");

// labels -----------------------------------------------
$report->setLabelRow( array('Facility ID', 'Facility Name', 'Street', 'City', 'State', 'Zip', 'Certified Installer', 'Inspection ID', 'Inspector', 'Inspection Type', 'Inspection Date'), array('style' => array('alignment' => array('wrap' => TRUE))) );
$report->getActiveSheet()->getRowDimension($report->row_num - 1)->setRowHeight(25);
$report->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($report->row_num-1, $report->row_num-1);
for($col = 'A'; $col !== 'O'; $col++) {
	$report->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
}
if(count($rs_arr)) {
	// main body --------------------------------------------
	foreach ($rs_arr as $row) {
		$report->setRow(array(
			array('value' => $row['ID'], 'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER))),
			array('value' => $row['FACILITY_NAME']),
			array('value' => implode(' ', array($row['ADDRESS1'], $row['ADDRESS2']))),
			array('value' => $row['CITY']),
			array('value' => $row['STATE']),
			array('value' => $row['ZIP']),	
			array('value' => $row['CERTIFIED_INSTALLER']),
			array('value' => $row['INSPECTION_ID']),
			array('value' => $row['INSPECTOR']),
			array('value' => $row['INSPECTION_TYPE']),
			array('value' => Report::TO_DATE($row['DATE_INSPECTED']), 'style' => Report::$STYLE_DATE)
		));
	}
}
$report->setColumnSize(array(8, 35, 35, 20, 8, 8, 18, 16, 16, 15, 13));
$flag = $report->output('inspections_by_certified_installer');
