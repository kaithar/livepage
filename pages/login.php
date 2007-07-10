<?php

if (isset($_POST['Login']))
{
  $username = $_POST['uname'];
  $pass = $_POST['pass'];
  $errors = Array();
  if (!$username) { $errors[] = "Missing username"; }
  if (!$pass) { $errors[] = "Missing password"; }

  if (count($errors) == 0)
  {
    $sql = "SELECT *
              FROM cms_users
              WHERE `uname` = '".mysql_real_escape_string($username)."'
                AND `password` = '".mysql_real_escape_string(md5($pass))."'
                AND `fails` <=3
                AND `lastfail` <= ".(time()+(15*60))."
              LIMIT 1";

    $result = mysql_do_query($sql);
    if (mysql_num_rows($result) != 1)
    {
      $errors[] = "Sorry, bad username/password";
      mysql_do_query("UPDATE cms_users
                        SET `lastfail`='".mysql_real_escape_string(time())."',
                          `fails`=fails+1
                        WHERE
                          `uname`='".mysql_real_escape_string($username)."'");
    }
    else
    {
      $user = mysql_fetch_assoc($result);
      mysql_do_query("UPDATE cms_users
                        SET `lastfail`='0', `fails`='0' 
                        WHERE `user_id`= '".mysql_real_escape_string($user['user_id'])."'");
      $sid = md5(uniqid(rand(), true));
      $sql = "INSERT INTO cms_sessions (`user_id`,`session_id`,`lastview`)
                VALUES ('".mysql_real_escape_string($user['user_id'])."',
                        '".mysql_real_escape_string($sid)."',
                        UNIX_TIMESTAMP())";
      mysql_do_query($sql);
      setcookie ("cuser[sid]", $sid , time()+(60*60*24*365*10),"/",$_SERVER["HTTP_HOST"],0);
      setcookie ("cuser[user_id]", $user['user_id'], time()+(60*60*24*365*10),"/",$_SERVER["HTTP_HOST"],0);
      dbclose();
      header("location: /");
      die();
    }
  }
}

$content .= section("Login...",'<form method="POST" action="/login">
					<table border="0" cellpadding="3">
						<tr><td>Username:</td><td><input type="text" name="uname"/></td></tr>
						<tr><td>Password:</td><td><input type="password" name="pass"/></td></tr>
						<tr><td colspan=2><input type="submit" name="Login" value="Login"/></td></tr>
					</table></form>');
?>
