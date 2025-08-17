<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @link http://docs.kohanaphp.com/libraries/session Kohana Session class documentation
 * @package contextr
 * @subpackage config
 * @author Todd Hochman
 * @version 1.0
 *
 */
/**
 * Session driver name.
 */
$config['driver'] = 'native';
/**
 * Session storage parameter, used by drivers.
 */
$config['storage'] = '';

/**
 * Session name.
 * It must contain only alphanumeric characters and underscores. At least one letter must be present.
 */
global $GLOBAL_INI;
$config['name'] = 'onestop' . $GLOBAL_INI['instance']['environment'];  // diff name for each inst

/**
 * Session parameters to validate: user_agent, ip_address, expiration.
 */
//$config['validate'] = array('user_agent', 'ip_address', 'expiration');
$config['validate'] = array();

/**
 * Enable or disable session encryption.
 * Note: this has no effect on the native session driver.
 * Note: the cookie driver always encrypts session data. Set to TRUE for stronger encryption.
 */
$config['encryption'] = FALSE;

/**
 * Session lifetime. Number of seconds that each session will last.
 * A value of 0 will keep the session active until the browser is closed (with a limit of 24h).
 */
//$config['expiration'] = 2400;//40 minutes
$config['expiration'] = 4800;//80 minutes

/**
 * Number of page loads before the session id is regenerated.
 * A value of 0 will disable automatic session id regeneration.
 */
$config['regenerate'] = 0;

/**
 * Percentage probability that the gc (garbage collection) routine is started.
 *
 * <b>NOTE TO NMED DEVELOPERS:</b> This should be set to ZERO (0) under debian/Ubuntu; Garbage collection
 * should be done via cron under these OSes
 */
$config['gc_probability'] = 0;
