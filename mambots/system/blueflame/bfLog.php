<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: bfLog.php 827 2007-06-12 18:03:41Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 *
 */

class bfLog {

	/**
	 * @var string The logfiles path and filename
	 */
	var $_logFile;
	/**
	 * @var bool Toggle logging
	 */
	var $_controller_log="off";

	/**
	 * Enter description here...
	 *
	 * @var unknown_type
	 */
	var $_addTimeStamp = true;

	/**
	 * I store the reegistry
	 *
	 * @var object bfRegistry
	 */
	var $_registry = null;


	/**
	 * Enter description here...
	 *
	 */
	function __construct($component) {
	}

	/**
	 * Enter description here...
	 *
	 * @return bfLog
	 */
	function bfLog($component) {
		$this->__construct($component);
		$this->_component = $component;
		/* Set up our registry and namespace */
		$this->_registry =& bfRegistry::getInstance($component, $component);
		$this->_logFile = $this->_registry->getValue('config.devLogFile');
	}

	/**
      * This implements the 'singleton' design pattern.
      */
	function &getInstance ($component='framework') {
		static $instance;
		if (!isset($instance)) {
			$c = __CLASS__;
			$instance = new $c($component);
		}
		$instance->_registry =& bfRegistry::getInstance($component, $component);
		$instance->_logFile = $instance->_registry->getValue('config.devLogFile', '/dev/null');
		return $instance;
	}

	/**
     * Turn logging on if I can touch the logfile
     *
     */
	function setLogOn() {
		$this->getLogFileFromConfig();
		if (@touch($this->_logFile)) $this->_controller_log="on";
	}

	/**
     * Turn logging off
     *
     */
	function setLogOff() {
		$this->_controller_log="off";
	}

	/**
	 * Enter description here...
	 *
	 */
	function getLogFileFromConfig(){
		$this->_logFile = $this->_registry->getValue('config.devLogFile');
	}

	/**
     * Append $msg with a timestamp to the end of the log file.
     *
     * @param unknown_type $msg
     */
	function log( $msg ) {
		if (!$this->_logFile){
			$this->_logFile = $this->_registry->getValue('config.devLogFile');
		}
		$this->checkLogFile($msg);
		if ($this->_controller_log == 'on') {
			if (!is_string($msg)) {
				$this->logObject($msg);
			} else {
				$timestamp = $this->getTimeStamp();
				$flog = fopen($this->_logFile,"a");
				fwrite($flog,$timestamp."$msg \n");
				fclose($flog);
			}
		}
	}


	/**
	 * give log some balnk new lines
	 *
	 * @param int $num
	 */
	function spacer($num){
		if ($this->_controller_log == 'on') {
			while ($num > 0){
				$flog = fopen($this->_logFile,"a");
				fwrite($flog,"\n");
				fclose($flog);
				$num--;
			}
		}
	}

	/**
     * Append an object to the end of the log file.
     *
     * @param object $arr
     */
	function logObject($arr){
		ob_start();

		print_R($arr);

		$CONTENTS = ob_get_contents();
		ob_end_clean();
		$this->log('OBJECT: ' . $CONTENTS);

	}

	/**
	 * Add a timestamp to log entries.
	 *
	 * @return unknown
	 */
	function getTimeStamp(){
		if ($this->_addTimeStamp==true){
			return date( 'Y-m-d H:i:s', time() ).': ';
		}
	}

	/**
	 * Dump contents of log file to the screen
	 *
	 */
	function dumpToScreen(){
		echo '<pre>'.file_get_contents($this->_logFile).'</pre>';
	}

	/**
     *
     * I ensure that the log file is writable and switch off logging
     * if it is not.
     *
     * @param unknown_type $msg
     */
	function checkLogFile($msg=null){
		if (!is_writeable($this->_logFile)) {
			$this->setLogOff();
		//	bfError::raiseWarning('404','Could not write to log file...' .  $msg . '<br />');
		}
	}

	/**
     *
     * I truncate the log file (to 0 bytes by default).
     *
     * @param unknown_type $msg
     */
	function truncate( $size=0 ) {
		$this->checkLogFile();
		if ($this->_controller_log == 'on') {
			$flog = fopen($this->_logFile,'a');
			ftruncate($flog,$size);
			fclose($flog);
		}
	}

}

?>
