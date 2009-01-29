<?php 

if (isset($_POST['logo']) && $_POST['logo'] == "Upload")
{
  if (strpos($_FILES['userfile']['type'], "image/") === false) { die('<script type="text/javascript">alert("Upload unsuitable");</script>'); }
	$type = @exif_imagetype($_FILES['userfile']['tmp_name']);
	if (($type != IMAGETYPE_GIF)&&($type != IMAGETYPE_JPEG)&&($type != IMAGETYPE_PNG)) { die('<script type="text/javascript">alert("Upload really unsuitable");</script>'); }
  if (!is_uploaded_file($_FILES['userfile']['tmp_name'])) { die('<script type="text/javascript">alert("Not uploaded");</script>'); }
  if ($_FILES['userfile']['size'] > 102400) { die('<script type="text/javascript">alert("Image too big");</script>'); }
	
	$newFile = "files/".$config['domain']."/images/";
	if (!file_exists($newFile))
		if (mkdir($newFile, 0755, true)) { die('<script type="text/javascript">alert("Not permissive enough");</script>'); }
  $newFile .= urlencode($_FILES['userfile']['name']);
	
  move_uploaded_file($_FILES['userfile']['tmp_name'], $newFile);
	
	$newLogo = "/files/images/".urlencode($_FILES['userfile']['name']);
	
	if (($site_config['logo'] != "/images/logo.png")&&($site_config['logo'] != $newLogo))
	{
		$tempLogo = str_replace("/files","files/".$config['domain'],$site_config['logo']);
		$type = @exif_imagetype($tempLogo);
		if (($type == IMAGETYPE_GIF)||($type == IMAGETYPE_JPEG)||($type == IMAGETYPE_PNG))
			unlink($tempLogo);
	}
	
  $newLogo = mysql_real_escape_string($newLogo);
	
  mysql_do_query("UPDATE `cms_config` 
                     SET `logo`='".$newLogo."'");
  die('<script type="text/javascript">var i = window.top.document.getElementById("logo"); i.src = "'.$newLogo.'";</script>');
}

$c = '<form enctype="multipart/form-data" action="/lp-admin.logo" method="POST" target="upload_frame">';
$c .= "<!-- MAX_FILE_SIZE must precede the file input field -->";
$c .= "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"102400\">";
$c .= "<!-- Name of input element determines name in $_FILES array -->";
$c .= "Upload logo: <input name=\"userfile\" type=\"file\" size=50/><br/><br/>";
$c .= "<input type=\"submit\" name=\"logo\" value=\"Upload\"/>";
$c .= "</form><br/>";
$c .= '<iframe id="upload_frame" name="upload_frame" src="about:blank" style="width:0px; height:0px; border: 0px;"/>';

$content .= section("Upload Logo",$c);
?>