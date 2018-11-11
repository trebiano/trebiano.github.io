<?php
//define( '_JEXEC' , '1');
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: bfRedirect.php 827 2007-06-12 18:03:41Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 */
/**
 *
 * joomla1.5 type redirect
 */
/**
* Utility function redirect the browser location to another url
*
* Can optionally provide a message.
* @param string The file system path
* @param string A filter for the names
*/
function bfRedirect( $url, $msg='' ) {

    // specific filters
	$iFilter = new InputFilter();
	$url = $iFilter->process( $url );
	if (!empty($msg)) {
		$msg = $iFilter->process( $msg );
	}

	if ($iFilter->badAttributeValue( array( 'href', $url ))) {
		$url = $GLOBALS['mosConfig_live_site'];
	}

	if (trim( $msg )) {
	 	if (strpos( $url, '?' )) {
			$url .= '&mosmsg=' . urlencode( $msg );
		} else {
			$url .= '?mosmsg=' . urlencode( $msg );
		}
	}

	if (headers_sent()) {
		echo "<script>document.location.href='$url';</script>\n";
	} else {
		@ob_end_clean(); // clear output buffer
		header( 'HTTP/1.1 301 Moved Permanently' );
		header( "Location: ". $url );
	}
	exit();
}
?>