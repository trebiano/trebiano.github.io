<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: bfImg.php 827 2007-06-12 18:03:41Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 */

/**
 * I provide a lot of image functions
 *
 */
class bfImg {

	/**
	 * build url to icon
	 *
	 * @param unknown_type $name
	 * @return unknown
	 */
	function buildURL($name, $ext='png', $framework=1){
		global $mainframe;
		if ($framework===1){
			return bfCompat::getLiveSite().'/'._PLUGIN_DIR_NAME.'/system/blueflame/view/images/'.$name.'.'.$ext;
		} else {
			return bfCompat::getLiveSite().'/components/'.$mainframe->get('component').'/view/images/'.$name.'.'.$ext;
		}
	}

	function url_bullet_star(){
		return bfImg::buildURL('bullet_star','gif');
	}

	function url_bullet_error(){
		return bfImg::buildURL('icon_error','png');
	}
}