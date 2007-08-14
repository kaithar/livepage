<?php

//die ("Handbrake on!");

define("MASSUPGRADE",true);

if (!(include('config.php')))
  die("Please configure the software");
  
if ($config['vhosts'] != 1) die("Not vhosted");
if (!$config['disabled']) die("Not disabled");

if ((!$config['massupgradepass'])||($config['massupgradepass'] != $_POST['pass']))
  die("<html><body><form method='POST'><input type='password' name='pass'/><input type='submit'/></form></body></html>");

$done = Array();

$files = scandir("domains");

foreach ($files as $v)
{
  if (!preg_match("/\.php$/",$v))
    continue;
  
  print "Attempting $v<br/>";
  
  $config = Array(
      'db_name' => "",
      'db_user' => "",
      'db_pass' => "",
      'domain' => ""
  );
  
  include("domains/".$v);
  
  if (($config['db_name'] == "") or
      ($config['db_user'] == "") or
      ($config['db_pass'] == "") or
      ($config['domain'] == ""))
  {
    print "$v failed (missing config info).<br/>";
    continue;
  }
  
  mysql_connect("localhost", $config['db_user'], $config['db_pass']) or die("Could not connect");
  mysql_select_db($config['db_name']) or die("Could not select database");  
  include ("upgrade.php");
  mysql_close();
}

?>
