<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
* @version $Id: bfConfig.php 827 2007-06-12 18:03:41Z phil $
* @package bfFramework
* @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
* @license Commercial
* @link http://www.blueflameit.ltd.uk
* @author Blue Flame IT Ltd.
*
* I handle updates to the component config file.
*/

class bfConfig {

	/**
	 * The configuration file name
	 *
	 * @var unknown_type
	 */
	var $_configfileName = '';
	/**
	 * The configuration file name with its path
	 *
	 * @var unknown_type
	 */
	var $_configfileWithPath = '';
	/**
	 * I flag whether the configuration file is writable
	 *
	 * @var boolean
	 */
	var $_isWriteable = false;

	/**
	 * The current configuration
	 *
	 * @var array
	 */
	var $_currentConfig = array();

	/**
	 * PHP4 Constructor
	 * @return bfConfig
	 */
	function bfConfig($option){
		$this->__construct($option);
	}

	/**
	 * The constructor
	 */
	function __construct($option) {
		$this->_component = $option;
		$registry =& bfRegistry::getInstance($option, $option);

		$this->setConfigFileName();
		$this->isWriteable();
		//		$this->_configVars = $registry->getValue('bfFramework.config_vars'); // Set defaults
		//		$this->_tabs = $registry->getValue('bfFramework.config_tabs'); // Set defaults
		$this->loadConfigFromINIFile();
	}

	/**
      * This implements the 'singleton' design pattern.
      */
	function &getInstance ($option='') {
		static $instance;

		if (!isset ($instances)) {
			$instances = array ();
		}

		if (!isset ($instances[$option])) {
			$c = __CLASS__;
			$instances[$option] = new $c($option);
			$instances[$option]->_component = $option;
		}


//
//		if (!isset($instance)) {
//			$c = __CLASS__;
//			$instance = new $c($option);
//			$instance->component = $option;
//		}
		return $instances[$option];
//		return $instance;
	}

	/**
	 * test if config file is writable
	 */
	function isWriteable(){
		if (is_writeable($this->_configfileWithPath)){
			$this->_isWriteable = 1;
		} else {
			$this->_isWriteable = 0;
		}
		return $this->_isWriteable;
	}

	/**
	 * Set the config file name
	 */
	function setConfigFileName($filename=null){
		if ($filename){
			$this->_configfileName = $filename;
		} else {
			$name = str_replace('com_','',$this->_component);
			
			if ($name=='framework') {
				$filename = $name . 'local';
			} else {
				$filename = $name;
			}
			$this->_configfileName = $filename . '.config.php';
		}
		$this->setConfigFileWithPath();
	}

	/**
	 * Set the config file Path
	 */
	function setConfigFileWithPath($path=null){
		if ($path){
			$this->_configfileWithPath = $path;
		} else {
//			echo bfCompat::getAbsolutePath() . DS . 'components' .DS . $this->_component . DS . 'etc' . DS . $this->_configfileName;
			if (file_exists(bfCompat::getAbsolutePath() . DS . 'components' .DS . $this->_component . DS . 'etc' . DS . $this->_configfileName)){
				$this->_configfileWithPath = bfCompat::getAbsolutePath() . DS . 'components' .DS . $this->_component . DS . 'etc' . DS . $this->_configfileName;
			} else {
				echo bfText::_('Could not locate a configuration file!, I looked at: ' . bfCompat::getAbsolutePath() . DS . 'components' .DS . $this->_component . DS . 'etc' . DS . $this->_configfileName);

				// Cannot use bfError in xAJAX - yet!
				//bfError::raiseError('404',bfText::_('Could not locate a configuration file!'));
			}
		}
	}

	/**
	 * Load configuration from config file.
	 *
	 * @return unknown
	 */
	function loadConfigFromINIFile(){
		if (!$this->_configfileWithPath || !file_exists($this->_configfileWithPath)){
			return false;
		}
		/* Set up our registry and namespace */
		$registry =& bfRegistry::getInstance($this->_component, $this->_component);
		$registry->loadFile($this->_configfileWithPath,'ini', 'config');

		/* Fudge as there is a bug in bfRegistry and loadFile */
		$r = $registry->_registry['config']['data']; //object
		unset($registry->_registry['config']);

		/* fudge: Convert from object to array */
		foreach ($r as $k=>$v){
			$registry->setValue('config.'.$k,$v);
			$this->$k = $v;
		}

	}

	/**
	 * Reload the configuration from the .ini file
	 *
	 */
	function reload(){
		$this->loadConfigFromINIFile();
	}


	/**
	 * Save (write back) the config file
	 *
	 * @param unknown_type $args
	 * @return unknown
	 */
	function saveConfigFile($args){
		global $mainframe;
		$registry =& bfRegistry::getInstance($mainframe->get('component'), $mainframe->get('component'));
		$user =& bfUser::getInstance();
		$now = date( 'Y-m-d H:i:s', time() );
		$class = 'bfConfig'.$mainframe->get('component_shortname');

		/* Set our config defaults to the new values */

		$configVars = $registry->getValue('bfFramework_'.$mainframe->get('component_shortname').'.config_vars') ;
		$saveThis = array();
		foreach ($args as $key=>$value){
			if (key_exists($key, $configVars)){
				$saveThis[$key] = $value;
			}
		}

		$txt  = "<?php \n";
		$txt .= "defined( '_JEXEC' ) or die( 'Restricted access' );\n";
		$txt .= "/* Last Changed: ". $now ." */ \n";
		$txt .= "/* Changed By: ". $user->get('id') . '::' .  $user->get('username') ." */ \n\n";
		$txt .= "?>\n";
		$txt .= "[".$this->_component."]\n";

		foreach ($saveThis as $k=>$v){
			if (substr($k,0,1)!='_'){
				$txt .= $k."=".addslashes($v)."\n";
			}
		}

		if ($fp = fopen($this->_configfileWithPath, "w")) {
			fputs($fp, $txt, strlen($txt));
			fclose ($fp);
			return true;
		} else {
			return false;
		}
	}
}
?>
