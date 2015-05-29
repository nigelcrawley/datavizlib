<?php
// DATA VISUALISATION FUNCTIONS //////////////
// A library by Nigel Crawley 22 March 2012 //

// INTERPOLATION FUNCTIONS /////////////

// NORM
// norm(dataVal, dataMin, dataMax)
function norm($dataVal, $dataMin, $dataMax) {
	$normedVal = ($dataVal-$dataMin)/($dataMax-$dataMin);
	return $normedVal;
}

// LERP
// lerp(dataVal, newMin, newMax)
function lerp($dataNorm, $dataMin, $dataMax) {
	$lerpedVal = ($dataNorm*($dataMax-$dataMin))+$dataMin;
	return $lerpedVal;
}

// MAP
// map(dataVal, dataMin, dataMax, newMin, newMax)
function map($dataVal, $startMin, $startMax, $endMin, $endMax) {
	$mappedVal = ((($dataVal-$startMin)/($startMax-$startMin)) * ($endMax-$endMin)) + $endMin;
	return $mappedVal;
}

// DISTANCE FUNCTION ///////////////
// dist(x1, y1, x2, y2);
function dist($xOne, $yOne, $xTwo, $yTwo) {
	$deltaX = $xTwo - $xOne;
	$deltaY = $yTwo - $yOne;
	$distance = sqrt(($deltaX*$deltaX) + ($deltaY*$deltaY));
	return $distance;
}

// COLOUR STUFF //////////////////

// RGB to HSV
// rgbhsv(red, green, blue);
function rgbhsv($red, $green, $blue) {
	$var_R = $red/255;                     //RGB from 0 to 255
	$var_G = $green/255;
	$var_B = $blue/255;
	$var_Min = min($var_R,$var_G,$var_B);    //Min. value of RGB
	$var_Max = max($var_R,$var_G,$var_B);    //Max. value of RGB
	$del_Max = $var_Max - $var_Min;             //Delta RGB value
	$V = $var_Max;
	if ( $del_Max == 0 ) //This is a gray, no chroma...
	{
		$H = 0; //HSV results from 0 to 1
		$S = 0;
	}
	else //Chromatic data...
	{
		$S = $del_Max/$var_Max;
		$del_R = ((($var_Max - $var_R)/6) + ($del_Max/2))/$del_Max;
		$del_G = ((($var_Max - $var_G)/6) + ($del_Max/2))/$del_Max;
		$del_B = ((($var_Max - $var_B)/6) + ($del_Max/2))/$del_Max;
		if ( $var_R == $var_Max )
			$H = $del_B - $del_G;
		else if ($var_G == $var_Max)
			$H = (1/3) + $del_R - $del_B;
		else if ( $var_B == $var_Max)
			$H = (2/ 3) + $del_G - $del_R;
		if ( $H < 0 ) { $H += 1; }
		if ( $H > 1 ) { $H -= 1; }
	}	
	return "$H,$S,$V"; // normalised
}

// HSV to RGB
// hsvrgb(hue, saturation, briliance);
function hsvrgb($H, $S, $V) {
	if ($S == 0) //HSV from 0 to 1
	{
		$R = $V * 255;
		$G = $V * 255;
		$B = $V * 255;
	}
	else
	{
		$var_h = $H * 6;
		if ($var_h == 6) { $var_h = 0; } //H must be < 1
		$var_i = floor($var_h); //Or ... var_i = int( var_h )
		$var_1 = $V * (1 - $S);
		$var_2 = $V * (1 - $S * ($var_h - $var_i));
		$var_3 = $V * (1 - $S * (1 - ($var_h - $var_i)));
		if ($var_i == 0)
		{
			$var_r = $V;
			$var_g = $var_3;
			$var_b = $var_1;
		}
		else if ($var_i == 1)
		{
			$var_r = $var_2;
			$var_g = $V;
			$var_b = $var_1;
		}
		else if ($var_i == 2)
		{
			$var_r = $var_1;
			$var_g = $V;
			$var_b = $var_3;
		}
		else if ($var_i == 3)
		{
			$var_r = $var_1;
			$var_g = $var_2;
			$var_b = $V;
		}
		else if ($var_i == 4)
		{
			$var_r = $var_3;
			$var_g = $var_1;
			$var_b = $V;
		}
		else
		{
			$var_r = $V;
			$var_g = $var_1;
			$var_b = $var_2;
		}
		$R = $var_r * 255; //RGB results from 0 to 255
		$G = $var_g * 255;
		$B = $var_b * 255;
	}
	return "$R,$G,$B";
}

// RGB to HEX COLOR
function rgbhex($red, $green, $blue) {
	return sprintf('#%02X%02X%02X', $red, $green, $blue);
}

// LERP COLOUR RGB (0) or HSB (1) color spaces
// colors are in hex
// lerpColor(colourOne, colourTwo, normalizedPercentage, hsbOnOff);
// this function requires rgb to hsv and hsv to rgb functions
function lerpColor($color1, $color2, $dataNorm, $isHSB) {
	$color1 = str_replace("#", '', $color1);
	$color2 = str_replace("#", '', $color2);
	$r1 = hexdec(substr($color1, 0, 2));
	$g1 = hexdec(substr($color1, 2, 2));
	$b1 = hexdec(substr($color1, 4, 2));
	$r2 = hexdec(substr($color2, 0, 2));
	$g2 = hexdec(substr($color2, 2, 2));
	$b2 = hexdec(substr($color2, 4, 2));
	$r3 = 0; //set function wide result variables
	$g3 = 0;
	$b3 = 0;
	if ($isHSB)
	{
		$hsv1 = explode(",", rgbhsv($r1, $g1, $b1));
		$hsv2 = explode(",", rgbhsv($r2, $g2, $b2));
		$h3 = ($dataNorm*($hsv2[0]-$hsv1[0]))+$hsv1[0];
		$s3 = ($dataNorm*($hsv2[1]-$hsv1[1]))+$hsv1[1];
		$v3 = ($dataNorm*($hsv2[2]-$hsv1[2]))+$hsv1[2];
		$rgb3 = explode(",", hsvrgb($h3,$s3,$v3));
		$r3 = $rgb3[0];
		$g3 = $rgb3[1];
		$b3 = $rgb3[2];
	}
	else
	{
		$r3 = ($dataNorm*($r2-$r1))+$r1;
		$g3 = ($dataNorm*($g2-$g1))+$g1;
		$b3 = ($dataNorm*($b2-$b1))+$b1;
	}
	return sprintf('#%02X%02X%02X', $r3, $g3, $b3);
	//return "rgb(".floor($r3).",".floor($g3).",".floor($b3).")";
	//return $color = str_replace("#", '', $color);
}

// XML STUFF ///////////////////////////

// SIMPLE XML PARSER
// gets contents of a single unique xml node
// simpleML(xmlToSearch, nodeName);
function simpleML($inStr, $nodeName) {
	preg_match('/'.$nodeName.'.*\>(.*)\<\/'.$nodeName.'/', $inStr, $matches);
	return $matches[1];
}

?>
