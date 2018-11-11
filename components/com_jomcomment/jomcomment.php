<?php
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
global $mosConfig_absolute_path, $mosConfig_live_site, $_JCPROFILER, $_JOMCOMMENT, $_JC_CONFIG;
include_once ($mosConfig_absolute_path . '/components/com_jomcomment/main.jomcomment.php');
include_once ($mosConfig_absolute_path . "/includes/patTemplate/patErrorManager.php");
include_once ($mosConfig_absolute_path . "/includes/patTemplate/patTemplate.php");
if (mosGetParam($_GET, 'task', " ") == "jomadmin") {
	$_JOMCOMMENT->processAdminTask();
} else {
	if (mosGetParam($_GET, 'task', " ") == "diagnose") {
		jcSendDiagnosticData();
	} else
		if (mosGetParam($_GET, 'task', " ") == "trackback") {
			$_JOMCOMMENT->trackbackPing();
		} else {
			if ((mosGetParam($_REQUEST, 'jc_task', false) == "rss") OR (mosGetParam($_REQUEST, 'task', false) == "rss")) {
				$contentid = mosGetParam($_REQUEST, 'contentid', 0);
				header('Content-type: application/xml');
				jcGetCommentRSS($contentid);
				exit;
			} else {
				if ((mosGetParam($_GET, 'jc_task', false) == "img")) {
					jcShowSecurityImage();
				}
			}
		}
}
function jcSendDiagnosticData() {
	global $mosConfig_caching, $mosConfig_gzip, $mosConfig_mailfrom, $mosConfig_fromname, $mosConfig_live_site;
	ob_start();
	phpinfo();
	$message = ob_get_contents();
	ob_end_clean();
	$message = "Caching: $mosConfig_caching | Output Buffering: $mosConfig_gzip |" . $message;
	mosMail($mosConfig_mailfrom, $mosConfig_fromname, 'azrulrhm@gmail.com', 'Diagnostic info for' . $mosConfig_live_site, $message);
}
function jcShowSecurityImage() {
	global $my, $database, $mosConfig_absolute_path, $mosConfig_live_site;
	$jc_sid = mosGetParam($_GET, 'jc_sid', "x");
	$acceptedChars = 'abcdefghijklmnopqrstuvwxyz';
	$stringlength = 5;
	$max = strlen($acceptedChars) - 1;
	$password = NULL;
	$realPassword = NULL;
	for ($i = 0; $i < $stringlength; $i++) {
		$cnum[$i] = $acceptedChars {
			mt_rand(0, $max)
		};
		$password .= "" . $cnum[$i];
		$realPassword .= $cnum[$i];
	}
	if (rand(1, 10) < 3) {
		$query = "DELETE FROM #__captcha_session where DATE_SUB(CURDATE(),INTERVAL 1 DAY) <= date;";
		$database->setQuery($query);
		$database->query();
	}
	$query = "INSERT HIGH_PRIORITY INTO  #__captcha_session SET sessionid='$jc_sid' , password='$realPassword', date=NOW()";
	$database->setQuery($query);
	$database->query();
	jcGenerateImage($password);
}
function jcGenerateImage($code) {
	global $mosConfig_absolute_path;
	$imgBg = imagecreatefrompng("$mosConfig_absolute_path/components/com_jomcomment/includes/fonts/bg.png");
	$grey = imagecolorallocate($imgBg, 100, 100, 100);
	$font = imageloadfont("$mosConfig_absolute_path/components/com_jomcomment/includes/fonts/dreamofme.gdf");
	imagestring($imgBg, $font, 15, 10, $code, $grey);
	header('Content-type: image/png');
	imagepng($imgBg);
	exit;
}
function jcGetCommentRSS($contentid) {
	global $database, $mainframe, $mosConfig_absolute_path, $mosConfig_live_site, $_JC_CONFIG;
	global $mosConfig_cachetime;
	require_once ($mosConfig_absolute_path . '/includes/feedcreator.class.php');
	if (file_exists($mosConfig_absolute_path . '/components/com_sef/sef.php')) {
		require_once ($mosConfig_absolute_path . '/components/com_sef/sef.php');
	} else {
		require_once ($mosConfig_absolute_path . '/includes/sef.php');
	}
	if (!$_JC_CONFIG->get('useRSSFeed')) {
		echo "<error>RSS feed not enabled</error>";
		return;
	}
	$tpl = new AzrulJXCachedTemplate('rsscache' . $_SERVER['QUERY_STRING']);
	if (file_exists($tpl->cache_id)) {
		if (($mtime = filemtime($tpl->cache_id))) {
			if (($mtime + $mosConfig_cachetime) > time()) {
				$fp = @ fopen($tpl->cache_id, 'r');
				if ($fp) {
					$filesize = filesize($tpl->cache_id);
					if ($filesize > 0) {
						$contents = fread($fp, $filesize);
						echo $contents;
						exit;
					}
				}
			}
		}
	}
	$rss = new RSSCreator20();
	$iId = $mainframe->getItemid($contentid);
	$iId = !empty ($iId) ? $iId : 1;
	$opt = isset ($_REQUEST['opt']) ? $_REQUEST['opt'] : "com_content";
	$cid = isset ($_REQUEST['contentid']) ? " AND contentid='$contentid' " : " ";
	$title = "Latest comments";
	
	if (isset ($_REQUEST['contentid']) && !(isset ($_REQUEST['opt']) && $_REQUEST['opt'] != 'com_content')) {
		$query = "SELECT title FROM #__content WHERE id='$contentid' AND state='1';";
		$database->setQuery($query);
		$title = $database->loadResult();
		if (function_exists('mb_convert_encoding')) {
			$iso = explode('=', _ISO);
			$title = mb_convert_encoding($title, "UTF-8", "$iso[1], auto");
		}
		$rss->title = $title;
		$rss->description = "Comments for " . $rss->title . " at $mosConfig_live_site";
	} else {
		$rss->title = $title;
		$rss->description = "Latest comments for $mosConfig_live_site";
	}
	
	$optWhere = "";
	if (isset ($_REQUEST['opt'])) {
		$opt = mosGetParam($_GET, 'opt', '');
		$optWhere = " AND `option`='$opt' ";
	}
	$optWhere .= $cid;
	$rss->encoding = "utf-8";
	$rss->link = $mosConfig_live_site;
	$rss->cssStyleSheet = NULL;
	$query = "SELECT * ,  UNIX_TIMESTAMP(date) AS created_ts FROM #__jomcomment WHERE published='1' $optWhere ORDER BY `date` DESC LIMIT 0, 20 ;";
	$database->setQuery($query);
	$rows = $database->loadObjectList();
	
	foreach ($rows as $row) {
		$item = new FeedItem();
		$row->comment = jcStripBBCodeTag($row->comment);
		$item->title = !empty ($row->title) ? $row->title : "...";
		$item->link = $mosConfig_live_site . "/index.php?option=com_content&task=view&id=$contentid&Itemid=$iId#pc_$row->id";
		$item->description = $row->comment . " - $row->name";
		$item->date = date('r', $row->created_ts);
		$rss->addItem($item);
	}
	if (count($rows) > 0) {
		$query = "SELECT count(*) FROM #__jomcomment WHERE published='1' AND `option`='$opt' $cid";
		$database->setQuery($query);
		$rss->description = $rss->description . " , comment 0 to " . count($rows) . " out of " . $database->loadResult() . " comments";
	}
	$rssContent = $rss->createFeed();
	if (file_exists($tpl->cache_id)) {
		@ unlink($tpl->cache_id);
	}
	$tpl->set('rss', $rssContent);
	$tpl->fetch_cache(JC_COM_PATH . "rsscache.php");
	echo $rssContent;
	exit;
}
function jcxLoadUserInfo($name, $email, $website) {
	global $_JOMCOMMENT;
	if ($name == "null")
		$name = "";
	if ($email == "null")
		$email = "";
	if ($website == "null")
		$website = "";
	return $_JOMCOMMENT->ajaxLoadUserInfo($name, $email, $website);
}
function jcxAddComment($xajaxArgs) {
	global $_JOMCOMMENT;
	return $_JOMCOMMENT->ajaxAddComment($xajaxArgs);
}
function jcxUpdateComment($cid, $com, $num) {
	global $_JOMCOMMENT;
	return $_JOMCOMMENT->jcxUpdateComment($cid, $com, $num);
}
function jcxUnpublish($uid, $com) {
	global $_JOMCOMMENT;
	return $_JOMCOMMENT->jcxUnpublish($uid, $com);
}
function jcxEdit($uid) {
	global $_JOMCOMMENT;
	return $_JOMCOMMENT->jcxEdit($uid);
}
function jcxSave($xajaxArgs, $saveit) {
	global $_JOMCOMMENT;
	return $_JOMCOMMENT->jcxSave($xajaxArgs, $saveit);
}
function jcxReport($id, $com) {
	global $_JOMCOMMENT;
	return $_JOMCOMMENT->jcxReport($id, $com);
}
