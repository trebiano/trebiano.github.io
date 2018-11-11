<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: bfDocument.php 857 2007-06-14 21:49:40Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
*/

if (!defined('_BF_FILEINCLUDED_BFDOCUMENT')) define('_BF_FILEINCLUDED_BFDOCUMENT', true);

class bfDocument {

	function setGenerator($string){
		global $mainframe;
		# TRANSLATE? - PT: No Dont Translate as its HTML Specification
		$mainframe->addMetaTag('Page Generator',$string);
	}

	function addCSS($string){
		bfDocument::addstylesheet($string);
	}

	function addstylesheet($string) {
		global $mainframe;
		$string = '<style type="text/css">@import url('.$string.');</style>';
		$mainframe->addCustomHeadTag($string);
	}

	function addscript($string){
		global $mainframe;
		$string = '<script src="'.$string.'" type="text/javascript"></script>';
		$mainframe->addCustomHeadTag($string);
	}

	function setTitle($string){
		if (_BF_PLATFORM=='JOOMLA1.5'){

		} else {
			global $mainframe;
			$mainframe->setPageTitle($string);
		}
	}

	function addPathway($string){
		if (_BF_PLATFORM=='JOOMLA1.5'){

		} else {
			global $mainframe;
			$mainframe->appendPathWay($string);
		}
	}

	function addMooTools(){
		
		global $mainframe;
		$registry =& bfRegistry::getInstance($mainframe->get('component'),$mainframe->get('component'));
		bfDocument::addScript($registry->getValue('bfFramework_'.$mainframe->get('component_shortname').'.mootools'));
		define('_BFMOOTOOLS', '1');
	}

	function getTemplate(){
		global $mainframe;
		$mainframe->getTemplate();
	}

	function initTips(){

		$uncompressed_script = '

	if (typeof($S)=="function") {
	/* hide problematic moo tooltips */
				var spans = $S(\'div.tooltip\');
				  for (var n = 0; n < spans.length; n++){
					spans[n].style.visibility = \'hidden\';
				  }
	/* init tooltips */
			    var myTips = new Tips($S(\'.hasTip\'), {
			        maxTitleChars: 50, //I like my captions a little long
			        maxOpacity: .9, //let\'s leave a little transparancy in there
			    });
	/* make nice fade ins */
			    }

	';

		$script = 'if(typeof($S)=="function"){var spans=$S("div.tooltip");for(var n=0;n<spans.length;n++){spans[n].style.visibility="hidden"}var myTips=new Tips($S(".hasTip"),{maxTitleChars:50,maxOpacity:.9,})}';
		return ''; //'<script language="JavaScript" type="text/javascript">//<![CDATA['.$script.']]>//</script>';
	}
}
