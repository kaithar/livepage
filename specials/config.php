<?php

if (isset($_POST['general']) && $_POST['general'] == "Submit")
{
  $site_name = mysql_real_escape_string($_POST['site_name']);
	
  mysql_do_query("UPDATE `cms_config` 
                     SET `site_name`='$site_name'");
  header("location: ".$page['path'].".config");
  die();
}

if (isset($_POST['logo']) && $_POST['logo'] == "Upload")
{
  if (strpos($_FILES['userfile']['type'], "image/") === false) { die("Upload unsuitable"); }
	$type = @exif_imagetype($_FILES['userfile']['tmp_name']);
	if (($type != IMAGETYPE_GIF)&&($type != IMAGETYPE_JPEG)&&($type != IMAGETYPE_PNG)) { die("Upload really unsuitable"); }
  if (!is_uploaded_file($_FILES['userfile']['tmp_name'])) { die("Not uploaded"); }
  if ($_FILES['userfile']['size'] > 102400) { die("Image too big"); }
	
	$newFile = "files/".$config['domain']."/images/";
	if (!file_exists($newFile))
		if (mkdir($newFile, 0755, true)) { die("Not permissive enough"); }
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
                     SET `logo`='$newLogo'");
  header("location: ".$page['path'].".config");
  die();
}

$c = "<form action=\"{$page['path']}.config\" method=\"POST\">";
$c .= "Site name: <input type=\"text\" name=\"site_name\" size=\"95\" value=\"{$site_config['site_name']}\"><br/><br/>";
$c .= "<input type=\"Submit\" name=\"general\" value=\"Submit\"></form>";

$content .= section("General Config",$c);

$c = "<form enctype=\"multipart/form-data\" action=\"{$page['path']}.config\" method=\"POST\">";
$c .= "<!-- MAX_FILE_SIZE must precede the file input field -->";
$c .= "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"102400\">";
$c .= "<!-- Name of input element determines name in $_FILES array -->";
$c .= "Upload logo:<br/><br/>";
$c .= "<input name=\"userfile\" type=\"file\" size=50/><br/>";
$c .= "<input type=\"submit\" name=\"logo\" value=\"Upload\"/>";
$c .= "</form>";

$content .= section("Upload Logo",$c);

?>