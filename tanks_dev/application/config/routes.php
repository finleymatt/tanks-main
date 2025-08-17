<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * File: Routes
 * This file enables kohana to do stuff like have a default controller loaded from the application root.
 * for more info check out the docs @ http://docs.kohanaphp.com/general/routing
 *
 * Supported Shortcuts:
 *  :any - matches any non-blank string
 *  :num - matches any number
 *
 * Options:
 *  _allowed - Permitted URI characters
 *  _default - Default route when no URI segments are found; base page of application
 *
 */
$config = array
(
	'_allowed' => 'a-z 0-9~%.:_-',
	'_default' => 'home',
);
