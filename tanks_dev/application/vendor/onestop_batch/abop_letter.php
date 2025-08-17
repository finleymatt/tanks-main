<?php

set_time_limit(3000);  // 50min. default insider setting: 30 secs

if (php_sapi_name() != 'cli')
	echo_log('Invoice batch script called outside of CLI.', TRUE);

$envfile = '/home/mlee/tanks.ini';

$GLOBAL_INI = parse_ini_file($envfile, TRUE);

// relative paths don't work for includes for some reason
require_once("{$GLOBAL_INI['kohana']['application_path']}/vendor/reports/Report.php");
require_once("{$GLOBAL_INI['kohana']['application_path']}/vendor/reports/tcpdf_old/tcpdf.php");
require_once('abop_letter_print.php');


$conn = db_connect($GLOBAL_INI['dbconnectr']);
$owner_rows = select_owners($conn);

// write log ------------------------------------------------------------------
echo_log("Starting batch AB operator letter print.");

$temp_rows = db_query($conn, 'select ustx.log_seq.nextval PROCESS_ID from dual');
$process_id = $temp_rows[0]['PROCESS_ID'];
db_log($conn, $process_id, "New_abop_start");

// print_letter ---------------------------------------------------------------

$filenames = array();
foreach($owner_rows as $seq => $owner_row) {
	$seq_str = '_' . str_pad($seq, 4, '0', STR_PAD_LEFT);

	$filenames[] = print_letter($owner_row, $conn, $seq_str);
if ($seq > 3) break;
}

// post-process ---------------------------------------------------------------
if (count($filenames) > 1) {
	// merge all pdf into one pdf using ghostscript into download folder
	$err_redir = "{$GLOBAL_INI['kohana']['application_path']}/logs/batch_log.txt";
	shell_exec("gs -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -sOutputFile={$GLOBAL_INI['kohana']['www_path']}/download/all_abop_letters.pdf {$GLOBAL_INI['kohana']['application_path']}/cache/abop*.pdf 2>>{$err_redir}");
}

db_log($conn, $process_id, "New_abop_finish " . count($filenames));
echo_log('Finished batch AB operator letter print.');

// clean up temporary files -----------------------------------------
foreach($filenames as $filename)
	unlink($filename);

?>
