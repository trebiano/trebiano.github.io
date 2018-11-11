<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: bfToolbar.php 827 2007-06-12 18:03:41Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 */

// send, delete, help, cancel, config, apply, back, forward, save, edit, copy, move, new, upload, assign, html, css, publish, unpublish, restore, trash, archive, unarchive, preview, default


/**
 * I build and display the toolbar for Blue Flame components
 *
 */
class bfToolbar {

	/**
	 * Enter description here...
	 *
	 * @var unknown_type
	 */
	var $_buttons = array();

	/**
	 *  The controller
	 *
	 * @var unknown_type
	 */
	var $_controller = null;

	/**
      * This implements the 'singleton' design pattern.
      */
	function &getInstance (&$controller) {

		static $instance;
		if (!isset($instance)) {

			$c = __CLASS__;
			$instance = new $c();
			$instance->_controller =& $controller;
		}
		return $instance;
	}

	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	function _getPrefix(){
		return '<table class="bftoolbar"><tbody><tr>';
	}

	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	function _getSuffix(){
		return '</tr></tbody></table>';
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $icon
	 * @param unknown_type $xajaxTask
	 */
	function addButton($icon, $xajaxTask='todo', $tip=''){

		switch ($xajaxTask){

			case "xunpublish":
				$xajaxTask = "javascript:if(document.adminForm.boxchecked.value==0){alert(".bfText::_('Please make a selection from the list to unPublish').");}else{submitToXAJAX('unpublish')}";
				break;
			case "xpublish":
				$xajaxTask = "javascript:if(document.adminForm.boxchecked.value==0){alert(".bfText::_('Please make a selection from the list to Publish').");}else{submitToXAJAX('publish')}";
				break;

				default:
				$xajaxTask = 'submitToXAJAX(\''.$xajaxTask.'\');';
				break;
		}

		$tip ? $title = bfText::_(ucwords($icon)) . '::' . $tip : $title = ucwords($icon);
		$tip ? $hasTip = 'hasTip ' : $hasTip = '';

		$html ='<td id="bftoolbar-##ICON##" class="'.$hasTip.'bfbutton" title="'.$title.'"><a class="toolbar" onclick="##ONCLICK##" href="#"><span class="icon-32-##ICON##"></span>##UICON##</a></td>';
		$html = str_replace('##ICON##',$icon, $html);
		$html = str_replace('##ONCLICK##',$xajaxTask, $html);
		$html = str_replace('##UICON##',bfText::_(ucwords($icon)), $html);
		$this->_buttons[] = $html;
	}

	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	function _renderButtons(){
		return implode("\n",$this->_buttons);
	}

	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	function toString(){
		$html = $this->_getPrefix();
		$html .= $this->_renderButtons();
		$html .= $this->_getSuffix();
		return $html;
	}

	/**
	 * Enter description here...
	 *
	 */
	function render(){
		$this->_controller->setToolbar($this->toString());
	}
}
?>
