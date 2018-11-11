<?php
//define( '_JEXEC' , '1');
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: bfString.php 827 2007-06-12 18:03:41Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 */

/* utf8 should be here */
class bfString {
	/**
	 * PHP4 constructor just calls PHP5 constructor
	 */
	function bfString() {
		$this->__construct();
	}

	/**
	 * PHP5 constructor
	 */
	function __construct() {
	}

	function strtolower($format){
		return strtolower($format);
	}

	function substr($str, $start, $len=null){
		return substr($str, $start, $len);
	}

	function ucwords($str){
		return ucwords($str);
	}

	function trim($str){
		return trim($str);
	}
} // End of class String
?>
