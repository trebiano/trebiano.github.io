<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: bfLoader.php 827 2007-06-12 18:03:41Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 */

class bfImport {

	/**
	 * Enter description here...
	 *
	 * @var unknown_type
	 */
	var $_included = array();

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $lib
	 */
	function bfInclude($lib){
		global $mainframe;
		if (!in_array($lib,$this->_included)){

			if (ereg('helper',$lib)){
				$file = bfCompat::getAbsolutePath() . DS . 'components' . DS . $mainframe->get('component') . DS . 'lib' . DS . $lib . '.php';
				if (file_exists($file)){
					if (!require($file)){
						echo $file;
						echo "<BR>".bfText::_('COULD NOT INCLUDE LAST FILE');
					}
				}else {
					echo $file;
					echo "<BR>".bfText::_('COULD NOT INCLUDE LAST FILE');
				}
				return;
			}

			$parts = explode('.',$lib);
			if (count($parts) > 1){  // .config or .view
				$filename = $parts[1] . DS .$parts[0].'.'.$parts[1];
			} else {
				$filename = $parts[0];
			}

			if (defined('_BF_FILEINCLUDED_'.strtoupper($filename))) return ;
			$file = _BF_FRONT_LIB_DIR . DS . $filename . '.php';
			if (file_exists($file)){
				if (!require($file)){
					echo $file;
					echo "<BR>".bfText::_('COULD NOT INCLUDE LAST FILE');
				}
			} else {
				$file = _BF_FRONT_LIB_DIR . DS . $filename . '.php';
				if (file_exists($file)){
					if (!require($file)){
						echo $file;
						echo "<BR>".bfText::_('COULD NOT INCLUDE LAST FILE');
					}
				} else {
					echo $file;
					echo "<BR>".bfText::_('COULD NOT INCLUDE LAST FILE');
				}
			}
			$this->_included[] = $lib;
		}
	}

	/**
	 * PHP5 constructor does nothing.
	 *
	 */
	function __construct() {
	}

	/**
	 * PHP4 constructir just calls PHP5 constructor
	 *
	 * @return bfImport
	 */
	function bfImport() {
		$this->__construct();
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
}

/**
 * Enter description here...
 *
 * @param unknown_type $bfLib
 */
function bfLoad($bfLib){
	/* E.g.
	* bfImport('bfHTML');
	*/
	$bfFramework =& bfImport::getInstance();
	$bfFramework->bfInclude($bfLib);
}

function bfImport($bflib){
	bfLoad($bfLib);
}
