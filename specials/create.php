<?php

if ($page['found'] == false)
{
	for ($path as $cat)
	{
		if ($cat['found'])
			print "Already have {$cat['key']}<br/>";
		else
			print "Need to create {$cat['key']}<br/>";
	}
	print "Need to create {$page['key']}<br/>";
	die();
}

print "Already have {$page['key']}<br/>";

die();

if (mysql_num_rows($mypage) == 0)
{
  // Okie, this page definitely doesn't exist, so create it!
  mysql_do_query("INSERT INTO `cms_pages`
                          SET `page_key` = '".mysql_real_escape_string($request)."',
                              `page_parent` = '".mysql_real_escape_string($parent)."',
                              `page_title` = 'Under Construction'");
  header("location: ".$request);
  die();  
}
$showpage = 1;
?>
