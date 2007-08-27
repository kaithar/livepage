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

if (!(include_once('config.php')))
	die("Please configure the software");

/**
 * In multisite mode, use the domain name requested to load settings
 */

if ($config['vhosts'] == 1)
{
  $configbk = $config;
  $config = Array();
  
	/**
	 * Notice the www is trimmed.  Bit of a problem should www.com want to host with us.
	 */
	$domain = $_SERVER['HTTP_HOST'];
	$domain = preg_replace("/^www\./","",$domain);
  $domain = preg_replace("/\.\.+/",".",$domain);
	if (!(include_once ('domains/'.$domain.'.php')))
		die("I'm sorry, unable to find a site by that name.");
  
  
  // Override the local conf with some global values.
  $config['vhosts'] = 1;
  $config['massupgradepass'] = $configbk['massupgradepass'];
  
  if ($configbk['disabled'])
    $config['disabled'] = $configbk['disabled'];
  
  unset ($configbk);
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
{
  if (defined("INSTALLER"))
    print("<b>Warning:</b> Site is currently set as disabled! (\"{$config['disabled']}\")<br/>");
  else
	  die($config['disabled']);
}
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
 * Hyper upgrade password!
 */
if (!isset($config['massupgradepass']))
  $failed = 1;

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
