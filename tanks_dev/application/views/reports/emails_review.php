<?php
/**
 * Emails Review Report
 *
 * @package Onestop
 * @subpackage views
 * @uses Report.php
 * @author George Huang
 *
*/

$db = Database::instance();

$entity_type_ucfirst = ucfirst($entity_type);
if($contact_type !== 'All') {
	$contact_type_name = Model::instance('Ref_contact_type')->get_lookup_desc($contact_type, FALSE);
}
if($entity_type == 'facility') {
	$table = 'FACILITIES_MVW';
	$entity_name = 'FACILITY_NAME';
	$entity_id_title = 'Facility ID';
	$entity_name_title = 'Facility Name';
} else {
	$table = 'OWNERS_MVW';
	$entity_name = 'OWNER_NAME';
	$entity_id_title = 'Owner ID';
	$entity_name_title = 'Owner Name';
}
switch($contact_type) {
	case 'All':
		$contact_type_filter = "";
		$contact_type_name = $contact_type;
		break;
	case '1':
		$contact_type_filter = "AND E.CONTACT_TYPE_ID = '1'";
		break;
	case '2':
		$contact_type_filter = "AND E.CONTACT_TYPE_ID = '2'";
		break;
	case '3':
		$contact_type_filter = "AND E.CONTACT_TYPE_ID = '3'";
		break;
	case '4':
		$contact_type_filter = "AND E.CONTACT_TYPE_ID = '4'";
		break;
	case '5':
		$contact_type_filter = "AND E.CONTACT_TYPE_ID = '5'";
		break;
	case '6':
		$contact_type_filter = "AND E.CONTACT_TYPE_ID = '6'";
		break;
	case '7':
		$contact_type_filter = "AND E.CONTACT_TYPE_ID = '7'";
		break;
	case '8':
		$contact_type_filter = "AND E.CONTACT_TYPE_ID = '8'";
		break;
	case '9':
		$contact_type_filter = "AND E.CONTACT_TYPE_ID = '9'";
		break;
}

$report_sql = "
	SELECT F.ID, F.{$entity_name}, E.TITLE, E.FULLNAME, E.EMAIL, E.COMMENTS, E.DATE_CREATED
	FROM USTX.EMAILS E
	INNER JOIN USTX.{$table} F
	ON E.ENTITY_ID = F.ID
	WHERE E.ENTITY_TYPE = :entity_type
	{$contact_type_filter} 
	ORDER BY E.FULLNAME";

$rs_arr = $db->query($report_sql, array(':entity_type' => $entity_type))->as_array();


$report = new Report($output_format, "{$entity_type_ucfirst} Emails Review Report", "Contact type: {$contact_type_name}");

// labels -----------------------------------------------
$report->setLabelRow( array("{$entity_id_title}", "{$entity_name_title}", 'Title', 'Full Name', 'Email', 'Date Created', 'Comments'), array('style' => array('alignment' => array('wrap' => TRUE))) );
$report->getActiveSheet()->getRowDimension($report->row_num - 1)->setRowHeight(25);
$report->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($report->row_num-1, $report->row_num-1);

if (count($rs_arr)) {
	// main body --------------------------------------------
	foreach ($rs_arr as $row) {
		$report->setRow(array(
			array('value' => $row['ID'], 'style' => array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER))),
			array('value' => $row[$entity_name]),
			array('value' => $row['TITLE']),
			array('value' => $row['FULLNAME']),
			array('value' => $row['EMAIL']),
			array('value' => Report::TO_DATE($row['DATE_CREATED']), 'style' => Report::$STYLE_DATE),
			array('value' => $row['COMMENTS']),
		));
	}
}

$report->setColumnSize(array(20, 60, 30, 30, 35, 20, 100));
$flag = $report->output("{$entity_type_ucfirst} Emails Review");
