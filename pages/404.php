<?php

$page['page_title'] = "Oops, 404";

$foo = "The requested page or one of it's parent categories don't seem to exist.<br/><br/>";
$foo .= "If you followed a link to get here, please inform the owner of the originating page that their link is broken.<br/><br/>";
$foo .= "Perhaps you can find the desired page using the site navigation?";

if ($user['editcontent'] == 1) 
{
  $foo .= "<br/><br/>Click <a href=\"{$page['parent_path']}/{$page['page_key']}.create\">here</a> to create this page and the required categories.";
}

$content .= section("Ooops!",$foo);

?>
