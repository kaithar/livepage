<?php 

$admining = 1;
if ($user['editcontent'] != 1)
{
	die("Not allowed");
}

$content .= '<script type="text/javascript" src="/js/jquery-1.3.1.min.js"></script>';
$content .= '<script type="text/javascript" src="/js/admin.js"></script>';

if (isset($vfile[1]))
{
	switch ($vfile[1])
	{
		case "structure":
			require_once("specials/structure.php");
			die($content);
		case "config":
			require_once("specials/config.php");
			die($content);
		case "logo":
			require_once("specials/logo.php");
			die($content);
		case "template":
			require_once("specials/template_config.php");
			die($content);
	}
}
else
{
	$content .= '<a href="javascript:viewPage(\'config\')">Site Config</a> '.
	 			'| <a href="javascript:viewPage(\'structure\')">Site structure</a> '.
	 			'| <a href="javascript:viewPage(\'logo\')">Logo</a> '.
				'| <a href="javascript:viewPage(\'template\')">Template Config</a><br/><br/>';
	$content .= '<div id="adminbody">';
	require_once("specials/structure.php");
	$content .= '</div>';
}
?>