<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Database connection settings
 *
 * <b>How this is supposed to work:</b>
 * multiple database connections are defined as arrays, or "groups". If no group
 * name is used when loading the database library, the group named "default"
 * will be used.
 *
 * Each group can be connected to independently, and multiple groups can be
 * connected at once, for multiple DB connections.
 *
 * -------------------------------------------------
 *
 *
 *
 * Note also that the 'database' subkey needs to include (for Oracle purposes) the entire connection string.
 * Use the example string in the 'oracle' key in the code of this file as an example of a properly formatted 'database' subkey value.
 *
 * Group Options:
 * - <pre>show_errors   - Enable or disable database exceptions</pre>
 * - <pre>benchmark     - Enable or disable database benchmarking</pre>
 * - <pre>persistent    - Enable or disable a persistent connection</pre>
 * - <pre>connection    - DSN identifier: driver://user:password@server/database</pre>
 * - <pre>character_set - Database character set</pre>
 * - <pre>table_prefix  - Database table prefix</pre>
 * - <pre>object        - Enable or disable object results</pre>
 */

global $GLOBAL_INI;
if (! $GLOBAL_INI['dbconnectr'])
	exit('No DB credential found.');

$commonDBinfo = array('type' => 'oracle', 'socket' => FALSE);

$config['default'] = array(
	'show_errors'   => TRUE,
	'benchmark'     => TRUE,
	'persistent'    => FALSE,
	'connection'    => array_merge($commonDBinfo, $GLOBAL_INI['dbconnectr']),
	'character_set' => 'utf8',
	'table_prefix'  => '',
	'object' => TRUE, //!!!!
	'cache' => FALSE,
	'escape' => TRUE,
);

$config['outparam_indicators'] = array('_cursor', '_out', '_inout',);
