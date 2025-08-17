<?php defined('SYSPATH') or die('No direct script access.');

global $GLOBAL_INI;

$config['sepappid'] = $GLOBAL_INI['authenticatr']['sepappid'];

$commonDBinfo = array(
	'type' => 'oracle',
	'port' => 1521,
	'socket' => FALSE
);

$config['sepdb'] = array(
	'show_errors'   => TRUE,
	'benchmark'     => TRUE,
	'persistent'    => FALSE,
	'connection'    => array_merge($commonDBinfo, $GLOBAL_INI['authenticatr']),
	'character_set' => 'utf8',
	'table_prefix'  => '',
	'object' => TRUE,
	'cache' => FALSE,
	'escape' => TRUE
);

