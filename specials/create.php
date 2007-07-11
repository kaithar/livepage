<?php

if ($page['found'] == false)
{
	$parent_id = 0;
  foreach ($path as $cat)
  {
    if ($cat['found'] == false)
		{
			$key = mysql_real_escape_string($cat['cat_key']);
      mysql_do_query("INSERT INTO `cms_categories`
                              SET `cat_parent` = '".mysql_real_escape_string($parent_id)."',
                                  `cat_key` = '$key',
                                  `cat_title` = '$key'");
			// Note: this relies on there being no other queries after the insert.
			// It'll probably break if there is.
			$parent_id = mysql_insert_id();
		}
		else
		{
			$parent_id = $cat['cat_id'];
		}
  }
  mysql_do_query("INSERT INTO `cms_pages`
                          SET `page_key` = '".mysql_real_escape_string($page['page_key'])."',
                              `page_parent` = '".mysql_real_escape_string($parent_id)."',
                              `page_title` = 'Under Construction'");
  header("location: ".$request);
	die();
}

$showpage = 1;
?>
