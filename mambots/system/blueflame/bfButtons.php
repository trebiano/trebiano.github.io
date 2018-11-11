<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: bfButtons.php 827 2007-06-12 18:03:41Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 */


//$mosConfig_live_site = 'http://127.0.0.1/security';

//$buttons = new bfButtons();
//$buttons->addButton('new', 		'alert(\'asdad\');', 'New');
//$buttons->addButton('refresh', 	'alert(\'asdad\');', 'Refresh');
//$buttons->addButton('cancel', 	'alert(\'asdad\');', 'Cancel');
//$buttons->addButton('ok', 		'alert(\'asdad\');', 'Ok');
//
//$buttons->display();
//echo $buttons->getHTML();

class bfButtons {

	var $_buttons = array();
	var $_supportedImages = array ('new', 'refresh', 'ok', 'cancel');
	var $_align = 'left';

	function bfButtons($align='left', $displayStyle=true){
		$this->__construct($align,$displayStyle);
	}

	function __construct($align='left', $displayStyle=true){
		$this->_align = $align;
		if ($displayStyle===true){
			$this->echoStyles();
		}
	}

	function addButton($id, $onclick, $title, $tip='',$bfHandler=true){
		if ($tip == '') $tip=bfText::_('No Help Available');
		$this->_buttons[] = array(
		'id'=>$id,
		'onclick'=>$onclick,
		'title'=>bfText::_($title),
		'tip'=>$tip,
		'usebfHandler'=>$bfHandler
		);
	}

	function echoStyles(){
		if (@!defined('_bf_echoStyles')){
			echo $this->_style();
			define('_bf_echoStyles',1);
		}
	}

	function _style(){
		global $mosConfig_live_site;
		$html = '<style>
					.commonButton {
						float: '.$this->_align.';
					}
					'.$this->_getStyles().'
					</style>';
		return $html;

	}

	function _getStyles(){
		global $mosConfig_live_site;
		$html = '';
		foreach ($this->_supportedImages as $image){

			$html .= '#bid-'.$image.' button {
						background-image:url('.bfCompat::getLiveSite().'/'.bfCompat::mambotsfoldername().'/system/blueflame/view/images/button-'.$image.'.gif);
						padding-left:10px;
					}' . "\n";
		}
		return $html;
	}


	function getHTML(){
		return $this->display(true);
	}

	function display($r=false){
		$html = '';
		foreach ($this->_buttons as $button) {
			$tooltiptitle = $button['title'] . '::' . $button['tip'];
			if ($button['usebfHandler']===true){
				$bfhandler_pre = 'bfHandler(';
				$bfhandler_post = ')';
			} else {
				$bfhandler_pre = '';
				$bfhandler_post = '';
			}
			$html .= "\n" . '<div onclick="'.$bfhandler_pre.$button['onclick'].$bfhandler_post.';" title="'.$tooltiptitle.'" id="bid-'.$button['id'].'" class="commonButton hasTip"><button onclick="return false;" name="bname_'.$button['id'].'">'.$button['title'].'</button><span>'.$button['title'].'</span></div>';
		}

		if ($r===true){
			return str_replace ("\n", "", $html);
		} else {
			echo $html;
		}
	}

	function toString(){
		$this->display();
	}
}
?>
