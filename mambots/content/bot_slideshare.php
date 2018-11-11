<?php

/**
 * slideshare 1.0
 * License : http://www.gnu.org/copyleft/gpl.html
 * @by ot2sen
 * @Copyright (C) 2007 http://www.ot2sen.dk
 * Based on:
 * YouTube Video 0.1
 * License : http://www.gnu.org/copyleft/gpl.html
 * @by SmashD
 * @Copyright (C) 2006 http://www.smashd.de
 */
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

$_MAMBOTS->registerFunction( 'onPrepareContent', 'botslideshare' );

function botslideshare( $published, &$row, &$params, $page=0 ) {
	global $mosConfig_absolute_path;

	$regex = "#{slideshare}(.*?){/slideshare}#s";

	if ( !$published ) {
		$row->text = preg_replace( $regex, '', $row->text );
		return;
	}

	$row->text = preg_replace_callback( $regex, 'botslideshare_replacer', $row->text );
	return true;
}


/**
* @param array An array of matches
* @return string
*/
function botslideshare_replacer ( &$matches ) {
	
$slideshare = $matches[1];	

$res = '
<object width="425" height="350"><param name="movie" value="https://s3.amazonaws.com:443/slideshare/ssplayer.swf?'. $slideshare.'"></param><embed src="https://s3.amazonaws.com:443/slideshare/ssplayer.swf?'. $slideshare.'" type="application/x-shockwave-flash" width="425" height="350"></embed></object>
';
	return $res;
}

?>
