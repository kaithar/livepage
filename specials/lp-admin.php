<?php 

$admining = 1;

$content .= '<script type="text/javascript" src="/js/admin.js"></script>';

switch ($vfile[1])
{
	case "structure":
		require_once("specials/structure.php");
		die($content);

	default:
		$content .= '<a href="javascript:viewStructure()">Site structure</a><br/><br/>';
		$content .= '<div id="adminbody">';
		require_once("specials/structure.php");
		$content .= '</div>';
		break;
}
?>