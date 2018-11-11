<?php
/**
 * @version $Id: xajax.tag.php 827 2007-06-12 18:03:41Z phil $
 * @package #PACKAGE#
 * @subpackage #SUBPACKAGE#
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
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
		define('JPATH_CONFIGURATION', $GLOBALS['mosConfig_absolute_path']);
		if (!defined('DS')) 		define('DS', DIRECTORY_SEPARATOR);
	}
} else {
	header('HTTP/1.1 403 Forbidden');
	die('Direct Access Not Allowed');
}

if (!defined('JPATH_CONFIGURATION')) define('JPATH_CONFIGURATION', $GLOBALS['mosConfig_absolute_path']);

/* Turn on Error Reporting for the fun of it */
//error_reporting(E_ALL);

/* Pull in the bfFramework */
include_once (JPATH_CONFIGURATION . DS .  _PLUGIN_DIR_NAME . DS . 'system' . DS . 'blueflame' . DS . 'bfCompat.php');
include_once (JPATH_CONFIGURATION . DS .  _PLUGIN_DIR_NAME . DS . 'system' . DS . 'blueflame' . DS . 'bfDocument.php');
/* Load this components stylesheet */
if (!defined('_BF_TAG_FRONTCSS')){
	bfDocument::addCSS(bfCompat::getLiveSite() . '/'._PLUGIN_DIR_NAME.'/system/blueflame/bfCombine.php?type=css/tag/bffront_css,front_css');
	define('_BF_TAG_FRONTCSS',1);
}
if (!defined('_BF_TAG_FRONTJS')){
	bfDocument::addscript(bfCompat::getLiveSite() . '/'._PLUGIN_DIR_NAME.'/system/blueflame/bfCombine.php?type=js&amp;c='.'tag'.'&amp;f=jquery,bffront_js,front_js');
	define('_BF_TAG_FRONTJS',1);
}



/* Register our functions so the xAJAX plugin knows about us */
$xajaxFunctions[] = 'bf_com_bftag_Handler';

/* Register our functions so the xAJAX plugin knows about us */
$xajaxFunctions[] = 'bf_com_bftag_AdminHandler';

/**
 * This handles passing the incoming request to superXMVC and returning XML
 *
 * @return XML
 */
function bf_com_bftag_Handler(){
	global $mainframe;

	define('_IS_XAJAX_CALL','1');
	define('JPATH_COMPONENT',dirname(__FILE__));

	/* Get Configuration from params */
	if (_BF_PLATFORM=='JOOMLA1.0'){
		global $database;
		$query = "SELECT params"
		. "\n FROM #__"._PLUGIN_DIR_NAME
		. "\n WHERE element = 'xajax.system'"
		. "\n AND folder = 'system'"
		;
		$database->setQuery( $query );
		$database->loadObject($mambot);

		$pluginParams = new mosParameters( $mambot->params );
	} else {
		$plugin =& JPluginHelper::getPlugin('system', 'xajax.system');
		$pluginParams = new JParameter( $plugin->params );
	}

	/* Start object for returning */
	$objResponse = new xajaxResponse($pluginParams->get('encoding','iso-8859-1'));

	/* define our components names */
	$mainframe->set('component', 'com_tag');
	$mainframe->set('component_shortname', 'tag');

	/* Setup our framework */
	include_once(JPATH_CONFIGURATION . DS .  _PLUGIN_DIR_NAME . DS . 'system' . DS . 'blueflame' . DS . 'bfFramework.php');

	/* let superXMVC handle our xAJAX controlling */
	require_once(JPATH_CONFIGURATION . DS .  _PLUGIN_DIR_NAME . DS . 'system' . DS . 'blueflame' . DS . 'superXMVC.php');

	/* Return XML to xAJAX */
	return $objResponse;
}

/**
 * This handles passing the incoming request to superXMVC and returning XML
 *
 * @return XML
 */
function bf_com_bftag_AdminHandler(){
	global $mainframe;

	ini_set('session.bug_compat_42',0);
	define('JPATH_COMPONENT', dirname(__FILE__));
	define('_IS_XAJAX_CALL','1');
	define('_XAJAX_ADMIN', '1');


	/* Start object for returning */
	$objResponse = new xajaxResponse();

	// mainframe is an API workhorse, lots of 'core' interaction routines
	global $database, $option, $task,$mainframe, $my;

	// must start the session before we create the mainframe object
	if (_BF_PLATFORM=='JOOMLA1.0'){
		$site = $mainframe->getCfg('live_site');
		if (@!isset($_SESSION)){
			@session_name( md5( $mainframe->getCfg('live_site') ) );
			@session_start();
		}
		$mainframe 		= new mosMainFrame( $database, $option, '..', true );
		$my 			= $mainframe->initSessionAdmin( $option, $task );
	} elseif (_BF_PLATFORM=='JOOMLA1.5'){
		/* nothing to do */
	}
	
		/* define our components names */
	$mainframe->set('component', 'com_tag');
	$mainframe->set('component_shortname', 'tag');


	/**
	 * As this is an admin controller we need to make sure we are logged in
	 * before we allow any xAJAX to take place
	 */
	if (_BF_PLATFORM=='JOOMLA1.5'){
		/* get the user object */
		$user =& JFactory::getUser();
		/* get the user id =0 if not logged in */
		$userid = $user->get('id');
	} elseif (_BF_PLATFORM=='JOOMLA1.0'){
		/* get the userid fm the session */
		$userid = $my->id;
	}

	/* We have no session, session expired, or logged out */
	if (!$userid) {
		$objResponse->alert(bfText::_('Your sesion has expired, Please login again'));
		$objResponse->redirect('index.php?option=logout&mosmsg=Your sesion has expired, Please login again');
		/* Return XML to xAJAX - dont go any further */
		return $objResponse;
	}

	/* Setup our framework */
	include_once(JPATH_ROOT . DS . _PLUGIN_DIR_NAME . DS . 'system' . DS . 'blueflame' . DS . 'bfFramework.php');

	/* let superXMVC handle our xAJAX controlling */
	require_once(JPATH_ROOT . DS . _PLUGIN_DIR_NAME . DS . 'system' . DS . 'blueflame' . DS . 'superXMVC.php');

	/* Return XML to xAJAX */
	return $objResponse;
}
?>
