<?php
//define( '_JEXEC' , '1');
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: bfText.php 827 2007-06-12 18:03:41Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 */

/*
* I am a singleton class that translates text.
* I read in the English language file then the target language file
* parsing them and storing the language values in a hash. I can then easily
* look up the translated text.
*/
class bfText {

	var $debug = false;
	var $translation = array();

	/**
	 * PHP4 constructor just calls PHP5 constructor
	 */
	function bfText() {

		$this->__construct();
	}

	/**
	 * PHP5 constructor
	 */
	function __construct() {
		global $mainframe;
		// Read in the BF framework and component language files
		// Read in the target language files if not English
		// Populate the translation hash

		// Work out the site language
		if (_BF_PLATFORM=='JOOMLA1.0') {
			// The 1.0 Way
			global $mosConfig_lang;
			$language = $mosConfig_lang;
			//			$language = 'tester';
		} else {
			// The 1.5 Way
			$config = new JConfig();
			$language = $config->lang;
		}

		$this->parse_language_file(bfCompat::getAbsolutePath() . DS . 'components' . DS . $mainframe->get('component') . DS . 'language' . DS . 'english.lang');
		if ($language != 'english') {
			$this->parse_language_file(_BF_FRAMEWORK_LANGUAGE_DIR . DS . $language . '.lang');
		}

	}

	/*
	* Parse the language file - fail silently if it does not exist
	*/
	function parse_language_file( $fname ) {
		bfLoad('bfCache');
		$cache =& bfCache::getInstance();

		$key = 'framework.language.'.$fname;

		$cached = $cache->get(md5($key),'language');
		if ($cached){
			$this->translation = $cached;
		} else {
			$fp=@fopen($fname,'r');
			if (!$fp) return;
			while( $line=fgets($fp) ) {
				list($from,$to)=preg_split('/=/', $line, 2);
				$this->translation[$from] = $to;
			}
			fclose($fp);
		}
		$cache->api_add(md5($key),$this->translation,'language');
		$cache->save();

	}

	/**
	 * I implement the 'singleton' design pattern.
	 */
	function &getInstance() {
		static $instance;
		if (!isset($instance)) {
			$c = __CLASS__;
			$instance = new $c();
		}
		return $instance;
	}

	function _($str){
		$instance =& bfText::getInstance();
		$translation =& $instance->translation;
		if (is_object($str) || is_array($str) || strlen(trim($str)) < 1) {
			return;
		}
		$str2=strtoupper($str);
		if (@$translation[$str2]) {
			if ($instance->debug) {
				return "&bull; ".trim($translation[$str2])." &bull;";
			} else {
				return trim($translation[$str2]);
			}
		}


		// return $str;
		if ($instance->debug){
			bfText::_logUnknown($str);
			return "? " .trim($str)." ?";
		} else{
			return trim($str);
		}
	}

	/*
	* Set up debugging - not in constructor as registry isn't ready yet
	*/
	function setDebug($component) {
		$instance =& bfText::getInstance($component);
		$registry =& bfRegistry::getInstance($component, $component);
		$instance->debug = $registry->getValue('config.langDebug');
	}

	function _logUnknown($str){
		$data = null;
		$unknownsFile = _BF_FRAMEWORK_LANGUAGE_DIR . DS . 'unknowns.lang';
		if (file_exists($unknownsFile)){
			$fp=@fopen($unknownsFile,'r');
			if (!$fp) return;

			while( $line=fgets($fp) ) {
				$data .= $line;
			}
			fclose($fp);

		}

		$data = trim($data);

		if (!ereg(strtoupper($str),$data)){
			$data .= "\n".strtoupper($str).'='.$str;
			if ($fp = fopen($unknownsFile, "w")) {
				fputs($fp, $data, strlen($data));
				fclose ($fp);
			}
		}
	}
}
?>
