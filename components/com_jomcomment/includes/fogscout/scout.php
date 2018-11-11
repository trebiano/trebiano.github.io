<?php

if(!defined('FOG_BUGZ_SCOUT')){

define('FOG_BUGZ_SCOUT', 1);

class FogBugScout
{
	# Return the componen version
	function _getVersion($comName){

	  	global $mosConfig_absolute_path;
	  	require_once( $mosConfig_absolute_path . '/includes/domit/xml_domit_lite_include.php' );
	  	
		# Read the file to see if it's a valid component XML file
		$xmlDoc = new DOMIT_Lite_Document();
		$xmlDoc->resolveErrors( true );
	
		if (!$xmlDoc->loadXML( $mosConfig_absolute_path . "/administrator/components/com_$comName/$comName.xml", false, true )) {
			continue;
		}
	
		$root = &$xmlDoc->documentElement;
	
		if ($root->getTagName() != 'mosinstall') {
			continue;
		}
		if ($root->getAttribute( "type" ) != "component") {
			continue;
		}
	
		$element 	= &$root->getElementsByPath('version', 1);
		$version 	= $element ? $element->getText() : '';
	
		return $version;
	}
	
	# Return the componen version
	function _getProjectName($comName){
	  	global $mosConfig_absolute_path;
	  	require_once( $mosConfig_absolute_path . '/includes/domit/xml_domit_lite_include.php' );
	  	
		# Read the file to see if it's a valid component XML file
		$xmlDoc = new DOMIT_Lite_Document();
		$xmlDoc->resolveErrors( true );
	
		if (!$xmlDoc->loadXML( $mosConfig_absolute_path . "/administrator/components/com_$comName/$comName.xml", false, true )) {
			continue;
		}
	
		$root = &$xmlDoc->documentElement;
	
		if ($root->getTagName() != 'mosinstall') {
			continue;
		}
		if ($root->getAttribute( "type" ) != "component") {
			continue;
		}
	
		$element 			= &$root->getElementsByPath('scoutProject', 1);
		$version 		= $element ? $element->getText() : '';
	
		return $version;
	}

	
	# Post request to remote server
	function _post($host, $query, $others = '') {
		if(function_exists('curl_init')){
			$ch = @curl_init();
			@curl_setopt ($ch, CURLOPT_URL, "http://" .$host . "?". $query);
			@curl_setopt ($ch, CURLOPT_HEADER, 0);
			@ob_start();
			@curl_exec ($ch);
			@curl_close ($ch);
			$string = @ob_get_contents();
			@ob_end_clean();
			return $string;
		}
		
		/*
		if(ini_get('allow_url_fopen') == 1){
			
			$dh = fopen("http://". $host . "?". $query,'r');
			$result = fread($dh,8192);                                                                                                                   
			return $result;
		}
		
		$path = explode('/', $host);
		$host = $path[0];
		$r = "";
		unset ($path[0]);
		$path = '/' . (implode('/', $path));
		$post = "POST $path HTTP/1.0\r\nHost: $host\r\nContent-type: application/x-www-form-urlencoded\r\n${others}User-Agent: Mozilla 4.0\r\nContent-length: " . strlen($query) . "\r\nConnection: close\r\n\r\n$query";
		$h = fsockopen($host, 80, $errno, $errstr, 7);
		if ($h) {
			fwrite($h, $post);
			for ($a = 0, $r = ''; !$a;) {
				$b = fread($h, 8192);
				$r .= $b;
				$a = (($b == '') ? 1 : 0);
			}
			fclose($h);
		}
		return $r;
		*/
	}

	# Submit the bug
	# Example use: 	FogBugScout::submitBug('myblog', 'A ug description');
	function submitBug($comName, $description, $more=""){
		global $mosConfig_live_site, $mosConfig_mailfrom, $_VERSION;
		
		$extra = "$mosConfig_live_site \nPHP: ". phpversion();
		if(function_exists('mysql_get_server_info'))
			$extra .= "\nMySql : " . mysql_get_server_info();
			
		# Joomla Version 
		if(isset($_VERSION))	
			$extra .= "\nJoomla Version: " . $_VERSION->getShortVersion();
			
		# Jom Comment version
		$extra .= "\nVersion: " . FogBugScout::_getVersion($comName);	
		
		# More info
		if(!empty($more))
			$extra .= "\nMore: " . $more;	 
		
		$buginfo['ScoutUserName'] 	= "Azrul";
		$buginfo['ScoutProject']	= FogBugScout::_getProjectName($comName);
		$buginfo['ScoutArea']		= "Scout";
		$buginfo['Description']		= $description;
		$buginfo['Extra']			= $extra;
		$buginfo['Email']			= $mosConfig_mailfrom;
		$buginfo['ScoutDefaultMessage'] 	= "";
		$buginfo['FriendlyResponse']		= "1";
		$buginfo['ForceNewBug']				= "0";
		
		$query = "";
		foreach($buginfo as $key=> $val){
			$query .= "$key=" . urlencode($val) . "&";
		}
		echo FogBugScout::_post('azrul.selfip.org/fogbugz/scoutSubmit.php', $query);
	}
	
}

}
