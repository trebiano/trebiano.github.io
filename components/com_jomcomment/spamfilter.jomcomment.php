<?php


/**
 * Jom Comment 
 * @package JomComment
 * @copyright (C) 2006 by Azrul Rahim - All rights reserved!
 * @license Copyrighted Commercial Software
 * 
 * Responsible displaying the data given  
 **/

# Don't allow direct linking
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

include_once (JC_LANGUAGE . $_JC_CONFIG->get('language'));

class JCSpamFilter {
	var $_data;
	var $_result;

	function JCSpamFilter($data) {
		$data->_referer = isset ($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "";
		$data->_useragent = isset ($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";

		$this->_data = $data;
		$this->_result['status'] = JC_STATUS_OK;
	}

	function isSpam() {
		global $_JC_CONFIG, $mosConfig_live_site, $mosConfig_absolute_path;
		include_once($mosConfig_absolute_path . '/components/com_jomcomment/includes/akismet/Akismet.class.php');
		
		#$this->_validReferer();     if($this->_result != JC_STATUS_OK) return $this->_result;
		$this->_validUserAgent();
		if ($this->_result['status'] != JC_STATUS_OK)
			return true;

		$this->_validDomain();
		if ($this->_result['status'] != JC_STATUS_OK)
			return true;

		$this->_validEmail();
		if ($this->_result['status'] != JC_STATUS_OK)
			return true;

		$this->_validUsername();
		if ($this->_result['status'] != JC_STATUS_OK)
			return true;

		#$this->_validUrl();
		#if ($this->_result['status'] != JC_STATUS_OK)
		#	return true;
// 		
// 		if($_JC_CONFIG->get('remoteSpam')){
// 			$document = $this->_data->comment;
// 			$data = "action=cat&type=comment&document=" . urlencode($document) . "&version=2";
// 			$return = jc_post("filter0.azrul.com/index.php", $data);
// 			
// 			# The server will return text with spam/nonspam word enclosed in <spam> tag
// 			$isspam = "nonspam";
// 			$pattern = "'<cat>(.*)</cat>'s";
// 			preg_match($pattern, $return, $matches);
// 			if($matches){
// 				$isspam = @ $matches[1];
// 			}
// 			
// 			if($isspam == 'spam'){
// 				$this->setWarning('Spam content detected');
// 				return true;
// 			}
// 
// 		}
// 		
		if($_JC_CONFIG->get('remoteSpam') && $_JC_CONFIG->get('akismetKey')){
			$akismet = new Akismet($mosConfig_live_site, trim($_JC_CONFIG->get('akismetKey')));
		
			$akismet->setAuthor("");
			$akismet->setAuthorEmail("");
			$akismet->setAuthorURL("");
			$akismet->setContent($this->_data->comment);
			//$akismet->setPermalink("http://example.com/path/to/your/blog/post/");
			
			if($akismet->isSpam()) {
			    $this->setWarning('Spam content detected');
				return true;
			} else {
			    //$cat = "nonspam"; //echo "thanks for not spamming me :)";
			}
		}
		
		

		return false;
	}

	/**
	 * Set internal warning string
	 */
	function setWarning($notice) {
		global $_JC_UTF8;
		$this->_result['status'] = JC_STATUS_WARNING;
		$this->_result['message'] = $_JC_UTF8->utf8ToHtmlEntities($notice);
		return $this->_result;
	}

	function getErrorMsg() {
		return $this->_result['message'];
	}

	function _validReferer() {
		# @rule: referer, if exist must not be empty
		if (isset ($this->_data->_referer)) {
			if (empty ($this->_data->_referer)) {
				return $this->setWarning("Permission Error, Empty Referer");
			}

			# Referer, if it exists, must contain a :
			# While a relative URL is technically valid in Referer, all known
			# legit user-agents send an absolute URL
			if (strpos($this->_data->_referer, ":") === FALSE) {
				return $this->setWarning("Permission Error. Relative referer not allowed");
			}
		}
	}

	function _validUserAgent() {
		$userAgentOk = true;
		# Is the user-agent a known spambot?
		# Occurs at the beginning of the string
		$wp_bb_spambots_str0 = array (
				"8484 Boston Project", # video poker/porn spam
		"autoemailspider", # spam harvester
		"Digger", # spam harvester
		"ecollector", # spam harvester
		"EmailCollector", # spam harvester
		"Email Extractor", # spam harvester
		"Email Siphon", # spam harvester
		"EmailSiphon", # spam harvester
		"grub crawler", # misc comment/email spam
		"Jakarta Commons", # custommised spambots
		"Java 1.", # definitely a spammer
		"libwww-perl", # spambot scripts
		"LWP", # exploited boxes
		"Microsoft URL", # spam harvester
		"Missigua", # spam harvester
		"Mozilla ", # forum exploits
		"Shockwave Flash", # spam harvester
		"User-Agent: ", # spam harvester
		"Wordpress Hash Grabber", # malicious software
		"www.weblogs.com", # referrer spam (not the real www.weblogs.com)

	
		);

		# Occurs anywhere in the string
		$wp_bb_spambots_str = array (
				"Bad Behavior Test", # Add this to your user-agent to test BB
		"compatible ; MSIE", # misc comment/email spam
		"DTS Agent", # misc comment/email spam
		"Gecko/25", # revisit this in 500 years
		"grub-client", # search engine ignores robots.txt
		"Indy Library", # misc comment/email spam
		".NET CLR 1)", # free poker, etc.
		"POE-Component-Client", # free poker, etc.
		"WISEbot", # spam harvester
		"WISEnutbot", # spam harvester
	
		);

		# Regex matching
		$wp_bb_spambots_reg = array (
				"/^[A-Z]{10}$/", # misc email spam
		"/^Mozilla...0$/i", # fake user agent/email spam
		#	"/MSIE.*Windows XP/",	# misc comment spam

	
		);

		$http_user_agent = isset ($this->_data->_useragent) ? $this->_data->_useragent : "";
		if (empty ($http_user_agent)) {
			$userAgentOk = false;
		}

		foreach ($wp_bb_spambots_str0 as $wp_bb_spambot) {
			$pos = stristr($http_user_agent, $wp_bb_spambot);
			if ($pos !== FALSE && $pos == 0) {
				$userAgentOk = false;
			}
		}

		foreach ($wp_bb_spambots_str as $wp_bb_spambot) {
			if (stristr($http_user_agent, $wp_bb_spambot) !== FALSE) {
				$userAgentOk = false;
			}
		}

		foreach ($wp_bb_spambots_reg as $wp_bb_spambot) {
			if (preg_match($wp_bb_spambot, $http_user_agent)) {
				$userAgentOk = false;
			}
		}

		if (!$userAgentOk and !empty ($http_user_agent)) {
			return $this->setWarning("Invalid user agent");
		}
	}

	function _validDomain() {
		global $_JC_CONFIG;
		include_once (JC_LANGUAGE . $_JC_CONFIG->get('language'));

		if ($_JC_CONFIG->get('blockDomain')) {
			$domains = explode(",", $_JC_CONFIG->get('blockDomain'));
			array_walk($domains, "jctrim");
			if (in_array($this->_data->ip, $domains)) {
				return $this->setWarning(_JC_IP_BLOCKED); #
			}
		}
	}

	function _emailIsValid($email) {
		# make sure email is valid
		$regexp = "^([_a-z0-9-]+)(\.[_a-z0-9-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,4})$";
		return eregi($regexp, $email);
	}

	function _validEmail() {
		global $_JC_CONFIG;
		include_once (JC_LANGUAGE . $_JC_CONFIG->get('language'));

		if ($_JC_CONFIG->get('fieldEmail')) {
			if ($_JC_CONFIG->get('moreInfo') AND !$this->_emailIsValid($this->_data->email)) {
				return $this->setWarning(_JC_INVALID_EMAIL);
			}
		}
	}

	function _validUrl() {
		/*
		global $_JC_CONFIG;
		include_once (JC_LANGUAGE . $_JC_CONFIG->get('language'));
		
		if ($_JC_CONFIG->get('fieldEmail')) {
			if ($_JC_CONFIG->get('moreInfo') && !jcEmailIsValid($this->_data->email)) {
				return $this->setWarning(_JC_INVALID_EMAIL);
			}
		}
		*/
	}

	function _validUsername() {
		global $_JC_CONFIG,$_JC_UTF8;
		include_once (JC_LANGUAGE . $_JC_CONFIG->get('language'));
		global $database, $my;
		
		# If the poster is registered user, we accept whatever name he/shes uses
		/*if($my->username){
			return;
		}
		*/
		
		# @rule: If, anonpost is disabled, anon cannot post in any circumstances
		if (!$_JC_CONFIG->get('anonComment') and !$my->id) {
			# we shouldn't enter this area at all
			return $this->setWarning(_JC_TPL_GUEST_MUST_LOGIN);
		}			

		# @rule: username cannot be left empty for. The, $this->_data->name should
		# already be filled by Data Manager		
		if (!$_JC_CONFIG->get('anonComment') and !$this->_data->name) {
			# we shouldn't enter this area at all
			return $this->setWarning(_JC_TPL_GUEST_MUST_LOGIN);
		}

		# @rule: If name field is required, the name should not be left empty 		
		if (empty ($this->_data->name) and $_JC_CONFIG->get('moreInfo')){
			return $this->setWarning(_JC_EMPTY_USERNAME);
		}
		
		# @rule: If name field is NOT required, and the name is empty, assign it
		# a predefined 'guest' name.		
		if (empty($this->_data->name) and !$_JC_CONFIG->get('moreInfo')){
			$this->_data->name = $_JC_UTF8->utf8ToHtmlEntities(_JC_GUEST_NAME);
		}

		# @rule: username must not be blocked
		$blockedUsers = explode(",", $_JC_CONFIG->get('blockUsers'));

		if (!empty ($blockedUsers)) {
			array_walk($blockedUsers, "jctrim");
			if (in_array($this->_data->name, $blockedUsers)) {
				return $this->setWarning(_JC_USERNAME_BLOCKED);
			}
		}

		# @rule: a guest cannot use registered user's email/name/username
		$query = sprintf("SELECT count(*) FROM #__users 
							WHERE username='%s' OR name='%s'", $this->_data->name, $this->_data->name);
		$database->setQuery($query);
		$found = $database->loadResult();
		if (($found > 0) && ($this->_data->name != $my->username) && ($this->_data->name != $my->name)) {
			return $this->setWarning(_JC_USERNAME_TAKEN);
		}
	}
}
