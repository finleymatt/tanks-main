<?php
//////////////////////////////////////////////////////////////////////////////
// Command line batch script for generating certificate records in DB
//   and printing certificates into PDFs.
// If multiple are printed, files are merged into 'all_certs_{count}.pdf'
//////////////////////////////////////////////////////////////////////////////

set_time_limit(3000);  // 50min. default insider setting: 30 secs

if (php_sapi_name() != 'cli')
	echo_log('Certificate batch script called outside of CLI.', TRUE);

if ($argc < 6)  // this script name, envfile, owner_id, facility_id, date_permitted, fy
	echo_log('Not enough arguments provided.', TRUE);

list(, $envfile, $owner_id, $facility_id, $date_permitted, $fy, $user_id) = $argv;
$is_batch = (empty($owner_id) && empty($facility_id) ? true : false);

$GLOBAL_INI = parse_ini_file($envfile, TRUE);

// relative paths don't work for includes for some reason
require_once("{$GLOBAL_INI['kohana']['application_path']}/vendor/reports/tcpdf_old/tcpdf.php");
require_once('certificate_print.php');

$conn = db_connect($GLOBAL_INI['dbconnectr']);

// get all facilities of a single owner
if(!empty($owner_id) && empty($facility_id)) {
	$is_owner_batch = true;
	$sql = "select * from USTX.facilities_mvw where owner_id = :owner_id";
	$bound_vars[':owner_id'] = $owner_id;
	$facilities = db_query($conn, $sql, $bound_vars);
} else {
	$is_owner_batch = false;
}

///////////////////////////////////////////////////////////////////////////////

// generate cert(s) ===========================================================
echo_log("Starting batch certficate generation with parameters: owner_id({$owner_id}), facility_id({$facility_id}), date_permitted({$date_permitted}), fy({$fy}).");

if ($is_batch)
	db_query($conn, "BEGIN ustx.permit.refresh_all({$fy}, '{$user_id}'); END;");
else if ($is_owner_batch) {
	foreach($facilities as $facility) {
		db_query($conn, "BEGIN ustx.permit.insert_single({$owner_id}, {$facility['ID']}, {$fy}, '{$date_permitted}', '{$user_id}'); END;");
	}
}
else
	db_query($conn, "BEGIN ustx.permit.insert_single({$owner_id}, {$facility_id}, {$fy}, '{$date_permitted}', '{$user_id}'); END;");

echo_log('Finished batch certificate generation.');

// print certificates =========================================================
echo_log("Starting batch certificate print with parameters: owner_id({$owner_id}), facility_id({$facility_id}), date_permitted({$date_permitted}), fy({$fy}).");

$temp_rows = db_query($conn, 'select ustx.log_seq.nextval PROCESS_ID from dual');
$process_id = $temp_rows[0]['PROCESS_ID'];
db_log($conn, $process_id, "New_cert_start {$owner_id} {$facility_id} {$date_permitted} {$fy}");

$cert_rows = select_certs($conn, $is_batch, $is_owner_batch, $owner_id, $facility_id, $fy);

$filenames = array();
foreach($cert_rows as $seq => $cert) {
	// if batch, make sure certs are named with sequence number
	$seq_str = ($is_batch ? '_' . str_pad($seq, 4, '0', STR_PAD_LEFT) : NULL);
	
	$filenames[] = print_cert($cert, $seq_str);
}

// if > 1 file, zip all new files ---------------------------
$cert_count = count($filenames);
if ($cert_count > 1) {
	/********* instead of zipping, merging into one pdf now
	$zip = new ZipArchive();
	if ($zip->open("{$GLOBAL_INI['kohana']['www_path']}/download/all_certs.zip", ZIPARCHIVE::OVERWRITE)!==TRUE)
		echo_log('Zip file creation failed.', TRUE);
	foreach ($filenames as $filename)
		$zip->addFile($filename, basename($filename)); // add file, but remove path info
	$zip->close();
	*******************************************************/
	$err_redir = "{$GLOBAL_INI['kohana']['application_path']}/logs/batch_log.txt";
	shell_exec("gs -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -sOutputFile={$GLOBAL_INI['kohana']['www_path']}/download/all_certs_{$cert_count}.pdf {$GLOBAL_INI['kohana']['application_path']}/cache/cert*.pdf 2>>{$err_redir}");
}
elseif ($cert_count == 1)
	copy($filenames[0], "{$GLOBAL_INI['kohana']['www_path']}/download/" . basename($filenames[0]));

db_log($conn, $process_id, "New_cert_finish {$owner_id} {$facility_id} {$date_permitted} {$fy} {$cert_count}");
echo_log('Finished batch certificate print.');

// clean up temporary files ===================================================
foreach($filenames as $filename)
	unlink($filename);

// enter 'date_printed' for printed certs =====================================
if ($cert_count)
	date_certs($conn, $is_batch, $owner_id, $facility_id, $fy, $cert_rows, $user_id);


//////////////////////////////////////////////////////////////////////////////
// local functions
//////////////////////////////////////////////////////////////////////////////

/**
 * Returns all certifcate and related data needed for printing
 */
function select_certs($conn, $is_batch, $is_owner_batch, $owner_id, $facility_id, $fy) {
	$bound_vars = array();

	if ($is_batch) {
		$sql = "-- for finding all refreshed certificates to print
			SELECT O.id owner_id, owner_name, O.address1 owner_address_1,
				O.address2 owner_address_2, O.city||', '||O.state||' '||O.zip owner_address_3,
				F.id facility_id, facility_name, F.address1 facility_address_1, F.address2 facility_address_2, F.city||', '||F.state||' '||F.zip facility_address_3,
				P.tanks, to_char(P.date_permitted,'ddth') permit_date_day, trim(to_char(P.date_permitted,'Month')) permit_date_month, to_char(P.date_permitted,'yyyy') permit_date_year, P.permit_number,
				FY.fiscal_year, to_char(start_date,'yyyy') start_date, to_char(end_date,'yyyy') end_date
			FROM ustx.owners_mvw O, ustx.facilities_mvw F, ustx.permits P, ustx.fiscal_years FY
			WHERE P.owner_id = O.id 
				and P.facility_id = F.id 
				and P.fiscal_year = FY.fiscal_year 
				and FY.fiscal_year = :fy
				and P.permit_number is not null
				and P.date_printed is null
				and P.tanks > 0
			ORDER BY owner_name, facility_name";
		$bound_vars[':fy'] = $fy;
	}
	else if ($is_owner_batch) {
		$sql = "-- for finding all refreshed certificates to print
			SELECT O.id owner_id, owner_name, O.address1 owner_address_1,
				O.address2 owner_address_2, O.city||', '||O.state||' '||O.zip owner_address_3,
				F.id facility_id, facility_name, F.address1 facility_address_1, F.address2 facility_address_2, F.city||', '||F.state||' '||F.zip facility_address_3,
				P.tanks, to_char(P.date_permitted,'ddth') permit_date_day, trim(to_char(P.date_permitted,'Month')) permit_date_month, to_char(P.date_permitted,'yyyy') permit_date_year, P.permit_number,
				FY.fiscal_year, to_char(start_date,'yyyy') start_date, to_char(end_date,'yyyy') end_date
			FROM ustx.owners_mvw O, ustx.facilities_mvw F, ustx.permits P, ustx.fiscal_years FY
			WHERE O.id = :owner_id	
				and P.owner_id = O.id
				and P.facility_id = F.id
				and P.fiscal_year = FY.fiscal_year
				and FY.fiscal_year = :fy
				and P.tanks > 0
			ORDER BY facility_name";
		$bound_vars[':owner_id'] = $owner_id;
		$bound_vars[':fy'] = $fy;
	}
	else {
		$sql = "-- for finding single certificate to print
			SELECT O.id owner_id, owner_name, O.address1 owner_address_1,
				O.address2 owner_address_2, O.city||', '||O.state||' '||O.zip owner_address_3,
				F.id facility_id, facility_name, F.address1 facility_address_1, F.address2 facility_address_2, F.city||', '||F.state||' '||F.zip facility_address_3,
				P.tanks, to_char(P.date_permitted,'ddth') permit_date_day, trim(to_char(P.date_permitted,'Month')) permit_date_month, to_char(P.date_permitted,'yyyy') permit_date_year, P.permit_number,
				FY.fiscal_year, to_char(start_date,'yyyy') start_date, to_char(end_date,'yyyy') end_date
			FROM ustx.owners_mvw O, ustx.facilities_mvw F, ustx.permits P, ustx.fiscal_years FY
			WHERE O.id = :owner_id
				and F.id = :facility_id
				and P.owner_id = O.id 
				and P.facility_id = F.id 
				and P.fiscal_year = FY.fiscal_year 
				and FY.fiscal_year = :fy
				and P.tanks > 0";
		$bound_vars[':owner_id'] = $owner_id;
		$bound_vars[':facility_id'] = $facility_id;
		$bound_vars[':fy'] = $fy;
	}

	return(db_query($conn, $sql, $bound_vars));
}

/**
 * Updates date_printed in all printed certifcates with today's date
 */
function date_certs($conn, $is_batch, $owner_id, $facility_id, $fy, $certs, $user_id) {
	$bound_vars = array();

	if ($is_batch) {
		$sql = "-- when permit is printed, update date_printed for all fy permits
			UPDATE ustx.permits
				SET date_printed = SYSDATE,
				user_modified = :user_id,
				date_modified = SYSDATE
			WHERE fiscal_year = :fy
				AND permit_number is not null
				AND date_printed is null";
		$bound_vars[':fy'] = $fy;
		$bound_vars[':user_id'] = $user_id;
	}
	else {
		$sql = "-- when permit is printed, update date_printed for single permit
			UPDATE ustx.permits
				SET date_printed = SYSDATE,
				user_modified = :user_id,
				date_modified = SYSDATE
			WHERE fiscal_year = :fy
				AND owner_id = :owner_id
				AND facility_id = :facility_id
				AND permit_number = :permit_number";
		$bound_vars[':owner_id'] = $owner_id;
		$bound_vars[':facility_id'] = $facility_id;
		$bound_vars[':fy'] = $fy;
		$bound_vars[':permit_number'] = $certs[0]['PERMIT_NUMBER'];
		$bound_vars[':user_id'] = $user_id;
	}

	return(db_query($conn, $sql, $bound_vars));
}

function echo_log($line, $do_exit=FALSE) {
	echo(date('Y-M-d H:i') . ' ' . $line . "\n");
	if ($do_exit)
		exit();
}


function db_connect($db_config) {
	$conn = oci_connect($db_config['user'], $db_config['pass'], $db_config['database']);

	if (!$conn) {
		echo_log('DB connection failed.');
		exit();
	}

	return $conn;
}

function db_query($conn, $query, $bound_vars=NULL, $ret_name=NULL) {
	$stmt = oci_parse($conn, $query);

	if ($bound_vars)
		foreach ($bound_vars as $name => $value) // bind variables if supplied
			oci_bind_by_name($stmt, $name, $bound_vars[$name]);

	if ($ret_name) { // if is proc and has return parameter
	        $ret_value = 0;
        	oci_bind_by_name($stmt, $ret_name, $ret_value, SQLT_LNG);
	}

	$stmt_type = oci_statement_type($stmt);
	if(!oci_execute($stmt)) {
		echo_log('DB query failed: ' . $query);
		exit();
	}

	if ($stmt_type == 'SELECT') {
		oci_fetch_all($stmt, $arr, 0, -1, OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC);
		return($arr);
	}
	else
		return(TRUE);
}

function db_log($conn, $process_id, $mesg) {
	db_query($conn, "INSERT INTO USTX.UST_LOG (process_id, log_timestamp, log_text) VALUES
		({$process_id}, sysdate, '{$mesg}')");
}
?>
