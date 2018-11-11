<?php
defined( '_VALID_MOS' ) or die( 'Restricted access' );
/**
 * @version $Id: bfCompat.php 827 2007-06-12 18:03:41Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 */

if (!defined('_BF_FILEINCLUDED_BFCOMPAT')) define('_BF_FILEINCLUDED_BFCOMPAT', true);

if (defined( '_VALID_MOS' ) OR defined( '_JEXEC' )){
	/* ok we are in Joomla 1.0.x or Joomla 1.5+ */
	if (!defined('_VALID_MOS'))	{
		/* We are in Joomla 1.5 */
		define('_VALID_MOS', '1');
		define('_PLUGIN_DIR_NAME','plugins');
		define('_BF_PLATFORM','JOOMLA1.5');
	} else if (!defined('_JEXEC')){
		/* we are in Joomla 1.0 */
		define('_JEXEC', '1');
		define('_PLUGIN_DIR_NAME','mambots');
		define('_BF_PLATFORM','JOOMLA1.0');
		define('JPATH_ROOT', $GLOBALS['mosConfig_absolute_path']);
		if (!defined('DS')) 		define('DS', DIRECTORY_SEPARATOR);
	}
} else {
	header('HTTP/1.1 403 Forbidden');
	die('Direct Access Not Allowed');
}

if (!defined('DS')) 		define('DS', DIRECTORY_SEPARATOR);
if (!defined('JPATH_SITE')) define('JPATH_SITE', $GLOBALS['mosConfig_live_site']);
if (!defined('_BF_JPATH_BASE')) define('_BF_JPATH_BASE', $GLOBALS['mosConfig_absolute_path']);


class bfCompat {

	function getdbPrefix(){
		if (_BF_PLATFORM=='JOOMLA1.0'){
			return $GLOBALS['mosConfig_dbprefix'];
		} else {
			global $mainframe;
			return $mainframe->getCfg('dbprefix');
		}
	}

	function getPluginDir(){
		global $mainframe;
		$root = $mainframe->getCfg('absolute_path');
		if (file_exists($root .DS .'mambots')){
			return 'mambots';
		} else {
			return 'plugins';
		}
	}

	function getSiteName(){
		if (_BF_PLATFORM=='JOOMLA1.0'){
			return $GLOBALS['mosConfig_sitename'];
		} else {
			global $mainframe;
			return $mainframe->getCfg('sitename');
		}
	}

	function now(){
		return date( 'Y-m-d H:i', time() );
	}

	function setPageTitle($str){
		global $mainframe;
		$mainframe->setPageTitle($str);
	}

	function mosConfig_live_site(){
		return bfCompat::getLiveSite();
	}

	function getLiveSite(){
		if (_BF_PLATFORM=='JOOMLA1.5'){
			$str =JURI::base();
			return str_replace('/administrator/','',$str);
		} else {
			global $mosConfig_live_site;
			return $mosConfig_live_site;
		}
	}

	function getAbsolutePath(){
		global $mainframe;
		return $mainframe->getCfg('absolute_path');
	}
	/**
	 * Joomla 1.0 = mambots
	 * Joomla 1.5 = plugins
	 *
	 * @return unknown
	 */
	function mambotsfoldername(){
		return _PLUGIN_DIR_NAME;
	}

	function isAdmin(){
		global $mainframe;
		return $mainframe->isAdmin();
	}

	function getCfg($key){
		global $mainframe;
		return $mainframe->getCfg($key);
	}

	function redirect($location,$message){
		global $mainframe;
		$mainframe->redirect($location,$message);
	}

	function getUser($client = ''){
		global $mainframe;
		if (bfCompat::isAdmin() OR $client=='admin'){
			global $my;
		} else {
			$my = $mainframe->getUser();
		}
		return $my;
	}

	function getMainFrame($database, $option, $task, $client='front'){
		global $mainframe;
		return $mainframe;
	}

	function getLangDir(){
		return 'ltr'; // or 'rtr'
	}

	function getLangSite(){
		global $mainframe;
		if (_BF_PLATFORM=='JOOMLA1.5'){
			$mainframe->getCfg('lang_site');
		} else {
			$mainframe->getCfg('lang_site');
		}
	}

	function &getDBO(){
		if (_BF_PLATFORM=='JOOMLA1.5'){
			$db =& JFactory::getDBO();
			return $db;
		} else {
			$db =& bfDatabase::getInstance('mysql',$GLOBALS['mosConfig_host'], $GLOBALS['mosConfig_user'], $GLOBALS['mosConfig_password'],$GLOBALS['mosConfig_db'],$GLOBALS['mosConfig_dbprefix']);
			return $db;
		}
	}

	function findOption(){
		global $mainframe;
		return $mainframe->get('component');
	}

	function findItemid(){
		if (_BF_PLATFORM!='JOOMLA1.5'){
			$Itemid = 1;
		}

		return $Itemid;
	}

	function sefRelToAbs($str){

		if (_BF_PLATFORM=='JOOMLA1.5'){
			$router =& JRouter::getInstance();
			return $router->build($str);
		} else {
			/* function doesnt exist in admin!!! */
			if (function_exists('sefRelToAbs')){
				$str = sefRelToAbs($str);
			}
			return $str;
		}
	}

	function setMeta($type, $data){
		global $mainframe;
		$mainframe->addMetaTag($type,$data);
	}

	function getItemID($id){

		bfLoad('bfCache');
		$cache = bfCache::getInstance();

		$key = 'ITEMID_FOR_'.$id;

		$cached = $cache->get(md5($key),'sql');
		if ($cached){
			return $cached;
		} else {
			global $mainframe;
			$itemid = $mainframe->getItemid($id);
			$cache->api_add(md5($key),$itemid,'sql');
			$cache->save();
			return $itemid;
		}
	}

	function getComponentItemID(){
		global $mainframe;
		$registry =& bfRegistry::getInstance($mainframe->get('component'));
		return $registry->getValue('config.itemid','1');
	}
}
?>