<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: bfRequest.php 827 2007-06-12 18:03:41Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 */

class bfRequest {
	/**
	 * PHP4 constructor just calls PHP5 constructor
	 */
	function bfRequest() {
		$this->__construct();
	}

	/**
	 * PHP5 constructor
	 */
	function __construct() {
	}

	function getSessionVar($name, $default){
		if (isset($_SESSION[$name])) {
			return $_SESSION[$name];
		} elseif (isset($_SESSION['__default'][$name])){
			return $_SESSION['__default'][$name];
		} elseif (isset($_SESSION['__default']['user']->$name)) {
			return $_SESSION['__default']['user']->$name;
		} else {
			return $default;
		}
	}


	function getVar($name, $default = null, $hash = 'default', $type = 'none', $mask = 0){
		// Ensure hash and type are uppercase
		$hash = strtoupper( $hash );
		if ($hash === 'METHOD') {
			$hash = strtoupper( $_SERVER['REQUEST_METHOD'] );
		}
		$type	= strtoupper( $type );
		$sig	= $hash.$mask;

		// Get the input hash
		switch ($hash){
			case 'GET' :
				$input = &$_GET;
				break;
			case 'POST' :
				$input = &$_POST;
				break;
			case 'FILES' :
				$input = &$_FILES;
				break;
			case 'COOKIE' :
				$input = &$_COOKIE;
				break;
			case 'SESSION' :
				$input = &$_SESSION;
				break;
			default:
				$input = &$_REQUEST;
				break;
		}

		if (isset($GLOBALS['_JREQUEST'][$name]) && ($GLOBALS['_JREQUEST'][$name] == 'SET')) {
			// Get the variable from the input hash
			$var = (isset($input[$name]) && $input[$name] !== null) ? $input[$name] : $default;
		}elseif (!isset($GLOBALS['_JREQUEST'][$name][$sig]))	{
			$var = (isset($input[$name]) && $input[$name] !== null) ? $input[$name] : $default;
			// Get the variable from the input hash
			$var = bfRequest::_cleanVar($var, $mask, $type);

			// Handle magic quotes compatability
			if (get_magic_quotes_gpc() && ($var != $default)){
				if (!is_array($var) && is_string($var)) {
					$var = stripslashes($var);
				}
			}
			if ($var !== null) {
				$GLOBALS['_JREQUEST'][$name][$sig] = $var;
			} else {
				$var = $default;
			}
		} else {
			$var = $GLOBALS['_JREQUEST'][$name][$sig];
		}

		return $var;
	}

	function _cleanVar($var, $mask=0, $type=null){
		// Static input filters for specific settings
		static $noHtmlFilter	= null;
		static $safeHtmlFilter	= null;

		// If the no trim flag is not set, trim the variable
		if (!($mask & 1) && is_string($var)) {
			$var = trim($var);
		}

		// Now we handle input filtering
		if ($mask & 2)
		{
			// If the allow raw flag is set, do not modify the variable
			$var = $var;
		}
		elseif ($mask & 4)
		{
			// If the allow html flag is set, apply a safe html filter to the variable
			if (is_null($safeHtmlFilter)) {
				$safeHtmlFilter = & bfInputFilter::getInstance(null, null, 1, 1);
			}
			$var = $safeHtmlFilter->clean($var, $type);
		}
		else
		{
			// Since no allow flags were set, we will apply the most strict filter to the variable
			if (is_null($noHtmlFilter)) {
				$noHtmlFilter = & bfInputFilter::getInstance(/* $tags, $attr, $tag_method, $attr_method, $xss_auto */);
			}
			$var = $noHtmlFilter->clean($var, $type);
		}
		return $var;
	}

	function setVar($name, $value = null, $hash = 'default'){
		// Clean global request var
		$GLOBALS['_JREQUEST'][$name] = 'SET';

		// Get the request hash value
		$hash = strtoupper($hash);
		if ($hash === 'METHOD') {
			$hash = strtoupper($_SERVER['REQUEST_METHOD']);
		}
		switch ($hash)
		{
			case 'GET' :
				$_GET[$name] = $value;
				$_REQUEST[$name] = $value;
				break;
			case 'POST' :
				$_POST[$name] = $value;
				$_REQUEST[$name] = $value;
				break;
			case 'FILES' :
				$_FILES[$name] = $value;
				$_REQUEST[$name] = $value;
				break;
			case 'COOKIE' :
				$_COOKIE[$name] = $value;
				$_REQUEST[$name] = $value;
				break;
			default:
				$_GET[$name] = $value;
				$_POST[$name] = $value;
				$_REQUEST[$name] = $value;
				break;
		}

		return $value;
	}

	/**
	 * Fetches and returns a request array.
	 *
	 * The default behaviour is fetching variables depending on the
	 * current request method: GET and HEAD will result in returning
	 * $_GET, POST and PUT will result in returning $_POST.
	 *
	 * You can force the source by setting the $hash parameter:
	 *
	 *   post		$_POST
	 *   get		$_GET
	 *   files		$_FILES
	 *   cookie		$_COOKIE
	 *   method		via current $_SERVER['REQUEST_METHOD']
	 *   default	$_REQUEST
	 *
	 * @static
	 * @param	string	$hash	to get (POST, GET, FILES, METHOD)
	 * @param	int		$mask	Filter mask for the variable
	 * @return	mixed	Request hash
	 * @since	1.5
	 */
	function get($hash = 'default', $mask = 0){
		static $hashes;

		if (!isset($hashes)) {
			$hashes = array();
		}

		$hash		= strtoupper( $hash );
		$signature	= $hash.$mask;
		if (!isset($hashes[$signature])) {
			$result		= null;
			$matches	= array();

			if ($hash === 'METHOD') {
				$hash = strtoupper( $_SERVER['REQUEST_METHOD'] );
			}

			switch ($hash)
			{
				case 'GET' :
					$input = $_GET;
					break;

				case 'POST' :
					$input = $_POST;
					break;

				case 'FILES' :
					$input = $_FILES;
					break;

				case 'COOKIE' :
					$input = $_COOKIE;
					break;

				default:
					$input = $_REQUEST;
					break;
			}

			$result = bfRequest::_cleanVar($input, $mask);

			// Handle magic quotes compatability
			if (get_magic_quotes_gpc()) {
				$result = bfRequest::_stripSlashesRecursive( $result );
			}
			$hashes[$signature] = &$result;
		}
		return $hashes[$signature];
	}

	/**
	 * Strips slashes recursively on an array
	 *
	 * @access	protected
	 * @param	array	$array		Array of (nested arrays of) strings
	 * @return	array	The input array with stripshlashes applied to it
	 */
	function _stripSlashesRecursive( $value ){
		$value = is_array( $value ) ? array_map( array( 'bfRequest', '_stripSlashesRecursive' ), $value ) : stripslashes( $value );
		return $value;
	}
} // End of class bfRequest
?>
