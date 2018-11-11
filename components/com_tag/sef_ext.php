<?php
/**
 * @version $Id: sef_ext.php 827 2007-06-12 18:03:41Z phil $
 * @package bfFramework
 * @subpackage Joomla Tags
 * @copyright Copyright (C) 2007 Blue Flame IT Ltd. All rights reserved.
 * @license see LICENSE.php
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 */

defined( '_VALID_MOS' ) or die( 'Restricted access' );

class sef_tag {
	
	function create ($string) {

		$sefstring = "";

		if (eregi("&amp;tag=",$string)) {
			$temp = split("&amp;tag=", $string);
			$temp[1] = $temp[1];
			$temp = split("&amp;", @$temp[1]);
			$temp = split("&tag_id=", $temp[0]);
			$sefstring .= sefencode($temp[0])."/";
		}

		if (eregi("&amp;limit='",$string)) {
			//
			// limit= javascript case
			//
			$temp = split("&amp;limit='", $string);
			@$temp = split("'", @$temp[1]);
			$sefstring .= "'".@$temp[0]."'/";
		} else if (eregi("&amp;limit=",$string)) {
			//
			// No javascript case
			//
			$temp = split("&amp;limit=", $string);
			@$temp = split("&", @$temp[1]);
			$sefstring .= @sefencode($temp[0])."/";
		}

		if (eregi("&amp;limitstart=",$string)) {
			$temp = split("&amp;limitstart=", $string);
			@$temp = split("&", @$temp[1]);
			$sefstring .= @sefencode($temp[0])."/";
		}

		//		if (isset($GLOBASL['debug']) && ($GLOBALS['debug'] == 10 )) {
//					print "sef_ext.php In create input: $string output: $sefstring <br/>";
		//		}

		return $sefstring;
	}

	function revert ($url_array, $pos) {
		// define all variables you pass as globals
		// Examine the SEF advance URL and extract the variables building the query string
		//
		// $pos + 2 is the tagname
		// $pos + 3 is the limit
		// $pos + 4 is the limitstart
		//
		$QUERY_STRING = "";
		if (isset($url_array[$pos+2])) {
			if ($url_array[$pos+2]=='cloud'){
				$_GET['page'] = $url_array[$pos+2];
				$_REQUEST['page'] = $url_array[$pos+2];
				$QUERY_STRING .= "&page=".$url_array[$pos+2];
			} else {

				$t = sefdecode($url_array[$pos+2]);
				$_GET['tag'] = $t;
				$_REQUEST['tag'] = $t;
				$QUERY_STRING .= "&tag=".$t;
			}
		}
		if (isset($url_array[$pos+3]) && $url_array[$pos+3] > 0) {
			$t = sefdecode($url_array[$pos+3]);
			$_GET['limit'] = $t;
			$_REQUEST['limit'] = $t;
			$QUERY_STRING .= "&limit=".$t;
		}
		if (isset($url_array[$pos+4])) {
			$t = sefdecode($url_array[$pos+4]);
			$_GET['limitstart'] = $t;
			$_REQUEST['limitstart'] = $t;
			$QUERY_STRING .= "&limitstart=".$t;
		}

		//echo "sef_ext.php In revert $QUERY_STRING <br/>";

		return $QUERY_STRING;
	}
}
?>
