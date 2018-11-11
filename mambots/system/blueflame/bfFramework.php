<?php
/**
 * @version $Id: bfFramework.php 827 2007-06-12 18:03:41Z phil $
 * @package bfFramework
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
	}
} else {
	header('HTTP/1.1 403 Forbidden');
	die('Direct Access Not Allowed');
}

define('_BF_JPATH_LIBRARIES',dirname(__FILE__) . DS . 'libs');

/**
 * Compatibility with other charsets and older mysql versions
 * 
 * Turn off utf-8 mysql set names utf-8 
 * Used by bfMysql
 */
define('_BF_UTF8_MYSQL', true);

/* increase memory limit - a bit of breathing space on shared hsots */
@ini_set('memory_limit', '32M');

/* set zend compatibility mode to its default - see: http://groups.google.com/group/joomla-devel/browse_thread/thread/dd7ae8348dd05966/28d0ce55544fc13f?#28d0ce55544fc13f */
@ini_set('zend.ze1_compatibility_mode', '0');

/* initialize test mode */
if (!defined('_BF_TEST_MODE')) define ('_BF_TEST_MODE',false);

/* make our compatibility layer active */
if (!class_exists('bfCompat')) require( dirname(__FILE__) . DS . 'bfCompat.php' );

/* Set our defines */
require(dirname(__FILE__) . DS . 'bfDefine.php');

/* allow loading of bfLibs by bfLoad(); */
require(dirname(__FILE__) . DS . 'bfLoader.php');

/* am I an admin user ? */
$isAdmin = bfCompat::isAdmin();

/* The following ORDER is VERY important to fulfil dependancies! */
bfLoad('bfDatabase');
bfLoad('bfRedirect');
bfLoad('bfText');
bfLoad('bfString');
bfLoad('bfError');
bfLoad('bfLog');
bfLoad('bfRegistry');
bfLoad('bfRequest');
bfLoad('bfInputFilter');
bfLoad('bfString');
if ($isAdmin) bfLoad('bfToolbar');
if ($isAdmin) bfLoad('bfSubmenu');
bfLoad('bfButtons');
bfLoad('bfTable');
bfLoad('bfUser');
bfLoad('bfHTML');
bfLoad('bfDocument');
bfLoad('bfUTF8');
bfLoad('bfCache');

global $mainframe;
/* Set up our registry and namespace */
$registry =& bfRegistry::getInstance($mainframe->get('component'), $mainframe->get('component'));


/* load utils */
bfLoad('bfUtils');


/* include configuration class */
bfLoad('bfConfig');

/* @var $bfConfig bfconfig */
$bfConfig =& bfConfig::getInstance($mainframe->get('component'));
//$bfConfig = new bfConfig($component);

/* include the framework config */
require_once(JPATH_ROOT . DS .  'components' . DS . $mainframe->get('component') . DS . 'etc' . DS . 'framework.config.php');

bfLoad('bfSession');
/* start our session */

$bfsession =& bfSession::getInstance($mainframe->get('component'));

/* set up our user */
$bfUser =& bfUser::getInstance();

/* Start logging if needed, defined in framework configuration */
$log =& bfLog::getInstance($mainframe->get('component'));
if ($registry->getValue('config.devLog') == '1') $log->setLogOn();
/* Set up bfText debugging */

bfText::setDebug($mainframe->get('component'));
/* Set our Security Levels and checks */
bfLoad('bfSecurity');
?>