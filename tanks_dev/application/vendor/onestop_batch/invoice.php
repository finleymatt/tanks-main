<?php
//////////////////////////////////////////////////////////////////////////////
// action options:
//	gen - generate
//	print - print
//	gen-print - generate, and then print
//////////////////////////////////////////////////////////////////////////////

set_time_limit(3000);  // 50min. default insider setting: 30 secs

if (php_sapi_name() != 'cli')
	echo_log('Invoice batch script called outside of CLI.', TRUE);

if ($argc < 8)  // this script name, envfile, action, owner_id, fy, invoice_date, due_date, print_opt
	echo_log('Not enough arguments provided.', TRUE);

list(, $envfile, $action, $owner_id, $fy, $invoice_date, $due_date, $user_id, $print_opt) = $argv;
$is_single_owner = (empty($owner_id) ? false : true);
$print_opt = ($is_single_owner ? 'single' : $print_opt);  // if owner specified, 'single' is only option

if (!in_array($action, array('gen', 'print', 'gen-print')))
	echo_log("Unknown action specified: {$action}", TRUE);

$GLOBAL_INI = parse_ini_file($envfile, TRUE);

// relative paths don't work for includes for some reason
require_once("{$GLOBAL_INI['kohana']['application_path']}/vendor/reports/Report.php");
require_once("{$GLOBAL_INI['kohana']['application_path']}/vendor/reports/tcpdf_old/tcpdf.php");
require_once('invoice_print.php');

$conn = db_connect($GLOBAL_INI['dbconnectr']);

//////////////////////////////////////////////////////////////////////////////

// generate invoice(s), if specified -------------------------------------
// does not need db_log, since logging for gen is done in ustx.invoice.main
if (($action == 'gen') || ($action == 'gen-print')) {  // db logging occurs in stored proc
	echo_log("Starting batch invoice generation with parameters: owner_id({$owner_id}), invoice_date({$invoice_date}), due_date({$due_date}), fy({$fy}).");
	db_query($conn, "BEGIN ustx.invoice.main({$owner_id}, '{$invoice_date}', '{$due_date}', {$fy}, '{$user_id}', :inv_id); END;", NULL, ':inv_id');
	echo_log('Finished batch invoice generation.');
}

// print invoice(s), if specified ------------------------------------------
if (($action == 'print') || ($action == 'gen-print')) {
	echo_log("Starting batch invoice print with parameters: owner_id({$owner_id}), invoice_date({$invoice_date}), due_date({$due_date}), fy({$fy}), print_opt({$print_opt}).");

	$temp_rows = db_query($conn, 'select ustx.log_seq.nextval PROCESS_ID from dual');
	$process_id = $temp_rows[0]['PROCESS_ID'];
	db_log($conn, $process_id, "Batch invoice print start {$owner_id} {$invoice_date} {$due_date} {$fy}");

	$invoice_rows = select_invoices($conn, $owner_id, $invoice_date, $fy, $print_opt);

	$filenames = array();
	foreach($invoice_rows as $seq => $invoice) {
		// if batch, make sure files are named with sequence number
		$seq_str = ($is_single_owner ? NULL : '_' . str_pad($seq, 4, '0', STR_PAD_LEFT));

		$filename = print_invoice($invoice['ID'], $print_opt, TRUE, $conn, $seq_str);
		if ($filename)
			$filenames[] = $filename;
	}

	// post-process -------------------------------------------------------
	if (count($filenames) > 1) {
		// zip all pdf into invoice.zip ---------------------
		$zip = new ZipArchive();
		if ($zip->open("{$GLOBAL_INI['kohana']['www_path']}/download/invoices.zip", ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE) !== TRUE)
			echo_log('Zip file creation failed.', TRUE);
		foreach ($filenames as $filename)
			$zip->addFile($filename, basename($filename)); // add file, but remove path info
		$zip->close();
		
		// merge all pdf into one pdf using ghostscript -------------
		$err_redir = "{$GLOBAL_INI['kohana']['application_path']}/logs/batch_log.txt";
		shell_exec("gs -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -sOutputFile={$GLOBAL_INI['kohana']['www_path']}/download/all_invoices.pdf {$GLOBAL_INI['kohana']['application_path']}/cache/inv*.pdf 2>>{$err_redir}");
	}
	elseif (count($filenames) == 1)
		copy($filenames[0], "{$GLOBAL_INI['kohana']['www_path']}/download/" . basename($filenames[0]));

	// if owner specified, only one invoice printed
	$invoice_id = (($owner_id && count($filenames)) ? $invoice['ID'] : 0);
	db_log($conn, $process_id, "Batch invoice print finish {$owner_id} {$invoice_date} {$due_date} {$fy} " . count($filenames) . " {$invoice_id}");
	echo_log('Finished batch invoice print.');

	// clean up temporary files -----------------------------------------
	foreach($filenames as $filename)
		unlink($filename);
}

//////////////////////////////////////////////////////////////////////////////
// local functions
//////////////////////////////////////////////////////////////////////////////

/**
 * Returns invoice IDs of the invoices to print
 */
function select_invoices($conn, $owner_id, $invoice_date, $fy, $print_opt) {
	$bound_vars = array();

	// get invoice IDs to print --------------------------------------
	switch($print_opt) {
		case 'single' :
			$sql = "-- when owner_id is specified, return only one invoice
				-- uses subquery since Max returns one row even when NULL
				SELECT * FROM (
					SELECT Max(id) ID
					FROM ustx.invoices
					WHERE owner_id = :owner_id
						AND invoice_date = :invoice_date
						AND invoice_code = 'UST') max_id
				WHERE max_id.ID IS NOT NULL";
			$bound_vars[':invoice_date'] = $invoice_date;
			$bound_vars[':owner_id'] = $owner_id;
			break;
		case 'all_fys' :
			$sql = "-- matches invoice_date
				SELECT invoices.id ID
				FROM ustx.invoices, ustx.owners_mvw owners,
					(select owner_id, sum(DECODE(TRANSACTION_CODE, 'PP', AMOUNT*-1, 'LP', AMOUNT*-1, 
						'WP', AMOUNT*-1, 'IP', AMOUNT*-1, 'PW', AMOUNT*-1, 'LW', AMOUNT*-1, 
						'IW', AMOUNT*-1, AMOUNT)) balance
					from ustx.transactions
					where fiscal_year > 1978
						and INSTR(TRANSACTION_CODE, 'H')=0
						and INSTR(TRANSACTION_CODE, 'G')=0
					group by owner_id
					having sum(DECODE(TRANSACTION_CODE, 'PP', AMOUNT*-1, 'LP', AMOUNT*-1, 
						'WP', AMOUNT*-1, 'IP', AMOUNT*-1, 'PW', AMOUNT*-1, 'LW', AMOUNT*-1, 
						'IW', AMOUNT*-1, AMOUNT)) > 0) balance
				WHERE trunc(invoices.invoice_date) = :invoice_date
					AND invoices.owner_id = owners.id
					AND owners.id = balance.owner_id 
				ORDER BY owners.owner_name";
			$bound_vars[':invoice_date'] = $invoice_date;
			break;
		case 'selected_fy' :
			$sql = "-- matches invoice_date and fy
				SELECT invoices.id ID
				FROM ustx.invoices, ustx.owners_mvw owners
				WHERE trunc(invoices.invoice_date) = :invoice_date
					AND invoices.owner_id = owners.id
					AND invoices.owner_id in (select owner_id
						from ustx.transactions
						where instr(transaction_code, 'H')=0
							and instr(transaction_code, 'G')=0
							and fiscal_year between 1979 and :fy - 1
						group by owner_id
						having sum(decode(transaction_code,'PP',amount*-1,'LP',amount*-1,
						'WP',amount*-1,'IP',amount*-1,'PW',amount*-1,
						'LW', amount*-1, 'IW', amount*-1, amount)) = 0)
					AND invoices.owner_id in (select owner_id
						from ustx.transactions
						where instr(transaction_code, 'H')=0
							and instr(transaction_code, 'G')=0
							and fiscal_year = :fy
						group by owner_id
						having sum(decode(transaction_code,'PP',amount*-1,'LP',amount*-1,
							'WP',amount*-1,'IP',amount*-1,'PW',amount*-1,
							'LW', amount*-1, 'IW', amount*-1, amount)) > 0)
				ORDER BY owners.owner_name";
			$bound_vars[':invoice_date'] = $invoice_date;
			$bound_vars[':fy'] = $fy;
			break;
		case 'prior_fys' :
			$sql = "-- matches invoice_date and fy
				SELECT invoices.id ID
				FROM ustx.invoices, ustx.owners_mvw owners
				WHERE invoices.invoice_date = :invoice_date
					AND invoices.owner_id = owners.id
					AND invoices.owner_id in (select owner_id
						from ustx.transactions
						where instr(transaction_code, 'H')=0
							and instr(transaction_code, 'G')=0
							and fiscal_year between 1979 and :fy - 1
						group by owner_id
						having sum(decode(transaction_code,'PP',amount*-1,'LP',amount*-1,
							'WP',amount*-1,'IP',amount*-1,'PW',amount*-1,
							'LW', amount*-1, 'IW', amount*-1, amount)) > 0)
				ORDER BY owners.owner_name";
			$bound_vars[':invoice_date'] = $invoice_date;
			$bound_vars[':fy'] = $fy;
			break;
		case 'selected_prior1_fy' :
			$sql = "-- matches invoice_date and fy-2 and fy
				SELECT invoices.id ID
				FROM ustx.invoices, ustx.owners_mvw owners
				WHERE trunc(invoices.invoice_date) between to_date(:invoice_date,'dd-mon-yyyy')-2 and :invoice_date
					and invoices.owner_id = owners.id
					and invoices.owner_id in (select owner_id
						from ustx.transactions
						where instr(transaction_code, 'H')=0
							and instr(transaction_code, 'G')=0
							and fiscal_year between 1979 and :fy - 2
						group by owner_id
						having sum(decode(transaction_code,'PP',amount*-1,'LP',amount*-1,
							'WP',amount*-1,'IP',amount*-1,'PW',amount*-1,
							'LW', amount*-1, 'IW', amount*-1, amount)) = 0)
					and invoices.owner_id in (select owner_id
						from ustx.transactions
						where instr(transaction_code, 'H')=0
							and instr(transaction_code, 'G')=0
							and fiscal_year between :fy - 1 and :fy
						group by owner_id
						having sum(decode(transaction_code,'PP',amount*-1,'LP',amount*-1,
							'WP',amount*-1,'IP',amount*-1,'PW',amount*-1,
							'LW', amount*-1, 'IW', amount*-1, amount)) > 0)
				ORDER BY owners.owner_name";
			$bound_vars[':invoice_date'] = $invoice_date;
			$bound_vars[':fy'] = $fy;
			break;
		default: echo_log("Unknown invoice print_opt specfied: {$print_opt}.", TRUE);
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
