<?php
/**
 * Insurance Review Report
 *
 * @package Onestop
 * @subpackage views
 * @uses Report.php
 *
*/

$db = Database::instance();

$report_sql = "
SELECT DISTINCT F.OWNER_ID, 
	O.OWNER_NAME, 
	F.ID AS FACILITY_ID,
	FR.METHOD_DESCRIPTION as FINANCIAL_METHOD, 
	FR.PROVIDER_DESCRIPTION as FINANCIAL_PROVIDER, 
	FR.POLICY_NUMBER, 
	FR.BEGIN_DATE, 
	FR.END_DATE, 
	FR.AMOUNT, 
	FR.COVERED_TANKS_COUNT
FROM USTX.FACILITIES_MVW F
JOIN USTX.OWNERS_MVW O ON F.OWNER_ID = O.ID
LEFT OUTER JOIN ( SELECT FR1.FACILITY_ID, 
		FR1.FIN_METH_CODE, 
		FR1.FIN_PROV_CODE, 
		FR1.POLICY_NUMBER, 
		FR1.BEGIN_DATE, 
		FR1.END_DATE, 
		FR1.AMOUNT, 
		FR1.COVERED_TANKS_COUNT,
		FM.DESCRIPTION AS METHOD_DESCRIPTION,
		FP.DESCRIPTION AS PROVIDER_DESCRIPTION
	FROM USTX.FINANCIAL_RESPONSIBILITIES FR1
	JOIN USTX.FINANCIAL_METHODS FM ON FR1.FIN_METH_CODE = FM.CODE
	JOIN USTX.FINANCIAL_PROVIDERS FP ON FR1.FIN_PROV_CODE = FP.CODE
	WHERE FR1.END_DATE = ( SELECT MAX(FR2.END_DATE) 
		FROM USTX.FINANCIAL_RESPONSIBILITIES FR2
		WHERE FR2.FACILITY_ID = FR1.FACILITY_ID ) ) FR ON FR.FACILITY_ID = F.ID
ORDER BY OWNER_ID, FACILITY_ID
";

$rs_arr = $db->query($report_sql)->as_array();
$today = date("Y/m/d");
$thirty_days_later = date("Y/m/d", strtotime('+30 days'));
$report = new Report($output_format, 'Insurance Review', "Generate on {$today}");

// labels -----------------------------------------------
$report->setLabelRow( array('Owner ID', 'Owner Name', 'Facility ID', 'Financial Method', 'Financial Provider', 'Policy Number', 'Amount', 'Effective Date', 'Expiration Date', 'Expired', 'Expire in 30 Days', 'Number of Tanks Covered'), array('style' => array('alignment' => array('wrap' => TRUE))) );

// set cell size automatically
for($col = 'A'; $col !== 'P'; $col++) {
	$report->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
}

if (count($rs_arr)) {
	// main body --------------------------------------------
	foreach ($rs_arr as $row) {
		$expiration_date = date("Y/m/d", strtotime($row['END_DATE']));
		if(!is_null($row['END_DATE'])) {
			$expired = ($expiration_date < $today) ? 'Yes' : 'No';
			$expire_in_30_days = ($expiration_date >= $today && $expiration_date <= $thirty_days_later) ? 'Yes' : 'No';
		} else {
			$expired = '';
			$expire_in_30_days = '';
		}
		$report->setRow(array(
			array('value' => $row['OWNER_ID']),
			array('value' => $row['OWNER_NAME']),
			array('value' => $row['FACILITY_ID']),
			array('value' => $row['FINANCIAL_METHOD']),
			array('value' => $row['FINANCIAL_PROVIDER']),
			array('value' => $row['POLICY_NUMBER']),
			array('value' => $row['AMOUNT'], 'style' => Report::$STYLE_MONEY),
			array('value' => Report::TO_DATE($row['BEGIN_DATE']), 'style' => Report::$STYLE_DATE),
			array('value' => Report::TO_DATE($row['END_DATE']), 'style' => Report::$STYLE_DATE),
			array('value' => $expired),
			array('value' => $expire_in_30_days),
			array('value' => $row['COVERED_TANKS_COUNT'])
		));
	}
}

$flag = $report->output('insurance_review');


?>
