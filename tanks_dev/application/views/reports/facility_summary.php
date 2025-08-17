<?php
/**
 * Facility Summary
 * Migrated from Legacy Onestop
 * This report has more complex and unusual display structure than other reports.
 * Therefore, the coding uses a more procedural method with local procedures.
 *
 * @package Onestop
 * @subpackage views
 * @uses Report.php
 *
*/

define('DO_RAW_HTML', TRUE); // dont escape <hr />

$col_widths = array(11, 11, 11, 11, 11, 11, 11, 11, 11, 11, 11, 11, 11, 11, 11, 11, 11, 11, 11);
define('COL_COUNT', count($col_widths));
$db = Database::instance();

$report_sql = array(
	'facility' => "
		select facility_name name, id, (address1 || ' ' || address2) street, city, state, zip, '' phone_number,
			decode((select count(*) from lust.lust_releases_mvw where facility_id = 28507), 0, 'No', 'Yes') is_lust
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

	'contacts' => "
		select * from ustx.emails
		where (entity_id = :facility_id and entity_type = 'facility')
			or (entity_id = :owner_id and entity_type = 'owner')",

	'abops' => "
		select * from ustx.ab_operator
		where facility_id = :facility_id
		order by last_name, first_name",

	'tanks' => "
		select T.id, T.facility_id, T.meets_1988_req, T.meets_2011_req,
			T.tank_type, T.capacity, T.comments,
			SC.description tank_status, FM.description fill_material
		from ustx.tanks T, ustx.fill_material FM, ustx.tank_status_codes SC
		where T.fill_material = FM.code (+)
			and T.tank_status_code = SC.code
			and T.facility_id = :facility_id
		order by T.id",

	'tank_installed_date' => "
		select min(history_date) date_installed
		from ustx.tank_history
		where tank_id = :tank_id and history_code = 'I'
		group by tank_id",

	'tank_removed_date' => "
		select max(history_date) date_removed
		from ustx.tank_history
		where tank_id = :tank_id and history_code = 'R'
		group by tank_id",

	'tank_bought_date' => "
		select max(history_date) date_bought
		from ustx.tank_history
		where tank_id = :tank_id and history_code in ('P','BP','LE')
		group by tank_id",

	'tank_sold_date' => "
		select max(history_date) date_sold
		from ustx.tank_history
		where tank_id = :tank_id and history_code in ('S','BS','LO')
		group by tank_id",

	'tank_detail' => "-- used multiple times for each detail code type
		select TD.tank_detail_code, TDC.description
		from ustx.tanks T, ustx.tank_details TD, ustx.tank_detail_codes TDC
		where T.id = TD.tank_id
			and TD.tank_detail_code = TDC.code
			and T.facility_id = :facility_id
			and T.id = :tank_id
			and TDC.tank_info_code = :detail_code",

	'inspection_history' => "
		select I.date_inspected, I.case_id, I.staff_code, I.compliance_order_issue_date,
			I.compliance_date, I.nov_number, IC.description
		from ustx.inspections I, ustx.inspection_codes IC
		where I.facility_id = :facility_id and I.inspection_code = IC.code
		order by I.date_inspected desc",

	'violation_history' => "
		select I.date_inspected, I.compliance_date, I.nov_number,
			P.penalty_code, P.date_corrected, PC.description
		from ustx.inspections I, ustx.penalties P, ustx.penalty_codes PC
		where I.id = P.inspection_id and P.penalty_code = PC.code
			and I.facility_id = :facility_id
		order by I.date_inspected desc",

	'owner_comment' => "
		select comment_date, comments, user_created, owner_id
		from ustx.owner_comments
		where owner_id = :owner_id
		order by comment_date desc"
);


$facility_rs = $db->query($report_sql['facility'], array(':facility_id' => $facility_id))->as_array();
if (! count($facility_rs)) exit('Invalid Facility ID provided');

$report = new Report($output_format, 'NMED PSTB Facility Summary', "Facility ID: {$facility_id}\nLUST Site?: {$facility_rs[0]['IS_LUST']}");
$report->getDefaultStyle()->getFont()->setName('Arial')->setSize(7.5);
$report->getActiveSheet()->getPageSetup()->setFitToWidth(1);
$report->getActiveSheet()->getPageSetup()->setFitToHeight(0);
$report->setStyle('LABEL', array_merge(Report::$STYLE_LABEL, array(
		'font' => array(
			'bold' => true
		),
		'fill' => array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array('argb' => 'FFCCCCCC')
		)
	)));  // set default label style

// display addresses --------------------------------------------------
$owner_rs = $db->query($report_sql['owner'], array(':facility_id' => $facility_id))->as_array();
$operator_rs = $db->query($report_sql['operator'], array(':facility_id' => $facility_id))->as_array();
display_title($report);
display_address($report, $facility_rs, 'Facility:');
display_address($report, $owner_rs, 'Owner:');
display_address($report, $operator_rs, 'Operator:');

// contacts  ------------------------------------------------------------------
$contacts_rs = $db->query($report_sql['contacts'], array(':facility_id' => $facility_id, ':owner_id' => $owner_rs[0]['ID']))->as_array();
display_title($report, 'Contacts');
display_contacts($report, $contacts_rs);

// ab operators  -------------------------------------------------------------
$abops_rs = $db->query($report_sql['abops'], array(':facility_id' => $facility_id))->as_array();
display_title($report, 'A/B Operators');
display_abops($report, $abops_rs);

// empty notes area -----------------------------------------------------------
display_title($report, 'Notes');
$report->setPageBreak();

// list tanks -----------------------------------------------------------------
display_title($report, 'Tank Summary');

$tanks_rs = $db->query($report_sql['tanks'], array(':facility_id' => $facility_id))->as_array();
display_tanks($report, $tanks_rs, $report_sql);

// list inspection history -----------------------------------------------------
$inspection_rs = $db->query($report_sql['inspection_history'], array(':facility_id' => $facility_id))->as_array();
display_title($report, 'Inspection History');
display_inspection($report, $inspection_rs);

// list violation history -----------------------------------------------------
$violation_rs = $db->query($report_sql['violation_history'], array(':facility_id' => $facility_id))->as_array();
display_title($report, 'Violation History');
display_violation($report, $violation_rs);

// list owner comments -----------------------------------------------------
$owner_comment_rs = $db->query($report_sql['owner_comment'], array(':owner_id' => $owner_rs[0]['ID']))->as_array();
display_title($report, 'Owner Comments');
display_owner_comment($report, $owner_comment_rs);


$report->setColumnSize($col_widths);
$flag = $report->output('facility_summary');


function display_address($report, $rs_arr, $label) {
	foreach($rs_arr as $row) {
		$report->setRow(array(
			array('value' => $label, 'colspan' => 2),
			array('value' => $row['ID'], 'colspan' => 2, 'style' => Report::$STYLE_LEFT),
			array('value' => $row['NAME'], 'colspan' => 5),
			array('value' => $row['STREET'], 'colspan' => 4),
			array('value' => $row['CITY'], 'colspan' => 2),
			array('value' => $row['STATE']),
			array('value' => $row['ZIP'], 'style' => Report::$STYLE_CENTER),
			array('value' => $row['PHONE_NUMBER'], 'colspan' => 2)
		), FALSE, array_merge(Report::$STYLE_TEXT, array('alignment' => array('wrap' => TRUE))));
	}
}

function display_contacts($report, $rs_arr) {
	foreach($rs_arr as $row) {
		$report->setRow(array(
			array('value' => ucfirst($row['ENTITY_TYPE']) . ':', 'colspan' => 2),
			array('value' => $row['TITLE'], 'colspan' => 2),
			array('value' => $row['FULLNAME'], 'colspan' => 3),
			array('value' => $row['EMAIL'], 'colspan' => 3),
			array('value' => $row['PHONE'], 'colspan' => 2),
			array('value' => $row['COMMENTS'], 'colspan' => 5)
		), FALSE, array_merge(Report::$STYLE_TEXT, array('alignment' => array('wrap' => TRUE))));
	}
}

function display_abops($report, $rs_arr) {
	foreach($rs_arr as $row) {
		$report->setRow(array(
			array('value' => "{$row['LAST_NAME']}, {$row['FIRST_NAME']}", 'colspan' => 3),
			array('value' => 'Cert Level: ' . Model::instance('Ab_operator')->get_effective_cert_level($row['ID']), 'colspan' => 2)
		), FALSE, array_merge(Report::$STYLE_TEXT, array('alignment' => array('wrap' => TRUE))));
	}
}

function display_tanks($report, $rs_arr, $report_sql) {
	$db = Database::instance();

	$style_date = array_merge(Report::$STYLE_DATE, Report::$STYLE_CENTER);

	$detail_cats = array('Tank Construction' => 'A', 'Secondary Containment' => 'S', 'Tank Release Detection' => 'H', 'Piping' => 'F', 'Contents' => 'B', 'Piping Release Detection' => 'G', 'Spill/Overfill Protection' => 'I', 'Corrosion Protection' => 'C', 'Loading Rack' => 'L', 'Usage' => 'U');

	foreach($rs_arr as $row) {
		$installed_date = $db->query_field($report_sql['tank_installed_date'], array(':tank_id' => $row['ID']));
		$removed_date = $db->query_field($report_sql['tank_removed_date'], array(':tank_id' => $row['ID']));
		$bought_date = $db->query_field($report_sql['tank_bought_date'], array(':tank_id' => $row['ID']));
		$sold_date = $db->query_field($report_sql['tank_sold_date'], array(':tank_id' => $row['ID']));

		$report->setLabelRow(array('Tank#', '1988 Upg?', '2011 Upg?', 'Type', array('value'=>'Status', 'colspan'=>2), 'Size', array('value'=>'Comments', 'colspan'=>4), array('value'=>'Date Installed', 'colspan'=>2), array('value'=>'Date Removed', 'colspan'=>2), array('value'=>'Date Bought', 'colspan'=>2), array('value'=>'Date Sold', 'colspan'=>2)));
		$report->setRow(array(
			array('value' => $row['ID']),
			array('value' => $row['MEETS_1988_REQ']),
			array('value' => $row['MEETS_2011_REQ']),
			array('value' => $row['TANK_TYPE']),
			array('value' => $row['TANK_STATUS'], 'colspan' => 2),
			array('value' => $row['CAPACITY']),
			array('value' => $row['COMMENTS'], 'colspan' => 4),
			array('value' => Report::TO_DATE($installed_date), 'style' => $style_date, 'colspan' => 2),
			array('value' => Report::TO_DATE($removed_date), 'style' => $style_date, 'colspan' => 2),
			array('value' => Report::TO_DATE($bought_date), 'style' => $style_date, 'colspan' => 2),
			array('value' => Report::TO_DATE($sold_date), 'style' => $style_date, 'colspan' => 2)
		), FALSE, Report::$STYLE_CENTER);

		// list tank detail codes --------------------------------------
		$total_count = 0; $col_count = 0;
		$row_label = array(''); $row_data = array('');
		foreach ($detail_cats as $label => $detail_code) {
			$total_count++; $col_count++;
			array_push($row_label, array('value' => $label, 'colspan' => 5,
				'style' => array('font' => array('underline' => PHPExcel_Style_Font::UNDERLINE_SINGLE))));

			$detail_rs = $db->query($report_sql['tank_detail'], array(':facility_id' => $row['FACILITY_ID'], ':tank_id' => $row['ID'], ':detail_code' => $detail_code))->as_array();

			if (count($detail_rs)) {
				$code_arr = array(); $desc_arr = array();
				foreach($detail_rs as $detail) {
					$code_arr[] = "{$detail['TANK_DETAIL_CODE']}";
					$desc_arr[] = "{$detail['DESCRIPTION']}";
				}
				array_push($row_data,
					implode(', ', $code_arr),
					array('value' => implode(', ', $desc_arr), 'colspan' => 4));
			}
			else
				array_push($row_data, '', array('value'=>'', 'colspan'=>4));

			if ((($col_count % 3) == 0) || ($total_count == count($detail_cats))) {  // 3 per row
				$report->setRow($row_label, FALSE);
				$report->setRow($row_data, FALSE);
				$report->setBlankRow();
				$row_label = array(''); $row_data = array('');
				$col_count = 0;
			}
			else {  // add space to the right
				$row_label[] = '';
				$row_data[] = '';
			}
		}
	}
}

function display_inspection($report, $rs_arr) {
	$report->setLabelRow(array(
		array('value' => 'Inspection Date', 'colspan' => 3),
		array('value' => 'Case#', 'colspan' => 2),
		array('value' => 'Inspector', 'colspan' => 3),
		array('value' => 'Inspection Type', 'colspan' => 5),
		array('value' => 'NOV#', 'colspan' => 3),
		array('value' => 'Compliance Cert Date', 'colspan' => 3)));

	foreach($rs_arr as $row) {
		$report->setRow(array(
			array('value' => Report::TO_DATE($row['DATE_INSPECTED']), 'style' => array_merge(Report::$STYLE_DATE, Report::$STYLE_CENTER), 'colspan' => 3),
			array('value' => $row['CASE_ID'], 'colspan' => 2),
			array('value' => $row['STAFF_CODE'], 'colspan' => 3),
			array('value' => $row['DESCRIPTION'], 'colspan' => 5),
			array('value' => $row['NOV_NUMBER'], 'colspan' => 3),
			array('value' => Report::TO_DATE($row['COMPLIANCE_DATE']), 'style' => array_merge(Report::$STYLE_DATE, Report::$STYLE_CENTER), 'colspan' => 3),
		), FALSE, Report::$STYLE_CENTER);
	}

}

function display_violation($report, $rs_arr) {
	$report->setLabelRow(array(
		array('value' => 'NOV#', 'colspan' => 2),
		array('value' => 'NOV Code', 'colspan' => 2),
		array('value' => 'NOV Description', 'colspan' => 6),
		array('value' => 'Inspection Date', 'colspan' => 3),
		array('value' => 'Compliance Date', 'colspan' => 3),
		array('value' => 'Date Corrected', 'colspan' => 3)));

	foreach($rs_arr as $row) {
		// empty value doesn't work with UDAPI, so use 01-JAN-68 instead in DB
		$date_corrected = ($row['DATE_CORRECTED'] == '01-JAN-68') ? Null : $row['DATE_CORRECTED'];
		$report->setRow(array(
			array('value' => $row['NOV_NUMBER'], 'colspan' => 2, 'style' => Report::$STYLE_CENTER),
			array('value' => $row['PENALTY_CODE'], 'colspan' => 2, 'style' => array_merge(Report::$STYLE_TEXT, Report::$STYLE_CENTER)),
			array('value' => $row['DESCRIPTION'], 'colspan' => 6),
			array('value' => Report::TO_DATE($row['DATE_INSPECTED']), 'style' => array_merge(Report::$STYLE_DATE, Report::$STYLE_CENTER), 'colspan' => 3),
			array('value' => Report::TO_DATE($row['COMPLIANCE_DATE']), 'style' => array_merge(Report::$STYLE_DATE, Report::$STYLE_CENTER), 'colspan' => 3),
			array('value' => Report::TO_DATE($date_corrected), 'style' => array_merge(Report::$STYLE_DATE, Report::$STYLE_CENTER), 'colspan' => 3),
		), FALSE);
	}

}

function display_owner_comment($report, $rs_arr) {
	$report->setLabelRow(array(
		array('value' => 'Comment Date', 'colspan' => 2),
		array('value' => 'Comment', 'colspan' => 14),
		array('value' => 'User ID', 'colspan' => 3)));

	foreach($rs_arr as $row) {
		$report->setRow(array(
			array('value' => Report::TO_DATE($row['COMMENT_DATE']), 'style' => array_merge(Report::$STYLE_DATE, Report::$STYLE_CENTER), 'colspan' => 2),
			array('value' => $row['COMMENTS'], 'colspan' => 14),
			array('value' => $row['USER_CREATED'], 'colspan' => 3, 'style' => Report::$STYLE_CENTER)
		), FALSE);
	}

}

function display_title($report, $title='') {
	$report->setBlankRow();
	$report->setBlankRow();
	$report->setLabelRow(array(array('value' => $title, 'colspan' => COL_COUNT)),
		Report::$STYLE_LABEL_2);
	$report->setLine(COL_COUNT);
}
?>
