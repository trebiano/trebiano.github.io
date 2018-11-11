<?php
/**
 * @version $Id: mod_tag_latest.php 827 2007-06-12 18:03:41Z phil $
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
require_once($registry->getValue('bfFramework_'.$mainframe->get('component_shortname').'.controller.front'));

$registry =& bfRegistry::getInstance('com_tag');

/* Make a controller instance and provide some sensible defaults */
$controller = new com_tagController();

$tmp =& bfSmarty::getInstance('com_tag');
$user =& bfUser::getInstance();

$tmp->caching = false;
$tmp->compile_id = md5('most latest items');

$data =& $controller->getModel('tag');
$latestItems = $data->getLAtestTags();

/* popular */
$tag_items = array();
foreach ($latestItems as $row){

	foreach ($row as $k=>$v){
		$t[$k] = $v;
	}
	$tag_items[] = $t;
}
$tmp->assign('latestitems', $tag_items );

if ($registry->getValue('config.tagcloudintegers')==='1'){
	$tmp->assign('showqty', 1 );
}

$tmp->display(md5(7).'.php', true);
?>