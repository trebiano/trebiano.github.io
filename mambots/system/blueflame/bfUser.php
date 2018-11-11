<?php
//define( '_JEXEC' , '1');
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: bfUser.php 827 2007-06-12 18:03:41Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 *
 */

class bfUser  {

	var $_my = null;

	/**
	 * PHP4 constructor just calls PHP5 constructor
	 */
	function bfUser() {
		$this->__construct();
	}

	/**
	 * PHP5 constructor
	 */
	function __construct() {
		$isAdmin = bfCompat::isAdmin();

		if (_BF_PLATFORM=='JOOMLA1.5'){
			$my 			= new stdClass();
			$my->id 		= intval( bfRequest::getSessionVar('id', '' ) );
			$my->username 	= strval( bfRequest::getSessionVar('username', '' ) );
			$my->usertype 	= strval( bfRequest::getSessionVar('usertype', '' ) );
			$my->gid 		= intval( bfRequest::getSessionVar('gid', '0' ) );
			$my->params		= bfRequest::getSessionVar('user_params', '' );
		} elseif ($isAdmin) {
			$my 			= new stdClass();
			$my->id         = intval( bfRequest::getVar( 'session_user_id', 0, 'SESSION' ) );
			$my->username     = strval( bfRequest::getVar( 'session_username', '', 'SESSION' ) );
			$my->usertype     = strval( bfRequest::getVar( 'session_usertype', '',  'SESSION' ) );
			$my->gid         = intval( bfRequest::getVar( 'session_gid', '0', 'SESSION' ) );
			$my->params        = bfRequest::getVar( 'session_user_params', '', 'SESSION'  );
		} elseif (!$isAdmin){
			if (_BF_PLATFORM=='JOOMLA1.5'){

			} else {
				global $mainframe;
				$my = $mainframe->getUser();
			}
		}
		$this->_my =& $my;
		return $this->_my;
	}

	/**
     * I implement the 'singleton' design pattern.
     */
	function &getInstance () {
		static $instance;
		if (!isset($instance)) {
			$c = __CLASS__;
			$instance = new $c();
		}
		return $instance;
	}

	function get($key){
		if (is_array($this->_my)){
			return $this->_my[$key];
		} else {
			return @$this->_my->$key;
		}
	}
}
?>