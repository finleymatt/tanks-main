<?php

require_once('db_funcs.php');

$GLOBALS['PRINT_SQL'] = array(
	'facility' => '
		SELECT *
		FROM ustx.facilities_mvw F
		WHERE F.owner_id = :owner_id
			and F.id in (select T.facility_id
				from ustx.tanks T
				where T.tank_status_code in (1, 2)
					and T.facility_id = F.id)'
);


/**
 * $conn: DB connection is passed in only when called via cli
 * */
function print_letter($owner_row, $conn=NULL, $seq=NULL) {
	global $GLOBAL_INI, $PRINT_SQL;

	// prepare data -------------------------------------------------------

	$owner_row = h($owner_row);

	$facility_rows = query(!is_null($conn), $conn, $PRINT_SQL['facility'], array(':owner_id' => $owner_row['ID']));
	$facility_html = '';
	foreach($facility_rows as $j => $facility) {
		$facility = h($facility);

		if ($j > 0) $facility_html .= '<br />';

		$facility_html .= "{$facility['FACILITY_NAME']} (Facility ID: {$facility['ID']})<br />
			{$facility['ADDRESS1']}<br />
			" . (empty($facility['ADDRESS2']) ? '' : "{$facility['ADDRESS2']}<br />") . "
			{$facility['CITY']}, {$facility['STATE']} {$facility['ZIP']}<br /><br />
			Name(s) of Class A and Class B Operators. The Bureau recommends that no more than one or two individuals be designated per facility. (please assure training certificate is attached for each individual listed):<br /><br />
			" . str_repeat('Class A, B, or Both: _____________
			Last Name: ____________________
			First Name: ____________________<br />', 4) . "<br />
			
			Tank Status:<br />
			<table cellpadding=2><tr>
				<td width=23>" .check_box() . "</td>
				<td>Active</td>
			</tr></table>
			<table cellpadding=2><tr>
				<td width=23>" .check_box() . "</td>
				<td width=400>Temporary out of Service.  Last active date ____________________</td>
			</tr></table>
			<table cellpadding=2><tr>
				<td width=23>" .check_box() . "</td>
				<td width=400>Other.  Please explain _____________________________________________</td>
			</tr></table><br />

			Confirmation of Class C Operator as per 20.5.18.11 NMAC:<br />
			<table cellpadding=2><tr>
				<td width=23>" .check_box() . "</td>
				<td width=400>Yes, this facility has a Class C Operator as per 20.5.18.11 NMAC.</td>
			</tr></table>
			<table cellpadding=2><tr>
				<td width=23>" .check_box() . "</td>
				<td width=400>No, this facility does not have a Class C Operator.</td>
			</tr></table><br />";
	}

	$html = '
<table border="0" width="100%">
<tr>
<td width="15%">
	<img src="' . "{$GLOBALS['GLOBAL_INI']['kohana']['www_path']}/images/nmed_logo_med.gif" . '" width="68px" />
</td>

<td width="85%">
	<br /><font size="14"><b>New Mexico Environment Department<br />
	Petroleum Storage Tank Bureau</b></font>
</td>
</tr>
</table><br />

<table border="0" width="100%">
	<tr><td width="48">Date:</td><td>' . $owner_row['NOW_DATE'] . '<br /></td></tr>
	<tr><td width="48">To:</td><td width=400>' . owner_address($owner_row, TRUE) . '</td></tr>
</table><br />

<p><b>Re: Class A, B, and C Operator Designee and Tank Status</b></p>

<p>The New Mexico Environment Department, Petroleum Storage Tank Bureau (PSTB), is requesting that Class A, B, and C operator training records be updated for all facilities to determine compliance with 20.5.18 NMAC.   To assist you we have provided a list of facilities registered to you or your company.  Please provide the following information for each facility attached:</p>

<ul>
<li>Name of Class A &amp; B operator <u>for each</u> facility and a copy <u>of each</u> operator\'s Class A and B Operator Certificate.</li>
<li>Confirmation of Class C Operator as per 20.5.18.11 NMAC.</li>
<li>A copy of each designated A/B Operator\'s training certificate for all facilities.</li>
<li>Please indicate the current tank status, and if inactive, the date when the tank was last active.*</li>
<li>Complete the enclosed Certification Statement with signature.</li>
</ul>

<p>Documentation is due ' . $owner_row['DUE_DATE'] . '.  Please submit your facility information to the address provided below for each facility listed:</p>

<table width="100%"><tr>
	<td width="25%"><b>Submit to:</b></td>

	<td width="50%" align="center">
		<table width="98%" border="2" cellpadding="3"><tr><td>
		Petroleum Storage Tank Bureau<br />
		Attn:  Antonette Cordova<br />
		2905 Rodeo Park Drive East, Building 1<br />
		Santa Fe, New Mexico 87505
		</td></tr></table>
	</td>

	<td width="25%"></td>
</tr></table><br />

<p>Information on Class A/B/C Operators is outlined within 20.5.18 NMAC and can be found on the PSTB web site, http://www.nmenv.state.nm.us/ust/ustbtop.html. If you have any questions in regards to this request, please contact Antonette Cordova at (505) 476-4392, or Micaela Fierro at (505) 476-4394, or Bertha Aragon at (505) 476-4393.</p>

<br /><br /><br /><br /><br /><br /><br />
<p><b>* Reminder, if one or more of the tanks at one or all of your facilities is temporarily out-of-service or permanently closed, you are required to notify PSTB per 20.5.8 NMAC.</b></p>

<tcpdf method="AddPage" />
<h2>Contact Information Form</h2>

<p>Each Owner/Operator is allowed <b>ONE (1)</b> primary correspondence contact who receives all correspondence from the Petroleum Storage Tank Bureau.  Please verify that the current owner contact information is correct with a check mark in the box provided.  If any of the information provided below is not correct, please make the necessary changes in the respective fields and the PSTB will change the information in our records and database accordingly.</p>

<h2>Owner Contact Information</h2>

<table width="100%" border="0" cellspacing="10"><tr>
<td width="46%">
	<p>
		' . owner_address($owner_row) . '<br /><br />
		Ph: ' .$owner_row['PHONE_NUMBER'] . '<br />
		Email: ' . $owner_row['EMAIL'] . '<br />
		Owner ID: ' . $owner_row['ID'] . '
	</p>

	<p><i>Please check here if the above contact information is correct:</i> ' . check_box() . '</p>
</td>

<td width="54%">
	Contact Name:<hr />
	Business Name:<hr />
	Address:<hr />
	City, State, Zip:<hr />
	Phone:                                              Fax:<hr />
	Email:<hr />
</td>
</tr></table>

<p>If the above information is incorrect or has changed, please provide the updated information to the right.  Please list your email address above to receive email correspondence.</p>

<tcpdf method="AddPage" />

<table border="1" bgcolor="#CCCCCC" width="100%" cellspacing="10" cellpadding="4"><tr><td align="center"><blockquote><b>A &amp; B Operators must be designated for the AST and/or UST Facilities listed below.</b></blockquote></td></tr></table><br />

' . $facility_html . '

<tcpdf method="AddPage" />
<h2>Certification Statement</h2>

<p>The Owner/Operator certifies that all Class A, Class B, and Class C Operator Training submittals have been REVIEWED and are COMPLETE and accurate. The Owner/Operator is aware that all updates and or changes to this information must be provided to the Petroleum Storage Tank Bureau within 30 days of update and or change.</p>

<table width="70%" border="0" cellspacing="5">
<tr>
	<td align="right" width="70">Owner Name:</td><td width="150"><br /><hr /></td>
	<td align="right" width="70">Owner ID:</td><td width="150"><br /><hr /></td>
</tr>
<tr>
	<td align="right" width="70">Signed By:</td><td width="150"><br /><hr /></td>
	<td align="right" width="70">Title:</td><td width="150"><br /><hr /></td>
</tr>
<tr>
	<td align="right" width="70">Printed Name:</td><td width="150"><br /><hr /></td>
</tr>
<tr>
	<td align="right" width="70">Date:</td><td width="150"><br /><hr /></td>
</tr>
<tr>
	<td align="right" width="70">Phone Number:</td><td width="150"><br /><hr /></td>
</tr>
<tr>
	<td align="right" width="70">E-mail:</td><td width="150"><br /><hr /></td>
</tr>
</table>';

	// start PDF ----------------------------------------------------------
	$pdf = new TCPDF('P', PDF_UNIT, 'LETTER', true, 'UTF-8', false);

	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('PSTB');
	$pdf->SetTitle('AB Operator Letter');

	$pdf->SetMargins(PDF_MARGIN_LEFT, 12, PDF_MARGIN_RIGHT); // default top: 27
	//$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	//$pdf->SetFooterMargin(PDF_MARGIN_HEADER);
	$pdf->setPrintHeader(FALSE);
	$pdf->setPrintFooter(FALSE);
	$pdf->SetAutoPageBreak(TRUE, 10); // default: 25
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	$pdf->AddPage();
	$pdf->SetFont('times', '', 11);

	$pdf->writeHTML($html, true, false, true, false, '');

	$filename = 'abop' . (is_null($seq) ? '' : $seq) . "_{$owner_row['ID']}.pdf";

	if ($conn) {
		$filename = "{$GLOBAL_INI['kohana']['application_path']}/cache/{$filename}";
		$pdf->Output($filename, 'F'); // F = save to local filesystem
	}
	else
		$pdf->Output($filename, 'D'); // D = download via browser

	return($filename);
}


function select_owners($conn, $owner_id=NULL) {
	$sql = "
		SELECT
			O.id, O.owner_name, O.address1, O.address2, O.city, O.state, O.zip, O.phone_number,
			E.title, E.title, E.fullname, E.email,
			TO_CHAR((SYSDATE + 30), 'MON DD, YYYY') due_date, TO_CHAR(SYSDATE, 'MON DD, YYYY') now_date
		FROM ustx.owners_mvw O
			LEFT OUTER JOIN (select entity_id, title, fullname, email from ustx.emails where entity_type = 'owner' and
				id in (select max(id) from ustx.emails group by entity_id)) E
				on O.id = E.entity_id
		WHERE O.id in (select distinct F.owner_id
			from ustx.facilities_mvw F
				inner join ustx.tanks T on F.id = T.facility_id
				where T.tank_status_code in (1, 2))
			" . (is_null($owner_id) ?  '' : "AND O.id = {$owner_id}") . "
		ORDER BY O.owner_name";

	return(query(!is_null($conn), $conn, $sql));
}


function owner_address($owner_row, $include_id=FALSE) {

	return( (empty($owner_row['FULLNAME']) ? '' : "{$owner_row['TITLE']} {$owner_row['FULLNAME']}<br />") . "
		{$owner_row['OWNER_NAME']}" . ($include_id ? " (Owner ID: {$owner_row['ID']})" : '') . "<br />
		{$owner_row['ADDRESS1']}<br />
		" . (empty($owner_row['ADDRESS2']) ? '' : "{$owner_row['ADDRESS2']}<br />") . "
		{$owner_row['CITY']}, {$owner_row['STATE']} {$owner_row['ZIP']}");
}


function check_box() {
	return('<table border="1" width="14"><tr><td></td></tr></table>');
}


function h($row) {
	foreach($row as $j => $str)
		$row[$j] = htmlspecialchars($str);

	return($row);
}
?>
