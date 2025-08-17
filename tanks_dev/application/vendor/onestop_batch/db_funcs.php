<?php
/*****************************************************************************
 * Collection of simple functions for accessing Oracle DB.
 * Used when running CLI scripts outside of Kohana framework.
 *
 *****************************************************************************/

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


// $is_cmdline parameter kept for backward compatibility with invoice_print just in case
function query($is_cmdline, $conn, $query, $bound_vars=array(), $ret_name=NULL) {
	if ($is_cmdline || $conn)
		return(db_query($conn, $query, $bound_vars, $ret_name));
	else
		return(Database::instance()->query($query, $bound_vars)->as_array());
}
?>
