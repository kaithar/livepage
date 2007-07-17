<?php

$path = explode("/", $requested);

array_shift(&$path);

if ($path[0] != "files")
	die("Something went horribly wrong");
	
array_shift(&$path);

switch ($path[0])
{
	case "images":
		array_shift(&$path);
		$img = "files/".$config['domain']."/images/".$path[0];
		if (!file_exists($img))
			die("Requested file doesn't seem to exist!");
		$type = @exif_imagetype($img);
		switch ($type)
		{
			case IMAGETYPE_GIF:
			case IMAGETYPE_JPEG:
			case IMAGETYPE_PNG:
				header("Content-type: ".image_type_to_mime_type($type));
	    	readfile($img);
				die();
			default:
				die("Unknown or unsupported type");
		}
	default:
		die("Unknown request");
}

die();
?>