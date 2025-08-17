<?php
/**
 * Facilities with A/B/C Operators Report
 *
 * @package Onestop
 * @subpackage views
 * @uses Report.php
 *
*/

$db = Database::instance();

if ($cert_level == 'A/B') {
	$cert_level_filter = "AND AB_O.FIRST_NAME <> 'C Operator'";
	$cert_level_filter2 = "AND abcert.cert_level IN ('A/B', 'A', 'B', 'A/B and C')";
} elseif ($cert_level == 'C') {
	$cert_level_filter = "AND AB_O.FIRST_NAME = 'C Operator' AND AB_O.LAST_NAME = 'Compliant'";
	$cert_level_filter2 = "AND abcert.cert_level IN ('C', 'A/B and C')";
} else {// both
	$cert_level_filter = '';
	$cert_level_filter2 = '';
}

$report_sql = "-- limit to facilities with IN USE and TOS status to match ABOP letter sending
	SELECT 'MAIN' MAIN, O.ID, OWNER_ID, O.OWNER_NAME,
		F.ID FACILITY_ID, F.FACILITY_NAME, F.CITY, F.STATE, F.ZIP,
		AB_O.ID AB_OP_ID, AB_O.LAST_NAME, AB_O.FIRST_NAME, AB_O.TITLE, AB_O.PHONE,
		(case
			when (((select count('x') from ustx.ab_cert where ab_operator_id = AB_O.id and cert_level = 'A' and cert_date >= add_months(sysdate, -60)) > 0)
				and ((select count('x') from ustx.ab_cert where ab_operator_id = AB_O.id and cert_level = 'B' and cert_date >= add_months(sysdate, -60)) > 0))
				then 'A/B'
			else (select max(cert_level) from ustx.ab_cert where ab_operator_id = AB_O.id and cert_date >= add_months(sysdate, -60))
		end) MAX_CERT_LEVEL,
		(select max(cert_date) from ustx.ab_cert where ab_operator_id = AB_O.id and cert_date >= add_months(sysdate, -60)) MAX_CERT_DATE,
		(select add_months(max(cert_date), 60) from ustx.ab_cert where ab_operator_id = AB_O.id) CERT_EXPIRE_DATE,
		(select count('x') from ustx.tanks T where F.id = T.facility_id and T.tank_type = 'A' and T.tank_status_code in (1,2)) AST_COUNT,
		(select count('x') from ustx.tanks T where F.id = T.facility_id and T.tank_type = 'U' and T.tank_status_code in (1,2)) UST_COUNT,
		( SELECT ( CASE WHEN COUNT('x') > 0 
				THEN 'Yes'
				ELSE 'No' 
			END )
		FROM ustx.ab_cert abcert, ustx.ab_operator op
		WHERE op.facility_id = F.id AND 
			op.id = abcert.ab_operator_id AND
			abcert.cert_date >= add_months(sysdate, -60) {$cert_level_filter2} ) facility_abc_cert
	FROM USTX.OWNERS_MVW O
		INNER JOIN USTX.FACILITIES_MVW F ON O.ID = F.OWNER_ID
		LEFT OUTER JOIN USTX.AB_OPERATOR AB_O
			ON F.ID = AB_O.FACILITY_ID {$cert_level_filter}
	WHERE F.id in (select distinct T.facility_id
		from ustx.tanks T
		where T.tank_status_code in (1, 2))
	ORDER BY F.FACILITY_NAME";

function summary_sql($tank_types) {
	return("
	SELECT
		-- count of facilitiies
		(select count(*) from ustx.facilities_mvw where id in
			(select distinct T.facility_id from ustx.tanks T
			where T.tank_status_code in (1, 2)
				and T.tank_type in ({$tank_types}))) FAC_COUNT,

		-- count of facilities with AB Operator
		(select count(*)
		from (
			-- has A/B cert
			(select AB_O.facility_id
			from ustx.ab_cert AB_C
				inner join ustx.ab_operator AB_O on AB_C.ab_operator_id = AB_O.id
			where AB_C.ab_operator_id = AB_O.id and AB_C.cert_level = 'A/B' and AB_C.cert_date >= add_months(sysdate, -60)
				-- dynamically modified portion for tank type
				and AB_O.facility_id in (select distinct T.facility_id
					from ustx.tanks T
					where T.tank_type in ({$tank_types}))
			group by AB_O.facility_id)
				
			union
		
			-- has A and B certs separately
			select id facility_id from ustx.facilities_mvw where
				id in (select AB_O.facility_id
				from ustx.ab_cert AB_C
					inner join ustx.ab_operator AB_O on AB_C.ab_operator_id = AB_O.id
				where AB_C.ab_operator_id = AB_O.id and AB_C.cert_level = 'A' and AB_C.cert_date >= add_months(sysdate, -60)
				group by AB_O.facility_id)
		
				and id in (select AB_O.facility_id
				from ustx.ab_cert AB_C
					inner join ustx.ab_operator AB_O on AB_C.ab_operator_id = AB_O.id
				where AB_C.ab_operator_id = AB_O.id and AB_C.cert_level = 'B' and AB_C.cert_date >= add_months(sysdate, -60)
				group by AB_O.facility_id)

				-- dynamically modified portion for tank type
				and id in (select distinct T.facility_id
					from ustx.tanks T
					where T.tank_type in ({$tank_types}))
		)) FAC_AB_COUNT,

		-- has C cert
		(select count(count(*))
		from ustx.ab_operator
		where first_name = 'C Operator' and last_name = 'Compliant'
			-- dynamically modified portion for tank type
			and facility_id in (select distinct T.facility_id
				from ustx.tanks T
				where T.tank_type in ({$tank_types}))
		group by facility_id
		) FAC_C_COUNT
	FROM DUAL");
}


$report = new Report($output_format, 'Facilities with A/B/C Operators', "Certificate Level: {$cert_level}");

// summary stats - show before main report to allow sorting --------------------
foreach(array('Both tank types'=>"'A', 'U'", 'AST'=>"'A'", 'UST'=>"'U'") as $tank_desc => $tank_types) {
	$rs_arr = $db->query(summary_sql($tank_types))->as_array();

	$report->setRow(array(array('colspan' => 3, 'value' => "Summary Stats ({$tank_desc})", 'style' => Report::$STYLE_LABEL_2)));

	// A/B count summary ----------------------------------------------------------
	$fac_total_cell = "C{$report->row_num}";
	$report->setRow(array(
		array('colspan' => 2, 'value' => 'Total Facilities', 'style' => Report::$STYLE_LABEL),
		2 => array('value' => $rs_arr[0]['FAC_COUNT'], 'style' => Report::$STYLE_BORDER),
		array('colspan' => 4, 'value' => 'Facilities with at least one active or TOS tank.')
	), FALSE);

	$report->setRow(array(
		array('colspan' => 2, 'value' => 'Facilities with A and B Operator', 'style' => Report::$STYLE_LABEL),
		2 => array('value' => $rs_arr[0]['FAC_AB_COUNT'], 'style' => Report::$STYLE_BORDER),
		array('colspan' => 4, 'value' => 'Facilities with at least one active A/B operator or one of A and one of B.')
	), FALSE);
	$report->setRow(array(
		array('colspan' => 2, 'value' => 'Facilities without A and B Operator', 'style' => Report::$STYLE_LABEL),
		2 => array('value' => "={$fac_total_cell} - C" . ($report->row_num -1), 'style' => Report::$STYLE_BORDER)
	), FALSE);

	// C count summary ------------------------------------------------------------
	$report->setRow(array(
		array('colspan' => 2, 'value' => 'Facilities with C Operator', 'style' => Report::$STYLE_LABEL),
		2 => array('value' => $rs_arr[0]['FAC_C_COUNT'], 'style' => Report::$STYLE_BORDER),
		array('colspan' => 4, 'value' => 'Facilities with a C operator.  Certificates for C operators do not expire.')
	), FALSE);
	$report->setRow(array(
		array('colspan' => 2, 'value' => 'Facilities without C Operator', 'style' => Report::$STYLE_LABEL),
		2 => array('value' => "={$fac_total_cell} - C" . ($report->row_num -1), 'style' => Report::$STYLE_BORDER)
	), FALSE);
	$report->setBlankRow();
}
$report->setBlankRow();


// main report ----------------------------------------------------------------

$report->setRow(array(array('colspan' => 3, 'value' => 'Main Report', 'style' => Report::$STYLE_LABEL_2)));
$report->setRow(array(array('colspan' => 8, 'value' => 'This section lists all the A/B/C Operators that exist for each of the facilities. A and B certificates have dates, if specified. C certificates do not have dates.')), FALSE);
$report->setLabelRow( array('O ID', 'Owner Name', 'F ID', 'Facility Name', 'F City', 'F State', 'F Zip', 'Tank Type', 'Op LName', 'Op FName', 'Cert Level', 'Cert Date', 'Cert Expires On', 'Op Cert Active?', 'F Cert Active?'), array('style' => array('alignment' => array('wrap' => TRUE))) );

$rs_arr = $db->query($report_sql)->as_array();
foreach($rs_arr as $row) {
	$min_cert_date = date('m/d/Y', strtotime('-5 years'));
	$max_cert_level = ((($row['FIRST_NAME'] == 'C Operator')
		&& ($row['LAST_NAME'] == 'Compliant')) ? 'C'
		: $row['MAX_CERT_LEVEL']);
	$report->setRow(array(
		$row['OWNER_ID'],
		$row['OWNER_NAME'],
		$row['FACILITY_ID'],
		$row['FACILITY_NAME'],
		$row['CITY'],
		$row['STATE'],
		array('value' => $row['ZIP'], 'style' => Report::$STYLE_TEXT),
		((($row['AST_COUNT'] > 0) && ($row['UST_COUNT'])) ? 'Both' :
			(($row['AST_COUNT'] > 0) ?  'A' : 'U')), 
		$row['LAST_NAME'], $row['FIRST_NAME'],
		$max_cert_level,
		array('value' => Report::TO_DATE($row['MAX_CERT_DATE']), 'style' => Report::$STYLE_DATE),
		array('value' => Report::TO_DATE($row['CERT_EXPIRE_DATE']), 'style' => Report::$STYLE_DATE),
		array('value' => ($max_cert_level == 'C' ? 'Yes' : "=IF(K{$report->row_num} >= DATEVALUE(\"{$min_cert_date}\"), \"Yes\", \"No\")"), 'style' => Report::$STYLE_TEXT),
		$row['FACILITY_ABC_CERT']
	), FALSE, Report::$STYLE_BORDER);
}


$report->setColumnSize(array(8, 38, 8, 38, 22, 8, 9, 9, 14, 14, 10, 13, 13, 13, 13));
$flag = $report->output('facilities_abc_op');

?>
