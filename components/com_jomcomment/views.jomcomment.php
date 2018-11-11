<?php

/**
 * Jom Comment 
 * @package JomComment
 * @copyright (C) 2006 by Azrul Rahim - All rights reserved!
 * @license Copyrighted Commercial Software
 * 
 * Responsible displaying the data given  
 **/

// Don't allow direct linking
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
global $maxMemUsage;

if (!function_exists('str_ireplace')) {
    function str_ireplace($needleA, $strA, $haystack) {
    	for($i = 0; $i < count($needleA); $i++){
    		$needle = $needleA[$i];
    		$str = $strA[$i];
    		@preg_replace("/$needle/i", $str, $haystack);
		}
        return $haystack;
    }
} 

if( !function_exists('memory_get_usage') ){
	function memory_get_usage(){
		global $maxMemUsage;
		//If its Windows
		//Tested on Win XP Pro SP2. Should work on Win 2003 Server too
		//Doesn't work for 2000
		//If you need it to work for 2000 look at http://us2.php.net/manual/en/function.memory-get-usage.php#54642
		if ( substr(PHP_OS,0,3) == 'WIN') {
		          $output = array();
		          exec( 'tasklist /FI "PID eq ' . getmypid() . '" /FO LIST', $output );
		       
		          $mem = preg_replace( '/[\D]/', '', $output[5] ) * 1024;
				  if($mem > $maxMemUsage)
				  	$maxMemUsage = $mem; 
		}else{
			//We now assume the OS is UNIX
			//Tested on Mac OS X 10.4.6 and Linux Red Hat Enterprise 4
			//This should work on most UNIX systems
			$pid = getmypid();
			exec("ps -eo%mem,rss,pid | grep $pid", $output);
			$output = explode("  ", $output[0]);
			//rss is given in 1024 byte units
			return $output[1] * 1024;
		}
	}
}



class JCView {

	var $_utf8 = null;

	function JCView() {
		// set up utf8 object
		$this->_utf8 = new Utf8Helper();
	}
	
	function decodeBracket($str){
		$search = array (
			"0x7B",
			"0x7D"
		);
		$replace = array (
			"{",
			"}"
		);
		$str = str_replace($search, $replace, $str);
		return $str;
	}
	
	# We need to encode the brackets character since it interfere with th JSON
	# eval	
	function encodeBracket($str){
		$search = array (
			"{",
			"}"
		);
		$replace = array (
			"0x7B",
			"0x7D"
		);
		return $str;
	}
	
	function _translateTemplate($text) {
		global $mosConfig_absolute_path, $_JC_CONFIG;

		include_once (JC_LANGUAGE . $_JC_CONFIG->get('language'));

		$word = array (
			"_JC_TPL_COMMENT_RSS_URI",
			"_JC_TPL_WRITE_COMMENT",
			"_JC_TPL_ADDCOMMENT",
			"_JC_TPL_AUTHOR",
			"_JC_TPL_EMAIL",
			"_JC_TPL_WEBSITE",
			"_JC_TPL_COMMENTS",
			"_JC_TPL_TITLE",
			"_JC_TPL_WRITTEN_BY",
			"_JC_TPL_READMORE",
			"_JC_TPL_COMMENT",
			"_JC_TPL_SEC_CODE",
			"_JC_TPL_SUBMIT_COMMENTS",
			"_JC_TPL_GUEST_MUST_LOGIN",
			"_JC_TPL_HIDESHOW_FORM",
			"_JC_TPL_REMEMBER_INFO",
			"_JC_TPL_SUBSCRIBE",
			"_JC_TPL_PAGINATE_NEXT",
			"_JC_TPL_PAGINATE_PREV",
			"_JC_TPL_NOSCRIPT",
			"_JC_TPL_INPUT_LOCKED",
			"_JC_TPL_TRACKBACK_URI",
			"_JC_TPL_HIDESHOW_AREA"
		);
		$utf = new Utf8Helper();
		$replacement = array (
			$utf->utf8ToHtmlEntities(_JC_TPL_COMMENT_RSS_URI),
			$utf->utf8ToHtmlEntities(_JC_TPL_WRITE_COMMENT), 
			$utf->utf8ToHtmlEntities(_JC_TPL_ADDCOMMENT), 
			$utf->utf8ToHtmlEntities(_JC_TPL_AUTHOR), 
			$utf->utf8ToHtmlEntities(_JC_TPL_EMAIL), 
			$utf->utf8ToHtmlEntities(_JC_TPL_WEBSITE), 
			$utf->utf8ToHtmlEntities(_JC_TPL_COMMENTS), 
			$utf->utf8ToHtmlEntities(_JC_TPL_TITLE), 
			$utf->utf8ToHtmlEntities(_JC_TPL_WRITTEN_BY), 
			$utf->utf8ToHtmlEntities(_JC_TPL_READMORE), 
			$utf->utf8ToHtmlEntities(_JC_TPL_COMMENT), 
			$utf->utf8ToHtmlEntities(_JC_TPL_SEC_CODE), 
			$utf->utf8ToHtmlEntities(_JC_TPL_SUBMIT_COMMENTS), 
			$utf->utf8ToHtmlEntities(_JC_TPL_GUEST_MUST_LOGIN), 
			$utf->utf8ToHtmlEntities(_JC_TPL_HIDESHOW_FORM), 
			$utf->utf8ToHtmlEntities(_JC_TPL_REMEMBER_INFO), 
			$utf->utf8ToHtmlEntities(_JC_TPL_SUBSCRIBE), 
			$utf->utf8ToHtmlEntities(_JC_TPL_PAGINATE_NEXT), 
			$utf->utf8ToHtmlEntities(_JC_TPL_PAGINATE_PREV), 
			$utf->utf8ToHtmlEntities(_JC_TPL_NOSCRIPT),
			$utf->utf8ToHtmlEntities(_JC_TPL_INPUT_LOCKED),
			$utf->utf8ToHtmlEntities(_JC_TPL_TRACKBACK_URI),
			$utf->utf8ToHtmlEntities(_JC_TPL_HIDESHOW_AREA));

		$text = str_replace($word, $replacement, $text);

		return $text;
	}

	# shorten long URL
	function shortenURL($ret){
		
		# Need to pad with a space so that the regex works
		$ret = ' ' . $ret;
		$ret = preg_replace("#(^|[\n ])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "$1<a href='$2' rel='nofollow'>$2</a>", $ret);
		$ret = preg_replace("#(^|[\n ])((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "$1<a href='http://$2' rel='nofollow'>$2</a>", $ret);
		$this->_shortenURL($ret);
		$ret = preg_replace("#(\s)([a-z0-9\-_.]+)@([^,< \n\r]+)#i", "$1<a href=\"mailto:$2@$3\">$2@$3</a>", $ret);	
		$ret = substr($ret, 1);
		return($ret);
	}

	# only shorten the url enclosed within <a tags, we assume the link has been properly created
	function _shortenURL(&$ret){
	   $links = explode('<a', $ret);
	   $countlinks = count($links);
	   for ($i = 0; $i < $countlinks; $i++){
			$link = $links[$i];			
			$link = (preg_match('#(.*)(href=")#is', $link)) ? '<a' . $link : $link;
	
			$begin = strpos($link, '>') + 1;
			$end = strpos($link, '<', $begin);
			$length = $end - $begin;
			$urlname = substr($link, $begin, $length);

			$chunked = (strlen($urlname) > 50 && preg_match('#^(http://|ftp://|www\.)#is', $urlname)) ? substr_replace($urlname, '...', 30, -10) : $urlname;
			$ret = str_replace('>' . $urlname . '<', '>' . $chunked . '<', $ret); 
	
	   }
	} 	
	
	
	# Prep the data for 1 comment for display
	function prepData(&$data, $item_num, $cssClass, $addAdminPanel = true) {
		global $database, $option, $my, $task, $mosConfig_live_site, $mosConfig_absolute_path, $Itemid, $_JC_CONFIG;
		@include_once ($mosConfig_absolute_path . "/components/com_jomcomment/languages/" . $_JC_CONFIG->get('language'));

		$pcTemplatePath = $mosConfig_absolute_path . "/components/com_jomcomment/templates/";
		$pcTemplate_comment = $pcTemplatePath . $_JC_CONFIG->get('template');
		$pcSmileyPath = $mosConfig_live_site . "/components/com_jomcomment/templates/images/";

		$data->adminPanel = "";
		$data->gravatar = "";
		$data->comment = jc_nl2brStrict($data->comment);
		$data->comment = $this->encodeBracket($data->comment);

		# Process BBcode tags and smilies
		if ($_JC_CONFIG->get('useBBCode')) {
			if (!class_exists('HTML_BBCodeParser') AND !function_exists('BBCode')) {
				include_once ($mosConfig_absolute_path . "/components/com_jomcomment/bbcode.php");
			}

			$data->comment = BBCode($data->comment);
			if($_JC_CONFIG->get('useSmilies')){
				$data->comment = jcDecodeSmilies($data->comment);
			}
			
		} else {
			$data->comment = jcStripBBCodeTag($data->comment);
		}

		# set up text wrapping for super long word
		if ($_JC_CONFIG->get('textWrap')) {
			$data->comment = jcTextwrap($data->comment, 55);
			$data->title = jcTextwrap($data->title, 55);
		}

		# Fix utf-8 data
		if (jcValid_utf8($data->comment)) {
			$data->comment = $this->_utf8->utf8ToHtmlEntities($data->comment);
		}

		if (jcValid_utf8($data->name)) {
			$data->name = $this->_utf8->utf8ToHtmlEntities($data->name);
		}

		if (jcValid_utf8($data->title)) {
			$data->title = $this->_utf8->utf8ToHtmlEntities($data->title);
		}

		if (empty ($data->title))
			$data->title = "...";
		if (empty ($data->name))
			$data->name = $this->_utf8->utf8ToHtmlEntities( _JC_GUEST_NAME );

		$isAdmin = (strtolower($my->usertype) == 'editor' || strtolower($my->usertype) == 'publisher' || strtolower($my->usertype) == 'manager' || strtolower($my->usertype) == 'administrator' || strtolower($my->usertype) == 'super administrator');

		
		/*$template->assign("{adminPanel}", "");
		if ($isAdmin) 
		{
			//$template->assign("{adminPanel}", "<div class=\"jcAdminPanel\" id=\"jc_adminPanel_pc_$item->id\"><a href=\"#\" onclick=\"jc_unpublishPost('{pcId}');return false;\">Unpublish</a></div>");
		}
		*/

		# create avatar link		
		if($_JC_CONFIG->get('gravatar') != 'none'){
			$avLinkStart = "";
			$avLinkEnd = "";
			$grav_link = $this->_getAvatarLink($data);
		
			switch ($_JC_CONFIG->get('gravatar')) {
				case "website" :
					$link = preg_replace('/(http:\/\/)([a-zA-Z-_\.\?\&]+)/', '', $data->website);
					if ($link) {
						$avLinkStart = '<a href="http://' . $grav_link . '" target="_blank" rel="nofollow" >';
						$avLinkEnd = "</a>";
					}
	
					break;
					
				case "smf":
				case "cb" :
					if ($data->user_id) {
						$avLinkStart = "<a href=\"$grav_link\">";
						$avLinkEnd = "</a>";
					} 
					break;
				default :
					break;
			}
	
			# add avatar image gravatar
			$width = ($_JC_CONFIG->get('gWidth')) ? "width=\"" . intval($_JC_CONFIG->get('gWidth')) . "\" " : "";
			$height = ($_JC_CONFIG->get('gHeight')) ? "height=\"" . intval($_JC_CONFIG->get('gHeight')) . "\" " : "";
	
			$grav_url = $this->_getAvatarImg($data);
			if(!empty($grav_url)){	
				$data->gravatar = "<div class=\"avatarImg\">$avLinkStart<img src=\"$grav_url\" $width $height alt=\"\" border=\"0\"/>$avLinkEnd</div>"; 
			}
		}
		

		# Add website link
		if (!empty ($data->website)) {
			if ($data->website == "#") {
				$data->website = "";
			} else {
				# if http:// is missing, add it
				if (strpos($data->website, "http") === false)
					$data->website = "http://" . $data->website;
			}
		}

		# Reformat the date
		if ($_JC_CONFIG->get('dateFormat')) {
			$data->date = @ strftime($_JC_CONFIG->get('dateFormat'), strtotime($data->date));
		}

		$data->style = $cssClass;
		$data->itemNum = $item_num;

		if ($isAdmin AND $addAdminPanel) {
			$data->adminPanel = '<div class="jcAdminPanel" id="jc_adminPanel_pc_{id}">
						            	<a href="#" onclick="jax.call(\'jomcomment\', \'jcxEdit\', \'{id}\');return false;">Edit</a>&nbsp;|&nbsp;
						                <a href="#" onclick="jc_unpublishPost(\'pc_{id}\', \'{option}\', \'{id}\');return false;">Unpublish</a>&nbsp;
						            </div><div id="pc_edit_{id}">';

			$data->adminPanel = str_replace('{id}', $data->id, $data->adminPanel);
			$data->adminPanel = str_replace('{option}', $data->option, $data->adminPanel);
		}

		$data->comment = str_replace("href='www", "href='http://www", $data->comment);

		# re=nofollow
		if ($_JC_CONFIG->get('linkNofollow')) {
			$data->comment = str_replace("href=", "rel=\"nofollow\" href=", $data->comment);
		}

		# close admins' edit block
		if ($isAdmin && $addAdminPanel) {
			$data->adminPanel .= '</div>';
		}
		
		unset ($template);
		return stripslashes($data->comment);
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
	
	
	# Return BBCODE toolbar 
	function getBBCodeToolbar($path){
		global $_JC_CONFIG;
		
		$smilies  = array (
			":)"     => $path . "smilies/smiley-toolbar.gif",
			";)"     => $path . "smilies/wink-toolbar.gif",
			":D"     => $path . "smilies/cheesy-toolbar.gif",
			";D"     => $path . "smilies/grin-toolbar.gif",
			">:("    => $path . "smilies/angry-toolbar.gif",
			":("     => $path . "smilies/sad-toolbar.gif",
			":o"     => $path . "smilies/shocked-toolbar.gif",
			"8)"     => $path . "smilies/cool-toolbar.gif",
			":P"     => $path . "smilies/tongue-toolbar.gif",
			":-\\\\" => $path . "smilies/undecided-toolbar.gif",
			":-*"    => $path . "smilies/kiss-toolbar.gif",
			":\'("   => $path . "smilies/cry-toolbar.gif"
		);

		$bbCodeToolbar = '<div id="bb_container"><div id="bb_main">';
		
		$bbCodeToolbar .= '<div class="bb_item bb_itemImg" style="width:13px;"><img class="bb_itemImg" src="'. $path . 'smilies/bbcode_front.gif" alt="quote" border="0"/></div>';
		$bbCodeToolbar .= '<div class="bb_item"><a href="javascript:void(0);" tabindex="-1" onclick="jc_encloseText(\'[b]\', \'[/b]\', jax.$(\'jc_comment\')); return false;"><img class="bb_itemImg" src="'. $path . 'smilies/bold.gif" alt="bold"   border="0"/></a></div>';
		$bbCodeToolbar .= '<div class="bb_item"><a href="javascript:void(0);" tabindex="-1" onclick="jc_encloseText(\'[i]\', \'[/i]\', jax.$(\'jc_comment\')); return false;"><img class="bb_itemImg" src="'. $path . 'smilies/italicize.gif" alt="italicize"  border="0"/></a></div>';
		$bbCodeToolbar .= '<div class="bb_item"><a href="javascript:void(0);" tabindex="-1" onclick="jc_encloseText(\'[u]\', \'[/u]\', jax.$(\'jc_comment\')); return false;"><img class="bb_itemImg" src="'. $path . 'smilies/underline.gif" alt="underline" border="0"/></a></div>';
		$bbCodeToolbar .= '<div class="bb_item"><a href="javascript:void(0);" tabindex="-1" onclick="jc_encloseText(\'[s]\', \'[/s]\', jax.$(\'jc_comment\')); return false;"><img class="bb_itemImg" src="'. $path . 'smilies/strike.gif" alt="strike" border="0"/></a></div>';
		$bbCodeToolbar .= '<div class="bb_item"><a href="javascript:void(0);" tabindex="-1" onclick="jc_encloseText(\'[url]\', \'[/url]\', jax.$(\'jc_comment\')); return false;"><img class="bb_itemImg" src="'. $path . 'smilies/url.gif" alt="url" border="0"/></a></div>';
		$bbCodeToolbar .= '<div class="bb_item"><a href="javascript:void(0);" tabindex="-1" onclick="jc_encloseText(\'[img]\', \'[/img]\', jax.$(\'jc_comment\')); return false;"><img class="bb_itemImg" src="'. $path . 'smilies/img.gif" alt="image"  border="0"/></a></div>';
		$bbCodeToolbar .= '<div class="bb_item"><a href="javascript:void(0);" tabindex="-1" onclick="jc_encloseText(\'[quote]\', \'[/quote]\', jax.$(\'jc_comment\')); return false;"><img class="bb_itemImg" src="'. $path . 'smilies/quote.gif" alt="quote"  border="0"/></a></div>';
        $bbCodeToolbar .= '<div class="bb_item bb_itemImg" style="width:13px;"><img class="bb_itemImg" src="'. $path . 'smilies/bbcode_front.gif" alt="quote" border="0"/></div>';

		if($_JC_CONFIG->get('useSmilies')){
			foreach ($smilies as $key => $value) {
				$bbCodeToolbar .= '<div class="bb_item"><a href="javascript:void(0);" tabindex="-1" onclick="jc_addText(\' ' . $key . '\', \'jc_comment\'); return false;"><img class="bb_itemImg" src="' . $value . '" alt="Smiley" title="Smiley" border="0" /></a></div>';
			}
		}

		
		$bbCodeToolbar .= "</div></div>";
		return $bbCodeToolbar;
	}
	
	
	# Return just the (HTML formatted) comment part of jom comment
	function getCommentsHTML(&$data){
		global $_JC_CONFIG, $_JCPROFILER, $_JOMCOMMENT;
		global $mosConfig_cachepath, $mosConfig_live_site, $mosConfig_absolute_path, $my, $mosConfig_debug;
		
		//$comments_tpl 	= new Template(); // this is the inner template
		$cacheid = "";
		if(is_array($data)){
			$cacheid = strval(count($data));
		}
		
		$_JCPROFILER->mark('Serialize:');
		$comments_tpl 	= new AzrulJXCachedTemplate(serialize($data). $my->id .$_JC_CONFIG->get('template') );
		$_JCPROFILER->mark('Building Jom COmment:');
		
		if(!$comments_tpl->is_cached()){
			$dataArray = array();
			$styleOffset = 0;
			$createdBy	 = 0;
			$_JCPROFILER->mark('Load List:');
			
			# The data could be an array or a single data. Convert it all to array. 
			# if it is a single data, we need to make sure we get the corret style
			if(is_array($data)){
				$dataArray = $data;
			} else if(isset($data)){
				$dataArray = array();
				$dataArray[] = $data;
				$numComment  = $_JOMCOMMENT->_dataMgr->getNumComment($data->contentid, $data->option);
				$styleOffset = intval(!($numComment & 1));
			}else {
				$dataArray = array();
			}
			
			# If the data is for "com_content", we need to find the content author 
			# to apply the author specific css
			if($dataArray)
				if($dataArray[0]->option == 'com_content'){
					$createdBy = jcContentAuthorGet($dataArray[0]->contentid);
				}							
							
			
			$styles = explode(",", $_JC_CONFIG->get('cycleStyle'));
			array_walk($styles, "jctrim");
			$numStyle = count($styles);
			$styleCount = 1;
			$size =  @count($dataArray);
			for($i = 0; $i < $size; $i++) {
				$style = ($styleCount+  $styleOffset) % $numStyle;
				$style = $styles[$style];
				if (isset($dataArray[$i]->created_by) && ($dataArray[$i]->user_id != 0) && ($dataArray[$i]->user_id == $dataArray[$i]->created_by)) {
					$style .= " ". $_JC_CONFIG->get('authorStyle');
				}
				
				$this->prepData($dataArray[$i], $i+1, $style);
				$dataArray[$i]->style = $style;
				
				$dataArray[$i]->comment = $this->censorText(stripslashes($dataArray[$i]->comment));
				$dataArray[$i]->title   = $this->censorText($dataArray[$i]->title);
				$dataArray[$i]->name    = $this->censorText($dataArray[$i]->name);
				
				# apply the shorted URL filter
				$dataArray[$i]->comment = $this->shortenURL($dataArray[$i]->comment);
				$dataArray[$i]->title 	= $this->shortenURL($dataArray[$i]->title);				
				
				$styleCount++;
			}
			
			// Make sure $dataArray is not empty
			if(!$dataArray){
				$dataArray = array();
			}
			$comments_tpl->set('adminPanel', ""); 
			$comments_tpl->set('comments', $dataArray);
			$comments_tpl->set('debugview', false);
		}
		
		
		$html = $comments_tpl->fetch_cache(JC_BOT_PATH . "templates/" .$_JC_CONFIG->get('template') .'/comment.tpl.html');
		$_JCPROFILER->mark('Fetch Cache:');
		$html = trim($html); 
		
		# Censored code cannot be applied here since it will affect the untranslated
		# test as well		
		//$html = $this->censorText($html);
		$html = $this->_translateTemplate($html);
		return $html;
	}

	/**
	 * Process all the comments
	 */
	function prepAll($dataArray, $cid, $option, $contentObj) {
		global $_JC_CONFIG, $_JCPROFILER, $_JOMCOMMENT, $Itemid;
		global $mosConfig_cachepath, $mosConfig_live_site, $mosConfig_absolute_path, $my, $mosConfig_debug;
		
		$tpl 			=  new AzrulJXTemplate(); // this is the outer template
		
		$comments  = array();
		$comments['count']	= count($dataArray);
		
		# Hide/show settings
		$show = array();
		$show['name'] 		= $_JC_CONFIG->get('moreInfo');
		$show['email'] 		= $_JC_CONFIG->get('fieldEmail');
		$show['title'] 		= $_JC_CONFIG->get('fieldTitle');
		$show['website'] 	= $_JC_CONFIG->get('fieldWebsite');
		$show['feed'] 		= $_JC_CONFIG->get('useRSSFeed');
		$show['trackback'] 	= $_JC_CONFIG->get('enableTrackback');
		$show['bbcode']		= $_JC_CONFIG->get('useBBCode');
		$show['useSmilies'] = $_JC_CONFIG->get('useSmilies');
		$show['captcha']	= $my->username ? $_JC_CONFIG->get('useCaptchaRegistered') : $_JC_CONFIG->get('useCaptcha'); 
		$show['allow_guest']= !empty($my->username) ? true : $_JC_CONFIG->get('anonComment');
		$show['inputform']	= true;
		$show['last']		=  
		
		$show['hide_show_form']		= $_JC_CONFIG->get('slideForm');
		$show['hide_show_comment']	= $_JC_CONFIG->get('slideComment');
		
		$show['start_form_hidden']		= $_JC_CONFIG->get('startFormHidden');
		$show['start_comment_hidden']	= $_JC_CONFIG->get('startAreaHidden');
		
		
		# Content related info
		$doc = array();		
		$doc['option']		= $option;
		$doc['id']			= $cid;
		
		# Captcha info
		$captcha = array();
		$captcha['show']	= $show['captcha'];
		$captcha['sid']	 	= $_JOMCOMMENT->getSid();
		$captcha['img']		= $mosConfig_live_site . "/index2.php?option=com_jomcomment&amp;jc_task=img&amp;jc_sid=" . $captcha['sid'];	
			
		# Rss FEED
		$feed = array();
		$feed['show']		= $show['feed'];
		$feed['link']		= sefRelToAbs($mosConfig_live_site . "/index.php?option=com_jomcomment&amp;jc_task=rss&amp;contentid=$cid&opt=$option");
		
		# $my object, just use $my
		$my_arr = array();
		$my_arr = $this->object_to_array($my);
		
		# Locking
		$lock = array();
		$lock['locked'] = strpos($contentObj->text, "{jomcomment_lock}") || strpos($contentObj->text, "{jomcomment lock}");
		$lock['date']	= false;
		
		# Site information
		$site = array();
		$site['live_site'] = $mosConfig_live_site;
		$site['site_path'] = $mosConfig_absolute_path;
		$site['template_path'] 	= "";
		$site['com_path']		= "";
		$site['bot_path']		= "";
		$site['id']				= $cid;
		$site['option']			= $option;
		
		# Trackbacks
		$trackback['show'] 		= $show['trackback'];
		$trackback['text']		= $this->tbPrepAll($_JOMCOMMENT->_dataMgr->tbGetAll($cid, $option), $cid, $option);
		$trackback['link']		= sefRelToAbs($mosConfig_live_site . "/index.php?option=com_jomcomment&task=trackback&id=$cid&opt=$option");
		$trackback['count']		= $_JOMCOMMENT->_dataMgr->tbGetCount($cid, $option);
		
		$show['inputform']	= (!$lock['locked'] && ($show['allow_guest'])) || 
							( !$show['allow_guest'] && !empty($my->username));
		# If we're in printing mode pop=1, remove comment form
		if(isset($_GET['pop'])) $show['inputform'] = false;		
		
		# Add pagination if necessary, We need to strip the data if pagination is active
// 				
// 		if($_JC_CONFIG->get('paging') && ($comments['count'] > $_JC_CONFIG->get('paging'))){
// 			include_once ($mosConfig_absolute_path . "/includes/pageNavigation.php");
// 			
// 			$total = $comments['count'];
// 			$limitstart = mosGetParam($_GET, 'limitstart', 0);		
// 			$limit = mosGetParam($_GET, 'limit', $_JC_CONFIG->get('paging'));
// 			$dataArray = array_slice($dataArray, $limitstart, $limitstart + $limit);
// 			
// 			$comments['text']	= $this->getCommentsHTML($dataArray);
// 			
// 			$queryString = $_SERVER['QUERY_STRING'];
// 			$queryString = preg_replace("/\&limit=[0-9]*/i", "", $queryString);
// 			$queryString = preg_replace("/\&limitstart=[0-9]*/i", "", $queryString);
// 			
// 			$pageNav = new mosPageNav($total, $limitstart, $limit);
// 			$comments['text'] .= '<div id="jcPaging">' . $pageNav->writePagesLinks(sefRelToAbs('index.php?' . $queryString)) . '</div>';
// 			
// 			# If we're not at the last page, do not show the input form
// 			if($total > ($limitstart + $limit)){			
// 				$show['inputform']	= false;		
// 			}
// 						
// 		} else {
// 			$comments['text']	= $this->getCommentsHTML($dataArray);
// 		}
		$comments['text']	= $this->getCommentsHTML($dataArray);
		$tpl->set('feed', $feed);
		$tpl->set('show', $show);
		$tpl->set('captcha', $captcha);
		$tpl->set('my', $my_arr);
		$tpl->set('lock', $lock);
		$tpl->set('site', $site);
		$tpl->set('trackback', $trackback);
		$tpl->set('doc', $doc);
		
		$tpl->set('bbcode', $this->getBBCodeToolbar(JC_BOT_LIVEPATH));
		$tpl->set('comments', $comments);
		$tpl->set('debugview', false);
		$content = "";
		
		if(file_exists(JC_BOT_PATH . "/templates/" .$_JC_CONFIG->get('template') .'/index.tpl.html'))
			$content = trim($tpl->fetch(JC_BOT_PATH . "templates/" .$_JC_CONFIG->get('template') .'/index.tpl.html'));
		else
			$content = trim($tpl->fetch(JC_BOT_PATH . "templates/_default/index.tpl.html"));
		
		if ($mosConfig_debug) {
			$content .= $_JCPROFILER->getHTML();
		}
		$_JCPROFILER->mark('Finish List:');
		
		$html = $this->_translateTemplate($content);
		return $this->_cleaupOutput($html);
		
	}
	
	function _cleaupOutput($html){
		global $mosConfig_live_site, $_JC_CONFIG;
		
		# Clean up the content
		$search = array ("&lt;", "&gt;");
		$replace = array ("<", ">");
		$html = str_replace($search, $replace, $html);
		
		# Change relative image url to template folder
		$images_path = "src=\"" . $mosConfig_live_site . "/components/com_jomcomment/templates/". $_JC_CONFIG->get('template')."/";
		$html = str_replace("src=\"", $images_path, $html);
		$html = str_replace($images_path . "http", "src=\"http", $html);
		$html = str_replace("src=\"images/", "src=\"". JC_BOT_PATH ."templates/".$_JC_CONFIG->get('template')."/images/", $html);
		$html = jcFixLiveSiteUrl($html);	
		
		return $html;
	}
	
	function tbPrepAll($dataArray, $cid, $option){
		global $_JC_CONFIG, $_JCPROFILER, $mosConfig_live_site;
		
		$trackbackLink = sefRelToAbs($mosConfig_live_site . "/index.php?option=com_jomcomment&task=trackback&id=$cid&opt=$option");
		
		$html = "";
		$tpl 	= new AzrulJXTemplate();
		
		
		$size = count($dataArray);
		$dateformat = $_JC_CONFIG->get('dateFormat');
		for($i = 0; $i < $size; $i++){
			$dataArray[$i]->num = $i+1;
			
			# Reformat the date
			$dataArray[$i]->date = @ strftime($_JC_CONFIG->get('dateFormat'), strtotime($dataArray[$i]->date));
		}
		
		$tpl->set('trackbacks', $dataArray);
		$tpl->set('trackback_link', $trackbackLink);
		$tpl->set('debugview', false);
		
		if(file_exists(JC_BOT_PATH . "templates/" .$_JC_CONFIG->get('template') .'/trackback.tpl.html'))
			$html = $tpl->fetch(JC_BOT_PATH . "templates/" .$_JC_CONFIG->get('template') .'/trackback.tpl.html');
		else
			$html = $tpl->fetch(JC_BOT_PATH . "templates/_default/trackback.tpl.html");
			
		return $html;

	}

	/**
	 * Return the blog view HTML codes
	 */
	function getBlogView($cid) {

	}
	
	
	/**
	 * Return the gravatar link
	 */	 	
	function _getAvatarLink($data) {
		global $_JC_CONFIG, $mosConfig_live_site, $database, $Itemid, $mosConfig_db, $mosConfig_absolute_path;
		
		$link = "";
		switch ($_JC_CONFIG->get('gravatar')) {
			case "website" :
				$link = preg_replace('/(http:\/\/)([a-zA-Z-_\.\?\&]+)/', '', $data->website);
				break;
				
			case "cb" :
				if ($data->user_id) {
					$link = sefRelToAbs("index.php?option=com_comprofiler&task=userProfile&user=$data->user_id&Itemid=$Itemid");
				}
				break;
				
			case "smf":
				
				$smfPath = $_JC_CONFIG->get('smfPath');
				if (substr($smfPath, strlen($smfPath) - 1, 1) == "/")
				$smfPath = substr($smfPath, 0, strlen($smfPath) - 1);
				if (!$smfPath or $smfPath == "" or !file_exists("$smfPath/Settings.php")) {
					$database->setQuery("select id from #__components WHERE `option`='com_smf'");
					if ($database->loadResult()) {
						$database->setQuery("select value1 from #__smf_config WHERE variable='smf_path'");
						$smfPath = $database->loadResult();
						$smfPath = str_replace("\\", "/", $smfPath);
						$smfPath = rtrim($smfPath, "/");
					}
				}
				if (!$smfPath or $smfPath == "" or !file_exists("$smfPath/Settings.php"))
					$smfPath = "$mosConfig_absolute_path/forum";
				if (file_exists("$smfPath/Settings.php")) {
					include("$smfPath/Settings.php");
					mysql_select_db($db_name, $database->_resource);
					$useremail = $data->email;
					$q = sprintf("SELECT ID_MEMBER FROM $db_prefix" . "members WHERE emailAddress='%s'", mysql_real_escape_string($useremail));
					$result = mysql_query($q);
					$result_row = mysql_fetch_array($result);
					mysql_select_db($mosConfig_db, $database->_resource);
					if ($result_row) {
						$database->setQuery("select id from #__components WHERE `option`='com_smf'");
						$smfWrap = "";
						if ($database->loadResult()) {
							$database->setQuery("SELECT value1 from #__smf_config WHERE variable='wrapped'");
							$smfWrap = $database->loadResult();
							if ($smfWrap == "true") {
								$smfWrap = "1";
							} else
								$smfWrap = "";
						}
						if ($_JC_CONFIG->get('smfWrapped'))
							$smfWrap = "1";
						if ($smfWrap and $smfWrap != "") {
							$link = sefRelToAbs("index.php?option=com_smf&action=profile&u=" . $result_row[0] . "&Itemid=$Itemid");
						} else
							$link = sefRelToAbs($boardurl . "/index.php?action=profile&u=" . $result_row[0]);
					}
				} 
				
				break;
				
			default :
				break;
		}
		
		return $link;
	}
	
	# Apply the word censor to the given text
	function censorText($text){
		global $_JC_CONFIG;
		
		if ($_JC_CONFIG->get('censoredWords')) {
			$censoredWords = explode(",", $_JC_CONFIG->get('censoredWords'));
			array_walk($censoredWords, "jctrim");
			$replaceWords = $censoredWords;
			$count = 0;
			foreach ($replaceWords as $word) {
				$cword = "";
				$word = trim($word);
				
				// Only word longer than 2 character wil be censored
				if(isset($word) && strlen($word) > 2){
					for ($i = 0; $i < @strlen($word); $i++)
						$cword .= "*";
	
					$cword[0] = @$word[0];
					$cword[strlen($word) - 1] = @$word[strlen($word) - 1];
					$replaceWords[$count] = $cword;
					$count++;
				}
			}

			$count = 0;
			foreach ($censoredWords as $word) {
				$word = trim($word);
				
				// Only word longer than 2 character wil be censored
				if(isset($word) && @strlen($word) > 2){
					$censoredWords[$count] = $word;
					$count++;
				}
			}
			
			if(is_array($text)){
				for($i = 0; $i < count($i); $i++){
					$text[$i] = str_ireplace($censoredWords, $replaceWords, $text[$i] );
				}
			} else
				$text = str_ireplace($censoredWords, $replaceWords, $text);
		}
		
		return $text;
	}
	
	/**
	 * Return the gravatar image link
	 */	 	
	function _getAvatarImg($data) {
		global $_JC_CONFIG, $mosConfig_live_site, $grav_link, $database, $mosConfig_absolute_path,
				$mosConfig_db;
		
		$grav_url = $mosConfig_live_site . "/components/com_jomcomment/smilies/guest.gif";
		
		$gWidth = ($_JC_CONFIG->get('gWidth')) ? intval($_JC_CONFIG->get('gWidth')) : "";
		$gHeight = ($_JC_CONFIG->get('gHeight')) ? intval($_JC_CONFIG->get('gHeight')) : "";
		
		switch ($_JC_CONFIG->get('gravatar')) {
			case "gravatar" :
				$gWidth = ($gWidth) ? $gWidth : "40";
				$grav_url = "http://www.gravatar.com/avatar.php?gravatar_id=" . md5($data->email) .
				"&default=" . urlencode($mosConfig_live_site . "/components/com_jomcomment/smilies/guest.gif") .
				"&size=$gWidth";

				break;
				
			case "cb" :
				$database->setQuery("SELECT avatar FROM #__comprofiler WHERE user_id=" . $data->user_id . " AND avatarapproved=1");
				$result = $database->loadResult();
				if ($result) {
					// CB might store the images in either of this 2 folder
					if (file_exists($mosConfig_absolute_path . "/components/com_comprofiler/images/" . $result))
						$grav_url = $mosConfig_live_site . "/components/com_comprofiler/images/" . $result;
					else
						if (file_exists($mosConfig_absolute_path . "/images/comprofiler/" . $result))
							$grav_url = $mosConfig_live_site . "/images/comprofiler/" . $result;
				} 
				break;
				
			case "smf" :
				$smfPath = $_JC_CONFIG->get('smfPath');
				$smfPath = trim($smfPath);
				$smfPath = rtrim($smfPath, '/');
				if (!$smfPath or $smfPath == "" or !file_exists("$smfPath/Settings.php"))
					$smfPath = "$mosConfig_absolute_path/forum";
				if (!$smfPath or $smfPath == "" or !file_exists("$smfPath/Settings.php")) {
					$database->setQuery("select id from #__components WHERE `option`='com_smf'");
					if ($database->loadResult()) {
						$database->setQuery("select value1 from #__smf_config WHERE variable='smf_path'");
						$smfPath = $database->loadResult();
						$smfPath = str_replace("\\", "/", $smfPath);
						$smfPath = rtrim($smfPath, "/");
					}
				}
				if (file_exists("$smfPath/Settings.php")) {
					include("$smfPath/Settings.php");
					mysql_select_db($mosConfig_db, $database->_resource);
					$useremail = $data->email;
					mysql_select_db($db_name, $database->_resource);
					$q = sprintf("SELECT avatar,ID_MEMBER FROM $db_prefix" . "members WHERE emailAddress='$useremail'");
					$result = mysql_query($q);
					if ($result)
					{
						$result_row = mysql_fetch_array($result);
						mysql_select_db($mosConfig_db, $database->_resource);
						if ($result_row) {
							$id_member = $result_row[1];
							if (trim($result_row[0]) != "") {
								if (substr($result_row[0], 0, 7) != "http://")
									$grav_url = $boardurl . "/avatars/$result_row[0]";
								else
									$grav_url = $result_row[0];
							} else {
								mysql_select_db($db_name);
								$q = sprintf("SELECT ID_ATTACH FROM $db_prefix" . "attachments WHERE ID_MEMBER='$id_member' and ID_MSG=0 and attachmentType=0");
								$result = mysql_query($q);
								if ($result)
								{
									$result_avatar = mysql_fetch_array($result);
									mysql_select_db($mosConfig_db, $database->_resource);
									if ($result_avatar[0])
										$grav_url = "$boardurl/index.php?action=dlattach;attach=" . $result_avatar[0] . ";type=avatar";
								}
							}
						}
					}
				} 
				
				break;
			default :
				break;
		}
		
		return $grav_url;
	}

	function _truncateLink($url, $mode = '0', $trunc_before = '', $trunc_after = '...') {
		if (1 == $mode) {
			$url = preg_replace("/(([a-z]+?):\\/\\/[A-Za-z0-9\-\.]+).*/i", "$1", $url);
			$url = $trunc_before . preg_replace("/([A-Za-z0-9\-\.]+\.(com|org|net|gov|edu|us|info|biz|ws|name|tv|eu)).*/i", "$1", $url) . $trunc_after;
		}
		elseif (($mode > 10) && (strlen($url) > $mode)) {
			$url = $trunc_before . substr($url, 0, $mode) . $trunc_after;
		}
		return $url;
	}

	/**
	 * mode: 0=full url; 1=host-only ;11+=number of characters to truncate after
	 */
	function _hyperlinkUrls($text, $mode = '0', $trunc_before = '', $trunc_after = '...', $open_in_new_window = true) {
		$text = ' ' . $text . ' ';
		$new_win_txt = ($open_in_new_window) ? ' target="_blank"' : '';

		# Hyperlink Class B domains
		$text = preg_replace("#([\s{}\(\)\[\]])([A-Za-z0-9\-\.]+)\.(com|org|net|gov|edu|us|info|biz|ws|name|tv|eu|mobi)((?:/[^\s{}\(\)\[\]]*[^\.,\s{}\(\)\[\]]?)?)#ie", "'$1<a href=\"http://$2.$3$4\" title=\"http://$2.$3$4\"$new_win_txt>' . $this->_truncateLink(\"$2.$3$4\", \"$mode\", \"$trunc_before\", \"$trunc_after\") . '</a>'", $text);

		# Hyperlink anything with an explicit protocol
		$text = preg_replace("#([\s{}\(\)\[\]])(([a-z]+?)://([A-Za-z_0-9\-]+\.([^\s{}\(\)\[\]]+[^\s,\.\;{}\(\)\[\]])))#ie", "'$1<a href=\"$2\" title=\"$2\"$new_win_txt>' . $this->_truncateLink(\"$4\", \"$mode\", \"$trunc_before\", \"$trunc_after\") . '</a>'", $text);

		# Hyperlink e-mail addresses
		$text = preg_replace("#([\s{}\(\)\[\]])([A-Za-z0-9\-_\.]+?)@([^\s,{}\(\)\[\]]+\.[^\s.,{}\(\)\[\]]+)#ie", "'$1<a href=\"mailto:$2@$3\" title=\"mailto:$2@$3\">' . $this->_truncateLink(\"$2@$3\", \"$mode\", \"$trunc_before\", \"$trunc_after\") . '</a>'", $text);

		return substr($text, 1, strlen($text) - 2);
	}
	
	
}
