<?

if ($page['found'])
{
  if (isset($_POST['Submit']))
  {
    $page_id = mysql_real_escape_string($page['page_id']);
    
    $title = mysql_real_escape_string($_POST['title']);
    $content = mysql_real_escape_string($_POST['content']);

    $order = mysql_do_query("SELECT count(*) as `c` FROM `cms_sections` WHERE `page_id` = '$page_id'");
    $order = mysql_fetch_assoc($order);
    $order = mysql_real_escape_string($order['c']);

    mysql_do_query("INSERT INTO `cms_sections`
                            SET `page_id`='$page_id', 
                                `order`='$order', 
                                `section_title`='$title',
                                `section_text`='$content'");
    header("location: ".$page['parent_path'].$page['page_key']);
    die();
  }
	
  $c = "<form action=\"{$page['parent_path']}{$page['page_key']}.createsection\" method=\"POST\">";
  $c .= "Section title:<br><input type=\"text\" name=\"title\" size=\"95\"><br><br>";
  $c .= "Content:<br><textarea name=\"content\" cols=\"80\" rows=\"10\"></textarea><br>";
  $c .= "<input type=\"Submit\" name=\"Submit\" value=\"Submit\"></form>";
  $content .= section("Create section...",$c);
}
?>
