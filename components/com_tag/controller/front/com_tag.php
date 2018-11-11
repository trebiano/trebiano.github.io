<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: com_tag.php 827 2007-06-12 18:03:41Z phil $
 * @package bfFramework
 * @subpackage Joomla Tags
 * @copyright Copyright (C) 2007 Blue Flame IT Ltd. All rights reserved.
 * @license see LICENSE.php
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 *
 */

class com_tagController extends bfController {

	/**
	 * Constructor just calls the parent bfController
	 *
	 */
	function com_tagController() {
		$this->__construct();
	}

	function __construct() {
		parent::__construct();
	}
	
	/**
	 * 3pd integration
	 */
	function embed_pd3_admintab(){
		$this->setView('3p-tab');
	}

	/**
	 * I am invoked when a user clicks on a tag
	 *
	 */
	function frontpage(){
		global $mosConfig_sef;
		$id 		= bfRequest::getVar('tag_id', '','REQUEST','string',0);
		$tagname	= bfRequest::getVar('tag','','REQUEST','string',null);

		/* ONLY FOR CORE SEF !!!! */
		if (!file_exists(_BF_JPATH_BASE . DS . 'components' . DS . 'com_sef' . DS . 'sef.php') && $mosConfig_sef=="1"){
			/* Remove spaces when core sef urls enabled */
			$tagname = str_replace('%20',' ',$tagname);
			if (strpos($tagname,'%') > 0){
				bfLoad('bfUTF8');
				$u8 = new bfUtf8();
				$tagname = $u8->unicode_to_utf8($tagname);
			}
		}

		if (($tagname == '' && $id == '')){
			if(_BF_PLATFORM=='JOOMLA1.0'){
				global $mainframe;
				$menu = $mainframe->get( 'menu' );
				$params = new mosParameters( $menu->params );
				$tagname = $params->get('tags',null);
			}
		}

		if (($tagname == '' && $id == '')){
			$id 		= bfRequest::getVar('tag_id', '','REQUEST','string',0);
			$tagname	= bfRequest::getVar('tag','','REQUEST','string',null);
		}

		if ( (!$id && !$tagname) OR $tagname=='cloud') {

			/* & is important for PHP4 */
			/* Load Model */
			$cloud_details =& $this->getModel('tag');
			/* Get all rows */
			$cloud_details->generateCloud(false);
			/* set the view file (optional) */
			$this->setView('tagcloud');
		} else {
			$tagData =& $this->getModel('tag');

			$multipletags = false;
			$parts_id = explode('|', $id);
			$parts_name = explode('|', $tagname);

			if (  (count($parts_id) > 1) OR (count($parts_name) > 1) ){
				$multipletags = true;
				$this->_registry->setValue('tag.multiple', true);
			}

			if ($multipletags === false){
				if ($id){
					$tagData->get($id);
				} else {
					$tagData->_tbl_key = 'tagname';
					$tagData->get($tagname);

					/* OpenSEF Lowercase url tmp fix
					*
					* no longer needed???
					*/
					/*
					if (!$tagData->id){
					$tagname = ucfirst($tagname);
					$tagData->get($tagname);
					}*/
				}
				/* hit tag */
				$tagData->hit();

				$tagData->getContentItems();
				$this->setView('frontpage');
			} else {
				/* deal with searching by multiple tags */
				/* I hold the list of tag names being searched */
				$tagnames = array();
				$tagids = array();

				/* I hold the list of items */
				$taggeditems = array();

				if (count($parts_id) > 1){
					/* deal with id */
					foreach ($parts_id as $id){
						$tagData->get($id);
						/* hit tag */
						$tagData->hit();

						$tagids[] = $id;
						$tagnames[] = $tagData->tagname;
					}
				} else {
					/* deal with name */
					foreach ($parts_name as $tagname){
						$tagData->_tbl_key = 'tagname';
						$tagData->get($tagname);
						/* hit tag */
						$tagData->hit();
						$tagids[] = $tagData->id;
						$tagnames[] = $tagData->tagname;
					}
				}

				print_R($tagname);
				if (!$tagData->id){
					die('No tag');
				}

				$this->_registry->setValue('tag.tagnames', $tagnames);
				$this->_registry->setValue('tag.tagids', $tagids);
				$tagData->getContentItems();

			}
		}
	}

	/**
	 * I get called by the mambot to display the add tags footer
	 *
	 */
	function plugin_footerText(){
		$registry =& bfRegistry::getInstance();
		$task = bfRequest::getVar('task','','request','string',0);
		$view = bfRequest::getVar('view','','request','string',0);
		$row = $registry->getValue('row');
		if (is_object($row)){
			$id = $row->id;
		} else {
			$id = bfRequest::getVar('id', 0,'request','int',0);
		}

		$map =& $this->getModel('tag_map');
		$map->getTagsForContentId( (int) $id );


		if (_BF_PLATFORM=='JOOMLA1.0'){
			switch ($task){
				case ('view'):
					/* When viewing a whole article */
					$registry->set('template',$registry->getValue('config.footerDetail'));
					$this->setView('footer');
					break;
				default:

					/* when viewing a blog/introtext only */
					$registry->set('template',$registry->getValue('config.footerIntro'));
					$this->setView('footer');
					break;
			}
		} else {
			/* Joomla 1.5 */
			switch ($view){
				case ('article'):
					/* When viewing a whole article */
					$registry->set('template','2');
					$this->setView('footer');
					break;
				default:
					/* when viewing a blog/introtext only */
					$registry->set('template','3');
					$this->setView('footer');
					break;
			}
		}
	}

	/**
	 * I am the only public way of adding tags
	 * I get called by the form on a content item
	 */
	function xpublic_addnewtag(){
		global $mainframe;
		$registry =& bfRegistry::getInstance($mainframe->get('component'));
		$user =& bfUser::getInstance();

		/* are we allowed to add tags ? */
		if ($registry->getValue('config.allowfrontendsubmission',0)=='0'){
			bfError::raiseError('403',bfText::_('Access Denied') . ' ' . bfText::_('Frontend submission disabled'));
			return;
		}

		$gid = $user->get('gid');
		global $my;
		print_R($my);
		if (!$gid) $gid = "0";
		if ($registry->getValue('config.frontendsubmissionaccesslevel',0) > $gid){
			bfError::raiseError('403',bfText::_('Access Denied') . ' ' . bfText::_('Frontend submission limited to those with greater access permissions than you (Have you logged in?)'.$gid));
			return;
		}

		/**
		 * Args:
		 * 1 = Content id
		 * 2 = Tag to add
		 * 3 = scope E.g. com_content
		 */
		$args = $this->getAllArguments();
		$newTags = $args[2];
		$html = '';
		$addedTags = array();

		/* add multiple tags separated by commas */
		if (ereg(',',$newTags)){
			$newTags = explode(',',$newTags);

			if (count($newTags)){
				foreach ($newTags as $tag){
					/* stop blanks */
					if (!$tag) continue;

					$tag = (string) bfSecurity::cleanVar($tag,0,'string');

					if (function_exists('sef_decode')){
						$tag = sefdecode('tagname');
					}

					$notAllowed = array('_','-','&','(',')','*',';',':','@','#',"'");
					foreach ($notAllowed as $not){
						$tag = str_replace($not,'', $tag);
					}
					$tag = str_replace('%20',' ', $tag);


					/* add this tag to the database if not exist, or get this tags id number */
					$tags =& $this->getModel('tag');
					$tagid = $tags->AddTagIfNotExists( (string) trim($tag) );
					$addedTags[] = $tag;

					/* add a map to this tag to the content id (or other key for other scopes) */
					$map =& $this->getModel('tag_map');
					$duplicate = $map->AddTagToContent( (int) $tagid, (int) $args[1] , (int) $args[3]);

					/* reset so we can go again */
					$tags->id = 0;
					$map->id = 0;
				}
				$addedMultiple = true;
			}
		} else {
			/* add a single tag */
			$addedMultiple = false;

			$tag = (string) bfSecurity::cleanVar($args[2],0,'string');

			if (function_exists('sef_decode')){
				$tag = sefdecode('tagname');
			}

			$notAllowed = array('_','-','&','(',')','*',';',':','@','#',"'");
			foreach ($notAllowed as $not){
				$tag = str_replace($not,'', $tag);
			}
			$tag = str_replace('%20',' ', $tag);

			/* add this tag to the database if not exist, or get this tags id number */
			$tags =& $this->getModel('tag');
			$tagid = $tags->AddTagIfNotExists( (string) $args[2] );

			$addedTags[] = $args[2];

			/* add a map to this tag to the content id (or other key for other scopes) */
			$map =& $this->getModel('tag_map');
			$duplicate = $map->AddTagToContent( (int) $tagid, (int) $args[1] , (int) $args[3]);
		}

		if ($duplicate===null){
			$this->xajax->addalert(bfText::_('Duplicate tagging is not allowed.'));
			$this->xajax->addscript("jQuery('input#tagname".$args[1]."').val('');");
		} else {
			if ($addedMultiple===true){
				if (count($newTags)){
					foreach ($newTags as $tag){
						/* stop blanks */
						if (!$tag) continue;

						$tag = (string) bfSecurity::cleanVar($tag,0,'string');

						if (function_exists('sef_decode')){
							$tag = sefdecode('tagname');
						}

						$notAllowed = array('_','-','&','(',')','*',';',':','@','#',"'");
						foreach ($notAllowed as $not){
							$tag = str_replace($not,'', $tag);
						}
						$tag = str_replace('%20',' ', $tag);

						/* provide visual feedback that tag has been added */
						$url = bfCompat::sefRelToAbs( 'index.php?option=com_tag&amp;tag=' . $tag );
						$html .= '<a href="'.$url. '" class="bfnew">' . $tag . '</a>';
					}
				}
			} else {

				$tag = (string) bfSecurity::cleanVar($args[2],0,'string');

				if (function_exists('sef_decode')){
					$tag = sefdecode('tagname');
				}

				$notAllowed = array('_','-','&','(',')','*',';',':','@','#',"'");
				foreach ($notAllowed as $not){
					$tag = str_replace($not,'', $tag);
				}
				$tag = str_replace('%20',' ', $tag);

				/* provide visual feedback that tag has been added */
				$url = bfCompat::sefRelToAbs( 'index.php?option=com_tag&amp;tag=' . $tag . '&amp;tag_id=' . $tagid);
				$html .= '<a href="'.$url. '" class="bfnew">' . $tag . '</a>';
			}

			/* clear form input box */
			$this->xajax->addscript("jQuery('input#tagname".$args[1]."').val('');");

			/* hide add form */
			$this->xajax->addscript("jQuery('div#tagaddform".$args[1]."').hide('slow');");

			/* update tags */
			$this->xajax->addscript("jQuery('span#tagsfor".$args[1]."').append('".$html."');");

			/* send emails for moderation if needed */
			//$addedTags

			if ($this->_registry->getValue('config.holdformoderation') == '1'){
				$this->xajax->addalert(bfText::_('Thank you' . "\n\n" .'The tags you submitted are being held for moderation and will be added soon.'));
			}

			if ($this->_registry->getValue('config.holdformoderation') == '1' OR $this->_registry->getValue('config.emaileverytime') == '1'){
				bfLoad('bfMail');
				global $mainframe;
				$mail = new bfMail(
				$mainframe->getCfg('mailfrom'),
				$mainframe->getCfg('fromname'),
				$this->_registry->getValue('config.moderationemail'),
				bfText::_('New Tags Submitted on') . ' ' . bfCompat::getSiteName(),
				'The following tags were just added:'."\n\n" . implode(', ', $addedTags) . "\n\n" . bfCompat::getLiveSite().'/administrator/index2.php'
				);
			}
		}
	}
}
?>