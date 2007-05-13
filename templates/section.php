<?php
 function section($title, $content)
{
  return "
     <div class=\"section\">
      <div class=\"title\">$title</div>
      <div class=\"content\">
        $content
      </div>
     </div>";
}
?>
