<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: bfDefine.php 827 2007-06-12 18:03:41Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 *
 * I provide global defines for use in files and paths
 */

if (@!defined('_BF_DEFINES_INCLUDED')){
	
	define('_BF_JPATH_SITE',bfCompat::getLiveSite());

	/**
	 * Generic Paths to Blue Flame Framework
	 */
	define('_BF_FRAMEWORK_LIB_DIR',bfCompat::getCfg('absolute_path') . DS . _PLUGIN_DIR_NAME . DS . 'system' . DS . 'blueflame');
	define('_BF_FRAMEWORK_LIB_URL',bfCompat::getCfg('live_site') . '/'._PLUGIN_DIR_NAME.'/system/blueflame');

	define('_BF_FRONT_LIB_DIR', 			_BF_FRAMEWORK_LIB_DIR );
	define('_BF_FRONT_LIB_VIEW_DIR', 		_BF_FRONT_LIB_DIR 		. DS .'view');
	define('_BF_SMARTY_LIB_DIR',	 	_BF_FRAMEWORK_LIB_DIR . DS .'libs' . DS . 'smarty');
	define('_BF_FRAMEWORK_LANGUAGE_DIR',	 	_BF_FRAMEWORK_LIB_DIR . DS .'language');

	/* Make sure we only define things once :-) */
	define('_BF_DEFINES_INCLUDED','1');
}
?>
