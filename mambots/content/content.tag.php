<?php
/**
 * @version $Id: content.tag.php 827 2007-06-12 18:03:41Z phil $
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

if (!defined('_BF_TAG_FRONTCSS')){
	bfDocument::addCSS(bfCompat::getLiveSite() . '/'._PLUGIN_DIR_NAME.'/system/blueflame/bfCombine.php?type=css/tag/bffront_css,front_css');
	define('_BF_TAG_FRONTCSS',1);
}
if (!defined('_BF_TAG_FRONTJS')){
	bfDocument::addscript(bfCompat::getLiveSite() . '/'._PLUGIN_DIR_NAME.'/system/blueflame/bfCombine.php?type=js&amp;c='.'tag'.'&amp;f=jquery,bffront_js,front_js');
	define('_BF_TAG_FRONTJS',1);
}

if (_BF_PLATFORM=='JOOMLA1.0'){
	$_MAMBOTS->registerFunction( 'onPrepareContent', '_bf_tag_showfooter' );
}

function _bf_tag_showfooter($published, &$row, &$params, $page=0 ){
	global $mainframe;
	$mainframe->set('component', 'com_tag');
	$mainframe->set('component_shortname', 'tag');

	global $QUERY_STRING;
	if (ereg('pop=1', $QUERY_STRING)) return ;

	/* find our where we are */
	$option = bfRequest::getVar('option','','request','string',0);
	$task = bfRequest::getVar('task','','request','string',0);

	/* Check we are in allowed components */
	$c = array('com_content','com_frontpage','com_tag');
	if (!in_array($option,$c)) {
		return;
	}

	if (ereg('\{hidetags\}', $row->text)){
		$row->text = str_replace('{hidetags}','',$row->text);
		return;
	}

	/* If this page is not my page then hide me... */

	/* Hide adding tags on Modules - tags on modules is not supported */
	if (@$row->content){
		return;
	}

	/* Setup our framework */
	include_once(_BF_JPATH_BASE . DS .  'mambots' . DS . 'system' . DS . 'blueflame' . DS . 'bfFramework.php');

	if (!defined('_IS_XAJAX_CALL')) define('_IS_XAJAX_CALL', false);

	bfLoad('bfController');
	bfLoad('bfModel');


	$registry =& bfRegistry::getInstance();

	/* stop over riding the page title */
	$registry->setValue('isMambot', true);

	/* hide if on frontpage - (Optional - see config) */
	if ($registry->getValue('config.showonfrontpage') == 0 && $option=='com_frontpage') return;

	/* hide if on introtext only - (Optional - see config) */
	if ($registry->getValue('config.showonintrotext') == 0 && $task != 'view') {
		return $row->text;
	}

	/* Include our front controller */
	require_once($registry->getValue('bfFramework_tag.controller.front'));

	/* Security Check */
	$task = (string) bfRequest::getVar('task','mambot','request');

	/* Make a controller instance and provide some sensible defaults */
	$controller_class = $mainframe->get('component') . 'Controller';
	$controller = new $controller_class();
	/* @var $controller bfController */

	/* Set the default models path */
	$controller->setModelPath(bfCompat::getAbsolutePath() . DS . 'components' . DS . 'com_tag' .DS. 'model');

	/* Pass the controller incoming args */
	$controller->setArguments( bfRequest::get('request') );

	/* plug the row into the execute controller */
	$registry->setValue('row',$row);

	/* If the execute cannot find xfoo in the controller it sets view as foo */
	$taskresult = $controller->execute( 'plugin_footerText' );

	/* error checking - if we dont have a view we dont know what to do! */
	$view = $controller->getView();

	/* If we have no view then try the task name as a view name */
	if (!isset($view) || $view == '') $view = $task;

	/* Deal with the layout/view or just return some xajax actions*/
	switch ($controller->getLayout()) {

		case 'none':
			break;

		case 'html':
		case 'view':
			/* Get the view, load it, parse it and get HTML back */

			$row->text = $controller->renderView($row->text);
			break;

		case 'xml':
			// Not implemented yet but you probably only need to
			// point to an XML generating php file in the view directory.
			break;
	}
}
?>