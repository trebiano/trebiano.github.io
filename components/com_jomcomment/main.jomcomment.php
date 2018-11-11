<?php

/**
 * Jom Comment 
 * @package JomComment
 * @copyright (C) 2006 by Azrul Rahim - All rights reserved!
 * @license Copyrighted Commercial Software
 **/

# Don't allow direct linking
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
if (defined('_JC_MAINFRAME_CLASS')) {
	return;
} else {
	define('_JC_MAINFRAME_CLASS', 1);
}
global $mosConfig_absolute_path, $mosConfig_live_site, $_JCPROFILER, $_JOMCOMMENT, $_JC_CONFIG, $_JC_UTF8;
if (defined('_JEXEC')) {
	defined('_JEXEC') or die('Direct Access to this location is not allowed.');
	define('JC_BOT_FOLDER', "plugins");
	define('JC_BOT_PATH', $mosConfig_absolute_path . '/components/com_jomcomment/');
	define('JC_BOT_LIVEPATH', $mosConfig_live_site . '/components/com_jomcomment/');
	define('JC_LANGUAGE', $mosConfig_absolute_path . '/components/com_jomcomment/languages/');

} else {
	defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
	define('JC_BOT_FOLDER', "mambots");
	define('JC_BOT_PATH', $mosConfig_absolute_path . '/components/com_jomcomment/');
	define('JC_BOT_LIVEPATH', $mosConfig_live_site . '/components/com_jomcomment/');
	define('JC_LANGUAGE', $mosConfig_absolute_path . '/components/com_jomcomment/languages/');
	include_once ($mosConfig_absolute_path . "/includes/patTemplate/patTemplate.php");
}

define('JC_COM_PATH', $mosConfig_absolute_path . '/components/com_jomcomment/');
define('JC_ADMIN_COM_PATH', $mosConfig_absolute_path . '/administrator/components/com_jomcomment/');
define('JC_CONFIG', $mosConfig_absolute_path . '/administrator/components/com_jomcomment/config.jomcomment.php');
define('JC_STATUS_BLOCKED', '2');
define('JC_STATUS_OK', '1');
define('JC_STATUS_WARNING', '0');
define('JC_DEBUG', '0');
define('JC_VERSION', '1.8');
include_once (JC_CONFIG);
include_once (JC_COM_PATH . 'class.templates.php');
include_once (JC_COM_PATH . 'class.encoding.php');
include_once (JC_COM_PATH . 'profiler.jomcomment.php');
include_once (JC_COM_PATH . 'functions.jomcomment.php');
include_once (JC_COM_PATH . 'includes/fogscout/scout.php');
include_once (JC_COM_PATH . 'cms/spframework.php');

/**
 * Main controller class
 */
class JCMainFrame 
{
	var $_language = null;
	var $_utf8 = null;
	var $_viewMgr = null;
	var $_dataMgr = null;

	/**
	 * Constructor
	 */
	function JCMainFrame() {
		global $mosConfig_absolute_path;
		include_once (JC_COM_PATH . 'datamanager.jomcomment.php');
		include_once (JC_COM_PATH . 'views.jomcomment.php');
		include_once (JC_ADMIN_COM_PATH . 'config.jomcomment.php');

		# load the config
		$this->_config = new StdClass();

		# set up utf8 object
		$this->_utf8 = new Utf8Helper();

		# set up data manager
		$this->_dataMgr = new JCDataManager();

		# set up view manager
		$this->_viewMgr = new JCView();
	}
	function getCommentContainer() {
	}

	# Return the formatted comments list
	function getHTML($cid, $option, & $contentObj) {
		$page = mosGetParam($_GET, 'cpage', 0);
		$data = $this->_dataMgr->getAll($cid, $option);
		$html = $this->_viewMgr->prepAll($data, $cid, $option, $contentObj);
		unset($data);
		return $html;
	}
	function tbGetHTML($cid, $option) {
		$data = $this->_dataMgr->tbGetAll($cid, $option);
		$html = $this->_viewMgr->tbPrepAll($data, $cid, $option);
		return $html;
	}
	
	/**
	 * return some default path
	 */
	function getPath($varname) {
		global $mosConfig_absolute_path, $_JC_CONFIG;
		switch ($varname) {
			case 'spamfilter' :
				return JC_COM_PATH . "spamfilter.jomcomment.php";
			case 'views' :
				return JC_COM_PATH . "views.jomcomment.php";
			case 'datamanager' :
				return JC_COM_PATH . "datamanager.jomcomment.php";
			case 'language' :
				return $mosConfig_absolute_path . "/components/com_jomcomment/languages/" . $_JC_CONFIG->get('language');
		}
	}
	
	/**
	 * Return the name of current template.
	 * Just a special version of config.
	 */
	function getTemplate() {
		global $_JC_CONFIG;
		return $_JC_CONFIG->get('template');
	}
	function getSecurityImg($sid) {
	}

	/**
	 * Return a unique 32 character unique id
	 */
	function getSid() {
		$token = md5(uniqid('a'));
		$sid = md5(uniqid(rand(), true));
		return $sid;
	}

	/**
	 * Need to notify admin and give link to publish/unpublish the comment
	 */
	function notifyAdmin($data) {
		global $database, $mosConfig_live_site, $mosConfig_offset_user, $_JC_CONFIG;
		global $mosConfig_mailfrom, $mosConfig_fromname, $mosConfig_offset, $_JCPROFILER;
		
		# Must make sure that the emai is valid, otherwise, do not send the
		# email				
		$email = $_JC_CONFIG->get('notifyEmail');		
		$regexp = "^([_a-z0-9-]+)(\.[_a-z0-9-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,4})$";
		if(!empty($email) && eregi($regexp, $email)){
		
		$sid = $this->getSid();
		$date = strftime("%Y-%m-%d %H:%M:%S", time() + ($mosConfig_offset * 60 * 60));
		$database->setQuery("DELETE FROM #__jomcomment_admin WHERE sid='$sid'");
		$database->query();
		$database->setQuery("INSERT INTO #__jomcomment_admin 
									SET sid='$sid', action='moderate', commentid=$data->id, date='$date'
									ON DUPLICATE KEY UPDATE date='$date'");
		$database->query();
		$subject = "New comment added. ";
		
		if (!$data->published)
			$subject .= "Moderator approval required.";
			
		$publishLink = sefRelToAbs($mosConfig_live_site . "/index.php?option=com_jomcomment&task=jomadmin&sid=$sid&do=publish");
		$unpublishLink = sefRelToAbs($mosConfig_live_site . "/index.php?option=com_jomcomment&task=jomadmin&sid=$sid&do=unpublish");
		$deleteLink = sefRelToAbs($mosConfig_live_site . "/index.php?option=com_jomcomment&task=jomadmin&sid=$sid&do=delete");
		
		// Need to find the proper Itemid, this isn't right
		$link = sefRelToAbs($mosConfig_live_site . "/index.php?option=com_content&task=view&id=$data->id&Itemid=1");
		
		if(isset($_SERVER['HTTP_REFERER'])){ 
			$link = $_SERVER['HTTP_REFERER'];
		}
		$contentTitle = jcContentTitleGet($data->contentid);
		$comment = jcTextwrap($data->comment);
		$email_msg = "";
		if (!$data->published)
			$email_msg = "
		The following message needs your approval. You can approve it through the 
		backend or simply use the link below to publish/unpublish/delete the comment. This link will
		expires in 7 days.";
		else
			$email_msg = "
		The following message has been published. You can use the link below to publish/unpublish/delete the comment. This link will
		expires in 7 days.";
		$email_msg .= "
	\n
	1. Publish:
	$publishLink
	
	2. Unpublish:
	$unpublishLink
	
	3. To Delete:
	$deleteLink
	
	===========================================================================
	Content Title: $contentTitle
	===========================================================================\n
	Comment Title: $data->title
	Author: $data->name
	Email: $data->email
	Link to content: $link
	
	Comment: 
	$data->comment\n
	\n
	===========================================================================\n
	\n
	\n
	[Powered by Jom Comment]\n
	";
		$mode = 0;
		$cc = NULL;
		$bcc = NULL;
		$attachment = NULL;
		$replyto = NULL;
		$replytoname = NULL;
		if ($data->email)
			$replyto = $data->email;
		if ($data->name)
			$replytoname = $data->name;
		jomMail($mosConfig_mailfrom, $mosConfig_fromname, $_JC_CONFIG->get('notifyEmail'), $subject, $email_msg, $mode, $cc, $bcc, $attachment, $replyto, $replytoname);
		
		}
	}

	# Test if the given section id is part of our valid sections
	# This is now, more appropriately, should be called valid category	
	function validCategory($sectionid) {
		global $_JC_CONFIG;
		$valid = true;
		
		# 1. section 1024 is all valid
		if ($sectionid == 1024)
			return true;
		
		# 2. if we don't limit on section, it's valid
		#if(!$_JC_CONFIG->get('limitSection'))
		#	return true;
		
		# 3. Check if static content needs comment as well.
		if ($_JC_CONFIG->get('staticContent') && $sectionid == 0)
			return true;
			
		# 4. If we limit on section, make sure it is there.	
		$categories = explode(",", $_JC_CONFIG->get('categories'));
		return in_array($sectionid, $categories);
	}
	
	
	# Add the 'add comment' | 'read more' link in front page and blog view 		 
	function showBlogView(& $row, & $params, $showComment = true) {
		global $mainframe, $mosConfig_live_site, $mosConfig_absolute_path, $_JC_CONFIG, $Itemid;
		global $option;
		if (@ !isset ($params->_params->menu_image) && ($option == "com_content")) {
			return;
		}
	
		# we need to decide if we want to display the comment count or not.
		$showComment = $_JC_CONFIG->get('showCommentCount');
		;
		if ($showComment) {
			$categories = explode(",", $_JC_CONFIG->get('categories'));
			$showComment = !(!in_array($row->catid, $categories) AND !($_JC_CONFIG->get('staticContent') && ($row->sectionid == 0))) && !strpos($row->text, "{!jomcomment}");
		}
		if ($row->sectionid == 0) {
			# no need to read-more for static content AT ALL
			return;
		}
	
		# Do not add link if we are in some modules (newsflashes? perhaps) 
		if (isset ($params->_params)) {
			if (array_key_exists("moduleclass_sfx", $params->_params)) {
				if (!@ $params->_params['readmore']) {
					return;
				}
			}
		}
		$iId = 1;
		if (class_exists('JApplicationHelper')) {
			$iId = JApplicationHelper :: getItemid($row->id);
		} else {
			$iId = $mainframe->getItemid($row->id);
			if (empty ($iId))
				$iId = $Itemid;
		}
		$show = array ();
		$show['readmore'] = $_JC_CONFIG->get('useReadMore');
		$show['comment'] = $showComment;
		$show['hit'] = $_JC_CONFIG->get('showHitCount');
		$link = array ();
		$link['readmore'] = sefRelToAbs('index.php?option=com_content&amp;task=view&amp;id=' . $row->id . '&amp;Itemid=' . $iId . '');
		$link['comment'] = $link['readmore'] . "#jc_writeComment";
		$count = array ();
		$count['comment'] = jcCommentGetNum($row->id);
		$count['hit'] = jcContentHitCount($row->id);
		
		
		# If the system is set to show readmore, display it!
		if (isset ($row->readmore)) {
			if ($row->readmore && $_JC_CONFIG->get('useReadMore')) {
				$show['readmore'] = true;
			}
		}
	
		# If the system is not set to show readmore, check if Jom Comment is set
		# to show it by force
		if (isset ($row->readmore)) {
			if (!$row->readmore && !$_JC_CONFIG->get('useSelectiveReadMore') && $_JC_CONFIG->get('useReadMore')) {
				$show['readmore'] = true;
			}
		
			if (!$row->readmore && $_JC_CONFIG->get('useSelectiveReadMore')) {
				$show['readmore'] = false;
			}
		}
		if ($show['readmore']) {
			$row->readmore = 0;
			$params->_params->readmore = $row->readmore;
		}
		$tpl = new AzrulJXTemplate();
		$tpl->set('show', $show);
		$tpl->set('link', $link);
		$tpl->set('count', $count);
		$tpl->set('debugview', false);
		$fdata = "";
		if (file_exists(JC_BOT_PATH . "/templates/" . $_JC_CONFIG->get('template') . '/readmore.tpl.html'))
			$fdata = trim($tpl->fetch(JC_BOT_PATH . "templates/" . $_JC_CONFIG->get('template') . '/readmore.tpl.html'));
		else
			$fdata = trim($tpl->fetch(JC_BOT_PATH . "templates/_default/readmore.tpl.html"));
		$fdata = $this->_viewMgr->_translateTemplate($fdata);
		$row->text .= $fdata;
		unset ($template);
	}
	
	/**
	 * Process click from admin's email
	 */
	function processAdminTask() {
		global $database, $mosConfig_live_site;
		$sid = mosGetParam($_GET, 'sid', "");
		if ($sid) {
			$database->setQuery("SELECT commentid FROM #__jomcomment_admin WHERE sid='$sid'");
			$commentid = $database->loadResult();
			if ($commentid) {
				switch ($_GET['do']) {
					case 'publish' :
						$this->_dataMgr->publish($commentid);
						echo "<p><b><img src=\"$mosConfig_live_site/administrator/images/tick.png\" /> The comment has been published</b></p>";
						break;
					case 'unpublish' :
						$this->_dataMgr->unpublish($commentid);
						echo "<p><b><img src=\"$mosConfig_live_site/administrator/images/tick.png\" /> The comment has been unpublished</b></p>";
						break;
					case 'delete' :
						$this->_dataMgr->delete($commentid);
						echo "<p><b><img src=\"$mosConfig_live_site/administrator/images/tick.png\" /> The comment has been deleted</b></p>";
						break;
				}
				
				# delete older admin code
				$query = "DELETE FROM #__jomcomment_admin where DATE_SUB(CURDATE(),INTERVAL 14 DAY) <= date;";
				$database->setQuery($query);
				$database->query();
				return;
			}
		}
		echo "<p><b>The link is invalid. You can use the backend to publish the comment.</b></p>";
	}

	/**
	 * Given the name, email and website, we simply return all of it back, with
	 * updates name and email if user is currently logged in 
	 */
	function ajaxLoadUserInfo($name, $email, $website) {
		global $my, $mosConfig_caching, $_JC_CONFIG;
		while (@ ob_end_clean());
		ob_start();
		if (!isset ($name))
			$name = "";
		if (!isset ($email))
			$email = "";
		if (!isset ($website))
			$website = "";
		if (is_array($name))
			$name = "";
		if (is_array($email))
			$email = "";
		if (is_array($website))
			$website = "";
		if ($my->name) {
			$name = $my->name;
			$email = $my->email;
			
			# name is all but in utf-8 encoding. We need to convert this to utf8
			if (function_exists('mb_convert_encoding') && @ (_ISO)) {
				$iso = explode('=', _ISO);
				$name = mb_convert_encoding($name, "UTF-8", $iso[1]);
			}
		}
		$objResponse = new JAXResponse();
		$objResponse->addAssign('jc_name', 'value', strval($name));
		$objResponse->addAssign('jc_email', 'value', strval($email));
		$objResponse->addAssign('jc_website', 'value', strval($website));
		
		// Need to load new security code picture as well
		if ($_JC_CONFIG->get('useCaptcha') AND $mosConfig_caching) {
			$sidNew = $this->getSid();
			$resultCaptchaImg = sefRelToAbs("index2.php?option=com_jomcomment&no_html=1&jc_task=img&jc_sid=$sidNew");
			$resultCaptchaSid = $sidNew;
			$objResponse->addAssign('jc_captchaImg', 'src', $resultCaptchaImg);
			$objResponse->addAssign('jc_sid', 'value', $resultCaptchaSid);
		}
		return $objResponse->sendResponse();
	}
	
	/**
	 * Some code that need to be attached to the page <head> section.
	 * There is no need to add this code if the user opt to. 	 
	 */
	function addCustomHeader() {
		global $mosConfig_absolute_path, $mainframe, $option, $mosConfig_live_site, $_JC_CONFIG;
		if (!$this->requireHeaderScript())
			return;
		$jsscript = "";
		$style = $_JC_CONFIG->get('template') . "/comment_style.css";
		if (@ strpos($style, ".html")) {
			$style = substr($_JC_CONFIG->get('template'), 0, -5) . "/comment_style.css";
		}
		$jsscript .= '<link rel="stylesheet" type="text/css" href="' . $mosConfig_live_site . '/' . 'components/com_jomcomment/style.css"/>' . "\n";
		$jsscript .= '<link rel="stylesheet" type="text/css" href="' . $mosConfig_live_site . '/' . 'components/com_jomcomment/templates/' . $style . '"/>' . "\n";
		$jsscript .= $this->addCustomScript();
		$jsscript = jcFixLiveSiteUrl($jsscript);
		$mainframe->addCustomHeadTag($jsscript);
		return;
	}
	function addCustomScript() {
		global $mosConfig_live_site, $option, $_JC_CONFIG, $my, $mainframe;
		if (!isset ($my))
			$my = $mainframe->getUser();
		$name_field = $_JC_CONFIG->get('username');
		$user_name = isset ($my-> $name_field) ? $my-> $name_field : "";
		$user_email = isset ($my->email) ? $my->email : "";
		$busy_gif = $mosConfig_live_site . '/' . 'components/com_jomcomment/busy.gif';
		$jsscript = "";
		$jsscript .= '
	<script type=\'text/javascript\'>
	/*<![CDATA[*/
	var jc_option           = "' . $option . '";
	var jc_autoUpdate       = "' . $_JC_CONFIG->get('autoUpdate') . '";
	var jc_update_period    = ' . $_JC_CONFIG->get('updatePeriod') . '*1000;
	var jc_orderBy          = "' . $_JC_CONFIG->get('sortBy') . '";
	var jc_livesite_busyImg = "' . $busy_gif . '";
	var jc_commentForm;
	/*]]>*/
	</script>' . "\n";
		$task = mosGetParam($_GET, 'task', "");
		$jsscript .= '<script src="' . $mosConfig_live_site . '/' . 'components/com_jomcomment/script.js?' . JC_VERSION . '" type="text/javascript"></script>';
		return $jsscript;
	}
	/**
	 * Add a new comment
	 */
	function ajaxAddComment($xajaxArgs) {
		error_reporting(E_ALL);
		global $_JCPROFILER, $my, $mosConfig_caching, $mosConfig_absolute_path, $_JC_CONFIG, $_JCPROFILER;
		global $database, $mosConfig_fromname;
		include_once ($this->getPath('spamfilter'));
		include_once ($this->getPath('language'));
		$ob_active = ob_get_length() !== FALSE;
		if ($ob_active) {
			while (@ ob_end_clean());
			if (function_exists('ob_clean')) {
				@ ob_clean();
			}
		}
		ob_start();
		$emailAdmin = $_JC_CONFIG->get('notifyAdmin');
		$objResponse = new JAXResponse();
		$responseMsg = "";
		$resultMsg = $this->_utf8->utf8ToHtmlEntities(_JC_MESSAGE_ADDED);
		$status = JC_STATUS_OK;

		# create a new comment object
		$data = $this->_dataMgr->create($xajaxArgs);
		if ($data == null) {
			# If 'create' fail, there could be some missing info or data not validated
			$resultMsg = $this->_dataMgr->getCreateError();
			$status = JC_STATUS_WARNING;
		}

		# apply filters
		if ($status == JC_STATUS_OK) {
			$filter = new JCSpamFilter($data);
			if ($filter->isSpam()) {
				$resultMsg = $filter->getErrorMsg();
				$status = JC_STATUS_WARNING;
			}
		}
		# @rule: For com_content, do not add the comment if the content are unpublished
		if($data->option == 'com_content'){
			if(jcContentPublished($data->contentid) != 1){
				$resultMsg = "Cannot add comment to unpublished content";
				$status = JC_STATUS_WARNING;
			}
		}

		# @rule: hard limit on the number of comment per 30 minutes by the same IP 
		# we block it's IP automatically. and inform admin
		$numcommentByIp = $this->_dataMgr->getNumCommentByIP($data->ip, $data->date);
		if (($status == JC_STATUS_OK) AND ($numcommentByIp > 20)) {
			$_JC_CONFIG->addBlockedIP($data->ip);
			$status = JC_STATUS_WARNING;
			$resultMsg = "Are you trying to spam? Please allow '" . $_JC_CONFIG->get('postInterval') . "' seconds between post.";
		}

		# @rule: block SPAM flood
		if ($_JC_CONFIG->get('postInterval') AND ($status == JC_STATUS_OK)) {
			if ($this->_dataMgr->getFlood($data->name, $data->ip, $data->date, $_JC_CONFIG->get('postInterval'))) {
				$status = JC_STATUS_WARNING;
				$resultMsg = "Are you trying to spam? Please allow '" . $_JC_CONFIG->get('postInterval') . "' seconds between post.";
			}
		}

		# @rule : minimum comment length
		if ($_JC_CONFIG->get('commentMinLen') AND ($status == JC_STATUS_OK)) {
			if ($this->_utf8->strlen($data->comment) < intval(trim($_JC_CONFIG->get('commentMinLen')))) {
				$status = JC_STATUS_WARNING;
				$resultMsg = $this->_utf8->utf8ToHtmlEntities(_JC_TPL_TOO_SHORT);
			}
		}

		# @rule : maximum comment length
		if (($status == JC_STATUS_OK) AND $_JC_CONFIG->get('commentMaxLen')) {
			if ($this->_utf8->strlen($data->comment) > intval($_JC_CONFIG->get('commentMaxLen'))) {
				$status = JC_STATUS_WARNING;
				$resultMsg = $this->_utf8->utf8ToHtmlEntities(_JC_TPL_TOO_LONG);
			}
		}

		# @rule: duplicate entry
		if (($status == JC_STATUS_OK) AND $this->_dataMgr->searchSimilarComments($data->contentid, $data->comment, $data->date)) {
			$status = JC_STATUS_WARNING;
			$resultMsg = $this->_utf8->utf8ToHtmlEntities(_JC_TPL_DUPLICATE);
		}

		# @rule: password must be correct, 
		if ($_JC_CONFIG->get('useCaptcha') AND ($status == JC_STATUS_OK)) {
			$isOk = false;
			if (!$_JC_CONFIG->get('useCaptchaRegistered') AND $my->username) {
				$isOk = true;
			} else {
				$secCode = $this->_dataMgr->getPassword($data->_sid);
				$isOk = (isset ($secCode) AND (strval($secCode) == strval($data->_password)));
				$this->_dataMgr->deletePassword($data->_sid);
			}

			if (!$isOk) {
				$status = JC_STATUS_WARNING;
				$resultMsg = $this->_utf8->utf8ToHtmlEntities(_JC_CAPTCHA_MISMATCH);
			}
		}
		
		# store the new comment into database
		if ($status == JC_STATUS_OK) {
			$data->store();
		}
		
		# check if we need to unpublish it
		if ($status == JC_STATUS_OK) {
			global $mosConfig_mailfrom;
			
			# @unpublish rule: unpublish if necesary
			if (!$_JC_CONFIG->get('autoPublish')) {
				$this->_dataMgr->unpublish($data->id);
				$data->published = 0;
				$status = JC_STATUS_BLOCKED;
				$resultMsg = $this->_utf8->utf8ToHtmlEntities(_JC_MESSAGE_NEED_MOD);
			}
			
			# @unpublish rule: moderate guest post
			if (($status == JC_STATUS_OK) AND $_JC_CONFIG->get('modGuest') AND !$my->username) {
				$this->_dataMgr->unpublish($data->id);
				$data->published = 0;
				$status = JC_STATUS_BLOCKED;
				$resultMsg = $this->_utf8->utf8ToHtmlEntities(_JC_MESSAGE_NEED_MOD);
				$emailAdmin = true;
			}
			
			# @unpublish rule: contain blocked words
			if (($status == JC_STATUS_OK) AND $_JC_CONFIG->get('blockWords')) {
				$words = explode(",", $_JC_CONFIG->get('blockWords'));
				array_walk($words, "jctrim");
				foreach ($words as $word) {
					if (!empty ($word)) {
						if (@ stripos($data->comment, $word) !== FALSE) {
							$this->_dataMgr->unpublish($data->id);
							$data->published = 0;
							$status = JC_STATUS_BLOCKED;
							$resultMsg = $this->_utf8->utf8ToHtmlEntities(_JC_MESSAGE_NEED_MOD);
							$emailAdmin = true;
						}
					}
				}
			}
			# @unpublish rule: maximum number of link.
			# unfortunately, we need to process the comment first to be able to reliably count
			# the number of links
			$comment = $data->comment;
			if (!class_exists('HTML_BBCodeParser') AND !function_exists('BBCode')) {
				include_once ($mosConfig_absolute_path . "/components/com_jomcomment/bbcode.php");
			}
		
			$comment = BBCode($comment);
			
			# @rule : maximum comment length
			$urlCount = preg_match_all("/(a href=)/ie", $comment, $matches);
			if (intval($urlCount) > $_JC_CONFIG->get('spamMaxLink')) {
				$this->_dataMgr->unpublish($data->id);
				$data->published = 0;
				$status = JC_STATUS_BLOCKED;
				$resultMsg = $this->_utf8->utf8ToHtmlEntities(_JC_MESSAGE_NEED_MOD);
			}
			
			# send notification to admins if required
			$_JCPROFILER->mark('Sending Email');
			if ($emailAdmin) {
				$this->notifyAdmin($data);
			}
			$_JCPROFILER->mark('Notify Admin');
			
			# send notification to content author
			if ($_JC_CONFIG->get('notifyAuthor') && ($data->option == "com_content")) {
				$auid = jcContentAuthorGet($data->contentid);
				$contentTitle = jcContentTitle($data->contentid);
				$database->setQuery("SELECT email FROM #__users WHERE id=$auid");
				$authorEmail = $database->loadResult();
				
				$link = sefRelToAbs($mosConfig_live_site . "/index.php?option=com_content&task=view&id=$data->id&Itemid=1");
				if(isset($_SERVER['HTTP_REFERER'])){
					$link = $_SERVER['HTTP_REFERER'];
				}
				
				$email_msg = "Article title: " . $contentTitle . "\n\nTitle: " . $data->title . "\n\nComment: " . $data->comment . "\n\n\n";
				$mode = 0;
				$cc = NULL;
				$bcc = NULL;
				$attachment = NULL;
				$replyto = NULL;
				$replytoname = NULL;
				if ($data->email)
					$replyto = $data->email;
				if ($data->name)
					$replytoname = $data->name;
				jomMail($mosConfig_mailfrom, $mosConfig_fromname, $authorEmail, "Author notification: New comment posted", $email_msg, $mode, $cc, $bcc, $attachment, $replyto, $replytoname);
			}
			$_JCPROFILER->mark('Notify Author');
			# for joomla 1.0.9 and above, we need to clear content cache
			# also needs to clear up caching for modules
			if ($mosConfig_caching) {
				mosCache :: cleanCache('com_content');
			}
		}
		$sidNew = $this->getSid();
		$resultCaptchaImg = sefRelToAbs("index2.php?option=com_jomcomment&no_html=1&jc_task=img&jc_sid=$sidNew");
		$resultCaptchaSid = $sidNew;
		$responseMsg = '<div class="infolevel1"></div><div class="infolevel2"></div>';
		$responseMsg .= '<div class="infolevel3">' . $resultMsg . '</div>';
		$responseMsg .= '<div class="infolevel2"></div><div class="infolevel1"></div>';
		switch ($status) {
			case JC_STATUS_OK :
				$styles = explode(",", $_JC_CONFIG->get('cycleStyle'));
				array_walk($styles, "jctrim");
				$numStyle = count($styles);
				$styleCount = $this->_dataMgr->getNumComment($data->contentid, $data->option);
				$count = $styleCount;
				$style = $styleCount % $numStyle;
				$newComment = $this->_viewMgr->getCommentsHTML($data);
				$newComment = $this->_viewMgr->_cleaupOutput($newComment);
				$objResponse->addAssign('jc_busyDiv', 'innerHTML', $responseMsg);
				$objResponse->addScriptCall("jc_insertNewEntry", $newComment, "pc_" . $data->id);
				$objResponse->addClear('jc_comment', 'value');
				$objResponse->addClear('jc_title', 'value');
				$objResponse->addAssign('jc_numComment', 'innerHTML', $this->_dataMgr->getNumComment($data->contentid, $data->option));
				break;
			case JC_STATUS_BLOCKED :
				$objResponse->addClear('jc_title', 'value');
				$objResponse->addClear('jc_comment', 'value');
				break;
			case JC_STATUS_WARNING :
				break;
		}
		$objResponse->addAssign('mos_profiler', 'innerHTML', $_JCPROFILER->getHTML());
		$objResponse->addAssign('jc_captchaImg', 'src', $resultCaptchaImg);
		$objResponse->addAssign('jc_sid', 'value', $resultCaptchaSid);
		$objResponse->addAssign('jc_busyDiv', 'innerHTML', $responseMsg);
		$objResponse->addClear('jc_password', 'value');
		$objResponse->addScriptCall("jc_enableForm");
		$objResponse->addScriptCall("jcOpacity", "jc_busyDiv", 0, 100);
		$objResponse->addScriptCall("jc_fadeMessage");
		
		# technically, the output buffering should be empty. If it is not, send a bug report
		$ob_content = ob_get_contents();
		if (!empty ($ob_content)) {
		}
		return $objResponse->sendResponse();
	}
	function notifySubscribers($contentid, $option, $newcomment) {
		global $database;
		$sql = "SELECT * FROM `#__content` WHERE `id`='$contentid'";
		$database->loadQuery($sql);
		$mailq = new JCMailQueue();
		$mailq->mail($email, $subject, $content);
	}
	function jcxUpdateComment($cid, $com, $num) {
		global $mosConfig_absolute_path, $_JCPROFILER;
		$contentid = intval($cid);
		$com = strval($com);
		$currentNum = $this->_dataMgr->getNumComment($cid, $com);
		while (@ ob_end_clean());
		ob_start();
		$objResponse = new JAXResponse();
		if ($num != $currentNum) {
			$comments = $this->getHTML($cid, $com, 0);
			$objResponse->addAssign('jc_commentsDiv', 'innerHTML', $comments);
			$objResponse->addAssign('jc_numComment', 'innerHTML', $currentNum);
		}
		$objResponse->addScriptCall("setTimeout", "jc_update()", strval($_JC_CONFIG->get('updatePeriod')));
		return $objResponse->sendResponse();
	}
	function jcxUnpublish($postid, $com) {
		global $database, $my;
		$allowedUser = array (
			'Editor',
			'Publisher',
			'Manager',
			'Administrator',
			'Super Administrator'
		);
		$isAdmin = in_array($my->usertype, $allowedUser);
		$objResponse = new JAXResponse();
		if ($isAdmin) {
			$id = substr($postid, 3);
			$database->setQuery("UPDATE #__jomcomment SET published=0 WHERE id=$id AND `option`='$com'");
			$database->query();
			$database->setQuery("SELECT contentid FROM #__jomcomment WHERE id=$id");
			$contentid = $database->loadResult();
			$objResponse->addAssign('jc_numComment', 'innerHTML', jcCommentGetNum($contentid, $com));
		} else {
			$objResponse->addAlert("Permission Error. You might have been logged-out.");
		}
		return $objResponse->sendResponse();
	}
	function jcxEdit($postid) {
		global $database, $my;
		$allowedUser = array (
			'Editor',
			'Publisher',
			'Manager',
			'Administrator',
			'Super Administrator'
		);
		$isAdmin = in_array($my->usertype, $allowedUser);
		$objResponse = new JAXResponse();
		if ($isAdmin) {
			$id = substr($postid, 3);
			$database->setQuery("SELECT comment  FROM #__jomcomment WHERE id=$postid");
			$comment = $database->loadResult();
			$text = '<div id="pc_{id}" name="pc_{id}">
								                <form id="form-edit-{id}" name="form-edit-{id}" method="post" action="">
								                                    <label>
								                                    <textarea name="comment" rows="8" id="comment" style="width:98%">{comment}</textarea>
								                                    </label>
								                    <input name="id" type="hidden" id="id" value="{id}" />
								                                    <label>
								                                    <input name="Save" type="button" value="Save" onclick="jax.call(\'jomcomment\', \'jcxSave\', jax.getFormValues(\'form-edit-{id}\'), true);" />
								                                    </label>
								                                    <label>
								                                    <input name="Discard" type="button" value="Discard"  onclick="jax.call(\'jomcomment\',\'jcxSave\', jax.getFormValues(\'form-edit-{id}\'), false);"/>
								                                    </label>
								                </form>
								                </div>';
			$text = str_replace('{id}', $postid, $text);
			$text = str_replace('{comment}', $comment, $text);
			$objResponse->addAssign('pc_edit_' . $postid, 'innerHTML', $text);
		} else {
			$objResponse->addAlert("Permission Error. You might have been logged-out.");
		}
		return $objResponse->sendResponse();
	}
	function requireHeaderScript() {
		global $option, $database, $_JC_CONFIG;
		
		if($_JC_CONFIG->get('extComSupport')){
			return true;
		}
		
		return ($option == 'com_content' OR $option == 'com_myblog' OR $option=='com_frontpage');
	}

	/**
	 * Saving the data from front-end editing
	 */
	function jcxSave($xajaxArgs, $saveit) {
		global $database, $my, $_JC_CONFIG, $_JOMCOMMENT;
		$allowedUser = array (
			'Editor',
			'Publisher',
			'Manager',
			'Administrator',
			'Super Administrator'
		);
		$isAdmin = in_array($my->usertype, $allowedUser);
		$objResponse = new JAXResponse();
		if ($isAdmin) {
			require (JC_CONFIG);
			$comment = isset ($xajaxArgs['comment']) ? $xajaxArgs['comment'] : "";
			$id = isset ($xajaxArgs['id']) ? $xajaxArgs['id'] : 0;
			if (version_compare(phpversion(), "4.3.0") < 0) {
				$comment = mysql_escape_string(strip_tags($comment, $_JC_CONFIG->get('allowedTags')));
			} else {
				$comment = mysql_real_escape_string(strip_tags($comment, $_JC_CONFIG->get('allowedTags')));
			}
			if (true) {
				$database->setQuery("UPDATE #__jomcomment SET comment='$comment' WHERE id=$id");
				$database->query();
			}
			$query = "SELECT * FROM #__jomcomment WHERE id=$id";
			$database->setQuery($query);
			$dbRes = $database->query();
			$item = mysql_fetch_object($dbRes);
			$this->_viewMgr->prepData($item, 0, "none", false);
			$objResponse->addAssign('comment-text-container-' . $id, 'innerHTML', $item->comment);
			$objResponse->addAssign('pc_edit_' . $id, 'innerHTML', "");
		} else {
			$objResponse->addAlert("Permission Error. You might have been logged-out.");
		}
		
		# Clear the cache, otherwise it won't show after refresh
		global $mosConfig_cachepath;		
		$file_list = mosReadDirectory($mosConfig_cachepath, "");
		foreach ($file_list as $val) {
			if (strstr($val, "cache_")) {
				@ unlink($mosConfig_cachepath . "/" . $val);
			}
		}
		return $objResponse->sendResponse();
	}
	function _trackbackResponse($error = false, $error_message = "") {
		header("Content-Type: text/xml; charset=ISO-8859-1");
		if ($error)
			: print "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		print "<response>\n";
		print "<error>1</error>\n";
		print "<message>" . $error_message . "</message>\n";
		print "</response>";
		die();
		elseif (!$error) : print "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		print "<response>\n";
		print "<error>0</error>\n";
		print "</response>";
		endif;
		exit;
	}

	# Send outgoing trackback ping. If we have already send to the address before, 
	# do not send it again or we might be marked as spam
	# @param row	From com_content row	
	function trackbackSend($row) {
		global $database, $mosConfig_live_site, $mosConfig_sitename;
		include_once (JC_COM_PATH . 'includes/trackback/trackback_cls.php');
		$pattern = "'{trackback}(.*){/trackback}'s";
		preg_match($pattern, $row->text, $matches);
		if ($matches) {
			$trackbacks = @ $matches[1];
			
			# Found the trackbacks url
			$trackback_arr = explode(",", $trackbacks);
			foreach ($trackback_arr as $url) {
				$database->setQuery("SELECT count(*) FROM `#__jomcomment_tb_sent` WHERE `url`='$url'");
				if ($database->loadResult() == 0) {
					$trackback = new Trackback($mosConfig_sitename, $site_author, 'UTF-8');
					$url = jc_nl2brStrict($url);
					$trackback_url = trim(strip_tags($url));
					$content_url = $mosConfig_live_site;
					$content_title = $trackback->cut_short($row->title);
					$content_excerpt = $trackback->cut_short($row->text);
					$content_id = $row->id;
					$site_author = "azrul";
					if ($trackback->ping($trackback_url, $content_url, $content_title, $content_excerpt)) {
						$database->setQuery("INSERT INTO `#__jomcomment_tb_sent` SET `url`='$trackback_url', `contentid`='$content_id'");
						$database->query();
					}
				}
			}
		} else {
			return;
		}
	}
	/**
	 * Insert a new trackback entry
	 */	 	
	function trackbackPing() {
		global $database, $mosConfig_offset, $mosConfig_absolute_path, $_JC_CONFIG, $mosConfig_live_site;
		require ($mosConfig_absolute_path . '/administrator/components/com_jomcomment/class.jomcomment.php');
		if (!$_JC_CONFIG->get('enableTrackback'))
			return;
		
		# If the referrer is the same as the main site, that means user are clicking the
		# trackback. Output an error notice
		$referer = @ $_SERVER['HTTP_REFERER'];
		if (!empty ($referer) && !(strpos($referer, $mosConfig_live_site) === false)) {
			echo "To use trackback, copy the url below and use it in your trackback-enabled blogging tools. <br/><br/>";
			echo 'You can find out more about trackback <a href="http://en.wikipedia.org/wiki/Trackback">here</a><br/><br/>';
			echo '<fieldset><legend>trackback url</legend>';
			echo $mosConfig_live_site . "/index.php?" . $_SERVER['QUERY_STRING'];
			echo '</fieldset>';
			return;
		}
		$tb_request = "HTTP_POST_VARS";
		$tb_id = (isset ($_REQUEST["id"]) ? intval($_REQUEST["id"]) : 0);
		$tb = new mosJomcommentTb($database);
		$tb->bind($_POST);
		$tb->contentid = $tb_id;
		$tb->ip = $_SERVER['REMOTE_ADDR'];
		$tb->date = strftime("%Y-%m-%d %H:%M:%S", time() + ($mosConfig_offset * 60 * 60));
		$tb->title = stripslashes(mosGetParam($_POST, 'title', '...'));
		$tb->excerpt = stripslashes(mosGetParam($_POST, 'excerpt', '...'));
		$tb->url = stripslashes(mosGetParam($_POST, 'url', ''));
		$tb->blog_name = stripslashes(mosGetParam($_POST, 'blog_name', '...'));
		$tb->charset = mosGetParam($_POST, 'charset', 'iso-8859-1');
		$tb->option = mosGetParam($_POST, 'opt', 'com_content');
		if (!intval($tb_id)) {
			$this->_trackbackResponse(true, "I really need an ID for this to work.");
			exit;
		}
		if (!$_JC_CONFIG->get('enableTrackback')) {
			$this->_trackbackResponse(true, "Trackback disabled");
			exit;
		}
		if (!intval($tb_id)) {
			$this->_trackbackResponse(true, "I really need an ID for this to work.");
			exit;
		}
		
		# @rule: For com_content, do not add the comment if the content are unpublished
		if($tb->option == 'com_content'){
			if(jcContentPublished($tb->contentid) != 1){
				$this->_trackbackResponse(true, "You cannot add trackback to unpublished content");
				exit;
			}
		}
		
		# required field
		if (empty ($tb->url)) {
			$this->_trackbackResponse(true, "Please provide complete trackback data for this to work.");
			exit;
		}
		# Use Jom Comment keyword blocking on title and excerpt
		if ($_JC_CONFIG->get('blockWords')) {
			$words = explode(",", $_JC_CONFIG->get('blockWords'));
			array_walk($words, "jctrim");
			foreach ($words as $word) {
				if (!empty ($word)) {
					if (!empty ($tb->title)) {
						if (@ strpos($tb->title, $word) !== FALSE) {
							$this->_trackbackResponse(true, "Spam detected");
							exit;
						}
					}
					if (!empty ($tb->excerpt)) {
						if (@ strpos($tb->excerpt, $word) !== FALSE) {
							$this->_trackbackResponse(true, "Spam detected");
							exit;
						}
					}
				}
			}
		}
		# If user choose to use linkback instead, we need to verify that the sender's does 
		# have our url somewhere in the page they give
		if ($_JC_CONFIG->get('useLinkback')) {
			$pageContent = jc_post($tb->url, "");
			if ($pageContent) {
				if (!strpos($pageContent, $mosConfig_live_site)) {
					$this->_trackbackResponse(true, "A link to this website is required");
					exit;
				}
			} else {
				$this->_trackbackResponse(true, "A link to this website is required");
				exit;
			}
		}
	
		# we should only insert the trackback once.
		if ($this->_dataMgr->tbExist($tb_id, $tb->url)) {
			$this->_trackbackResponse(true, "We already have a ping from that URI for this post.");
		} else {
			if ($_JC_CONFIG->get('remoteSpam') && $_JC_CONFIG->get('akismetKey')) {
				$akismet = new Akismet($mosConfig_live_site, trim($_JC_CONFIG->get('akismetKey')));
				$akismet->setAuthor("");
				$akismet->setAuthorEmail("");
				$akismet->setAuthorURL("");
				$akismet->setContent($document);
				if ($akismet->isSpam()) {
					$this->_trackbackResponse(true, "We already have a ping from that URI for this post.");
					return true;
				}
			}
// 			# Apply remote spam filter if necessary
// 			if($_JC_CONFIG->get('remoteSpam')){
// 				$document = $tb->excerpt . " " . $tb->url;
// 				$data = "action=cat&type=comment&document=" . urlencode($document) . "&version=2";
// 				$return = jc_post("www.azrul.com/filter/index.php", $data);
// 				
// 				# The server will return text with spam/nonspam word enclosed in <spam> tag
// 				$isspam = "nonspam";
// 				$pattern = "'<spam>(.*)</spam>'s";
// 				preg_match($pattern, $this->fdata, $matches);
// 				if($matches){
// 					$isspam = @ $matches[1];
// 				}
// 				
// 				if($isspam == 'spam'){
// 					$tb->published = 0;
// 				}
// 			}
			$tb->store();
			$this->_trackbackResponse(false);
		}
	}
	function trackbackGetHTML($cid, $option) {
	}
	
	/**
	 * User report a post to admin. Send an email to the admin
	 */	 	
	function ajaxReport($id, $com, $referrer){
		include_once ($this->getPath('language'));
		$objResponse = new JAXResponse();
		$objResponse->addAlert("Site admin has been notified!");
		return $objResponse->sendResponse();
	}
}
$_JC_CONFIG = new JCConfig();
$_JOMCOMMENT = new JCMainFrame();
$_JCPROFILER = new JomProfiler('Jom Comment');
$_JC_UTF8 = new Utf8Helper();
