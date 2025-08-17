<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Base path of the web site. If this includes a domain, eg: localhost/kohana/
 * then a full URL will be used, eg: http://localhost/kohana/. If it only includes
 * the path, and a site_protocol is specified, the domain will be auto-detected.
 *
 * %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
 * %%%%%%%%%%%%%%%%%%%%%% IMPORTANT NOTES FOR NMED DEVELOPERS: %%%%%%%%%%%%%%%%%%%%%%%%%%%
 * %% KOHANA NEEDS A COPY OF THIS FILE IN YOUR [APPLICATION]/CONFIG FOLDER FOR EACH DISTINCT APPLICATION!!! %%%
 * %% THIS FILE WILL AT LEAST NEED TO HAVE THE SITE_DOMAIN BELOW CHANGED FOR EACH DISTINCT APPLICATION %%
 * %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
 */

global $GLOBAL_INI;

$config['site_domain'] = $GLOBAL_INI['instance']['site_domain'];

/**
 * Force a default protocol to be used by the site. If no site_protocol is
 * specified, then the current protocol is used, or when possible, only an
 * absolute path (with no protocol/domain) is used.
 */
$config['site_protocol'] = 'https';

/**
 * Name of the front controller for this application. Default: index.php
 * This can be removed by using URL rewriting.
 */
$config['index_page'] = '';

/**
 * Fake file extension that will be added to all generated URLs. Example: .html
 */
$config['url_suffix'] = '';

/**
 * Length of time of the internal cache in seconds. 0 or FALSE means no caching.
 * The internal cache stores file paths and config entries across requests and
 * can give significant speed improvements at the expense of delayed updating.
 */
$config['internal_cache'] = FALSE;

/**
 * Enable or disable gzip output compression. This can dramatically decrease
 * server bandwidth usage, at the cost of slightly higher CPU usage. Set to
 * the compression level (1-9) that you want to use, or FALSE to disable.
 *
 * Do not enable this option if you are using output compression in php.ini!
 */
$config['output_compression'] = FALSE;

/**
 * Enable or disable global XSS filtering of GET, POST, and SERVER data. This
 * option also accepts a string to specify a specific XSS filtering tool.
 */
$config['global_xss_filtering'] = TRUE;

/**
 * Enable or disable hooks. Setting this option to TRUE will enable
 * all hooks. By using an array of hook filenames, you can control
 * which hooks are enabled. Setting this option to FALSE disables hooks.
 */
$config['enable_hooks'] = FALSE;

/**
 * Log thresholds:
 *  0 - Disable logging
 *  1 - Errors and exceptions
 *  2 - Warnings
 *  3 - Notices
 *  4 - Debugging
 */
$config['log_threshold'] = 1;

/**
 * Message logging directory.
 */
$config['log_directory'] = APPPATH.'logs';

/**
 * Enable or disable displaying of Kohana error pages. This will not affect
 * logging. Turning this off will disable ALL error pages.
 */
$config['display_errors'] = TRUE;

/**
 * Enable or disable statistics in the final output. Stats are replaced via
 * specific strings, such as {execution_time}.
 *
 * @see http://docs.kohanaphp.com/general/configuration
 */
$config['render_stats'] = TRUE;

/**
 * Filename prefixed used to determine extensions. For example, an
 * extension to the Controller class would be named MY_Controller.php.
 * NMED DEVS: THERE IS NO REALLY GOOD REASON TO CHANGE THIS; KEEPING IT AS IS INCREASES CODE REUSABILITY!
 */
$config['extension_prefix'] = 'MY_';

/**
 * Additional resource paths, or "modules". Each path can either be absolute
 * or relative to the docroot. Modules can include any resource that can exist
 * in your application directory, configuration files, controllers, views, etc.
 */
$config['modules'] = array
(
	//MODPATH.'auto_modeler', //zombor's automodeler library
	//MODPATH.'sys_class_overloadr',
	//MODPATH.'contextr',
	MODPATH.'authenticatr',
	MODPATH.'dbconnectr', //DB connectivity
	//MODPATH.'formr',     // Form generation - NMED revised version of Forge for 2.2
	//MODPATH.'headr', // <head> module
	//MODPATH.'displayr', //need for MY_Router library
	//MODPATH.'reportr', //PHPExcel library in a KPF wrapper
	//MODPATH.'templatr', //custom NMED stuff, including head helper, security system, and other stuff
);

