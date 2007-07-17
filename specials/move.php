<?

if (isset($_POST['Submit']) && $_POST['Submit'] == "Submit")
{
  $page_id = mysql_real_escape_string($page['page_id']);

  $key = mysql_real_escape_string($_POST['location']);

  $keytest = mysql_do_query("SELECT * FROM `cms_pages` WHERE `page_key`='$key'");
  if (mysql_num_rows($keytest) > 0)
  {
    $content .= "Sorry, that page is in use";
  }
  else
  {
    mysql_do_query("UPDATE `cms_pages` SET `page_key`='$key'
                     WHERE `page_id`='".mysql_real_escape_string($page_id)."'");
    header("location: ".$page['parent_path'].$key);
    die();
  }
}

$c = "<form action=\"{$page['path']}.move\" method=\"POST\">";
$c .= "Page title:<br><input type=\"text\" name=\"location\" size=\"95\" value=\"{$page['page_key']}\"><br>";
$c .= "<input type=\"Submit\" name=\"Submit\" value=\"Submit\"></form>";

$content .= section("Move page...",$c);
?>

