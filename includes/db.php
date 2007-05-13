<?php

$link = mysql_connect("localhost", $config['db_user'], $config['db_pass'])
	or die("Could not connect");
mysql_select_db($config['db_name'])
	or die("Could not select database");

function mysql_do_query($sql, $comment = "") {
	$result = mysql_query($sql) or die("Sql Error!<br><br>$sql<br><br>".mysql_error()); 
	return $result;
}

function dbclose() {
	mysql_close();
}


$cuser = (isset($_COOKIE["cuser"]) ? $_COOKIE["cuser"] : Array("user_id" => 0, sid => "");
$sql = "SELECT *
          FROM cms_sessions
          WHERE user_id='".mysql_real_escape_string($cuser['user_id'])."'
            AND session_id='".mysql_real_escape_string($cuser['sid'])."'
          LIMIT 1";
$result = mysql_do_query($sql);
if(mysql_num_rows($result) == 1) {
  $result = $session = mysql_fetch_assoc($result);
  $sql = "SELECT `user_id`,`uname`,`displayname`,`editcontent`
						FROM `cms_users` WHERE user_id='".mysql_real_escape_string($result['user_id'])."'";
  $result = mysql_do_query($sql);
  $user = mysql_fetch_assoc($result);

  mysql_do_query("UPDATE cms_sessions
               SET lastview='".time()."'
               WHERE user_id='".mysql_real_escape_string($cuser['user_id'])."'
                 AND session_id='".mysql_real_escape_string($cuser['sid'])."'");
} else {
  $user = Array("editcontent" => 0);
  $session = Array();
}
?>
