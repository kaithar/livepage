<?

require_once('includes/cat_tree.php');

$tree = build_cat_tree();

if (isset($_POST['Submit']) && $_POST['Submit'] == "Submit" && isset($tree['ids'][$_POST['category']]))
{
  $page_id = mysql_real_escape_string($page['page_id']);

  $key = mysql_real_escape_string($_POST['location']);
	
	$cat = mysql_real_escape_string($_POST['category']);

  $keytest = mysql_do_query("SELECT * FROM `cms_pages` WHERE `page_key`='$key' AND `page_category`='$cat'");
  if (mysql_num_rows($keytest) > 0)
  {
    $content .= "Sorry, that page is in use";
  }
  else
  {
    mysql_do_query("UPDATE `cms_pages` SET `page_key`='$key', `page_category`='$cat'
                     WHERE `page_id`='".mysql_real_escape_string($page_id)."'");
    header("location: ".$tree['ids'][$cat]['path']."/".$key);
    die();
  }
}

$c = "<form action=\"{$page['path']}.move\" method=\"POST\">";
$c .= "Move to:<br/><select name=\"category\">";
foreach ($tree['flat'] as $tcat)
{
	$c .= "<option value=\"{$tcat['cat_id']}\"".(($tcat['cat_id'] == $page['page_category'])?" selected=\"selected\"":"").">";
	$c .= $tcat['flat_path']."</option>";
}
$c .= "</select> &raquo; ";
$c .= "<input type=\"text\" name=\"location\" size=\"95\" value=\"{$page['page_key']}\"><br>";
$c .= "<input type=\"Submit\" name=\"Submit\" value=\"Submit\"></form>";

$content .= section("Move page...",$c);
?>

