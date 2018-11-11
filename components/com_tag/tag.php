<?php
/**
 * @version $Id: tag.php 827 2007-06-12 18:03:41Z phil $
 * @package #PACKAGE#
 * @subpackage #SUBPACKAGE#
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 */
//global $QUERY_STRING;
//echo $QUERY_STRING;
//die;
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
		define('JPATH_CONFIGURATION', $GLOBALS['mosConfig_absolute_path']);
		if (!defined('DS')) 		define('DS', DIRECTORY_SEPARATOR);
	}
} else {
	header('HTTP/1.1 403 Forbidden');
	die('Direct Access Not Allowed');
}

if (!defined('JPATH_SITE')) define('JPATH_SITE', $mosConfig_absolute_path);
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
if (!defined('_JEXEC')) define('_JEXEC', '1');
if (!defined('BF_PLATFORM')) define('BF_PLATFORM','STANDALONE');
if (!defined('_BF_JPATH_BASE')) define('_BF_JPATH_BASE', $GLOBALS['mosConfig_absolute_path']);

global $mainframe;
/* define our components names */
$mainframe->set('component', 'com_tag');
$mainframe->set('component_shortname', 'tag');

/* Pull in the bfFramework */
include (JPATH_CONFIGURATION . DS .  _PLUGIN_DIR_NAME . DS . 'system' . DS . 'blueflame' . DS . 'bfFramework.php');

/* Check we have rights to do this */
bfSecurity::checkPermissions('Front','view front');

/* Initialise the session before calling the controller constructor */
$bfsession =& bfSession::getInstance($mainframe->get('component'));
$registry =& bfRegistry::getInstance($mainframe->get('component'), $mainframe->get('component'));


$log =& bfLog::getInstance();
$log->log('Loading frontend');

/* Get the Document so we can add things to it */
/* Set the Page Generator Meta Tag */
bfDocument::setGenerator($registry->getValue('Component.Title') . ' - ' . bfText::_('More details at http://www.blueflameit.ltd.uk'));

if (!defined('_BF_TAG_FRONTCSS')){
	bfDocument::addCSS(bfCompat::getLiveSite() . '/'._PLUGIN_DIR_NAME.'/system/blueflame/bfCombine.php?type=css/tag/bffront_css,front_css');
	define('_BF_TAG_FRONTCSS',1);
}
if (!defined('_BF_TAG_FRONTJS')){
	bfDocument::addscript(bfCompat::getLiveSite() . '/'._PLUGIN_DIR_NAME.'/system/blueflame/bfCombine.php?type=js&amp;c='.'tag'.'&amp;f=jquery,bffront_js,front_js');
	define('_BF_TAG_FRONTJS',1);
}

/* Set the Default Page Title - Should be overridden by xAJAX tasks Later */
bfDocument::setTitle($registry->getValue('Component.Title') . ' v' . $registry->getValue('Component.Version') );

bfDocument::addPathway( $registry->getValue('Component.Title'), 'index.php?option='.$mainframe->get('component') );

/* include our other framework libs */
bfLoad('bfController');
bfLoad('bfModel');

/**
 * Pull in and set up the controller
 * then exec the task for this URI
 */
require($registry->getValue('bfFramework_'.$mainframe->get('component_shortname').'.controller.front'));

$controller_class = $mainframe->get('component') . 'Controller';
$controller = new $controller_class();
/* @var $controller bfController */

$controller->setArguments( bfRequest::get('REQUEST') , false );

/* If the execute cannot find xfoo in the controller it sets view as foo */
$task = bfRequest::getVar('task','frontpage','REQUEST');

$controller->execute( $task );

/* Deal with the layout/view or just return some xajax actions*/
switch ($controller->getLayout()) {

	case 'xajax':
		break;

	case 'none':
		break;

	case 'text':
		break;

	case "html":
	case 'view':

		/* Get the view */
		$view = $controller->getView();

		/* check the view */
		if (!$view) {
			bfError::raiseError('404',bfText::_('We dont have a view!!'));
		}

		/* if no view set then set the view name the same as the task name */
		if (!isset($view)) {
			$view = $task;
		}

		/* we need to echo it as the default is to just return the html */
		echo $controller->renderView();
		break;

	case 'xml':
		// Not implemented yet but you probably only need to
		// point to an XML generating php file in the view directory.
		//
		// foreach( $controller->getModelList() as $modelname => $modelObject) {
		// $$modelname = $modelObject->getPublicVars();
		// }
		// ob_start();
		// require_once('view'.DS.$task."_xml".DS.'.php');
		// $message=ob_get_contents();
		// ob_end_flush();

		/* test using message only */
		///index2.php?option=com_security&task=clearSession&no_html=1
		header('Content-Type: text/xml' );
		echo '<xml><message>'.$controller->getMessage().'</message></xml>';

		break;

}
?>