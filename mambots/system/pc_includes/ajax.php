<?php

defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

global $jaxFuncNames;
$jaxFuncNames  = array();

if(!class_exists('JAXResponse')){

class JAXResponse
{
	
	var $_response = null;
	
	function JAXResponse(){
		$this->_response = array();
	}
	
	function object_to_array($obj) {
       $_arr = is_object($obj) ? get_object_vars($obj) : $obj;
       $arr = array();
       foreach ($_arr as $key => $val) {
               $val = (is_array($val) || is_object($val)) ? $this->object_to_array($val) : $val;
               $arr[$key] = $val;
       }
       return $arr;
	}
	
	/**
	 * Assign new sData to the $sTarget's $sAttribute property
	 */	 	
	function addAssign($sTarget,$sAttribute,$sData){
		//$sData = $this->_hackString($sData);
		//$sData = preg_replace("((\r\n)+)", '', $sData);
		$this->_response[] = array('as', $sTarget, $sAttribute, $sData);
	}
	
	/**
	 * Clear the given target property
	 */	 	
	function addClear($sTarget,$sAttribute){
		$this->_response[] = array('as', $sTarget, $sAttribute, "");
	}
	
	function addCreate($sParent, $sTag, $sId, $sType=""){
		$this->_response[] = array('ce', $sParent, $sTag, $sId);
	}
	
	function addRemove($sTarget){
		$this->_response[] = array('rm', $sTarget);
	}
	
	/**
	 * Assign new sData to the $sTarget's $sAttribute property
	 */	 	
	function addAlert($sData){
		$this->_response[] = array('al', "", "", $sData);
	}
	
	function _hackString($str){
		# Convert '{' and '}' to 0x7B and 0x7D
	    //$str = str_replace(array('{', '}'), array('&#123;', '&#125;'), $str);	   
		return $str;
	}
	
	/**
	 * Add a script call
	 */	 	
	function addScriptCall($func){
		$size = func_num_args();
		$response = "";

		if($size > 1){
			$response = array();
		
			for ($i = 1; $i < $size; $i++) {
				$arg = func_get_arg($i);
				$response[] = $arg;
			}
		}
		
		
		$this->_response[] = array('cs', $func, "", $response);
	}
	
	function encodeString($contents){
	    $ascii = '';
	    $strlen_var = strlen($contents);
	
	   /*
	    * Iterate over every character in the string,
	    * escaping with a slash or encoding to UTF-8 where necessary
	    */
	    for ($c = 0; $c < $strlen_var; ++$c) {
	
	        $ord_var_c = ord($contents{$c});
	
	        switch ($ord_var_c) {
	            case 0x08:  $ascii .= '\b';  break;
	            case 0x09:  $ascii .= '\t';  break;
	            case 0x0A:  $ascii .= '\n';  break;
	            case 0x0C:  $ascii .= '\f';  break;
	            case 0x0D:  $ascii .= '\r';  break;

	            default:
	                $ascii .= $contents{$c};
	          }
	    } 
	    
	    
	    return $ascii;
	    
	    //return $this->_hackString($ascii);
	}
	
	/**
	 * Flush the output back
	 */	 	
	function sendResponse(){
		global $mosConfig_absolute_path, $mosConfig_lang;
		
		$ob_active = ob_get_length () !== FALSE;
		if($ob_active){
			while (@ ob_end_clean());
			if(function_exists('ob_clean')){
				@ob_clean();
			}
		}
		ob_start();
	
		// Send text/html if we're using iframe
		if(isset($_GET['func'])){

			// loads english language file by default
			if ($mosConfig_lang=='') {
				$mosConfig_lang = 'english';
			}
			include_once( $mosConfig_absolute_path .'/language/' . $mosConfig_lang . '.php' );

			$iso = explode( '=', _ISO );
			header("Content-type: text/html; $iso");
		}else
			header('Content-type: text/plain');
		
		if(!defined('SERVICES_JSON_SLICE'))
			include_once($mosConfig_absolute_path . '/mambots/system/pc_includes/JSON.php');
		
		$json = new Services_JSON();
		
		# Encode '{' and '}' characters
				
		# convert a complex value to JSON notation
		$output = $json->encode($this->_response);
		
		if(isset($_GET['func']))
			$output = "<body onload=\"parent.jax_iresponse();\">" . htmlentities($output). "</body>";
		echo(($output));
		exit;
	}
}

class JAX
{
	var $_html = "";
	var $_funct = null;
	var $_path = "";
	var $livePath = "";
	var $_reqURL = "";
	var $_param = null;
	
	function JAX($livepath, $param= null){
		$this->_funct = array();
		$this->_path  = dirname(__FILE__);
		$this->_livePath = $livepath;
		$this->_param = $param;
		
	}
	
	function registerFunction($mFunction){
		$this->_funct[] = $mFunction;
	}
	
	function addAssign(){
	}
	
	function setReqURI($reqURL){
		$this->_reqURL = $this->_fixHTTPS($reqURL);
	}
	
	function _fixHTTPS($content){
		global $mosConfig_live_site;
		
		$reqURI   = $mosConfig_live_site;
	
		# if host have wwww, but mosConfig doesn't
		if((substr_count($_SERVER['HTTP_HOST'], "www.") != 0) && (substr_count($reqURI, "www.") == 0)) {
			$reqURI = str_replace("://", "://www.", $reqURI);
				
		} else if((substr_count($_SERVER['HTTP_HOST'], "www.") == 0) && (substr_count($reqURI, "www.") != 0)) {
			// host do not have 'www' but mosConfig does
			$reqURI = str_replace("www.", "", $reqURI);
		}
	
		/* Check for HTTPS */
		if(isset($HTTP_SERVER_VARS)){
			if(isset($HTTP_SERVER_VARS['HTTPS'])){
				if($HTTP_SERVER_VARS['HTTPS'] == "ON" ){
					$reqURI = str_replace("http://", "https://", $reqURI);
				}
			}
		}
	
		return str_replace($mosConfig_live_site, $reqURI, $content);
	}
	
	/**
	 * Return the script thatshould be added to the <head> section
	 */	 	
	function getScript(){
		global $mosConfig_gzip;
		
		$reqURI = $this->_reqURL;
		$compress	= $mosConfig_gzip;
		
		//if($this->_param)
		//	@$compress	= $this->_param->def( 'compress', 1 );

		# if host have wwww, but mosConfig doesn't
		//if (substr($_SERVER['HTTP_HOST'], 0, 4) == "www.") {
		if((substr_count(@$_SERVER['HTTP_HOST'], "www.") != 0) && (substr_count($reqURI, "www.") == 0)) {
			$reqURI = str_replace("://", "://www.", $reqURI);
				
		} else if((substr_count(@$_SERVER['HTTP_HOST'], "www.") == 0) && (substr_count($reqURI, "www.") != 0)) {
			// host do not have 'www' but mosConfig does
			$reqURI = str_replace("www.", "", $reqURI);
		}
	
		/* Check for HTTPS */
		if(isset($HTTP_SERVER_VARS)){
			if(isset($HTTP_SERVER_VARS['HTTPS'])){
				if($HTTP_SERVER_VARS['HTTPS'] == "ON" ){
					$reqURI = str_replace("http://", "https://", $reqURI);
				}
			}
		}
		
	
		$html = 
"<script type='text/javascript'>
/*<![CDATA[*/
	var jax_live_site = '$reqURI';
/*]]>*/
</script>";
		$html .= "<script type=\"text/javascript\" src=\"$this->_livePath/ajax.js\"></script>\n";
		return $this->_fixHTTPS($html);
	}
	
	function nl2brStrict($text) {
		return preg_replace("/\r\n|\n|\r/", " <br />", $text);
	}
	
	function br2nl($text){
		$text = str_replace(' <br />', "\n", $text);
		return $this->_fixQuote($text);	
	}
	
	function _fixQuote($text){
		return str_replace('&quot;', '"', $text);
	}

	function singleLineIt($text){
		return preg_replace("((\r\n)+)", '', $text);
	}
		
	/**
	 *
	 */	 	
	function process(){
		global $mosConfig_absolute_path;
		if(!defined('SERVICES_JSON_SLICE'))
			include_once($mosConfig_absolute_path . '/mambots/system/pc_includes/JSON.php');
			
		$json = new Services_JSON();
		global $my, $mainframe;
		
		if(@isset($_REQUEST['task']) && ($_REQUEST['task'] == 'azrul_ajax')){
			
			if(!isset($my))
				$my = $mainframe->getUser();
			
			$func = @$_REQUEST['func'];
			
			// Security fix.
			// 1. check if user are trying to run an eval
			
			# build an array of args
			$args = array();
			$argCount = 0;
			
			# All POST data that are meant to be send to the function will
			# be appended by 'arg' keyword. Only pass this vars to the function								 				
			foreach($_REQUEST as $key => $postData){
				if(substr($key, 0, 3) == 'arg' ){
					//if ( get_magic_quotes_gpc() ) {
						$postData = stripslashes($postData);
					//}


					$postData = ($this->nl2brStrict($postData));					
					$decoded = $json->decode($postData);
					$key = "";
					$val = "";
					
					# if the args is an array, we need to pass it as an array
					# todo@ we need to expand this array further. We now assume,
					# if an array is passed, it comes in a pair of (key/value)												
					if(is_array($decoded)){
						foreach($decoded as $d){
							if(is_array($d)){
								$key = $d[0];
								$val = $this->br2nl(urldecode($d[1]));
								
								if(substr($d[0], 0, 6) == '_d_' ){
									$decoded = array($val);
								} else {
									$newA = array($key => $val);
									$decoded = array_merge($decoded, $newA);
								}
							} else {
								if($d != '_d_' ){
									$decoded = $this->br2nl(urldecode($d));
								}
							}
						}
						$args[] = $decoded;
					} else {
						$args[] = $this->br2nl(urldecode($decoded));
					}
					$argCount++;
				} 
			}
			
			# Include the main component file
			$comName = $_REQUEST['option'];
			ob_start();
			
			global $jaxFuncNames;
			
			
			// This is a really silly test!
			if(strpos($this->_reqURL, 'hidemainmenu=1')){
				include_once($mosConfig_absolute_path . "/administrator/components/com_$comName/$comName.php");
				// Test, and make sure $my object is valid
				if(!$my->id){
					echo "Invalid access";
					exit;
				}
			}
			else {
				include_once($mosConfig_absolute_path . "/components/com_$comName/$comName.php");
			}

			@ob_end_clean();
			
			@include_once($mosConfig_absolute_path . "/components/com_$comName/jax.$comName.php");
			// check and make sure the fucntion name is actually registered
			if(!in_array($func, $jaxFuncNames)){
				echo 'invalid function calls';
				exit;
			}
			
			$response = call_user_func_array($func, $args);
			//header('Content-type: text/plain');
			//echo $response;
			//exit;
		}
	}
	
}

}
