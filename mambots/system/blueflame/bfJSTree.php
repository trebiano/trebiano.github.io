<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: bfJSTree.php 857 2007-06-14 21:49:40Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2007 Blue Flame IT Ltd. All rights reserved.
 * @license see LICENSE.php
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 */

class bfJSTree {

	var $_js = '
			USETEXTLINKS = 1
			STARTALLOPEN = 0
			USEFRAMES = 0
			USEICONS = 1
			WRAPTEXT = 1
			PRESERVESTATE = 0
			// Specify if the images are in a subdirectory;
			foldersTree = gFld("<b>Categories</b>")
  			foldersTree.treeID = "Frameless-com_kb"
			';
	var $_jsfile = null;

	function bfJSTree($isFront=false){
		$this->_js = 'ICONPATH = "'.bfCompat::getLiveSite() .'/'. bfCompat::mambotsfoldername() .'/system/blueflame/view/images/"' . $this->_js;
		$this->_jsfile = '/system/blueflame/libs/treeview/treeview.js';
		$this->_jsuafile = '/system/blueflame/libs/treeview/ua.js';
	}

	function makejssafe($str){
		$str = str_replace(' ','',$str);
		$str = str_replace('?','',$str);
		$str = str_replace('-','',$str);
		return $str;
	}

	function addJStoHEAD(){
		if (@_POPUP===1){
			if (_BF_PLATFORM=='JOOMLA1.0'){
				global $mainframe;
				$ls = bfCompat::getLiveSite();
				$mf = bfCompat::mambotsfoldername();
				echo (
				sprintf('<script src="%s" type="text/javascript"></script>',
				$ls . '/' . $mf . $this->_jsuafile
				)
				);
				echo(
				sprintf('<script src="%s" type="text/javascript"></script>',
				$ls . '/' . $mf . $this->_jsfile
				)
				);

				$mainframe->addCustomHeadTag($this->_getJS());
			} else {
				// @todo Joomla 1.5
			}
		} else {
			if (_BF_PLATFORM=='JOOMLA1.0'){
				global $mainframe;
				$ls = bfCompat::getLiveSite();
				$mf = bfCompat::mambotsfoldername();
				$mainframe->addCustomHeadTag(
				sprintf('<script src="%s" type="text/javascript"></script>',
				$ls . '/' . $mf . $this->_jsuafile
				)
				);
				$mainframe->addCustomHeadTag(
				sprintf('<script src="%s" type="text/javascript"></script>',
				$ls . '/' . $mf . $this->_jsfile
				)
				);

				$mainframe->addCustomHeadTag($this->_getJS());
			} else {
				// @todo Joomla 1.5
			}
		}
	}

	function _append($str){
		$this->_js .= $str . "\n";
	}

	function _getJS(){
		return sprintf('
		<script type="text/javascript">
		%s
		</script>',
		$this->_js
		);
	}
}
?>