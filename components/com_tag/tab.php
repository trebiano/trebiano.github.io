<?php
/**
 * @version $Id: tab.php 827 2007-06-12 18:03:41Z phil $
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
		define('JPATH_ROOT', $GLOBALS['mosConfig_absolute_path']);
		if (!defined('DS')) 		define('DS', DIRECTORY_SEPARATOR);
	}
} else {
	header('HTTP/1.1 403 Forbidden');
	die('Direct Access Not Allowed');
}

/* Joom!Fish 1.7 */
if (file_exists(JPATH_ROOT . '/components/com_joomfish/joomfish.php' )) {
	require_once( JPATH_ROOT . '/administrator/components/com_joomfish/mldatabase.class.php' );
	require_once( JPATH_ROOT . '/administrator/components/com_joomfish/joomfish.class.php' );
}

global $mainframe;
/* define our components names */
$mainframe->set('component', 'com_tag');
$mainframe->set('component_shortname', 'tag');

require_once(JPATH_ROOT . DS .  _PLUGIN_DIR_NAME . DS . 'system' . DS . 'blueflame' . DS . 'bfCompat.php');
require_once(JPATH_ROOT . DS .  _PLUGIN_DIR_NAME . DS . 'system' . DS . 'blueflame' . DS . 'bfFramework.php');


bfDocument::addCSS(bfCompat::getLiveSite() . '/'._PLUGIN_DIR_NAME.'/system/blueflame/bfCombine.php?type=css&c='.$mainframe->get('component_shortname').'&f=bfadmin_css,admin_css');

if (_BF_PLATFORM=='JOOMLA1.0'){
	bfDocument::addscript(bfCompat::getLiveSite() . '/'._PLUGIN_DIR_NAME.'/system/blueflame/bfCombine.php?type=js&c='.$mainframe->get('component_shortname').'&f=mootools,jquery,bfadmin_js,admin_js');
} else {
	bfDocument::addscript(bfCompat::getLiveSite() . '/'._PLUGIN_DIR_NAME.'/system/blueflame/bfCombine.php?type=js&c='.$mainframe->get('component_shortname').'&f=jquery,bfadmin_js,admin_js');
}

/* Setup our framework */
include_once(JPATH_ROOT . DS .  _PLUGIN_DIR_NAME . DS . 'system' . DS . 'blueflame' . DS . 'bfFramework.php');
bfLoad('bfSmarty');
bfLoad('bfController');
bfLoad('bfModel');
$registry =& bfRegistry::getInstance();

if (_BF_PLATFORM=='JOOMLA1.0'){
	global $_MAMBOTS;
	$_MAMBOTS->loadBotGroup('system');
	$_MAMBOTS->trigger('onAfterStart', array(true), true);
}

/* stop over riding the page title */
$registry->setValue('isModule', true);

/* Include our front controller */
require_once($registry->getValue('bfFramework_'.$mainframe->get('component_shortname').'.controller.front'));

/* Make a controller instance and provide some sensible defaults */
$controller = new com_tagController();

$tmp =& bfSmarty::getInstance('com_tag');
$user =& bfUser::getInstance();

$tmp->caching = false;
$tmp->compile_id = md5('most popular items');

$data =& $controller->getModel('tag');

if (_BF_PLATFORM=='JOOMLA1.0'){
	$contentid = bfRequest::getVar('id');
} else{
	$contentid = bfRequest::getVar('cid');
	$contentid = $contentid[0];
}

$tag =& $controller->getModel('tag');
$tagtotal = $tag->getCount();

$map =& $controller->getModel('tag_map');
$tags = $map->getTagsForContentId($contentid,true);

$tag_items = array();
foreach ($tags as $r){

	foreach ($r as $k=>$v){
		$t[$k] = $v;
	}
	$tag_items[] = $t;
}
$tmp->assign('tagitems', $tag_items );
$tmp->assign('content_id', $row->id );
$tmp->assign('tagcount', $tagtotal );
$tmp->assign('COMPONENT', 'com_content' );



$contentid = bfRequest::getVar('id','','request','int');

if (_BF_PLATFORM=='JOOMLA1.0'){
	$tabs->startTab("Tags","tags-page");


	echo '
		<style>
		#bfTagsTab {
		background-color: #fff;
		}
		</style>';
}

/* d3d9446802a44259755d38e6d163e820 */
echo '<div id="bfTagsTab">'.$tmp->display(md5(10).'.php', true). '</div>';

if (_BF_PLATFORM=='JOOMLA1.0'){
	$tabs->endTab();
}
?>