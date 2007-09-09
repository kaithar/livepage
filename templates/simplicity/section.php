<?php
 function section($title, $content, $template_data = "")
{
  $title_css = "";

  if ($template_data != "")
  {
    $s = explode(";", $template_data);
    foreach ($s as $ss)
    {
      $ss = explode(":",$ss);
      switch ($ss[0])
      {
        case "title_bg":
          $title_css .= "background: ".$ss[1].";";
          break;
      }
    }
  }

  return "
     <div class=\"section\">
      <div class=\"title\" style=\"$title_css\">$title</div>
      <div class=\"content\">
        $content
      </div>
     </div>";
}
?>
