<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: bfSecurity.php 827 2007-06-12 18:03:41Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 *
 */

/* If we are calling through xAJAX then get our args */
@defined('_IS_XAJAX_CALL') ? $args = func_get_args() : $args = array();

/* Check if we are logged in */
bfSecurity::checkLoginSession();

/**
 * bfSecurity is a class with static methods that provide
 * security checks and functions
 */
class bfSecurity {

	/**
	 * Security check: All our xajax functions start with an x prefix so this
	 * Stops people calling our functions with XSS etc...
	 */
	function checkXFunction($args){

		/* Get log */
		$log =& bfLog::getInstance();
		$log->log('bfSecurity: ... checking XFunction ...');

		$task = (string) bfSecurity::cleanVar($args[0], 0);

		if (count($args) && is_string($task)){

			if (substr($task,0,1)!='x'){
				$user =& bfUser::getInstance();
				$log->log('bfSecurity: ACCESS DENIED, xHandler task did not start with "x"');
				$log->log('bfSecurity: IP Address: ' . $_SERVER['REMOTE_ADDR']);
				$log->log('bfSecurity: User: ' . $user->get('id') . '::' . $user->get('username'));
				if (_IS_XAJAX_CALL){
					return false;
				} else {
					bfError::raiseError('403','Access Denied to Security.AdminController.'.$task);
				}
			} else {
				/* Check the framework config for access controls */
				if (bfSecurity::acl('AdminController.'.$task,' method ' . $task ,false)===false){
					if (_IS_XAJAX_CALL){
						return false;
					} else {
						bfError::raiseError('403','Access Denied to Security.AdminController.'.$task);
					}
				}
			}
		}
	}

	/**
	 * Security check: All our xajax functions start with an xpublic prefix so this
	 * Stops people calling our functions with XSS etc...
	 */
	function checkXPublicFunction($args){
		/* Get log */
		$log =& bfLog::getInstance();

		$task = (string) bfSecurity::cleanVar($args[0], 0);

		if (count($args) && is_string($task)){
			$log->log('bfSecurity: checking xhandler task: '.$task.' against security rules ...');

			if (substr($task,0,7)!='xpublic'){
				$user =& bfUser::getInstance();
				$log->log('bfSecurity: ACCESS DENIED, xHandler task did not start with "xpublic"');
				$log->log('bfSecurity: IP Address: ' . $_SERVER['REMOTE_ADDR']);
				$log->log('bfSecurity: User: ' . $user->get('id') . '::' . $user->get('username'));
				if (_IS_XAJAX_CALL){
					return false;
				} else {
					bfError::raiseError('403','Access Denied');
				}
			} else {

				/* Check the framework config for access controls */
				if (bfSecurity::acl('XAJAXFrontController.'.$task,' method ' . $task ,false)===false){
					if (_IS_XAJAX_CALL){
						$log->log('TEST:: '.'XAJAXFrontController.'.$task);
						return false;
					} else {
						bfError::raiseError('403','Access Denied to Security.XAJAXFrontController.'.$task);
					}
				}
			}
		}
	}

	/**
	 * I check if we are in administrator check we are logged in!
	 *
	 * @return bool
	 */
	function checkLoginSession(){
		/* Get log */
		$log =& bfLog::getInstance();

		/* Set up */
		if (bfCompat::isAdmin() && (_BF_TEST_MODE == false)){
			$user =& bfUser::getInstance();

			if ($user->get('id') == 0) {

				/* We have no session, session expired, or logged out */

				/* Are we calling this from xAJAX calls ? */
				if (@defined('_IS_XAJAX_CALL')){
					$log->log('bfSecurity: WARNING Not logged in! ...');
					// @TODO Does this do anything $objResponse is undefined!
					/* Display popup alert */
					$objResponse->alert(bfText::_('Your sesion has expired, Please login again.'));
					/* Redirect back to admin login page */
					$objResponse->redirect(bfURI::base());
					/* Return XML to xAJAX */
					return $objResponse;
					/* just in case */
					die();
				} else {
					$log->log('bfSecurity: WARNING Not logged in! ...');
					bfRedirect(bfCompat::getLiveSite(), bfText::_('Your sesion has expired, Please login again.'));
					/* just in case */
					die();
				}
			}
		}
		return true;
	}

	function acl($acl, $what=null, $die=true){
		$log =& bfLog::getInstance();
		$log->log('Checking ' . $acl);
		return bfSecurity::checkPermissions($acl, $what, $die);
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $acl
	 * @param unknown_type $what
	 * @param unknown_type $die
	 * @return unknown
	 */
	function checkPermissions($acl, $what=null, $die=true){
		global $mainframe;
		$log =& bfLog::getInstance($mainframe->get('component'));
		/* Set up our registry and namespace */
		$registry =& bfRegistry::getInstance($mainframe->get('component'), $mainframe->get('component'));

		$user =& bfUser::getInstance();

		if ($registry->getValue('Security.'.$acl) > $user->get('gid')){
			if (defined('_IS_XAJAX_CALL')){
				$log->log('Access Denied - userid='.$user->get('id').' gid= '.$user->get('gid').bfText::_('You do not have sufficient access rights to ') . $what);
				if ($die===true) die('You do not have sufficient access rights to ' . $what);
				return false;
			} else {
				$log->log('Access Denied - userid='.$user->get('id').bfText::_(' You do not have sufficient access rights to ') . $what);
				if ($die===true) bfError::raiseError('403',bfText::_('Access Denied').' - '.bfText::_('You do not have sufficient access rights to ') . $what);
				return false;
			}
		} else {
		}
		return true;
	}

	/**
	 * Clean the input of some or any HTML depending on the
	 * input mask.
	 *
	 * @param string $var
	 * @param int $mask
	 * @param unknown_type $type
	 * @return unknown
	 */
	function cleanVar($var, $mask=0, $type=null){
		$log =& bfLog::getInstance();
		if (is_array($var)){
			return $var;
		}

		$unclean = $var;
		// Static input filters for specific settings
		static $noHtmlFilter	= null;
		static $safeHtmlFilter	= null;

//		$var = trim($var);

		switch ($mask){
			// Now we handle input filtering
			case (2):

				// If the allow raw flag is set, do not modify the variable
				$var = $var;
				break;
			case (4):
				// If the allow html flag is set, apply a safe html filter to the variable
				if (is_null($safeHtmlFilter)) {
					$safeHtmlFilter = & bfInputFilter::getInstance(null, null, 1, 1);
				}
				$var = $safeHtmlFilter->clean($var, $type);
				break;
			default:
				// Since no allow flags were set, we will apply the most strict filter to the variable
				if (is_null($noHtmlFilter)) {
					$noHtmlFilter = & bfInputFilter::getInstance(/* $tags, $attr, $tag_method, $attr_method, $xss_auto */);
				}
				$var = $noHtmlFilter->clean($var, $type);
				break;
		}

		if ($unclean != $var){
			$log =& bfLog::getInstance();
			$log->log('bfSecurity: Cleaning Var, Original: '. $unclean .' Changed to: ' . $var);
		}
		return $var;
	}

	
}
?>
