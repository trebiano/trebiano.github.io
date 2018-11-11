<?php


/**
* @version 0.1
* @copyright (C) 2005 Walter Cedric
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Free Software
* 
* Based on:
*  Inspiration: 
*/
defined('_VALID_MOS');

//can not Mambo database object (or I have not succeed to...) because this scripts will be use in an HREF= and not in a valid MOS include
include ('../../../../../configuration.php');
require ($mosConfig_absolute_path."/administrator/components/com_securityimages/pluginsA/core/1.1/config.php");

//retry will be stored in sessions
session_name( md5( $mosConfig_live_site ) );
session_start();

//Generate Reference ID if needed
//PHP5 support
if (!isset ($HTTP_GET_VARS)) {
	$HTTP_GET_VARS = & $_GET;
	$HTTP_POST_VARS = & $_POST;
	$HTTP_COOKIE_VARS = & $_COOKIE;
	$HTTP_POST_FILES = & $_FILES;
}
//end PHP5 support

if (isset ($HTTP_GET_VARS["refid"]) && $HTTP_GET_VARS["refid"] != "") {
	$referenceid = stripslashes($HTTP_GET_VARS["refid"]);
} else {
	$referenceid = md5(mktime() * rand());
}

if (isset ($HTTP_GET_VARS["size"]) && $HTTP_GET_VARS["size"] != "") {
	$captchasize = stripslashes($HTTP_GET_VARS["size"]);
} else {
	$captchasize = "L";
}

if (isset ($HTTP_GET_VARS["reload"]) && $HTTP_GET_VARS["reload"] != "") {
	$reload = stripslashes($HTTP_GET_VARS["reload"]);
} else {
	$reload = "0";
}

#temporary variables use across all method
$red = "";
$green = "";
$blue = "";

$fullPathToFont = $mosConfig_absolute_path."/components/com_securityimages/fonts/";

#verify TTF range
/*
$temp = array();
foreach($TTF_RANGE as $k=>$v)
{1 3 
  if(is_readable($TTF_folder.$v)) $temp[] = $v;
}
$TTF_RANGE = $temp;
	*/
if (getGDVersion() >= 2) {
	$functionCreateImage = 'imagecreatetruecolor';
	$functionColorize = 'imagecolorallocate';
} else {
	$functionCreateImage = 'imageCreate';
	$functionColorize = 'imagecolorclosest';
}

function getRandomColor($min, $max) {
	global $red, $green, $blue;
	srand((double) microtime() * 1000000);
	$red = intval(rand($min, $max));
	srand((double) microtime() * 1000000);
	$green = intval(rand($min, $max));
	srand((double) microtime() * 1000000);
	$blue = intval(rand($min, $max));
}

function createGrillAcrossImage($twidth, $theight, $image) {
	global $red, $green, $blue, $functionCreateImage, $functionColorize;

	for ($i = 0; $i < $twidth; $i += $twidth / 2.5) {
		getRandomColor(160, 224);
		$color = call_user_func($functionColorize, $image, $red, $green, $blue);
		@ imageline($image, $i, 0, $i, $theight, $color);
	}
	for ($i = 0; $i < $theight; $i += $theight / 2.8) {
		getRandomColor(160, 224);
		$color = call_user_func($functionColorize, $image, $red, $green, $blue);
		@ imageline($image, 0, $i, $twidth, $i, $color);
	}
}

function outputImage($image) {
	global $securityImagesoutput;

	switch ($securityImagesoutput) {
		case "jpg" :
			header("Content-Type: image/jpeg");
			ImageJPEG($image);
			break;
		case "gif" :
			header("Content-Type: image/gif");
			ImageGIF($image);
			break;
		case "png" :
		default :
			header("Content-Type: image/png");
			ImagePNG($image);
			break;
	}
	imagedestroy($image);
}

function generateRandomTextString() {
	global $textLength, $securityimages_userandomtextlength, $securityimages_randomtextlengthmin, $securityimages_randomtextlengthmax, $securityimages_textUseExtendedCharacterSet;

	$chars = array ("a", "A", "b", "B", "c", "C", "d", "D", "e", "E", "f", "F", "g", "G", "h", "H", "i", "I", "j", "J", "k", "K", "l", "L", "m", "M", "n", "N", "o", "O", "p", "P", "q", "Q", "r", "R", "s", "S", "t", "T", "u", "U", "v", "V", "w", "W", "x", "X", "y", "Y", "z", "Z", "1", "2", "3", "4", "5", "6", "7", "8", "9");

	if ($securityimages_textUseExtendedCharacterSet)
		$chars = array ("a", "A", "b", "B", "c", "C", "d", "D", "e", "E", "f", "F", "g", "G", "h", "H", "i", "I", "j", "J", "k", "K", "l", "L", "m", "M", "n", "N", "o", "O", "p", "P", "q", "Q", "r", "R", "s", "S", "t", "T", "u", "U", "v", "V", "w", "W", "x", "X", "y", "Y", "z", "Z", "1", "2", "3", "4", "5", "6", "7", "8", "9", "+", "*", "%", "&", "/", "(", ")", "=", "?", "!", "$", "£", "@", "#");

	$textstr = "";
	$newtextlength = $textLength;
	if ($securityimages_userandomtextlength)
		$newtextlength = rand($securityimages_randomtextlengthmin, $securityimages_randomtextlengthmax);

	for ($i = 0; $i < $newtextlength; $i ++) {
		$textstr .= $chars[rand(0, count($chars) - 1)];
	}
	return $textstr;
}

function getGDVersion() {
	if (function_exists('imagecreatetruecolor'))
		return 2;
	else
		die('Need  GD PHP extension library'); // no GD installed, or old version
}

function getRandomTrueTypeFont() {
	global $securityimages_fontsarray, $fullPathToFont;
	srand((float) microtime() * 10000000);
	$item = array_rand(explode(',',$securityimages_fontsarray));
	$fontsArray = explode(',',$securityimages_fontsarray);
		
	return $fullPathToFont.$fontsArray[$item];
}

function getTrueTypeFont() {
	global $securityimages_fontsarray, $fullPathToFont;
	$fontsArray = explode(',',$securityimages_fontsarray);
	return $fullPathToFont.$fontsArray[0];
}

function loadRandomBackgroundImage() {
	global $numberOfAvailableImages, $mosConfig_absolute_path, $captchasize, $selectedBigBackground, $selectedSmallBackground;
	$BASE = $mosConfig_absolute_path."/components/com_securityimages/plugins/core/1.1/images/";
	
	$_array = $captchasize == "S" ? $selectedSmallBackground : $selectedBigBackground;
	$bkgArray = explode(",", $_array);
	
	//an array start at 0 and stop at n-1
	$index = rand(0, sizeof($bkgArray)-1);
	$imgname = $BASE.$bkgArray[$index];
	$image = @ imagecreatefrompng($imgname);
	/* Attempt to open */
	if (!$image) { /* See if it failed */
		$image = imagecreate(150, 30); /* Create a blank image */
		$bgc = imagecolorallocate($image, 255, 255, 255);
		$tc = imagecolorallocate($image, 0, 0, 0);
		imagefilledrectangle($image, 0, 0, 150, 30, $bgc);
		/* Output an errmsg */
		imagestring($image, 1, 5, 5, "Error loading $index", $tc);
	}
	return $image;
}

//start of code
$image = loadRandomBackgroundImage();
$textstr = generateRandomTextString();

//Create random size, angle, and dark color
if ($useRandomSize = 1)
	$size = rand($textFontSizeMin, $textFontSizeMax);
else
	$size = $textFontSizeDefualt;

if ($useRandomTextAngle)
	$angle = rand($textAngleMin, $textAngleMax);
else
	$angle = 0;

getRandomColor($securityimages_textRgbMin, $securityimages_textRgbMax);
$color = ImageColorAllocate($image, $red, $green, $blue);

//Determine text size, and use dimensions to generate x & y coordinates
if ($useRandomFont)
	$_fullPathToFont = getRandomTrueTypeFont();
else
	$_fullPathToFont = getTrueTypeFont();

if ($alignementStrategy == "0")
	$alignementStrategy = rand(1, 3);

//Add text to image
switch ($alignementStrategy) {
	case "1" :
		$characterNumber = strlen($textstr);
		//start at 1/third of image width
		$x = imagesx($image) / 3;
		for ($i = 0; $i < $characterNumber; $i ++) {
			$systemFont = rand(4, 5);
			$x = $x +rand(imagefontwidth($systemFont), imagefontwidth($systemFont) * 2);
			$y = rand(1, imagesy($image) - imagefontheight($systemFont));
			imagestring($image, $systemFont, $x, $y, $textstr[$i], $color);
		}
		break;

	case "2" :
		$characterNumber = strlen($textstr);
		//hardcoded I need some fucntion on TTF which return size of fonts...
		$x = 5; //imagesx($image) / 3;
		$imagefontheight = 0.2;
		$imagefontwidth = 15;
		for ($i = 0; $i < $characterNumber; $i ++) {
			$x = $x +rand($imagefontwidth, $imagefontwidth * 2);
			$y = rand(15, 35); //rand(1, imagesy($image)  - $imagefontheight);
			ImageTTFText($image, $size, $angle, $x, $y, $color, $_fullPathToFont, $textstr[$i]);
		}
		break;

	default :
		$textsize = imagettfbbox($size, $angle, $_fullPathToFont, $textstr);
		$twidth = abs($textsize[2] - $textsize[0]);
		$theight = abs($textsize[5] - $textsize[3]);
		$x = (imagesx($image) / 2) - ($twidth / 2) + (rand(-20, 20));
		$y = (imagesy($image)) - ($theight / 2);
		ImageTTFText($image, $size, $angle, $x, $y, $color, $_fullPathToFont, $textstr);
		break;
}

if ($securityimages_usegrille)
	createGrillAcrossImage(230, 35, $image);

if (empty ($_SESSION['securityimages_retry'])) {
	$_SESSION['securityimages_retry'] = 1;
} else {
	$_SESSION['securityimages_retry']++;
}

if ($_SESSION['securityimages_retry'] > $securityImagesMaxRetry) {
	$image = imagecreate(150, 30); /* Create a blank image */
	$bgc = imagecolorallocate($image, 0, 0, 0);
	$tc = imagecolorallocate($image, 255, 255, 255);
	imagefilledrectangle($image, 0, 0, 150, 30, $bgc);
	/* Output an errmsg */
	imagestring($image, 3, 5, 5, "Reload Limit Exceeded", $tc);
	outputImage($image);
	exit;
} else {
	//Insert reference into database, and delete any old ones
	mysql_connect($mosConfig_host, $mosConfig_user, $mosConfig_password) or die(mysql_error());
	mysql_select_db($mosConfig_db);
	mysql_query("INSERT INTO ".$mosConfig_dbprefix."security_images (insertdate, referenceid, hiddentext,retry) VALUES (now(), '".$referenceid."', '".$textstr."', '".$reload."')");
	mysql_query("DELETE FROM ".$mosConfig_dbprefix."security_images WHERE insertdate < date_sub(now(), interval ".$cleanupTable.")");
	outputImage($image);
	exit;
}
?>