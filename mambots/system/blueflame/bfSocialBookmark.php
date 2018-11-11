<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: bfSocialBookmark.php 827 2007-06-12 18:03:41Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 *
 */

class bfSocialBookmarks {

	var $_link = null;
	var $_title = null;
	var $sites = array();


	function bfSocialBookmarks(){
		$this->path = _BF_FRAMEWORK_LIB_URL . '/view/images/';

		$this->sites['digg'] 		= array('img'=>'digg.gif' 		, 'link'=>'http://digg.com/submit?phase=2&url=%s');
		$this->sites['Delicious'] 	= array('img'=>'delicious.gif' 	, 'link'=>'http://del.icio.us/post?url=%s');
		$this->sites['Furl'] 		= array('img'=>'furl.gif' 	, 'link'=>'http://furl.net/storeIt.jsp?t=%s');
		$this->sites['reddit'] 		= array('img'=>'reddit.gif' 	, 'link'=>'http://reddit.com/submit?url=%s');
		$this->sites['blinklist'] 		= array('img'=>'blinklist.gif' 	, 'link'=>'http://blinklist.com/index.php?Action=Blink/addblink.php&url=%s');
		$this->sites['Technorati'] 		= array('img'=>'technorati.gif' 	, 'link'=>'http://technorati.com/cosmos/search.html?url=%s');
		$this->sites['jeqq'] 		= array('img'=>'jeqq.png' 	, 'link'=>'http://jeqq.com/submit.php?phase=2&url=%s');
	}

	function setArticleDetail($link, $title){
		$this->_link = $link;
		$this->_title = $title;
	}

	function toHTML(){
		$html = '';
		foreach ($this->sites as $site=>$sitedata){
			$html .= '<a class="sociallink" title="'.$site.'" rel="nofollow" href="'.sprintf($sitedata['link'], $this->_link).'"><img alt="'.$site.'" border="0" src="'.$this->path . $sitedata['img'].'" /></a>';
		}
		return $html;
	}
}
?>