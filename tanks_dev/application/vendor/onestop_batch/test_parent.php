<?php
/////////////////////////////////////////////////////////////
// this is a test script that will launch child php script
// it uses 'at' for launching child as a separate process
/////////////////////////////////////////////////////////////

echo 'about to run<br />';

$script = '/home/mlee/onestop/onestop_application/vendor/onestop_batch/test_child.php';
$out_redir = '/home/mlee/onestop/onestop_application/logs/test_output.txt';
$err_redir = '/dev/null';

// loads oci8, which is not included in CLI PHP php.ini
shell_exec("echo \"/usr/bin/php -q -d extension=oci8.so {$script} 14245 '09-OCT-12' '09-OCT-12' 2013 >>{$out_redir}\" | at now 2>{$err_redir}");

echo 'Done.';

?>
