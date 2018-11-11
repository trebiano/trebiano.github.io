<?php
/**
 * @version $Id: bfCombine.php 857 2007-06-14 21:49:40Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 * 
 * Loosly based on concepts - Copyright 2006 by Niels Leenheer
 */
ini_set('display_errors','On');

/* turn these to false to stop conflicts */
define('bfJS_MOOTOOLS', true);
define('bfJS_JQUERY', true);


$output = new bfCombine();


class bfCombine {

	var $cache_enabled 	= true;
	var $debug 			= false;
	var $allowedFiles 	= array();
	var $_lastmodified 	= 0;
	var $_hash			= null;
	var $_filesstring 	= null;
	var $_encoding		= null;

	var $files 			= array();
	var $component_shortname = null;
	var $component 		= null;
	var $type		 	= null;
	var $cachedir 		= null;

	function __construct(){
		$this->bfCombine();
	}

	function bfCombine(){
		if ($this->debug === true){
			$this->debugOn();
		}

		$this->_setDefines();
		$this->_parseUrl();
		$this->_setAllowedFiles();
		$this->_setCacheFolder();
		$this->_getLastModified();
		$this->_setHash();
		$this->_sendHashHeader();
		$this->_checkETag();
		$this->_setEncoding();

		// Try the cache first to see if the combined files were already generated
		$this->cachefile = 'bfCache-' . $this->_lastmodified . $this->component . '.' . $this->_filesstring . '.'  . $this->type  . ($this->_encoding != 'none' ? '.' . $this->_encoding : '');

		if ($this->cache_enabled===true){
			$this->_deliverCachedFile();
		} else {
			$contents = $this->_createCacheFile();
			$this->_deliverCachedFile($contents);
		}
	}

	function debugOn(){
		ini_set('display_errors','On');
		error_reporting(E_ALL);
	}

	function _setAllowedFiles(){
		$this->allowedFiles = array(
		'mootools'=>	DS . _PLUGIN_DIR  . DS . 'system'	. DS . 'blueflame'	. DS . 'libs'	. DS . 'mootools'. DS . 'mootools.v1.1.js',
		'bfadmin_js'=>	DS . _PLUGIN_DIR  . DS . 'system'	. DS . 'blueflame'	. DS . 'view'	. DS . 'admin.js',
		'bffront_js'=>	DS . _PLUGIN_DIR  . DS . 'system'	. DS . 'blueflame'	. DS . 'view'	. DS . 'front.js',
		'front_js'=>	DS . 'components' . DS . $this->component	. DS . 'view'		. DS . 'front'	. DS . 'front.js',
		'front_css'=>	DS . 'components' . DS . $this->component	. DS . 'view'		. DS . 'front'	. DS . 'front.css',
		'admin_css'=>	DS . 'components' . DS . $this->component	. DS . 'view'		. DS . 'admin'	. DS . 'admin.css',
		'admin_js'=>	DS . 'components' . DS . $this->component	. DS . 'view'		. DS . 'admin'	. DS . 'admin.js',
		'bffront_css'=>	DS . _PLUGIN_DIR  . DS . 'system'	. DS . 'blueflame'	. DS . 'view'	. DS . 'front.css',
		'bfadmin_css'=>	DS . _PLUGIN_DIR  . DS . 'system'	. DS . 'blueflame'	. DS . 'view'	. DS . 'admin.css',
		'jquery'=>		DS . _PLUGIN_DIR  . DS . 'system'	. DS . 'blueflame'	. DS . 'libs'	. DS . 'jquery'. DS . 'jquery.js',
		'jquery.tabs'=>	DS . _PLUGIN_DIR   . DS . 'system'	. DS . 'blueflame'	. DS . 'libs'	. DS . 'jquery'. DS . 'jquery.tabs.pack.js',
		'jquery.thickbox_js'=>		DS . _PLUGIN_DIR . DS . 'system'. DS . 'blueflame'. DS . 'libs'. DS . 'jquery'. DS . 'jquery.thickbox.js',
		'jquery.thickbox_css'=>		DS . _PLUGIN_DIR . DS . 'system'. DS . 'blueflame'. DS . 'libs'. DS . 'jquery'. DS . 'jquery.thickbox.css',
		'jquery.reflection_js'=>		DS . _PLUGIN_DIR . DS . 'system'. DS . 'blueflame'. DS . 'libs'. DS . 'jquery'. DS . 'jquery.reflection.js',
		'dimensions'=>		DS . _PLUGIN_DIR . DS . 'system'. DS . 'blueflame'. DS . 'libs'. DS . 'treeview'. DS . 'dimensions.js',
		'splitter'=>		DS . _PLUGIN_DIR . DS . 'system'. DS . 'blueflame'. DS . 'libs'. DS . 'treeview'. DS . 'splitter.js'
		);
	}

	function _setCacheFolder(){
		$this->cachedir  = __ROOT__ . DS ._PLUGIN_DIR . DS . 'system' . DS . 'blueflame' . DS . 'cache';
	}

	function _checkETag(){
		if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && stripslashes($_SERVER['HTTP_IF_NONE_MATCH']) == '"' . $this->_hash . '"' && $this->debug == false){
			/* Return visit and no modifications, so do not send anything */
			header ("HTTP/1.0 304 Not Modified");
			header ('Content-Length: 0');
			die;
		}
	}

	function _cleanCache($str){
		$files = $this->_readDirectory($this->cachedir,'.*'.$str.'.*',false,true);
		foreach ($files as $file){
			@unlink($file);
		}
	}

	function _compressCSS($buffer) {
		// remove comments
		$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
		// remove tabs, spaces, newlines, etc.
		$buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
		return trim($buffer);
	}


	function _isAllowed($element){
		if (!array_key_exists($element, $this->allowedFiles)){
			die('alert(\'bfCombine: ' . $element .' not authorised\');');
		}

		$path = realpath(__ROOT__ . $this->allowedFiles[$element]);
		if (($type == 'js' && substr($path, -3) != '.js') || ($type == 'css' && substr($path, -4) != '.css')) {
			header ("HTTP/1.0 403 Forbidden");
			echo 'Cant find: ' . __ROOT__ . $this->allowedFiles[$element];
			exit;
		}

		/* important check for ONLY our known files */
		if (substr($path, 0, strlen(__ROOT__)) != __ROOT__ || !file_exists($path)) {
			header ("HTTP/1.0 404 Not Found");
			echo 'cant find element '. $element . ' at path ' . $path . '<br />';
			exit;
		}
	}

	function _clean($str){
		return str_replace(array('\\', '/', '.'),'',$str);
	}

	function _createCacheFile(){

		/* clean old files */
		$this->_cleanCache($this->component . '.' . $this->_filesstring . '.'  . $this->type);

		// Get contents of the files
		$contents = "/* bfCache " . date('H:m:s') . " */" ;
		foreach ($this->files as $element){
			if (!array_key_exists($element['name'], $this->allowedFiles)) continue;
			$path = realpath(__ROOT__ . $this->allowedFiles[$element['name']]);
			if ($this->type=='css'){
				$str = $this->_compressCSS(file_get_contents($path));
			} else {
				$str = file_get_contents($path);
			}
			$contents .= "\n\n" . $str;
		}

		if (array_key_exists('jquery',$this->files)){
			$contents .= "\n" . 'jQuery.noConflict();';
		}

		// Send Content-Type
		if ($this->type=='js') {
			$t = 'javascript';
		} else {
			$t = 'css';
		}
		header ("Content-Type: text/" . $t);

		if ($this->cache_enabled===true){
			if ($fp = fopen($this->cachedir . DS . $this->cachefile, 'wb')) {
				fwrite($fp, $contents);
				fclose($fp);
			}
		} else {
			return $contents;
		}
	}

	function _deliverCachedFile($contents = null){

		if (!file_exists($this->cachedir . DS . $this->cachefile) && $contents===null) {
			$this->_createCacheFile();
		}

		if ($contents===null){
			if ($fp = fopen($this->cachedir . DS . $this->cachefile, 'rb') ) {
				$contents = fread($fp, filesize($this->cachedir . DS . $this->cachefile));
			}
		}

		if ($contents===null) die('No contents;');
		if ($this->type=='js') {
			$t = 'javascript';
		} else {
			$t = 'css';
		}
		header ("Content-Type: text/" . $t);

		if (isset($this->_encoding) && $this->_encoding != 'none'){
			// Send compressed contents
			$contents = gzencode($contents, 9, $this->_gzip ? FORCE_GZIP : FORCE_DEFLATE);
			header ("Content-Encoding: " . $this->_encoding);
			header ('Content-Length: ' . strlen($contents));

		} else {
			// Send regular contents
			header ('Content-Length: ' . strlen($contents));

		}
		echo $contents;
		exit;

	}

	function _getLastModified(){
		foreach ($this->files as $file){
			if (isset($this->allowedFiles[$file['name']])){
				$path = realpath(__ROOT__ . $this->allowedFiles[$file['name']]);
				$this->_lastmodified = max($this->_lastmodified, filemtime($path));
			}
		}
	}
	function _makeCompatible(){

		/* dont use jquery if compat says so */
		if (bfJS_JQUERY===false){
			unset($this->allowedFiles['jquery']);
			unset($this->allowedFiles['jquery.tabs']);
		}

		/* dont use mootools if compat says so */
		if (bfJS_MOOTOOLS===false){
			unset($this->allowedFiles['mootools']);
		}
	}

	function _setDefines(){

		if (file_exists('../../../libraries/joomla/config.php')){
			define('_PLUGIN_DIR', 'plugins');
		} else {
			define('_PLUGIN_DIR', 'mambots');
		}
		define('DS',DIRECTORY_SEPARATOR);

		$root = str_replace( DS . _PLUGIN_DIR . DS . 'system' . DS . 'blueflame', '', dirname(__FILE__));

		define('__ROOT__', $root);
	}

	function _setEncoding(){
		// Determine supported compression method
		$this->_gzip = strstr(@$_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip');
		$this->_deflate = strstr(@$_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate');

		// Determine used compression method
		$this->_encoding = $this->_gzip ? 'gzip' : ($this->_deflate ? 'deflate' : 'none');

		// Check for buggy versions of Internet Explorer
		if (!strstr($_SERVER['HTTP_USER_AGENT'], 'Opera') &&
		preg_match('/^Mozilla\/4\.0 \(compatible; MSIE ([0-9]\.[0-9])/i', $_SERVER['HTTP_USER_AGENT'], $matches)) {
			$version = floatval($matches[1]);

			if ($version < 6)
			$this->_encoding = 'none';

			if ($version == 6 && !strstr($_SERVER['HTTP_USER_AGENT'], 'EV1'))
			$this->_encoding = 'none';
		}
	}

	function _setHash(){
		$this->_hash = $this->_lastmodified . '-' . md5($this->_filesstring);
	}

	function _sendHashHeader(){
		header ("Etag: \"" . $this->_hash . "\"");
	}

	function _readDirectory( $path, $filter='.', $recurse=false, $fullpath=false  ) {
		$arr = array();
		if (!@is_dir( $path )) {
			return $arr;
		}
		$handle = opendir( $path );

		while ($file = readdir($handle)) {
			$dir = $this->_PathName( $path.'/'.$file, false );
			$isDir = is_dir( $dir );
			if (($file != ".") && ($file != "..")) {
				if (preg_match( "/$filter/", $file )) {
					if ($fullpath) {
						$arr[] = trim( $this->_PathName( $path.'/'.$file, false ) );
					} else {
						$arr[] = trim( $file );
					}
				}
				if ($recurse && $isDir) {
					$arr2 = $this->_readDirectory( $dir, $filter, $recurse, $fullpath );
					$arr = array_merge( $arr, $arr2 );
				}
			}
		}
		closedir($handle);
		asort($arr);
		return $arr;
	}

	function _PathName($p_path,$p_addtrailingslash = true) {
		$retval = "";

		$isWin = (substr(PHP_OS, 0, 3) == 'WIN');

		if ($isWin)	{
			$retval = str_replace( '/', '\\', $p_path );
			if ($p_addtrailingslash) {
				if (substr( $retval, -1 ) != '\\') {
					$retval .= '\\';
				}
			}

			// Check if UNC path
			$unc = substr($retval,0,2) == '\\\\' ? 1 : 0;

			// Remove double \\
			$retval = str_replace( '\\\\', '\\', $retval );

			// If UNC path, we have to add one \ in front or everything breaks!
			if ( $unc == 1 ) {
				$retval = '\\'.$retval;
			}
		} else {
			$retval = str_replace( '\\', '/', $p_path );
			if ($p_addtrailingslash) {
				if (substr( $retval, -1 ) != '/') {
					$retval .= '/';
				}
			}

			// Check if UNC path
			$unc = substr($retval,0,2) == '//' ? 1 : 0;

			// Remove double //
			$retval = str_replace('//','/',$retval);

			// If UNC path, we have to add one / in front or everything breaks!
			if ( $unc == 1 ) {
				$retval = '/'.$retval;
			}
		}

		return $retval;
	}
	function _parseUrl(){
		if (@!$_GET['type']) {
			die('alert(\'bfCombine: Type Name not set\');');
		}



		if (@!$_GET['c'] || @!$_GET['f']){
			/* try exploding parts for css */
			$parts = explode('/',$_GET['type']);
			if (@!$parts[2]) die('no parts');
			switch ($parts[0]){
				case 'css':
					$this->type = 'css';
					break;
				case 'js':
					$this->type = 'js';
					break;
				default:
					die('invalid type');
					break;
			}

			switch ($this->type) {
				case 'css':
				case 'js':
					break;
				default:
					header ("HTTP/1.0 503 Not Implemented");
					echo "HTTP/1.0 503 Not Implemented";
					exit;
			}

			if (!$parts[1]) die('No component set');
			$this->component_shortname = $parts[1];
			$this->_filesstring = str_replace(',','.',$parts[2]);
			if (!is_array($parts[2])) $parts[2] = explode(',', $parts[2]);
			foreach ($parts[2] as $f){
				$this->files[$f]['name'] = $f;
			}

			$this->component 			= 'com_' . $this->_clean($parts[1]);
		} else {
			$this->type 				= $_GET['type'];
			$this->component_shortname 	= $_GET['c'];
			$this->component 			= 'com_' . $this->_clean(@$_GET['c']);
			$fs = explode(',',$_GET['f'] );
			$this->_filesstring = str_replace(',','.',$_GET['f']);
			foreach ($fs as $f){
				$this->files[$f]['name'] = $f;
			}
		}
	}
}
?>