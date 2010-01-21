<?php
	$color = $_GET['color'];
	if (!$color) {$color = "ff776b";} //default google map color
	$color = str_replace("#", "", $color);
	$string = $_GET['text'];
	
	$font = realpath('arial.ttf');

	//unfortunately we still must do some offsetting
	switch (ord(substr($string,0,1))) {
		case 49: //1
			$offset = -2;
			break;
		case 55: //7
			$offset = -1;
			break;
		case 65: //A
			$offset = 1;
			break;
		case 74: //J
			$offset = -1;
			break;
		case 84: //T
			$offset = 1;
			break;
		case 99: //c
			$offset = -1;
			break;
		case 106: //j
			$offset = 1;
			break;
	}
	if (strlen($string) == 1) {
		$fontsize = 10.5;
	} else if (strlen($string) == 2) {
		$fontsize = 9;
	} else {
		$fontsize = 10.5;
		$offset = 0; //reset offset
		$string = chr(149);
	}
	
	$bbox = imagettfbbox($fontsize, 0, $font, $string);
	$width = $bbox[2] - $bbox[0] + 1;
	$height = $bbox[1] - $bbox[7] + 1;

	$image_name = "http://chart.apis.google.com/chart?cht=mm&chs=20x34&chco=$color,$color,000000&ext=.png";
	
	$ch = curl_init();
	curl_setopt ($ch, CURLOPT_URL, $image_name);
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 0);
	
	// Getting binary data
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
	
	$image_string = curl_exec($ch);
	curl_close($ch);
	
	$im = imagecreatefromstring ($image_string);
	imageAlphaBlending($im, true);
	imageSaveAlpha($im, true);
	$black = imagecolorallocate($im, 0, 0, 0);

	
	imagettftext($im, $fontsize, 0, 11 - $width/2 + $offset, 9 + $height/2, $black, $font, $string);

	header("Content-type: image/png");
	imagepng($im);
	imagedestroy($im);
?>