<?php

error_reporting(0);

$config = Array();

/**
 * Vhosts ... switch for multisite mode
 * 0 - Single database / All domains show same site
 * 1 - Multi database / Domains show different sites
 */
$config['vhosts'] = 0;

/**
 * Should we be spewing debug info?
 * 0 - Show no errors
 * 1 - Show all errors
 */
$config['debug'] = 0;

/**
 * Load global config
 */

include('config.php')
		or die("Please configure the software");

/**
 * In multisite mode, use the domain name requested to load settings
 */

if ($config['vhosts'] == 1)
{
	/**
	 * Notice the www is trimmed.  Bit of a problem should www.com want to host with us.
	 */
	$domain = $_SERVER['HTTP_HOST'];
	preg_replace("/^www\./","",$domain);
	include ('domains/'.$domain)
			or die("I'm sorry, unable to find a site by that name.");
}

/**
 * We should have proper config details now... lets check...
 */
$failed = 0;

/**
 * First and most important ... are we debugging?
 */
if ($config['debug'] == 1)
	error_reporting(E_ALL);

/**
 * Is this site disabled? (Locally or globally)
 */
if ($config['disabled'])
	die($config['disabled']);
else
	$config['disabled'] = 0;

/**
 * Database config stuff ... no defaults
 */
if (($config['db_name'] == "") or
		($config['db_user'] == "") or
		($config['db_pass'] == ""))
{
	$failed = 1;
}

/**
 * Any config errors?
 */
if ($failed == 1)
	die("Failed to load config");


/**
 * Now to load the database
 */
require_once("includes/db.php");

?>
