<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: bfError.php 827 2007-06-12 18:03:41Z phil $
 * @package bfError
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 */

class bfError {

	/**
	 * Enter description here...
	 *
	 * @param integer $errno
	 * @param string $msg
	 */
	function raiseError( $errno, $msg ) {
		global $mainframe;
		// Test and  xajax cases
		$log=&bfLog::getInstance();
		$log->log("bfError: $errno: $msg");
		$registry =& bfRegistry::getInstance($mainframe->get('component'), $mainframe->get('component'));
		$registry->setValue('errno',$errno);
		$registry->setValue('error',$msg);

		if ( _BF_TEST_MODE == true || defined('_IS_XAJAX_CALL') ) {
		} else {
			// Normal FE case
			$error=bfText::_('Error');
			$s = '<span class="error-red"><h1>%s %s - %s!</h1><p>%s</p></span>';
			echo 	sprintf($s,
			$errno,
			$error,
			$msg,
			$msg);
			//			parent::raiseError( $errno, $msg );
		}
	}

	function raiseWarning($errno, $msg ) {
		// Test and  xajax cases
		$log =& bfLog::getInstance();
		$log->log("bfError: $errno: $msg");

		// Normal FE case
		echo '<dl style="background-color: rgb(255, 255, 255);" id="system-message" class="message fade"><dt class="error">Error</dt><dd class="error"><ul>';
		echo '<li>'. $msg . '</li></ul></dd></dl>';
	}


	function _403($str){
		_('403', bfText::_('Access Denied'), $str);
	}

	function _($errno, $error, $str){
		$error_translated=bfText::_('Error');
		$s = '<h1>%s $error - %s!</h1><p>%s</p>';
		sprintf($s,
		$errno,
		$error_translated,
		$error,
		$str);
	}
}
?>
