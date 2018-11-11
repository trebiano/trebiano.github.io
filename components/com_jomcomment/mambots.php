<?php 

defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
if (isset ($_MAMBOTS))
	$_MAMBOTS->registerFunction('onPrepareContent', 'jomcomment_bot');
function HTML_updateScript() {
	$jsscript = '
			<script type=\'text/javascript\'>
		    <!-- 
		    ';
	$jsscript .='
    function updateComment(){
        var comment_div = document.getElementById("jc_commentsDiv");
        var numComment = comment_div.child();
    }
		    '; $jsscript .= "//--></script> "; } 

function jomcomment($uid, $com_option, $custom_params = "", $custom_template = "") { 
	$item = new stdClass; 
	$params = new stdClass; 
	$item->id = intval($uid); 
	$item->text = ""; 
	$item->sectionid = 1024; 
	$item->readmore = false; 
	jomcomment_bot(true, $item, $params, 0, $com_option); 
	return $item->text; 
} 

function removeJomTags(& $text) { 
	$text = str_replace("{moscomment}", "", $text); 
	$text = str_replace("{!moscomment}", "", $text); 
	$text = str_replace("{jomcomment}", "", $text); 
	$text = str_replace("{!jomcomment}", "", $text); 
	$text = str_replace("{jomcomment_lock}", "", $text); 
	$text = str_replace("{jomcomment lock}", "", $text); 
	$text = preg_replace('/\{trackback\}(.*)\{\/trackback\}/', '', $text); 
	return $text; 
} 

function jomcomment_bot_1_5(& $row, & $params, $page = 0) { 
	jomcomment_bot(1, $row, $params); 
} 

function jomcomment_bot($published, & $row, & $params, $page = 0, $extoption = "com_content", $exttask = "view") { 
	global $option, $task, $mainframe, $my, $mosConfig_live_site, $mosConfig_absolute_path; 
	global $_JOMCOMMENT, $_JC_CONFIG, $_JC_UTF8; 
	include_once ($mosConfig_absolute_path . "/components/com_jomcomment/main.jomcomment.php"); 
	include_once ($mosConfig_absolute_path . "/components/com_jomcomment/includes/mailq/mailq.php"); 
	if (!$_JC_CONFIG->get('enable')) { return; } 
	$pcTemplatePath = JC_BOT_PATH . "templates/"; $pcTemplate = $pcTemplatePath . $_JC_CONFIG->get('template'); 
	$pcLivePath_images = JC_BOT_LIVEPATH . "/templates/images/"; 
	if ($extoption == "com_content") { 
		if (!isset ($row->sectionid)) { 
			removeJomTags($row->text); 
			return; 
		} 
		
		if (!$published) { 
			removeJomTags($row->text); 
			return; 
		} 
		
		if (!($option == 'com_content' AND $task == 'view')) { 
			$_JOMCOMMENT->showBlogView($row, $params, false); 
			removeJomTags($row->text); 
			return; 
		} 
	} 
	
	
include_once ($mosConfig_absolute_path . "/components/com_jomcomment/jomcomment.php");
if (($option == 'com_content' AND $task == 'view') OR ($extoption != 'com_content' AND $exttask == 'view')) {
	if (@ !isset ($params->_params->url) && ($option == "com_content")) {
		return;
	}
	$showCommentArea = true;
	if (isset ($row->sectionid) && isset ($row->catid) && ($option == 'com_content')) {
		$showCommentArea = $_JOMCOMMENT->validCategory($row->catid);
	}
	if (!isset ($row->sectionid) OR strpos($row->text, "{!jomcomment}")) {
		$showCommentArea = false;
	}
	if (strpos($row->text, "{jomcomment}")) {
		$showCommentArea = true;
	}
	if (isset ($params->_params->intro_only)) {
		if ($params->_params->intro_only) {
			$showCommentArea = false;
		}
	}
	if (!$showCommentArea) {
		removeJomTags($row->text);
		return;
	}
	if ($_JC_CONFIG->get('autoUpdate'))
		$mainframe->addCustomHeadTag(HTML_updateScript());
		$name_field = $_JC_CONFIG->get('username');
		$user_name = isset ($my-> $name_field) ? $my-> $name_field : "";
		$user_email = isset ($my->email) ? $my->email : "";
		$busy_gif = $mosConfig_live_site . '/' . 'components/com_jomcomment/busy.gif';
		$user_email = str_replace('@', '+', $user_email);
		$jsscript = "";
		$jsscript .='
<script type=\'text/javascript\'>
/*<![CDATA[*/
var jc_username			= "'. $user_name.'";
var jc_email			= "'. $user_email.'";
/*]]>*/
</script>' . "\n"; $bbCodeToolbar = $_JOMCOMMENT->_viewMgr->getBBCodeToolbar(JC_BOT_LIVEPATH); $comments = $jsscript . $_JOMCOMMENT->getHTML($row->id, $extoption, $row); $busyImgPath = $mosConfig_live_site . "/components/com_jomcomment/busy.gif"; $footerText = '<br/><div style="text-align:center;font-size:95%"><a target="_blank" href="http://www.azrul.com"></a></div>'; $footerText .= '<script type=\'text/javascript\'> jc_loadUserInfo(); </script>'; $footerText .= '<img src="' . $busyImgPath . '" alt="busy" style="visibility:hidden;display:none;"/>'; $footerText .= '<!-- JOM COMMENT END -->'; $footerText = jcFixLiveSiteUrl($footerText); $row->text .= '<!-- JOM COMMENT START -->'; $row->text .= $comments; $row->text .= $footerText; $row->text = removeJomTags($row->text); $_JOMCOMMENT->trackbackSend($row); $mailq = new JCMailQueue(); $mailq->send(); return; } return true; }
