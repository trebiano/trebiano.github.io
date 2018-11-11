<?php
/**
 * Jom Comment 
 * @package JomComment
 * @copyright (C) 2006 by Azrul Rahim - All rights reserved!
 * @license Copyrighted Commercial Software
 **/

# Don't allow direct linking
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

class JCDataManager {
	var $_createError = "";
	var $_utf8 = null;

	function JCDataManager() {
		# set up utf8 object
		$this->_utf8 = new Utf8Helper();
	}

	/**
	 * Resturn the password given the secret id
	 */
	function getPassword($sid) {
		$db = CMSDb::getInstance();
		$query = "SELECT password FROM #__captcha_session WHERE sessionid='$sid'";
		$db->query($query);
		$secCode = $db->get_value();
		
		$query = "DELETE FROM #__captcha_session where sessionid='$sid';";
		$db->query($query);
	
		return $secCode;
	}

	function deletePassword($sid) {
		$db = CMSDb::getInstance();
		$query = "DELETE FROM #__captcha_session WHERE sessionid='$sid'";
		$db->query($query);
	}

	/**
	 * Return 1 comment object given the unique id. 
	 * Return null if not found
	 */
	function get($uid) {
	}

	function getFlood($username, $ip, $date, $postInterval) {
		$db = CMSDb::getInstance();
		$query = sprintf("	SELECT count(*) FROM #__jomcomment 
							WHERE name='%s' 
								AND ip='%s' 
								AND '%s' <= DATE_ADD(date, interval %d second);", $username, $ip, $date, $postInterval);

		$db->query($query);
		
		return $db->get_value();
	}

	function getNumComment($cid, $option) {
		$db = CMSDb::getInstance();
		$query = "";
		
		if ($option != "com_content" && $option != "com_myblog") {
			$query = "	SELECT count(*) FROM #__jomcomment 
								WHERE `contentid`='$cid' 
								AND `option`='$option'
								AND `published`='1'";
		}else {
			$query = "	SELECT count(*) FROM #__jomcomment as a
								WHERE `contentid`='$cid' 
								AND ( a.`option`='com_content' OR a.`option`='com_myblog')
								AND `published`='1'";
		}

		$db->query($query);
		//if ($db->getErrorMsg()) {
			//echo $database->getErrorMsg();
		//	exit;
		//}
		return $db->get_value();
	}

	/**
	 * Return the number of comment by the specific IP address
	 */	 	
	function getNumCommentByIP($ip, $date) {
		$db = CMSDb::getInstance();
		$query = "	SELECT count(*) FROM #__jomcomment 
							WHERE ip='$ip' 
							AND '$date' <= DATE_ADD(date, interval 1800 second);";

		$db->query($query);
		return $db->get_value();
	}

	/**
	 * return true if similar content already exist
	 */	 	
	function searchSimilarComments($contentid, $comment, $date) {
		$db = CMSDb::getInstance();
		$query = "	SELECT comment FROM #__jomcomment
				        WHERE
				        	`contentid`=$contentid 
				        	AND	`published`=1 
				        	AND	'$date' <= DATE_ADD(
				        		date, 
				        		INTERVAL 1800 second);
				        ";
		$db->query($query);
		$rows = $db->get_object_list();

		if ($rows) {
			$len = strlen($comment);
			foreach ($rows as $row) {
				if (abs(strlen($row->comment) - $len) < ($len / 4)) {
					$percent = 0;
					similar_text($row->comment, $comment, $percent);
					if ($percent > 96) {
						return true;
					}

				}
			}
		}

		return false;
	}

	/**
	 * Return array of comments	for the given contentid and option
	 */
	function getAll($cid, $option) {
		global $database, $_JC_CONFIG;

		# set the ordering
		$orderBy = " ORDER BY id DESC ";
		if ($_JC_CONFIG->get('sortBy') == 0) {
			$orderBy = " ORDER BY date DESC, id DESC ";
		} else {
			$orderBy = " ORDER BY date ASC, id ASC ";
		}

		# add limit
		/*		
		if (@$_JC_CONFIG->get('paginate')) 
		{
			$limitStart = $page * intval($pc_paginate);
			$limitMax = intval($pc_paginate);
			$orderBy .= " LIMIT $limitStart, $limitMax";
		}
		*/

		if ($option != "com_content" && $option != "com_myblog") {
			$database->setQuery("SELECT *, '-1' as created_by FROM #__jomcomment 
						                    WHERE `contentid`='$cid' 
						                    AND `option`='$option' 
						                    AND `published`='1' " . $orderBy);
		} else {
			$database->setQuery("SELECT a.*, b.created_by FROM #__jomcomment as a, #__content as b 
						            WHERE a.`contentid`='$cid' 
						                AND ( a.`option`='com_content' OR a.`option`='com_myblog') 
						                AND a.`published`='1' 
						                AND b.`id`=a.`contentid` " . $orderBy);

		}

		$result = $database->loadObjectList();
		return $result;
	}

	/**
	 * Return the unique id given the IP and date. We assume combination of date
	 * and ip is unique	 
	 */
	function getUid($ip, $date) {
	}

	/**
	 * Create a new jom comment object based on the given input
	 */
	function create($xajaxArgs) {
		global $my, $mosConfig_offset, $_JC_CONFIG, $mosConfig_absolute_path;
		global $database;
		
		require ($mosConfig_absolute_path . '/administrator/components/com_jomcomment/class.jomcomment.php');

		$this->_createError = "";
		$data = new mosJomcomment($database);

		# more data
		$option = isset ($xajaxArgs['jc_option']) ? $xajaxArgs['jc_option'] : "com_content";
		$ip = $_SERVER['REMOTE_ADDR']; //getenv('REMOTE_ADDR');
		$date = strftime("%Y-%m-%d %H:%M:%S", time() + ($mosConfig_offset * 60 * 60));
		$email = isset ($xajaxArgs['jc_email']) ? $xajaxArgs['jc_email'] : $my->email;
		$username = isset ($xajaxArgs['jc_name']) ? $xajaxArgs['jc_name'] : $my->name;
		
		# fix data with different name
		$arrayInput = array (
			"comment" => "jc_comment",
			"contentid" => "jc_contentid",
			"title" => "jc_title",
			"option" => "jc_option",
			"website" => "jc_website",
			"name" => "jc_username",
			"_password" => "jc_password",
			"_sid" => "jc_sid"
		);

		foreach ($arrayInput as $key => $value) {
			if (!isset ($xajaxArgs[$value]))
				$xajaxArgs[$value] = "";

			$xajaxArgs[$key] = $xajaxArgs[$value];
		}

		# If more_info is set, we get username from args or the login name
		$username = "";
		$name_field = $_JC_CONFIG->get('username');
		
		if ($_JC_CONFIG->get('moreInfo')) {
			if ($my->username) {
				$username = isset ($xajaxArgs['jc_name']) ? $xajaxArgs['jc_name'] : $my->$name_field;
			} else {
				// no username supplied, just give a blank username
				$username = isset ($xajaxArgs['jc_name']) ? $xajaxArgs['jc_name'] : "";
			}
		} else {
			// if more_info is not set, set all unregistered user as guest
			if ($my->name) {
				$username = $my->$name_field;
			} else {
				$username = $this->_utf8->utf8ToHtmlEntities(_JC_GUEST_NAME);
			}
		}

		if (!$_JC_CONFIG->get('moreInfo') and !$email)
			$email = "#";
			
		if (!$_JC_CONFIG->get('moreInfo') and !$username)
			$username = $this->_utf8->utf8ToHtmlEntities(_JC_GUEST_NAME);

		if ($email == "#" AND $my->email)
			$email = $my->email;

		$xajaxArgs['id'] 		= 0;
		$xajaxArgs['date'] 		= $date;
		$xajaxArgs['user_id'] 	= $my->id;
		$xajaxArgs['ip'] 		= $ip;
		$xajaxArgs['email'] 	= $email;
		$xajaxArgs['name'] 		= $username;
		$xajaxArgs['published'] = intval($_JC_CONFIG->get('autoPublish'));

		# bind the array, the private data has to be added manually
		$data->bind($xajaxArgs);
		$data->_sid = $xajaxArgs['jc_sid'];
		$data->_password = isset($xajaxArgs['jc_password']) ? $xajaxArgs['jc_password']: "";
		$data->comment = $xajaxArgs['jc_comment'];
		
		# must strip html tags from input
		$data->comment 	= strip_tags($data->comment, $_JC_CONFIG->get('allowedTags'));
		$data->website 	= strip_tags($data->website);
		$data->name 	= strip_tags($data->name);
		$data->title 	= strip_tags($data->title);

		# validate required fields
		return $data;
	}

	/**
	 * Return the las
	 */
	function getCreateError() {
		return $this->_createError;
	}

	/**
	 * Enough said!
	 */
	function publish($uid) {
		global $database;
		$database->setQuery("
									UPDATE #__jomcomment 
									SET published='1' 
									WHERE id='$uid'");
		$database->query();
	}

	/**
	 * Enough said!
	 */
	function unpublish($uid) {
		global $database;
		$database->setQuery("
									UPDATE #__jomcomment 
									SET published='0' 
									WHERE id='$uid'");
		$database->query();
	}
	
	/**
	 * Enough said!
	 */
	function delete($uid) {
		global $database;
		$database->setQuery("DELETE FROM #__jomcomment WHERE id='$uid'");
		$database->query();
	}
	
	
	/**
	 * Check if the particular url has already ping our site
	 */	 	
	function tbExist($cid, $url){
		$db = CMSDb::getInstance();
		$query = sprintf("SELECT count(*) FROM #__jomcomment_tb WHERE contentid='%d' AND url='%s'",
				$cid, $url);
		$db->query($query);
		return ($db->get_value() > 0);
	}
	
	/**
	 * Return all the trackback for the particular content
	 */	 	
	function tbGetAll($cid, $option){
		global $database;

		
		# consolidate com_content and com_myblog		
		if($option == 'com_myblog' OR $option == 'com_content' ){
			$option ="com_myblog' OR `option`='com_content";
		}
		
		# set the ordering		
		$orderBy = " ORDER BY id DESC ";
		$query 	 = "SELECT * FROM #__jomcomment_tb WHERE contentid='$cid' AND published=1 AND `option`='$option' $orderBy";
		$database->setQuery($query);
		
		return $database->loadObjectList(); 
	}
	
	function tbGetCount($cid, $option){
		$db = CMSDb::getInstance();
		
		# consolidate com_content and com_myblog
		if($option == 'com_myblog' OR $option == 'com_content' ){
			$option ="com_myblog' OR `option`='com_content";
		}
		# set the ordering
		$orderBy = " ORDER BY id DESC ";
		$query 	 = "SELECT count(*) FROM #__jomcomment_tb WHERE contentid='$cid' AND published=1 AND `option`='$option' $orderBy";
		$db->query($query);
		
		return $db->get_value(); 
	}
}
