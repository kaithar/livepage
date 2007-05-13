<?php
$foo = "The requested page doesn't seem to exist.<br/><br/>";
$foo .= "If you followed a link to get here, please inform the owner of the originating page that their link is broken.<br/><br/>";
$foo .= "Perhaps you can find the desired page using the site navigation?";

if ($user['editcontent'] == 1) 
{
  $foo .= "<br/><br/>Click <a href=\"$parent_path/$request.create\">here</a> to create this page.";
}

$content .= section("Ooops!",$foo);

?>
