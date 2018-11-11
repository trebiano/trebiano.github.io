<?php
/**
 * @version $Id: mod_tag_cloud.php 828 2007-06-13 09:09:16Z phil $
 * @package #PACKAGE#
 * @subpackage #SUBPACKAGE#
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license see LICENSE.php
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 */

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
	}
} else {
	header('HTTP/1.1 403 Forbidden');
	die('Direct Access Not Allowed');
}

global $mainframe;
/* define our components names */
$mainframe->set('component', 'com_tag');
$mainframe->set('component_shortname', 'tag');


require_once(JPATH_ROOT . DS .  _PLUGIN_DIR_NAME . DS . 'system' . DS . 'blueflame' . DS . 'bfCompat.php');
require_once(JPATH_ROOT . DS .  _PLUGIN_DIR_NAME . DS . 'system' . DS . 'blueflame' . DS . 'bfFramework.php');

bfDocument::addCSS(bfCompat::getLiveSite() . '/'._PLUGIN_DIR_NAME.'/system/blueflame/bfCombine.php?type=css&c='.$mainframe->get('component_shortname').'&f=bffront_css,front_css');
bfDocument::addscript(bfCompat::getLiveSite() . '/'._PLUGIN_DIR_NAME.'/system/blueflame/bfCombine.php?type=js&c='.$mainframe->get('component_shortname').'&f=jquery,bffront_js,front_js');

/* Setup our framework */
bfLoad('bfSmarty');
bfLoad('bfController');
bfLoad('bfModel');
bfLoad('bfCache');
$cache =& bfCache::getInstance($mainframe->get('component'));
$registry =& bfRegistry::getInstance($mainframe->get('component'));

/* include the framework config */
require_once(_BF_JPATH_BASE . DS .  'components' . DS . $mainframe->get('component') . DS . 'etc' . DS . 'framework.config.php');

/* stop over riding the page title */
$registry->setValue('isModule', true);

/* Include our front controller */
$r = 'bfFramework_'.$mainframe->get('component_shortname').'.controller.front';
require_once($registry->getValue($r));

$registry =& bfRegistry::getInstance('com_tag');

/* Make a controller instance and provide some sensible defaults */
$controller = new com_tagController();

$tmp =& bfSmarty::getInstance('com_tag');
$user =& bfUser::getInstance();

$tmp->caching = false;
$tmp->compile_id = md5('tag cloud');

/* Load Model */
$cloud_details =& $controller->getModel('tag');
/* Get all rows */
$clould  = $cloud_details->generateCloud(true);
$u8 = new bfUtf8();

$tag_items = array();
foreach ($clould as $row){
	foreach ($row as $k=>$v){
		if ($k=='tagname'){
			$t[$k] = $u8->utf8ToHtmlEntities($v);
		} else {
			$t[$k] = $v;
		}
	}
	$tag_items[] = $t;
}

$tmp->assign('items', $tag_items );

if ($registry->getValue('config.tagcloudintegers')==='1'){
	$tmp->assign('showqty', 1 );
}
/* c20ad4d76fe97759aa27a0c99bff6710 */
$tmp->display(md5(12).'.php', true);
?>