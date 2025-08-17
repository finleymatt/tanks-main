<?php
/**
 * Field Inspection Report
 * 
 * This report is created from an existing PDF report by Joe Godwin,
 * and made to include as much info pre-filled as possible.
 *
 * Code is similar to Facility Summary Report, but display_tanks function is
 * complicated due to having to display tanks by columns.
 *
 * Also contains many formatting customizations by calling PHPExcel methods.
 *
 * @package Onestop
 * @subpackage views
 * @uses Report.php
 *
*/

$col_widths = array_fill(0, 16, 10);
define('COL_COUNT', count($col_widths));
$db = Database::instance();

$report_sql = array(
	'lust' => "-- returns: NFA, Yes, Null(no)
		select
			-- max used to limit record to 1 just in case
			max(nvl(
				(select 'NFA' status from lust.lust_status_mvw S
				where status_code in ('M05', 'M22')
					and S.rel_id = R.id),
				'Yes')) status
		from lust.lust_releases_mvw R
		where R.facility_id = :facility_id",
	'facility' => "
		select facility_name name, id, (address1 || ' ' || address2) street, city, state, zip, '' phone_number
		from ustx.facilities_mvw
		where id = :facility_id",

	'owner' => "
		select O.owner_name name, O.id, (O.address1 || ' ' || O.address2) street, O.city, O.state, O.zip, O.phone_number
		from ustx.facilities_mvw F, ustx.owners_mvw O
		where F.owner_id = O.id and F.id = :facility_id",

	'operator' => "
		select distinct OP.operator_name name, OP.id, (OP.address1 || ' ' || OP.address2) street, OP.city, OP.state, OP.zip, OP.phone_number
		from ustx.tanks T, ustx.operators_mvw OP
		where T.operator_id = OP.id
			and T.facility_id = :facility_id",

	'tanks' => "
		select T.id, T.facility_id, T.meets_1988_req, T.meets_2011_req,
			T.tank_type, T.capacity, T.comments,
			SC.description tank_status, FM.description fill_material,
			SC.description tank_status, FM.description fill_material,
				(select min(history_date) date_installed
				from ustx.tank_history
				where tank_id = T.id and history_code = 'I'
				group by tank_id) date_installed
		from ustx.tanks T, ustx.fill_material FM, ustx.tank_status_codes SC
		where T.fill_material = FM.code (+)
			and T.tank_status_code = SC.code
			and T.tank_status_code in (1, 2)  -- 1=in use, 2=TOS
			and T.facility_id = :facility_id
		order by T.id",

	'tank_detail' => "-- used multiple times for each detail code type
		select TD.tank_detail_code, TDC.description
		from ustx.tanks T, ustx.tank_details TD, ustx.tank_detail_codes TDC
		where T.id = TD.tank_id
			and TD.tank_detail_code = TDC.code
			and T.facility_id = :facility_id
			and T.id = :tank_id
			and TDC.tank_info_code = :info_code
		order by TD.tank_detail_code"
);


$facility_rs = $db->query($report_sql['facility'], array(':facility_id' => $facility_id))->as_array();
if (! count($facility_rs)) exit('Invalid Facility ID provided');

$report = new Report($output_format, 'NMED PSTB Field Inspection Report', "2905 Rodeo Park Drive East, Bldg. 1
Santa Fe, NM 87505
Phone: 505.476.4397
Fax: 505.476.4374
http://www.nmenv.state.nm.us/ust/ustbtop.html");
$report->getActiveSheet()->getRowDimension(2)->setRowHeight(80); // make prev line tall
//$report->getDefaultStyle()->getFont()->setName('Arial')->setSize(8);
$report->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
$report->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
$report->getActiveSheet()->getPageSetup()->setVerticalCentered(true);
$report->getActiveSheet()->getDefaultRowDimension()->setRowHeight(21);
$report->getActiveSheet()->getPageSetup()->setFitToWidth(1);
$report->getActiveSheet()->getPageSetup()->setFitToHeight(1);
$report->getActiveSheet()->getPageMargins()->setTop(0.5);
$report->getActiveSheet()->getPageMargins()->setRight(0.6);
$report->getActiveSheet()->getPageMargins()->setLeft(0.6);
$report->getActiveSheet()->getPageMargins()->setBottom(0.5);


// inspection type info -------------------------------------------------------
$report->setRow(array(label_cell('Inspection Type', 2), data_cell('', 14)
), FALSE);
$report->setRow(array(label_cell('Case Num', 2), data_cell('', 3),
	label_cell('Inspection Start Time', 2), data_cell('', 4),
	label_cell('Date'), data_cell('', 4),
), FALSE);

// display facility, owner, operator ------------------------------------------
$owner_rs = $db->query($report_sql['owner'], array(':facility_id' => $facility_id))->as_array();
$operator_rs = $db->query($report_sql['operator'], array(':facility_id' => $facility_id))->as_array();
$lust_rs = $db->query($report_sql['lust'], array(':facility_id' => $facility_id))->as_array();

display_contacts($report, $facility_rs, 'I. Facility', $lust_rs);
display_contacts($report, $owner_rs, 'II. Owner');
display_contacts($report, $operator_rs, 'III. Operator');
display_contacts($report, array(), 'IV. Class A/B Operator');
display_contacts($report, array(), 'V. NMED Compliance Officer');


// list tanks -----------------------------------------------------------------
$tank_rs = $db->query($report_sql['tanks'], array(':facility_id' => $facility_id))->as_array();

display_title($report, 'Tanks');
display_tanks($report, $tank_rs, $report_sql);


$report->setColumnSize($col_widths);
$flag = $report->output('field_inspection');


function display_contacts($report, $rs_arr, $rec_type, $lust_rs=NULL) {
	if (count($rs_arr))
		$row = $rs_arr[0];
	else
		$row = array_fill_keys(array('ID', 'NAME', 'PHONE_NUMBER', 'STREET', 'CITY', 'STATE', 'ZIP'), '');

	$report->setBlankRow();
	$report->setRow(array(
		label_cell("{$rec_type} Name", 2), data_cell($row['NAME'], 7),
		label_cell('ID'), data_cell($row['ID'], 2),
		label_cell('Phone'), data_cell($row['PHONE_NUMBER'], 3)
	), FALSE);
	$report->setRow(array(
		label_cell('Address'), data_cell($row['STREET'], 6),
		label_cell('City'), data_cell($row['CITY'], 3),
		label_cell('State'), data_cell($row['STATE']),
		label_cell('Zip'), data_cell($row['ZIP'], 2)
	), FALSE);

	if ($rec_type == 'I. Facility') { // facility has a unique line
		$report->setRow(array(
			label_cell('Access authorized by', 2), data_cell('', 4),
			label_cell('LUST Site', 2), data_cell(($lust_rs[0]['STATUS'] ? $lust_rs[0]['STATUS'] : 'No'), 3),
			label_cell('Email'), data_cell('', 4)
		), FALSE);
	}
	else {
		$report->setRow(array(
			label_cell('Contact Name', 2), data_cell('', 7),
			label_cell('Email'), data_cell('', 6)
		), FALSE);
	}
}

function display_tanks($report, $rs_arr, $report_sql) {
	$db = Database::instance();

	$left_align = array('alignment' => array(
		'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
		'wrap' => TRUE));
	$center_align = array('alignment' => array(
		'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		'wrap' => TRUE));
	$style_date = array_merge(Report::$STYLE_DATE, Report::$STYLE_CENTER);
	$row_height = 34;

	$detail_cats = array('B'=>'Contents', 'A'=>'Tank Construction', 'S'=>'Secondary Containment', 'H'=>'Tank Release Detection', 'F'=>'Piping Construction', 'G'=>'Piping Release Detection', 'I'=>'Spill/Overfill Protection', 'C'=>'Corrosion Protection', 'L'=>'Loading Rack', 'U'=>'Usage');

	foreach(array('ID'=>'Tank#', 'TANK_TYPE'=>'Tank Type', 'CAPACITY'=>'Size', 'DATE_INSTALLED'=>'Installation Date') as $key => $label) {
		$report->setRow(array_merge(
			tank_row_arr($rs_arr, $key, $label)
		), FALSE, $center_align, $row_height);
	}

	// list tank detail codes --------------------------------------
	foreach ($detail_cats as $info_code => $label) {
		$row_data = array();
		foreach($rs_arr as $tank) {
			$detail_rs = $db->query($report_sql['tank_detail'], array(':facility_id' => $tank['FACILITY_ID'], ':tank_id' => $tank['ID'], ':info_code' => $info_code))->as_array();

			if (count($detail_rs)) {
				$codes = array();
				foreach($detail_rs as $detail)
					$codes[] = $detail['TANK_DETAIL_CODE'];
					//$code_data .= "({$detail['TANK_DETAIL_CODE']}) {$detail['DESCRIPTION']}\n";

				$code_data = implode(', ', $codes);
				array_push($row_data, array($code_data));
			}
			else
				array_push($row_data, array(''));
		}

		// fill up with empty boxes
		$row_data = array_pad($row_data, 7, array(''));

		$report->setRow(
			tank_row_arr($row_data, 0, $label) // 0 as key since rows have only 1 value
		, FALSE, $center_align, $row_height);
	}

	$report->setRow(
		tank_row_arr($rs_arr, 'TANK_STATUS', 'Tank Status')
	, FALSE, $center_align, $row_height);
}

function display_title($report, $title='') {
	$report->setBlankRow();
	$report->setBlankRow();
	$report->setLabelRow(array(array('value' => $title, 'colspan' => COL_COUNT)),
		Report::$STYLE_LABEL_2);
}

function label_cell($label, $colspan=1) {
	return(array('value' => "{$label}:", 'colspan' => $colspan,
		'style' => array(
			'font' => array('bold' => true),
			'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
				'wrap' => TRUE),
			'borders' => array(
				'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
				'right' => array('style' => PHPExcel_Style_Border::BORDER_NONE),
				'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
				'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
			)
		)
	));
}

function data_cell($data, $colspan=1) {
	return(array('value' => "{$data}", 'colspan' => $colspan,
		'style' => array_merge(Report::$STYLE_TEXT, array(
			'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
				'wrap' => TRUE),
			'borders' => array(
				'left' => array('style' => PHPExcel_Style_Border::BORDER_NONE),
				'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
				'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
				'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
			)
		)
	)));
}

/**
 * Given row and field info, returns array values of all tanks to be used in setRow
 */
function tank_row_arr($rs_arr, $field_key, $field_label) {
	return(array_merge(
		array(0 => label_cell($field_label, 2)),
		tank_values(array_pad($rs_arr, 7, array('')), $field_key)
	));
}

/**
 * Gathers all tanks' info for a given $key into array for display in a single row
 */
function tank_values($rs_arr, $key) {
	$style = array(
		'font' => array('size' => 7),
		'numberformat' => array(
			'code' => PHPExcel_Style_NumberFormat::FORMAT_TEXT),
		'borders' => array(
			'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
		));

	$result = array();
	foreach($rs_arr as $row) {
		if (isset($row[$key]))
			$result[] = array('value'=>$row[$key], 'style'=>$style, 'colspan'=>2);
		else
			$result[] = array('value'=>'', 'style'=>$style, 'colspan'=>2);
	}

	return($result);
}
?>
