<?php
/**
 * Jom Comment 
 * @package JomComment
 * @copyright (C) 2006 by Azrul Rahim - All rights reserved!
 * @license Copyrighted Commercial Software
 **/

# Don't allow direct linking
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

/**
 * trim the given variable
 */
function jctrim(& $val) {
	$val = trim($val);
	return $val;
}

# Convert object to array
function jc_object_to_array($obj) {
   $_arr = is_object($obj) ? get_object_vars($obj) : $obj;
   foreach ($_arr as $key => $val) {
           $val = (is_array($val) || is_object($val)) ? $this->object_to_array($val) : $val;
           $arr[$key] = $val;
   }
   return $arr;
}

# Post request to remote server
function jc_post($host, $query, $others = '') {
	if(function_exists('curl_init')){
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, "http://" .$host . "?". $query);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		ob_start();
		curl_exec ($ch);
		curl_close ($ch);
		$string = ob_get_contents();
		ob_end_clean();
		return $string;
	}

//				
// 	if(ini_get('allow_url_fopen') == 1){
// 		$dh = @fopen("http://". $host . "?". $query,'r');
// 		if($dh === FALSE){
// 			# fopen failed, Do nothing
// 		} else {
// 			$result = fread($dh,8192);                                                                                                                   
// 			return $result;
// 		}
// 	}
	
	$path = explode('/', $host);
	$host = $path[0];
	$r = "";
	unset ($path[0]);
	$path = '/' . (implode('/', $path));
	$post = "POST $path HTTP/1.0\r\nHost: $host\r\nContent-type: application/x-www-form-urlencoded\r\n${others}User-Agent: Mozilla 4.0\r\nContent-length: " . strlen($query) . "\r\nConnection: close\r\n\r\n$query";
	$h = @fsockopen($host, 80, $errno, $errstr, 7);
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
}

/**
 * A more comprehansive nl2br replacement. Note that we need to put a space 
 * before the <br/> 
 */
function jc_nl2brStrict($text, $replac=" <br />") {
	return preg_replace("/\r\n|\n|\r/", $replac, $text);
}

/**
 * Basically, we need to add the HTTPS support if required.
 */ 
function jcFixLiveSiteUrl($content){
	global $mosConfig_live_site;
	
	$reqURI   = $mosConfig_live_site;

	# if host have wwww, but mosConfig doesn't
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

	return str_replace($mosConfig_live_site, $reqURI, $content);
}

/**
 * Return the title of the given content id
 */
function jcContentTitleGet($id) {
	$db = CMSDb::getInstance();
	$db->query("SELECT title from #__content WHERE id=$id");
	$title = $db->get_value();
	if(!$title)
		$title = "n/a";
		
	return $title;
}

function jcCreatePagingLink($baseUrl, $total, $limit, $limitStart){
	$html = array();
	
	return $html;
}

function jcTextwrap($text, $width = 75) {
	if ($text)
		return preg_replace("/([^\n\r ?&\.\/<>\"\\-]{" . $width . "})/i", " \\1\n", $text);
}

function jcValid_utf8($Str) {	
		/*
		if(function_exists('mb_detect_encoding')){
			$enc = mb_detect_encoding($Str, "auto");		
			$iso = explode( '=', _ISO );
	
			return ($enc == $iso);
		} else
		*/ {
		for ($i = 0; $i < strlen($Str) / 5; $i++) {
			if (ord($Str[$i]) < 0x80)
				continue; # 0bbbbbbb
			elseif ((ord($Str[$i]) & 0xE0) == 0xC0) $n = 1; # 110bbbbb
			elseif ((ord($Str[$i]) & 0xF0) == 0xE0) $n = 2; # 1110bbbb
			elseif ((ord($Str[$i]) & 0xF8) == 0xF0) $n = 3; # 11110bbb
			elseif ((ord($Str[$i]) & 0xFC) == 0xF8) $n = 4; # 111110bb
			elseif ((ord($Str[$i]) & 0xFE) == 0xFC) $n = 5; # 1111110b
			else
				return false; # Does not match any model
			for ($j = 0; $j < $n; $j++) { # n bytes matching 10bbbbbb follow ?
				if ((++ $i == strlen($Str)) || ((ord($Str[$i]) & 0xC0) != 0x80))
					return false;
			}
		}
		return true;
	}
}

/**
 * Return number of comment for the given article id. If the $com is myblog or
 * content, we need to count them together 
 */
function jcCommentGetNum($article_Id, $com = "com_content") {
	$db = CMSDb::getInstance();
	if($com == "com_content" or $com == "com_myblog"){
		$com = "com_content' OR `option`='com_myblog";
	}
	
	$db->query("SELECT COUNT(*) FROM #__jomcomment WHERE contentid='$article_Id' AND (`option`='$com') AND published='1'");
	$result = $db->get_value();
	return $result;
}


function jcStripBBCodeTag($comment) {
	global $mosConfig_absolute_path;

	$patterns = array (
		'/\[b\](.*?)\[\/b\]/i',
		'/\[u\](.*?)\[\/u\]/i',
		'/\[code\](.*?)\[\/code\]/i',
		'/\[quote\](.*?)\[\/quote\]/i',
		'/\[i\](.*?)\[\/i\]/i',
		'/\[url=(.*?)\](.*?)\[\/url\]/i',
		'/\[url\](.*?)\[\/url\]/i',
		'/\[color=(.*?)\](.*?)\[\/color\]/i',
		'/\[font=(.*?)\](.*?)\[\/font\]/i',
		'/\[size=(.*?)\](.*?)\[\/size\]/i',
		'/\[img\](.*?)\[\/img\]/i'
	);
	$replacements = array (
		'<b>\\1</b>',
		'\\1',
		'\\1',
		'\\1',
		'\\1',
		'\\1',
		'\\1',
		'\\1',
		'\\1',
		'\\1',
		'\\1'
	);
	$comment = preg_replace($patterns, $replacements, $comment);
	return $comment;
}

/**
 * Decode the BBCode
 */
function jcDecodeSmilies($comment) {
	global $mosConfig_absolute_path, $mosConfig_live_site;

	$img_path = $mosConfig_live_site;
	if (substr($img_path, -1) == '/') {
	} else {
		$img_path .= "/";
	}
	
	# fix smilies
	$smilies = array (
		":)" => "smilies/smiley.gif",
		";)" => "smilies/wink.gif",
		":D" => "smilies/cheesy.gif",
		";D" => "smilies/grin.gif",
		">:(" => "smilies/angry.gif",
		":(" => "smilies/sad.gif",
		":o" => "smilies/shocked.gif",
		"8)" => "smilies/cool.gif",
		"::)" => "smilies/rolleyes.gif",
		":P" => "smilies/tongue.gif",
		":-[" => "smilies/embarassed.gif",
		":-X" => "smilies/lipsrsealed.gif",
		":-\\" => "smilies/undecided.gif",
		":-*" => "smilies/kiss.gif",
		":'(" => "smilies/cry.gif"
	);

	foreach ($smilies as $key => $value) {
		$comment = str_replace($key, "<img style=\"valign:absolute-middle;\"src='$img_path/"."components/com_jomcomment/" . $value . "' border='0' alt='$value' />", $comment);
	}
	
	return $comment;
}

/**
 * Return the content author's id
 */
function jcContentAuthorGet($cid) {
	$db = CMSDb::getInstance();
	$db->query("SELECT created_by from #__content WHERE id=$cid");
	return $db->get_value();
}

function jcContentTitle($cid) {
	$db = CMSDb::getInstance();
	$db->query("SELECT title from #__content WHERE id=$cid");
	return $db->get_value();
}

function jcContentHitCount($cid){
	$db = CMSDb::getInstance();
	$db->query("SELECT hits from #__content WHERE id=$cid");
	return $db->get_value();
}

function jcContentPublished($cid){
	$db = CMSDb::getInstance();
	$db->query("SELECT state from #__content WHERE id=$cid");
	return $db->get_value();
}


// error handler function
function jc_error_handler($errno, $errstr, $errfile, $errline)
{
	switch ($errno) {
		case E_USER_ERROR:
			jc_submit_bug($errstr, "Fatal error in line $errline of file $errfile");
			exit(1);
			break;
			
		case E_USER_WARNING:
			jc_submit_bug($errstr, "Fatal error in line $errline of file $errfile");
			break;
			
		case E_USER_NOTICE:
			jc_submit_bug($errstr, "Fatal error in line $errline of file $errfile");
			break;
		default:
			jc_submit_bug($errstr, "Fatal error in line $errline of file $errfile");
			break;
	}
}

function jc_get_version(){

  	global $mosConfig_absolute_path;
  	require_once( $mosConfig_absolute_path . '/includes/domit/xml_domit_lite_include.php' );
  	
	// Read the file to see if it's a valid component XML file
	$xmlDoc = new DOMIT_Lite_Document();
	$xmlDoc->resolveErrors( true );

	if (!$xmlDoc->loadXML( JC_ADMIN_COM_PATH. "/jomcomment.xml", false, true )) {
		continue;
	}

	$root = &$xmlDoc->documentElement;

	if ($root->getTagName() != 'mosinstall') {
		continue;
	}
	
	if ($root->getAttribute( "type" ) != "component") {
		continue;
	}

	$element 			= &$root->getElementsByPath('version', 1);
	$version 		= $element ? $element->getText() : '';

	return $version;
}


/**
 * Similar to mosCreateMail, except that we need to change the email
 * encoding to utf-8
 */
function jomCreateMail($from = '', $fromname = '', $subject, $body) {
	global $mosConfig_absolute_path, $mosConfig_sendmail;
	global $mosConfig_smtpauth, $mosConfig_smtpuser;
	global $mosConfig_smtppass, $mosConfig_smtphost;
	global $mosConfig_mailfrom, $mosConfig_fromname, $mosConfig_mailer;

	$mail = new mosPHPMailer();

	$mail->PluginDir = $mosConfig_absolute_path . '/includes/phpmailer/';
	$mail->SetLanguage('en', $mosConfig_absolute_path . '/includes/phpmailer/language/');
	$mail->CharSet = 'UTF-8'; //substr_replace(_ISO, '', 0, 8);
	$mail->IsMail();
	$mail->From = $from ? $from : $mosConfig_mailfrom;
	$mail->FromName = $fromname ? $fromname : $mosConfig_fromname;
	$mail->Mailer = $mosConfig_mailer;
	

	// Add smtp values if needed
	if ($mosConfig_mailer == 'smtp') {
		$mail->SMTPAuth = $mosConfig_smtpauth;
		$mail->Username = $mosConfig_smtpuser;
		$mail->Password = $mosConfig_smtppass;
		$mail->Host = $mosConfig_smtphost;
	} else

		// Set sendmail path
		if ($mosConfig_mailer == 'sendmail') {
			if (isset ($mosConfig_sendmail))
				$mail->Sendmail = $mosConfig_sendmail;
		} // if

	$mail->Subject = $subject;
	$mail->Body = $body;

	return $mail;
}

/**
 * Sending out email. Very similar to mosMail, except that we uses jomCreateMail
 * instead of mosCreateMail to allow us to change the encoding. 
 */
function jomMail($from, $fromname, $recipient, $subject, $body, $mode = 0, $cc = NULL, $bcc = NULL, $attachment = NULL, $replyto = NULL, $replytoname = NULL) {
	global $mosConfig_debug;

	$body = stripslashes($body);
	$mail = jomCreateMail($from, $fromname, $subject, $body);

	// activate HTML formatted emails
	if ($mode) {
		$mail->IsHTML(true);
	}

	if (is_array($recipient)) {
		foreach ($recipient as $to) {
			$mail->AddAddress($to);
		}
	} else {
		$mail->AddAddress($recipient);
	}
	if (isset ($cc)) {
		if (is_array($cc)) {
			foreach ($cc as $to) {
				$mail->AddCC($to);
			}
		} else {
			$mail->AddCC($cc);
		}
	}
	if (isset ($bcc)) {
		if (is_array($bcc)) {
			foreach ($bcc as $to) {
				$mail->AddBCC($to);
			}
		} else {
			$mail->AddBCC($bcc);
		}
	}
	if ($attachment) {
		if (is_array($attachment)) {
			foreach ($attachment as $fname) {
				$mail->AddAttachment($fname);
			}
		} else {
			$mail->AddAttachment($attachment);
		}
	}
	//Important for being able to use mosMail without spoofing...
	if ($replyto) {
		if (is_array($replyto)) {
			reset($replytoname);
			foreach ($replyto as $to) {
				$toname = ((list ($key, $value) = each($replytoname)) ? $value : '');
				$mail->AddReplyTo($to, $toname);
			}
		} else {
			$mail->AddReplyTo($replyto, $replytoname);
		}
	}

	$mailssend = $mail->Send();

	if ($mosConfig_debug) {
		//$mosDebug->message( "Mails send: $mailssend");
	}
	if ($mail->error_count > 0) {
		//$mosDebug->message( "The mail message $fromname <$from> about $subject to $recipient <b>failed</b><br /><pre>$body</pre>", false );
		//$mosDebug->message( "Mailer Error: " . $mail->ErrorInfo . "" );
	}
	return $mailssend;
}
