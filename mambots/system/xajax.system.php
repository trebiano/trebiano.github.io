<?php
/**
 * xajax.system.php :: Main xajax plugin for Joomla
 *
 * copyright (c) 2006 Blue Flame IT Ltd.
 * http://www.xajax-Joomla.com
 * Contributions from:
 * Azurl
 *
 * xajax for Joomla is an open source PHP class library for easily creating powerful
 * PHP-driven, web-based Ajax Applications. Using xajax, you can asynchronously
 * call PHP functions and update the content of your your webpage without
 * reloading the page.
 *
 * xajax and xajax for Joomla Plugin are both released under the terms of the LGPL license
 * http://www.gnu.org/copyleft/lesser.html#SEC3
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package xajax for Joomla
 * @version $Id: xajax.system.php 827 2007-06-12 18:03:41Z phil $
 * @copyright Copyright (c) 2006 Blue Flame IT Ltd.
 * @license http://www.gnu.org/copyleft/lesser.html#SEC3 LGPL License
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

/* Set error reporting max */
/* turn on error reporting */
if($_SERVER['HTTP_HOST']=='127.0.0.1' || $_SERVER['HTTP_HOST']=='localhost'){
	error_reporting(E_ALL);
}

/* Define xAJAX constants */

if (!defined('XAJAX_VER'))		define ('XAJAX_VER', 'xajax_0.2.4' );
if (!defined('_BF_JPATH_BASE')) define('_BF_JPATH_BASE', $GLOBALS['mosConfig_absolute_path']);
if (!defined('DS')) 			define('DS', DIRECTORY_SEPARATOR);
if (!defined('XAJAX_ROOT'))		define ('XAJAX_ROOT', JPATH_ROOT . DS . _PLUGIN_DIR_NAME . DS . 'system' );

/* Register the Plugin in Joomla */
if (_BF_PLATFORM=='JOOMLA1.0'){
	$_MAMBOTS->registerFunction( 'onAfterStart', '_LOAD_XAJAX' );
} else {
	$mainframe->registerEvent( 'onAfterInitialise', '_LOAD_XAJAX' );
}

/*Includes all the component xajax functions and outputs JS to Head */
function _LOAD_XAJAX () {
	global $mainframe, $xajaxFunctions;

	if (_BF_PLATFORM=='JOOMLA1.0'){
		$no_html = mosGetParam($_REQUEST,'no_html',null);
	} else{
		$no_html = JRequest::getVar('no_html','','REQUEST');
	}
	if ($no_html) return;

	if (_BF_PLATFORM=='JOOMLA1.0'){
		global $database;
	} else {
		$database =& JFactory::getDBO();
	}

	/* Require xAJAX */
	require_once(XAJAX_ROOT . DS . XAJAX_VER . DS . 'xajax.inc.php');

	/* Instantiate the xajax object. */
	$xajax = new xajax();

	/* Get Configuration from params */
	if (_BF_PLATFORM=='JOOMLA1.0'){
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

	if (XAJAX_VER=='xajax_0.5'){
		/* Set defaults from params */
		$xajax->setCharEncoding($pluginParams->get('encoding','UTF-8'));
		$pluginParams->get('statusMessagesOn','1') 	? $xajax->setFlag('statusMessages',true) : $xajax->setFlag('statusMessages',false);
		$pluginParams->get('waitCursorOn','1')		? $xajax->setFlag('waitCursor',true) 	: $xajax->setFlag('waitCursor',false);
		$pluginParams->get('debug','0')				? $xajax->setFlag('debug',true) 	 	: $xajax->setFlag('debug',false);
		$pluginParams->get('decodeUTF8','0') 			? $xajax->setFlag('decodeUTF8Input',false) : $xajax->setFlag('decodeUTF8Input',true);
	} else {
		$xajax->setCharEncoding($pluginParams->get('encoding','iso-8859-1'));
		$pluginParams->get('statusMessagesOn','1') 	? $xajax->statusMessagesOn(): $xajax->statusMessagesOff();
		$pluginParams->get('waitCursorOn','1')		? $xajax->waitCursorOn() 	: $xajax->waitCursorOff();
		$pluginParams->get('debug','0')				? $xajax->debugOn() 	 	: $xajax->debugOff();
		$pluginParams->get('decodeUTF8','0') 			? $xajax->decodeUTF8InputOn() : $xajax->decodeUTF8InputOff();

	}

	/* Locate and get PHP functions to wrap from the xAJAX file in each component (if exists) */
	$xajaxFunctions = array();

	/* azrul: Look into all component folder and call xajax.component.php */
	$database->setQuery("SELECT `option` FROM #__components WHERE parent=0 AND iscore=0 ");
	$coms = $database->loadObjectList();

	foreach($coms as $com){
		$base = JPATH_ROOT;
		$file = substr($com->option, 4) . '.php';

		/* Build path to file and filename */
		$xajaxFile = $base . DS . 'components'. DS . $com->option . DS . 'xajax.' .$file ;

		$files[] = $xajaxFile;
		/* If file exists include it */
		if(file_exists($xajaxFile)){
			include_once($xajaxFile);
		}
	}

	if (!count($xajaxFunctions)){
		return;
	}

	/* Register each function with xAJAX */
	foreach ($xajaxFunctions as $call ){
		if (is_array($call)){
			/* xajax 0.2.4 */

			/* check function is now callable */
			if (is_callable($call[0])){

				/* Tell xajax about our function */
				$xajax->registerFunction($call[0]);
			}

		} else {
			/* xajax 0.5 */

			/* check function is now callable */
			if (is_callable($call)){

				/* Tell xajax about our function */
				$xajax->registerFunction($call);
			}
		}
	}

	/* get our url */
	if (_BF_PLATFORM=='JOOMLA1.0'){
		$reqURI   = $mainframe->getCfg('live_site') .  "/index.php";
	} else {
		$reqURI   = $mainframe->getCfg('live_site') . '/administrator/' .  "index.php";
	}

	/* set the xAJAX request URL */
	$xajax->setRequestURI($reqURI);

	/* @var $document JDocument */
	global $mainframe;

	/* Get the xAJAX Javascript */
	$js = $xajax->getJavascript($mainframe->getCfg('live_site') .'/'. _PLUGIN_DIR_NAME .'/system/' . XAJAX_VER . '/');

	$mainframe->addCustomHeadTag($js);

	/* set the users $my - legacy mode */
	global $my;

	/**
	 * Process any requests.  Because our requestURI is the same as our html page,
	 * this must be called before any headers or HTML output have been sent
	 **/
	if (XAJAX_VER=='xajax_0.5'){
		$xajax->processRequest();
	} else {
		$xajax->processRequests();
	}
}
?>