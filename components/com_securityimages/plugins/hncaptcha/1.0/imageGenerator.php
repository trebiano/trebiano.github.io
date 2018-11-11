<?php
/************************************************************\
*
*		hncatcha plugin for securityimages
*       Copyright 2006 Walter Cedric
*		www.waltercedric.com
*
*    This file is part of securityimages.
*	
*	this plugin use the GPL engine HN Captcha php class (see http://www.phpclasses.org/browse/package/1569.html).
*	 
*    securityimages is free software; you can redistribute it and/or modify
*    it under the terms of the GNU General Public License as published by
*    the Free Software Foundation; either version 2 of the License, or
*    (at your option) any later version.
*
*    securityimages is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
*
*    You should have received a copy of the GNU General Public License
*    along with freeCap; if not, write to the Free Software
*    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*
*
\************************************************************/
defined('_VALID_MOS');
 
//insert config just because of $mosConfig_absolute_path
include ('../../../../../configuration.php'); 
require_once ($mosConfig_absolute_path.'/components/com_securityimages/plugins/hncaptcha/1.0/hn_captcha.class.php');
require ($mosConfig_absolute_path."/administrator/components/com_securityimages/pluginsA/hncaptcha/1.0/config.php");
$fullPathToFont = $mosConfig_absolute_path."/components/com_securityimages/fonts/";								   

session_name( md5( $mosConfig_live_site ) );
session_start();

//from config.php
$CAPTCHA_INIT = array (
'tempfolder' => '', 
'TTF_folder' => $fullPathToFont, 
'TTF_RANGE' => explode(',',$TTF_RANGE), 
'chars' =>$chars, 
'minsize'=>$minsize, 
'maxsize'=>$maxsize, 
'maxrotation'=>$maxrotation, 
'noise'=>$noise, 
'websafecolors'=>$websafecolors, 
'refreshlink'=>$refreshlink, 
'lang'=>$lang, 
'maxtry'=>$maxtry, 
'badguys_url'=>$badguys_url, 
'secretstring,'=>$secretstring, 
'secretposition'=>$secretposition, 
'debug'=>$debug,
'site_tags0' => $site_tags[0],
'site_tags1' => $site_tags[1],
'tag_pos' => $tag_pos,
'watermarkAntiFreePornAttack' => $watermarkAntiFreePornAttack,
'cw_defaultRGBRedBackgroungColor'=>$cw_defaultRGBRedBackgroungColor,
'cw_defaultRGBGreenBackgroungColor'=>$cw_defaultRGBGreenBackgroungColor,
'cw_defaultRGBBlueBackgroungColor'=>$cw_defaultRGBBlueBackgroungColor,
'cw_useRandomBackgroungColor'=>$cw_useRandomBackgroungColor,
'cw_minRGBBackgroungColor'=>$cw_minRGBBackgroungColor,
'cw_maxRGBBackgroungColor'=>$cw_maxRGBBackgroungColor
);

$captcha = & new hn_captcha($CAPTCHA_INIT);
$captcha->make_captcha();

//Ive add accessor on private properties, it is bad bu HNCaptcha is too monolithic:
// it is mixing model, view and controller in one class
$_SESSION['publicKey'] =  $captcha->getPublicKey();
$_SESSION['hnkey'] =  $captcha->getKey();
$_SESSION['privateKey'] =  $captcha->getPrivateKey();
?>