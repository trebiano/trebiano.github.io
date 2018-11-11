<?php
defined( '_JEXEC' ) or die( 'Restricted access to bfCache' );
/**
 * @version $Id: bfChecks.php 966 2007-07-05 18:04:52Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 *
 */

define('_SKIP_UPDATE_CHECK', false);

class bfChecks {

	var $warnings = 0;
	var $warningsHTML = array();

	var $errors = 0;
	var $errorHTML = array();

	/**
	 * I run the checks
	 */
	function runchecks(){

		$popup = bfRequest::getVar('pop',null);
		if ($popup) return;

		/* checks likely to error */
		$this->_checkValidDomain();
		$this->_checkJPromoter();
		$this->_checkSMFBridge();
		$this->_checkJoomlaVersion();
		$this->_checkDBTables();
		$this->_checkXAJAX();
		$this->_checkSiteOnline();
		

		/* checks likely to warn */
		$this->_checkIE();
		$this->_checkOpenSEF();
		$this->_checkLatestVersion();
		$this->_checkFish();

		/* clean up environment */
		$this->_cleanUpOldStuff();

		if ($this->errors!==0){
			return $this->_displayErrors();
		}

		if ($this->warnings!==0){
			$this->_displayWarnings();
		}
	}

	function _checkFish(){
		$registry =& bfRegistry::getInstance();
		if ($registry->getValue('joomfish_compatible')===false) return;
		$fish = _BF_JPATH_BASE . DS . bfCompat::mambotsfoldername() . DS . 'system' . DS . 'jfdatabase.systembot.php';
		if (@file_exists($fish)){
			$this->warnings++;
			$this->warningsHTML[] = 'You have Joom!fish installed.  '
			. $registry->getValue('Component.Title')
			. ' has a content element xml for that, <a href="#" onclick="killTinyMCE();jQuery(\'div.col85\').fadeOut(\'fast\');bfHandler(\'xplugins\')">click here</a> to install it';
		}
	}

	function _checkJPromoter(){
		global $mainframe;
		$filename = _BF_JPATH_BASE . DS . bfCompat::mambotsfoldername() . DS . 'system' . DS . 'jpromoter.metaedit.php';
		if (@file_exists($filename)){
			$contents = file_get_contents($filename);
			if (ereg('session_name', $contents)) {
				return;
			}

			$this->errors++;
			$this->errorHTML[] = bfText::_('You have the JPromoter System Mambot installed. The JPromoter System Mambot doesnt handle Joomla Session Management correctly.')
			. '<br /><br />'
			. bfText::_(' We can help you with that though :-) -')
			.'<a href="index2.php?option='.$mainframe->get('component').'&entry_task=jpromoter">' . bfText::_('Click here') . '</a>' . ' ' . bfText::_('to make a small 1 line change to the JPromoter System Mambot to ensure compatibility')
			;
			if (!file_exists($filename) OR !is_writeable($filename)){
				$this->errorHTML[] = '<br/><br/><b>The file is not writeable - fix this and then refresh this page<br />
					'.$filename.'</b>';
			}
		}
	}

	function _checkSMFBridge(){
		global $mainframe;

		$filename = _BF_JPATH_BASE . DS . bfCompat::mambotsfoldername() . DS . 'system' . DS . 'SMF_header_include.php';
		if (@file_exists($filename)){
			$contents = file_get_contents($filename);
			if (ereg('_IS_XAJAX_CALL', $contents)) {
				return;
			}

			$this->errors++;
			$this->errorHTML[] = bfText::_('You have the SMF Forum Bridge installed.  The SMFBridge uses complex system plugins the same way this component does and therefore there is a conflict')
			. '<br /><br />'
			. bfText::_(' We can help you with that though :-) -')
			.'<a href="index2.php?option='.$mainframe->get('component').'&entry_task=smfbridge">' . bfText::_('Click here') . '</a>' . ' ' . bfText::_('to make a small 1 line change to the SMF bridge to ensure compatibility')
			;
			if (!file_exists($filename) OR !is_writeable($filename)){
				$this->errorHTML[] = '<br/><br/><b>The file is not writeable - fix this and then refresh this page<br />
					'.$filename.'</b>';
			}
		}
	}

	function _checkLatestVersion(){
		global $mainframe;
		if (_SKIP_UPDATE_CHECK===true) return;
		if (function_exists('file_get_contents')){
			$versions = @file_get_contents('http://www.phil-taylor.com/versions.php?option=com_tag&domain=' . $GLOBALS['mosConfig_live_site']);
			if (@$versions){
				$registry =& bfRegistry::getInstance($mainframe->get('component'));
				/* check my component version */
				$xml = new bfXml();
				$arr = $xml->parse($versions,null);
				foreach ($arr['component'] as $component){
					if ($component['name']==$registry->getValue('Component.Title')){
						if ($component['version'] > $registry->getValue('Component.Version') ){
							$this->warnings++;
							$this->warningsHTML[] = 'There is a new version of '
							. $registry->getValue('Component.Title')
							. ' available! - <a href="http://www.phil-taylor.com/cc" target="_blank">Download now</a>';
						}

						if ($component['version'] < $registry->getValue('Component.Version') ){
							$this->warnings++;
							$this->warningsHTML[] = 'This version of '
							. $registry->getValue('Component.Title')
							. ' has not yet been released - SVN Rev '. $registry->getValue('Component.Version');
						}
					}
				}
			}

		}
	}
	function _checkOpenSEF(){
		if (file_exists(_BF_JPATH_BASE . DS . 'administrator' . DS . 'components' . DS . 'com_sef' . DS . 'sef.functions.php')){
			$this->warnings++;
			$this->warningsHTML[] = '<img src="components/com_sef/admin/images/opensef_logo_small.png" align="left" />' .
			bfText::_("You currently have OpenSEF Installed.  This component NOW DOES support OpenSEF 2.0.0-RC5_SP2, however we need you to take a few more steps to get it working, to overide a few OpenSEF niggles.") .' <br />
			<br />
			<ul style="margin-left: 25px;">
			<li>' . bfText::_("Add a link in the Joomla Menu to this component - leave that unpublished (or published if you want)").'</li>
			<li>' . bfText::_("In OpenSEF Configuration -> Features: Turn off Enforce Canonical URLs").'</li>
			<li>' . bfText::_("In OpenSEF Configuration -> Components: Choose to Alias this component - this overides a known issue.(Your alis can be whatever you like - as long as the box is checked!)").'</li> 
			</ul>
			<br /><b>' . bfText::_("If you want a great SEF Component that is supported then take a look at SEF Advance from").' <a href="http://www.sakic.net">http://www.sakic.net</a></b>
			';
		}
	}
	/**
	 * Check the Joomla version
	 */
	function _checkJoomlaVersion(){
		global $_VERSION;
		if ( $_VERSION->DEV_LEVEL < 12){
			$this->errors++;
			$this->errorHTML[] = '<span style="color: red; font-weight: bolder;">'.bfText::_('You must upgrade to at least Joomla 1.0.12 in order to run this component').'</span>';
		}
	}

	/**
	 * Clean up dust form old things
	 *
	 */
	function _cleanUpOldStuff(){

		/* Remove tags component mambot */
		$filename = _BF_JPATH_BASE . DS . bfCompat::mambotsfoldername() . DS . 'content' . DS . 'tag.php';
		if (file_exists($filename)){
			@chmod($filename, 0777);
			@unlink($filename);
		}
	}

	/**
	 * make sure we have the correct tables for this component - if not install them!
	 *
	 */
	function _checkDBTables(){
		bfLoad('bfDBUtils');
		$check = new bfDBUtils();
		$check->checktables();
		$check->checkComponentLink();
	}

	/**
	 * I am interested in the version of the xAJAX Mambot installed.
	 * I need to be the blue flame one!!
	 */
	function _checkXAJAX(){
		$db =& bfCompat::getDBO();
		$db->setQuery("SELECT count(*) FROM `#__mambots`WHERE `name` = 'XAJAX System Mambot For Joomla'");
		$installed = $db->loadResult();

		if ($installed){
			$this->errors++;
			$this->errorHTML[] = '<span style="color: red; font-weight: bolder;">'.bfText::_('You must remove the xAJAX system mambot that is currently installed.  Then return here and this component will help you install the updated version of xAJAX Plugin.').'</span>';
		}
	}

	/**
	 * I display the results to the user
	 *
	 */
	function _displayErrors(){
		$registry =& bfRegistry::getInstance();
		$pre = '<div style="text-align: left;width: 500px;background-color: #fff;border: 5px solid red; padding: 15px; font-weight: bolder;">
		<h1>'.$registry->getValue('Component.Title') . ' ' .bfText::_('System Check') .'</h1>'
		;
		$post = '</div>';

		echo $pre . implode('<br /><br />',$this->errorHTML) . $post;
		return false;
	}

	function _displayWarnings(){

		$pre = '<div id="warnings" style="margin-bottom: 20px; text-align: left;width: 500px;background-color: #fff;border: 5px solid orange; padding: 15px; font-weight: bolder;">';
		$post = ' <span style="float:right"><a href="#" onClick="jQuery(\'#warnings\').hide(\'slow\');">Dismiss</a></span></div>';

		echo $pre . implode('<br /><br />',$this->warningsHTML) . $post;
	}

	function _checkValidDomain() {
		$parts = explode('/',bfCompat::getLiveSite());
		if (!ereg($parts[2],$_SERVER['HTTP_HOST'])){
			$info = file_get_contents(dirname(__FILE__) . DS . 'view' . DS . 'sameorignpolicy.php');
			$info = str_replace('###HOST###', $_SERVER['HTTP_HOST'], $info);
			$info = str_replace('###LIVESITE###', bfCompat::getLiveSite(), $info);
			$this->errors++;
			$this->errorHTML[] = $info;
		}
	}

	function _checkSiteOnline(){
		$registry =& bfRegistry::getInstance();
		if ($GLOBALS['mosConfig_offline'] == '1'){
			$this->errors++;
			$this->errorHTML[] = bfText::_("Your site needs to be set to online in the Joomla global configuration for").$registry->getValue('Component.Title').bfText::_("to work <br/><br/>This is required for XAJAX mambot, which powers the").$registry->getValue('Component.Title').bfText::_("admin console");
		}
	}
	
	function _checkIE(){
		if ($this->_detect_ie()===true){
			define('_BF_IS_IE', true);
			$this->warnings++;
			$this->warningsHTML[] = '<span style="color: red; font-weight: bolder;">
			<img src="http://www.phil-taylor.com/firefoxIcon.png" align="left" />
			'.bfText::_('This component is only developed and tested using the Firefox Web Browser.  We do not test or use Internet explorer - Please use Firefox for best speed and security.')
			.' - <a href="http://www.getfirefox.com/" target="_blank">Get Firefox Now</a></span>';
		} else {
			define('_BF_IS_IE', false);
		}
	}

	function _detect_ie(){
		if (isset($_SERVER['HTTP_USER_AGENT']) &&
		(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
		return true;
		else
		return false;
	}
}
?>